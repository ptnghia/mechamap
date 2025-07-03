<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * 🔐 MechaMap Role Controller
 *
 * Quản lý roles và permissions cho hệ thống MechaMap
 * Tương thích với cấu trúc 4 nhóm và 14 roles
 */
class RoleController extends Controller
{
    /**
     * Display a listing of roles with permissions matrix
     */
    public function index(Request $request): View
    {
        // Get filter parameters
        $roleGroup = $request->get('role_group');
        $search = $request->get('search');

        // Build query
        $query = Role::withCount(['users' => function($query) {
                $query->where('user_has_roles.is_active', true);
            }])
            ->with(['permissions'])
            ->orderBy('hierarchy_level')
            ->orderBy('name');

        if ($roleGroup) {
            $query->where('role_group', $roleGroup);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->get();

        // Get all permissions grouped by category
        $permissions = Permission::active()
            ->orderBy('category')
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('category');

        // Get role groups for filter
        $roleGroups = config('mechamap_permissions.role_groups');

        // Statistics
        $stats = [
            'total_roles' => Role::count(),
            'active_roles' => Role::active()->count(),
            'total_permissions' => Permission::count(),
            'system_roles' => Role::system()->count(),
        ];

        return view('admin.roles.index', compact(
            'roles',
            'permissions',
            'roleGroups',
            'stats',
            'roleGroup',
            'search'
        ));
    }

    /**
     * Show the form for creating a new role
     */
    public function create(): View
    {
        $roleGroups = config('mechamap_permissions.role_groups');
        $permissions = Permission::active()
            ->orderBy('category')
            ->orderBy('module')
            ->get()
            ->groupBy('category');

        return view('admin.roles.create', compact('roleGroups', 'permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_group' => 'required|in:system_management,community_management,community_members,business_partners',
            'hierarchy_level' => 'required|integer|min:1|max:20',
            'color' => 'required|string|max:20',
            'icon' => 'required|string|max:50',
            'max_users' => 'nullable|integer|min:1',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create role
            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'role_group' => $request->role_group,
                'hierarchy_level' => $request->hierarchy_level,
                'color' => $request->color,
                'icon' => $request->icon,
                'max_users' => $request->max_users,
                'is_system' => false,
                'is_active' => true,
                'can_be_assigned' => true,
                'is_visible' => true,
                'created_by' => auth()->id(),
            ]);

            // Assign permissions
            if ($request->has('permissions')) {
                $permissionData = [];
                foreach ($request->permissions as $permissionId) {
                    $permissionData[$permissionId] = [
                        'is_granted' => true,
                        'granted_by' => auth()->id(),
                        'granted_at' => now(),
                        'grant_reason' => 'Assigned during role creation',
                    ];
                }
                $role->permissions()->attach($permissionData);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->display_name}' đã được tạo thành công.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role): View
    {
        $role->load(['permissions', 'users.profile']);

        // Get permission statistics
        $permissionStats = [
            'total' => $role->permissions->count(),
            'by_category' => $role->permissions->groupBy('category')->map->count(),
        ];

        // Get user statistics
        $userStats = [
            'total' => $role->users->count(),
            'active' => $role->users->where('is_active', true)->count(),
            'max_allowed' => $role->max_users,
        ];

        return view('admin.roles.show', compact('role', 'permissionStats', 'userStats'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role): View
    {
        if ($role->is_system && $role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('warning', 'Không thể chỉnh sửa role Super Admin.');
        }

        $role->load('permissions');
        $roleGroups = config('mechamap_permissions.role_groups');
        $permissions = Permission::active()
            ->orderBy('category')
            ->orderBy('module')
            ->get()
            ->groupBy('category');

        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'roleGroups', 'permissions', 'assignedPermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->is_system && $role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('warning', 'Không thể chỉnh sửa role Super Admin.');
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_group' => 'required|in:system_management,community_management,community_members,business_partners',
            'hierarchy_level' => 'required|integer|min:1|max:20',
            'color' => 'required|string|max:20',
            'icon' => 'required|string|max:50',
            'max_users' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'can_be_assigned' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update role
            $role->update([
                'display_name' => $request->display_name,
                'description' => $request->description,
                'role_group' => $request->role_group,
                'hierarchy_level' => $request->hierarchy_level,
                'color' => $request->color,
                'icon' => $request->icon,
                'max_users' => $request->max_users,
                'is_active' => $request->boolean('is_active', true),
                'can_be_assigned' => $request->boolean('can_be_assigned', true),
                'updated_by' => auth()->id(),
            ]);

            // Sync permissions
            if ($request->has('permissions')) {
                $permissionData = [];
                foreach ($request->permissions as $permissionId) {
                    $permissionData[$permissionId] = [
                        'is_granted' => true,
                        'granted_by' => auth()->id(),
                        'granted_at' => now(),
                        'grant_reason' => 'Updated during role edit',
                    ];
                }
                $role->permissions()->sync($permissionData);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->display_name}' đã được cập nhật thành công.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa role hệ thống.'
            ], 403);
        }

        if (!$role->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa role này vì vẫn có người dùng được gán role này.'
            ], 400);
        }

        try {
            $roleName = $role->display_name;
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => "Role '{$roleName}' đã được xóa thành công."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle role status (active/inactive)
     */
    public function toggleStatus(Role $role): JsonResponse
    {
        if ($role->is_system && $role->name === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thay đổi trạng thái role Super Admin.'
            ], 403);
        }

        try {
            $role->update([
                'is_active' => !$role->is_active,
                'updated_by' => auth()->id(),
            ]);

            $status = $role->is_active ? 'kích hoạt' : 'vô hiệu hóa';

            return response()->json([
                'success' => true,
                'message' => "Role '{$role->display_name}' đã được {$status}.",
                'is_active' => $role->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role permissions for AJAX
     */
    public function getPermissions(Role $role): JsonResponse
    {
        $permissions = $role->permissions()
            ->where('is_granted', true)
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * Assign role to user
     */
    public function assignToUser(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'is_primary' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
            'assignment_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$role->canAssignMoreUsers()) {
            return response()->json([
                'success' => false,
                'message' => 'Role này đã đạt giới hạn số lượng người dùng.'
            ], 400);
        }

        try {
            $user = User::findOrFail($request->user_id);

            // Check if user already has this role
            if ($user->roles()->where('role_id', $role->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng đã có role này.'
                ], 400);
            }

            // Nếu đây là role primary, set các role khác thành non-primary
            if ($request->boolean('is_primary', false)) {
                $user->roles()->updateExistingPivot(
                    $user->roles()->pluck('roles.id')->toArray(),
                    ['is_primary' => false]
                );
            }

            $user->roles()->attach($role->id, [
                'is_primary' => $request->boolean('is_primary', false),
                'assigned_at' => now(),
                'expires_at' => $request->expires_at,
                'assigned_by' => auth()->id(),
                'assignment_reason' => $request->assignment_reason,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Role '{$role->display_name}' đã được gán cho {$user->name}."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign multiple roles to user
     */
    public function assignMultipleRoles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            'primary_role_id' => 'nullable|exists:roles,id',
            'assignment_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($request->user_id);
            $roleIds = $request->role_ids;
            $primaryRoleId = $request->primary_role_id;

            // Validate primary role is in the list
            if ($primaryRoleId && !in_array($primaryRoleId, $roleIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Primary role phải nằm trong danh sách roles được gán.'
                ], 400);
            }

            DB::beginTransaction();

            // Remove existing roles
            $user->roles()->detach();

            // Assign new roles
            foreach ($roleIds as $roleId) {
                $isPrimary = ($roleId == $primaryRoleId);

                $user->roles()->attach($roleId, [
                    'is_primary' => $isPrimary,
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                    'assignment_reason' => $request->assignment_reason ?? 'Multiple roles assignment',
                    'is_active' => true,
                ]);
            }

            DB::commit();

            $assignedRoles = Role::whereIn('id', $roleIds)->pluck('display_name')->toArray();

            return response()->json([
                'success' => true,
                'message' => "Đã gán thành công " . count($roleIds) . " roles cho {$user->name}: " . implode(', ', $assignedRoles)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
