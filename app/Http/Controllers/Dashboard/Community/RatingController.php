<?php

namespace App\Http\Controllers\Dashboard\Community;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\ThreadRating;
use App\Models\ShowcaseRating;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Rating Controller cho Dashboard Community
 * 
 * Quản lý ratings của user trong dashboard
 */
class RatingController extends BaseController
{
    /**
     * Hiển thị danh sách ratings của user
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, threads, showcases
        $rating = $request->get('rating');
        $hasReview = $request->get('has_review');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $threadRatings = collect();
        $showcaseRatings = collect();

        // Get thread ratings
        if ($type === 'all' || $type === 'threads') {
            $threadQuery = ThreadRating::with(['thread' => function ($q) {
                $q->with(['user', 'forum'])
                    ->withCount(['allComments as comments_count', 'ratings']);
            }])
                ->where('user_id', $this->user->id);

            $this->applyRatingFilters($threadQuery, $rating, $hasReview, $search, $sort);
            $threadRatings = $threadQuery->get()->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'type' => 'thread',
                    'title' => $rating->thread->title,
                    'url' => route('threads.show', $rating->thread),
                    'rating' => $rating->rating,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at,
                    'updated_at' => $rating->updated_at,
                    'target' => $rating->thread];
            });
        }

        // Get showcase ratings
        if ($type === 'all' || $type === 'showcases') {
            $showcaseQuery = ShowcaseRating::with(['showcase' => function ($q) {
                $q->with(['user', 'category']);
            }])
                ->where('user_id', $this->user->id);

            $this->applyRatingFilters($showcaseQuery, $rating, $hasReview, $search, $sort);
            $showcaseRatings = $showcaseQuery->get()->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'type' => 'showcase',
                    'title' => $rating->showcase->title,
                    'url' => route('showcase.show', $rating->showcase),
                    'rating' => $rating->rating,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at,
                    'updated_at' => $rating->updated_at,
                    'target' => $rating->showcase];
            });
        }

        // Combine and sort ratings
        $allRatings = $threadRatings->merge($showcaseRatings);

        if ($sort === 'oldest') {
            $allRatings = $allRatings->sortBy('created_at');
        } else {
            $allRatings = $allRatings->sortByDesc('created_at');
        }

        // Paginate manually
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $ratings = $allRatings->forPage($currentPage, $perPage);

        // Get statistics
        $stats = $this->getRatingStats();

        return $this->dashboardResponse('dashboard.community.ratings.index', [
            'ratings' => $ratings,
            'stats' => $stats,
            'currentType' => $type,
            'currentRating' => $rating,
            'currentHasReview' => $hasReview,
            'search' => $search,
            'currentSort' => $sort,
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $allRatings->count(),
                'last_page' => ceil($allRatings->count() / $perPage)]]);
    }

    /**
     * Hiển thị form chỉnh sửa rating
     */
    public function edit(Request $request, $type, $id)
    {
        if ($type === 'thread') {
            $rating = ThreadRating::where('id', $id)
                ->where('user_id', $this->user->id)
                ->with('thread')
                ->firstOrFail();
        } elseif ($type === 'showcase') {
            $rating = ShowcaseRating::where('id', $id)
                ->where('user_id', $this->user->id)
                ->with('showcase')
                ->firstOrFail();
        } else {
            abort(404);
        }

        return $this->dashboardResponse('dashboard.community.ratings.edit', [
            'rating' => $rating,
            'type' => $type]);
    }

    /**
     * Cập nhật rating
     */
    public function update(Request $request, $type, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000']);

        if ($type === 'thread') {
            $rating = ThreadRating::where('id', $id)
                ->where('user_id', $this->user->id)
                ->firstOrFail();
        } elseif ($type === 'showcase') {
            $rating = ShowcaseRating::where('id', $id)
                ->where('user_id', $this->user->id)
                ->firstOrFail();
        } else {
            abort(404);
        }

        $rating->update([
            'rating' => $request->rating,
            'review' => $request->review]);

        return redirect()->route('dashboard.community.ratings')
            ->with('success', 'Rating updated successfully.');
    }

    /**
     * Xóa rating
     */
    public function destroy($type, $id): JsonResponse
    {
        if ($type === 'thread') {
            $rating = ThreadRating::where('id', $id)
                ->where('user_id', $this->user->id)
                ->firstOrFail();
        } elseif ($type === 'showcase') {
            $rating = ShowcaseRating::where('id', $id)
                ->where('user_id', $this->user->id)
                ->firstOrFail();
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        }

        $rating->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rating deleted successfully.'
        ]);
    }

    /**
     * Apply filters to rating query
     */
    private function applyRatingFilters($query, $rating, $hasReview, $search, $sort)
    {
        if ($rating) {
            $query->where('rating', $rating);
        }

        if ($hasReview === 'yes') {
            $query->whereNotNull('review')->where('review', '!=', '');
        } elseif ($hasReview === 'no') {
            $query->where(function ($q) {
                $q->whereNull('review')->orWhere('review', '');
            });
        }

        if ($search) {
            $query->where('review', 'like', "%{$search}%");
        }

        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }
    }

    /**
     * Lấy thống kê ratings
     */
    private function getRatingStats()
    {
        $threadRatingsCount = ThreadRating::where('user_id', $this->user->id)->count();
        $showcaseRatingsCount = ShowcaseRating::where('user_id', $this->user->id)->count();
        $totalRatings = $threadRatingsCount + $showcaseRatingsCount;

        $threadRatingsWithReview = ThreadRating::where('user_id', $this->user->id)
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->count();

        $showcaseRatingsWithReview = ShowcaseRating::where('user_id', $this->user->id)
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->count();

        $ratingsWithReview = $threadRatingsWithReview + $showcaseRatingsWithReview;

        // Average ratings given
        $threadAverage = ThreadRating::where('user_id', $this->user->id)->avg('rating');
        $showcaseAverage = ShowcaseRating::where('user_id', $this->user->id)->avg('rating');
        $overallAverage = $totalRatings > 0 ? 
            (($threadAverage * $threadRatingsCount) + ($showcaseAverage * $showcaseRatingsCount)) / $totalRatings : 0;

        // Rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $threadCount = ThreadRating::where('user_id', $this->user->id)
                ->where('rating', $i)->count();
            $showcaseCount = ShowcaseRating::where('user_id', $this->user->id)
                ->where('rating', $i)->count();
            
            $ratingDistribution[$i] = $threadCount + $showcaseCount;
        }

        return [
            'total_ratings' => $totalRatings,
            'thread_ratings' => $threadRatingsCount,
            'showcase_ratings' => $showcaseRatingsCount,
            'ratings_with_review' => $ratingsWithReview,
            'ratings_without_review' => $totalRatings - $ratingsWithReview,
            'average_rating_given' => round($overallAverage, 2),
            'rating_distribution' => $ratingDistribution,
            'this_month' => ThreadRating::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count() +
                ShowcaseRating::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count()];
    }
}
