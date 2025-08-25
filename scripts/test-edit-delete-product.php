<?php

/**
 * Script test Edit & Delete Product cho Seller
 * Kiểm tra toàn diện việc sửa và xóa sản phẩm
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Services\UnifiedMarketplacePermissionService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== EDIT & DELETE PRODUCT TESTING SCRIPT ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "=============================================" . PHP_EOL . PHP_EOL;

// Test với supplier01
$user = User::where('username', 'supplier01')->first();
$seller = MarketplaceSeller::where('user_id', $user->id)->first();

echo "🧪 Testing Product Edit & Delete for: {$user->name}" . PHP_EOL;
echo "Seller: {$seller->business_name}" . PHP_EOL;
echo "=============================================" . PHP_EOL;

// Get test products we just created
$testProducts = MarketplaceProduct::where('seller_id', $seller->id)
    ->where('name', 'like', 'Test%Product%')
    ->orderBy('created_at', 'desc')
    ->limit(2)
    ->get();

if ($testProducts->count() < 2) {
    echo "❌ Not enough test products found. Please run test-create-product.php first." . PHP_EOL;
    exit;
}

$digitalProduct = $testProducts->where('product_type', 'digital')->first();
$newProduct = $testProducts->where('product_type', 'new_product')->first();

echo "Found test products:" . PHP_EOL;
echo "   - Digital: {$digitalProduct->name} (ID: {$digitalProduct->id})" . PHP_EOL;
echo "   - New Product: {$newProduct->name} (ID: {$newProduct->id})" . PHP_EOL;

// 1. Test Product Edit - Digital Product
echo PHP_EOL . "✏️ 1. TESTING DIGITAL PRODUCT EDIT" . PHP_EOL;
echo "==================================" . PHP_EOL;

$originalName = $digitalProduct->name;
$originalPrice = $digitalProduct->price;
$originalSpecs = $digitalProduct->technical_specs;

$updateData = [
    'name' => $originalName . ' - UPDATED',
    'description' => 'Mô tả đã được cập nhật với thông tin chi tiết hơn',
    'price' => $originalPrice + 50000,
    'sale_price' => $originalPrice + 30000,
    'is_on_sale' => true,
    'technical_specs' => array_merge($originalSpecs ?: [], [
        'updated_at' => date('Y-m-d H:i:s'),
        'version' => '2.0',
        'new_feature' => 'Enhanced precision'
    ]),
    'file_formats' => ['dwg', 'step', 'iges', 'pdf'],
    'software_compatibility' => ['AutoCAD 2024', 'SolidWorks 2023', 'Inventor 2024', 'Fusion 360'],
    'tags' => ['cad', 'drawing', 'mechanical', 'test', 'updated'],
    'meta_description' => 'Sản phẩm kỹ thuật số đã được cập nhật'
];

try {
    // Validate permissions for update
    $errors = UnifiedMarketplacePermissionService::validateProductCreation($user, array_merge($updateData, [
        'product_type' => $digitalProduct->product_type,
        'seller_id' => $seller->id
    ]));
    
    if (!empty($errors)) {
        echo "❌ Update permission validation failed:" . PHP_EOL;
        foreach ($errors as $error) {
            echo "   - {$error}" . PHP_EOL;
        }
    } else {
        echo "✅ Update permission validation: PASS" . PHP_EOL;
        
        // Update product
        $digitalProduct->update($updateData);
        $digitalProduct->refresh();
        
        echo "✅ Digital product updated successfully!" . PHP_EOL;
        echo "   - Name: {$originalName} → {$digitalProduct->name}" . PHP_EOL;
        echo "   - Price: " . number_format($originalPrice) . " → " . number_format($digitalProduct->price) . " VND" . PHP_EOL;
        echo "   - Sale Price: " . number_format($digitalProduct->sale_price) . " VND" . PHP_EOL;
        echo "   - File Formats: " . implode(', ', $digitalProduct->file_formats) . PHP_EOL;
        echo "   - Software: " . implode(', ', $digitalProduct->software_compatibility) . PHP_EOL;
        echo "   - Updated At: {$digitalProduct->updated_at}" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ Error updating digital product: " . $e->getMessage() . PHP_EOL;
}

// 2. Test Product Edit - New Product (Stock Management)
echo PHP_EOL . "🏭 2. TESTING NEW PRODUCT EDIT (Stock Management)" . PHP_EOL;
echo "=================================================" . PHP_EOL;

$originalStock = $newProduct->stock_quantity;
$originalThreshold = $newProduct->low_stock_threshold;

$stockUpdateData = [
    'name' => $newProduct->name . ' - STOCK UPDATED',
    'stock_quantity' => $originalStock + 25,
    'low_stock_threshold' => 15,
    'manage_stock' => true,
    'in_stock' => true,
    'material' => 'Thép không gỉ 316L (upgraded)',
    'manufacturing_process' => 'Gia công CNC 5 trục, mài bóng mirror',
    'technical_specs' => [
        'dimensions' => '200x100x50mm',
        'weight' => '2.3kg (optimized)',
        'operating_temperature' => '-40°C to +120°C (extended)',
        'protection_rating' => 'IP67 (upgraded)',
        'certification' => 'ISO 9001:2015, CE, RoHS'
    ],
    'mechanical_properties' => [
        'tensile_strength' => '580 MPa (improved)',
        'yield_strength' => '230 MPa',
        'elongation' => '45%',
        'hardness' => '200 HB'
    ],
    'price' => $newProduct->price + 200000,
    'tags' => ['mechanical', 'equipment', 'cnc', 'stainless', 'upgraded']
];

try {
    // Validate and update
    $errors = UnifiedMarketplacePermissionService::validateProductCreation($user, array_merge($stockUpdateData, [
        'product_type' => $newProduct->product_type,
        'seller_id' => $seller->id
    ]));
    
    if (!empty($errors)) {
        echo "❌ Stock update validation failed:" . PHP_EOL;
        foreach ($errors as $error) {
            echo "   - {$error}" . PHP_EOL;
        }
    } else {
        echo "✅ Stock update validation: PASS" . PHP_EOL;
        
        $newProduct->update($stockUpdateData);
        $newProduct->refresh();
        
        echo "✅ New product updated successfully!" . PHP_EOL;
        echo "   - Stock: {$originalStock} → {$newProduct->stock_quantity}" . PHP_EOL;
        echo "   - Threshold: {$originalThreshold} → {$newProduct->low_stock_threshold}" . PHP_EOL;
        echo "   - Material: {$newProduct->material}" . PHP_EOL;
        echo "   - Price: " . number_format($newProduct->price) . " VND" . PHP_EOL;
        echo "   - In Stock: " . ($newProduct->in_stock ? 'Yes' : 'No') . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ Error updating new product: " . $e->getMessage() . PHP_EOL;
}

// 3. Test Product Status Changes
echo PHP_EOL . "📊 3. TESTING PRODUCT STATUS CHANGES" . PHP_EOL;
echo "====================================" . PHP_EOL;

// Test activate/deactivate
echo "Testing activate/deactivate:" . PHP_EOL;
$originalActive = $digitalProduct->is_active;
$digitalProduct->update(['is_active' => !$originalActive]);
echo "   - Digital product active: {$originalActive} → " . ($digitalProduct->is_active ? 'true' : 'false') . PHP_EOL;

// Test featured status
$originalFeatured = $newProduct->is_featured;
$newProduct->update(['is_featured' => !$originalFeatured]);
echo "   - New product featured: {$originalFeatured} → " . ($newProduct->is_featured ? 'true' : 'false') . PHP_EOL;

// 4. Test Product Deletion (Soft Delete)
echo PHP_EOL . "🗑️ 4. TESTING PRODUCT DELETION (Soft Delete)" . PHP_EOL;
echo "=============================================" . PHP_EOL;

// Create a temporary product for deletion test
$tempProduct = MarketplaceProduct::create([
    'name' => 'Temp Product for Deletion Test',
    'description' => 'This product will be deleted',
    'product_type' => 'digital',
    'seller_id' => $seller->id,
    'product_category_id' => 1,
    'price' => 100000,
    'status' => 'pending'
]);

echo "Created temporary product for deletion test:" . PHP_EOL;
echo "   - ID: {$tempProduct->id}" . PHP_EOL;
echo "   - Name: {$tempProduct->name}" . PHP_EOL;

try {
    // Soft delete
    $tempProduct->delete();
    echo "✅ Product soft deleted successfully!" . PHP_EOL;
    echo "   - Deleted at: {$tempProduct->deleted_at}" . PHP_EOL;
    
    // Verify it's not in normal queries
    $found = MarketplaceProduct::find($tempProduct->id);
    echo "   - Found in normal query: " . ($found ? 'Yes (ERROR)' : 'No (CORRECT)') . PHP_EOL;
    
    // Verify it's in trashed
    $trashed = MarketplaceProduct::withTrashed()->find($tempProduct->id);
    echo "   - Found in trashed: " . ($trashed ? 'Yes (CORRECT)' : 'No (ERROR)') . PHP_EOL;
    
    // Test restore
    $trashed->restore();
    echo "✅ Product restored successfully!" . PHP_EOL;
    
    // Permanent delete
    $trashed->forceDelete();
    echo "✅ Product permanently deleted!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error during deletion test: " . $e->getMessage() . PHP_EOL;
}

// 5. Test Bulk Operations
echo PHP_EOL . "📦 5. TESTING BULK OPERATIONS" . PHP_EOL;
echo "=============================" . PHP_EOL;

$sellerProducts = MarketplaceProduct::where('seller_id', $seller->id)
    ->where('name', 'like', 'Test%')
    ->get();

echo "Found {$sellerProducts->count()} test products for bulk operations" . PHP_EOL;

// Bulk status update
$updatedCount = MarketplaceProduct::where('seller_id', $seller->id)
    ->where('name', 'like', 'Test%')
    ->update(['is_active' => true, 'updated_at' => now()]);

echo "✅ Bulk updated {$updatedCount} products to active status" . PHP_EOL;

// Bulk price adjustment (10% increase)
foreach ($sellerProducts as $product) {
    $newPrice = $product->price * 1.1;
    $product->update(['price' => $newPrice]);
}
echo "✅ Applied 10% price increase to all test products" . PHP_EOL;

echo PHP_EOL . "✅ Edit & Delete Product Testing Completed!" . PHP_EOL;
