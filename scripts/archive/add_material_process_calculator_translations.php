<?php

/**
 * Script to add translation keys for Material Calculator and Process Calculator pages
 * Specifically for /tools/material-calculator and /manufacturing/processes/calculator
 *
 * Usage: php scripts/add_material_process_calculator_translations.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Translation;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Adding Translation Keys for Calculator Pages\n";
echo "===============================================\n\n";

// Translation keys for Material Calculator
$materialCalculatorKeys = [
    // Page title and meta
    'tools.material_calculator.title' => [
        'vi' => 'Máy Tính Chi Phí Vật Liệu',
        'en' => 'Material Cost Calculator'
    ],
    'tools.material_calculator.description' => [
        'vi' => 'Tính toán chi phí vật liệu, số lượng và ước tính dự án một cách chính xác',
        'en' => 'Calculate material costs, quantities, and project estimates with precision'
    ],

    // Buttons
    'tools.material_calculator.save_calculation' => [
        'vi' => 'Lưu Tính Toán',
        'en' => 'Save Calculation'
    ],
    'tools.material_calculator.export_results' => [
        'vi' => 'Xuất Kết Quả',
        'en' => 'Export Results'
    ],
    'tools.material_calculator.history' => [
        'vi' => 'Lịch Sử',
        'en' => 'History'
    ],
    'tools.material_calculator.calculate' => [
        'vi' => 'Tính Toán Chi Phí Vật Liệu',
        'en' => 'Calculate Material Cost'
    ],

    // Form sections
    'tools.material_calculator.parameters' => [
        'vi' => 'Thông Số Tính Toán Vật Liệu',
        'en' => 'Material Calculation Parameters'
    ],
    'tools.material_calculator.material_type' => [
        'vi' => 'Loại Vật Liệu',
        'en' => 'Material Type'
    ],
    'tools.material_calculator.select_material' => [
        'vi' => 'Chọn Vật Liệu',
        'en' => 'Select Material'
    ],
    'tools.material_calculator.material_grade' => [
        'vi' => 'Cấp/Thông Số Vật Liệu',
        'en' => 'Material Grade/Specification'
    ],
    'tools.material_calculator.select_grade' => [
        'vi' => 'Chọn Cấp',
        'en' => 'Select Grade'
    ],

    // Material types
    'tools.material_calculator.carbon_steel' => [
        'vi' => 'Thép Carbon',
        'en' => 'Carbon Steel'
    ],
    'tools.material_calculator.stainless_steel' => [
        'vi' => 'Thép Không Gỉ',
        'en' => 'Stainless Steel'
    ],
    'tools.material_calculator.aluminum' => [
        'vi' => 'Nhôm',
        'en' => 'Aluminum'
    ],
    'tools.material_calculator.copper' => [
        'vi' => 'Đồng',
        'en' => 'Copper'
    ],
    'tools.material_calculator.brass' => [
        'vi' => 'Đồng Thau',
        'en' => 'Brass'
    ],
    'tools.material_calculator.titanium' => [
        'vi' => 'Titan',
        'en' => 'Titanium'
    ],
    'tools.material_calculator.abs_plastic' => [
        'vi' => 'Nhựa ABS',
        'en' => 'ABS Plastic'
    ],
    'tools.material_calculator.pvc' => [
        'vi' => 'PVC',
        'en' => 'PVC'
    ],

    // Dimensions section
    'tools.material_calculator.dimensions_quantities' => [
        'vi' => 'Kích Thước & Số Lượng',
        'en' => 'Dimensions & Quantities'
    ],
    'tools.material_calculator.length_mm' => [
        'vi' => 'Chiều Dài (mm)',
        'en' => 'Length (mm)'
    ],
    'tools.material_calculator.width_mm' => [
        'vi' => 'Chiều Rộng (mm)',
        'en' => 'Width (mm)'
    ],
    'tools.material_calculator.thickness_mm' => [
        'vi' => 'Độ Dày (mm)',
        'en' => 'Thickness (mm)'
    ],
    'tools.material_calculator.quantity' => [
        'vi' => 'Số Lượng',
        'en' => 'Quantity'
    ],

    // Shape section
    'tools.material_calculator.shape_form' => [
        'vi' => 'Hình Dạng & Dạng Thức',
        'en' => 'Shape & Form'
    ],
    'tools.material_calculator.shape_type' => [
        'vi' => 'Loại Hình Dạng',
        'en' => 'Shape Type'
    ],
    'tools.material_calculator.rectangular_sheet' => [
        'vi' => 'Hình Chữ Nhật/Tấm',
        'en' => 'Rectangular/Sheet'
    ],
    'tools.material_calculator.round_cylinder' => [
        'vi' => 'Tròn/Hình Trụ',
        'en' => 'Round/Cylinder'
    ],
    'tools.material_calculator.tube_pipe' => [
        'vi' => 'Ống/Đường Ống',
        'en' => 'Tube/Pipe'
    ],
    'tools.material_calculator.angle_l_shape' => [
        'vi' => 'Góc/Hình L',
        'en' => 'Angle/L-Shape'
    ],
    'tools.material_calculator.channel_u_shape' => [
        'vi' => 'Rãnh/Hình U',
        'en' => 'Channel/U-Shape'
    ],
    'tools.material_calculator.i_beam_h_beam' => [
        'vi' => 'Dầm I/Dầm H',
        'en' => 'I-Beam/H-Beam'
    ],
    'tools.material_calculator.form_type' => [
        'vi' => 'Loại Dạng Thức',
        'en' => 'Form Type'
    ],
    'tools.material_calculator.raw_material' => [
        'vi' => 'Vật Liệu Thô',
        'en' => 'Raw Material'
    ],
    'tools.material_calculator.machined' => [
        'vi' => 'Đã Gia Công',
        'en' => 'Machined'
    ],
    'tools.material_calculator.fabricated' => [
        'vi' => 'Đã Chế Tạo',
        'en' => 'Fabricated'
    ],
    'tools.material_calculator.finished_product' => [
        'vi' => 'Sản Phẩm Hoàn Thiện',
        'en' => 'Finished Product'
    ],

    // Additional parameters
    'tools.material_calculator.additional_parameters' => [
        'vi' => 'Thông Số Bổ Sung',
        'en' => 'Additional Parameters'
    ],
    'tools.material_calculator.waste_percentage' => [
        'vi' => 'Tỷ Lệ Phế Liệu (%)',
        'en' => 'Waste Percentage (%)'
    ],
    'tools.material_calculator.labor_cost_vnd' => [
        'vi' => 'Chi Phí Nhân Công (VND/giờ)',
        'en' => 'Labor Cost (VND/hour)'
    ],
    'tools.material_calculator.processing_time' => [
        'vi' => 'Thời Gian Gia Công (giờ)',
        'en' => 'Processing Time (hours)'
    ],

    // Currency and units
    'tools.material_calculator.currency' => [
        'vi' => 'Tiền Tệ',
        'en' => 'Currency'
    ],
    'tools.material_calculator.vnd' => [
        'vi' => 'Đồng Việt Nam (VND)',
        'en' => 'Vietnamese Dong (VND)'
    ],
    'tools.material_calculator.usd' => [
        'vi' => 'Đô La Mỹ (USD)',
        'en' => 'US Dollar (USD)'
    ],
    'tools.material_calculator.eur' => [
        'vi' => 'Euro (EUR)',
        'en' => 'Euro (EUR)'
    ],
    'tools.material_calculator.unit_system' => [
        'vi' => 'Hệ Đơn Vị',
        'en' => 'Unit System'
    ],
    'tools.material_calculator.metric' => [
        'vi' => 'Hệ Mét (mm, kg)',
        'en' => 'Metric (mm, kg)'
    ],
    'tools.material_calculator.imperial' => [
        'vi' => 'Hệ Anh (in, lb)',
        'en' => 'Imperial (in, lb)'
    ],

    // Results section
    'tools.material_calculator.calculation_results' => [
        'vi' => 'Kết Quả Tính Toán',
        'en' => 'Calculation Results'
    ],
    'tools.material_calculator.enter_parameters' => [
        'vi' => 'Nhập thông số vật liệu và nhấn tính toán để xem kết quả',
        'en' => 'Enter material parameters and click calculate to see results'
    ],

    // Material Properties section
    'tools.material_calculator.material_properties' => [
        'vi' => 'Thuộc Tính Vật Liệu',
        'en' => 'Material Properties'
    ],
    'tools.material_calculator.select_material_properties' => [
        'vi' => 'Chọn vật liệu để xem thuộc tính',
        'en' => 'Select a material to view its properties'
    ],

    // Quick Calculations section
    'tools.material_calculator.quick_calculations' => [
        'vi' => 'Tính Toán Nhanh',
        'en' => 'Quick Calculations'
    ],
    'tools.material_calculator.steel_sheet' => [
        'vi' => 'Tấm Thép',
        'en' => 'Steel Sheet'
    ],
    'tools.material_calculator.steel_pipe' => [
        'vi' => 'Ống Thép',
        'en' => 'Steel Pipe'
    ],
    'tools.material_calculator.i_beam' => [
        'vi' => 'Dầm I',
        'en' => 'I-Beam'
    ],
    'tools.material_calculator.aluminum_block' => [
        'vi' => 'Khối Nhôm',
        'en' => 'Aluminum Block'
    ],

    // Recent Calculations section
    'tools.material_calculator.recent_calculations' => [
        'vi' => 'Tính Toán Gần Đây',
        'en' => 'Recent Calculations'
    ],
    'tools.material_calculator.date' => [
        'vi' => 'Ngày',
        'en' => 'Date'
    ],
    'tools.material_calculator.material' => [
        'vi' => 'Vật Liệu',
        'en' => 'Material'
    ],
    'tools.material_calculator.dimensions' => [
        'vi' => 'Kích Thước',
        'en' => 'Dimensions'
    ],
    'tools.material_calculator.total_cost' => [
        'vi' => 'Tổng Chi Phí',
        'en' => 'Total Cost'
    ],
    'tools.material_calculator.actions' => [
        'vi' => 'Thao Tác',
        'en' => 'Actions'
    ],
    'tools.material_calculator.no_calculations' => [
        'vi' => 'Chưa có tính toán nào',
        'en' => 'No calculations yet'
    ],
];

// Translation keys for Manufacturing Process Calculator
$processCalculatorKeys = [
    // Missing keys from the process calculator
    'tools.process_calculator.manufacturing_process' => [
        'vi' => 'Quy Trình Sản Xuất',
        'en' => 'Manufacturing Process'
    ],
    'tools.process_calculator.select_process' => [
        'vi' => 'Chọn một quy trình...',
        'en' => 'Select a process...'
    ],
    'tools.process_calculator.material_selection' => [
        'vi' => 'Lựa Chọn Vật Liệu',
        'en' => 'Material Selection'
    ],
    'tools.process_calculator.select_material' => [
        'vi' => 'Chọn vật liệu...',
        'en' => 'Select material...'
    ],
    'tools.process_calculator.dimensions' => [
        'vi' => 'Kích Thước',
        'en' => 'Dimensions'
    ],
    'tools.process_calculator.length_mm' => [
        'vi' => 'Chiều Dài (mm)',
        'en' => 'Length (mm)'
    ],
    'tools.process_calculator.width_mm' => [
        'vi' => 'Chiều Rộng (mm)',
        'en' => 'Width (mm)'
    ],
    'tools.process_calculator.height_mm' => [
        'vi' => 'Chiều Cao (mm)',
        'en' => 'Height (mm)'
    ],
    'tools.process_calculator.quantity' => [
        'vi' => 'Số Lượng',
        'en' => 'Quantity'
    ],
    'tools.process_calculator.process_parameters' => [
        'vi' => 'Thông Số Quy Trình',
        'en' => 'Process Parameters'
    ],
    'tools.process_calculator.cutting_speed' => [
        'vi' => 'Tốc Độ Cắt (m/min)',
        'en' => 'Cutting Speed (m/min)'
    ],
    'tools.process_calculator.feed_rate' => [
        'vi' => 'Tốc Độ Tiến Dao (mm/rev)',
        'en' => 'Feed Rate (mm/rev)'
    ],
    'tools.process_calculator.depth_of_cut' => [
        'vi' => 'Độ Sâu Cắt (mm)',
        'en' => 'Depth of Cut (mm)'
    ],
    'tools.process_calculator.cost_parameters' => [
        'vi' => 'Thông Số Chi Phí',
        'en' => 'Cost Parameters'
    ],
    'tools.process_calculator.machine_rate' => [
        'vi' => 'Giá Máy (VND/giờ)',
        'en' => 'Machine Rate (VND/hour)'
    ],
    'tools.process_calculator.labor_rate' => [
        'vi' => 'Giá Nhân Công (VND/giờ)',
        'en' => 'Labor Rate (VND/hour)'
    ],
    'tools.process_calculator.material_cost' => [
        'vi' => 'Chi Phí Vật Liệu (VND/kg)',
        'en' => 'Material Cost (VND/kg)'
    ],
    'tools.process_calculator.calculate_process' => [
        'vi' => 'Tính Toán Quy Trình',
        'en' => 'Calculate Process'
    ],
    'tools.process_calculator.quick_calculations' => [
        'vi' => 'Tính Toán Nhanh',
        'en' => 'Quick Calculations'
    ],
    'tools.process_calculator.turning' => [
        'vi' => 'Tiện',
        'en' => 'Turning'
    ],
    'tools.process_calculator.milling' => [
        'vi' => 'Phay',
        'en' => 'Milling'
    ],
    'tools.process_calculator.drilling' => [
        'vi' => 'Khoan',
        'en' => 'Drilling'
    ],
    'tools.process_calculator.grinding' => [
        'vi' => 'Mài',
        'en' => 'Grinding'
    ],
    'tools.process_calculator.process_tips' => [
        'vi' => 'Mẹo Quy Trình',
        'en' => 'Process Tips'
    ],
    'tools.process_calculator.calculation_history' => [
        'vi' => 'Lịch Sử Tính Toán',
        'en' => 'Calculation History'
    ],
    'tools.process_calculator.no_calculations' => [
        'vi' => 'Chưa có tính toán nào được thực hiện',
        'en' => 'No calculations performed yet'
    ],

    // Additional missing keys for Process Calculator
    'tools.process_calculator.calculation_results' => [
        'vi' => 'Kết Quả Tính Toán',
        'en' => 'Calculation Results'
    ],
    'tools.process_calculator.enter_parameters' => [
        'vi' => 'Nhập thông số và nhấn tính toán để xem kết quả',
        'en' => 'Enter parameters and click calculate to see results'
    ],
    'tools.process_calculator.clear_history' => [
        'vi' => 'Xóa Lịch Sử',
        'en' => 'Clear History'
    ],
    'tools.process_calculator.machine_time' => [
        'vi' => 'Thời Gian Máy',
        'en' => 'Machine Time'
    ],
    'tools.process_calculator.total_time' => [
        'vi' => 'Tổng Thời Gian',
        'en' => 'Total Time'
    ],
    'tools.process_calculator.total_cost_result' => [
        'vi' => 'Tổng Chi Phí',
        'en' => 'Total Cost'
    ],
    'tools.process_calculator.cost_breakdown' => [
        'vi' => 'Chi Tiết Chi Phí',
        'en' => 'Cost Breakdown'
    ],
    'tools.process_calculator.machine_cost' => [
        'vi' => 'Chi Phí Máy',
        'en' => 'Machine Cost'
    ],
    'tools.process_calculator.labor_cost_result' => [
        'vi' => 'Chi Phí Nhân Công',
        'en' => 'Labor Cost'
    ],
    'tools.process_calculator.material_cost_result' => [
        'vi' => 'Chi Phí Vật Liệu',
        'en' => 'Material Cost'
    ],
];

// Combine all keys
$allKeys = array_merge($materialCalculatorKeys, $processCalculatorKeys);

echo "📝 Found " . count($allKeys) . " translation keys to add\n\n";

// Add keys to database
$addedCount = 0;
$skippedCount = 0;

foreach ($allKeys as $key => $translations) {
    foreach ($translations as $locale => $content) {
        // Check if key already exists
        $existing = Translation::where('key', $key)
                              ->where('locale', $locale)
                              ->first();

        if ($existing) {
            echo "⏭️  Skipped: {$key} ({$locale}) - already exists\n";
            $skippedCount++;
            continue;
        }

        // Create new translation
        $translation = Translation::create([
            'key' => $key,
            'content' => $content,
            'locale' => $locale,
            'group_name' => 'tools',
            'is_active' => true,
            'created_by' => 1, // System user
            'updated_by' => 1,
        ]);

        echo "✅ Added: {$key} ({$locale})\n";
        $addedCount++;
    }
}

echo "\n📊 Summary:\n";
echo "- Added: {$addedCount} translations\n";
echo "- Skipped: {$skippedCount} translations\n";
echo "- Total processed: " . ($addedCount + $skippedCount) . " translations\n\n";

echo "🎯 Next steps:\n";
echo "1. Update blade templates to use translation keys\n";
echo "2. Test the pages to ensure translations work correctly\n";
echo "3. Check https://mechamap.test/translations for management\n\n";

echo "✅ Translation keys added successfully!\n";
