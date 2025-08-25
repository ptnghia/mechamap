<?php

/**
 * Script to add missing notification translation keys found in notification dropdown
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Translation;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔔 Adding missing notification translation keys...\n";
echo "=" . str_repeat("=", 60) . "\n";

$translations = [
    // Notification UI
    [
        'group' => 'notifications',
        'key' => 'ui.header',
        'vi' => 'Thông báo',
        'en' => 'Notifications'
    ],
    [
        'group' => 'notifications',
        'key' => 'ui.manage',
        'vi' => 'Quản lý',
        'en' => 'Manage'
    ],

    // Notification Types
    [
        'group' => 'notifications',
        'key' => 'types.seller_message',
        'vi' => 'Tin nhắn từ người bán',
        'en' => 'Seller Message'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.thread_created',
        'vi' => 'Chủ đề mới',
        'en' => 'New Thread'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.thread_replied',
        'vi' => 'Trả lời chủ đề',
        'en' => 'Thread Reply'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.comment_mention',
        'vi' => 'Được nhắc đến',
        'en' => 'Mentioned in Comment'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.forum_activity',
        'vi' => 'Hoạt động diễn đàn',
        'en' => 'Forum Activity'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.product_approved',
        'vi' => 'Sản phẩm được duyệt',
        'en' => 'Product Approved'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.order_update',
        'vi' => 'Cập nhật đơn hàng',
        'en' => 'Order Update'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.order_status_changed',
        'vi' => 'Trạng thái đơn hàng thay đổi',
        'en' => 'Order Status Changed'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.price_drop_alert',
        'vi' => 'Cảnh báo giảm giá',
        'en' => 'Price Drop Alert'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.product_out_of_stock',
        'vi' => 'Sản phẩm hết hàng',
        'en' => 'Product Out of Stock'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.review_received',
        'vi' => 'Nhận được đánh giá',
        'en' => 'Review Received'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.wishlist_available',
        'vi' => 'Sản phẩm yêu thích có sẵn',
        'en' => 'Wishlist Item Available'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.marketplace_activity',
        'vi' => 'Hoạt động thị trường',
        'en' => 'Marketplace Activity'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.commission_paid',
        'vi' => 'Hoa hồng đã thanh toán',
        'en' => 'Commission Paid'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.login_from_new_device',
        'vi' => 'Đăng nhập từ thiết bị mới',
        'en' => 'Login from New Device'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.password_changed',
        'vi' => 'Mật khẩu đã thay đổi',
        'en' => 'Password Changed'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.security_alert',
        'vi' => 'Cảnh báo bảo mật',
        'en' => 'Security Alert'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.user_followed',
        'vi' => 'Người dùng theo dõi',
        'en' => 'User Followed'
    ],
    [
        'group' => 'notifications',
        'key' => 'types.user_registered',
        'vi' => 'Người dùng đăng ký',
        'en' => 'User Registered'
    ]
];

$created = 0;
$updated = 0;
$skipped = 0;

foreach ($translations as $translation) {
    echo "\n📝 Processing: {$translation['group']}.{$translation['key']}\n";

    try {
        // Create full key (group.key format)
        $fullKey = $translation['group'] . '.' . $translation['key'];

        // Check Vietnamese translation
        $viTranslation = Translation::where('key', $fullKey)
            ->where('locale', 'vi')
            ->first();

        if (!$viTranslation) {
            Translation::create([
                'group_name' => $translation['group'],
                'key' => $fullKey,
                'content' => $translation['vi'],
                'locale' => 'vi',
                'is_active' => true
            ]);
            $created++;
            echo "   ✅ Created VI: {$translation['vi']}\n";
        } else {
            $skipped++;
            echo "   ⏭️ Skipped VI: already exists\n";
        }

        // Check English translation
        $enTranslation = Translation::where('key', $fullKey)
            ->where('locale', 'en')
            ->first();

        if (!$enTranslation) {
            Translation::create([
                'group_name' => $translation['group'],
                'key' => $fullKey,
                'content' => $translation['en'],
                'locale' => 'en',
                'is_active' => true
            ]);
            $created++;
            echo "   ✅ Created EN: {$translation['en']}\n";
        } else {
            $skipped++;
            echo "   ⏭️ Skipped EN: already exists\n";
        }

    } catch (\Exception $e) {
        echo "   ❌ Error processing {$translation['group']}.{$translation['key']}: {$e->getMessage()}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Created: {$created}\n";
echo "   🔄 Updated: {$updated}\n";
echo "   ⏭️ Skipped: {$skipped}\n";
echo "   📝 Total processed: " . (($created + $updated + $skipped) / 2) . " translation keys\n";
echo "\n🎉 Notification translation keys setup completed!\n";
echo "📋 Next steps:\n";
echo "   1. Refresh the notification dropdown to see translations\n";
echo "   2. Test all notification types display correctly\n";
echo "   3. Verify both Vietnamese and English translations work\n";
