<?php

/**
 * Add Notification Translation Keys to Database
 * Adds missing translation keys for notification system multilingual support
 */

$basePath = '/Applications/XAMPP/xamppfiles/htdocs/mechamap';
require_once $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🌐 ADDING NOTIFICATION TRANSLATION KEYS\n";
echo "======================================\n\n";

// Define notification translation keys
$translationKeys = [
    // Default notification translations
    'notifications.default.title' => [
        'vi' => 'Thông báo mới',
        'en' => 'New Notification',
        'group' => 'notifications'
    ],
    'notifications.default.message' => [
        'vi' => 'Bạn có một thông báo mới.',
        'en' => 'You have a new notification.',
        'group' => 'notifications'
    ],

    // Notification UI translations
    'notifications.ui.header' => [
        'vi' => 'Thông báo',
        'en' => 'Notifications',
        'group' => 'notifications'
    ],
    'notifications.ui.mark_all_read' => [
        'vi' => 'Đánh dấu tất cả là đã đọc',
        'en' => 'Mark all as read',
        'group' => 'notifications'
    ],
    'notifications.ui.clear_all' => [
        'vi' => 'Xóa tất cả',
        'en' => 'Clear all',
        'group' => 'notifications'
    ],
    'notifications.ui.loading' => [
        'vi' => 'Đang tải...',
        'en' => 'Loading...',
        'group' => 'notifications'
    ],
    'notifications.ui.loading_notifications' => [
        'vi' => 'Đang tải thông báo...',
        'en' => 'Loading notifications...',
        'group' => 'notifications'
    ],
    'notifications.ui.no_notifications' => [
        'vi' => 'Không có thông báo nào',
        'en' => 'No notifications',
        'group' => 'notifications'
    ],
    'notifications.ui.view_all' => [
        'vi' => 'Xem tất cả thông báo',
        'en' => 'View all notifications',
        'group' => 'notifications'
    ],
    'notifications.ui.new_badge' => [
        'vi' => 'Mới',
        'en' => 'New',
        'group' => 'notifications'
    ],
    'notifications.ui.delete_notification' => [
        'vi' => 'Xóa thông báo',
        'en' => 'Delete notification',
        'group' => 'notifications'
    ],
    'notifications.ui.unread_notifications' => [
        'vi' => 'thông báo chưa đọc',
        'en' => 'unread notifications',
        'group' => 'notifications'
    ],

    // Notification actions and messages
    'notifications.actions.marked_all_read' => [
        'vi' => 'Đã đánh dấu tất cả thông báo là đã đọc',
        'en' => 'All notifications marked as read',
        'group' => 'notifications'
    ],
    'notifications.actions.notification_deleted' => [
        'vi' => 'Đã xóa thông báo',
        'en' => 'Notification deleted',
        'group' => 'notifications'
    ],
    'notifications.actions.error_occurred' => [
        'vi' => 'Có lỗi xảy ra',
        'en' => 'An error occurred',
        'group' => 'notifications'
    ],
    'notifications.actions.error_loading' => [
        'vi' => 'Không thể tải thông báo',
        'en' => 'Unable to load notifications',
        'group' => 'notifications'
    ],
    'notifications.actions.error_deleting' => [
        'vi' => 'Có lỗi xảy ra khi xóa thông báo',
        'en' => 'Error occurred while deleting notification',
        'group' => 'notifications'
    ],

    // Notification types
    'notifications.types.comment' => [
        'vi' => 'Bình luận mới',
        'en' => 'New comment',
        'group' => 'notifications'
    ],
    'notifications.types.reply' => [
        'vi' => 'Phản hồi mới',
        'en' => 'New reply',
        'group' => 'notifications'
    ],
    'notifications.types.mention' => [
        'vi' => 'Nhắc đến',
        'en' => 'Mention',
        'group' => 'notifications'
    ],
    'notifications.types.follow' => [
        'vi' => 'Người theo dõi mới',
        'en' => 'New follower',
        'group' => 'notifications'
    ],
    'notifications.types.like' => [
        'vi' => 'Lượt thích mới',
        'en' => 'New like',
        'group' => 'notifications'
    ],
    'notifications.types.system' => [
        'vi' => 'Thông báo hệ thống',
        'en' => 'System notification',
        'group' => 'notifications'
    ],

    // Auth-related notifications
    'notifications.auth.login_to_view' => [
        'vi' => 'Đăng nhập để xem thông báo',
        'en' => 'Login to view notifications',
        'group' => 'notifications'
    ],

    // Enhanced notification features
    'notifications.enhanced.sound_toggle' => [
        'vi' => 'Bật/tắt âm thanh thông báo',
        'en' => 'Toggle notification sound',
        'group' => 'notifications'
    ],
    'notifications.enhanced.settings' => [
        'vi' => 'Cài đặt thông báo',
        'en' => 'Notification settings',
        'group' => 'notifications'
    ],
];

$totalAdded = 0;
$totalSkipped = 0;

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

    // Check if key already exists
    $existingVi = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'vi')
        ->first();

    $existingEn = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'en')
        ->first();

    if ($existingVi && $existingEn) {
        echo "   ⏭️ Skipped: Key already exists\n";
        $totalSkipped++;
        continue;
    }

    // Add Vietnamese translation
    if (!$existingVi) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $data['vi'],
            'locale' => 'vi',
            'group_name' => $data['group'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added VI: {$data['vi']}\n";
    }

    // Add English translation
    if (!$existingEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $data['en'],
            'locale' => 'en',
            'group_name' => $data['group'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added EN: {$data['en']}\n";
    }

    $totalAdded++;
}

echo "\n🎉 SUMMARY:\n";
echo "===========\n";
echo "✅ Keys processed: " . count($translationKeys) . "\n";
echo "✅ Keys added: {$totalAdded}\n";
echo "⏭️ Keys skipped: {$totalSkipped}\n";

echo "\n🔄 CLEARING TRANSLATION CACHE...\n";
echo "=================================\n";

// Clear translation cache
try {
    Artisan::call('cache:clear');
    echo "✅ Cache cleared successfully\n";
} catch (Exception $e) {
    echo "⚠️ Warning: Could not clear cache: " . $e->getMessage() . "\n";
}

echo "\n🚀 Notification translation keys have been added successfully!\n";
echo "The notification system now supports both Vietnamese and English.\n";
echo "\n📍 Next steps:\n";
echo "1. Update JavaScript to load translations from server\n";
echo "2. Test notification dropdown with both languages\n";
echo "3. Verify translation keys are working properly\n";
