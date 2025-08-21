<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Translation keys for CAD Library and Documentation Portal
$translations = [
    // CAD Library Keys
    'cad.library.title' => [
        'vi' => 'Thư viện CAD',
        'en' => 'CAD Library'
    ],
    'cad.library.description' => [
        'vi' => 'Tải xuống và chia sẻ file CAD, mô hình 3D và bản vẽ kỹ thuật',
        'en' => 'Download and share CAD files, 3D models and technical drawings'
    ],
    'cad.library.export' => [
        'vi' => 'Xuất dữ liệu',
        'en' => 'Export Data'
    ],
    'cad.library.cad_files' => [
        'vi' => 'File CAD',
        'en' => 'CAD Files'
    ],
    'cad.library.downloads' => [
        'vi' => 'Lượt tải',
        'en' => 'Downloads'
    ],
    'cad.library.file_types' => [
        'vi' => 'Loại file',
        'en' => 'File Types'
    ],
    'cad.library.contributors' => [
        'vi' => 'Người đóng góp',
        'en' => 'Contributors'
    ],
    'cad.library.search_cad_files' => [
        'vi' => 'Tìm kiếm file CAD',
        'en' => 'Search CAD Files'
    ],
    'cad.library.search_placeholder' => [
        'vi' => 'Tìm kiếm theo tên file, mô tả hoặc từ khóa...',
        'en' => 'Search by filename, description or keywords...'
    ],
    'cad.library.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category'
    ],
    'cad.library.all_categories' => [
        'vi' => 'Tất cả danh mục',
        'en' => 'All Categories'
    ],
    'cad.library.file_type' => [
        'vi' => 'Loại file',
        'en' => 'File Type'
    ],
    'cad.library.all_types' => [
        'vi' => 'Tất cả loại',
        'en' => 'All Types'
    ],
    'cad.library.software' => [
        'vi' => 'Phần mềm',
        'en' => 'Software'
    ],
    'cad.library.all_software' => [
        'vi' => 'Tất cả phần mềm',
        'en' => 'All Software'
    ],
    'cad.library.sort_by' => [
        'vi' => 'Sắp xếp theo',
        'en' => 'Sort By'
    ],
    'cad.library.newest' => [
        'vi' => 'Mới nhất',
        'en' => 'Newest'
    ],
    'cad.library.most_downloaded' => [
        'vi' => 'Tải nhiều nhất',
        'en' => 'Most Downloaded'
    ],
    'cad.library.highest_rated' => [
        'vi' => 'Đánh giá cao nhất',
        'en' => 'Highest Rated'
    ],
    'cad.library.name_az' => [
        'vi' => 'Tên A-Z',
        'en' => 'Name A-Z'
    ],
    'cad.library.file_size' => [
        'vi' => 'Kích thước file',
        'en' => 'File Size'
    ],
    'cad.library.rating' => [
        'vi' => 'Đánh giá',
        'en' => 'Rating'
    ],
    'cad.library.license' => [
        'vi' => 'Giấy phép',
        'en' => 'License'
    ],
    'cad.library.commercial' => [
        'vi' => 'Thương mại',
        'en' => 'Commercial'
    ],
    'cad.library.educational' => [
        'vi' => 'Giáo dục',
        'en' => 'Educational'
    ],
    'cad.library.free' => [
        'vi' => 'Miễn phí',
        'en' => 'Free'
    ],
    'cad.library.view' => [
        'vi' => 'Xem',
        'en' => 'View'
    ],
    'cad.library.login' => [
        'vi' => 'Đăng nhập',
        'en' => 'Login'
    ],
    'cad.library.download' => [
        'vi' => 'Tải xuống',
        'en' => 'Download'
    ],
    'cad.library.by' => [
        'vi' => 'bởi',
        'en' => 'by'
    ],
    'cad.library.popular_cad_software' => [
        'vi' => 'Phần mềm CAD phổ biến',
        'en' => 'Popular CAD Software'
    ],
    'cad.library.files_available' => [
        'vi' => 'file có sẵn',
        'en' => 'files available'
    ],

    // Documentation Portal Keys
    'docs.title' => [
        'vi' => 'Cổng thông tin Tài liệu',
        'en' => 'Documentation Portal'
    ],
    'docs.subtitle' => [
        'vi' => 'Hướng dẫn toàn diện, tutorials và tài liệu API cho nền tảng MechaMap',
        'en' => 'Comprehensive guides, tutorials, and API documentation for the MechaMap platform'
    ],
    'docs.documents' => [
        'vi' => 'Tài liệu',
        'en' => 'Documents'
    ],
    'docs.categories' => [
        'vi' => 'Danh mục',
        'en' => 'Categories'
    ],
    'docs.total_views' => [
        'vi' => 'Tổng lượt xem',
        'en' => 'Total Views'
    ],
    'docs.downloads' => [
        'vi' => 'Lượt tải',
        'en' => 'Downloads'
    ],
    'docs.search_placeholder' => [
        'vi' => 'Tìm kiếm tài liệu...',
        'en' => 'Search documentation...'
    ],
    'docs.search' => [
        'vi' => 'Tìm kiếm',
        'en' => 'Search'
    ],
    'docs.featured_documentation' => [
        'vi' => 'Tài liệu nổi bật',
        'en' => 'Featured Documentation'
    ],
    'docs.recent_documentation' => [
        'vi' => 'Tài liệu gần đây',
        'en' => 'Recent Documentation'
    ],
    'docs.quick_links' => [
        'vi' => 'Liên kết nhanh',
        'en' => 'Quick Links'
    ],
    'docs.user_guides' => [
        'vi' => 'Hướng dẫn người dùng',
        'en' => 'User Guides'
    ],
    'docs.tutorials' => [
        'vi' => 'Hướng dẫn thực hành',
        'en' => 'Tutorials'
    ],
    'docs.api_documentation' => [
        'vi' => 'Tài liệu API',
        'en' => 'API Documentation'
    ],
    'docs.beginner_guides' => [
        'vi' => 'Hướng dẫn cơ bản',
        'en' => 'Beginner Guides'
    ],
    'docs.advanced_topics' => [
        'vi' => 'Chủ đề nâng cao',
        'en' => 'Advanced Topics'
    ],
    'docs.need_help' => [
        'vi' => 'Cần trợ giúp?',
        'en' => 'Need Help?'
    ],
    'docs.views' => [
        'vi' => 'lượt xem',
        'en' => 'views'
    ],
    'docs.ago' => [
        'vi' => 'trước',
        'en' => 'ago'
    ],
    'docs.month' => [
        'vi' => 'tháng',
        'en' => 'month'
    ],
    'docs.months' => [
        'vi' => 'tháng',
        'en' => 'months'
    ],
    'docs.day' => [
        'vi' => 'ngày',
        'en' => 'day'
    ],
    'docs.days' => [
        'vi' => 'ngày',
        'en' => 'days'
    ],
    'docs.hour' => [
        'vi' => 'giờ',
        'en' => 'hour'
    ],
    'docs.hours' => [
        'vi' => 'giờ',
        'en' => 'hours'
    ],
];

echo "🚀 Bắt đầu thêm translation keys cho CAD Library và Documentation Portal...\n\n";

$addedCount = 0;
$skippedCount = 0;

foreach ($translations as $key => $values) {
    // Check if translation key already exists
    $existingTranslation = DB::table('translations')
        ->where('group_name', 'technical')
        ->where('key', $key)
        ->first();

    if ($existingTranslation) {
        echo "⚠️  Key đã tồn tại: {$key}\n";
        $skippedCount++;
        continue;
    }

    // Add Vietnamese translation
    DB::table('translations')->insert([
        'group_name' => 'technical',
        'key' => $key,
        'locale' => 'vi',
        'content' => $values['vi'],
        'is_active' => true,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Add English translation
    DB::table('translations')->insert([
        'group_name' => 'technical',
        'key' => $key,
        'locale' => 'en',
        'content' => $values['en'],
        'is_active' => true,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Đã thêm: {$key}\n";
    echo "   VI: {$values['vi']}\n";
    echo "   EN: {$values['en']}\n\n";

    $addedCount += 2; // VI + EN
}

echo "🎉 Hoàn thành!\n";
echo "📊 Thống kê:\n";
echo "   - Đã thêm: {$addedCount} translation keys\n";
echo "   - Đã bỏ qua: {$skippedCount} keys (đã tồn tại)\n";
echo "   - Tổng keys xử lý: " . count($translations) . " keys\n\n";

echo "🔄 Xóa cache translations...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅ Đã xóa cache thành công!\n";
} catch (Exception $e) {
    echo "⚠️  Lỗi khi xóa cache: " . $e->getMessage() . "\n";
}

echo "\n🎯 Các translation keys đã được thêm vào group 'technical':\n";
echo "   - CAD Library: " . count(array_filter(array_keys($translations), fn($k) => str_starts_with($k, 'cad.'))) . " keys\n";
echo "   - Documentation Portal: " . count(array_filter(array_keys($translations), fn($k) => str_starts_with($k, 'docs.'))) . " keys\n";
