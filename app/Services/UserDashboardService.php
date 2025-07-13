<?php

namespace App\Services;

use App\Models\User;
use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadRating;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\Showcase;
use Illuminate\Support\Facades\Auth;

/**
 * 🎯 MechaMap User Dashboard Service
 * 
 * Service xử lý logic dashboard động theo role của user
 * Hỗ trợ 4 nhóm thành viên frontend:
 * - Community Members (senior_member L7, member L8, guest L9)
 * - Business Partners (verified_partner L10, manufacturer L11, supplier L12, brand L13)
 */
class UserDashboardService
{
    /**
     * Lấy dữ liệu dashboard theo role của user
     */
    public static function getDashboardData(User $user): array
    {
        $role = $user->role;
        $roleGroup = self::getRoleGroup($role);
        
        return [
            'user' => $user,
            'role' => $role,
            'role_group' => $roleGroup,
            'navigation' => self::getNavigationMenu($role),
            'stats' => self::getUserStats($user),
            'widgets' => self::getDashboardWidgets($role, $user),
            'permissions' => self::getUserPermissions($role),
            'quick_actions' => self::getQuickActions($role),
        ];
    }

    /**
     * Xác định nhóm role của user
     */
    public static function getRoleGroup(string $role): string
    {
        $roleGroups = config('mechamap_permissions.role_groups');
        
        foreach ($roleGroups as $groupKey => $group) {
            if (in_array($role, $group['roles'])) {
                return $groupKey;
            }
        }
        
        return 'community_members'; // Default fallback
    }

    /**
     * Lấy navigation menu theo role
     */
    public static function getNavigationMenu(string $role): array
    {
        $baseMenu = [
            'dashboard' => [
                'title' => __('nav.user.dashboard'),
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'user.dashboard',
                'active' => true,
            ],
            'profile' => [
                'title' => __('nav.user.profile'),
                'icon' => 'fas fa-user',
                'route' => 'profile.edit',
            ],
            'notifications' => [
                'title' => __('nav.user.notifications'),
                'icon' => 'fas fa-bell',
                'route' => 'user.notifications',
            ],
        ];

        // Community Members menu
        if (in_array($role, ['member', 'senior_member'])) {
            $baseMenu['my_threads'] = [
                'title' => __('messages.my_threads'),
                'icon' => 'fas fa-comments',
                'route' => 'user.my-threads',
            ];
            $baseMenu['bookmarks'] = [
                'title' => __('messages.bookmarks'),
                'icon' => 'fas fa-bookmark',
                'route' => 'user.bookmarks',
            ];
            $baseMenu['activity'] = [
                'title' => __('messages.activity'),
                'icon' => 'fas fa-chart-line',
                'route' => 'user.activity',
            ];
        }

        // Guest role menu (limited)
        if ($role === 'guest') {
            $baseMenu['following'] = [
                'title' => __('messages.following'),
                'icon' => 'fas fa-heart',
                'route' => 'user.following',
            ];
            $baseMenu['marketplace'] = [
                'title' => __('messages.marketplace'),
                'icon' => 'fas fa-store',
                'route' => 'marketplace.index',
            ];
        }

        // Business Partners menu
        if (in_array($role, ['manufacturer', 'supplier', 'brand', 'verified_partner'])) {
            $baseMenu['business_dashboard'] = [
                'title' => __('messages.business_dashboard'),
                'icon' => 'fas fa-chart-bar',
                'route' => 'business.dashboard',
            ];
            
            if (in_array($role, ['manufacturer', 'supplier', 'verified_partner'])) {
                $baseMenu['my_products'] = [
                    'title' => __('messages.my_products'),
                    'icon' => 'fas fa-box',
                    'route' => 'marketplace.seller.products',
                ];
                $baseMenu['orders'] = [
                    'title' => __('messages.orders'),
                    'icon' => 'fas fa-shopping-cart',
                    'route' => 'marketplace.seller.orders',
                ];
            }

            if ($role === 'brand') {
                $baseMenu['market_insights'] = [
                    'title' => __('messages.market_insights'),
                    'icon' => 'fas fa-chart-pie',
                    'route' => 'brand.insights',
                ];
                $baseMenu['promotion_opportunities'] = [
                    'title' => __('messages.promotion_opportunities'),
                    'icon' => 'fas fa-bullhorn',
                    'route' => 'brand.promotions',
                ];
            }
        }

        return $baseMenu;
    }

    /**
     * Lấy thống kê user theo role
     */
    public static function getUserStats(User $user): array
    {
        $role = $user->role;
        $baseStats = [
            'threads_created' => Thread::where('user_id', $user->id)->count(),
            'threads_bookmarked' => ThreadBookmark::where('user_id', $user->id)->count(),
            'ratings_given' => ThreadRating::where('user_id', $user->id)->count(),
            'average_rating_received' => Thread::where('user_id', $user->id)
                ->where('ratings_count', '>', 0)
                ->avg('average_rating'),
        ];

        // Business Partners stats
        if (in_array($role, ['manufacturer', 'supplier', 'verified_partner'])) {
            $baseStats['products_count'] = MarketplaceProduct::where('seller_id', $user->id)->count();
            $baseStats['total_sales'] = MarketplaceOrder::where('seller_id', $user->id)
                ->where('status', 'completed')
                ->sum('total_amount');
            $baseStats['pending_orders'] = MarketplaceOrder::where('seller_id', $user->id)
                ->where('status', 'pending')
                ->count();
        }

        // Manufacturer specific stats
        if ($role === 'manufacturer') {
            $baseStats['digital_products'] = MarketplaceProduct::where('seller_id', $user->id)
                ->where('type', 'digital')
                ->count();
        }

        // Brand specific stats
        if ($role === 'brand') {
            $baseStats['showcases_count'] = Showcase::where('user_id', $user->id)->count();
            $baseStats['total_views'] = Showcase::where('user_id', $user->id)->sum('view_count');
        }

        return $baseStats;
    }

    /**
     * Lấy widgets dashboard theo role
     */
    public static function getDashboardWidgets(string $role, User $user): array
    {
        $widgets = [];

        // Community Members widgets
        if (in_array($role, ['member', 'senior_member'])) {
            $widgets['recent_threads'] = [
                'title' => __('messages.recent_threads'),
                'type' => 'list',
                'data' => Thread::where('user_id', $user->id)
                    ->with(['forum'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
            
            $widgets['recent_bookmarks'] = [
                'title' => __('messages.recent_bookmarks'),
                'type' => 'list',
                'data' => ThreadBookmark::with(['thread.user', 'thread.forum'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Guest widgets
        if ($role === 'guest') {
            $widgets['marketplace_highlights'] = [
                'title' => __('messages.marketplace_highlights'),
                'type' => 'grid',
                'data' => MarketplaceProduct::where('type', 'digital')
                    ->where('status', 'active')
                    ->latest()
                    ->limit(6)
                    ->get(),
            ];
        }

        // Business Partners widgets
        if (in_array($role, ['manufacturer', 'supplier', 'verified_partner'])) {
            $widgets['recent_orders'] = [
                'title' => __('messages.recent_orders'),
                'type' => 'table',
                'data' => MarketplaceOrder::where('seller_id', $user->id)
                    ->with(['buyer', 'product'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];

            $widgets['product_performance'] = [
                'title' => __('messages.product_performance'),
                'type' => 'chart',
                'data' => self::getProductPerformanceData($user),
            ];
        }

        // Brand specific widgets
        if ($role === 'brand') {
            $widgets['market_trends'] = [
                'title' => __('messages.market_trends'),
                'type' => 'chart',
                'data' => self::getMarketTrendsData(),
            ];
        }

        return $widgets;
    }

    /**
     * Lấy permissions của user theo role
     */
    public static function getUserPermissions(string $role): array
    {
        $permissions = config('mechamap_permissions.permission_groups');
        $rolePermissions = [];

        // Basic permissions for all users
        $rolePermissions = array_merge($rolePermissions, $permissions['basic']['permissions'] ?? []);

        // Guest permissions
        if ($role === 'guest') {
            $rolePermissions = array_merge($rolePermissions, $permissions['guest']['permissions'] ?? []);
        }

        // Business permissions
        if (in_array($role, ['manufacturer', 'supplier', 'brand', 'verified_partner'])) {
            $rolePermissions = array_merge($rolePermissions, $permissions['business']['permissions'] ?? []);
        }

        return array_unique($rolePermissions);
    }

    /**
     * Lấy quick actions theo role
     */
    public static function getQuickActions(string $role): array
    {
        $actions = [];

        // Community Members actions
        if (in_array($role, ['member', 'senior_member'])) {
            $actions['create_thread'] = [
                'title' => __('messages.create_thread'),
                'icon' => 'fas fa-plus',
                'route' => 'threads.create',
                'class' => 'btn-primary',
            ];
        }

        // Business Partners actions
        if (in_array($role, ['manufacturer', 'supplier', 'verified_partner'])) {
            $actions['add_product'] = [
                'title' => __('messages.add_product'),
                'icon' => 'fas fa-plus',
                'route' => 'marketplace.seller.products.create',
                'class' => 'btn-success',
            ];
        }

        // Brand actions
        if ($role === 'brand') {
            $actions['create_showcase'] = [
                'title' => __('messages.create_showcase'),
                'icon' => 'fas fa-star',
                'route' => 'showcases.create',
                'class' => 'btn-info',
            ];
        }

        return $actions;
    }

    /**
     * Lấy dữ liệu performance sản phẩm
     */
    private static function getProductPerformanceData(User $user): array
    {
        // Implementation for product performance chart data
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => [12, 19, 3, 5, 2, 3],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ]
            ]
        ];
    }

    /**
     * Lấy dữ liệu market trends
     */
    private static function getMarketTrendsData(): array
    {
        // Implementation for market trends data
        return [
            'trending_categories' => ['CAD Files', 'Technical Drawings', 'Equipment'],
            'growth_rate' => 15.5,
            'market_size' => 1250000,
        ];
    }
}
