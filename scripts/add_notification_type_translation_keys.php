<?php

/**
 * Script để thêm các translation keys cho Notification Types
 * Chạy: php scripts/add_notification_type_translation_keys.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 THÊM NOTIFICATION TYPE TRANSLATION KEYS\n";
echo "==========================================\n\n";

// Các translation keys cho notification types
$translationKeys = [
    // Notification Type Names
    'notifications.types.login_from_new_device' => [
        'vi' => 'Đăng nhập từ thiết bị mới',
        'en' => 'Login from new device',
        'group' => 'notifications'
    ],
    'notifications.types.system_announcement' => [
        'vi' => 'Thông báo hệ thống',
        'en' => 'System announcement',
        'group' => 'notifications'
    ],
    'notifications.types.product_approved' => [
        'vi' => 'Sản phẩm được duyệt',
        'en' => 'Product approved',
        'group' => 'notifications'
    ],
    'notifications.types.business_verified' => [
        'vi' => 'Doanh nghiệp được xác minh',
        'en' => 'Business verified',
        'group' => 'notifications'
    ],
    'notifications.types.commission_paid' => [
        'vi' => 'Hoa hồng đã thanh toán',
        'en' => 'Commission paid',
        'group' => 'notifications'
    ],
    'notifications.types.quote_request' => [
        'vi' => 'Yêu cầu báo giá',
        'en' => 'Quote request',
        'group' => 'notifications'
    ],
    'notifications.types.role_changed' => [
        'vi' => 'Vai trò được cập nhật',
        'en' => 'Role updated',
        'group' => 'notifications'
    ],
    'notifications.types.thread_replied' => [
        'vi' => 'Phản hồi trong thread',
        'en' => 'Thread replied',
        'group' => 'notifications'
    ],
    'notifications.types.comment_mention' => [
        'vi' => 'Nhắc đến trong bình luận',
        'en' => 'Comment mention',
        'group' => 'notifications'
    ],
    'notifications.types.order_update' => [
        'vi' => 'Cập nhật đơn hàng',
        'en' => 'Order update',
        'group' => 'notifications'
    ],
    'notifications.types.achievement_unlocked' => [
        'vi' => 'Mở khóa thành tựu',
        'en' => 'Achievement unlocked',
        'group' => 'notifications'
    ],
    'notifications.types.price_drop_alert' => [
        'vi' => 'Cảnh báo giảm giá',
        'en' => 'Price drop alert',
        'group' => 'notifications'
    ],
    'notifications.types.review_received' => [
        'vi' => 'Nhận đánh giá',
        'en' => 'Review received',
        'group' => 'notifications'
    ],
    'notifications.types.thread_created' => [
        'vi' => 'Thread mới được tạo',
        'en' => 'New thread created',
        'group' => 'notifications'
    ],
    'notifications.types.seller_message' => [
        'vi' => 'Tin nhắn từ người bán',
        'en' => 'Seller message',
        'group' => 'notifications'
    ],
    'notifications.types.user_followed' => [
        'vi' => 'Người theo dõi mới',
        'en' => 'New follower',
        'group' => 'notifications'
    ],
    'notifications.types.forum_activity' => [
        'vi' => 'Hoạt động diễn đàn',
        'en' => 'Forum activity',
        'group' => 'notifications'
    ],
    'notifications.types.weekly_digest' => [
        'vi' => 'Tổng hợp tuần',
        'en' => 'Weekly digest',
        'group' => 'notifications'
    ],
    'notifications.types.marketplace_activity' => [
        'vi' => 'Hoạt động marketplace',
        'en' => 'Marketplace activity',
        'group' => 'notifications'
    ],
    'notifications.types.product_out_of_stock' => [
        'vi' => 'Sản phẩm hết hàng',
        'en' => 'Product out of stock',
        'group' => 'notifications'
    ],
    
    // Generic notification type labels
    'notifications.types.general' => [
        'vi' => 'Chung',
        'en' => 'General',
        'group' => 'notifications'
    ],
    'notifications.types.security' => [
        'vi' => 'Bảo mật',
        'en' => 'Security',
        'group' => 'notifications'
    ],
    'notifications.types.business' => [
        'vi' => 'Kinh doanh',
        'en' => 'Business',
        'group' => 'notifications'
    ],
    'notifications.types.social' => [
        'vi' => 'Xã hội',
        'en' => 'Social',
        'group' => 'notifications'
    ],
    'notifications.types.commerce' => [
        'vi' => 'Thương mại',
        'en' => 'Commerce',
        'group' => 'notifications'
    ],
    'notifications.types.technical' => [
        'vi' => 'Kỹ thuật',
        'en' => 'Technical',
        'group' => 'notifications'
    ],
    
    // Time-related translations
    'notifications.time.just_now' => [
        'vi' => 'vừa xong',
        'en' => 'just now',
        'group' => 'notifications'
    ],
    'notifications.time.minute_ago' => [
        'vi' => 'phút trước',
        'en' => 'minute ago',
        'group' => 'notifications'
    ],
    'notifications.time.minutes_ago' => [
        'vi' => 'phút trước',
        'en' => 'minutes ago',
        'group' => 'notifications'
    ],
    'notifications.time.hour_ago' => [
        'vi' => 'giờ trước',
        'en' => 'hour ago',
        'group' => 'notifications'
    ],
    'notifications.time.hours_ago' => [
        'vi' => 'giờ trước',
        'en' => 'hours ago',
        'group' => 'notifications'
    ],
    'notifications.time.day_ago' => [
        'vi' => 'ngày trước',
        'en' => 'day ago',
        'group' => 'notifications'
    ],
    'notifications.time.days_ago' => [
        'vi' => 'ngày trước',
        'en' => 'days ago',
        'group' => 'notifications'
    ],
    'notifications.time.week_ago' => [
        'vi' => 'tuần trước',
        'en' => 'week ago',
        'group' => 'notifications'
    ],
    'notifications.time.weeks_ago' => [
        'vi' => 'tuần trước',
        'en' => 'weeks ago',
        'group' => 'notifications'
    ],
    'notifications.time.month_ago' => [
        'vi' => 'tháng trước',
        'en' => 'month ago',
        'group' => 'notifications'
    ],
    'notifications.time.months_ago' => [
        'vi' => 'tháng trước',
        'en' => 'months ago',
        'group' => 'notifications'
    ],
    'notifications.time.year_ago' => [
        'vi' => 'năm trước',
        'en' => 'year ago',
        'group' => 'notifications'
    ],
    'notifications.time.years_ago' => [
        'vi' => 'năm trước',
        'en' => 'years ago',
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
echo "==========================================\n";
echo "🎯 KẾT QUẢ CUỐI CÙNG:\n";
echo "✅ Đã thêm: {$totalAdded} keys\n";
echo "⏭️ Đã bỏ qua: {$totalSkipped} keys\n";
echo "❌ Lỗi: {$totalErrors} keys\n";
echo "📊 Tổng cộng: {$totalKeys} keys\n";
echo "==========================================\n";

if ($totalErrors === 0) {
    echo "🎉 HOÀN THÀNH! Tất cả notification type translation keys đã được thêm thành công!\n";
} else {
    echo "⚠️ Hoàn thành với một số lỗi. Vui lòng kiểm tra lại.\n";
}
