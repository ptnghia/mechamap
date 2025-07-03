<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with forums grouped by category.
     * This replaces the old forums index to show proper 3-tier structure.
     */
    public function index(): View
    {
        // Cache categories with their forums and statistics
        $categories = Cache::remember('categories.with_forums_stats', 3600, function () {
            return Category::where('is_active', true)
                ->whereNull('parent_id') // Only root categories
                ->with([
                    'forums' => function ($query) {
                        $query->where('is_private', false)
                            ->orderBy('order')
                            ->withCount(['threads', 'posts']);
                    },
                    'forums.media' => function ($query) {
                        $query->where('mime_type', 'like', 'image/%');
                    }
                ])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(function ($category) {
                    // Calculate category statistics
                    $category->stats = $this->calculateCategoryStats($category);

                    // Get recent threads from this category's forums
                    $category->recent_threads = $this->getRecentThreadsForCategory($category);

                    return $category;
                });
        });

        // Cache overall forum statistics
        $stats = Cache::remember('forums.overall_stats', 1800, function () {
            return [
                'categories' => Category::where('is_active', true)->count(),
                'forums' => Forum::count(),
                'threads' => Thread::count(),
                'posts' => Comment::count(),
                'users' => User::count(),
                'newest_member' => User::latest()->first()
            ];
        });

        return view('forums.index', compact('categories', 'stats'));
    }

    /**
     * Display the specified category with its forums and recent threads.
     */
    public function show(Category $category): View
    {
        // Load category with forums and their statistics
        $category->load([
            'forums' => function ($query) {
                $query->where('is_private', false)
                    ->withCount(['threads', 'posts'])
                    ->orderBy('order');
            },
            'forums.media'
        ]);

        // Get category statistics
        $categoryStats = $this->calculateCategoryStats($category);

        // Get recent threads from this category (paginated) - sticky first
        $recentThreads = Thread::whereHas('forum', function ($query) use ($category) {
                $query->where('category_id', $category->id);
            })
            ->with(['user', 'forum'])
            ->withCount('comments')
            ->publicVisible()
            ->orderBy('is_sticky', 'desc')
            ->latest()
            ->paginate(20);

        // Get trending threads from this category
        $trendingThreads = Thread::whereHas('forum', function ($query) use ($category) {
                $query->where('category_id', $category->id);
            })
            ->with(['user', 'forum'])
            ->withCount('comments')
            ->publicVisible()
            ->trending()
            ->take(10)
            ->get();

        return view('categories.show', compact('category', 'categoryStats', 'recentThreads', 'trendingThreads'));
    }

    /**
     * Calculate comprehensive statistics for a category.
     */
    private function calculateCategoryStats(Category $category): array
    {
        $forumIds = $category->forums->pluck('id');

        if ($forumIds->isEmpty()) {
            return [
                'forums_count' => 0,
                'threads_count' => 0,
                'posts_count' => 0,
                'views_count' => 0,
                'participants_count' => 0,
                'last_activity_at' => null,
                'most_active_forum' => null
            ];
        }

        // Use database aggregation for better performance
        $stats = DB::table('threads')
            ->whereIn('forum_id', $forumIds)
            ->selectRaw('
                COUNT(*) as threads_count,
                SUM(view_count) as total_views,
                SUM(cached_comments_count) as total_comments,
                COUNT(DISTINCT user_id) as unique_participants,
                MAX(last_activity_at) as last_activity
            ')
            ->first();

        // Get most active forum in this category
        $mostActiveForum = Forum::whereIn('id', $forumIds)
            ->withCount('threads')
            ->orderByDesc('threads_count')
            ->first();

        return [
            'forums_count' => $category->forums->count(),
            'threads_count' => $stats->threads_count ?? 0,
            'posts_count' => $stats->total_comments ?? 0,
            'views_count' => $stats->total_views ?? 0,
            'participants_count' => $stats->unique_participants ?? 0,
            'last_activity_at' => $stats->last_activity ? \Carbon\Carbon::parse($stats->last_activity) : null,
            'most_active_forum' => $mostActiveForum
        ];
    }

    /**
     * Get recent threads for a category (for homepage display).
     */
    private function getRecentThreadsForCategory(Category $category, int $limit = 5): \Illuminate\Support\Collection
    {
        $forumIds = $category->forums->pluck('id');

        if ($forumIds->isEmpty()) {
            return collect();
        }

        return Thread::whereIn('forum_id', $forumIds)
            ->with(['user', 'forum'])
            ->withCount('comments')
            ->publicVisible()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Search within a specific category.
     */
    public function search(Category $category, Request $request): View
    {
        $request->validate([
            'q' => 'required|string|min:3|max:100'
        ]);

        $query = $request->get('q');
        $forumIds = $category->forums->pluck('id');

        // Search threads in this category
        $threads = Thread::whereIn('forum_id', $forumIds)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['forum', 'user'])
            ->withCount('comments')
            ->publicVisible()
            ->paginate(20);

        return view('categories.search', compact('category', 'threads', 'query'));
    }

    /**
     * Get trending categories based on activity.
     */
    public function trending(): View
    {
        $trendingCategories = Cache::remember('categories.trending', 1800, function () {
            return Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['forums'])
                ->get()
                ->map(function ($category) {
                    $stats = $this->calculateCategoryStats($category);
                    $category->trending_score = $this->calculateTrendingScore($stats);
                    return $category;
                })
                ->sortByDesc('trending_score')
                ->take(10);
        });

        return view('categories.trending', compact('trendingCategories'));
    }

    /**
     * Calculate trending score for a category.
     */
    private function calculateTrendingScore(array $stats): float
    {
        $threadsWeight = 0.3;
        $viewsWeight = 0.4;
        $participantsWeight = 0.3;

        return ($stats['threads_count'] * $threadsWeight) +
               ($stats['views_count'] * 0.001 * $viewsWeight) + // Scale down views
               ($stats['participants_count'] * $participantsWeight);
    }
}
