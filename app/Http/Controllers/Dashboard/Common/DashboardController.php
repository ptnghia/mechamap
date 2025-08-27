<?php

namespace App\Http\Controllers\Dashboard\Common;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Dashboard Controller chính
 *
 * Hiển thị trang dashboard tổng quan cho thành viên
 */
class DashboardController extends BaseController
{
    /**
     * Hiển thị dashboard chính
     */
    public function index(Request $request)
    {
        $data = [
            'recentActivity' => $this->getRecentActivity(),
            'quickStats' => $this->getQuickStats(),
            'recentThreads' => $this->getRecentThreads(),
            'recentNotifications' => $this->getRecentNotifications(),
            'upcomingEvents' => $this->getUpcomingEvents(),
        ];

        // Thêm marketplace data nếu user có quyền
        // TODO: Implement marketplace permissions
        // if ($this->user->hasAnyMarketplacePermission()) {
        //     $data['recentOrders'] = $this->getRecentOrders();
        //     $data['marketplaceActivity'] = $this->getMarketplaceActivity();
        // }

        return $this->dashboardResponse('dashboard.common.index', $data);
    }

    /**
     * Lấy hoạt động gần đây của user
     */
    private function getRecentActivity()
    {
        $activities = collect();

        // Thread activities
        $recentThreads = $this->user->threads()
            ->with(['category', 'forum'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($thread) {
                return [
                    'type' => 'thread_created',
                    'title' => 'Created thread: ' . $thread->title,
                    'url' => route('threads.show', $thread),
                    'created_at' => $thread->created_at,
                    'icon' => 'fas fa-plus-circle',
                    'color' => 'success'
                ];
            });

        // Comment activities
        $recentComments = $this->user->comments()
            ->with(['thread'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment_created',
                    'title' => 'Commented on: ' . $comment->thread->title,
                    'url' => route('threads.show', $comment->thread) . '#comment-' . $comment->id,
                    'created_at' => $comment->created_at,
                    'icon' => 'fas fa-comment',
                    'color' => 'info'
                ];
            });

        // Bookmark activities
        $recentBookmarks = $this->user->bookmarks()
            ->with(['bookmarkable'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($bookmark) {
                // Only process thread bookmarks for now
                if ($bookmark->bookmarkable_type === 'App\\Models\\Thread' && $bookmark->bookmarkable) {
                    return [
                        'type' => 'thread_bookmarked',
                        'title' => 'Bookmarked: ' . $bookmark->bookmarkable->title,
                        'url' => route('threads.show', $bookmark->bookmarkable),
                        'created_at' => $bookmark->created_at,
                        'icon' => 'fas fa-bookmark',
                        'color' => 'warning'
                    ];
                }
                return null; // Skip non-thread bookmarks
            })
            ->filter(); // Remove null values

        return $activities
            ->merge($recentThreads)
            ->merge($recentComments)
            ->merge($recentBookmarks)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }

    /**
     * Lấy thống kê nhanh
     */
    private function getQuickStats()
    {
        $stats = $this->getDashboardStats();

        return [
            [
                'title' => 'My Threads',
                'value' => $stats['threads_count'],
                'icon' => 'fas fa-comments',
                'color' => 'primary',
                'url' => route('dashboard.community.threads.index')
            ],
            [
                'title' => 'Comments',
                'value' => $stats['comments_count'],
                'icon' => 'fas fa-comment',
                'color' => 'info',
                'url' => route('dashboard.community.comments.index')
            ],
            [
                'title' => 'Bookmarks',
                'value' => $stats['bookmarks_count'],
                'icon' => 'fas fa-bookmark',
                'color' => 'warning',
                'url' => route('dashboard.community.bookmarks.index')
            ],
            [
                'title' => 'Following',
                'value' => $stats['following_count'],
                'icon' => 'fas fa-users',
                'color' => 'success',
                'url' => route('dashboard.activity')
            ],
        ];
    }

    /**
     * Lấy threads gần đây
     */
    private function getRecentThreads()
    {
        return $this->user->threads()
            ->with(['category', 'forum', 'user'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Lấy thông báo gần đây
     */
    private function getRecentNotifications()
    {
        return $this->user->notifications()
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Lấy sự kiện sắp tới
     */
    private function getUpcomingEvents()
    {
        // Placeholder - có thể implement sau
        return collect();
    }

    /**
     * Lấy đơn hàng gần đây (marketplace)
     */
    private function getRecentOrders()
    {
        if (!$this->user->hasMarketplacePermission('buy')) {
            return collect();
        }

        return $this->user->marketplaceOrders()
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Lấy hoạt động marketplace
     */
    private function getMarketplaceActivity()
    {
        if (!$this->user->hasAnyMarketplacePermission()) {
            return collect();
        }

        $activities = collect();

        // Recent purchases
        if ($this->user->hasMarketplacePermission('buy')) {
            $recentPurchases = $this->user->marketplaceOrders()
                ->with(['items.product'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($order) {
                    return [
                        'type' => 'order_placed',
                        'title' => 'Purchased order #' . $order->order_number,
                        'url' => route('dashboard.marketplace.orders.show', $order),
                        'created_at' => $order->created_at,
                        'icon' => 'fas fa-shopping-cart',
                        'color' => 'success'
                    ];
                });

            $activities = $activities->merge($recentPurchases);
        }

        // Recent sales (for sellers)
        if ($this->user->hasMarketplacePermission('sell')) {
            $recentSales = $this->user->marketplaceSales()
                ->with(['product'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($sale) {
                    return [
                        'type' => 'product_sold',
                        'title' => 'Sold: ' . $sale->product->name,
                        'url' => route('dashboard.marketplace.seller.orders', $sale),
                        'created_at' => $sale->created_at,
                        'icon' => 'fas fa-dollar-sign',
                        'color' => 'success'
                    ];
                });

            $activities = $activities->merge($recentSales);
        }

        return $activities->sortByDesc('created_at')->take(5)->values();
    }
}
