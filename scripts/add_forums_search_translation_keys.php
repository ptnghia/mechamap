<?php

/**
 * Add Forums Search Translation Keys to Database
 *
 * This script adds new translation keys for the enhanced forums search functionality
 * that allows searching categories and forums directly on the forums index page.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Adding Forums Search Translation Keys\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Define new translation keys for forums search functionality
$translationKeys = [
    // Search placeholders
    'forums.search.placeholder_forums' => [
        'vi' => 'Tìm kiếm danh mục và diễn đàn...',
        'en' => 'Search categories and forums...',
        'group' => 'forums'
    ],

    // Search descriptions
    'forums.search.description_forums' => [
        'vi' => 'Tìm kiếm trong tên và mô tả của danh mục, diễn đàn. Tối thiểu 2 ký tự.',
        'en' => 'Search in category and forum names and descriptions. Minimum 2 characters.',
        'group' => 'forums'
    ],

    'forums.search.description_results' => [
        'vi' => 'Kết quả tìm kiếm danh mục và diễn đàn phù hợp với từ khóa.',
        'en' => 'Search results for categories and forums matching your keywords.',
        'group' => 'forums'
    ],

    // Search results
    'forums.search.results_for' => [
        'vi' => 'Kết quả tìm kiếm cho',
        'en' => 'Search results for',
        'group' => 'forums'
    ],

    'forums.search.categories_found' => [
        'vi' => 'danh mục tìm thấy',
        'en' => 'categories found',
        'group' => 'forums'
    ],

    'forums.search.show_all' => [
        'vi' => 'Hiển thị tất cả',
        'en' => 'Show all',
        'group' => 'forums'
    ],

    // Search links
    'forums.search.search_threads' => [
        'vi' => 'Tìm kiếm chủ đề',
        'en' => 'Search threads',
        'group' => 'forums'
    ],

    // UI actions
    'ui.actions.clear' => [
        'vi' => 'Xóa',
        'en' => 'Clear',
        'group' => 'ui'
    ],

    // Activity search
    'activity.search' => [
        'vi' => 'Tìm kiếm',
        'en' => 'Search',
        'group' => 'activity'
    ],

    // Search match types
    'forums.search.category_matches' => [
        'vi' => 'khớp danh mục',
        'en' => 'category matches',
        'group' => 'forums'
    ],

    'forums.search.forum_matches' => [
        'vi' => 'khớp diễn đàn',
        'en' => 'forum matches',
        'group' => 'forums'
    ],

    'forums.search.exact_match' => [
        'vi' => 'Khớp chính xác',
        'en' => 'Exact match',
        'group' => 'forums'
    ],

    'forums.search.contains_matches' => [
        'vi' => 'Chứa kết quả',
        'en' => 'Contains matches',
        'group' => 'forums'
    ],

    'forums.search.category_name_match' => [
        'vi' => 'Tên hoặc mô tả danh mục khớp với từ khóa tìm kiếm',
        'en' => 'Category name or description matches search keyword',
        'group' => 'forums'
    ],

    'forums.search.contains_matching_forums' => [
        'vi' => 'Danh mục này chứa các diễn đàn khớp với từ khóa tìm kiếm',
        'en' => 'This category contains forums that match the search keyword',
        'group' => 'forums'
    ],
];

$totalAdded = 0;
$totalSkipped = 0;
$totalErrors = 0;

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
echo "\n🎉 Forums search translation keys setup completed!\n";
echo "\n📋 NEXT STEPS:\n";
echo "1. Test the search functionality at https://mechamap.test/forums\n";
echo "2. Verify translation keys are working correctly\n";
echo "3. Clear translation cache if needed: php artisan cache:clear\n";
