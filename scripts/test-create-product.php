<?php

/**
 * Script test Create Product cho Seller
 * Kiểm tra toàn diện việc tạo sản phẩm với các loại khác nhau
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\ProductCategory;
use App\Services\UnifiedMarketplacePermissionService;
use Illuminate\Support\Str;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CREATE PRODUCT TESTING SCRIPT ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "=====================================" . PHP_EOL . PHP_EOL;

// Test với supplier01
$user = User::where('username', 'supplier01')->first();
$seller = MarketplaceSeller::where('user_id', $user->id)->first();

echo "🧪 Testing Product Creation for: {$user->name}" . PHP_EOL;
echo "Seller: {$seller->business_name}" . PHP_EOL;
echo "=============================================" . PHP_EOL;

// Get first category
$category = ProductCategory::where('is_active', true)->first();

// 1. Test Digital Product Creation
echo PHP_EOL . "📱 1. TESTING DIGITAL PRODUCT CREATION" . PHP_EOL;
echo "======================================" . PHP_EOL;

$digitalProductData = [
    'name' => 'Test Digital Product - ' . date('Y-m-d H:i:s'),
    'description' => 'Đây là sản phẩm kỹ thuật số test với đầy đủ thông tin kỹ thuật',
    'short_description' => 'Sản phẩm test digital',
    'product_type' => 'digital',
    'seller_id' => $seller->id,
    'product_category_id' => $category->id,
    'price' => 150000,
    'sale_price' => 120000,
    'is_on_sale' => true,
    'stock_quantity' => 999,
    'manage_stock' => false,
    'in_stock' => true,
    
    // Digital specific attributes
    'file_formats' => ['dwg', 'step', 'iges'],
    'software_compatibility' => ['AutoCAD 2024', 'SolidWorks 2023', 'Inventor 2024'],
    'file_size_mb' => 25.5,
    'download_limit' => 5,
    'digital_files' => [
        ['name' => 'main_drawing.dwg', 'size' => '15MB'],
        ['name' => 'assembly.step', 'size' => '10.5MB']
    ],
    
    // Technical specifications
    'technical_specs' => [
        'dimensions' => '100x50x25mm',
        'weight' => '0.5kg',
        'tolerance' => '±0.1mm',
        'surface_finish' => 'Ra 1.6'
    ],
    'mechanical_properties' => [
        'tensile_strength' => '400 MPa',
        'yield_strength' => '250 MPa',
        'hardness' => '200 HB'
    ],
    'material' => 'Thép carbon C45',
    'manufacturing_process' => 'Gia công CNC, nhiệt luyện',
    'standards_compliance' => ['ISO 9001', 'JIS B1176'],
    
    // Media
    'images' => ['product1.jpg', 'product2.jpg'],
    'featured_image' => 'featured.jpg',
    'attachments' => ['spec_sheet.pdf', 'installation_guide.pdf'],
    
    // SEO
    'meta_title' => 'Test Digital Product - Bản vẽ kỹ thuật',
    'meta_description' => 'Sản phẩm kỹ thuật số chất lượng cao',
    'tags' => ['cad', 'drawing', 'mechanical', 'test'],
    
    'status' => 'pending',
    'is_active' => true,
    'is_featured' => false
];

try {
    // Validate permissions first
    $errors = UnifiedMarketplacePermissionService::validateProductCreation($user, $digitalProductData);
    if (!empty($errors)) {
        echo "❌ Permission validation failed:" . PHP_EOL;
        foreach ($errors as $error) {
            echo "   - {$error}" . PHP_EOL;
        }
    } else {
        echo "✅ Permission validation: PASS" . PHP_EOL;
        
        // Create product
        $digitalProduct = MarketplaceProduct::create($digitalProductData);
        echo "✅ Digital product created successfully!" . PHP_EOL;
        echo "   - ID: {$digitalProduct->id}" . PHP_EOL;
        echo "   - UUID: {$digitalProduct->uuid}" . PHP_EOL;
        echo "   - SKU: {$digitalProduct->sku}" . PHP_EOL;
        echo "   - Slug: {$digitalProduct->slug}" . PHP_EOL;
        echo "   - Status: {$digitalProduct->status}" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ Error creating digital product: " . $e->getMessage() . PHP_EOL;
}

// 2. Test New Product Creation
echo PHP_EOL . "🏭 2. TESTING NEW PRODUCT CREATION" . PHP_EOL;
echo "==================================" . PHP_EOL;

$newProductData = [
    'name' => 'Test New Product - ' . date('Y-m-d H:i:s'),
    'description' => 'Đây là sản phẩm vật lý mới với đầy đủ thông tin kỹ thuật và quản lý kho',
    'short_description' => 'Sản phẩm test new product',
    'product_type' => 'new_product',
    'seller_id' => $seller->id,
    'product_category_id' => $category->id,
    'price' => 2500000,
    'sale_price' => null,
    'is_on_sale' => false,
    'stock_quantity' => 50,
    'manage_stock' => true,
    'in_stock' => true,
    'low_stock_threshold' => 10,
    
    // Physical product specific
    'material' => 'Thép không gỉ 304',
    'manufacturing_process' => 'Gia công CNC, mài bóng',
    
    // Technical specifications
    'technical_specs' => [
        'dimensions' => '200x100x50mm',
        'weight' => '2.5kg',
        'operating_temperature' => '-20°C to +80°C',
        'protection_rating' => 'IP65'
    ],
    'mechanical_properties' => [
        'tensile_strength' => '520 MPa',
        'yield_strength' => '210 MPa',
        'elongation' => '40%'
    ],
    'standards_compliance' => ['ISO 9001', 'CE', 'RoHS'],
    
    // Media
    'images' => ['new_product1.jpg', 'new_product2.jpg', 'new_product3.jpg'],
    'featured_image' => 'new_featured.jpg',
    'attachments' => ['datasheet.pdf', 'warranty.pdf'],
    
    // SEO
    'meta_title' => 'Test New Product - Thiết bị cơ khí',
    'meta_description' => 'Sản phẩm cơ khí chất lượng cao, gia công chính xác',
    'tags' => ['mechanical', 'equipment', 'cnc', 'stainless'],
    
    'status' => 'pending',
    'is_active' => true,
    'is_featured' => true
];

try {
    // Validate permissions
    $errors = UnifiedMarketplacePermissionService::validateProductCreation($user, $newProductData);
    if (!empty($errors)) {
        echo "❌ Permission validation failed:" . PHP_EOL;
        foreach ($errors as $error) {
            echo "   - {$error}" . PHP_EOL;
        }
    } else {
        echo "✅ Permission validation: PASS" . PHP_EOL;
        
        // Create product
        $newProduct = MarketplaceProduct::create($newProductData);
        echo "✅ New product created successfully!" . PHP_EOL;
        echo "   - ID: {$newProduct->id}" . PHP_EOL;
        echo "   - UUID: {$newProduct->uuid}" . PHP_EOL;
        echo "   - SKU: {$newProduct->sku}" . PHP_EOL;
        echo "   - Slug: {$newProduct->slug}" . PHP_EOL;
        echo "   - Status: {$newProduct->status}" . PHP_EOL;
        echo "   - Stock: {$newProduct->stock_quantity}" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ Error creating new product: " . $e->getMessage() . PHP_EOL;
}

// 3. Test Used Product Creation (should fail)
echo PHP_EOL . "♻️ 3. TESTING USED PRODUCT CREATION (Should Fail)" . PHP_EOL;
echo "=================================================" . PHP_EOL;

$usedProductData = [
    'name' => 'Test Used Product - ' . date('Y-m-d H:i:s'),
    'description' => 'Đây là sản phẩm cũ - không được phép tạo',
    'product_type' => 'used_product',
    'seller_id' => $seller->id,
    'product_category_id' => $category->id,
    'price' => 1000000,
];

$errors = UnifiedMarketplacePermissionService::validateProductCreation($user, $usedProductData);
if (!empty($errors)) {
    echo "✅ Used product creation correctly blocked:" . PHP_EOL;
    foreach ($errors as $error) {
        echo "   - {$error}" . PHP_EOL;
    }
} else {
    echo "❌ Used product should be blocked but wasn't!" . PHP_EOL;
}

echo PHP_EOL . "✅ Product Creation Testing Completed!" . PHP_EOL;
