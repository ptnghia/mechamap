<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\ThreadRating;
use App\Models\ThreadBookmark;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ThreadQualityController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->middleware('auth:sanctum');
        $this->moderationService = $moderationService;
    }

    /**
     * Đánh giá thread (1-5 sao)
     */
    public function rateThread(Request $request, Thread $thread)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Kiểm tra quyền đánh giá
        if (!$this->moderationService->canViewThread($thread, Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền đánh giá thread này'
            ], 403);
        }

        // Không thể đánh giá thread của chính mình
        if ($thread->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể đánh giá thread của chính mình'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Tạo hoặc cập nhật rating
            $rating = ThreadRating::updateOrCreate(
                [
                    'thread_id' => $thread->id,
                    'user_id' => Auth::id()
                ],
                [
                    'rating' => $request->rating,
                    'comment' => $request->comment
                ]
            );

            // Thread model sẽ tự động cập nhật average_rating và ratings_count
            // thông qua event trong ThreadRating model

            DB::commit();

            // Reload thread để lấy thông tin mới nhất
            $thread->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được lưu',
                'data' => [
                    'rating' => $rating,
                    'thread_stats' => [
                        'average_rating' => round($thread->average_rating, 1),
                        'ratings_count' => $thread->ratings_count
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu đánh giá'
            ], 500);
        }
    }

    /**
     * Xóa đánh giá thread
     */
    public function removeRating(Thread $thread)
    {
        $rating = ThreadRating::where([
            'thread_id' => $thread->id,
            'user_id' => Auth::id()
        ])->first();

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đánh giá thread này'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $rating->delete();
            // Thread stats sẽ được cập nhật tự động qua event

            DB::commit();

            $thread->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa đánh giá',
                'data' => [
                    'thread_stats' => [
                        'average_rating' => round($thread->average_rating ?? 0, 1),
                        'ratings_count' => $thread->ratings_count
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đánh giá'
            ], 500);
        }
    }

    /**
     * Lấy đánh giá của user cho thread
     */
    public function getUserRating(Thread $thread)
    {
        if (!$this->moderationService->canViewThread($thread, Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem thread này'
            ], 403);
        }

        $rating = ThreadRating::where([
            'thread_id' => $thread->id,
            'user_id' => Auth::id()
        ])->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user_rating' => $rating,
                'thread_stats' => [
                    'average_rating' => round($thread->average_rating ?? 0, 1),
                    'ratings_count' => $thread->ratings_count
                ]
            ]
        ]);
    }

    /**
     * Bookmark thread
     */
    public function bookmarkThread(Request $request, Thread $thread)
    {
        $request->validate([
            'folder' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        // Kiểm tra quyền bookmark
        if (!$this->moderationService->canViewThread($thread, Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền bookmark thread này'
            ], 403);
        }

        // Kiểm tra đã bookmark chưa
        $existingBookmark = ThreadBookmark::where([
            'thread_id' => $thread->id,
            'user_id' => Auth::id()
        ])->first();

        if ($existingBookmark) {
            return response()->json([
                'success' => false,
                'message' => 'Thread đã được bookmark trước đó'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $bookmark = ThreadBookmark::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'folder' => $request->folder,
                'notes' => $request->notes
            ]);

            // Thread bookmark_count sẽ được cập nhật tự động qua event

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thread đã được bookmark',
                'data' => [
                    'bookmark' => $bookmark,
                    'bookmark_count' => $thread->fresh()->bookmark_count
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi bookmark thread'
            ], 500);
        }
    }

    /**
     * Xóa bookmark thread
     */
    public function removeBookmark(Thread $thread)
    {
        $bookmark = ThreadBookmark::where([
            'thread_id' => $thread->id,
            'user_id' => Auth::id()
        ])->first();

        if (!$bookmark) {
            return response()->json([
                'success' => false,
                'message' => 'Thread chưa được bookmark'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $bookmark->delete();
            // Thread bookmark_count sẽ được cập nhật tự động qua event

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bookmark',
                'data' => [
                    'bookmark_count' => $thread->fresh()->bookmark_count
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa bookmark'
            ], 500);
        }
    }

    /**
     * Cập nhật bookmark (folder, notes)
     */
    public function updateBookmark(Request $request, Thread $thread)
    {
        $request->validate([
            'folder' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        $bookmark = ThreadBookmark::where([
            'thread_id' => $thread->id,
            'user_id' => Auth::id()
        ])->first();

        if (!$bookmark) {
            return response()->json([
                'success' => false,
                'message' => 'Thread chưa được bookmark'
            ], 404);
        }

        $bookmark->update([
            'folder' => $request->folder,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark đã được cập nhật',
            'data' => [
                'bookmark' => $bookmark->fresh()
            ]
        ]);
    }

    /**
     * Lấy trạng thái bookmark của user cho thread
     */
    public function getBookmarkStatus(Thread $thread)
    {
        if (!$this->moderationService->canViewThread($thread, Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem thread này'
            ], 403);
        }

        $bookmark = ThreadBookmark::where([
            'thread_id' => $thread->id,
            'user_id' => Auth::id()
        ])->first();

        return response()->json([
            'success' => true,
            'data' => [
                'is_bookmarked' => !!$bookmark,
                'bookmark' => $bookmark,
                'bookmark_count' => $thread->bookmark_count
            ]
        ]);
    }

    /**
     * Lấy danh sách thread ratings (public)
     */
    public function getThreadRatings(Thread $thread, Request $request)
    {
        if (!$this->moderationService->canViewThread($thread, Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem thread này'
            ], 403);
        }

        $ratings = ThreadRating::with(['user:id,name,avatar'])
            ->where('thread_id', $thread->id)
            ->when($request->with_comments, function ($query) {
                return $query->whereNotNull('comment');
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'ratings' => $ratings,
                'thread_stats' => [
                    'average_rating' => round($thread->average_rating ?? 0, 1),
                    'ratings_count' => $thread->ratings_count,
                    'rating_distribution' => $this->getRatingDistribution($thread)
                ]
            ]
        ]);
    }

    /**
     * Lấy danh sách folder bookmark của user
     */
    public function getBookmarkFolders()
    {
        $folders = ThreadBookmark::where('user_id', Auth::id())
            ->whereNotNull('folder')
            ->select('folder', DB::raw('COUNT(*) as count'))
            ->groupBy('folder')
            ->orderBy('folder')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'folders' => $folders
            ]
        ]);
    }

    /**
     * Lấy danh sách bookmarks của user
     */
    public function getUserBookmarks(Request $request)
    {
        $request->validate([
            'folder' => 'nullable|string',
            'search' => 'nullable|string|max:100'
        ]);

        $query = ThreadBookmark::with(['thread.user', 'thread.forum'])
            ->where('user_id', Auth::id());

        if ($request->folder) {
            $query->where('folder', $request->folder);
        }

        if ($request->search) {
            $query->whereHas('thread', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $bookmarks = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'bookmarks' => $bookmarks
            ]
        ]);
    }

    /**
     * Helper: Lấy phân bố rating cho thread
     */
    private function getRatingDistribution(Thread $thread)
    {
        $distribution = ThreadRating::where('thread_id', $thread->id)
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->rating . '_star' => (int) $item->count];
            })
            ->toArray();

        // Đảm bảo có đầy đủ 1-5 sao (default = 0)
        $fullDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $fullDistribution[$i . '_star'] = $distribution[$i . '_star'] ?? 0;
        }

        return $fullDistribution;
    }

    /**
     * Lấy thống kê rating chi tiết cho thread
     */
    public function getThreadRatingStats(Thread $thread)
    {
        if (!$this->moderationService->canViewThread($thread, Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem thread này'
            ], 403);
        }

        $distribution = $this->getRatingDistribution($thread);
        $totalRatings = $thread->ratings_count;

        // Tính phần trăm cho mỗi rating
        $percentageDistribution = [];
        foreach ($distribution as $rating => $count) {
            $percentageDistribution[$rating] = $totalRatings > 0
                ? round(($count / $totalRatings) * 100, 1)
                : 0;
        }

        // Tính quality score (dựa trên rating distribution)
        $qualityScore = $this->calculateQualityScore($distribution, $totalRatings);

        return response()->json([
            'success' => true,
            'data' => [
                'thread_id' => $thread->id,
                'thread_title' => $thread->title,
                'stats' => [
                    'average_rating' => round($thread->average_rating ?? 0, 2),
                    'ratings_count' => $totalRatings,
                    'distribution' => $distribution,
                    'percentage_distribution' => $percentageDistribution,
                    'quality_score' => $qualityScore,
                    'positive_rating_percentage' => $thread->getPositiveRatingPercentage(),
                    'rating_trend' => $this->getRatingTrend($thread)
                ]
            ]
        ]);
    }

    /**
     * Tính quality score dựa trên rating distribution
     */
    private function calculateQualityScore(array $distribution, int $totalRatings): float
    {
        if ($totalRatings === 0) {
            return 0;
        }

        $weightedSum = 0;
        $weights = [
            '1_star' => 1,
            '2_star' => 2,
            '3_star' => 3,
            '4_star' => 4,
            '5_star' => 5
        ];

        foreach ($distribution as $rating => $count) {
            $weightedSum += $weights[$rating] * $count;
        }

        // Normalized score (0-100)
        $rawScore = ($weightedSum / ($totalRatings * 5)) * 100;

        // Adjust for sample size (penalize small samples)
        $sampleSizeAdjustment = min(1, $totalRatings / 10); // Full weight at 10+ ratings

        return round($rawScore * $sampleSizeAdjustment, 1);
    }

    /**
     * Lấy trend rating trong 30 ngày gần nhất
     */
    private function getRatingTrend(Thread $thread): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        $recentRatings = ThreadRating::where('thread_id', $thread->id)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->orderBy('created_at')
            ->get(['rating', 'created_at']);

        if ($recentRatings->isEmpty()) {
            return [
                'trend' => 'stable',
                'direction' => 0,
                'recent_average' => 0,
                'change_percentage' => 0
            ];
        }

        $recentAverage = $recentRatings->avg('rating');
        $overallAverage = $thread->average_rating ?? 0;

        $changePercentage = $overallAverage > 0
            ? round((($recentAverage - $overallAverage) / $overallAverage) * 100, 1)
            : 0;

        $trend = 'stable';
        if (abs($changePercentage) >= 5) {
            $trend = $changePercentage > 0 ? 'improving' : 'declining';
        }

        return [
            'trend' => $trend,
            'direction' => $changePercentage > 0 ? 1 : ($changePercentage < 0 ? -1 : 0),
            'recent_average' => round($recentAverage, 2),
            'change_percentage' => $changePercentage
        ];
    }
}
