<?php

namespace App\Http\Controllers\Dashboard\Common;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Activity Controller cho Dashboard
 *
 * Hiển thị hoạt động của user
 */
class ActivityController extends BaseController
{
    /**
     * Hiển thị trang activity
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $period = $request->get('period', 'week');

        $activities = $this->getActivities($filter, $period);
        $stats = $this->getActivityStats($period);
        $timeline = $this->getActivityTimeline($period);

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Activity', 'route' => 'dashboard.activity']
        ]);

        return $this->dashboardResponse('dashboard.common.activity.index', [
            'activities' => $activities,
            'stats' => $stats,
            'timeline' => $timeline,
            'currentFilter' => $filter,
            'currentPeriod' => $period,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Lấy danh sách activities
     */
    private function getActivities($filter, $period)
    {
        $activities = collect();
        $dateRange = $this->getDateRange($period);

        // Thread activities
        if ($filter === 'all' || $filter === 'threads') {
            $threadActivities = $this->user->threads()
                ->whereBetween('created_at', $dateRange)
                ->with(['category', 'forum'])
                ->latest()
                ->get()
                ->map(function ($thread) {
                    return [
                        'type' => 'thread_created',
                        'title' => 'Created thread: ' . $thread->title,
                        'description' => 'Posted in ' . $thread->forum->name,
                        'url' => route('threads.show', $thread),
                        'created_at' => $thread->created_at,
                        'icon' => 'fas fa-plus-circle',
                        'color' => 'success',
                        'data' => $thread
                    ];
                });

            $activities = $activities->merge($threadActivities);
        }

        // Comment activities
        if ($filter === 'all' || $filter === 'comments') {
            $commentActivities = $this->user->comments()
                ->whereBetween('created_at', $dateRange)
                ->with(['thread'])
                ->latest()
                ->get()
                ->map(function ($comment) {
                    return [
                        'type' => 'comment_created',
                        'title' => 'Commented on: ' . $comment->thread->title,
                        'description' => substr(strip_tags($comment->content), 0, 100) . '...',
                        'url' => route('threads.show', $comment->thread) . '#comment-' . $comment->id,
                        'created_at' => $comment->created_at,
                        'icon' => 'fas fa-comment',
                        'color' => 'info',
                        'data' => $comment
                    ];
                });

            $activities = $activities->merge($commentActivities);
        }

        // Bookmark activities
        if ($filter === 'all' || $filter === 'bookmarks') {
            $bookmarkActivities = $this->user->bookmarks()
                ->whereBetween('created_at', $dateRange)
                ->with(['thread'])
                ->latest()
                ->get()
                ->map(function ($bookmark) {
                    return [
                        'type' => 'thread_bookmarked',
                        'title' => 'Bookmarked: ' . $bookmark->thread->title,
                        'description' => 'Saved for later reading',
                        'url' => route('threads.show', $bookmark->thread),
                        'created_at' => $bookmark->created_at,
                        'icon' => 'fas fa-bookmark',
                        'color' => 'warning',
                        'data' => $bookmark
                    ];
                });

            $activities = $activities->merge($bookmarkActivities);
        }

        // Marketplace activities
        if (($filter === 'all' || $filter === 'marketplace') && $this->user->canAccessMarketplace()) {
            $marketplaceActivities = $this->getMarketplaceActivities($dateRange);
            $activities = $activities->merge($marketplaceActivities);
        }

        return $activities->sortByDesc('created_at')->take(50)->values();
    }

    /**
     * Lấy marketplace activities
     */
    private function getMarketplaceActivities($dateRange)
    {
        $activities = collect();

        // Order activities
        if ($this->user->canBuyAnyProduct()) {
            $orderActivities = $this->user->marketplaceOrders()
                ->whereBetween('created_at', $dateRange)
                ->with(['items.product'])
                ->latest()
                ->get()
                ->map(function ($order) {
                    return [
                        'type' => 'order_placed',
                        'title' => 'Placed order #' . $order->order_number,
                        'description' => $order->items->count() . ' items, Total: ' . number_format($order->total_amount) . ' VND',
                        'url' => route('dashboard.marketplace.orders.show', $order),
                        'created_at' => $order->created_at,
                        'icon' => 'fas fa-shopping-cart',
                        'color' => 'success',
                        'data' => $order
                    ];
                });

            $activities = $activities->merge($orderActivities);
        }

        // Product activities (for sellers)
        if ($this->user->canSellAnyProduct()) {
            $productActivities = $this->user->marketplaceProducts()
                ->whereBetween('created_at', $dateRange)
                ->latest()
                ->get()
                ->map(function ($product) {
                    return [
                        'type' => 'product_created',
                        'title' => 'Listed product: ' . $product->name,
                        'description' => 'Price: ' . number_format($product->price) . ' VND',
                        'url' => route('marketplace.products.show', $product),
                        'created_at' => $product->created_at,
                        'icon' => 'fas fa-box',
                        'color' => 'primary',
                        'data' => $product
                    ];
                });

            $activities = $activities->merge($productActivities);
        }

        return $activities;
    }

    /**
     * Lấy thống kê activity
     */
    private function getActivityStats($period)
    {
        $dateRange = $this->getDateRange($period);

        return [
            'threads_created' => $this->user->threads()->whereBetween('created_at', $dateRange)->count(),
            'comments_posted' => $this->user->comments()->whereBetween('created_at', $dateRange)->count(),
            'bookmarks_added' => $this->user->bookmarks()->whereBetween('created_at', $dateRange)->count(),
            'likes_received' => $this->getLikesReceived($dateRange),
            'profile_views' => $this->getProfileViews($dateRange),
            'marketplace_orders' => $this->user->canBuyAnyProduct() ?
                $this->user->marketplaceOrders()->whereBetween('created_at', $dateRange)->count() : 0,
        ];
    }

    /**
     * Lấy timeline activity
     */
    private function getActivityTimeline($period)
    {
        $dateRange = $this->getDateRange($period);
        $timeline = [];

        // Group activities by date
        $dates = collect();
        $start = Carbon::parse($dateRange[0]);
        $end = Carbon::parse($dateRange[1]);

        while ($start->lte($end)) {
            $dates->push($start->copy());
            $start->addDay();
        }

        foreach ($dates as $date) {
            $dayStart = $date->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $timeline[] = [
                'date' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('M d'),
                'threads' => $this->user->threads()->whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'comments' => $this->user->comments()->whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'bookmarks' => $this->user->bookmarks()->whereBetween('created_at', [$dayStart, $dayEnd])->count(),
            ];
        }

        return $timeline;
    }

    /**
     * Lấy date range theo period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'year':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->startOfWeek(), now()->endOfWeek()];
        }
    }

    /**
     * Lấy số likes received
     */
    private function getLikesReceived($dateRange)
    {
        // Count likes on user's threads
        $threadLikes = $this->user->threads()
            ->whereBetween('created_at', $dateRange)
            ->withCount('likes')
            ->get()
            ->sum('likes_count');

        // Count likes on user's comments (if Comment model has likes relationship)
        $commentLikes = $this->user->comments()
            ->whereBetween('created_at', $dateRange)
            ->withCount('likes')
            ->get()
            ->sum('likes_count');

        return $threadLikes + $commentLikes;
    }

    /**
     * Lấy profile views
     */
    private function getProfileViews($dateRange)
    {
        // Placeholder - implement if profile views tracking is available
        return 0;
    }
}
