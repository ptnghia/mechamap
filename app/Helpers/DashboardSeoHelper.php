<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class DashboardSeoHelper
{
    /**
     * Get dashboard page title
     *
     * @param string|null $routeName
     * @return string
     */
    public static function getPageTitle(?string $routeName = null): string
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        $titles = [
            'dashboard' => 'Dashboard - MechaMap',
            'dashboard.profile.edit' => 'Chỉnh sửa hồ sơ - Dashboard - MechaMap',
            'dashboard.activity' => 'Hoạt động - Dashboard - MechaMap',
            'dashboard.notifications.index' => 'Thông báo - Dashboard - MechaMap',
            'dashboard.messages.index' => 'Tin nhắn - Dashboard - MechaMap',
            'dashboard.messages.groups.index' => 'Nhóm thảo luận - Dashboard - MechaMap',
            'dashboard.messages.groups.create' => 'Tạo nhóm - Dashboard - MechaMap',
            'dashboard.settings.index' => 'Cài đặt - Dashboard - MechaMap',
            'dashboard.community.threads.index' => 'Quản lý bài viết - Dashboard - MechaMap',
            'dashboard.community.bookmarks.index' => 'Bookmark - Dashboard - MechaMap',
            'dashboard.community.comments.index' => 'Quản lý bình luận - Dashboard - MechaMap',
            'dashboard.community.showcases.index' => 'Showcase - Dashboard - MechaMap',
            'dashboard.marketplace.orders.index' => 'Đơn hàng - Dashboard - MechaMap',
            'dashboard.marketplace.downloads.index' => 'Tải xuống - Dashboard - MechaMap',
            'dashboard.marketplace.wishlist.index' => 'Danh sách yêu thích - Dashboard - MechaMap',
            'dashboard.marketplace.seller.dashboard' => 'Bảng điều khiển người bán - Dashboard - MechaMap',
            'dashboard.marketplace.seller.products.index' => 'Quản lý sản phẩm - Dashboard - MechaMap',
            'dashboard.marketplace.seller.analytics.index' => 'Phân tích bán hàng - Dashboard - MechaMap',
        ];

        return $titles[$routeName] ?? 'Dashboard - MechaMap';
    }

    /**
     * Get dashboard page description
     *
     * @param string|null $routeName
     * @return string
     */
    public static function getPageDescription(?string $routeName = null): string
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        $descriptions = [
            'dashboard' => 'Quản lý tài khoản và hoạt động của bạn trên MechaMap - Cộng đồng kỹ thuật cơ khí Việt Nam',
            'dashboard.profile.edit' => 'Cập nhật thông tin cá nhân, avatar và cài đặt tài khoản trên MechaMap',
            'dashboard.activity' => 'Theo dõi lịch sử hoạt động và tương tác của bạn trên MechaMap',
            'dashboard.notifications.index' => 'Quản lý thông báo, tin nhắn và cập nhật từ cộng đồng MechaMap',
            'dashboard.messages.index' => 'Quản lý tin nhắn cá nhân và nhóm thảo luận trên MechaMap',
            'dashboard.messages.groups.index' => 'Tham gia và quản lý các nhóm thảo luận kỹ thuật trên MechaMap',
            'dashboard.messages.groups.create' => 'Tạo nhóm thảo luận mới cho cộng đồng kỹ thuật cơ khí',
            'dashboard.settings.index' => 'Cấu hình tùy chọn cá nhân, quyền riêng tư và thông báo trên MechaMap',
            'dashboard.community.threads.index' => 'Quản lý các bài viết, thảo luận kỹ thuật mà bạn đã tạo hoặc tham gia',
            'dashboard.community.bookmarks.index' => 'Quản lý các bài viết và tài liệu kỹ thuật đã lưu trên MechaMap',
            'dashboard.community.comments.index' => 'Theo dõi và quản lý các bình luận của bạn trong cộng đồng MechaMap',
            'dashboard.community.showcases.index' => 'Quản lý các dự án showcase kỹ thuật của bạn trên MechaMap',
            'dashboard.marketplace.orders.index' => 'Quản lý đơn hàng và giao dịch mua bán trên MechaMap Marketplace',
            'dashboard.marketplace.downloads.index' => 'Quản lý các file đã mua và tải xuống từ MechaMap Marketplace',
            'dashboard.marketplace.wishlist.index' => 'Quản lý danh sách sản phẩm yêu thích trên MechaMap Marketplace',
            'dashboard.marketplace.seller.dashboard' => 'Quản lý cửa hàng, sản phẩm và doanh số bán hàng trên MechaMap Marketplace',
            'dashboard.marketplace.seller.products.index' => 'Quản lý sản phẩm và dịch vụ bán trên MechaMap Marketplace',
            'dashboard.marketplace.seller.analytics.index' => 'Phân tích doanh số, khách hàng và hiệu suất bán hàng',
        ];

        return $descriptions[$routeName] ?? 'Quản lý tài khoản và hoạt động của bạn trên MechaMap';
    }

    /**
     * Get dashboard page keywords
     *
     * @param string|null $routeName
     * @return string
     */
    public static function getPageKeywords(?string $routeName = null): string
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        $keywords = [
            'dashboard' => 'dashboard, quản lý tài khoản, mechamap, kỹ thuật cơ khí',
            'dashboard.profile.edit' => 'chỉnh sửa hồ sơ, cập nhật thông tin, avatar, tài khoản',
            'dashboard.activity' => 'hoạt động, lịch sử, tương tác, theo dõi',
            'dashboard.notifications.index' => 'thông báo, tin nhắn, cập nhật, quản lý',
            'dashboard.messages.index' => 'tin nhắn, nhóm thảo luận, chat, giao tiếp',
            'dashboard.messages.groups.index' => 'nhóm thảo luận, group chat, kỹ thuật, cộng đồng',
            'dashboard.messages.groups.create' => 'tạo nhóm, group chat, thảo luận kỹ thuật',
            'dashboard.settings.index' => 'cài đặt, tùy chọn, quyền riêng tư, thông báo',
            'dashboard.community.threads.index' => 'quản lý bài viết, thảo luận kỹ thuật, forum, cộng đồng',
            'dashboard.community.bookmarks.index' => 'bookmark, lưu bài viết, tài liệu kỹ thuật, quản lý',
            'dashboard.community.comments.index' => 'quản lý bình luận, theo dõi, tương tác, cộng đồng',
            'dashboard.community.showcases.index' => 'showcase, dự án kỹ thuật, portfolio, trưng bày',
            'dashboard.marketplace.orders.index' => 'đơn hàng, giao dịch, mua bán, marketplace, thương mại',
            'dashboard.marketplace.downloads.index' => 'tải xuống, file đã mua, digital products, marketplace',
            'dashboard.marketplace.wishlist.index' => 'danh sách yêu thích, wishlist, sản phẩm, marketplace',
            'dashboard.marketplace.seller.dashboard' => 'người bán, cửa hàng, sản phẩm, doanh số, bán hàng',
            'dashboard.marketplace.seller.products.index' => 'quản lý sản phẩm, bán hàng, marketplace, thương mại',
            'dashboard.marketplace.seller.analytics.index' => 'phân tích bán hàng, doanh số, thống kê, analytics',
        ];

        return $keywords[$routeName] ?? 'dashboard, mechamap, kỹ thuật cơ khí';
    }

    /**
     * Get Open Graph image for dashboard page
     *
     * @param string|null $routeName
     * @return string
     */
    public static function getOgImage(?string $routeName = null): string
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        // Default dashboard image
        $defaultImage = '/images/brand/mechamap-dashboard-banner.jpg';
        
        $images = [
            'dashboard' => '/images/brand/mechamap-dashboard-banner.jpg',
            'dashboard.profile.edit' => '/images/brand/mechamap-profile-banner.jpg',
            'dashboard.messages.index' => '/images/brand/mechamap-messages-banner.jpg',
            'dashboard.messages.groups.index' => '/images/brand/mechamap-groups-banner.jpg',
            'dashboard.community.threads.index' => '/images/brand/mechamap-community-banner.jpg',
            'dashboard.community.bookmarks.index' => '/images/brand/mechamap-bookmarks-banner.jpg',
            'dashboard.marketplace.orders.index' => '/images/brand/mechamap-marketplace-banner.jpg',
            'dashboard.marketplace.seller.dashboard' => '/images/brand/mechamap-seller-banner.jpg',
        ];

        return $images[$routeName] ?? $defaultImage;
    }

    /**
     * Get canonical URL for dashboard page
     *
     * @param string|null $routeName
     * @return string
     */
    public static function getCanonicalUrl(?string $routeName = null): string
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        if (!$routeName) {
            return url()->current();
        }

        try {
            return route($routeName);
        } catch (\Exception $e) {
            return url()->current();
        }
    }

    /**
     * Check if dashboard page should be indexed
     *
     * @param string|null $routeName
     * @return bool
     */
    public static function shouldIndex(?string $routeName = null): bool
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        // Most dashboard pages are private and should not be indexed
        $publicPages = [
            // Add any public dashboard pages here if needed
        ];

        return in_array($routeName, $publicPages);
    }

    /**
     * Generate structured data for dashboard page
     *
     * @param string|null $routeName
     * @return array
     */
    public static function getStructuredData(?string $routeName = null): array
    {
        $routeName = $routeName ?: Route::currentRouteName();
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => self::getPageTitle($routeName),
            'description' => self::getPageDescription($routeName),
            'url' => self::getCanonicalUrl($routeName),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'MechaMap',
                'url' => url('/')
            ],
            'inLanguage' => app()->getLocale(),
            'potentialAction' => [
                '@type' => 'ReadAction',
                'target' => self::getCanonicalUrl($routeName)
            ]
        ];
    }
}
