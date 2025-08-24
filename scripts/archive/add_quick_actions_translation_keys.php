<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Load Laravel configuration
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Adding Quick Actions Translation Keys for MechaMap Notifications\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Quick Actions translation keys
$translationKeys = [
    // Quick Actions
    'notifications.actions.reply' => [
        'vi' => 'Trả lời',
        'en' => 'Reply',
        'group' => 'notifications'
    ],
    'notifications.actions.view_details' => [
        'vi' => 'Xem chi tiết',
        'en' => 'View Details',
        'group' => 'notifications'
    ],
    'notifications.actions.mark_read' => [
        'vi' => 'Đánh dấu đã đọc',
        'en' => 'Mark as read',
        'group' => 'notifications'
    ],
    'notifications.actions.mark_unread' => [
        'vi' => 'Đánh dấu chưa đọc',
        'en' => 'Mark as unread',
        'group' => 'notifications'
    ],
    'notifications.actions.archive' => [
        'vi' => 'Lưu trữ',
        'en' => 'Archive',
        'group' => 'notifications'
    ],
    'notifications.actions.delete' => [
        'vi' => 'Xóa',
        'en' => 'Delete',
        'group' => 'notifications'
    ],
    
    // Reply Modal
    'notifications.reply.modal_title' => [
        'vi' => 'Trả lời thông báo',
        'en' => 'Reply to Notification',
        'group' => 'notifications'
    ],
    'notifications.reply.placeholder' => [
        'vi' => 'Nhập nội dung trả lời...',
        'en' => 'Enter your reply...',
        'group' => 'notifications'
    ],
    'notifications.reply.send_button' => [
        'vi' => 'Gửi trả lời',
        'en' => 'Send Reply',
        'group' => 'notifications'
    ],
    'notifications.reply.cancel_button' => [
        'vi' => 'Hủy bỏ',
        'en' => 'Cancel',
        'group' => 'notifications'
    ],
    'notifications.reply.success' => [
        'vi' => 'Trả lời đã được gửi thành công!',
        'en' => 'Reply sent successfully!',
        'group' => 'notifications'
    ],
    'notifications.reply.error' => [
        'vi' => 'Có lỗi xảy ra khi gửi trả lời.',
        'en' => 'Error occurred while sending reply.',
        'group' => 'notifications'
    ],
    
    // Archive Actions
    'notifications.archive.success' => [
        'vi' => 'Thông báo đã được lưu trữ.',
        'en' => 'Notification archived successfully.',
        'group' => 'notifications'
    ],
    'notifications.archive.confirm' => [
        'vi' => 'Bạn có chắc muốn lưu trữ thông báo này?',
        'en' => 'Are you sure you want to archive this notification?',
        'group' => 'notifications'
    ],
    
    // Delete Actions
    'notifications.delete.success' => [
        'vi' => 'Thông báo đã được xóa.',
        'en' => 'Notification deleted successfully.',
        'group' => 'notifications'
    ],
    'notifications.delete.confirm' => [
        'vi' => 'Bạn có chắc muốn xóa thông báo này?',
        'en' => 'Are you sure you want to delete this notification?',
        'group' => 'notifications'
    ],
    
    // Mark Read Actions
    'notifications.mark_read.success' => [
        'vi' => 'Đã đánh dấu thông báo là đã đọc.',
        'en' => 'Notification marked as read.',
        'group' => 'notifications'
    ],
    'notifications.mark_unread.success' => [
        'vi' => 'Đã đánh dấu thông báo là chưa đọc.',
        'en' => 'Notification marked as unread.',
        'group' => 'notifications'
    ],
];

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
        
    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
        $totalErrors++;
    }
    
    echo "\n";
}

echo "=" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Keys added: {$totalAdded}\n";
echo "   ⏭️ Keys skipped: {$totalSkipped}\n";
echo "   ❌ Errors: {$totalErrors}\n";
echo "   📝 Total processed: " . ($totalAdded + $totalSkipped + $totalErrors) . "\n";
echo "\n🎉 Quick Actions translation keys setup completed!\n";
