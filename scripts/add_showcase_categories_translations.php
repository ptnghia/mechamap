<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ADDING SHOWCASE CATEGORIES TRANSLATION KEYS ===" . PHP_EOL;

// Translation keys cần thêm cho showcase categories
$translationKeys = [
    'showcase.categories' => [
        'vi' => 'Danh mục',
        'en' => 'Categories',
        'group' => 'showcase'
    ],
    'showcase.files' => [
        'vi' => 'Files',
        'en' => 'Files',
        'group' => 'showcase'
    ],
    'showcase.downloads' => [
        'vi' => 'Tải xuống',
        'en' => 'Downloads',
        'group' => 'showcase'
    ],
    'showcase.no_categories_available' => [
        'vi' => 'Chưa có danh mục nào',
        'en' => 'No categories available',
        'group' => 'showcase'
    ],
    'showcase.category_stats' => [
        'vi' => 'Thống kê danh mục',
        'en' => 'Category Statistics',
        'group' => 'showcase'
    ],
    'showcase.browse_category' => [
        'vi' => 'Duyệt danh mục',
        'en' => 'Browse Category',
        'group' => 'showcase'
    ],
    'showcase.view_all_in_category' => [
        'vi' => 'Xem tất cả trong danh mục',
        'en' => 'View all in category',
        'group' => 'showcase'
    ],
    'showcase.category_description' => [
        'vi' => 'Mô tả danh mục',
        'en' => 'Category Description',
        'group' => 'showcase'
    ],
    'showcase.total_files' => [
        'vi' => 'Tổng số files',
        'en' => 'Total Files',
        'group' => 'showcase'
    ],
    'showcase.total_downloads' => [
        'vi' => 'Tổng lượt tải',
        'en' => 'Total Downloads',
        'group' => 'showcase'
    ]
];

echo "Đang thêm " . count($translationKeys) . " translation keys..." . PHP_EOL;

$addedCount = 0;
$skippedCount = 0;

foreach ($translationKeys as $key => $translations) {
    foreach (['vi', 'en'] as $locale) {
        // Kiểm tra xem key đã tồn tại chưa
        $existing = DB::table('translations')
            ->where('group_name', $translations['group'])
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();

        if ($existing) {
            echo "  ⏭️  Bỏ qua: {$key} ({$locale}) - đã tồn tại" . PHP_EOL;
            $skippedCount++;
            continue;
        }

        // Thêm translation key mới
        DB::table('translations')->insert([
            'group_name' => $translations['group'],
            'key' => $key,
            'locale' => $locale,
            'content' => $translations[$locale],
            'namespace' => '*',
            'is_active' => true,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "  ✅ Đã thêm: {$key} ({$locale}) = '{$translations[$locale]}'" . PHP_EOL;
        $addedCount++;
    }
}

echo PHP_EOL;
echo "=== KẾT QUẢ ===" . PHP_EOL;
echo "✅ Đã thêm: {$addedCount} translation keys" . PHP_EOL;
echo "⏭️  Đã bỏ qua: {$skippedCount} keys (đã tồn tại)" . PHP_EOL;
echo PHP_EOL;

// Kiểm tra lại các keys đã thêm
echo "=== KIỂM TRA LẠI ===" . PHP_EOL;
foreach (array_keys($translationKeys) as $key) {
    $viTranslation = DB::table('translations')
        ->where('group_name', 'showcase')
        ->where('key', $key)
        ->where('locale', 'vi')
        ->value('content');

    $enTranslation = DB::table('translations')
        ->where('group_name', 'showcase')
        ->where('key', $key)
        ->where('locale', 'en')
        ->value('content');

    if ($viTranslation && $enTranslation) {
        echo "✅ {$key}: VI='{$viTranslation}' | EN='{$enTranslation}'" . PHP_EOL;
    } else {
        echo "❌ {$key}: THIẾU TRANSLATION" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== HOÀN THÀNH ===" . PHP_EOL;
