<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadRating;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý rating threads
 * Quản lý việc đánh giá threads với rating và review
 */
class ThreadRatingController extends Controller
{
    /**
     * Tạo rating mới cho thread
     */
    public function store(Request $request, Thread $thread): JsonResponse
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            $user = Auth::user();

            // Kiểm tra đã rating chưa
            $existingRating = ThreadRating::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if ($existingRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đánh giá thread này rồi',
                    'can_rate' => false
                ], 409);
            }

            // Không thể rate thread của chính mình
            if ($thread->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đánh giá thread của chính mình',
                    'can_rate' => false
                ], 403);
            }

            // Tạo rating mới
            $rating = ThreadRating::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'rating' => $request->rating,
                'review' => $request->review,
                'is_anonymous' => $request->boolean('is_anonymous', false),
                'rated_at' => now(),
            ]);

            // Cập nhật thread rating statistics
            $this->updateThreadRatingStats($thread);

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá thành công',
                'rating' => [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'review' => $rating->review,
                    'is_anonymous' => $rating->is_anonymous,
                    'rated_at' => $rating->rated_at->format('d/m/Y H:i'),
                ],
                'thread_stats' => $this->getThreadRatingStats($thread)
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi rating thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đánh giá'
            ], 500);
        }
    }

    /**
     * Cập nhật rating hiện có
     */
    public function update(Request $request, Thread $thread): JsonResponse
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            $user = Auth::user();

            $rating = ThreadRating::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa đánh giá thread này',
                    'can_rate' => true
                ], 404);
            }

            // Cập nhật rating
            $rating->update([
                'rating' => $request->rating,
                'review' => $request->review,
                'is_anonymous' => $request->boolean('is_anonymous', $rating->is_anonymous),
                'updated_at' => now(),
            ]);

            // Cập nhật thread rating statistics
            $this->updateThreadRatingStats($thread);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đánh giá thành công',
                'rating' => [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'review' => $rating->review,
                    'is_anonymous' => $rating->is_anonymous,
                    'rated_at' => $rating->rated_at->format('d/m/Y H:i'),
                    'updated_at' => $rating->updated_at->format('d/m/Y H:i'),
                ],
                'thread_stats' => $this->getThreadRatingStats($thread)
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật rating thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật đánh giá'
            ], 500);
        }
    }

    /**
     * Xóa rating
     */
    public function destroy(Thread $thread): JsonResponse
    {
        try {
            $user = Auth::user();

            $rating = ThreadRating::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa đánh giá thread này',
                    'can_rate' => true
                ], 404);
            }

            // Xóa rating
            $rating->delete();

            // Cập nhật thread rating statistics
            $this->updateThreadRatingStats($thread);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa đánh giá thành công',
                'can_rate' => true,
                'thread_stats' => $this->getThreadRatingStats($thread)
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa rating thread: ' . $e->getMessage(), [
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đánh giá'
            ], 500);
        }
    }

    /**
     * Lấy thông tin rating của user cho thread
     */
    public function getUserRating(Thread $thread): JsonResponse
    {
        try {
            $user = Auth::user();

            $rating = ThreadRating::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            $canRate = $rating === null && $thread->user_id !== $user->id;

            return response()->json([
                'success' => true,
                'can_rate' => $canRate,
                'user_rating' => $rating ? [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'review' => $rating->review,
                    'is_anonymous' => $rating->is_anonymous,
                    'rated_at' => $rating->rated_at->format('d/m/Y H:i'),
                ] : null,
                'thread_stats' => $this->getThreadRatingStats($thread)
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy rating thread: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin đánh giá'
            ], 500);
        }
    }

    /**
     * Lấy danh sách reviews của thread
     */
    public function getThreadReviews(Thread $thread): JsonResponse
    {
        try {
            $reviews = ThreadRating::where('thread_id', $thread->id)
                ->whereNotNull('review')
                ->where('review', '!=', '')
                ->with(['user:id,username,display_name,avatar'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $reviews->getCollection()->transform(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'review' => $rating->review,
                    'is_anonymous' => $rating->is_anonymous,
                    'user' => $rating->is_anonymous ? null : [
                        'id' => $rating->user->id,
                        'username' => $rating->user->username,
                        'display_name' => $rating->user->display_name,
                        'avatar' => $rating->user->avatar,
                    ],
                    'rated_at' => $rating->rated_at->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
                'thread_stats' => $this->getThreadRatingStats($thread)
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy reviews thread: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải reviews'
            ], 500);
        }
    }

    /**
     * Cập nhật thống kê rating của thread
     */
    private function updateThreadRatingStats(Thread $thread): void
    {
        $ratings = ThreadRating::where('thread_id', $thread->id)->get();

        if ($ratings->count() > 0) {
            $averageRating = $ratings->avg('rating');
            $totalRatings = $ratings->count();

            // Tính phân phối rating (1-5 sao)
            $distribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $distribution[$i] = $ratings->where('rating', $i)->count();
            }

            $thread->update([
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
                'rating_distribution' => json_encode($distribution),
            ]);
        } else {
            $thread->update([
                'average_rating' => null,
                'total_ratings' => 0,
                'rating_distribution' => null,
            ]);
        }
    }

    /**
     * Lấy thống kê rating của thread
     */
    private function getThreadRatingStats(Thread $thread): array
    {
        return [
            'average_rating' => $thread->average_rating,
            'total_ratings' => $thread->total_ratings,
            'rating_distribution' => $thread->rating_distribution ?
                json_decode($thread->rating_distribution, true) : null,
        ];
    }
}
