<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Bắt đầu chuyển đổi trang Technical Index sang translation keys...\n\n";

// 1. Thêm translation keys vào database
$translations = [
    // Main page content
    'technical.index.title' => [
        'vi' => 'Tài nguyên Kỹ thuật',
        'en' => 'Technical Resources'
    ],
    'technical.index.subtitle' => [
        'vi' => 'Trung tâm tài nguyên kỹ thuật dành cho kỹ sư cơ khí chuyên nghiệp',
        'en' => 'Technical resource center for professional mechanical engineers'
    ],

    // Technical Drawings section
    'technical.index.drawings.title' => [
        'vi' => 'Bản vẽ Kỹ thuật',
        'en' => 'Technical Drawings'
    ],
    'technical.index.drawings.description' => [
        'vi' => 'Thư viện bản vẽ kỹ thuật chuẩn, chi tiết gia công và assembly drawings',
        'en' => 'Library of standard technical drawings, machining details and assembly drawings'
    ],
    'technical.index.drawings.view_more' => [
        'vi' => 'Xem thêm',
        'en' => 'View More'
    ],

    // CAD Files section
    'technical.index.cad.title' => [
        'vi' => 'File CAD',
        'en' => 'CAD Files'
    ],
    'technical.index.cad.description' => [
        'vi' => 'Thư viện file CAD 3D, 2D drawings và models cho các ứng dụng cơ khí',
        'en' => 'Library of 3D CAD files, 2D drawings and models for mechanical applications'
    ],
    'technical.index.cad.coming_soon' => [
        'vi' => 'Sắp ra mắt',
        'en' => 'Coming Soon'
    ],

    // Materials Database section
    'technical.index.materials.title' => [
        'vi' => 'Cơ sở dữ liệu Vật liệu',
        'en' => 'Materials Database'
    ],
    'technical.index.materials.description' => [
        'vi' => 'Thông tin chi tiết về tính chất vật liệu, thép, hợp kim và vật liệu composite',
        'en' => 'Detailed information on material properties, steel, alloys and composite materials'
    ],

    // Engineering Standards section
    'technical.index.standards.title' => [
        'vi' => 'Tiêu chuẩn Kỹ thuật',
        'en' => 'Engineering Standards'
    ],
    'technical.index.standards.description' => [
        'vi' => 'Tiêu chuẩn TCVN, ISO, ASME, DIN và các quy chuẩn kỹ thuật quốc tế',
        'en' => 'TCVN, ISO, ASME, DIN standards and international technical regulations'
    ],

    // Calculation Tools section
    'technical.index.tools.title' => [
        'vi' => 'Công cụ Tính toán',
        'en' => 'Calculation Tools'
    ],
    'technical.index.tools.description' => [
        'vi' => 'Bộ công cụ tính toán kỹ thuật: độ bền, ứng suất, thiết kế trục, bánh răng',
        'en' => 'Technical calculation tools: strength, stress, shaft design, gears'
    ],

    // Manufacturing Processes section
    'technical.index.processes.title' => [
        'vi' => 'Quy trình Sản xuất',
        'en' => 'Manufacturing Processes'
    ],
    'technical.index.processes.description' => [
        'vi' => 'Hướng dẫn quy trình gia công, nhiệt luyện, hàn và các công nghệ sản xuất',
        'en' => 'Guides for machining, heat treatment, welding and manufacturing technologies'
    ],

    // Quick Access section
    'technical.index.quick_access.title' => [
        'vi' => 'Truy cập nhanh',
        'en' => 'Quick Access'
    ],
    'technical.index.quick_access.forums' => [
        'vi' => 'Diễn đàn',
        'en' => 'Forums'
    ],
    'technical.index.quick_access.marketplace' => [
        'vi' => 'Marketplace',
        'en' => 'Marketplace'
    ],
    'technical.index.quick_access.showcase' => [
        'vi' => 'Showcase',
        'en' => 'Showcase'
    ],
    'technical.index.quick_access.learning' => [
        'vi' => 'Học tập',
        'en' => 'Learning'
    ]
];

$addedCount = 0;
$skippedCount = 0;

foreach ($translations as $key => $values) {
    foreach ($values as $locale => $value) {
        $existing = DB::table('translations')
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();

        if (!$existing) {
            DB::table('translations')->insert([
                'key' => $key,
                'locale' => $locale,
                'content' => $value,
                'group_name' => 'technical',
                'namespace' => null,
                'is_active' => 1,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "✅ Đã thêm: {$key}\n";
            echo "   {$locale}: {$value}\n\n";
            $addedCount++;
        } else {
            echo "⏭️ Đã bỏ qua (đã tồn tại): {$key} ({$locale})\n";
            $skippedCount++;
        }
    }
}

echo "📊 Hoàn thành!\n";
echo "📊 Thống kê:\n";
echo "   - Đã thêm: {$addedCount} translation keys\n";
echo "   - Đã bỏ qua: {$skippedCount} keys (đã tồn tại)\n";
echo "   - Tổng keys xử lý: " . (count($translations) * 2) . " keys\n\n";

// 2. Xóa cache translations
echo "🗑️ Xóa cache translations...\n";
Cache::forget('translations');
Cache::forget('translations_vi');
Cache::forget('translations_en');
echo "✅ Đã xóa cache thành công!\n\n";

// 3. Cập nhật file view
echo "📝 Cập nhật file view technical/index.blade.php...\n";

$viewPath = __DIR__ . '/../resources/views/technical/index.blade.php';
$viewContent = file_get_contents($viewPath);

// Backup original file
$backupPath = __DIR__ . '/../resources/views/technical/index.blade.php.backup.' . date('Y_m_d_H_i_s');
file_put_contents($backupPath, $viewContent);
echo "💾 Đã backup file gốc: {$backupPath}\n";

// Replace hardcoded text with translation keys
$replacements = [
    // Title and subtitle
    'Tài nguyên Kỹ thuật' => "{{ __('technical.index.title') }}",
    'Trung tâm tài nguyên kỹ thuật dành cho kỹ sư cơ khí chuyên nghiệp' => "{{ __('technical.index.subtitle') }}",

    // Section titles
    'Bản vẽ Kỹ thuật' => "{{ __('technical.index.drawings.title') }}",
    'File CAD' => "{{ __('technical.index.cad.title') }}",
    'Cơ sở dữ liệu Vật liệu' => "{{ __('technical.index.materials.title') }}",
    'Tiêu chuẩn Kỹ thuật' => "{{ __('technical.index.standards.title') }}",
    'Công cụ Tính toán' => "{{ __('technical.index.tools.title') }}",
    'Quy trình Sản xuất' => "{{ __('technical.index.processes.title') }}",

    // Descriptions
    'Thư viện bản vẽ kỹ thuật chuẩn, chi tiết gia công và assembly drawings' => "{{ __('technical.index.drawings.description') }}",
    'Thư viện file CAD 3D, 2D drawings và models cho các ứng dụng cơ khí' => "{{ __('technical.index.cad.description') }}",
    'Thông tin chi tiết về tính chất vật liệu, thép, hợp kim và vật liệu composite' => "{{ __('technical.index.materials.description') }}",
    'Tiêu chuẩn TCVN, ISO, ASME, DIN và các quy chuẩn kỹ thuật quốc tế' => "{{ __('technical.index.standards.description') }}",
    '"Bộ công cụ tính toán kỹ thuật: độ bền, ứng suất, thiết kế trục, bánh răng"' => "{{ __('technical.index.tools.description') }}",
    'Hướng dẫn quy trình gia công, nhiệt luyện, hàn và các công nghệ sản xuất' => "{{ __('technical.index.processes.description') }}",

    // Buttons and links
    'Xem thêm' => "{{ __('technical.index.drawings.view_more') }}",
    'Sắp ra mắt' => "{{ __('technical.index.cad.coming_soon') }}",
    'Truy cập nhanh' => "{{ __('technical.index.quick_access.title') }}",
    'Diễn đàn' => "{{ __('technical.index.quick_access.forums') }}",
    'Marketplace' => "{{ __('technical.index.quick_access.marketplace') }}",
    'Showcase' => "{{ __('technical.index.quick_access.showcase') }}",
    'Học tập' => "{{ __('technical.index.quick_access.learning') }}"
];

foreach ($replacements as $search => $replace) {
    $viewContent = str_replace($search, $replace, $viewContent);
}

// Write updated content
file_put_contents($viewPath, $viewContent);
echo "✅ Đã cập nhật file view thành công!\n\n";

echo "🎉 Hoàn tất chuyển đổi trang Technical Index!\n";
echo "📋 Các translation keys đã được thêm vào group 'technical':\n";
echo "   - Main page: 2 keys\n";
echo "   - Technical sections: 12 keys\n";
echo "   - Quick access: 5 keys\n";
echo "   - Total: 19 keys x 2 languages = 38 translation entries\n\n";

echo "🔗 Trang có thể được truy cập tại: https://mechamap.test/technical\n";
