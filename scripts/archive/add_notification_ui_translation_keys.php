<?php

/**
 * Script để thêm các translation keys cho Notification UI
 * Chạy: php scripts/add_notification_ui_translation_keys.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 THÊM NOTIFICATION UI TRANSLATION KEYS\n";
echo "=====================================\n\n";

// Các translation keys cần thêm cho notification UI
$translationKeys = [
    // UI Components
    'notifications.ui.time.minutes_ago' => [
        'vi' => 'phút trước',
        'en' => 'minutes ago',
        'group' => 'notifications'
    ],
    'notifications.ui.time.hours_ago' => [
        'vi' => 'giờ trước', 
        'en' => 'hours ago',
        'group' => 'notifications'
    ],
    'notifications.ui.time.days_ago' => [
        'vi' => 'ngày trước',
        'en' => 'days ago', 
        'group' => 'notifications'
    ],
    'notifications.ui.time.weeks_ago' => [
        'vi' => 'tuần trước',
        'en' => 'weeks ago',
        'group' => 'notifications'
    ],
    'notifications.ui.time.months_ago' => [
        'vi' => 'tháng trước',
        'en' => 'months ago',
        'group' => 'notifications'
    ],
    'notifications.ui.time.years_ago' => [
        'vi' => 'năm trước',
        'en' => 'years ago',
        'group' => 'notifications'
    ],
    'notifications.ui.time.just_now' => [
        'vi' => 'vừa xong',
        'en' => 'just now',
        'group' => 'notifications'
    ],
    
    // Status & Actions
    'notifications.ui.mark_as_read' => [
        'vi' => 'Đánh dấu đã đọc',
        'en' => 'Mark as read',
        'group' => 'notifications'
    ],
    'notifications.ui.mark_as_unread' => [
        'vi' => 'Đánh dấu chưa đọc',
        'en' => 'Mark as unread',
        'group' => 'notifications'
    ],
    'notifications.ui.read' => [
        'vi' => 'Đã đọc',
        'en' => 'Read',
        'group' => 'notifications'
    ],
    'notifications.ui.unread' => [
        'vi' => 'Chưa đọc',
        'en' => 'Unread',
        'group' => 'notifications'
    ],
    
    // Notification Types
    'notifications.ui.type.system' => [
        'vi' => 'Hệ thống',
        'en' => 'System',
        'group' => 'notifications'
    ],
    'notifications.ui.type.user' => [
        'vi' => 'Người dùng',
        'en' => 'User',
        'group' => 'notifications'
    ],
    'notifications.ui.type.forum' => [
        'vi' => 'Diễn đàn',
        'en' => 'Forum',
        'group' => 'notifications'
    ],
    'notifications.ui.type.marketplace' => [
        'vi' => 'Thị trường',
        'en' => 'Marketplace',
        'group' => 'notifications'
    ],
    'notifications.ui.type.message' => [
        'vi' => 'Tin nhắn',
        'en' => 'Message',
        'group' => 'notifications'
    ],
    'notifications.ui.type.follow' => [
        'vi' => 'Theo dõi',
        'en' => 'Follow',
        'group' => 'notifications'
    ],
    'notifications.ui.type.like' => [
        'vi' => 'Thích',
        'en' => 'Like',
        'group' => 'notifications'
    ],
    'notifications.ui.type.comment' => [
        'vi' => 'Bình luận',
        'en' => 'Comment',
        'group' => 'notifications'
    ],
    'notifications.ui.type.mention' => [
        'vi' => 'Nhắc đến',
        'en' => 'Mention',
        'group' => 'notifications'
    ],
    
    // Error & Empty States
    'notifications.ui.error_loading' => [
        'vi' => 'Lỗi khi tải thông báo',
        'en' => 'Error loading notifications',
        'group' => 'notifications'
    ],
    'notifications.ui.retry' => [
        'vi' => 'Thử lại',
        'en' => 'Retry',
        'group' => 'notifications'
    ],
    'notifications.ui.empty_state' => [
        'vi' => 'Bạn không có thông báo nào',
        'en' => 'You have no notifications',
        'group' => 'notifications'
    ],
    
    // Actions
    'notifications.ui.refresh' => [
        'vi' => 'Làm mới',
        'en' => 'Refresh',
        'group' => 'notifications'
    ],
    'notifications.ui.settings' => [
        'vi' => 'Cài đặt thông báo',
        'en' => 'Notification settings',
        'group' => 'notifications'
    ],
    
    // Confirmation Messages
    'notifications.ui.confirm_mark_all_read' => [
        'vi' => 'Bạn có chắc muốn đánh dấu tất cả thông báo là đã đọc?',
        'en' => 'Are you sure you want to mark all notifications as read?',
        'group' => 'notifications'
    ],
    'notifications.ui.confirm_clear_all' => [
        'vi' => 'Bạn có chắc muốn xóa tất cả thông báo?',
        'en' => 'Are you sure you want to clear all notifications?',
        'group' => 'notifications'
    ],
    'notifications.ui.confirm_delete' => [
        'vi' => 'Bạn có chắc muốn xóa thông báo này?',
        'en' => 'Are you sure you want to delete this notification?',
        'group' => 'notifications'
    ],
    
    // Success Messages
    'notifications.ui.marked_as_read' => [
        'vi' => 'Đã đánh dấu là đã đọc',
        'en' => 'Marked as read',
        'group' => 'notifications'
    ],
    'notifications.ui.all_marked_as_read' => [
        'vi' => 'Tất cả thông báo đã được đánh dấu là đã đọc',
        'en' => 'All notifications marked as read',
        'group' => 'notifications'
    ],
    'notifications.ui.notification_deleted' => [
        'vi' => 'Thông báo đã được xóa',
        'en' => 'Notification deleted',
        'group' => 'notifications'
    ],
    'notifications.ui.all_notifications_cleared' => [
        'vi' => 'Tất cả thông báo đã được xóa',
        'en' => 'All notifications cleared',
        'group' => 'notifications'
    ]
];

// Thống kê
$totalKeys = count($translationKeys);
$totalAdded = 0;
$totalSkipped = 0;
$totalErrors = 0;

echo "📊 Tổng số keys cần xử lý: {$totalKeys}\n\n";

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

    try {
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
        echo "   🎉 Success!\n";

    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        $totalErrors++;
    }

    echo "\n";
}

// Kết quả cuối cùng
echo "=====================================\n";
echo "🎯 KẾT QUẢ CUỐI CÙNG:\n";
echo "✅ Đã thêm: {$totalAdded} keys\n";
echo "⏭️ Đã bỏ qua: {$totalSkipped} keys\n";
echo "❌ Lỗi: {$totalErrors} keys\n";
echo "📊 Tổng cộng: {$totalKeys} keys\n";
echo "=====================================\n";

if ($totalErrors === 0) {
    echo "🎉 HOÀN THÀNH! Tất cả translation keys đã được thêm thành công!\n";
} else {
    echo "⚠️ Hoàn thành với một số lỗi. Vui lòng kiểm tra lại.\n";
}
