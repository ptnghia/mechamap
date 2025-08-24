<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Base Controller cho tất cả Dashboard Controllers
 *
 * Cung cấp các method và data chung cho dashboard
 */
class BaseController extends Controller
{
    /**
     * User hiện tại
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');

        // Set user sau khi middleware auth chạy
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            // Share user data với tất cả views
            View::share('currentUser', $this->user);

            return $next($request);
        });
    }

    /**
     * Lấy thống kê chung cho dashboard
     */
    protected function getDashboardStats()
    {
        if (!$this->user) {
            return [];
        }

        return [
            'threads_count' => $this->user->threads()->count(),
            'comments_count' => $this->user->comments()->count(),
            'bookmarks_count' => $this->user->bookmarks()->count(),
            'following_count' => $this->user->following()->count(),
            'followers_count' => $this->user->followers()->count(),
            'notifications_unread' => $this->user->unreadNotifications()->count(),
        ];
    }

    /**
     * Lấy thống kê marketplace cho user
     */
    protected function getMarketplaceStats()
    {
        if (!$this->user) {
            return [];
        }

        $stats = [
            'orders_count' => 0,
            'downloads_count' => 0,
            'wishlist_count' => 0,
        ];

        // Chỉ load marketplace stats nếu user có quyền
        // TODO: Implement marketplace permissions
        // if ($this->user->hasMarketplacePermission('buy')) {
        //     $stats['orders_count'] = $this->user->marketplaceOrders()->count();
        //     $stats['downloads_count'] = $this->user->marketplaceDownloads()->count();
        //     $stats['wishlist_count'] = $this->user->marketplaceWishlist()->count();
        // }

        // Seller stats
        // TODO: Implement marketplace permissions
        // if ($this->user->hasMarketplacePermission('sell')) {
        //     $stats['products_count'] = $this->user->marketplaceProducts()->count();
        //     $stats['sales_count'] = $this->user->marketplaceSales()->count();
        // }

        return $stats;
    }

    /**
     * Lấy menu items cho dashboard sidebar
     */
    protected function getDashboardMenuItems()
    {
        $menuItems = [
            'common' => [
                [
                    'name' => __('sidebar.user_dashboard.dashboard'),
                    'route' => 'dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'active' => request()->routeIs('dashboard')
                ],
                [
                    'name' => __('sidebar.user_dashboard.profile'),
                    'route' => 'dashboard.profile.edit',
                    'icon' => 'fas fa-user',
                    'active' => request()->routeIs('dashboard.profile.*')
                ],
                [
                    'name' => __('sidebar.user_dashboard.activity'),
                    'route' => 'dashboard.activity',
                    'icon' => 'fas fa-chart-line',
                    'active' => request()->routeIs('dashboard.activity')
                ],
                [
                    'name' => __('sidebar.user_dashboard.notifications'),
                    'route' => 'dashboard.notifications.index',
                    'icon' => 'fas fa-bell',
                    'active' => request()->routeIs('dashboard.notifications.index'),
                    'badge' => $this->user ? $this->user->unreadNotifications()->count() : 0
                ],
                [
                    'name' => __('sidebar.user_dashboard.messages'),
                    'route' => 'dashboard.messages.index',
                    'icon' => 'fas fa-envelope',
                    'active' => request()->routeIs('dashboard.messages.*')
                ],
                [
                    'name' => __('sidebar.user_dashboard.settings'),
                    'route' => 'dashboard.settings.index',
                    'icon' => 'fas fa-cog',
                    'active' => request()->routeIs('dashboard.settings.index')
                ],
            ],
            'messages' => [
                [
                    'name' => __('sidebar.user_dashboard.all_messages'),
                    'route' => 'dashboard.messages.index',
                    'icon' => 'fas fa-comments',
                    'active' => request()->routeIs('dashboard.messages.index')
                ],
                [
                    'name' => __('sidebar.user_dashboard.group_conversations'),
                    'route' => 'dashboard.messages.groups.index',
                    'icon' => 'fas fa-users',
                    'active' => request()->routeIs('dashboard.messages.groups.*')
                ],
                [
                    'name' => __('sidebar.user_dashboard.create_group'),
                    'route' => 'dashboard.messages.groups.create',
                    'icon' => 'fas fa-plus-circle',
                    'active' => request()->routeIs('dashboard.messages.groups.create')
                ],
                [
                    'name' => __('sidebar.user_dashboard.new_message'),
                    'route' => 'dashboard.messages.create',
                    'icon' => 'fas fa-edit',
                    'active' => request()->routeIs('dashboard.messages.create')
                ],
            ],
            'community' => [
                [
                    'name' => __('sidebar.user_dashboard.my_threads'),
                    'route' => 'dashboard.community.threads.index',
                    'icon' => 'fas fa-comments',
                    'active' => request()->routeIs('dashboard.community.threads.index')
                ],
                [
                    'name' => __('sidebar.user_dashboard.bookmarks'),
                    'route' => 'dashboard.community.bookmarks.index',
                    'icon' => 'fas fa-bookmark',
                    'active' => request()->routeIs('dashboard.community.bookmarks.index')
                ],
                [
                    'name' => __('sidebar.user_dashboard.showcases'),
                    'route' => 'dashboard.community.showcases.index',
                    'icon' => 'fas fa-star',
                    'active' => request()->routeIs('dashboard.community.showcases.index')
                ],
                // TODO: Implement following feature
                // [
                //     'name' => 'Following',
                //     'route' => 'dashboard.following',
                //     'icon' => 'fas fa-users',
                //     'active' => request()->routeIs('dashboard.following')
                // ],
            ]
        ];

        // Thêm marketplace menu nếu user có quyền
        // TODO: Implement marketplace permissions
        // if ($this->user && $this->user->hasAnyMarketplacePermission()) {
        //     $menuItems['marketplace'] = [
        //         [
        //             'name' => 'Orders',
        //             'route' => 'dashboard.marketplace.orders',
        //             'icon' => 'fas fa-shopping-bag',
        //             'active' => request()->routeIs('dashboard.marketplace.orders')
        //         ],
        //         [
        //             'name' => 'Downloads',
        //             'route' => 'dashboard.marketplace.downloads',
        //             'icon' => 'fas fa-download',
        //             'active' => request()->routeIs('dashboard.marketplace.downloads')
        //         ],
        //         [
        //             'name' => 'Wishlist',
        //             'route' => 'dashboard.marketplace.wishlist',
        //             'icon' => 'fas fa-heart',
        //             'active' => request()->routeIs('dashboard.marketplace.wishlist')
        //         ],
        //     ];

        //     // Seller menu items
        //     if ($this->user->hasMarketplacePermission('sell')) {
        //         $menuItems['marketplace'][] = [
        //             'name' => 'Seller Dashboard',
        //             'route' => 'dashboard.marketplace.seller.dashboard',
        //             'icon' => 'fas fa-store',
        //             'active' => request()->routeIs('dashboard.marketplace.seller.*')
        //         ];
        //     }
        // }

        return $menuItems;
    }

    /**
     * Response với data chung cho dashboard
     */
    protected function dashboardResponse($view, $data = [])
    {
        $commonData = [
            'dashboardStats' => $this->getDashboardStats(),
            'marketplaceStats' => $this->getMarketplaceStats(),
            'menuItems' => $this->getDashboardMenuItems(),
        ];

        return view($view, array_merge($commonData, $data));
    }
}
