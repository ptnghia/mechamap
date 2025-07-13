<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadRating;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard tổng quan của user - Community Members (member, senior_member, guest)
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Role-specific dashboard data
        $dashboardData = $this->getDashboardDataByRole($user, $role);

        // Choose view template based on role
        $viewTemplate = $this->getViewTemplate($role);

        return view($viewTemplate, $dashboardData);
    }

    /**
     * Get dashboard data based on user role
     */
    private function getDashboardDataByRole($user, $role): array
    {
        $baseStats = [
            'threads_created' => Thread::where('user_id', $user->id)->count(),
            'threads_bookmarked' => ThreadBookmark::where('user_id', $user->id)->count(),
            'ratings_given' => ThreadRating::where('user_id', $user->id)->count(),
            'average_rating_received' => Thread::where('user_id', $user->id)
                ->where('ratings_count', '>', 0)
                ->avg('average_rating'),
        ];

        // Role-specific additions
        if ($role === 'guest') {
            $baseStats['following_count'] = $user->following()->count();
            $baseStats['followers_count'] = $user->followers()->count();
            $baseStats['marketplace_views'] = 0; // Placeholder for marketplace activity
        }

        if (in_array($role, ['member', 'senior_member'])) {
            $baseStats['comments_count'] = $user->posts()->count();
            $baseStats['reputation_score'] = $user->reaction_score ?? 0;
            $baseStats['forum_activity_level'] = $this->calculateActivityLevel($user);
        }

        // Add pending counts for dashboard navigation
        // Note: Threads don't have status column, so we'll use 0 for now
        $pendingThreadsCount = 0;

        // Note: Posts don't have status column, so we'll use 0 for now
        $pendingCommentsCount = 0;

        // Note: No rejected content tracking yet, so we'll use 0 for now
        $rejectedCount = 0;

        // Get recent threads for dashboard display
        $recentThreads = Thread::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent comments for dashboard display
        $recentComments = Comment::with(['thread'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent activities for dashboard display (placeholder for now)
        $recentActivities = collect(); // Empty collection for now

        // Get statistics for dashboard widgets
        $statistics = [
            'total_views' => Thread::where('user_id', $user->id)->sum('view_count'),
            'total_replies' => Thread::where('user_id', $user->id)->sum('replies'),
            'total_ratings' => Thread::where('user_id', $user->id)->sum('ratings_count'),
        ];

        return [
            'user' => $user,
            'role' => $role,
            'stats' => $baseStats,
            'navigation' => $this->getNavigationMenu($role),
            'widgets' => $this->getDashboardWidgets($role, $user),
            'quick_actions' => $this->getQuickActions($role),
            'pendingThreadsCount' => $pendingThreadsCount,
            'pendingCommentsCount' => $pendingCommentsCount,
            'rejectedCount' => $rejectedCount,
            'recentThreads' => $recentThreads,
            'recentComments' => $recentComments,
            'recentActivities' => $recentActivities,
            'statistics' => $statistics,
        ];
    }

    /**
     * Get view template based on role
     */
    private function getViewTemplate($role): string
    {
        return match($role) {
            'guest' => 'user.dashboard-guest',
            'member' => 'user.dashboard-member',
            'senior_member' => 'user.dashboard-senior',
            default => 'user.dashboard',
        };
    }

    /**
     * Get navigation menu based on role
     */
    private function getNavigationMenu($role): array
    {
        $baseMenu = [
            'dashboard' => [
                'title' => __('nav.user.dashboard'),
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'user.dashboard',
                'active' => true,
            ],
            'profile' => [
                'title' => __('nav.user.profile'),
                'icon' => 'fas fa-user',
                'route' => 'profile.edit',
            ],
        ];

        // Guest specific menu
        if ($role === 'guest') {
            $baseMenu['marketplace'] = [
                'title' => __('nav.marketplace'),
                'icon' => 'fas fa-store',
                'route' => 'marketplace.index',
            ];
            $baseMenu['following'] = [
                'title' => __('messages.following'),
                'icon' => 'fas fa-heart',
                'route' => 'user.following',
            ];
        }

        // Member and Senior Member menu
        if (in_array($role, ['member', 'senior_member'])) {
            $baseMenu['my_threads'] = [
                'title' => __('messages.my_threads'),
                'icon' => 'fas fa-comments',
                'route' => 'user.my-threads',
            ];
            $baseMenu['bookmarks'] = [
                'title' => __('messages.bookmarks'),
                'icon' => 'fas fa-bookmark',
                'route' => 'user.bookmarks',
            ];
            $baseMenu['activity'] = [
                'title' => __('messages.activity'),
                'icon' => 'fas fa-chart-line',
                'route' => 'user.activity',
            ];
        }

        return $baseMenu;
    }

    /**
     * Get dashboard widgets based on role
     */
    private function getDashboardWidgets($role, $user): array
    {
        $widgets = [];

        // Common widgets for all community members
        if (in_array($role, ['member', 'senior_member'])) {
            $widgets['recent_threads'] = [
                'title' => __('messages.recent_threads'),
                'data' => Thread::where('user_id', $user->id)
                    ->with(['forum'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Guest specific widgets
        if ($role === 'guest') {
            $widgets['marketplace_highlights'] = [
                'title' => __('messages.marketplace_highlights'),
                'data' => \App\Models\MarketplaceProduct::where('product_type', 'digital')
                    ->where('status', 'approved')
                    ->where('is_active', true)
                    ->latest()
                    ->limit(6)
                    ->get(),
            ];
        }

        return $widgets;
    }

    /**
     * Get quick actions based on role
     */
    private function getQuickActions($role): array
    {
        $actions = [];

        if (in_array($role, ['member', 'senior_member'])) {
            $actions['create_thread'] = [
                'title' => __('messages.create_thread'),
                'icon' => 'fas fa-plus',
                'route' => 'threads.create',
                'class' => 'btn-primary',
            ];
        }

        if ($role === 'guest') {
            $actions['browse_marketplace'] = [
                'title' => __('messages.browse_marketplace'),
                'icon' => 'fas fa-shopping-cart',
                'route' => 'marketplace.index',
                'class' => 'btn-success',
            ];
        }

        return $actions;
    }

    /**
     * Calculate user activity level
     */
    private function calculateActivityLevel($user): string
    {
        $threadsCount = Thread::where('user_id', $user->id)->count();
        $commentsCount = 0; // TODO: Implement posts count when model is available
        $totalActivity = $threadsCount + $commentsCount;

        if ($totalActivity >= 100) return 'very_active';
        if ($totalActivity >= 50) return 'active';
        if ($totalActivity >= 10) return 'moderate';
        return 'new';
    }

    /**
     * Calculate user activity streak in days
     */
    private function calculateActivityStreak($user): int
    {
        // Simple implementation - count consecutive days with activity
        $streak = 0;
        $currentDate = now()->startOfDay();

        for ($i = 0; $i < 30; $i++) { // Check last 30 days
            $hasActivity = Thread::where('user_id', $user->id)
                ->whereDate('created_at', $currentDate)
                ->exists();

            if ($hasActivity) {
                $streak++;
                $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Quản lý bookmarks của user.
     */
    public function bookmarks(Request $request)
    {
        $user = Auth::user();

        $query = ThreadBookmark::with(['thread' => function ($q) {
            $q->with(['user', 'forum'])
                ->withCount(['allComments as comments_count', 'ratings']);
        }])
            ->where('user_id', $user->id);

        // Filter theo folder
        if ($request->folder) {
            $query->where('folder', $request->folder);
        }

        // Search trong thread title hoặc notes
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('notes', 'like', "%{$request->search}%")
                    ->orWhereHas('thread', function ($threadQuery) use ($request) {
                        $threadQuery->where('title', 'like', "%{$request->search}%");
                    });
            });
        }

        $bookmarks = $query->latest()->paginate(20);

        // Available folders
        $folders = ThreadBookmark::select('folder', DB::raw('count(*) as count'))
            ->where('user_id', $user->id)
            ->whereNotNull('folder')
            ->groupBy('folder')
            ->get();

        // Stats
        $stats = [
            'total_bookmarks' => ThreadBookmark::where('user_id', $user->id)->count(),
            'total_folders' => ThreadBookmark::where('user_id', $user->id)->whereNotNull('folder')->distinct('folder')->count(),
            'recent_bookmarks' => ThreadBookmark::where('user_id', $user->id)->where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('user.bookmarks-new', compact('bookmarks', 'folders', 'stats'));
    }

    /**
     * Quản lý comments của user.
     */
    public function comments(Request $request)
    {
        $user = Auth::user();

        $query = Comment::with(['thread' => function ($q) {
            $q->with(['user', 'forum']);
        }])
            ->where('user_id', $user->id);

        // Filter theo thread
        if ($request->thread_id) {
            $query->where('thread_id', $request->thread_id);
        }

        // Filter theo status
        if ($request->status) {
            $query->where('moderation_status', $request->status);
        }

        $comments = $query->latest()->paginate(20);
        $threads = Thread::where('user_id', $user->id)->orderBy('title')->get();

        return view('user.comments', compact('comments', 'threads'));
    }

    /**
     * Quản lý ratings của user.
     */
    public function ratings(Request $request)
    {
        $user = Auth::user();

        $query = ThreadRating::with(['thread' => function ($q) {
            $q->with(['user', 'forum'])
                ->withCount(['allComments as comments_count', 'ratings']);
        }])
            ->where('user_id', $user->id);

        // Filter theo rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter theo có review text hay không
        if ($request->has_review === 'yes') {
            $query->whereNotNull('review');
        } elseif ($request->has_review === 'no') {
            $query->whereNull('review');
        }

        // Search trong review text
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('review', 'like', "%{$request->search}%")
                    ->orWhereHas('thread', function ($threadQuery) use ($request) {
                        $threadQuery->where('title', 'like', "%{$request->search}%");
                    });
            });
        }

        $ratings = $query->latest()->paginate(20);

        // Rating distribution
        $ratingDistribution = ThreadRating::where('user_id', $user->id)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();

        // Rating stats for the view
        $stats = [
            'total_ratings_given' => ThreadRating::where('user_id', $user->id)->count(),
            'avg_rating_given' => ThreadRating::where('user_id', $user->id)->avg('rating') ?? 0,
            'total_ratings_received' => 0, // TODO: Implement when we have ratings on user's threads
            'avg_rating_received' => 0, // TODO: Implement when we have ratings on user's threads
        ];

        // Prepare data for charts
        $ratingsGivenDistribution = [
            $ratingDistribution[5] ?? 0,
            $ratingDistribution[4] ?? 0,
            $ratingDistribution[3] ?? 0,
            $ratingDistribution[2] ?? 0,
            $ratingDistribution[1] ?? 0,
        ];

        // For now, ratings received is empty (TODO: implement when we have ratings on user's threads)
        $ratingsReceivedDistribution = [0, 0, 0, 0, 0];

        return view('user.ratings', compact('ratings', 'ratingDistribution', 'stats', 'ratingsGivenDistribution', 'ratingsReceivedDistribution'));
    }

    /**
     * Threads của user.
     */
    public function myThreads(Request $request)
    {
        $user = Auth::user();

        $query = Thread::with(['forum', 'tags'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->where('user_id', $user->id);

        // Filter theo moderation status
        if ($request->status) {
            $query->where('moderation_status', $request->status);
        }

        // Filter theo thread type
        if ($request->type) {
            $query->where('thread_type', $request->type);
        }

        // Filter theo solved status
        if ($request->solved === 'yes') {
            $query->where('is_solved', true);
        } elseif ($request->solved === 'no') {
            $query->where('is_solved', false);
        }

        $threads = $query->latest()->paginate(20);

        // Thread stats
        $threadStats = [
            'total' => Thread::where('user_id', $user->id)->count(),
            'approved' => Thread::where('user_id', $user->id)->where('moderation_status', 'approved')->count(),
            'pending' => Thread::where('user_id', $user->id)->where('moderation_status', 'under_review')->count(),
            'flagged' => 0, // Column is_flagged doesn't exist yet
            'solved' => Thread::where('user_id', $user->id)->where('is_solved', true)->count(),
            'average_rating' => Thread::where('user_id', $user->id)
                ->where('ratings_count', '>', 0)
                ->avg('average_rating'),
            'total_views' => Thread::where('user_id', $user->id)->sum('view_count'),
            'total_bookmarks' => Thread::where('user_id', $user->id)->sum('bookmark_count'),
        ];

        // Get forums for filter
        $forums = \App\Models\Forum::orderBy('name')->get();

        // Prepare stats for view
        $stats = [
            'total_threads' => $threadStats['total'] ?? 0,
            'total_views' => $threadStats['total_views'] ?? 0,
            'avg_rating' => $threadStats['average_rating'] ?? 0,
            'pending_threads' => $threadStats['pending'] ?? 0,
        ];

        return view('user.my-threads', compact('threads', 'forums', 'stats'));
    }

    /**
     * Activity feed của user.
     */
    public function activity(Request $request)
    {
        $user = Auth::user();

        // Combine different activity types
        $activities = collect();

        // Recent bookmarks
        $recentBookmarks = ThreadBookmark::with(['thread.user', 'thread.forum'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($bookmark) {
                return [
                    'type' => 'bookmark',
                    'created_at' => $bookmark->created_at,
                    'data' => $bookmark,
                ];
            });

        // Recent ratings
        $recentRatings = ThreadRating::with(['thread.user', 'thread.forum'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($rating) {
                return [
                    'type' => 'rating',
                    'created_at' => $rating->created_at,
                    'data' => $rating,
                ];
            });

        // Recent threads
        $recentThreads = Thread::with(['forum'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($thread) {
                return [
                    'type' => 'thread',
                    'created_at' => $thread->created_at,
                    'data' => $thread,
                ];
            });

        // Merge và sort activities
        $activities = $recentBookmarks
            ->concat($recentRatings)
            ->concat($recentThreads)
            ->sortByDesc('created_at')
            ->take(20);

        // Activity stats
        $stats = [
            'total_activities' => $activities->count(),
            'today_activities' => $activities->filter(function($activity) {
                return $activity->created_at->isToday();
            })->count(),
            'week_activities' => $activities->filter(function($activity) {
                return $activity->created_at->isCurrentWeek();
            })->count(),
            'streak_days' => $this->calculateActivityStreak($user),
        ];

        return view('user.activity', compact('activities', 'stats'));
    }

    /**
     * Bookmark folder management.
     */
    public function bookmarkFolders()
    {
        $user = Auth::user();

        $folders = ThreadBookmark::where('user_id', $user->id)
            ->whereNotNull('folder')
            ->select('folder', DB::raw('count(*) as bookmarks_count'))
            ->groupBy('folder')
            ->orderBy('folder')
            ->get();

        return view('dashboard.bookmark-folders', compact('folders'));
    }

    /**
     * Rename bookmark folder.
     */
    public function renameBookmarkFolder(Request $request)
    {
        $request->validate([
            'old_folder' => 'required|string',
            'new_folder' => 'required|string|max:50|different:old_folder',
        ]);

        $user = Auth::user();

        // Check if new folder name already exists
        $exists = ThreadBookmark::where('user_id', $user->id)
            ->where('folder', $request->new_folder)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Folder với tên này đã tồn tại'
            ], 422);
        }

        // Update all bookmarks with old folder name
        $updated = ThreadBookmark::where('user_id', $user->id)
            ->where('folder', $request->old_folder)
            ->update(['folder' => $request->new_folder]);

        return response()->json([
            'success' => true,
            'message' => "Đã đổi tên folder, cập nhật {$updated} bookmarks"
        ]);
    }

    /**
     * Delete bookmark folder (move bookmarks to uncategorized).
     */
    public function deleteBookmarkFolder(Request $request)
    {
        $request->validate([
            'folder' => 'required|string',
        ]);

        $user = Auth::user();

        // Move all bookmarks to uncategorized
        $updated = ThreadBookmark::where('user_id', $user->id)
            ->where('folder', $request->folder)
            ->update(['folder' => null]);

        return response()->json([
            'success' => true,
            'message' => "Đã xóa folder, di chuyển {$updated} bookmarks về uncategorized"
        ]);
    }

    /**
     * API: Lấy dữ liệu dashboard cho AJAX
     */
    public function dashboardData()
    {
        $user = Auth::user();

        // User statistics
        $stats = [
            'threads_created' => Thread::where('user_id', $user->id)->count(),
            'threads_bookmarked' => ThreadBookmark::where('user_id', $user->id)->count(),
            'ratings_given' => ThreadRating::where('user_id', $user->id)->count(),
            'average_rating_received' => Thread::where('user_id', $user->id)
                ->where('ratings_count', '>', 0)
                ->avg('average_rating'),
        ];

        // Recent activity
        $recentBookmarks = ThreadBookmark::with(['thread' => function ($q) {
            $q->with(['user', 'forum']);
        }])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentRatings = ThreadRating::with(['thread' => function ($q) {
            $q->with(['user', 'forum']);
        }])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_bookmarks' => $recentBookmarks,
            'recent_ratings' => $recentRatings,
        ]);
    }

    /**
     * API: Lấy danh sách bookmarks với pagination
     */
    public function getBookmarks(Request $request)
    {
        $user = Auth::user();

        $bookmarks = ThreadBookmark::with([
            'thread' => function ($q) {
                $q->with(['user', 'forum', 'tags']);
            },
            'folder'
        ])
            ->where('user_id', $user->id)
            ->when($request->folder_id, function ($q, $folderId) {
                return $q->where('folder_id', $folderId);
            })
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('thread', function ($threadQuery) use ($search) {
                    $threadQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->orderBy('bookmarked_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * API: Lấy danh sách ratings của user
     */
    public function getRatings(Request $request)
    {
        $user = Auth::user();

        $ratings = ThreadRating::with([
            'thread' => function ($q) {
                $q->with(['user', 'forum']);
            }
        ])
            ->where('user_id', $user->id)
            ->when($request->rating, function ($q, $rating) {
                return $q->where('rating', $rating);
            })
            ->orderBy('rated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'ratings' => $ratings,
        ]);
    }

    /**
     * API: Lấy danh sách threads của user với moderation status
     */
    public function getMyThreads(Request $request)
    {
        $user = Auth::user();

        $threads = Thread::with(['forum', 'tags'])
            ->where('user_id', $user->id)
            ->when($request->status, function ($q, $status) {
                return $q->where('moderation_status', $status);
            })
            ->when($request->type, function ($q, $type) {
                return $q->where('thread_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'threads' => $threads,
        ]);
    }

    /**
     * API: Lấy activity feed của user
     */
    public function getActivity(Request $request)
    {
        $user = Auth::user();
        $activities = collect();

        // Bookmarks
        $bookmarks = ThreadBookmark::with(['thread.forum'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($bookmark) {
                return [
                    'type' => 'bookmark',
                    'action' => 'bookmarked',
                    'thread' => $bookmark->thread,
                    'timestamp' => $bookmark->bookmarked_at,
                    'data' => [
                        'folder' => $bookmark->folder,
                        'notes' => $bookmark->notes,
                    ]
                ];
            });

        // Ratings
        $ratings = ThreadRating::with(['thread.forum'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($rating) {
                return [
                    'type' => 'rating',
                    'action' => 'rated',
                    'thread' => $rating->thread,
                    'timestamp' => $rating->rated_at,
                    'data' => [
                        'rating' => $rating->rating,
                        'review' => $rating->review,
                    ]
                ];
            });

        // Threads created
        $threads = Thread::with(['forum'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($thread) {
                return [
                    'type' => 'thread',
                    'action' => 'created',
                    'thread' => $thread,
                    'timestamp' => $thread->created_at,
                    'data' => [
                        'status' => $thread->moderation_status,
                        'type' => $thread->thread_type,
                    ]
                ];
            });

        // Merge và sort
        $activities = $bookmarks->concat($ratings)->concat($threads)
            ->sortByDesc('timestamp')
            ->take(50)
            ->values();

        return response()->json([
            'success' => true,
            'activities' => $activities,
        ]);
    }

    /**
     * User settings page
     */
    public function settings()
    {
        return view('user.settings');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'profession' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->only(['name', 'email', 'phone', 'location', 'bio', 'website', 'profession']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Thông tin cá nhân đã được cập nhật'
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Send password changed notification
        \App\Services\NotificationService::sendPasswordChangedNotification($user, $request->ip());

        return response()->json([
            'success' => true,
            'message' => 'Mật khẩu đã được thay đổi thành công'
        ]);
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,auto',
            'language' => 'required|in:vi,en',
            'threads_per_page' => 'required|integer|min:10|max:50',
            'timezone' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $preferences = $user->preferences ?? [];

        $preferences['theme'] = $request->theme;
        $preferences['language'] = $request->language;
        $preferences['threads_per_page'] = $request->threads_per_page;
        $preferences['timezone'] = $request->timezone;

        $user->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Tùy chọn đã được lưu'
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->notification_settings ?? [];

        // Email notifications
        $notifications['email_new_comment'] = $request->has('email_new_comment');
        $notifications['email_new_rating'] = $request->has('email_new_rating');
        $notifications['email_new_follower'] = $request->has('email_new_follower');
        $notifications['email_weekly_digest'] = $request->has('email_weekly_digest');

        // Push notifications
        $notifications['push_new_comment'] = $request->has('push_new_comment');
        $notifications['push_new_rating'] = $request->has('push_new_rating');
        $notifications['push_moderation_action'] = $request->has('push_moderation_action');
        $notifications['push_system_announcement'] = $request->has('push_system_announcement');

        $user->update(['notification_settings' => $notifications]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt thông báo đã được lưu'
        ]);
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $privacy = $user->privacy_settings ?? [];

        // Privacy settings
        $privacy['show_email'] = $request->has('show_email');
        $privacy['show_online_status'] = $request->has('show_online_status');
        $privacy['show_activity_feed'] = $request->has('show_activity_feed');
        $privacy['allow_direct_message'] = $request->has('allow_direct_message');
        $privacy['require_email_verification'] = $request->has('require_email_verification');

        // Logout other devices if requested
        if ($request->has('logout_other_devices')) {
            // This would require session management - implement based on your auth system
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete(); // For Sanctum
            }
        }

        $user->update(['privacy_settings' => $privacy]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt quyền riêng tư đã được lưu'
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Delete user's data
            Thread::where('user_id', $user->id)->delete();
            Comment::where('user_id', $user->id)->delete();
            ThreadRating::where('user_id', $user->id)->delete();
            ThreadBookmark::where('user_id', $user->id)->delete();

            // Delete avatar file
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            // Delete user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tài khoản đã được xóa thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa tài khoản'
            ], 500);
        }
    }

    /**
     * Create bookmark folder
     */
    public function createBookmarkFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if folder already exists for this user
        $exists = ThreadBookmark::where('user_id', Auth::id())
            ->where('folder', $request->folder_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Thư mục này đã tồn tại'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thư mục đã được tạo. Bạn có thể chọn thư mục này khi bookmark thread.'
        ]);
    }

    /**
     * Update bookmark
     */
    public function updateBookmark(Request $request, $id)
    {
        $request->validate([
            'folder' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        $bookmark = ThreadBookmark::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $bookmark->update([
            'folder' => $request->folder,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark đã được cập nhật'
        ]);
    }

    /**
     * Delete bookmark
     */
    public function deleteBookmark($id)
    {
        $bookmark = ThreadBookmark::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bookmark đã được xóa'
        ]);
    }

    /**
     * Bulk delete bookmarks
     */
    public function bulkDeleteBookmarks(Request $request)
    {
        $request->validate([
            'bookmark_ids' => 'required|array',
            'bookmark_ids.*' => 'integer|exists:thread_bookmarks,id'
        ]);

        $count = ThreadBookmark::where('user_id', Auth::id())
            ->whereIn('id', $request->bookmark_ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$count} bookmark"
        ]);
    }
}
