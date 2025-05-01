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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
                \Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
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
            \Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
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
}
