<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 THÊM NOTIFICATION CATEGORY TRANSLATION KEYS\n";
echo "==============================================\n\n";

// Add category translation keys
$categories = [
    'notifications.categories.forum' => ['vi' => 'Thông báo Forum', 'en' => 'Forum Notifications'],
    'notifications.categories.security' => ['vi' => 'Thông báo Bảo mật', 'en' => 'Security Notifications'],
    'notifications.categories.marketplace' => ['vi' => 'Thông báo Marketplace', 'en' => 'Marketplace Notifications'],
    'notifications.categories.social' => ['vi' => 'Thông báo Xã hội', 'en' => 'Social Notifications'],
    'notifications.categories.business' => ['vi' => 'Thông báo Kinh doanh', 'en' => 'Business Notifications'],
    'notifications.categories.system' => ['vi' => 'Thông báo Hệ thống', 'en' => 'System Notifications']
];

$totalAdded = 0;

foreach ($categories as $key => $translations) {
    echo "📝 Processing: $key\n";
    
    // Check if exists
    $existsVi = DB::table('translations')->where('key', $key)->where('locale', 'vi')->exists();
    $existsEn = DB::table('translations')->where('key', $key)->where('locale', 'en')->exists();
    
    if (!$existsVi) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translations['vi'],
            'locale' => 'vi',
            'group_name' => 'notifications',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added VI: {$translations['vi']}\n";
        $totalAdded++;
    }
    
    if (!$existsEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translations['en'],
            'locale' => 'en',
            'group_name' => 'notifications',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added EN: {$translations['en']}\n";
        $totalAdded++;
    }
    
    if ($existsVi && $existsEn) {
        echo "   ⏭️ Skipped: Already exists\n";
    }
    
    echo "\n";
}

echo "==============================================\n";
echo "🎯 KẾT QUẢ: Đã thêm $totalAdded translation keys\n";
echo "✅ Category translation keys added successfully!\n";
