<?php

/**
 * Script để thêm hàng loạt translation keys cho Showcase
 * Chạy: php scripts/add_showcase_translation_keys.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "🚀 THÊM TRANSLATION KEYS CHO SHOWCASE\n";
echo str_repeat("=", 60) . "\n";

// Danh sách các translation keys cần thêm
$translationKeys = [
    // Project Types
    'showcase.project_type_design' => [
        'vi' => 'Thiết kế',
        'en' => 'Design',
        'group' => 'showcase'
    ],
    'showcase.project_type_manufacturing' => [
        'vi' => 'Sản xuất',
        'en' => 'Manufacturing',
        'group' => 'showcase'
    ],
    'showcase.project_type_analysis' => [
        'vi' => 'Phân tích',
        'en' => 'Analysis',
        'group' => 'showcase'
    ],
    'showcase.project_type_assembly' => [
        'vi' => 'Lắp ráp',
        'en' => 'Assembly',
        'group' => 'showcase'
    ],
    'showcase.project_type_prototype' => [
        'vi' => 'Nguyên mẫu',
        'en' => 'Prototype',
        'group' => 'showcase'
    ],
    'showcase.project_type_simulation' => [
        'vi' => 'Mô phỏng',
        'en' => 'Simulation',
        'group' => 'showcase'
    ],

    // Industries
    'showcase.industry_aerospace' => [
        'vi' => 'Hàng không vũ trụ',
        'en' => 'Aerospace',
        'group' => 'showcase'
    ],
    'showcase.industry_automotive' => [
        'vi' => 'Ô tô',
        'en' => 'Automotive',
        'group' => 'showcase'
    ],
    'showcase.industry_construction' => [
        'vi' => 'Xây dựng',
        'en' => 'Construction',
        'group' => 'showcase'
    ],
    'showcase.industry_energy' => [
        'vi' => 'Năng lượng',
        'en' => 'Energy',
        'group' => 'showcase'
    ],
    'showcase.industry_manufacturing' => [
        'vi' => 'Sản xuất',
        'en' => 'Manufacturing',
        'group' => 'showcase'
    ],
    'showcase.industry_medical' => [
        'vi' => 'Y tế',
        'en' => 'Medical',
        'group' => 'showcase'
    ],

    // Additional filter options
    'showcase.complexity_beginner' => [
        'vi' => 'Cơ bản',
        'en' => 'Beginner',
        'group' => 'showcase'
    ],
    'showcase.complexity_intermediate' => [
        'vi' => 'Trung bình',
        'en' => 'Intermediate',
        'group' => 'showcase'
    ],
    'showcase.complexity_advanced' => [
        'vi' => 'Nâng cao',
        'en' => 'Advanced',
        'group' => 'showcase'
    ],
    'showcase.complexity_expert' => [
        'vi' => 'Chuyên gia',
        'en' => 'Expert',
        'group' => 'showcase'
    ],

    // Software options
    'showcase.software_solidworks' => [
        'vi' => 'SolidWorks',
        'en' => 'SolidWorks',
        'group' => 'showcase'
    ],
    'showcase.software_autocad' => [
        'vi' => 'AutoCAD',
        'en' => 'AutoCAD',
        'group' => 'showcase'
    ],
    'showcase.software_inventor' => [
        'vi' => 'Inventor',
        'en' => 'Inventor',
        'group' => 'showcase'
    ],
    'showcase.software_fusion360' => [
        'vi' => 'Fusion 360',
        'en' => 'Fusion 360',
        'group' => 'showcase'
    ],
    'showcase.software_catia' => [
        'vi' => 'CATIA',
        'en' => 'CATIA',
        'group' => 'showcase'
    ],
    'showcase.software_nx' => [
        'vi' => 'NX',
        'en' => 'NX',
        'group' => 'showcase'
    ],
    'showcase.software_creo' => [
        'vi' => 'Creo',
        'en' => 'Creo',
        'group' => 'showcase'
    ],
    'showcase.software_ansys' => [
        'vi' => 'ANSYS',
        'en' => 'ANSYS',
        'group' => 'showcase'
    ],
    'showcase.software_matlab' => [
        'vi' => 'MATLAB',
        'en' => 'MATLAB',
        'group' => 'showcase'
    ],
    'showcase.software_other' => [
        'vi' => 'Khác',
        'en' => 'Other',
        'group' => 'showcase'
    ],

    // Additional UI elements
    'showcase.no_results_found' => [
        'vi' => 'Không tìm thấy kết quả nào',
        'en' => 'No results found',
        'group' => 'showcase'
    ],
    'showcase.try_different_filters' => [
        'vi' => 'Thử sử dụng bộ lọc khác',
        'en' => 'Try different filters',
        'group' => 'showcase'
    ],
    'showcase.loading_projects' => [
        'vi' => 'Đang tải dự án...',
        'en' => 'Loading projects...',
        'group' => 'showcase'
    ],
    'showcase.view_project' => [
        'vi' => 'Xem dự án',
        'en' => 'View Project',
        'group' => 'showcase'
    ],
    'showcase.download_files' => [
        'vi' => 'Tải file',
        'en' => 'Download Files',
        'group' => 'showcase'
    ],
    'showcase.view_tutorial' => [
        'vi' => 'Xem hướng dẫn',
        'en' => 'View Tutorial',
        'group' => 'showcase'
    ],
    'showcase.project_details' => [
        'vi' => 'Chi tiết dự án',
        'en' => 'Project Details',
        'group' => 'showcase'
    ],
    'showcase.created_by' => [
        'vi' => 'Tạo bởi',
        'en' => 'Created by',
        'group' => 'showcase'
    ],
    'showcase.created_on' => [
        'vi' => 'Tạo ngày',
        'en' => 'Created on',
        'group' => 'showcase'
    ],
    'showcase.last_updated' => [
        'vi' => 'Cập nhật lần cuối',
        'en' => 'Last updated',
        'group' => 'showcase'
    ],
    'showcase.project_rating' => [
        'vi' => 'Đánh giá dự án',
        'en' => 'Project Rating',
        'group' => 'showcase'
    ],
    'showcase.total_downloads' => [
        'vi' => 'Tổng lượt tải',
        'en' => 'Total Downloads',
        'group' => 'showcase'
    ],
    'showcase.total_views' => [
        'vi' => 'Tổng lượt xem',
        'en' => 'Total Views',
        'group' => 'showcase'
    ]
];

// Thống kê
$totalAdded = 0;
$totalSkipped = 0;
$errors = [];

echo "📝 Bắt đầu thêm " . count($translationKeys) . " translation keys...\n\n";

foreach ($translationKeys as $key => $data) {
    echo "🔑 Xử lý key: {$key}\n";
    
    try {
        // Kiểm tra key đã tồn tại chưa
        $existingVi = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'vi')
            ->first();
            
        $existingEn = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'en')
            ->first();
        
        if ($existingVi && $existingEn) {
            echo "   ⏭️ Bỏ qua: Key đã tồn tại\n";
            $totalSkipped++;
            continue;
        }
        
        // Thêm bản dịch tiếng Việt
        if (!$existingVi) {
            DB::table('translations')->insert([
                'key' => $key,
                'content' => $data['vi'],
                'locale' => 'vi',
                'group_name' => $data['group'],
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            echo "   ✅ Thêm VI: {$data['vi']}\n";
            $totalAdded++;
        }
        
        // Thêm bản dịch tiếng Anh
        if (!$existingEn) {
            DB::table('translations')->insert([
                'key' => $key,
                'content' => $data['en'],
                'locale' => 'en',
                'group_name' => $data['group'],
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            echo "   ✅ Thêm EN: {$data['en']}\n";
            $totalAdded++;
        }
        
    } catch (Exception $e) {
        echo "   ❌ Lỗi: " . $e->getMessage() . "\n";
        $errors[] = "Key '{$key}': " . $e->getMessage();
    }
    
    echo "\n";
}

// Tóm tắt kết quả
echo str_repeat("=", 60) . "\n";
echo "📊 TÓM TẮT KẾT QUẢ:\n";
echo "   ✅ Đã thêm: {$totalAdded} translation entries\n";
echo "   ⏭️ Đã bỏ qua: {$totalSkipped} entries đã tồn tại\n";

if (!empty($errors)) {
    echo "   ❌ Lỗi: " . count($errors) . " entries\n";
    echo "\nCHI TIẾT LỖI:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}

echo "\n🎉 HOÀN THÀNH!\n";
echo "Bạn có thể kiểm tra kết quả tại: https://mechamap.test/translations\n";
