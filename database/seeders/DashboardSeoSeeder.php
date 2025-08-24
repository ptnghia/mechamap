<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PageSeo;

class DashboardSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dashboardSeoData = [
            // Main Dashboard
            [
                'route_name' => 'dashboard',
                'title' => 'Dashboard - MechaMap',
                'description' => 'Quản lý tài khoản và hoạt động của bạn trên MechaMap - Cộng đồng kỹ thuật cơ khí Việt Nam',
                'keywords' => 'dashboard, quản lý tài khoản, mechamap, kỹ thuật cơ khí',
                'title_i18n' => json_encode([
                    'vi' => 'Dashboard - MechaMap',
                    'en' => 'Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý tài khoản và hoạt động của bạn trên MechaMap - Cộng đồng kỹ thuật cơ khí Việt Nam',
                    'en' => 'Manage your account and activities on MechaMap - Vietnamese Mechanical Engineering Community'
                ]),
                'keywords_i18n' => json_encode([
                    'vi' => 'dashboard, quản lý tài khoản, mechamap, kỹ thuật cơ khí',
                    'en' => 'dashboard, account management, mechamap, mechanical engineering'
                ]),
                'og_title' => 'Dashboard - MechaMap',
                'og_description' => 'Quản lý tài khoản và hoạt động của bạn trên MechaMap',
                'og_image' => '/images/brand/mechamap-dashboard-banner.jpg',
                'twitter_title' => 'Dashboard - MechaMap',
                'twitter_description' => 'Quản lý tài khoản và hoạt động của bạn trên MechaMap',
                'twitter_image' => '/images/brand/mechamap-dashboard-banner.jpg',
                'canonical_url' => '/dashboard',
                'no_index' => false,
                'is_active' => true,
            ],

            // Profile Management
            [
                'route_name' => 'dashboard.profile.edit',
                'title' => 'Chỉnh sửa hồ sơ - Dashboard - MechaMap',
                'description' => 'Cập nhật thông tin cá nhân, avatar và cài đặt tài khoản trên MechaMap',
                'keywords' => 'chỉnh sửa hồ sơ, cập nhật thông tin, avatar, tài khoản',
                'title_i18n' => json_encode([
                    'vi' => 'Chỉnh sửa hồ sơ - Dashboard - MechaMap',
                    'en' => 'Edit Profile - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Cập nhật thông tin cá nhân, avatar và cài đặt tài khoản trên MechaMap',
                    'en' => 'Update personal information, avatar and account settings on MechaMap'
                ]),
                'og_title' => 'Chỉnh sửa hồ sơ - MechaMap',
                'og_description' => 'Cập nhật thông tin cá nhân và cài đặt tài khoản',
                'twitter_title' => 'Chỉnh sửa hồ sơ - MechaMap',
                'twitter_description' => 'Cập nhật thông tin cá nhân và cài đặt tài khoản',
                'canonical_url' => '/dashboard/profile/edit',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Activity
            [
                'route_name' => 'dashboard.activity',
                'title' => 'Hoạt động - Dashboard - MechaMap',
                'description' => 'Theo dõi lịch sử hoạt động và tương tác của bạn trên MechaMap',
                'keywords' => 'hoạt động, lịch sử, tương tác, theo dõi',
                'title_i18n' => json_encode([
                    'vi' => 'Hoạt động - Dashboard - MechaMap',
                    'en' => 'Activity - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Theo dõi lịch sử hoạt động và tương tác của bạn trên MechaMap',
                    'en' => 'Track your activity history and interactions on MechaMap'
                ]),
                'og_title' => 'Hoạt động - MechaMap',
                'og_description' => 'Theo dõi lịch sử hoạt động và tương tác',
                'canonical_url' => '/dashboard/activity',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Notifications
            [
                'route_name' => 'dashboard.notifications.index',
                'title' => 'Thông báo - Dashboard - MechaMap',
                'description' => 'Quản lý thông báo, tin nhắn và cập nhật từ cộng đồng MechaMap',
                'keywords' => 'thông báo, tin nhắn, cập nhật, quản lý',
                'title_i18n' => json_encode([
                    'vi' => 'Thông báo - Dashboard - MechaMap',
                    'en' => 'Notifications - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý thông báo, tin nhắn và cập nhật từ cộng đồng MechaMap',
                    'en' => 'Manage notifications, messages and updates from MechaMap community'
                ]),
                'og_title' => 'Thông báo - MechaMap',
                'og_description' => 'Quản lý thông báo và tin nhắn',
                'canonical_url' => '/dashboard/notifications',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Messages
            [
                'route_name' => 'dashboard.messages.index',
                'title' => 'Tin nhắn - Dashboard - MechaMap',
                'description' => 'Quản lý tin nhắn cá nhân và nhóm thảo luận trên MechaMap',
                'keywords' => 'tin nhắn, nhóm thảo luận, chat, giao tiếp',
                'title_i18n' => json_encode([
                    'vi' => 'Tin nhắn - Dashboard - MechaMap',
                    'en' => 'Messages - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý tin nhắn cá nhân và nhóm thảo luận trên MechaMap',
                    'en' => 'Manage personal messages and group conversations on MechaMap'
                ]),
                'og_title' => 'Tin nhắn - MechaMap',
                'og_description' => 'Quản lý tin nhắn và nhóm thảo luận',
                'canonical_url' => '/dashboard/messages',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Group Conversations
            [
                'route_name' => 'dashboard.messages.groups.index',
                'title' => 'Nhóm thảo luận - Dashboard - MechaMap',
                'description' => 'Tham gia và quản lý các nhóm thảo luận kỹ thuật trên MechaMap',
                'keywords' => 'nhóm thảo luận, group chat, kỹ thuật, cộng đồng',
                'title_i18n' => json_encode([
                    'vi' => 'Nhóm thảo luận - Dashboard - MechaMap',
                    'en' => 'Group Conversations - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Tham gia và quản lý các nhóm thảo luận kỹ thuật trên MechaMap',
                    'en' => 'Join and manage technical discussion groups on MechaMap'
                ]),
                'og_title' => 'Nhóm thảo luận - MechaMap',
                'og_description' => 'Tham gia các nhóm thảo luận kỹ thuật',
                'canonical_url' => '/dashboard/messages/groups',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Settings
            [
                'route_name' => 'dashboard.settings.index',
                'title' => 'Cài đặt - Dashboard - MechaMap',
                'description' => 'Cấu hình tùy chọn cá nhân, quyền riêng tư và thông báo trên MechaMap',
                'keywords' => 'cài đặt, tùy chọn, quyền riêng tư, thông báo',
                'title_i18n' => json_encode([
                    'vi' => 'Cài đặt - Dashboard - MechaMap',
                    'en' => 'Settings - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Cấu hình tùy chọn cá nhân, quyền riêng tư và thông báo trên MechaMap',
                    'en' => 'Configure personal preferences, privacy and notifications on MechaMap'
                ]),
                'og_title' => 'Cài đặt - MechaMap',
                'og_description' => 'Cấu hình tùy chọn cá nhân và quyền riêng tư',
                'canonical_url' => '/dashboard/settings',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Community - Threads
            [
                'route_name' => 'dashboard.community.threads.index',
                'title' => 'Quản lý bài viết - Dashboard - MechaMap',
                'description' => 'Quản lý các bài viết, thảo luận kỹ thuật mà bạn đã tạo hoặc tham gia',
                'keywords' => 'quản lý bài viết, thảo luận kỹ thuật, forum, cộng đồng',
                'title_i18n' => json_encode([
                    'vi' => 'Quản lý bài viết - Dashboard - MechaMap',
                    'en' => 'Manage Threads - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý các bài viết, thảo luận kỹ thuật mà bạn đã tạo hoặc tham gia',
                    'en' => 'Manage threads and technical discussions you created or participated in'
                ]),
                'og_title' => 'Quản lý bài viết - MechaMap',
                'og_description' => 'Quản lý thảo luận kỹ thuật của bạn',
                'canonical_url' => '/dashboard/community/threads',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Community - Bookmarks
            [
                'route_name' => 'dashboard.community.bookmarks.index',
                'title' => 'Bookmark - Dashboard - MechaMap',
                'description' => 'Quản lý các bài viết và tài liệu kỹ thuật đã lưu trên MechaMap',
                'keywords' => 'bookmark, lưu bài viết, tài liệu kỹ thuật, quản lý',
                'title_i18n' => json_encode([
                    'vi' => 'Bookmark - Dashboard - MechaMap',
                    'en' => 'Bookmarks - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý các bài viết và tài liệu kỹ thuật đã lưu trên MechaMap',
                    'en' => 'Manage saved articles and technical documents on MechaMap'
                ]),
                'og_title' => 'Bookmark - MechaMap',
                'og_description' => 'Quản lý bài viết và tài liệu đã lưu',
                'canonical_url' => '/dashboard/community/bookmarks',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Community - Comments
            [
                'route_name' => 'dashboard.community.comments.index',
                'title' => 'Quản lý bình luận - Dashboard - MechaMap',
                'description' => 'Theo dõi và quản lý các bình luận của bạn trong cộng đồng MechaMap',
                'keywords' => 'quản lý bình luận, theo dõi, tương tác, cộng đồng',
                'title_i18n' => json_encode([
                    'vi' => 'Quản lý bình luận - Dashboard - MechaMap',
                    'en' => 'Manage Comments - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Theo dõi và quản lý các bình luận của bạn trong cộng đồng MechaMap',
                    'en' => 'Track and manage your comments in MechaMap community'
                ]),
                'og_title' => 'Quản lý bình luận - MechaMap',
                'og_description' => 'Theo dõi và quản lý bình luận của bạn',
                'canonical_url' => '/dashboard/community/comments',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Marketplace - Orders
            [
                'route_name' => 'dashboard.marketplace.orders.index',
                'title' => 'Đơn hàng - Dashboard - MechaMap',
                'description' => 'Quản lý đơn hàng và giao dịch mua bán trên MechaMap Marketplace',
                'keywords' => 'đơn hàng, giao dịch, mua bán, marketplace, thương mại',
                'title_i18n' => json_encode([
                    'vi' => 'Đơn hàng - Dashboard - MechaMap',
                    'en' => 'Orders - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý đơn hàng và giao dịch mua bán trên MechaMap Marketplace',
                    'en' => 'Manage orders and transactions on MechaMap Marketplace'
                ]),
                'og_title' => 'Đơn hàng - MechaMap',
                'og_description' => 'Quản lý đơn hàng và giao dịch',
                'canonical_url' => '/dashboard/marketplace/orders',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Marketplace - Downloads
            [
                'route_name' => 'dashboard.marketplace.downloads.index',
                'title' => 'Tải xuống - Dashboard - MechaMap',
                'description' => 'Quản lý các file đã mua và tải xuống từ MechaMap Marketplace',
                'keywords' => 'tải xuống, file đã mua, digital products, marketplace',
                'title_i18n' => json_encode([
                    'vi' => 'Tải xuống - Dashboard - MechaMap',
                    'en' => 'Downloads - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý các file đã mua và tải xuống từ MechaMap Marketplace',
                    'en' => 'Manage purchased files and downloads from MechaMap Marketplace'
                ]),
                'og_title' => 'Tải xuống - MechaMap',
                'og_description' => 'Quản lý file đã mua và tải xuống',
                'canonical_url' => '/dashboard/marketplace/downloads',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Marketplace - Wishlist
            [
                'route_name' => 'dashboard.marketplace.wishlist.index',
                'title' => 'Danh sách yêu thích - Dashboard - MechaMap',
                'description' => 'Quản lý danh sách sản phẩm yêu thích trên MechaMap Marketplace',
                'keywords' => 'danh sách yêu thích, wishlist, sản phẩm, marketplace',
                'title_i18n' => json_encode([
                    'vi' => 'Danh sách yêu thích - Dashboard - MechaMap',
                    'en' => 'Wishlist - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý danh sách sản phẩm yêu thích trên MechaMap Marketplace',
                    'en' => 'Manage your favorite products on MechaMap Marketplace'
                ]),
                'og_title' => 'Danh sách yêu thích - MechaMap',
                'og_description' => 'Quản lý sản phẩm yêu thích',
                'canonical_url' => '/dashboard/marketplace/wishlist',
                'no_index' => true, // Private page
                'is_active' => true,
            ],

            // Marketplace - Seller Dashboard
            [
                'route_name' => 'dashboard.marketplace.seller.dashboard',
                'title' => 'Bảng điều khiển người bán - Dashboard - MechaMap',
                'description' => 'Quản lý cửa hàng, sản phẩm và doanh số bán hàng trên MechaMap Marketplace',
                'keywords' => 'người bán, cửa hàng, sản phẩm, doanh số, bán hàng',
                'title_i18n' => json_encode([
                    'vi' => 'Bảng điều khiển người bán - Dashboard - MechaMap',
                    'en' => 'Seller Dashboard - Dashboard - MechaMap'
                ]),
                'description_i18n' => json_encode([
                    'vi' => 'Quản lý cửa hàng, sản phẩm và doanh số bán hàng trên MechaMap Marketplace',
                    'en' => 'Manage your store, products and sales on MechaMap Marketplace'
                ]),
                'og_title' => 'Bảng điều khiển người bán - MechaMap',
                'og_description' => 'Quản lý cửa hàng và bán hàng',
                'canonical_url' => '/dashboard/marketplace/seller',
                'no_index' => true, // Private page
                'is_active' => true,
            ],
        ];

        foreach ($dashboardSeoData as $seoData) {
            PageSeo::updateOrCreate(
                ['route_name' => $seoData['route_name']],
                $seoData
            );
        }

        $this->command->info('Dashboard SEO data seeded successfully!');
    }
}
