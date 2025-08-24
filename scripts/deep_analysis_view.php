<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHÂN TÍCH SÂU BẢNG marketplace_products_normalized ===\n\n";

try {
    // 1. Kiểm tra view definition
    echo "1. Kiểm tra định nghĩa view:\n";
    $viewDef = DB::select("SHOW CREATE VIEW marketplace_products_normalized");
    if (!empty($viewDef)) {
        echo "   ✅ View definition exists\n";
        // Kiểm tra xem có lỗi syntax trong view không
        echo "   View được tạo thành công\n";
    }
    
    // 2. Kiểm tra các trường hợp edge case
    echo "\n2. Kiểm tra các trường hợp đặc biệt:\n";
    
    // Kiểm tra products có sale_price nhưng không is_on_sale
    $inconsistentSale = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products 
        WHERE sale_price IS NOT NULL AND sale_price > 0 AND is_on_sale = 0
    ')[0]->count;
    
    if ($inconsistentSale > 0) {
        echo "   ⚠️  {$inconsistentSale} products có sale_price nhưng is_on_sale = 0\n";
    } else {
        echo "   ✅ Sale price logic: OK\n";
    }
    
    // Kiểm tra products có is_on_sale nhưng không có sale_price
    $missingSalePrice = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products 
        WHERE is_on_sale = 1 AND (sale_price IS NULL OR sale_price = 0)
    ')[0]->count;
    
    if ($missingSalePrice > 0) {
        echo "   ⚠️  {$missingSalePrice} products có is_on_sale = 1 nhưng không có sale_price\n";
    } else {
        echo "   ✅ Sale flag logic: OK\n";
    }
    
    // 3. Kiểm tra digital products logic
    echo "\n3. Kiểm tra logic digital products:\n";
    
    // Products có product_type = 'digital' nhưng không có digital_files
    $digitalNoFiles = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products 
        WHERE product_type = "digital" AND (digital_files IS NULL OR JSON_LENGTH(digital_files) = 0)
    ')[0]->count;
    
    if ($digitalNoFiles > 0) {
        echo "   ⚠️  {$digitalNoFiles} digital products không có digital_files\n";
    } else {
        echo "   ✅ Digital files logic: OK\n";
    }
    
    // Products không phải digital nhưng có digital_files
    $nonDigitalWithFiles = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products 
        WHERE product_type != "digital" AND digital_files IS NOT NULL AND JSON_LENGTH(digital_files) > 0
    ')[0]->count;
    
    if ($nonDigitalWithFiles > 0) {
        echo "   ⚠️  {$nonDigitalWithFiles} non-digital products có digital_files\n";
    } else {
        echo "   ✅ Non-digital files logic: OK\n";
    }
    
    // 4. Kiểm tra stock logic
    echo "\n4. Kiểm tra logic stock:\n";
    
    // Products có manage_stock = 1 nhưng stock_quantity = 0 và vẫn available
    $stockIssues = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products_normalized mpn
        JOIN marketplace_products mp ON mpn.id = mp.id
        WHERE mp.manage_stock = 1 
        AND mp.stock_quantity = 0 
        AND mp.product_type != "digital"
        AND mpn.is_available = 1
    ')[0]->count;
    
    if ($stockIssues > 0) {
        echo "   ⚠️  {$stockIssues} products có stock = 0 nhưng vẫn available\n";
    } else {
        echo "   ✅ Stock availability logic: OK\n";
    }
    
    // 5. Kiểm tra foreign key integrity
    echo "\n5. Kiểm tra tính toàn vẹn dữ liệu:\n";
    
    // Seller không tồn tại
    $invalidSellers = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products_normalized mpn
        LEFT JOIN users u ON mpn.seller_id = u.id
        WHERE u.id IS NULL
    ')[0]->count;
    
    if ($invalidSellers > 0) {
        echo "   ❌ {$invalidSellers} products có seller_id không tồn tại\n";
    } else {
        echo "   ✅ Seller integrity: OK\n";
    }
    
    // Category không tồn tại (nếu có)
    $invalidCategories = DB::select('
        SELECT COUNT(*) as count 
        FROM marketplace_products_normalized mpn
        LEFT JOIN product_categories pc ON mpn.product_category_id = pc.id
        WHERE mpn.product_category_id IS NOT NULL AND pc.id IS NULL
    ')[0]->count;
    
    if ($invalidCategories > 0) {
        echo "   ❌ {$invalidCategories} products có category_id không tồn tại\n";
    } else {
        echo "   ✅ Category integrity: OK\n";
    }
    
    // 6. Kiểm tra performance
    echo "\n6. Kiểm tra performance:\n";
    
    $start = microtime(true);
    DB::select('SELECT COUNT(*) FROM marketplace_products_normalized WHERE status = "approved" AND is_active = 1');
    $queryTime = (microtime(true) - $start) * 1000;
    
    if ($queryTime > 100) {
        echo "   ⚠️  Query time: {$queryTime}ms (có thể cần tối ưu)\n";
    } else {
        echo "   ✅ Query performance: {$queryTime}ms\n";
    }
    
    // 7. Kiểm tra một số records có vấn đề (nếu có)
    echo "\n7. Kiểm tra records có vấn đề:\n";
    
    $problematicRecords = DB::select('
        SELECT 
            mpn.id, mpn.name, mpn.product_type, mpn.regular_price, mpn.effective_price,
            mp.is_on_sale, mp.sale_price, mp.manage_stock, mp.stock_quantity
        FROM marketplace_products_normalized mpn
        JOIN marketplace_products mp ON mpn.id = mp.id
        WHERE (
            (mp.is_on_sale = 1 AND (mp.sale_price IS NULL OR mp.sale_price = 0))
            OR (mp.sale_price IS NOT NULL AND mp.sale_price > 0 AND mp.is_on_sale = 0)
            OR (mp.product_type = "digital" AND (mp.digital_files IS NULL OR JSON_LENGTH(mp.digital_files) = 0))
            OR (mp.manage_stock = 1 AND mp.stock_quantity = 0 AND mp.product_type != "digital" AND mpn.is_available = 1)
        )
        LIMIT 5
    ');
    
    if (count($problematicRecords) > 0) {
        echo "   ⚠️  Tìm thấy " . count($problematicRecords) . " records có vấn đề:\n";
        foreach ($problematicRecords as $record) {
            echo "      ID {$record->id}: {$record->name} - Type: {$record->product_type}\n";
            echo "         Price: {$record->regular_price}/{$record->effective_price}, Sale: {$record->is_on_sale}/{$record->sale_price}\n";
            echo "         Stock: manage={$record->manage_stock}, qty={$record->stock_quantity}\n";
        }
    } else {
        echo "   ✅ Không tìm thấy records có vấn đề\n";
    }
    
    echo "\n=== KẾT THÚC PHÂN TÍCH SÂU ===\n";
    
} catch (Exception $e) {
    echo "❌ Lỗi khi phân tích: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
