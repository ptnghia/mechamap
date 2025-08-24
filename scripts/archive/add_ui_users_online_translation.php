<?php

/**
 * Script để thêm translation key ui.users.online vào database
 * Tự động bỏ qua nếu key đã tồn tại
 *
 * Usage: php scripts/add_ui_users_online_translation.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Script thêm translation key ui.users.online\n";
echo "================================================\n\n";

try {
    // Translation key cần thêm
    $translationKey = 'ui.users.online';
    $groupName = 'ui';
    $vietnameseContent = 'Trực tuyến';
    $englishContent = 'Online';

    echo "📝 Kiểm tra translation key: {$translationKey}\n";

    // Kiểm tra xem key đã tồn tại chưa
    $existingVietnamese = DB::table('translations')
        ->where('key', $translationKey)
        ->where('locale', 'vi')
        ->first();

    $existingEnglish = DB::table('translations')
        ->where('key', $translationKey)
        ->where('locale', 'en')
        ->first();

    $now = Carbon::now();
    $addedCount = 0;

    // Thêm Vietnamese translation nếu chưa tồn tại
    if (!$existingVietnamese) {
        DB::table('translations')->insert([
            'key' => $translationKey,
            'locale' => 'vi',
            'content' => $vietnameseContent,
            'group' => $group,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        echo "✅ Đã thêm Vietnamese translation: {$translationKey} = '{$vietnameseContent}'\n";
        $addedCount++;
    } else {
        echo "⏭️  Vietnamese translation đã tồn tại: {$translationKey} = '{$existingVietnamese->content}'\n";
    }

    // Thêm English translation nếu chưa tồn tại
    if (!$existingEnglish) {
        DB::table('translations')->insert([
            'key' => $translationKey,
            'locale' => 'en',
            'content' => $englishContent,
            'group' => $group,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        echo "✅ Đã thêm English translation: {$translationKey} = '{$englishContent}'\n";
        $addedCount++;
    } else {
        echo "⏭️  English translation đã tồn tại: {$translationKey} = '{$existingEnglish->content}'\n";
    }

    echo "\n";

    if ($addedCount > 0) {
        echo "🎉 Hoàn thành! Đã thêm {$addedCount} translation(s) mới.\n";
        echo "💡 Lưu ý: Cache sẽ được clear tự động khi sử dụng translation.\n";
    } else {
        echo "ℹ️  Không có translation nào được thêm (tất cả đã tồn tại).\n";
    }

    // Hiển thị thống kê
    echo "\n📊 Thống kê translations trong group 'ui':\n";
    $uiTranslations = DB::table('translations')
        ->where('group', 'ui')
        ->selectRaw('locale, COUNT(*) as count')
        ->groupBy('locale')
        ->get();

    foreach ($uiTranslations as $stat) {
        echo "   - {$stat->locale}: {$stat->count} keys\n";
    }

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    exit(1);
}

echo "\n✨ Script hoàn thành!\n";
