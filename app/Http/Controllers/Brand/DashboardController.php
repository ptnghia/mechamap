<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:brand']);
    }

    /**
     * Display brand dashboard
     * Brands have view-only access for promotion purposes
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get brand analytics for promotion insights
        $stats = $this->getBrandStats();

        // Get marketplace insights
        $marketplaceInsights = $this->getMarketplaceInsights();

        // Get forum insights for brand promotion
        $forumInsights = $this->getForumInsights();

        // Get trending topics related to brand's industry
        $trendingTopics = $this->getTrendingTopics();

        return view('brand.dashboard', compact('stats', 'marketplaceInsights', 'forumInsights', 'trendingTopics'));
    }

    /**
     * Get brand statistics for promotion insights
     */
    private function getBrandStats(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_marketplace_products' => MarketplaceProduct::where('is_active', true)->count(),
            'total_forum_threads' => Thread::count(),
            'active_users' => User::where('last_activity_at', '>=', Carbon::now()->subDays(30))->count(),
            'month_new_products' => MarketplaceProduct::where('created_at', '>=', $thisMonth)->count(),
            'month_new_threads' => Thread::where('created_at', '>=', $thisMonth)->count(),
            'growth_rate' => $this->calculateGrowthRate(),
        ];
    }

    /**
     * Get marketplace insights for brand promotion
     */
    private function getMarketplaceInsights(): array
    {
        return [
            'top_categories' => MarketplaceProduct::select('category', DB::raw('COUNT(*) as count'))
                ->where('is_active', true)
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'price_ranges' => MarketplaceProduct::select(
                    DB::raw('CASE
                        WHEN price < 50 THEN "Under $50"
                        WHEN price BETWEEN 50 AND 200 THEN "$50-$200"
                        WHEN price BETWEEN 200 AND 500 THEN "$200-$500"
                        ELSE "Over $500"
                    END as price_range'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('is_active', true)
                ->groupBy('price_range')
                ->get(),
            'popular_products' => MarketplaceProduct::withCount('orderItems')
                ->where('is_active', true)
                ->orderBy('order_items_count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get forum insights for brand engagement
     */
    private function getForumInsights(): array
    {
        return [
            'popular_topics' => Thread::withCount(['comments', 'likes'])
                ->orderBy('comments_count', 'desc')
                ->limit(10)
                ->get(),
            'active_categories' => Thread::select('category_id', DB::raw('COUNT(*) as thread_count'))
                ->groupBy('category_id')
                ->with('category')
                ->orderBy('thread_count', 'desc')
                ->limit(10)
                ->get(),
            'engagement_metrics' => [
                'avg_comments_per_thread' => Thread::withCount('comments')->avg('comments_count'),
                'avg_likes_per_thread' => Thread::withCount('likes')->avg('likes_count'),
                'most_active_day' => Thread::select(DB::raw('DAYNAME(created_at) as day'), DB::raw('COUNT(*) as count'))
                    ->groupBy('day')
                    ->orderBy('count', 'desc')
                    ->first(),
            ],
        ];
    }

    /**
     * Get trending topics for brand promotion opportunities
     */
    private function getTrendingTopics(): array
    {
        $lastWeek = Carbon::now()->subWeek();

        return [
            'trending_keywords' => Thread::where('created_at', '>=', $lastWeek)
                ->select('title')
                ->get()
                ->flatMap(function ($thread) {
                    return explode(' ', strtolower($thread->title));
                })
                ->filter(function ($word) {
                    return strlen($word) > 4; // Filter out short words
                })
                ->countBy()
                ->sortDesc()
                ->take(20)
                ->toArray(),
            'hot_discussions' => Thread::where('created_at', '>=', $lastWeek)
                ->withCount(['comments', 'likes'])
                ->orderBy('comments_count', 'desc')
                ->limit(10)
                ->get(),
            'emerging_categories' => Thread::where('created_at', '>=', $lastWeek)
                ->select('category_id', DB::raw('COUNT(*) as recent_activity'))
                ->groupBy('category_id')
                ->with('category')
                ->orderBy('recent_activity', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Calculate growth rate for brand insights
     */
    private function calculateGrowthRate(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2)->startOfMonth();

        $currentMonthProducts = MarketplaceProduct::whereBetween('created_at', [$thisMonth, Carbon::now()])->count();
        $lastMonthProducts = MarketplaceProduct::whereBetween('created_at', [$lastMonth, $thisMonth])->count();

        $currentMonthThreads = Thread::whereBetween('created_at', [$thisMonth, Carbon::now()])->count();
        $lastMonthThreads = Thread::whereBetween('created_at', [$lastMonth, $thisMonth])->count();

        return [
            'products_growth' => $lastMonthProducts > 0 ?
                round((($currentMonthProducts - $lastMonthProducts) / $lastMonthProducts) * 100, 2) : 0,
            'threads_growth' => $lastMonthThreads > 0 ?
                round((($currentMonthThreads - $lastMonthThreads) / $lastMonthThreads) * 100, 2) : 0,
        ];
    }
}
