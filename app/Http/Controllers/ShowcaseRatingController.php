<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\ShowcaseRating;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowcaseRatingController extends Controller
{
    /**
     * Store or update a rating for a showcase.
     */
    public function store(Request $request, Showcase $showcase): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đánh giá.'
            ], 401);
        }

        // Validate the request
        $request->validate([
            'technical_quality' => 'required|integer|min:1|max:5',
            'innovation' => 'required|integer|min:1|max:5',
            'usefulness' => 'required|integer|min:1|max:5',
            'documentation' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        // Prevent self-rating
        if ($showcase->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể đánh giá showcase của chính mình.'
            ], 403);
        }

        try {
            DB::transaction(function () use ($request, $showcase) {
                // Update or create rating
                ShowcaseRating::updateOrCreate(
                    [
                        'showcase_id' => $showcase->id,
                        'user_id' => Auth::id(),
                    ],
                    [
                        'technical_quality' => $request->technical_quality,
                        'innovation' => $request->innovation,
                        'usefulness' => $request->usefulness,
                        'documentation' => $request->documentation,
                        'review' => $request->review,
                    ]
                );

                // Update showcase rating cache
                $this->updateShowcaseRatingCache($showcase);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được lưu thành công.',
                'average_rating' => $showcase->fresh()->average_rating,
                'ratings_count' => $showcase->fresh()->ratings_count,
                'category_averages' => $showcase->fresh()->getCategoryAverages(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Get ratings for a showcase.
     */
    public function index(Showcase $showcase): JsonResponse
    {
        $ratings = $showcase->ratings()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'ratings' => $ratings,
            'average_rating' => $showcase->average_rating,
            'ratings_count' => $showcase->ratings_count,
            'category_averages' => $showcase->getCategoryAverages(),
        ]);
    }

    /**
     * Delete a rating.
     */
    public function destroy(Showcase $showcase): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập.'
            ], 401);
        }

        $rating = $showcase->ratings()->where('user_id', Auth::id())->first();

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đánh giá của bạn.'
            ], 404);
        }

        try {
            DB::transaction(function () use ($rating, $showcase) {
                $rating->delete();
                $this->updateShowcaseRatingCache($showcase);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa đánh giá của bạn.',
                'average_rating' => $showcase->fresh()->average_rating,
                'ratings_count' => $showcase->fresh()->ratings_count,
                'category_averages' => $showcase->fresh()->getCategoryAverages(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đánh giá.'
            ], 500);
        }
    }

    /**
     * Delete a specific rating (for admin/owner).
     */
    public function deleteRating(ShowcaseRating $rating): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $showcase = $rating->showcase;

        // Chỉ cho phép xóa nếu là chủ rating hoặc chủ showcase
        if ($rating->user_id !== $user->id && $showcase->user_id !== $user->id) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($rating, $showcase) {
                $rating->delete();
                $this->updateShowcaseRatingCache($showcase);
            });

            return back()->with('success', 'Đánh giá đã được xóa.');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa đánh giá.');
        }
    }

    /**
     * Update showcase rating cache.
     */
    private function updateShowcaseRatingCache(Showcase $showcase): void
    {
        $avgRating = $showcase->ratings()->avg('overall_rating') ?? 0;
        $ratingsCount = $showcase->ratings()->count();

        $showcase->update([
            'rating_average' => round($avgRating, 2),
            'rating_count' => $ratingsCount,
        ]);
    }
}
