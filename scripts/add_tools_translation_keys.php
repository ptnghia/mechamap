<?php
/**
 * Add Tools Translation Keys
 * Script để thêm translation keys cho tất cả các tools section
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🛠️ Adding Tools Translation Keys...\n";
echo "===================================\n";

// Translation keys cho Tools section
$translationKeys = [
    // CAD Library
    'tools.cad_library.title' => [
        'vi' => 'Thư viện CAD',
        'en' => 'CAD Library',
        'group' => 'tools'
    ],
    'tools.cad_library.description' => [
        'vi' => 'Tải xuống và chia sẻ file CAD, mô hình 3D và bản vẽ kỹ thuật',
        'en' => 'Download and share CAD files, 3D models, and technical drawings',
        'group' => 'tools'
    ],
    'tools.cad_library.my_files' => [
        'vi' => 'File của tôi',
        'en' => 'My Files',
        'group' => 'tools'
    ],
    'tools.cad_library.upload_file' => [
        'vi' => 'Tải lên file CAD',
        'en' => 'Upload CAD File',
        'group' => 'tools'
    ],
    'tools.cad_library.export' => [
        'vi' => 'Xuất dữ liệu',
        'en' => 'Export',
        'group' => 'tools'
    ],
    'tools.cad_library.csv_format' => [
        'vi' => 'Định dạng CSV',
        'en' => 'CSV Format',
        'group' => 'tools'
    ],
    'tools.cad_library.json_format' => [
        'vi' => 'Định dạng JSON',
        'en' => 'JSON Format',
        'group' => 'tools'
    ],
    'tools.cad_library.total_files' => [
        'vi' => 'Tổng số file',
        'en' => 'Total Files',
        'group' => 'tools'
    ],
    'tools.cad_library.total_downloads' => [
        'vi' => 'Tổng lượt tải',
        'en' => 'Total Downloads',
        'group' => 'tools'
    ],
    'tools.cad_library.active_users' => [
        'vi' => 'Người dùng hoạt động',
        'en' => 'Active Users',
        'group' => 'tools'
    ],
    'tools.cad_library.featured_files' => [
        'vi' => 'File nổi bật',
        'en' => 'Featured Files',
        'group' => 'tools'
    ],
    'tools.cad_library.view' => [
        'vi' => 'Xem',
        'en' => 'View',
        'group' => 'tools'
    ],
    'tools.cad_library.download' => [
        'vi' => 'Tải xuống',
        'en' => 'Download',
        'group' => 'tools'
    ],
    'tools.cad_library.login' => [
        'vi' => 'Đăng nhập',
        'en' => 'Login',
        'group' => 'tools'
    ],

    // Material Calculator
    'tools.material_calculator.title' => [
        'vi' => 'Máy tính chi phí vật liệu',
        'en' => 'Material Cost Calculator',
        'group' => 'tools'
    ],
    'tools.material_calculator.description' => [
        'vi' => 'Tính toán chi phí vật liệu cho các dự án kỹ thuật của bạn',
        'en' => 'Calculate material costs for your engineering projects',
        'group' => 'tools'
    ],
    'tools.material_calculator.view_materials' => [
        'vi' => 'Xem vật liệu',
        'en' => 'View Materials',
        'group' => 'tools'
    ],
    'tools.material_calculator.compare_materials' => [
        'vi' => 'So sánh vật liệu',
        'en' => 'Compare Materials',
        'group' => 'tools'
    ],
    'tools.material_calculator.parameters' => [
        'vi' => 'Thông số vật liệu',
        'en' => 'Material Parameters',
        'group' => 'tools'
    ],

    // Process Calculator
    'tools.process_calculator.title' => [
        'vi' => 'Máy tính quy trình sản xuất',
        'en' => 'Manufacturing Process Calculator',
        'group' => 'tools'
    ],
    'tools.process_calculator.description' => [
        'vi' => 'Tính toán thời gian, chi phí và thông số cho các quy trình sản xuất khác nhau',
        'en' => 'Calculate time, cost, and parameters for various manufacturing processes',
        'group' => 'tools'
    ],
    'tools.process_calculator.view_processes' => [
        'vi' => 'Xem tất cả quy trình',
        'en' => 'View All Processes',
        'group' => 'tools'
    ],
    'tools.process_calculator.compare_processes' => [
        'vi' => 'So sánh quy trình',
        'en' => 'Compare Processes',
        'group' => 'tools'
    ],
    'tools.process_calculator.parameters' => [
        'vi' => 'Thông số quy trình',
        'en' => 'Process Parameters',
        'group' => 'tools'
    ],

    // Common breadcrumb
    'tools.breadcrumb.home' => [
        'vi' => 'Trang chủ',
        'en' => 'Home',
        'group' => 'tools'
    ],
    'tools.breadcrumb.materials_database' => [
        'vi' => 'Cơ sở dữ liệu vật liệu',
        'en' => 'Materials Database',
        'group' => 'tools'
    ],
    'tools.breadcrumb.cost_calculator' => [
        'vi' => 'Máy tính chi phí',
        'en' => 'Cost Calculator',
        'group' => 'tools'
    ],
    'tools.breadcrumb.manufacturing_processes' => [
        'vi' => 'Quy trình sản xuất',
        'en' => 'Manufacturing Processes',
        'group' => 'tools'
    ],
    'tools.breadcrumb.process_calculator' => [
        'vi' => 'Máy tính quy trình',
        'en' => 'Process Calculator',
        'group' => 'tools'
    ],

    // Common actions
    'tools.actions.search' => [
        'vi' => 'Tìm kiếm',
        'en' => 'Search',
        'group' => 'tools'
    ],
    'tools.actions.filter' => [
        'vi' => 'Lọc',
        'en' => 'Filter',
        'group' => 'tools'
    ],
    'tools.actions.sort' => [
        'vi' => 'Sắp xếp',
        'en' => 'Sort',
        'group' => 'tools'
    ],
    'tools.actions.reset' => [
        'vi' => 'Đặt lại',
        'en' => 'Reset',
        'group' => 'tools'
    ],
    'tools.actions.calculate' => [
        'vi' => 'Tính toán',
        'en' => 'Calculate',
        'group' => 'tools'
    ],
    'tools.actions.clear' => [
        'vi' => 'Xóa',
        'en' => 'Clear',
        'group' => 'tools'
    ],
    'tools.actions.save' => [
        'vi' => 'Lưu',
        'en' => 'Save',
        'group' => 'tools'
    ],
    'tools.actions.load' => [
        'vi' => 'Tải',
        'en' => 'Load',
        'group' => 'tools'
    ]
];

$totalAdded = 0;
$totalSkipped = 0;

foreach ($translationKeys as $key => $data) {
    echo "\n📝 Processing key: {$key}\n";

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
echo "\n🚀 Tools translation keys have been added successfully!\n";
echo "Now you can update the view files to use these translation keys.\n";
