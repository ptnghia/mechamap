<?php

/**
 * Add User Dropdown Translation Keys
 * Thêm translation keys cho user dropdown menu mới
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Translation;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Adding User Dropdown Translation Keys...\n";
echo "=" . str_repeat("=", 50) . "\n";

// Translation keys cho user dropdown
$translations = [
    // Dashboard main sections
    [
        'group' => 'ui',
        'key' => 'dashboard.main',
        'vi' => 'Bảng điều khiển',
        'en' => 'Dashboard'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.main_desc',
        'vi' => 'Tổng quan hoạt động và thống kê',
        'en' => 'Overview of activities and statistics'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.profile',
        'vi' => 'Hồ sơ cá nhân',
        'en' => 'Profile'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.profile_desc',
        'vi' => 'Quản lý thông tin cá nhân',
        'en' => 'Manage personal information'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.activity',
        'vi' => 'Hoạt động',
        'en' => 'Activity'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.activity_desc',
        'vi' => 'Theo dõi hoạt động của bạn',
        'en' => 'Track your activities'
    ],

    // Community section
    [
        'group' => 'ui',
        'key' => 'dashboard.community_section',
        'vi' => 'Cộng đồng',
        'en' => 'Community'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.my_threads',
        'vi' => 'Chủ đề của tôi',
        'en' => 'My Threads'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.bookmarks',
        'vi' => 'Đánh dấu',
        'en' => 'Bookmarks'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.comments',
        'vi' => 'Bình luận',
        'en' => 'Comments'
    ],

    // Communication section
    [
        'group' => 'ui',
        'key' => 'dashboard.communication_section',
        'vi' => 'Liên lạc',
        'en' => 'Communication'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.messages',
        'vi' => 'Tin nhắn',
        'en' => 'Messages'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.notifications',
        'vi' => 'Thông báo',
        'en' => 'Notifications'
    ],

    // Marketplace section
    [
        'group' => 'ui',
        'key' => 'dashboard.marketplace_section',
        'vi' => 'Thị trường',
        'en' => 'Marketplace'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.orders',
        'vi' => 'Đơn hàng',
        'en' => 'Orders'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.wishlist',
        'vi' => 'Danh sách yêu thích',
        'en' => 'Wishlist'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.seller',
        'vi' => 'Người bán',
        'en' => 'Seller'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.seller_desc',
        'vi' => 'Quản lý cửa hàng và sản phẩm',
        'en' => 'Manage store and products'
    ],

    // Admin section
    [
        'group' => 'ui',
        'key' => 'dashboard.admin_section',
        'vi' => 'Quản trị',
        'en' => 'Administration'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.admin',
        'vi' => 'Quản trị viên',
        'en' => 'Admin Panel'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.admin_desc',
        'vi' => 'Bảng điều khiển quản trị',
        'en' => 'Administration dashboard'
    ],

    // Settings
    [
        'group' => 'ui',
        'key' => 'dashboard.settings',
        'vi' => 'Cài đặt',
        'en' => 'Settings'
    ],
    [
        'group' => 'ui',
        'key' => 'dashboard.settings_desc',
        'vi' => 'Tùy chỉnh tài khoản và ứng dụng',
        'en' => 'Customize account and application'
    ]
];

$created = 0;
$skipped = 0;

foreach ($translations as $translation) {
    echo "\n📝 Processing: {$translation['group']}.{$translation['key']}\n";
    
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
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Created: {$created} translation entries\n";
echo "   ⏭️ Skipped: {$skipped} existing entries\n";

// Test some keys
echo "\n🧪 TESTING TRANSLATION KEYS:\n";
echo "=" . str_repeat("=", 30) . "\n";

$testKeys = [
    'ui.dashboard.main',
    'ui.dashboard.community_section',
    'ui.dashboard.messages',
    'ui.dashboard.admin'
];

foreach ($testKeys as $key) {
    $viResult = Translation::where('key', $key)->where('locale', 'vi')->first();
    $enResult = Translation::where('key', $key)->where('locale', 'en')->first();
    
    echo "\n🔍 Key: {$key}\n";
    echo "   VI: " . ($viResult ? $viResult->content : 'NOT FOUND') . "\n";
    echo "   EN: " . ($enResult ? $enResult->content : 'NOT FOUND') . "\n";
}

echo "\n🎉 User dropdown translation keys added successfully!\n";
echo "📋 Next steps:\n";
echo "   1. Clear cache: php artisan cache:clear\n";
echo "   2. Update menu components to use new dropdown\n";
echo "   3. Test dropdown functionality\n";
