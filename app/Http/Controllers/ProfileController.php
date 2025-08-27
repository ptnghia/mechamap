<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\MarketplaceOrder;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * The user activity service instance.
     */
    protected UserActivityService $activityService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserActivityService $activityService)
    {
        $this->activityService = $activityService;
    }
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::query();
        $view = $request->input('view', 'list'); // list or grid
        $filter = $request->input('filter', 'all'); // all, online, staff

        // Search by name or username
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role groups
        if ($role = $request->input('role')) {
            switch ($role) {
                // System Management Group
                case 'system_management':
                    $query->whereIn('role', ['super_admin', 'system_admin', 'content_admin']);
                    break;

                // Community Management Group
                case 'community_management':
                    $query->whereIn('role', ['content_moderator', 'marketplace_moderator', 'community_moderator']);
                    break;

                // Community Members Group
                case 'community_members':
                    $query->whereIn('role', ['senior_member', 'member', 'guest']);
                    break;

                // Business Partners Group
                case 'business_partners':
                    $query->whereIn('role', ['verified_partner', 'manufacturer', 'supplier', 'brand']);
                    break;

                // Legacy support for old role group names
                case 'admin':
                    $query->whereIn('role', ['super_admin', 'system_admin', 'content_admin']);
                    break;
                case 'moderator':
                    $query->whereIn('role', ['content_moderator', 'marketplace_moderator', 'community_moderator']);
                    break;
                case 'member':
                    $query->whereIn('role', ['senior_member', 'member', 'guest']);
                    break;
                case 'business':
                    $query->whereIn('role', ['verified_partner', 'manufacturer', 'supplier', 'brand']);
                    break;

                // Individual roles
                default:
                    if (in_array($role, ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator', 'senior_member', 'member', 'guest', 'verified_partner', 'manufacturer', 'supplier', 'brand'])) {
                        $query->where('role', $role);
                    }
                    break;
            }
        }

        // Apply filters
        switch ($filter) {
            case 'online':
                $query->where('last_seen_at', '>=', now()->subMinutes(15));
                break;
            case 'staff':
                // Chỉ hiển thị nhóm quản trị cộng đồng, ẩn nhóm quản trị hệ thống
                $query->whereIn('role', [
                    'content_moderator', 'marketplace_moderator', 'community_moderator'
                ]);
                break;
            case 'all':
            default:
                // Ẩn nhóm quản trị hệ thống khỏi danh sách công khai
                $query->whereNotIn('role', [
                    'super_admin', 'system_admin', 'content_admin'
                ]);
                break;
        }

        // Sort users
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'posts':
                $query->withCount('comments')->orderByDesc('comments_count');
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

        // Always load counts and relationships (use comments as posts in MechaMap)
        $users = $query->withCount(['comments as posts_count', 'threads', 'followers'])
            ->with(['followers'])
            ->paginate(12)
            ->withQueryString();

        // Get statistics for sidebar - ẩn nhóm quản trị hệ thống
        $stats = [
            'total_members' => User::whereNotIn('role', [
                'super_admin', 'system_admin', 'content_admin'
            ])->count(),
            'newest_member' => User::whereNotIn('role', [
                'super_admin', 'system_admin', 'content_admin'
            ])->latest()->first(),
            'online_members' => User::where('last_seen_at', '>=', now()->subMinutes(15))->count(),
        ];

        // Get top contributors this month (based on comments)
        $topContributors = User::withCount(['comments' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->having('comments_count', '>', 0)
            ->orderByDesc('comments_count')
            ->take(5)
            ->get();

        // Get staff members - chỉ hiển thị nhóm quản trị cộng đồng
        $staffMembers = User::whereIn('role', [
                'content_moderator', 'marketplace_moderator', 'community_moderator'
            ])
            ->take(10)
            ->get();

        return view('profile.index', compact('users', 'view', 'filter', 'stats', 'topContributors', 'staffMembers'));
    }

    /**
     * Display online members.
     */
    public function online(Request $request): View
    {
        $request->merge(['filter' => 'online']);
        return $this->index($request);
    }

    /**
     * Display staff members.
     */
    public function staff(Request $request): View
    {
        $request->merge(['filter' => 'staff']);
        return $this->index($request);
    }

    /**
     * Display leaderboard.
     */
    public function leaderboard(Request $request): View
    {
        // Top posters based on comments (actual posts in MechaMap are comments)
        $topPosters = User::withCount('comments')
            ->having('comments_count', '>', 0)
            ->orderByDesc('comments_count')
            ->take(20)
            ->get();

        $topThreadCreators = User::withCount('threads')
            ->having('threads_count', '>', 0)
            ->orderByDesc('threads_count')
            ->take(20)
            ->get();

        $topFollowed = User::withCount('followers')
            ->having('followers_count', '>', 0)
            ->orderByDesc('followers_count')
            ->take(20)
            ->get();

        return view('profile.leaderboard', compact('topPosters', 'topThreadCreators', 'topFollowed'));
    }

    /**
     * Display the user's profile.
     */
    public function show(User $user): View
    {
        // Phân biệt user type để hiển thị stats phù hợp
        $isBusinessUser = in_array($user->role, ['manufacturer', 'supplier', 'brand']);

        if ($isBusinessUser) {
            // Stats cho Business User
            $stats = [
                'products_count' => 0, // Tạm thời set 0, sẽ implement sau
                'total_reviews' => $user->total_reviews ?? 0,
                'business_rating' => $user->business_rating ?? 0,
                'business_score' => $this->calculateBusinessScore($user),
            ];
        } else {
            // Stats cho Personal User
            $stats = [
                'replies' => $user->comments()->count(),
                'discussions_created' => $user->threads()->count(),
                'reaction_score' => $user->reaction_score ?? 0,
                'profile_views' => $user->profile_views ?? 0,
            ];
        }

        // Lấy danh sách người theo dõi và đang theo dõi
        $followers = $user->followers()->count();
        $following = $user->following()->count();

        // Lấy threads của user (thay thế profile posts)
        $userThreads = $user->threads()
            ->withCount(['comments', 'reactions'])
            ->with(['category', 'attachments', 'tags'])
            ->latest()
            ->paginate(10);

        // Lấy các hoạt động gần đây
        $activities = $user->activities()
            ->with(['thread', 'comment.thread'])
            ->latest()
            ->take(10)
            ->get();

        // Lấy portfolio items (từ showcases + threads có attachments)
        $portfolioItems = $this->getPortfolioItems($user);

        // Kiểm tra tiến độ thiết lập tài khoản
        $setupProgress = $this->calculateSetupProgress($user);

        // Sử dụng view profile show
        return view('profile.show', compact(
            'user',
            'stats',
            'followers',
            'following',
            'userThreads',
            'activities',
            'portfolioItems',
            'setupProgress',
            'isBusinessUser'
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

        // Log activity
        $this->activityService->logProfileUpdated($request->user());

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $user->updateAvatar($request->file('avatar'));

            // Log activity
            $this->activityService->logProfileUpdated($user);
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
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
     * AJAX theo dõi một người dùng.
     */
    public function ajaxFollow(User $user)
    {
        $currentUser = Auth::user();

        // Không thể tự theo dõi chính mình
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể theo dõi chính mình.'
            ], 400);
        }

        // Kiểm tra xem đã theo dõi chưa
        if ($currentUser->isFollowing($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã theo dõi người dùng này rồi.'
            ], 400);
        }

        // Thêm vào danh sách theo dõi
        $currentUser->following()->attach($user->id);

        // Cập nhật số lượng followers
        $followersCount = $user->followers()->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã theo dõi người dùng thành công.',
            'followers_count' => $followersCount,
            'is_following' => true
        ]);
    }

    /**
     * AJAX hủy theo dõi một người dùng.
     */
    public function ajaxUnfollow(User $user)
    {
        $currentUser = Auth::user();

        // Kiểm tra xem đã theo dõi chưa
        if (!$currentUser->isFollowing($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa theo dõi người dùng này.'
            ], 400);
        }

        // Xóa khỏi danh sách theo dõi
        $currentUser->following()->detach($user->id);

        // Cập nhật số lượng followers
        $followersCount = $user->followers()->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy theo dõi người dùng thành công.',
            'followers_count' => $followersCount,
            'is_following' => false
        ]);
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

    /**
     * Calculate business score for business users
     */
    private function calculateBusinessScore(User $user): int
    {
        $score = 0;

        // Base score from verification
        if ($user->is_verified_business) {
            $score += 50;
        }

        // Score from rating
        if ($user->business_rating) {
            $score += ($user->business_rating * 10);
        }

        // Score from reviews count
        $score += min(($user->total_reviews ?? 0) * 2, 50);

        // Score from products count (tạm thời bỏ qua, sẽ implement sau)
        // $score += min($user->products()->count() * 5, 100);

        return min($score, 500); // Cap at 500
    }

    /**
     * Get portfolio items from showcases and threads with attachments
     */
    private function getPortfolioItems(User $user)
    {
        $portfolioItems = collect();

        // Get showcases if user has them (tạm thời bỏ qua, sẽ implement sau)
        // if (method_exists($user, 'showcases')) {
        //     $showcases = $user->showcases()
        //         ->with(['images', 'category', 'tags'])
        //         ->published()
        //         ->latest()
        //         ->take(6)
        //         ->get();
        //     $portfolioItems = $portfolioItems->merge($showcases);
        // }

        // Get threads with attachments
        $threadsWithAttachments = $user->threads()
            ->whereHas('attachments')
            ->with(['attachments', 'category', 'tags'])
            ->latest()
            ->take(6)
            ->get();
        $portfolioItems = $portfolioItems->merge($threadsWithAttachments);

        // Sort by created_at and limit
        return $portfolioItems->sortByDesc('created_at')->take(12);
    }

    /**
     * Display user's orders.
     */
    public function orders(Request $request): View
    {
        $user = Auth::user();

        $orders = MarketplaceOrder::where('customer_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.orders', compact('orders'));
    }
}
