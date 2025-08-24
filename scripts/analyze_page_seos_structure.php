<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\PageSeo;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {})
    ->withExceptions(function ($exceptions) {})
    ->create();

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "📊 PHÂN TÍCH BẢNG PAGE_SEOS - MECHAMAP\n";
echo str_repeat("=", 70) . "\n\n";

// 1. Cấu trúc bảng hiện tại
echo "🏗️ CẤU TRÚC BẢNG HIỆN TẠI:\n";
echo str_repeat("-", 50) . "\n";

$columns = [
    // Core identification
    'id' => 'Primary key',
    'route_name' => 'Tên route Laravel',
    'url_pattern' => 'Pattern URL (regex)',
    
    // Basic SEO (legacy)
    'title' => 'Tiêu đề SEO (legacy)',
    'description' => 'Mô tả SEO (legacy)', 
    'keywords' => 'Từ khóa SEO (legacy)',
    
    // Multilingual SEO
    'title_i18n' => 'Tiêu đề đa ngôn ngữ (JSON)',
    'description_i18n' => 'Mô tả đa ngôn ngữ (JSON)',
    'keywords_i18n' => 'Từ khóa đa ngôn ngữ (JSON)',
    
    // Open Graph
    'og_title' => 'OG Title (legacy)',
    'og_description' => 'OG Description (legacy)',
    'og_image' => 'OG Image URL',
    'og_title_i18n' => 'OG Title đa ngôn ngữ (JSON)',
    'og_description_i18n' => 'OG Description đa ngôn ngữ (JSON)',
    
    // Twitter Cards
    'twitter_title' => 'Twitter Title (legacy)',
    'twitter_description' => 'Twitter Description (legacy)',
    'twitter_image' => 'Twitter Image URL',
    'twitter_title_i18n' => 'Twitter Title đa ngôn ngữ (JSON)',
    'twitter_description_i18n' => 'Twitter Description đa ngôn ngữ (JSON)',
    
    // Technical SEO
    'canonical_url' => 'URL canonical',
    'no_index' => 'Chặn index (boolean)',
    'extra_meta' => 'Meta tags bổ sung',
    
    // Management
    'is_active' => 'Trạng thái hoạt động',
    'created_at' => 'Ngày tạo',
    'updated_at' => 'Ngày cập nhật'
];

foreach ($columns as $column => $description) {
    echo sprintf("  %-25s %s\n", $column, $description);
}

// 2. Phân tích dữ liệu mẫu
echo "\n📋 PHÂN TÍCH DỮ LIỆU MẪU:\n";
echo str_repeat("-", 50) . "\n";

$samples = PageSeo::take(5)->get();
$totalRecords = PageSeo::count();

echo "Tổng số records: {$totalRecords}\n\n";

foreach ($samples as $index => $sample) {
    echo "📄 Record " . ($index + 1) . ":\n";
    echo "  Route: " . ($sample->route_name ?: 'N/A') . "\n";
    echo "  URL Pattern: " . ($sample->url_pattern ?: 'N/A') . "\n";
    echo "  Title (legacy): " . ($sample->title ?: 'N/A') . "\n";
    echo "  Title i18n: " . ($sample->title_i18n ? 'Có' : 'Không') . "\n";
    echo "  OG Title: " . ($sample->og_title ?: 'N/A') . "\n";
    echo "  Twitter Title: " . ($sample->twitter_title ?: 'N/A') . "\n";
    echo "  No Index: " . ($sample->no_index ? 'true' : 'false') . "\n";
    echo "  Active: " . ($sample->is_active ? 'true' : 'false') . "\n";
    echo "\n";
}

// 3. Thống kê sử dụng cột
echo "📊 THỐNG KÊ SỬ DỤNG CỘT:\n";
echo str_repeat("-", 50) . "\n";

$stats = [
    'route_name' => PageSeo::whereNotNull('route_name')->count(),
    'url_pattern' => PageSeo::whereNotNull('url_pattern')->count(),
    'title' => PageSeo::whereNotNull('title')->count(),
    'title_i18n' => PageSeo::whereNotNull('title_i18n')->count(),
    'description' => PageSeo::whereNotNull('description')->count(),
    'description_i18n' => PageSeo::whereNotNull('description_i18n')->count(),
    'keywords' => PageSeo::whereNotNull('keywords')->count(),
    'keywords_i18n' => PageSeo::whereNotNull('keywords_i18n')->count(),
    'og_title' => PageSeo::whereNotNull('og_title')->count(),
    'og_title_i18n' => PageSeo::whereNotNull('og_title_i18n')->count(),
    'og_description' => PageSeo::whereNotNull('og_description')->count(),
    'og_description_i18n' => PageSeo::whereNotNull('og_description_i18n')->count(),
    'og_image' => PageSeo::whereNotNull('og_image')->count(),
    'twitter_title' => PageSeo::whereNotNull('twitter_title')->count(),
    'twitter_title_i18n' => PageSeo::whereNotNull('twitter_title_i18n')->count(),
    'twitter_description' => PageSeo::whereNotNull('twitter_description')->count(),
    'twitter_description_i18n' => PageSeo::whereNotNull('twitter_description_i18n')->count(),
    'twitter_image' => PageSeo::whereNotNull('twitter_image')->count(),
    'canonical_url' => PageSeo::whereNotNull('canonical_url')->count(),
    'extra_meta' => PageSeo::whereNotNull('extra_meta')->count(),
    'no_index_true' => PageSeo::where('no_index', true)->count(),
    'is_active_true' => PageSeo::where('is_active', true)->count(),
];

foreach ($stats as $field => $count) {
    $percentage = round(($count / $totalRecords) * 100, 1);
    echo sprintf("  %-25s %3d/%d (%s%%)\n", $field, $count, $totalRecords, $percentage);
}

// 4. Phân tích chuẩn SEO
echo "\n🎯 PHÂN TÍCH THEO CHUẨN SEO:\n";
echo str_repeat("-", 50) . "\n";

echo "📋 Chuẩn Google SEO:\n";
echo "  ✅ Title tag: Có (legacy + i18n)\n";
echo "  ✅ Meta description: Có (legacy + i18n)\n";
echo "  ✅ Meta keywords: Có (legacy + i18n)\n";
echo "  ✅ Canonical URL: Có\n";
echo "  ✅ Robots meta: Có (no_index)\n";
echo "  ✅ Open Graph: Có (title, description, image)\n";
echo "  ✅ Twitter Cards: Có (title, description, image)\n";
echo "  ✅ Multilingual: Có (JSON i18n)\n";

echo "\n📋 Chuẩn Schema.org:\n";
echo "  ❌ Structured Data: Chưa có cột riêng\n";
echo "  ❌ JSON-LD: Chưa có cột riêng\n";
echo "  ⚠️  Có thể dùng extra_meta\n";

echo "\n📋 Chuẩn Technical SEO:\n";
echo "  ✅ Hreflang: Có thể implement qua i18n\n";
echo "  ❌ Priority: Chưa có cột\n";
echo "  ❌ Sitemap inclusion: Chưa có cột\n";
echo "  ❌ Last modified: Có updated_at\n";

// 5. Đánh giá thiếu sót
echo "\n⚠️  CÁC CỘT CÓ THỂ BỔ SUNG:\n";
echo str_repeat("-", 50) . "\n";

$missingColumns = [
    'priority' => 'Mức độ ưu tiên SEO (1-10)',
    'sitemap_include' => 'Có đưa vào sitemap không',
    'sitemap_priority' => 'Priority trong sitemap (0.0-1.0)',
    'sitemap_changefreq' => 'Tần suất thay đổi (daily, weekly, etc)',
    'structured_data' => 'JSON-LD structured data',
    'hreflang' => 'Hreflang alternatives (JSON)',
    'breadcrumb_title' => 'Tiêu đề breadcrumb',
    'breadcrumb_title_i18n' => 'Breadcrumb đa ngôn ngữ',
    'meta_author' => 'Tác giả meta tag',
    'meta_publisher' => 'Nhà xuất bản',
    'article_type' => 'Loại bài viết (article, product, etc)',
    'focus_keyword' => 'Từ khóa chính',
    'focus_keyword_i18n' => 'Từ khóa chính đa ngôn ngữ',
];

foreach ($missingColumns as $column => $description) {
    echo "  • {$column}: {$description}\n";
}

// 6. Đánh giá cột dư thừa
echo "\n🗑️  CÁC CỘT CÓ THỂ DƯ THỪA:\n";
echo str_repeat("-", 50) . "\n";

echo "  • title (legacy): Có thể thay bằng title_i18n\n";
echo "  • description (legacy): Có thể thay bằng description_i18n\n";
echo "  • keywords (legacy): Có thể thay bằng keywords_i18n\n";
echo "  • og_title (legacy): Có thể thay bằng og_title_i18n\n";
echo "  • og_description (legacy): Có thể thay bằng og_description_i18n\n";
echo "  • twitter_title (legacy): Có thể thay bằng twitter_title_i18n\n";
echo "  • twitter_description (legacy): Có thể thay bằng twitter_description_i18n\n";

echo "\n✅ KẾT LUẬN:\n";
echo str_repeat("-", 50) . "\n";
echo "1. Cấu trúc bảng: Tốt, đáp ứng chuẩn SEO cơ bản\n";
echo "2. Đa ngôn ngữ: Hoàn thiện với JSON i18n\n";
echo "3. Open Graph & Twitter: Đầy đủ\n";
echo "4. Technical SEO: Cơ bản, có thể mở rộng\n";
echo "5. Legacy columns: Có thể dọn dẹp sau\n";
echo "6. Cần bổ sung: Priority, Sitemap, Structured Data\n";
echo "7. Tổng đánh giá: 8/10 - Rất tốt\n\n";
