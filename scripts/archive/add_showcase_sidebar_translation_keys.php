<?php

/**
 * Script để thêm translation keys cho Showcase Sidebar
 * Chạy: php scripts/add_showcase_sidebar_translation_keys.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 ADDING SHOWCASE SIDEBAR TRANSLATION KEYS\n";
echo "==========================================\n";

// Translation keys cho Showcase Sidebar
$translationKeys = [
    // Sidebar Stats
    'showcase.quick_stats' => [
        'vi' => 'Thống kê nhanh',
        'en' => 'Quick Stats',
        'group' => 'showcase'
    ],
    'showcase.total_projects' => [
        'vi' => 'Tổng dự án',
        'en' => 'Total Projects',
        'group' => 'showcase'
    ],
    'showcase.contributors' => [
        'vi' => 'Người đóng góp',
        'en' => 'Contributors',
        'group' => 'showcase'
    ],

    // CTA Section
    'showcase.share_your_project' => [
        'vi' => 'Chia sẻ dự án của bạn',
        'en' => 'Share Your Project',
        'group' => 'showcase'
    ],
    'showcase.showcase_description' => [
        'vi' => 'Trưng bày dự án kỹ thuật và nhận phản hồi từ cộng đồng',
        'en' => 'Showcase your technical projects and get feedback from the community',
        'group' => 'showcase'
    ],

    // Search and Filter Labels
    'showcase.search_placeholder' => [
        'vi' => 'Tìm kiếm dự án...',
        'en' => 'Search projects...',
        'group' => 'showcase'
    ],
    'showcase.all_categories' => [
        'vi' => 'Tất cả danh mục',
        'en' => 'All Categories',
        'group' => 'showcase'
    ],
    'showcase.all_levels' => [
        'vi' => 'Tất cả mức độ',
        'en' => 'All Levels',
        'group' => 'showcase'
    ],
    'showcase.all_types' => [
        'vi' => 'Tất cả loại',
        'en' => 'All Types',
        'group' => 'showcase'
    ],
    'showcase.all_software' => [
        'vi' => 'Tất cả phần mềm',
        'en' => 'All Software',
        'group' => 'showcase'
    ],
    'showcase.all_ratings' => [
        'vi' => 'Tất cả đánh giá',
        'en' => 'All Ratings',
        'group' => 'showcase'
    ],

    // Rating Options
    'showcase.4_plus_stars' => [
        'vi' => '4+ sao',
        'en' => '4+ stars',
        'group' => 'showcase'
    ],
    'showcase.3_plus_stars' => [
        'vi' => '3+ sao',
        'en' => '3+ stars',
        'group' => 'showcase'
    ],
    'showcase.2_plus_stars' => [
        'vi' => '2+ sao',
        'en' => '2+ stars',
        'group' => 'showcase'
    ],

    // Sort Options
    'showcase.newest' => [
        'vi' => 'Mới nhất',
        'en' => 'Newest',
        'group' => 'showcase'
    ],
    'showcase.most_viewed' => [
        'vi' => 'Xem nhiều nhất',
        'en' => 'Most Viewed',
        'group' => 'showcase'
    ],
    'showcase.highest_rated' => [
        'vi' => 'Đánh giá cao nhất',
        'en' => 'Highest Rated',
        'group' => 'showcase'
    ],
    'showcase.most_downloads' => [
        'vi' => 'Tải nhiều nhất',
        'en' => 'Most Downloads',
        'group' => 'showcase'
    ],
    'showcase.oldest' => [
        'vi' => 'Cũ nhất',
        'en' => 'Oldest',
        'group' => 'showcase'
    ],

    // Form Labels
    'showcase.project_name' => [
        'vi' => 'Tên dự án',
        'en' => 'Project Name',
        'group' => 'showcase'
    ],
    'showcase.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category',
        'group' => 'showcase'
    ],
    'showcase.complexity' => [
        'vi' => 'Độ phức tạp',
        'en' => 'Complexity',
        'group' => 'showcase'
    ],
    'showcase.project_type' => [
        'vi' => 'Loại dự án',
        'en' => 'Project Type',
        'group' => 'showcase'
    ],
    'showcase.software' => [
        'vi' => 'Phần mềm',
        'en' => 'Software',
        'group' => 'showcase'
    ],
    'showcase.min_rating' => [
        'vi' => 'Đánh giá tối thiểu',
        'en' => 'Minimum Rating',
        'group' => 'showcase'
    ],
    'showcase.sort_by' => [
        'vi' => 'Sắp xếp theo',
        'en' => 'Sort By',
        'group' => 'showcase'
    ],

    // Actions
    'showcase.search' => [
        'vi' => 'Tìm kiếm',
        'en' => 'Search',
        'group' => 'showcase'
    ],
    'showcase.clear_filters' => [
        'vi' => 'Xóa bộ lọc',
        'en' => 'Clear Filters',
        'group' => 'showcase'
    ],
    'showcase.create_new' => [
        'vi' => 'Tạo mới',
        'en' => 'Create New',
        'group' => 'showcase'
    ],

    // Page Content
    'showcase.public_showcases' => [
        'vi' => 'Showcase công khai',
        'en' => 'Public Showcases',
        'group' => 'showcase'
    ],
    'showcase.page_description' => [
        'vi' => 'Khám phá các dự án sáng tạo từ cộng đồng',
        'en' => 'Discover creative projects from the community',
        'group' => 'showcase'
    ],
    'showcase.project_categories' => [
        'vi' => 'Danh mục dự án',
        'en' => 'Project Categories',
        'group' => 'showcase'
    ],
    'showcase.featured_projects' => [
        'vi' => 'Dự án nổi bật',
        'en' => 'Featured Projects',
        'group' => 'showcase'
    ],
    'showcase.advanced_search' => [
        'vi' => 'Tìm kiếm nâng cao',
        'en' => 'Advanced Search',
        'group' => 'showcase'
    ],
    'showcase.all_projects' => [
        'vi' => 'Tất cả dự án',
        'en' => 'All Projects',
        'group' => 'showcase'
    ],
    'showcase.projects' => [
        'vi' => 'dự án',
        'en' => 'projects',
        'group' => 'showcase'
    ],
    'showcase.avg_rating' => [
        'vi' => 'đánh giá TB',
        'en' => 'avg rating',
        'group' => 'showcase'
    ],
    'showcase.results' => [
        'vi' => 'kết quả',
        'en' => 'results',
        'group' => 'showcase'
    ],
    'showcase.no_projects_found' => [
        'vi' => 'Không tìm thấy dự án nào',
        'en' => 'No projects found',
        'group' => 'showcase'
    ],
    'showcase.try_different_filters' => [
        'vi' => 'Hãy thử các bộ lọc khác',
        'en' => 'Try different filters',
        'group' => 'showcase'
    ],
    'showcase.create_new_project' => [
        'vi' => 'Tạo dự án mới',
        'en' => 'Create New Project',
        'group' => 'showcase'
    ],
    'showcase.no_featured_projects' => [
        'vi' => 'Chưa có dự án nổi bật nào',
        'en' => 'No featured projects yet',
        'group' => 'showcase'
    ],

    // Search Tags
    'showcase.active_filters' => [
        'vi' => 'Bộ lọc đang áp dụng',
        'en' => 'Active Filters',
        'group' => 'showcase'
    ],
    'showcase.clear_all' => [
        'vi' => 'Xóa tất cả',
        'en' => 'Clear All',
        'group' => 'showcase'
    ],
    'showcase.stars' => [
        'vi' => 'sao',
        'en' => 'stars',
        'group' => 'showcase'
    ]
];

$totalAdded = 0;
$totalSkipped = 0;

echo "📊 Total keys to process: " . count($translationKeys) . "\n\n";

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

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
}

echo "\n🎉 SUMMARY:\n";
echo "===========\n";
echo "✅ Keys processed: " . count($translationKeys) . "\n";
echo "✅ Keys added: {$totalAdded}\n";
echo "⏭️ Keys skipped: {$totalSkipped}\n";
echo "\n🚀 Showcase sidebar translation keys have been added successfully!\n";
echo "Now you can access the showcase page to see the new sidebar in action.\n";
echo "\n📍 URL: https://mechamap.test/showcase\n";
