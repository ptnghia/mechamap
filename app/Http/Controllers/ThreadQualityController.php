<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadRating;
use App\Models\ThreadBookmark;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThreadQualityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Rate một thread (1-5 stars).
     */
    public function rateThread(Request $request, Thread $thread): JsonResponse
    {
        $validated = $request->validate(ThreadRating::rules());

        $user = Auth::user();

        try {
            // Check if user already rated this thread
            $existingRating = $thread->getRatingByUser($user);

            if ($existingRating) {
                // Update existing rating
                $existingRating->update([
                    'rating' => $validated['rating'],
                    'review' => $validated['review'] ?? null,
                ]);

                $message = 'Đánh giá của bạn đã được cập nhật';
            } else {
                // Create new rating
                $thread->ratings()->create([
                    'user_id' => $user->id,
                    'rating' => $validated['rating'],
                    'review' => $validated['review'] ?? null,
                ]);

                $message = 'Thread đã được đánh giá thành công';
            }

            // ThreadRating model will automatically recalculate ratings via booted() method
            $thread->refresh();

            // Update quality score based on new rating
            $this->updateQualityScore($thread);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'rating' => $validated['rating'],
                    'average_rating' => $thread->average_rating,
                    'ratings_count' => $thread->ratings_count,
                    'quality_score' => $thread->quality_score,
                    'rating_distribution' => $thread->getRatingDistribution(),
                    'positive_percentage' => $thread->getPositiveRatingPercentage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đánh giá thread',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bump một thread lên top.
     */
    public function bumpThread(Thread $thread): JsonResponse
    {
        // Chỉ author hoặc moderator mới có thể bump
        if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole('moderator')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền bump thread này',
            ], 403);
        }

        // Giới hạn bump (ví dụ: chỉ bump được 1 lần trong 24h)
        if ($thread->last_bump_at && $thread->last_bump_at->diffInHours(now()) < 24) {
            return response()->json([
                'success' => false,
                'message' => 'Thread chỉ có thể bump sau 24 giờ kể từ lần bump cuối',
            ], 429);
        }

        $thread->bump();

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được bump thành công',
            'data' => [
                'last_bump_at' => $thread->last_bump_at,
                'bump_count' => $thread->bump_count,
            ]
        ]);
    }

    /**
     * Bookmark một thread.
     */
    public function bookmarkThread(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'folder' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        try {
            // Check if already bookmarked
            $existingBookmark = $thread->bookmarks()->where('user_id', $user->id)->first();

            if ($existingBookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread này đã được bookmark trước đó',
                    'data' => [
                        'bookmark' => $existingBookmark,
                    ]
                ], 409);
            }

            // Create new bookmark
            $bookmark = $thread->bookmarks()->create([
                'user_id' => $user->id,
                'folder' => $request->folder,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thread đã được bookmark thành công',
                'data' => [
                    'bookmark' => $bookmark,
                    'bookmark_count' => $thread->fresh()->bookmark_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi bookmark thread',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove bookmark từ thread.
     */
    public function removeBookmark(Thread $thread): JsonResponse
    {
        $user = Auth::user();

        try {
            $bookmark = $thread->bookmarks()->where('user_id', $user->id)->first();

            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread này chưa được bookmark',
                ], 404);
            }

            $bookmark->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bookmark đã được xóa',
                'data' => [
                    'bookmark_count' => $thread->fresh()->bookmark_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa bookmark',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's bookmarks.
     */
    public function getUserBookmarks(Request $request): JsonResponse
    {
        $request->validate([
            'folder' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $perPage = $request->get('per_page', 15);

        try {
            $query = ThreadBookmark::with(['thread.user', 'thread.forum'])
                ->where('user_id', $user->id);

            // Filter by folder if specified
            if ($request->has('folder')) {
                $query->inFolder($request->folder);
            }

            $bookmarks = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Get available folders
            $folders = ThreadBookmark::getUserFolders($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'bookmarks' => $bookmarks,
                    'folders' => $folders,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách bookmark',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Share một thread.
     */
    public function shareThread(Thread $thread): JsonResponse
    {
        $thread->increment('share_count');

        return response()->json([
            'success' => true,
            'message' => 'Thread đã được share',
            'data' => [
                'share_count' => $thread->share_count,
            ]
        ]);
    }

    /**
     * Dislike một comment.
     */
    public function dislikeComment(Comment $comment): JsonResponse
    {
        $user = Auth::user();

        // Kiểm tra đã dislike chưa
        $existingDislike = $comment->dislikes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingDislike) {
            // Undislike
            $existingDislike->delete();
            $message = 'Đã bỏ dislike comment';
        } else {
            // Dislike
            $comment->dislikes()->create(['user_id' => $user->id]);
            $message = 'Đã dislike comment';

            // Nếu đã like trước đó thì remove like
            $existingLike = $comment->likes()
                ->where('user_id', $user->id)
                ->first();
            if ($existingLike) {
                $existingLike->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'dislikes_count' => $comment->fresh()->dislikes_count,
                'like_count' => $comment->fresh()->like_count,
                'net_score' => $comment->fresh()->getNetScore(),
            ]
        ]);
    }

    /**
     * Cập nhật thread type.
     */
    public function updateThreadType(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:discussion,question,announcement,tutorial,poll,project,showcase',
        ]);

        // Chỉ author hoặc moderator mới có thể update type
        if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole('moderator')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thay đổi type của thread này',
            ], 403);
        }

        $thread->update(['thread_type' => $request->type]);

        return response()->json([
            'success' => true,
            'message' => 'Thread type đã được cập nhật',
            'data' => [
                'thread_type' => $thread->thread_type,
            ]
        ]);
    }

    /**
     * Cập nhật meta description cho SEO.
     */
    public function updateThreadSEO(Request $request, Thread $thread): JsonResponse
    {
        $request->validate([
            'meta_description' => 'nullable|string|max:160',
            'search_keywords' => 'nullable|array',
            'search_keywords.*' => 'string|max:50',
            'read_time' => 'nullable|integer|min:1|max:60', // minutes
        ]);

        // Chỉ author hoặc moderator mới có thể update SEO
        if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole('moderator')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật SEO của thread này',
            ], 403);
        }

        $thread->update([
            'meta_description' => $request->meta_description,
            'search_keywords' => $request->search_keywords,
            'read_time' => $request->read_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SEO settings đã được cập nhật',
            'data' => [
                'meta_description' => $thread->meta_description,
                'search_keywords' => $thread->search_keywords,
                'read_time' => $thread->read_time,
            ]
        ]);
    }

    /**
     * Lấy threads có quality cao.
     */
    public function getHighQualityThreads(Request $request): JsonResponse
    {
        $minScore = $request->get('min_score', 4.0);

        $threads = Thread::with(['user', 'forum', 'category'])
            ->highQuality($minScore)
            ->notSpam()
            ->notFlagged()
            ->visible()
            ->orderBy('quality_score', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $threads,
        ]);
    }

    /**
     * Lấy trending threads dựa trên activity gần đây.
     */
    public function getTrendingThreads(Request $request): JsonResponse
    {
        $threads = Thread::with(['user', 'forum', 'category'])
            ->where('last_activity_at', '>=', now()->subDays(7)) // Activity trong 7 ngày qua
            ->notSpam()
            ->notFlagged()
            ->visible()
            ->orderByRaw('(view_count + cached_comments_count * 5 + bump_count * 3) DESC') // Trending algorithm
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $threads,
        ]);
    }

    /**
     * Update quality score dựa trên nhiều factors.
     */
    private function updateQualityScore(Thread $thread): void
    {
        $factors = [
            'rating' => $thread->average_rating ?? 0, // 0-5
            'views' => min($thread->view_count / 1000, 5), // Capped at 5
            'comments' => min($thread->cached_comments_count * 0.5, 5), // Capped at 5
            'likes_ratio' => $this->calculateLikesRatio($thread), // 0-5
            'solved_bonus' => $thread->is_solved ? 1 : 0, // Bonus cho solved threads
            'featured_bonus' => $thread->is_featured ? 0.5 : 0, // Bonus cho featured threads
        ];

        // Weighted average
        $weights = [
            'rating' => 0.4,
            'views' => 0.15,
            'comments' => 0.15,
            'likes_ratio' => 0.2,
            'solved_bonus' => 0.1,
            'featured_bonus' => 0.05,
        ];

        $qualityScore = 0;
        foreach ($factors as $factor => $value) {
            $qualityScore += $value * $weights[$factor];
        }

        $thread->update(['quality_score' => round($qualityScore, 2)]);
    }

    /**
     * Calculate likes ratio for quality scoring.
     */
    private function calculateLikesRatio(Thread $thread): float
    {
        $totalLikes = $thread->likes()->count();
        $totalDislikes = $thread->dislikes_count ?? 0;
        $totalVotes = $totalLikes + $totalDislikes;

        if ($totalVotes === 0) {
            return 2.5; // Neutral score
        }

        $ratio = $totalLikes / $totalVotes;
        return $ratio * 5; // Scale to 0-5
    }
}
