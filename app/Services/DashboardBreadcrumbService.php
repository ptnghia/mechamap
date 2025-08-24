<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class DashboardBreadcrumbService
{
    /**
     * Generate breadcrumbs for dashboard routes
     *
     * @return array
     */
    public static function generate(): array
    {
        $routeName = Route::currentRouteName();
        $breadcrumbs = [];

        // Always start with home
        $breadcrumbs[] = [
            'title' => __('ui.navigation.home'),
            'url' => route('home'),
            'active' => false
        ];

        // Add dashboard breadcrumbs based on route
        $breadcrumbs = array_merge($breadcrumbs, self::getDashboardBreadcrumbs($routeName));

        return $breadcrumbs;
    }

    /**
     * Get dashboard specific breadcrumbs
     *
     * @param string|null $routeName
     * @return array
     */
    private static function getDashboardBreadcrumbs(?string $routeName): array
    {
        if (!$routeName || !str_starts_with($routeName, 'dashboard')) {
            return [];
        }

        $breadcrumbs = [];

        // Dashboard home
        $breadcrumbs[] = [
            'title' => __('dashboard.navigation.dashboard'),
            'url' => route('dashboard'),
            'active' => $routeName === 'dashboard'
        ];

        // Route specific breadcrumbs
        switch ($routeName) {
            // Profile routes
            case 'dashboard.profile.edit':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.profile'),
                    'url' => route('dashboard.profile.edit'),
                    'active' => true
                ];
                break;

            // Activity
            case 'dashboard.activity':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.activity'),
                    'url' => route('dashboard.activity'),
                    'active' => true
                ];
                break;

            // Notifications
            case 'dashboard.notifications.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.notifications'),
                    'url' => route('dashboard.notifications.index'),
                    'active' => true
                ];
                break;

            // Messages
            case 'dashboard.messages.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.messages'),
                    'url' => route('dashboard.messages.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.messages.groups.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.messages'),
                    'url' => route('dashboard.messages.index'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.groups'),
                    'url' => route('dashboard.messages.groups.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.messages.groups.create':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.messages'),
                    'url' => route('dashboard.messages.index'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.groups'),
                    'url' => route('dashboard.messages.groups.index'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.create_group'),
                    'url' => route('dashboard.messages.groups.create'),
                    'active' => true
                ];
                break;

            // Settings
            case 'dashboard.settings.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.settings'),
                    'url' => route('dashboard.settings.index'),
                    'active' => true
                ];
                break;

            // Community routes
            case 'dashboard.community.threads.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.community'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.threads'),
                    'url' => route('dashboard.community.threads.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.community.bookmarks.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.community'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.bookmarks'),
                    'url' => route('dashboard.community.bookmarks.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.community.comments.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.community'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.comments'),
                    'url' => route('dashboard.community.comments.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.community.showcases.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.community'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.showcases'),
                    'url' => route('dashboard.community.showcases.index'),
                    'active' => true
                ];
                break;

            // Marketplace routes
            case 'dashboard.marketplace.orders.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.marketplace'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.orders'),
                    'url' => route('dashboard.marketplace.orders.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.marketplace.downloads.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.marketplace'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.downloads'),
                    'url' => route('dashboard.marketplace.downloads.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.marketplace.wishlist.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.marketplace'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.wishlist'),
                    'url' => route('dashboard.marketplace.wishlist.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.marketplace.seller.dashboard':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.marketplace'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.seller'),
                    'url' => route('dashboard.marketplace.seller.dashboard'),
                    'active' => true
                ];
                break;

            case 'dashboard.marketplace.seller.products.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.marketplace'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.seller'),
                    'url' => route('dashboard.marketplace.seller.dashboard'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.products'),
                    'url' => route('dashboard.marketplace.seller.products.index'),
                    'active' => true
                ];
                break;

            case 'dashboard.marketplace.seller.analytics.index':
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.marketplace'),
                    'url' => '#',
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.seller'),
                    'url' => route('dashboard.marketplace.seller.dashboard'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('dashboard.navigation.analytics'),
                    'url' => route('dashboard.marketplace.seller.analytics.index'),
                    'active' => true
                ];
                break;

            default:
                // For unknown dashboard routes, mark dashboard as active
                $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
                break;
        }

        return $breadcrumbs;
    }
}
