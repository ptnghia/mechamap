<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadRating;
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
     * Dashboard tổng quan của user.
     */
    public function index()
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

        $myThreads = Thread::with(['forum'])
            ->withCount(['comments', 'bookmarks', 'ratings'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'recentBookmarks',
            'recentRatings',
            'myThreads'
        ));
    }

    /**
     * Quản lý bookmarks của user.
     */
    public function bookmarks(Request $request)
    {
        $user = Auth::user();

        $query = ThreadBookmark::with(['thread' => function ($q) {
            $q->with(['user', 'forum'])
                ->withCount(['comments', 'ratings']);
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
            'total' => ThreadBookmark::where('user_id', $user->id)->count(),
            'with_folders' => ThreadBookmark::where('user_id', $user->id)->whereNotNull('folder')->count(),
            'with_notes' => ThreadBookmark::where('user_id', $user->id)->whereNotNull('notes')->count(),
        ];

        return view('user.bookmarks', compact('bookmarks', 'folders', 'stats'));
    }

    /**
     * Quản lý ratings của user.
     */
    public function ratings(Request $request)
    {
        $user = Auth::user();

        $query = ThreadRating::with(['thread' => function ($q) {
            $q->with(['user', 'forum'])
                ->withCount(['comments', 'ratings']);
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

        // Rating stats
        $ratingStats = [
            'total' => ThreadRating::where('user_id', $user->id)->count(),
            'average' => ThreadRating::where('user_id', $user->id)->avg('rating'),
            'with_review' => ThreadRating::where('user_id', $user->id)->whereNotNull('review')->count(),
        ];

        return view('dashboard.ratings', compact('ratings', 'ratingDistribution', 'ratingStats'));
    }

    /**
     * Threads của user.
     */
    public function myThreads(Request $request)
    {
        $user = Auth::user();

        $query = Thread::with(['forum', 'tags'])
            ->withCount(['comments', 'bookmarks', 'ratings'])
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
            'flagged' => Thread::where('user_id', $user->id)->where('is_flagged', true)->count(),
            'solved' => Thread::where('user_id', $user->id)->where('is_solved', true)->count(),
            'average_rating' => Thread::where('user_id', $user->id)
                ->where('ratings_count', '>', 0)
                ->avg('average_rating'),
            'total_views' => Thread::where('user_id', $user->id)->sum('view_count'),
            'total_bookmarks' => Thread::where('user_id', $user->id)->sum('bookmark_count'),
        ];

        return view('dashboard.my-threads', compact('threads', 'threadStats'));
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

        return view('dashboard.activity', compact('activities'));
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
            $user->tokens()->delete(); // For Sanctum
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
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Delete user's data
            $user->threads()->delete();
            $user->comments()->delete();
            $user->threadRatings()->delete();
            $user->threadBookmarks()->delete();

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
