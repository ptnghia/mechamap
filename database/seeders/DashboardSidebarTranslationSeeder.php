<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardSidebarTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Bổ sung translation keys cho Dashboard Sidebar
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Dashboard Sidebar translations...');

        $translations = $this->getDashboardSidebarTranslations();

        $totalCreated = 0;
        foreach ($translations as $key => $locales) {
            foreach ($locales as $locale => $value) {
                // Kiểm tra xem đã tồn tại chưa
                $existing = Translation::where('key', $key)
                    ->where('locale', $locale)
                    ->first();

                if (!$existing) {
                    Translation::create([
                        'key' => $key,
                        'locale' => $locale,
                        'value' => $value,
                        'group' => 'ui',
                        'namespace' => '*',
                    ]);
                    $this->command->info("✅ Tạo translation: {$key} ({$locale})");
                    $totalCreated++;
                } else {
                    $this->command->info("⚠️  Đã tồn tại: {$key} ({$locale})");
                }
            }
        }

        $this->command->info("🎉 Hoàn thành! Đã tạo {$totalCreated} translation keys mới.");
    }

    /**
     * Get Dashboard Sidebar translation data
     */
    private function getDashboardSidebarTranslations(): array
    {
        return [
            // ===== USER DASHBOARD SECTION =====

            'sidebar.user_dashboard.profile' => [
                'vi' => 'Hồ sơ',
                'en' => 'Profile',
            ],

            'sidebar.user_dashboard.notifications' => [
                'vi' => 'Thông báo',
                'en' => 'Notifications',
            ],

            'sidebar.user_dashboard.messages' => [
                'vi' => 'Tin nhắn',
                'en' => 'Messages',
            ],

            'sidebar.user_dashboard.settings' => [
                'vi' => 'Cài đặt',
                'en' => 'Settings',
            ],

            'sidebar.user_dashboard.showcases' => [
                'vi' => 'Showcase của tôi',
                'en' => 'My Showcases',
            ],

            // ===== MESSAGES SECTION =====

            'sidebar.user_dashboard.all_messages' => [
                'vi' => 'Tất cả tin nhắn',
                'en' => 'All Messages',
            ],

            'sidebar.user_dashboard.group_conversations' => [
                'vi' => 'Nhóm chat',
                'en' => 'Group Conversations',
            ],

            'sidebar.user_dashboard.create_group' => [
                'vi' => 'Tạo nhóm',
                'en' => 'Create Group',
            ],

            'sidebar.user_dashboard.new_message' => [
                'vi' => 'Tin nhắn mới',
                'en' => 'New Message',
            ],

            // ===== ADDITIONAL DASHBOARD TRANSLATIONS =====

            'sidebar.user_dashboard.activity' => [
                'vi' => 'Hoạt động',
                'en' => 'Activity',
            ],

            'sidebar.user_dashboard.bookmarks' => [
                'vi' => 'Đã lưu',
                'en' => 'Bookmarks',
            ],

            'sidebar.user_dashboard.threads' => [
                'vi' => 'Bài viết của tôi',
                'en' => 'My Threads',
            ],

            'sidebar.user_dashboard.comments' => [
                'vi' => 'Bình luận',
                'en' => 'Comments',
            ],

            'sidebar.user_dashboard.following' => [
                'vi' => 'Đang theo dõi',
                'en' => 'Following',
            ],

            // ===== SECTION HEADERS =====

            'sidebar.sections.dashboard' => [
                'vi' => 'Bảng điều khiển',
                'en' => 'Dashboard',
            ],

            'sidebar.sections.community' => [
                'vi' => 'Cộng đồng',
                'en' => 'Community',
            ],

            'sidebar.sections.messages' => [
                'vi' => 'Tin nhắn',
                'en' => 'Messages',
            ],

            'sidebar.sections.quick_actions' => [
                'vi' => 'Thao tác nhanh',
                'en' => 'Quick Actions',
            ],

            // ===== QUICK ACTIONS =====

            'sidebar.quick_actions.new_thread' => [
                'vi' => 'Bài viết mới',
                'en' => 'New Thread',
            ],

            'sidebar.quick_actions.browse_marketplace' => [
                'vi' => 'Duyệt sản phẩm',
                'en' => 'Browse Products',
            ],

            'sidebar.quick_actions.create_showcase' => [
                'vi' => 'Tạo Showcase',
                'en' => 'Create Showcase',
            ],

            'sidebar.quick_actions.browse_forums' => [
                'vi' => 'Duyệt diễn đàn',
                'en' => 'Browse Forums',
            ],

            // ===== HELP & SUPPORT =====

            'sidebar.help.documentation' => [
                'vi' => 'Tài liệu',
                'en' => 'Documentation',
            ],

            'sidebar.help.contact_support' => [
                'vi' => 'Liên hệ hỗ trợ',
                'en' => 'Contact Support',
            ],

            'sidebar.help.faq' => [
                'vi' => 'FAQ',
                'en' => 'FAQ',
            ],

            'sidebar.help.help_support' => [
                'vi' => 'Trợ giúp & Hỗ trợ',
                'en' => 'Help & Support',
            ],
        ];
    }
}
