<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách thành viên
     */
    public function index(Request $request): View
    {
        // Lấy các tham số tìm kiếm và lọc
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Query users
        $usersQuery = User::query()
            ->withCount(['threads', 'posts'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($status === 'active', function ($query) {
                return $query->whereNull('banned_at');
            })
            ->when($status === 'banned', function ($query) {
                return $query->whereNotNull('banned_at');
            })
            ->when($status === 'online', function ($query) {
                return $query->where('last_seen_at', '>=', now()->subMinutes(5));
            });

        // Sắp xếp
        if ($sortBy && in_array($sortBy, ['name', 'username', 'email', 'created_at', 'last_seen_at', 'threads_count', 'posts_count'])) {
            $usersQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Phân trang
        $users = $usersQuery->paginate(20)->withQueryString();

        // Thống kê
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'moderator' => User::where('role', 'moderator')->count(),
            'senior' => User::where('role', 'senior')->count(),
            'member' => User::where('role', 'member')->count(),
            'banned' => User::whereNotNull('banned_at')->count(),
            'online' => User::where('last_seen_at', '>=', now()->subMinutes(5))->count(),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')]
        ];

        return view('admin.users.index', compact('users', 'stats', 'breadcrumbs', 'search', 'role', 'status', 'sortBy', 'sortOrder'));
    }

    /**
     * Hiển thị form tạo thành viên mới
     */
    public function create(): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => 'Thêm thành viên mới', 'url' => route('admin.users.create')]
        ];

        return view('admin.users.create', compact('breadcrumbs'));
    }

    /**
     * Lưu thành viên mới
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'moderator', 'senior', 'member'])],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Tạo user mới
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->email_verified_at = now();

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $avatarPath;
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Thành viên mới đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết thành viên
     */
    public function show(User $user): View
    {
        // Lấy thống kê
        $stats = [
            'threads_count' => $user->threads()->count(),
            'posts_count' => $user->posts()->count(),
            'latest_threads' => $user->threads()->with('forum')->latest()->take(5)->get(),
            'latest_posts' => $user->posts()->with('thread')->latest()->take(5)->get(),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => $user->name, 'url' => route('admin.users.show', $user)]
        ];

        return view('admin.users.show', compact('user', 'stats', 'breadcrumbs'));
    }

    /**
     * Hiển thị form chỉnh sửa thành viên
     */
    public function edit(User $user): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => $user->name, 'url' => route('admin.users.show', $user)],
            ['title' => 'Chỉnh sửa', 'url' => route('admin.users.edit', $user)]
        ];

        return view('admin.users.edit', compact('user', 'breadcrumbs'));
    }

    /**
     * Cập nhật thông tin thành viên
     */
    public function update(Request $request, User $user)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'moderator', 'senior', 'member'])],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'about_me' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cập nhật thông tin cơ bản
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->about_me = $request->about_me;
        $user->website = $request->website;
        $user->location = $request->location;
        $user->signature = $request->signature;

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }

            // Upload avatar mới
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $avatarPath;
        }

        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Thông tin thành viên đã được cập nhật thành công.');
    }

    /**
     * Xóa thành viên
     */
    public function destroy(User $user)
    {
        // Kiểm tra xem có phải đang xóa chính mình không
        if ($user->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
        }

        // Xóa avatar nếu có
        if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
        }

        // Xóa user
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Thành viên đã được xóa thành công.');
    }

    /**
     * Cấm/bỏ cấm thành viên
     */
    public function toggleBan(User $user)
    {
        // Kiểm tra xem có phải đang cấm chính mình không
        if ($user->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'Bạn không thể cấm tài khoản của chính mình.');
        }

        // Cấm/bỏ cấm user
        if ($user->banned_at) {
            $user->banned_at = null;
            $user->banned_reason = null;
            $message = 'Thành viên đã được bỏ cấm thành công.';
        } else {
            $user->banned_at = now();
            $user->banned_reason = request('reason', 'Vi phạm nội quy');
            $message = 'Thành viên đã bị cấm thành công.';
        }

        $user->save();

        return back()->with('success', $message);
    }

    /**
     * Đặt lại mật khẩu thành viên
     */
    public function resetPassword(Request $request, User $user)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cập nhật mật khẩu
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Mật khẩu đã được đặt lại thành công.');
    }

    // ========== QUẢN LÝ ADMIN VÀ MODERATOR ==========

    /**
     * Hiển thị danh sách admin và moderator
     */
    public function admins(Request $request): View
    {
        // Lấy các tham số tìm kiếm và lọc
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Query chỉ admin và moderator
        $adminsQuery = User::query()
            ->whereIn('role', ['admin', 'moderator'])
            ->withCount(['threads', 'posts'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($status === 'active', function ($query) {
                return $query->whereNull('banned_at');
            })
            ->when($status === 'banned', function ($query) {
                return $query->whereNotNull('banned_at');
            })
            ->when($status === 'online', function ($query) {
                return $query->where('last_seen_at', '>=', now()->subMinutes(5));
            });

        // Sắp xếp
        if ($sortBy && in_array($sortBy, ['name', 'username', 'email', 'created_at', 'last_seen_at', 'threads_count', 'posts_count'])) {
            $adminsQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Phân trang
        $admins = $adminsQuery->paginate(20)->withQueryString();

        // Thống kê admin
        $stats = [
            'total' => User::whereIn('role', ['admin', 'moderator'])->count(),
            'admin' => User::where('role', 'admin')->count(),
            'moderator' => User::where('role', 'moderator')->count(),
            'banned' => User::whereIn('role', ['admin', 'moderator'])->whereNotNull('banned_at')->count(),
            'online' => User::whereIn('role', ['admin', 'moderator'])->where('last_seen_at', '>=', now()->subMinutes(5))->count(),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => 'Quản trị viên', 'url' => route('admin.users.admins')]
        ];

        return view('admin.users.admins.index', compact('admins', 'stats', 'breadcrumbs', 'search', 'role', 'status', 'sortBy', 'sortOrder'));
    }

    /**
     * Hiển thị form tạo admin/moderator mới
     */
    public function createAdmin(): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => 'Quản trị viên', 'url' => route('admin.users.admins')],
            ['title' => 'Thêm quản trị viên', 'url' => route('admin.users.admins.create')]
        ];

        return view('admin.users.admins.create', compact('breadcrumbs'));
    }

    /**
     * Lưu admin/moderator mới
     */
    public function storeAdmin(Request $request)
    {
        // Validate dữ liệu (chỉ cho phép tạo admin/moderator)
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'moderator'])],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'about_me' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Tạo admin/moderator mới
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->about_me = $request->about_me;
        $user->website = $request->website;
        $user->location = $request->location;
        $user->signature = $request->signature;
        $user->email_verified_at = now();

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $avatarPath;
        }

        $user->save();

        // Gán role permissions
        $user->assignRole($request->role);

        return redirect()->route('admin.users.admins')
            ->with('success', 'Quản trị viên mới đã được tạo thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa admin/moderator
     */
    public function editAdmin(User $user): View
    {
        // Kiểm tra xem có phải admin/moderator không
        if (!in_array($user->role, ['admin', 'moderator'])) {
            abort(404);
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => 'Quản trị viên', 'url' => route('admin.users.admins')],
            ['title' => $user->name, 'url' => route('admin.users.admins.edit', $user)]
        ];

        return view('admin.users.admins.edit', compact('user', 'breadcrumbs'));
    }

    /**
     * Cập nhật thông tin admin/moderator
     */
    public function updateAdmin(Request $request, User $user)
    {
        // Kiểm tra xem có phải admin/moderator không
        if (!in_array($user->role, ['admin', 'moderator'])) {
            abort(404);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'moderator'])],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'about_me' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cập nhật thông tin
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->about_me = $request->about_me;
        $user->website = $request->website;
        $user->location = $request->location;
        $user->signature = $request->signature;

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar && !str_contains($user->avatar, 'ui-avatars.com')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }

            // Upload avatar mới
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $avatarPath;
        }

        $user->save();

        // Cập nhật role permissions nếu cần
        if ($user->roles->first()?->name !== $request->role) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('admin.users.admins')
            ->with('success', 'Thông tin quản trị viên đã được cập nhật thành công.');
    }

    /**
     * Hiển thị form quản lý permissions cho admin/moderator
     */
    public function editPermissions(User $user): View
    {
        // Kiểm tra xem có phải admin/moderator không
        if (!in_array($user->role, ['admin', 'moderator'])) {
            abort(404);
        }

        // Lấy tất cả permissions
        $allPermissions = \Spatie\Permission\Models\Permission::all()->groupBy('group');

        // Lấy permissions hiện tại của user
        $userPermissions = $user->permissions->pluck('name')->toArray();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => 'Quản trị viên', 'url' => route('admin.users.admins')],
            ['title' => $user->name, 'url' => route('admin.users.admins.edit', $user)],
            ['title' => 'Phân quyền', 'url' => route('admin.users.admins.permissions', $user)]
        ];

        return view('admin.users.admins.permissions', compact('user', 'allPermissions', 'userPermissions', 'breadcrumbs'));
    }

    /**
     * Cập nhật permissions cho admin/moderator
     */
    public function updatePermissions(Request $request, User $user)
    {
        // Kiểm tra xem có phải admin/moderator không
        if (!in_array($user->role, ['admin', 'moderator'])) {
            abort(404);
        }

        // Validate permissions
        $validator = Validator::make($request->all(), [
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Cập nhật permissions
        $permissions = $request->input('permissions', []);
        $user->syncPermissions($permissions);

        return redirect()->route('admin.users.admins')
            ->with('success', 'Phân quyền đã được cập nhật thành công.');
    }

    // ========== QUẢN LÝ THÀNH VIÊN THƯỜNG ==========

    /**
     * Hiển thị danh sách thành viên thường (Senior và Member)
     */
    public function members(Request $request): View
    {
        // Lấy các tham số tìm kiếm và lọc
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Query chỉ senior, member và guest
        $membersQuery = User::query()
            ->whereIn('role', ['senior', 'member', 'guest'])
            ->withCount(['threads', 'posts'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($status === 'active', function ($query) {
                return $query->whereNull('banned_at');
            })
            ->when($status === 'banned', function ($query) {
                return $query->whereNotNull('banned_at');
            })
            ->when($status === 'online', function ($query) {
                return $query->where('last_seen_at', '>=', now()->subMinutes(5));
            });

        // Sắp xếp
        if ($sortBy && in_array($sortBy, ['name', 'username', 'email', 'created_at', 'last_seen_at', 'threads_count', 'posts_count'])) {
            $membersQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Phân trang
        $members = $membersQuery->paginate(20)->withQueryString();

        // Thống kê members
        $stats = [
            'total' => User::whereIn('role', ['senior', 'member', 'guest'])->count(),
            'senior' => User::where('role', 'senior')->count(),
            'member' => User::where('role', 'member')->count(),
            'guest' => User::where('role', 'guest')->count(),
            'active_today' => User::whereIn('role', ['senior', 'member', 'guest'])->where('last_seen_at', '>=', now()->startOfDay())->count(),
            'banned' => User::whereIn('role', ['senior', 'member', 'guest'])->whereNotNull('banned_at')->count(),
            'online' => User::whereIn('role', ['senior', 'member', 'guest'])->where('last_seen_at', '>=', now()->subMinutes(5))->count(),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý thành viên', 'url' => route('admin.users.index')],
            ['title' => 'Thành viên', 'url' => route('admin.users.members')]
        ];

        return view('admin.users.members.index', compact('members', 'stats', 'breadcrumbs', 'search', 'role', 'status', 'sortBy', 'sortOrder'));
    }

    /**
     * Bulk actions cho multiple users
     */
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:activate,deactivate,delete',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id'
            ]);

            $action = $request->action;
            $userIds = $request->user_ids;
            $count = 0;

            // Không cho phép thao tác trên chính mình
            $userIds = array_filter($userIds, function ($id) {
                return $id != Auth::id();
            });

            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thực hiện thao tác trên chính tài khoản của bạn.'
                ]);
            }

            switch ($action) {
                case 'activate':
                    $count = User::whereIn('id', $userIds)->update(['status' => 'active']);
                    $message = "Đã kích hoạt {$count} tài khoản thành công.";
                    break;

                case 'deactivate':
                    $count = User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                    $message = "Đã khóa {$count} tài khoản thành công.";
                    break;

                case 'delete':
                    // Kiểm tra không được xóa admin
                    $adminCount = User::whereIn('id', $userIds)
                        ->whereIn('role', ['admin', 'moderator'])
                        ->count();

                    if ($adminCount > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Không thể xóa tài khoản quản trị viên.'
                        ]);
                    }

                    $count = User::whereIn('id', $userIds)->delete();
                    $message = "Đã xóa {$count} tài khoản thành công.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle status của user
     */
    public function toggleStatus(Request $request, User $user)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            // Không cho phép thay đổi trạng thái chính mình
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thay đổi trạng thái tài khoản của chính bạn.'
                ]);
            }

            $user->update(['status' => $request->status]);

            $statusText = $request->status === 'active' ? 'kích hoạt' : 'khóa';

            return response()->json([
                'success' => true,
                'message' => "Đã {$statusText} tài khoản {$user->name} thành công.",
                'new_status' => $request->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export danh sách thành viên ra Excel
     */
    public function exportMembers(Request $request)
    {
        try {
            // Lấy dữ liệu thành viên với các filter hiện tại
            $query = User::whereIn('role', ['senior', 'member', 'guest']);

            // Áp dụng các filter từ request
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('email_verified')) {
                if ($request->email_verified === '1') {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            // Chuẩn bị dữ liệu cho Excel
            $data = [];
            $data[] = [
                'STT',
                'Tên đầy đủ',
                'Email',
                'Số điện thoại',
                'Vai trò',
                'Trạng thái',
                'Email xác thực',
                'Ngày đăng ký',
                'Hoạt động cuối'
            ];

            foreach ($users as $index => $user) {
                $data[] = [
                    $index + 1,
                    $user->name,
                    $user->email,
                    $user->phone ?? 'Chưa có',
                    $user->getRoleDisplayName(),
                    $user->status === 'active' ? 'Hoạt động' : ($user->status === 'inactive' ? 'Tạm khóa' : 'Chờ duyệt'),
                    $user->email_verified_at ? 'Đã xác thực' : 'Chưa xác thực',
                    $user->created_at->format('d/m/Y H:i'),
                    $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa có'
                ];
            }

            // Tạo file CSV (simple approach)
            $filename = 'danh_sach_thanh_vien_' . date('Y_m_d_H_i_s') . '.csv';
            $handle = fopen('php://output', 'w');

            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Add BOM for UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi export: ' . $e->getMessage());
        }
    }

    /**
     * Export danh sách quản trị viên ra Excel
     */
    public function exportAdmins(Request $request)
    {
        try {
            // Lấy dữ liệu quản trị viên với các filter hiện tại
            $query = User::whereIn('role', ['admin', 'moderator']);

            // Áp dụng các filter từ request
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            // Chuẩn bị dữ liệu cho Excel
            $data = [];
            $data[] = [
                'STT',
                'Tên đầy đủ',
                'Email',
                'Số điện thoại',
                'Vai trò',
                'Trạng thái',
                'Email xác thực',
                'Ngày tạo',
                'Hoạt động cuối',
                'Số quyền hạn'
            ];

            foreach ($users as $index => $user) {
                $data[] = [
                    $index + 1,
                    $user->name,
                    $user->email,
                    $user->phone ?? 'Chưa có',
                    $user->getRoleDisplayName(),
                    $user->status === 'active' ? 'Hoạt động' : ($user->status === 'inactive' ? 'Tạm khóa' : 'Chờ duyệt'),
                    $user->email_verified_at ? 'Đã xác thực' : 'Chưa xác thực',
                    $user->created_at->format('d/m/Y H:i'),
                    $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa có',
                    $user->getAllPermissions()->count() ?? 0
                ];
            }

            // Tạo file CSV (simple approach)
            $filename = 'danh_sach_quan_tri_vien_' . date('Y_m_d_H_i_s') . '.csv';
            $handle = fopen('php://output', 'w');

            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Add BOM for UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi export: ' . $e->getMessage());
        }
    }
}
