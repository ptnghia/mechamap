<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardPageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data cho các Dashboard routes quan trọng
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Dashboard SEO data...');

        $seoData = $this->getDashboardSeoData();

        $totalCreated = 0;
        foreach ($seoData as $data) {
            // Kiểm tra xem đã tồn tại chưa
            $existing = PageSeo::where('route_name', $data['route_name'])->first();
            if (!$existing) {
                PageSeo::create($data);
                $this->command->info("✅ Tạo SEO cho: {$data['route_name']}");
                $totalCreated++;
            } else {
                $this->command->info("⚠️  Đã tồn tại: {$data['route_name']}");
            }
        }

        $this->command->info("🎉 Hoàn thành! Đã tạo {$totalCreated} Dashboard SEO records.");

        // Hiển thị thống kê tổng quan
        $total = PageSeo::where('is_active', true)->count();
        $this->command->info("📊 Tổng số SEO records hiện tại: {$total}");
    }

    /**
     * Get SEO data for Dashboard routes
     */
    private function getDashboardSeoData(): array
    {
        return [
            // ===== MAIN DASHBOARD =====

            [
                'route_name' => 'dashboard.index',
                'url_pattern' => '/dashboard',
                'title' => 'Dashboard - Bảng điều khiển | MechaMap',
                'title_i18n' => [
                    'vi' => 'Dashboard - Bảng điều khiển | MechaMap',
                    'en' => 'Dashboard - Control Panel | MechaMap'
                ],
                'description' => 'Bảng điều khiển cá nhân MechaMap. Quản lý hoạt động, thông báo, tin nhắn và tương tác trong cộng đồng kỹ sư cơ khí.',
                'description_i18n' => [
                    'vi' => 'Bảng điều khiển cá nhân MechaMap. Quản lý hoạt động, thông báo, tin nhắn và tương tác trong cộng đồng kỹ sư cơ khí.',
                    'en' => 'Personal MechaMap dashboard. Manage activities, notifications, messages and interactions in the mechanical engineering community.'
                ],
                'keywords' => 'dashboard mechamap, bảng điều khiển, quản lý hoạt động, thông báo tin nhắn',
                'focus_keyword' => 'dashboard mechamap',
                'canonical_url' => '/dashboard',
                'breadcrumb_title' => 'Dashboard',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => false, // Private area
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'daily',
                'no_index' => true, // Private dashboard
                'is_active' => true,
            ],

            // ===== PROFILE MANAGEMENT =====

            [
                'route_name' => 'dashboard.profile.edit',
                'url_pattern' => '/dashboard/profile/edit',
                'title' => 'Chỉnh sửa Hồ sơ - Edit Profile | MechaMap',
                'title_i18n' => [
                    'vi' => 'Chỉnh sửa Hồ sơ - Edit Profile | MechaMap',
                    'en' => 'Edit Profile - Update Information | MechaMap'
                ],
                'description' => 'Cập nhật thông tin hồ sơ kỹ sư của bạn. Chỉnh sửa kinh nghiệm, kỹ năng, dự án và thông tin liên hệ chuyên nghiệp.',
                'keywords' => 'chỉnh sửa hồ sơ, edit profile, cập nhật thông tin, kinh nghiệm kỹ năng',
                'focus_keyword' => 'chỉnh sửa hồ sơ',
                'canonical_url' => '/dashboard/profile/edit',
                'breadcrumb_title' => 'Chỉnh sửa hồ sơ',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => false,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'monthly',
                'no_index' => true,
                'is_active' => true,
            ],

            [
                'route_name' => 'dashboard.profile.show',
                'url_pattern' => '/dashboard/profile',
                'title' => 'Hồ sơ của tôi - My Profile | MechaMap',
                'title_i18n' => [
                    'vi' => 'Hồ sơ của tôi - My Profile | MechaMap',
                    'en' => 'My Profile - Personal Information | MechaMap'
                ],
                'description' => 'Xem và quản lý hồ sơ cá nhân trong cộng đồng MechaMap. Theo dõi hoạt động, đóng góp và thành tích của bạn.',
                'keywords' => 'hồ sơ cá nhân, my profile, hoạt động đóng góp, thành tích kỹ sư',
                'focus_keyword' => 'hồ sơ cá nhân',
                'canonical_url' => '/dashboard/profile',
                'breadcrumb_title' => 'Hồ sơ',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => false,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'weekly',
                'no_index' => true,
                'is_active' => true,
            ],

            // ===== NOTIFICATIONS & MESSAGES =====

            [
                'route_name' => 'dashboard.notifications.index',
                'url_pattern' => '/dashboard/notifications',
                'title' => 'Thông báo - Notifications | MechaMap',
                'title_i18n' => [
                    'vi' => 'Thông báo - Notifications | MechaMap',
                    'en' => 'Notifications - Updates | MechaMap'
                ],
                'description' => 'Quản lý thông báo từ cộng đồng MechaMap. Cập nhật về thảo luận, tin nhắn, marketplace và hoạt động quan trọng.',
                'keywords' => 'thông báo mechamap, notifications, cập nhật cộng đồng, tin nhắn mới',
                'focus_keyword' => 'thông báo mechamap',
                'canonical_url' => '/dashboard/notifications',
                'breadcrumb_title' => 'Thông báo',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => false,
                'sitemap_priority' => 0.2,
                'sitemap_changefreq' => 'daily',
                'no_index' => true,
                'is_active' => true,
            ],

            [
                'route_name' => 'dashboard.messages.index',
                'url_pattern' => '/dashboard/messages',
                'title' => 'Tin nhắn - Messages | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tin nhắn - Messages | MechaMap',
                    'en' => 'Messages - Private Chat | MechaMap'
                ],
                'description' => 'Quản lý tin nhắn riêng tư với các thành viên cộng đồng. Trao đổi kinh nghiệm kỹ thuật và kết nối chuyên nghiệp.',
                'keywords' => 'tin nhắn riêng tư, private messages, trao đổi kỹ thuật, kết nối chuyên nghiệp',
                'focus_keyword' => 'tin nhắn riêng tư',
                'canonical_url' => '/dashboard/messages',
                'breadcrumb_title' => 'Tin nhắn',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => false,
                'sitemap_priority' => 0.2,
                'sitemap_changefreq' => 'daily',
                'no_index' => true,
                'is_active' => true,
            ],

            // ===== MARKETPLACE DASHBOARD =====

            [
                'route_name' => 'dashboard.marketplace.index',
                'url_pattern' => '/dashboard/marketplace',
                'title' => 'Marketplace Dashboard - Quản lý Bán hàng | MechaMap',
                'title_i18n' => [
                    'vi' => 'Marketplace Dashboard - Quản lý Bán hàng | MechaMap',
                    'en' => 'Marketplace Dashboard - Sales Management | MechaMap'
                ],
                'description' => 'Quản lý hoạt động bán hàng trên MechaMap Marketplace. Theo dõi sản phẩm, đơn hàng và doanh thu từ thiết bị cơ khí.',
                'keywords' => 'marketplace dashboard, quản lý bán hàng, theo dõi đơn hàng, doanh thu thiết bị',
                'focus_keyword' => 'marketplace dashboard',
                'canonical_url' => '/dashboard/marketplace',
                'breadcrumb_title' => 'Marketplace',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => false,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'weekly',
                'no_index' => true,
                'is_active' => true,
            ],

            [
                'route_name' => 'dashboard.marketplace.products.index',
                'url_pattern' => '/dashboard/marketplace/products',
                'title' => 'Quản lý Sản phẩm - Product Management | MechaMap',
                'title_i18n' => [
                    'vi' => 'Quản lý Sản phẩm - Product Management | MechaMap',
                    'en' => 'Product Management - Manage Listings | MechaMap'
                ],
                'description' => 'Quản lý danh sách sản phẩm thiết bị cơ khí của bạn. Thêm, chỉnh sửa, cập nhật giá và theo dõi hiệu suất bán hàng.',
                'keywords' => 'quản lý sản phẩm, product management, danh sách thiết bị, cập nhật giá bán',
                'focus_keyword' => 'quản lý sản phẩm',
                'canonical_url' => '/dashboard/marketplace/products',
                'breadcrumb_title' => 'Sản phẩm',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => false,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'weekly',
                'no_index' => true,
                'is_active' => true,
            ],

            // ===== COMMUNITY FEATURES =====

            [
                'route_name' => 'dashboard.threads.index',
                'url_pattern' => '/dashboard/threads',
                'title' => 'Chủ đề của tôi - My Threads | MechaMap',
                'title_i18n' => [
                    'vi' => 'Chủ đề của tôi - My Threads | MechaMap',
                    'en' => 'My Threads - Discussion Topics | MechaMap'
                ],
                'description' => 'Quản lý các chủ đề thảo luận bạn đã tạo. Theo dõi phản hồi, cập nhật nội dung và tương tác với cộng đồng.',
                'keywords' => 'chủ đề của tôi, my threads, quản lý thảo luận, theo dõi phản hồi',
                'focus_keyword' => 'chủ đề của tôi',
                'canonical_url' => '/dashboard/threads',
                'breadcrumb_title' => 'Chủ đề',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => false,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'weekly',
                'no_index' => true,
                'is_active' => true,
            ],

            [
                'route_name' => 'dashboard.bookmarks.index',
                'url_pattern' => '/dashboard/bookmarks',
                'title' => 'Đã lưu - Bookmarks | MechaMap',
                'title_i18n' => [
                    'vi' => 'Đã lưu - Bookmarks | MechaMap',
                    'en' => 'Bookmarks - Saved Content | MechaMap'
                ],
                'description' => 'Quản lý nội dung đã lưu từ cộng đồng MechaMap. Thảo luận, sản phẩm và tài liệu kỹ thuật quan trọng.',
                'keywords' => 'nội dung đã lưu, bookmarks, thảo luận quan trọng, tài liệu kỹ thuật',
                'focus_keyword' => 'nội dung đã lưu',
                'canonical_url' => '/dashboard/bookmarks',
                'breadcrumb_title' => 'Đã lưu',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => false,
                'sitemap_priority' => 0.2,
                'sitemap_changefreq' => 'weekly',
                'no_index' => true,
                'is_active' => true,
            ],

            // ===== SETTINGS =====

            [
                'route_name' => 'dashboard.settings.index',
                'url_pattern' => '/dashboard/settings',
                'title' => 'Cài đặt - Settings | MechaMap',
                'title_i18n' => [
                    'vi' => 'Cài đặt - Settings | MechaMap',
                    'en' => 'Settings - Account Preferences | MechaMap'
                ],
                'description' => 'Cài đặt tài khoản và tùy chỉnh trải nghiệm MechaMap. Quyền riêng tư, thông báo, ngôn ngữ và tùy chọn hiển thị.',
                'keywords' => 'cài đặt tài khoản, settings, quyền riêng tư, tùy chỉnh giao diện',
                'focus_keyword' => 'cài đặt tài khoản',
                'canonical_url' => '/dashboard/settings',
                'breadcrumb_title' => 'Cài đặt',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => false,
                'sitemap_priority' => 0.2,
                'sitemap_changefreq' => 'monthly',
                'no_index' => true,
                'is_active' => true,
            ],
        ];
    }
}
