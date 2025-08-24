<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHÂN TÍCH BẢNG marketplace_products_normalized ===\n\n";

try {
    // 1. Kiểm tra tổng số records
    $totalRecords = DB::select('SELECT COUNT(*) as count FROM marketplace_products_normalized')[0]->count;
    echo "1. Tổng số records: {$totalRecords}\n";
    
    // 2. Kiểm tra records có dữ liệu NULL hoặc rỗng
    $nullChecks = [
        'name' => DB::select('SELECT COUNT(*) as count FROM marketplace_products_normalized WHERE name IS NULL OR name = ""')[0]->count,
        'slug' => DB::select('SELECT COUNT(*) as count FROM marketplace_products_normalized WHERE slug IS NULL OR slug = ""')[0]->count,
        'regular_price' => DB::select('SELECT COUNT(*) as count FROM marketplace_products_normalized WHERE regular_price IS NULL OR regular_price = 0')[0]->count,
        'effective_price' => DB::select('SELECT COUNT(*) as count FROM marketplace_products_normalized WHERE effective_price IS NULL OR effective_price = 0')[0]->count,
        'seller_id' => DB::select('SELECT COUNT(*) as count FROM marketplace_products_normalized WHERE seller_id IS NULL')[0]->count,
    ];
    
    echo "\n2. Kiểm tra dữ liệu NULL/rỗng:\n";
    foreach ($nullChecks as $field => $count) {
        if ($count > 0) {
            echo "   ❌ {$field}: {$count} records có vấn đề\n";
        } else {
            echo "   ✅ {$field}: OK\n";
        }
    }
    
    // 3. Kiểm tra logic tính toán
    echo "\n3. Kiểm tra logic tính toán:\n";
    
    // Kiểm tra discount_percentage
    $wrongDiscount = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products_normalized 
        WHERE discount_percentage < 0 OR discount_percentage > 100
    ')[0]->count;
    
    if ($wrongDiscount > 0) {
        echo "   ❌ discount_percentage: {$wrongDiscount} records có giá trị không hợp lệ (< 0 hoặc > 100)\n";
    } else {
        echo "   ✅ discount_percentage: OK\n";
    }
    
    // Kiểm tra effective_price vs regular_price
    $wrongPrice = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products_normalized 
        WHERE effective_price > regular_price
    ')[0]->count;
    
    if ($wrongPrice > 0) {
        echo "   ❌ effective_price: {$wrongPrice} records có effective_price > regular_price\n";
    } else {
        echo "   ✅ effective_price: OK\n";
    }
    
    // 4. Kiểm tra is_available logic
    echo "\n4. Kiểm tra logic is_available:\n";
    $availabilityCheck = DB::select('
        SELECT 
            product_type,
            COUNT(*) as total,
            SUM(is_available) as available_count
        FROM marketplace_products_normalized 
        GROUP BY product_type
    ');
    
    foreach ($availabilityCheck as $check) {
        echo "   {$check->product_type}: {$check->available_count}/{$check->total} available\n";
    }
    
    // 5. Kiểm tra is_digital_product logic
    echo "\n5. Kiểm tra logic is_digital_product:\n";
    $digitalCheck = DB::select('
        SELECT 
            product_type,
            COUNT(*) as total,
            SUM(is_digital_product) as digital_count
        FROM marketplace_products_normalized 
        GROUP BY product_type
    ');
    
    foreach ($digitalCheck as $check) {
        echo "   {$check->product_type}: {$check->digital_count}/{$check->total} digital\n";
    }
    
    // 6. Kiểm tra status distribution
    echo "\n6. Phân bố status:\n";
    $statusCheck = DB::select('
        SELECT 
            status,
            COUNT(*) as count
        FROM marketplace_products_normalized 
        GROUP BY status
        ORDER BY count DESC
    ');
    
    foreach ($statusCheck as $status) {
        echo "   {$status->status}: {$status->count} records\n";
    }
    
    // 7. Kiểm tra một số sample records
    echo "\n7. Sample records (5 đầu tiên):\n";
    $samples = DB::select('
        SELECT 
            id, name, product_type, regular_price, effective_price, 
            discount_percentage, is_available, is_digital_product, status
        FROM marketplace_products_normalized 
        LIMIT 5
    ');
    
    foreach ($samples as $sample) {
        echo "   ID {$sample->id}: {$sample->name} | {$sample->product_type} | {$sample->regular_price}/{$sample->effective_price} | {$sample->discount_percentage}% | Available: {$sample->is_available} | Digital: {$sample->is_digital_product} | Status: {$sample->status}\n";
    }
    
    echo "\n=== KẾT THÚC PHÂN TÍCH ===\n";
    
} catch (Exception $e) {
    echo "❌ Lỗi khi phân tích: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
