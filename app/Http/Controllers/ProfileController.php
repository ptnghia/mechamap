<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Search by name or username
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Sort users
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'posts':
                $query->withCount('posts')->orderByDesc('posts_count');
                break;
            case 'threads':
                $query->withCount('threads')->orderByDesc('threads_count');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $users = $query->withCount(['posts', 'threads'])
            ->paginate(20)
            ->withQueryString();

        return view('profile.index', compact('users'));
    }

    /**
     * Display the user's profile.
     */
    public function show(User $user): View
    {
        // Lấy thông tin thống kê
        $stats = [
            'replies' => $user->posts()->count(),
            'discussions_created' => $user->threads()->count(),
            'reaction_score' => $user->reaction_score,
            'points' => $user->points,
        ];

        // Lấy danh sách người theo dõi
        $followers = $user->followers()->count();

        // Lấy danh sách người đang theo dõi
        $following = $user->following()->count();

        // Lấy các bài viết trên trang cá nhân
        $profilePosts = $user->receivedProfilePosts()
            ->with('user')
            ->latest()
            ->paginate(10);

        // Lấy các hoạt động gần đây
        $activities = $user->activities()
            ->latest()
            ->take(10)
            ->get();

        // Kiểm tra tiến độ thiết lập tài khoản
        $setupProgress = $this->calculateSetupProgress($user);

        // Sử dụng view mới theo mẫu SkyscraperCity
        return view('profile.show-skyscraper', compact(
            'user',
            'stats',
            'followers',
            'following',
            'profilePosts',
            'activities',
            'setupProgress'
        ));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Tính toán tiến độ thiết lập tài khoản.
     */
    private function calculateSetupProgress(User $user): int
    {
        $progress = 0;

        // Đã xác minh email
        if ($user->email_verified_at) {
            $progress++;
        }

        // Đã thêm avatar
        if ($user->avatar) {
            $progress++;
        }

        // Đã thêm thông tin cá nhân
        if ($user->about_me) {
            $progress++;
        }

        // Đã thêm vị trí
        if ($user->location) {
            $progress++;
        }

        // Đã tạo bài viết hoặc chủ đề
        if ($user->posts()->count() > 0 || $user->threads()->count() > 0) {
            $progress++;
        }

        return $progress;
    }

    /**
     * Theo dõi một người dùng.
     */
    public function follow(User $user)
    {
        $currentUser = Auth::user();

        // Không thể tự theo dõi chính mình
        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Bạn không thể theo dõi chính mình.');
        }

        // Kiểm tra xem đã theo dõi chưa
        if ($currentUser->following()->where('following_id', $user->id)->exists()) {
            return back()->with('error', 'Bạn đã theo dõi người dùng này rồi.');
        }

        // Thêm vào danh sách theo dõi
        $currentUser->following()->attach($user->id);

        return back()->with('success', 'Đã theo dõi người dùng thành công.');
    }

    /**
     * Hủy theo dõi một người dùng.
     */
    public function unfollow(User $user)
    {
        $currentUser = Auth::user();

        // Kiểm tra xem đã theo dõi chưa
        if (!$currentUser->following()->where('following_id', $user->id)->exists()) {
            return back()->with('error', 'Bạn chưa theo dõi người dùng này.');
        }

        // Xóa khỏi danh sách theo dõi
        $currentUser->following()->detach($user->id);

        return back()->with('success', 'Đã hủy theo dõi người dùng thành công.');
    }

    /**
     * Đăng bài viết lên trang cá nhân của người dùng.
     */
    public function storeProfilePost(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $currentUser = Auth::user();

        // Tạo bài viết mới
        $profilePost = $user->receivedProfilePosts()->create([
            'content' => $request->content,
            'user_id' => $currentUser->id,
        ]);

        return back()->with('success', 'Đã đăng bài viết thành công.');
    }
}
