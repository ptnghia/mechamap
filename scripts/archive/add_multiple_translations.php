<?php

/**
 * Script để thêm nhiều translation keys vào database cùng lúc
 * Tự động bỏ qua nếu key đã tồn tại
 * 
 * Usage: php scripts/add_multiple_translations.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Script thêm nhiều translation keys\n";
echo "=====================================\n\n";

// Danh sách translation keys cần thêm
$translations = [
    [
        'key' => 'ui.users.online',
        'group_name' => 'ui',
        'vi' => 'Trực tuyến',
        'en' => 'Online'
    ],
    [
        'key' => 'ui.users.offline',
        'group_name' => 'ui', 
        'vi' => 'Ngoại tuyến',
        'en' => 'Offline'
    ],
    [
        'key' => 'ui.users.posts',
        'group_name' => 'ui',
        'vi' => 'bài viết',
        'en' => 'posts'
    ],
    [
        'key' => 'ui.users.threads',
        'group_name' => 'ui',
        'vi' => 'chủ đề',
        'en' => 'threads'
    ],
    [
        'key' => 'ui.users.followers',
        'group_name' => 'ui',
        'vi' => 'theo dõi',
        'en' => 'followers'
    ],
    [
        'key' => 'ui.users.follow',
        'group_name' => 'ui',
        'vi' => 'Theo dõi',
        'en' => 'Follow'
    ],
    [
        'key' => 'ui.users.unfollow',
        'group_name' => 'ui',
        'vi' => 'Bỏ theo dõi',
        'en' => 'Unfollow'
    ],
    [
        'key' => 'ui.users.joined',
        'group_name' => 'ui',
        'vi' => 'Tham gia',
        'en' => 'Joined'
    ],
    // Thêm các keys khác nếu cần...
];

try {
    $now = Carbon::now();
    $totalAdded = 0;
    $totalSkipped = 0;
    
    echo "📝 Bắt đầu xử lý " . count($translations) . " translation keys...\n\n";
    
    foreach ($translations as $index => $translation) {
        $key = $translation['key'];
        $groupName = $translation['group_name'];
        $viContent = $translation['vi'];
        $enContent = $translation['en'];
        
        echo "🔍 [" . ($index + 1) . "/" . count($translations) . "] Xử lý key: {$key}\n";
        
        $addedForThisKey = 0;
        
        // Kiểm tra và thêm Vietnamese translation
        $existingVi = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'vi')
            ->first();
            
        if (!$existingVi) {
            DB::table('translations')->insert([
                'key' => $key,
                'locale' => 'vi',
                'content' => $viContent,
                'group_name' => $groupName,
                'namespace' => null,
                'is_active' => 1,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            echo "   ✅ Thêm VI: '{$viContent}'\n";
            $addedForThisKey++;
        } else {
            echo "   ⏭️  VI đã tồn tại: '{$existingVi->content}'\n";
        }
        
        // Kiểm tra và thêm English translation
        $existingEn = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'en')
            ->first();
            
        if (!$existingEn) {
            DB::table('translations')->insert([
                'key' => $key,
                'locale' => 'en',
                'content' => $enContent,
                'group_name' => $groupName,
                'namespace' => null,
                'is_active' => 1,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            echo "   ✅ Thêm EN: '{$enContent}'\n";
            $addedForThisKey++;
        } else {
            echo "   ⏭️  EN đã tồn tại: '{$existingEn->content}'\n";
        }
        
        if ($addedForThisKey > 0) {
            $totalAdded += $addedForThisKey;
        } else {
            $totalSkipped++;
        }
        
        echo "\n";
    }
    
    // Tổng kết
    echo "🎉 Hoàn thành!\n";
    echo "📊 Thống kê:\n";
    echo "   - Tổng số keys xử lý: " . count($translations) . "\n";
    echo "   - Translations đã thêm: {$totalAdded}\n";
    echo "   - Keys đã tồn tại (bỏ qua): {$totalSkipped}\n";
    
    if ($totalAdded > 0) {
        echo "\n💡 Lưu ý: Cache sẽ được clear tự động khi sử dụng translation.\n";
    }
    
    // Hiển thị thống kê theo group
    echo "\n📈 Thống kê translations theo group:\n";
    $groupStats = DB::table('translations')
        ->selectRaw('group_name, locale, COUNT(*) as count')
        ->whereIn('group_name', array_unique(array_column($translations, 'group_name')))
        ->groupBy('group_name', 'locale')
        ->orderBy('group_name')
        ->orderBy('locale')
        ->get();
        
    $currentGroup = null;
    foreach ($groupStats as $stat) {
        if ($currentGroup !== $stat->group_name) {
            echo "   📁 {$stat->group_name}:\n";
            $currentGroup = $stat->group_name;
        }
        echo "      - {$stat->locale}: {$stat->count} keys\n";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    exit(1);
}

echo "\n✨ Script hoàn thành!\n";
