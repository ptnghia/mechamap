<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PermissionService;
use App\Services\UserVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Advanced User Management Controller - Phase 3
 * Quản lý user nâng cao với verification, role switching, bulk operations
 */
class AdvancedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view-users']);
    }

    /**
     * Hiển thị danh sách user với filters nâng cao
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = User::with(['roles']);
        
        // Permission-based filtering
        if (!$user->hasPermissionTo('manage-all-users')) {
            // Chỉ xem users có level thấp hơn
            $userLevel = PermissionService::getRoleLevel($user);
            $query->whereHas('roles', function($q) use ($userLevel) {
                $allowedRoles = collect(config('mechamap_permissions.role_hierarchy'))
                    ->filter(fn($level) => $level > $userLevel)
                    ->keys();
                $q->whereIn('name', $allowedRoles);
            });
        }

        // Filters
        if ($request->filled('role_group')) {
            $query->where('role_group', $request->role_group);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'verified') {
                $query->where('is_verified_business', true);
            } elseif ($request->status === 'unverified') {
                $query->where('is_verified_business', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortBy, ['name', 'email', 'created_at', 'last_seen_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = $this->getUserStats($user);
        
        // Filter options
        $roleGroups = config('mechamap_permissions.role_groups');
        $roles = Role::all();

        return view('admin.users.advanced-index', compact(
            'users',
            'stats',
            'roleGroups',
            'roles'
        ));
    }

    /**
     * Business verification queue
     */
    public function verificationQueue(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('verify-business-accounts')) {
            abort(403, 'Không có quyền xác thực tài khoản kinh doanh');
        }

        $query = User::where('role_group', 'business_partners')
            ->where('is_verified_business', false)
            ->whereNotNull('business_name')
            ->with(['businessDocuments']);

        // Priority sorting
        $priority = $request->get('priority', 'newest');
        
        switch ($priority) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'manufacturer':
                $query->where('role', 'manufacturer')->orderBy('created_at', 'desc');
                break;
            case 'verified_partner':
                $query->where('role', 'verified_partner')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $pendingUsers = $query->paginate(15);
        
        $stats = [
            'total_pending' => User::where('role_group', 'business_partners')
                ->where('is_verified_business', false)->count(),
            'manufacturers' => User::where('role', 'manufacturer')
                ->where('is_verified_business', false)->count(),
            'suppliers' => User::where('role', 'supplier')
                ->where('is_verified_business', false)->count(),
            'verified_partners' => User::where('role', 'verified_partner')
                ->where('is_verified_business', false)->count(),
        ];

        return view('admin.users.verification-queue', compact('pendingUsers', 'stats'));
    }

    /**
     * Bulk verify business accounts
     */
    public function bulkVerify(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('verify-business-accounts')) {
            return response()->json(['error' => 'Không có quyền xác thực'], 403);
        }

        $userIds = $request->input('user_ids', []);
        
        if (empty($userIds)) {
            return response()->json(['error' => 'Chưa chọn user nào'], 400);
        }

        DB::beginTransaction();
        try {
            $updated = User::whereIn('id', $userIds)
                ->where('role_group', 'business_partners')
                ->where('is_verified_business', false)
                ->update([
                    'is_verified_business' => true,
                    'verified_by' => $user->id,
                    'verified_at' => now(),
                ]);

            // Send verification emails
            $verifiedUsers = User::whereIn('id', $userIds)->get();
            foreach ($verifiedUsers as $verifiedUser) {
                // TODO: Send verification email
                activity()
                    ->performedOn($verifiedUser)
                    ->causedBy($user)
                    ->log('Business account verified via bulk action');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã xác thực {$updated} tài khoản thành công",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Role switching system
     */
    public function switchRole(Request $request, User $targetUser)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('manage-user-roles')) {
            abort(403, 'Không có quyền thay đổi role');
        }

        // Check if can modify this user
        if (!PermissionService::canModerate($user, $targetUser)) {
            abort(403, 'Không thể thay đổi role của user này');
        }

        $request->validate([
            'new_role' => [
                'required',
                Rule::exists('roles', 'name'),
                function ($attribute, $value, $fail) use ($user) {
                    // Check if user can assign this role
                    $targetLevel = config('mechamap_permissions.role_hierarchy.' . $value);
                    $userLevel = PermissionService::getRoleLevel($user);
                    
                    if ($targetLevel <= $userLevel) {
                        $fail('Không thể gán role cao hơn hoặc bằng level của bạn');
                    }
                }
            ],
            'reason' => 'required|string|max:500'
        ]);

        $oldRole = $targetUser->role;
        $newRole = $request->new_role;
        $reason = $request->reason;

        DB::beginTransaction();
        try {
            // Update role
            $targetUser->update([
                'role' => $newRole,
                'role_group' => $this->getRoleGroup($newRole),
                'role_updated_at' => now(),
            ]);

            // Sync Spatie roles
            $targetUser->syncRoles([$newRole]);
            $targetUser->cachePermissions();

            // Log activity
            activity()
                ->performedOn($targetUser)
                ->causedBy($user)
                ->withProperties([
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'reason' => $reason
                ])
                ->log('Role changed');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã thay đổi role từ {$oldRole} thành {$newRole}",
                'new_role' => $newRole,
                'new_role_display' => $targetUser->getRoleDisplayName()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk role assignment
     */
    public function bulkAssignRole(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('manage-user-roles')) {
            return response()->json(['error' => 'Không có quyền thay đổi role'], 403);
        }

        $request->validate([
            'user_ids' => 'required|array',
            'new_role' => 'required|exists:roles,name',
            'reason' => 'required|string|max:500'
        ]);

        $userIds = $request->input('user_ids');
        $newRole = $request->input('new_role');
        $reason = $request->input('reason');

        // Check permissions for each user
        $targetUsers = User::whereIn('id', $userIds)->get();
        foreach ($targetUsers as $targetUser) {
            if (!PermissionService::canModerate($user, $targetUser)) {
                return response()->json([
                    'error' => "Không thể thay đổi role của user: {$targetUser->name}"
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            $updated = 0;
            foreach ($targetUsers as $targetUser) {
                $oldRole = $targetUser->role;
                
                $targetUser->update([
                    'role' => $newRole,
                    'role_group' => $this->getRoleGroup($newRole),
                    'role_updated_at' => now(),
                ]);

                $targetUser->syncRoles([$newRole]);
                $targetUser->cachePermissions();

                activity()
                    ->performedOn($targetUser)
                    ->causedBy($user)
                    ->withProperties([
                        'old_role' => $oldRole,
                        'new_role' => $newRole,
                        'reason' => $reason,
                        'bulk_action' => true
                    ])
                    ->log('Role changed via bulk action');

                $updated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật role cho {$updated} users",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * User activity log
     */
    public function activityLog(User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->hasPermissionTo('view-users') || 
            !PermissionService::canModerate($currentUser, $user)) {
            abort(403, 'Không có quyền xem log của user này');
        }

        $activities = activity()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->orWhere('causer_id', $user->id)
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.activity-log', compact('user', 'activities'));
    }

    /**
     * Get user statistics
     */
    private function getUserStats(User $user): array
    {
        $query = User::query();
        
        // Filter based on permissions
        if (!$user->hasPermissionTo('manage-all-users')) {
            $userLevel = PermissionService::getRoleLevel($user);
            $query->whereHas('roles', function($q) use ($userLevel) {
                $allowedRoles = collect(config('mechamap_permissions.role_hierarchy'))
                    ->filter(fn($level) => $level > $userLevel)
                    ->keys();
                $q->whereIn('name', $allowedRoles);
            });
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('is_active', true)->count(),
            'verified_business' => $query->where('is_verified_business', true)->count(),
            'new_today' => $query->whereDate('created_at', today())->count(),
            'by_role_group' => $query->select('role_group', DB::raw('count(*) as count'))
                ->groupBy('role_group')
                ->pluck('count', 'role_group')
                ->toArray(),
        ];
    }

    /**
     * Get role group for role
     */
    private function getRoleGroup(string $role): string
    {
        $roleGroups = [
            'super_admin' => 'system_management',
            'system_admin' => 'system_management',
            'content_admin' => 'system_management',
            'content_moderator' => 'community_management',
            'marketplace_moderator' => 'community_management',
            'community_moderator' => 'community_management',
            'senior_member' => 'community_members',
            'member' => 'community_members',
            'guest' => 'community_members',
            'student' => 'community_members',
            'manufacturer' => 'business_partners',
            'supplier' => 'business_partners',
            'brand' => 'business_partners',
            'verified_partner' => 'business_partners',
        ];

        return $roleGroups[$role] ?? 'community_members';
    }
}
