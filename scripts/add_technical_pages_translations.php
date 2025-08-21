<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Adding Translation Keys for Technical Pages\n";
echo "===============================================\n\n";

// Translation keys for Materials, Standards, and Manufacturing Processes pages
$translations = [
    // Materials Database Page
    'technical.materials.title' => [
        'vi' => 'Cơ sở dữ liệu vật liệu',
        'en' => 'Materials Database'
    ],
    'technical.materials.description' => [
        'vi' => 'Cơ sở dữ liệu toàn diện về vật liệu kỹ thuật với thuộc tính và thông số kỹ thuật',
        'en' => 'Comprehensive database of engineering materials with properties and specifications'
    ],
    'technical.materials.cost_calculator' => [
        'vi' => 'Máy tính chi phí',
        'en' => 'Cost Calculator'
    ],
    'technical.materials.compare_materials' => [
        'vi' => 'So sánh vật liệu',
        'en' => 'Compare Materials'
    ],
    'technical.materials.export' => [
        'vi' => 'Xuất dữ liệu',
        'en' => 'Export'
    ],
    'technical.materials.export_csv' => [
        'vi' => 'Xuất CSV',
        'en' => 'Export CSV'
    ],
    'technical.materials.export_excel' => [
        'vi' => 'Xuất Excel',
        'en' => 'Export Excel'
    ],
    'technical.materials.export_pdf' => [
        'vi' => 'Xuất PDF',
        'en' => 'Export PDF'
    ],
    'technical.materials.search_materials' => [
        'vi' => 'Tìm kiếm vật liệu',
        'en' => 'Search Materials'
    ],
    'technical.materials.search_placeholder' => [
        'vi' => 'Tìm theo tên, mã hoặc mô tả...',
        'en' => 'Search by name, code, or description...'
    ],
    'technical.materials.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category'
    ],
    'technical.materials.all_categories' => [
        'vi' => 'Tất cả danh mục',
        'en' => 'All Categories'
    ],
    'technical.materials.material_type' => [
        'vi' => 'Loại vật liệu',
        'en' => 'Material Type'
    ],
    'technical.materials.all_types' => [
        'vi' => 'Tất cả loại',
        'en' => 'All Types'
    ],
    'technical.materials.type' => [
        'vi' => 'Loại',
        'en' => 'Type'
    ],
    'technical.materials.properties' => [
        'vi' => 'Thuộc tính',
        'en' => 'Properties'
    ],
    'technical.materials.specifications' => [
        'vi' => 'Thông số kỹ thuật',
        'en' => 'Specifications'
    ],
    'technical.materials.compare' => [
        'vi' => 'So sánh',
        'en' => 'Compare'
    ],
    'technical.materials.view_details' => [
        'vi' => 'Xem chi tiết',
        'en' => 'View Details'
    ],
    'technical.materials.no_materials' => [
        'vi' => 'Không tìm thấy vật liệu nào phù hợp với tiêu chí tìm kiếm.',
        'en' => 'No materials found matching your search criteria.'
    ],
    'technical.materials.compare_button' => [
        'vi' => 'So sánh',
        'en' => 'Compare'
    ],

    // Standards Database Page
    'technical.standards.title' => [
        'vi' => 'Cơ sở dữ liệu tiêu chuẩn',
        'en' => 'Standards Database'
    ],
    'technical.standards.description' => [
        'vi' => 'Thư viện toàn diện các tiêu chuẩn kỹ thuật quốc tế và thông số kỹ thuật',
        'en' => 'Comprehensive library of international engineering standards and specifications'
    ],
    'technical.standards.compare_standards' => [
        'vi' => 'So sánh tiêu chuẩn',
        'en' => 'Compare Standards'
    ],
    'technical.standards.compliance_checker' => [
        'vi' => 'Kiểm tra tuân thủ',
        'en' => 'Compliance Checker'
    ],
    'technical.standards.export' => [
        'vi' => 'Xuất dữ liệu',
        'en' => 'Export'
    ],
    'technical.standards.organizations' => [
        'vi' => 'Tổ chức',
        'en' => 'Organizations'
    ],
    'technical.standards.categories' => [
        'vi' => 'Danh mục',
        'en' => 'Categories'
    ],
    'technical.standards.active_standards' => [
        'vi' => 'Tiêu chuẩn hiệu lực',
        'en' => 'Active Standards'
    ],
    'technical.standards.search_standards' => [
        'vi' => 'Tìm kiếm tiêu chuẩn',
        'en' => 'Search Standards'
    ],
    'technical.standards.search_placeholder' => [
        'vi' => 'Tìm theo số tiêu chuẩn, tiêu đề hoặc mô tả...',
        'en' => 'Search by standard number, title, or description...'
    ],
    'technical.standards.organization' => [
        'vi' => 'Tổ chức',
        'en' => 'Organization'
    ],
    'technical.standards.all_organizations' => [
        'vi' => 'Tất cả tổ chức',
        'en' => 'All Organizations'
    ],
    'technical.standards.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category'
    ],
    'technical.standards.all_categories' => [
        'vi' => 'Tất cả danh mục',
        'en' => 'All Categories'
    ],
    'technical.standards.status' => [
        'vi' => 'Trạng thái',
        'en' => 'Status'
    ],
    'technical.standards.active' => [
        'vi' => 'Hiệu lực',
        'en' => 'Active'
    ],
    'technical.standards.last_updated' => [
        'vi' => 'Cập nhật lần cuối',
        'en' => 'Last Updated'
    ],
    'technical.standards.compliance_rate' => [
        'vi' => 'Tỷ lệ tuân thủ',
        'en' => 'Compliance Rate'
    ],
    'technical.standards.view_standard' => [
        'vi' => 'Xem tiêu chuẩn',
        'en' => 'View Standard'
    ],
    'technical.standards.check_compliance' => [
        'vi' => 'Kiểm tra tuân thủ',
        'en' => 'Check Compliance'
    ],
    'technical.standards.standards_organizations' => [
        'vi' => 'Tổ chức tiêu chuẩn',
        'en' => 'Standards Organizations'
    ],
    'technical.standards.compare_button' => [
        'vi' => 'So sánh',
        'en' => 'Compare'
    ],
    'technical.standards.compliance_alert' => [
        'vi' => 'Trình kiểm tra tuân thủ sẽ phân tích quy trình của bạn theo tiêu chuẩn này',
        'en' => 'Compliance checker will analyze your processes against this standard'
    ],

    // Manufacturing Processes Page
    'technical.processes.title' => [
        'vi' => 'Quy trình sản xuất',
        'en' => 'Manufacturing Processes'
    ],
    'technical.processes.description' => [
        'vi' => 'Hướng dẫn toàn diện về quy trình và kỹ thuật sản xuất',
        'en' => 'Comprehensive guide to manufacturing processes and techniques'
    ],
    'technical.processes.process_selector' => [
        'vi' => 'Bộ chọn quy trình',
        'en' => 'Process Selector'
    ],
    'technical.processes.cost_calculator' => [
        'vi' => 'Máy tính chi phí',
        'en' => 'Cost Calculator'
    ],
    'technical.processes.compare_processes' => [
        'vi' => 'So sánh quy trình',
        'en' => 'Compare Processes'
    ],
    'technical.processes.export' => [
        'vi' => 'Xuất dữ liệu',
        'en' => 'Export'
    ],
    'technical.processes.search_processes' => [
        'vi' => 'Tìm kiếm quy trình',
        'en' => 'Search Processes'
    ],
    'technical.processes.search_placeholder' => [
        'vi' => 'Tìm theo tên, danh mục hoặc mô tả...',
        'en' => 'Search by name, category, or description...'
    ],
    'technical.processes.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category'
    ],
    'technical.processes.all_categories' => [
        'vi' => 'Tất cả danh mục',
        'en' => 'All Categories'
    ],
    'technical.processes.process_type' => [
        'vi' => 'Loại quy trình',
        'en' => 'Process Type'
    ],
    'technical.processes.all_types' => [
        'vi' => 'Tất cả loại',
        'en' => 'All Types'
    ],
    'technical.processes.sort_by' => [
        'vi' => 'Sắp xếp theo:',
        'en' => 'Sort by:'
    ],
    'technical.processes.name' => [
        'vi' => 'Tên',
        'en' => 'Name'
    ],
    'technical.processes.cost' => [
        'vi' => 'Chi phí',
        'en' => 'Cost'
    ],
    'technical.processes.production_rate' => [
        'vi' => 'Tốc độ sản xuất',
        'en' => 'Production Rate'
    ],
    'technical.processes.setup_time' => [
        'vi' => 'Thời gian thiết lập',
        'en' => 'Setup Time'
    ],
    'technical.processes.cost_per_hour' => [
        'vi' => 'Chi phí/giờ',
        'en' => 'Cost/Hour'
    ],
    'technical.processes.equipment' => [
        'vi' => 'Thiết bị',
        'en' => 'Equipment'
    ],
    'technical.processes.applications' => [
        'vi' => 'Ứng dụng',
        'en' => 'Applications'
    ],
    'technical.processes.materials' => [
        'vi' => 'Vật liệu',
        'en' => 'Materials'
    ],
    'technical.processes.view_details' => [
        'vi' => 'Xem chi tiết',
        'en' => 'View Details'
    ],
    'technical.processes.select_process' => [
        'vi' => 'Chọn quy trình',
        'en' => 'Select Process'
    ],
    'technical.processes.no_processes' => [
        'vi' => 'Không tìm thấy quy trình nào phù hợp với tiêu chí tìm kiếm.',
        'en' => 'No processes found matching your search criteria.'
    ],

    // Additional Materials Database Keys
    'technical.materials.density' => [
        'vi' => 'Mật độ',
        'en' => 'Density'
    ],
    'technical.materials.tensile_strength' => [
        'vi' => 'Độ bền kéo',
        'en' => 'Tensile Strength'
    ],
    'technical.materials.yield_strength' => [
        'vi' => 'Giới hạn chảy',
        'en' => 'Yield Strength'
    ],
    'technical.materials.cost' => [
        'vi' => 'Chi phí',
        'en' => 'Cost'
    ],
    'technical.materials.showing_results' => [
        'vi' => 'Hiển thị',
        'en' => 'Showing'
    ],
    'technical.materials.to' => [
        'vi' => 'đến',
        'en' => 'to'
    ],
    'technical.materials.of' => [
        'vi' => 'trong tổng số',
        'en' => 'of'
    ],
    'technical.materials.materials' => [
        'vi' => 'vật liệu',
        'en' => 'materials'
    ],
    'technical.materials.sort_by' => [
        'vi' => 'Sắp xếp theo:',
        'en' => 'Sort by:'
    ],
    'technical.materials.name' => [
        'vi' => 'Tên',
        'en' => 'Name'
    ],
    'technical.materials.code' => [
        'vi' => 'Mã',
        'en' => 'Code'
    ],

    // Additional Standards Database Keys
    'technical.standards.standards_available' => [
        'vi' => 'Tiêu chuẩn có sẵn',
        'en' => 'Standards Available'
    ],
    'technical.standards.organizations_count' => [
        'vi' => 'Tổ chức',
        'en' => 'Organizations'
    ],
    'technical.standards.industries_covered' => [
        'vi' => 'Ngành được bao phủ',
        'en' => 'Industries Covered'
    ],
    'technical.standards.search_standards_placeholder' => [
        'vi' => 'Tìm kiếm tiêu chuẩn',
        'en' => 'Search Standards'
    ],
    'technical.standards.industry' => [
        'vi' => 'Ngành',
        'en' => 'Industry'
    ],
    'technical.standards.compliance_level' => [
        'vi' => 'Mức độ tuân thủ',
        'en' => 'Compliance Level'
    ],
    'technical.standards.key_requirements' => [
        'vi' => 'Yêu cầu chính:',
        'en' => 'Key Requirements:'
    ],
    'technical.standards.documentation_record_keeping' => [
        'vi' => 'Lưu trữ tài liệu và hồ sơ',
        'en' => 'Documentation and record keeping'
    ],
    'technical.standards.quality_control_procedures' => [
        'vi' => 'Quy trình kiểm soát chất lượng',
        'en' => 'Quality control procedures'
    ],
    'technical.standards.testing_validation_methods' => [
        'vi' => 'Phương pháp kiểm tra và xác thực',
        'en' => 'Testing and validation methods'
    ],
    'technical.standards.view_details' => [
        'vi' => 'Xem chi tiết',
        'en' => 'View Details'
    ],
    'technical.standards.check' => [
        'vi' => 'Kiểm tra',
        'en' => 'Check'
    ],
    'technical.standards.standards_available_count' => [
        'vi' => 'tiêu chuẩn có sẵn',
        'en' => 'standards available'
    ],

    // Additional Manufacturing Processes Keys
    'technical.processes.search_processes_placeholder' => [
        'vi' => 'Tìm kiếm quy trình',
        'en' => 'Search Processes'
    ],
    'technical.processes.skill_level' => [
        'vi' => 'Mức độ kỹ năng',
        'en' => 'Skill Level'
    ],
    'technical.processes.all_levels' => [
        'vi' => 'Tất cả mức độ',
        'en' => 'All Levels'
    ],
    'technical.processes.basic' => [
        'vi' => 'Cơ bản',
        'en' => 'Basic'
    ],
    'technical.processes.intermediate' => [
        'vi' => 'Trung cấp',
        'en' => 'Intermediate'
    ],
    'technical.processes.advanced' => [
        'vi' => 'Nâng cao',
        'en' => 'Advanced'
    ],
    'technical.processes.expert' => [
        'vi' => 'Chuyên gia',
        'en' => 'Expert'
    ],
    'technical.processes.showing_results' => [
        'vi' => 'Hiển thị',
        'en' => 'Showing'
    ],
    'technical.processes.to' => [
        'vi' => 'đến',
        'en' => 'to'
    ],
    'technical.processes.of' => [
        'vi' => 'trong tổng số',
        'en' => 'of'
    ],
    'technical.processes.processes' => [
        'vi' => 'quy trình',
        'en' => 'processes'
    ],
    'technical.processes.cycle_time' => [
        'vi' => 'Thời gian chu kỳ',
        'en' => 'Cycle Time'
    ],
    'technical.processes.variable' => [
        'vi' => 'Biến đổi',
        'en' => 'Variable'
    ],
    'technical.processes.na' => [
        'vi' => 'Không có',
        'en' => 'N/A'
    ],
    'technical.processes.compare' => [
        'vi' => 'So sánh',
        'en' => 'Compare'
    ],
    'technical.processes.compare_button' => [
        'vi' => 'So sánh',
        'en' => 'Compare'
    ],
];

echo "📊 Found " . count($translations) . " translation keys to add\n\n";

$addedCount = 0;
$skippedCount = 0;

foreach ($translations as $key => $values) {
    foreach ($values as $locale => $content) {
        // Check if translation already exists
        $existing = DB::table('translations')
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();

        if ($existing) {
            echo "⏭️  Skipped: {$key} ({$locale}) - already exists\n";
            $skippedCount++;
            continue;
        }

        // Insert new translation
        DB::table('translations')->insert([
            'key' => $key,
            'locale' => $locale,
            'content' => $content,
            'group_name' => 'technical',
            'is_active' => true,
            'created_by' => 1, // System user
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ Added: {$key} ({$locale})\n";
        $addedCount++;
    }
}

echo "\n📊 Summary:\n";
echo "- Added: {$addedCount} translations\n";
echo "- Skipped: {$skippedCount} translations\n";
echo "- Total processed: " . ($addedCount + $skippedCount) . " translations\n\n";

echo "📋 Next steps:\n";
echo "1. Update blade templates to use translation keys\n";
echo "2. Test the pages to ensure translations work correctly\n";
echo "3. Check https://mechamap.test/translations for management\n\n";

echo "✅ Translation keys added successfully!\n";
