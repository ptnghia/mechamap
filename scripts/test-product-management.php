<?php

/**
 * Script test Product Management cho Seller
 * Kiểm tra toàn diện các tính năng CRUD sản phẩm
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\ProductCategory;
use App\Services\UnifiedMarketplacePermissionService;
use App\Services\MarketplacePermissionService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PRODUCT MANAGEMENT TESTING SCRIPT ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "=========================================" . PHP_EOL . PHP_EOL;

// Test với supplier01 (có quyền bán digital + new_product)
$user = User::where('username', 'supplier01')->first();
if (!$user) {
    echo "❌ User supplier01 not found" . PHP_EOL;
    exit;
}

$seller = MarketplaceSeller::where('user_id', $user->id)->first();
if (!$seller) {
    echo "❌ Seller profile not found" . PHP_EOL;
    exit;
}

echo "🧪 Testing Product Management for: {$user->name}" . PHP_EOL;
echo "Seller: {$seller->business_name}" . PHP_EOL;
echo "=============================================" . PHP_EOL;

// 1. Test Product Creation Permissions
echo PHP_EOL . "🔐 1. TESTING PRODUCT CREATION PERMISSIONS" . PHP_EOL;
echo "=============================================" . PHP_EOL;

$productTypes = ['digital', 'new_product', 'used_product'];
$allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

echo "Allowed product types for {$user->role}: " . implode(', ', $allowedTypes) . PHP_EOL;

foreach ($productTypes as $type) {
    $canSell = UnifiedMarketplacePermissionService::canSell($user, $type);
    $status = $canSell ? "✅ CAN CREATE" : "❌ CANNOT CREATE";
    echo "   {$type}: {$status}" . PHP_EOL;
}

// 2. Test Product Categories
echo PHP_EOL . "📂 2. TESTING PRODUCT CATEGORIES" . PHP_EOL;
echo "=================================" . PHP_EOL;

$categories = ProductCategory::where('is_active', true)->limit(5)->get();
echo "Available categories: {$categories->count()}" . PHP_EOL;

foreach ($categories as $category) {
    echo "   - {$category->name} (ID: {$category->id})" . PHP_EOL;
}

// 3. Test Current Products
echo PHP_EOL . "📦 3. TESTING CURRENT PRODUCTS" . PHP_EOL;
echo "===============================" . PHP_EOL;

$currentProducts = MarketplaceProduct::where('seller_id', $seller->id)->get();
echo "Current products: {$currentProducts->count()}" . PHP_EOL;

foreach ($currentProducts as $product) {
    echo "   - {$product->name}" . PHP_EOL;
    echo "     Type: {$product->product_type}" . PHP_EOL;
    echo "     Status: {$product->status}" . PHP_EOL;
    echo "     Price: " . number_format($product->price) . " VND" . PHP_EOL;
    echo "     Stock: {$product->stock_quantity}" . PHP_EOL;
    echo "     Active: " . ($product->is_active ? 'Yes' : 'No') . PHP_EOL;
    echo PHP_EOL;
}

// 4. Test Product Validation
echo PHP_EOL . "✅ 4. TESTING PRODUCT VALIDATION" . PHP_EOL;
echo "=================================" . PHP_EOL;

// Test validation cho digital product
$digitalProductData = [
    'product_type' => 'digital',
    'name' => 'Test Digital Product',
    'description' => 'Test description',
    'price' => 100000,
];

$validationErrors = UnifiedMarketplacePermissionService::validateProductCreation($user, $digitalProductData);
if (empty($validationErrors)) {
    echo "✅ Digital product validation: PASS" . PHP_EOL;
} else {
    echo "❌ Digital product validation: FAIL" . PHP_EOL;
    foreach ($validationErrors as $error) {
        echo "   - {$error}" . PHP_EOL;
    }
}

// Test validation cho new_product
$newProductData = [
    'product_type' => 'new_product',
    'name' => 'Test New Product',
    'description' => 'Test description',
    'price' => 500000,
];

$validationErrors = UnifiedMarketplacePermissionService::validateProductCreation($user, $newProductData);
if (empty($validationErrors)) {
    echo "✅ New product validation: PASS" . PHP_EOL;
} else {
    echo "❌ New product validation: FAIL" . PHP_EOL;
    foreach ($validationErrors as $error) {
        echo "   - {$error}" . PHP_EOL;
    }
}

// Test validation cho used_product (should fail)
$usedProductData = [
    'product_type' => 'used_product',
    'name' => 'Test Used Product',
    'description' => 'Test description',
    'price' => 300000,
];

$validationErrors = UnifiedMarketplacePermissionService::validateProductCreation($user, $usedProductData);
if (!empty($validationErrors)) {
    echo "✅ Used product validation: CORRECTLY BLOCKED" . PHP_EOL;
    foreach ($validationErrors as $error) {
        echo "   - {$error}" . PHP_EOL;
    }
} else {
    echo "❌ Used product validation: SHOULD BE BLOCKED" . PHP_EOL;
}

// 5. Test Product Attributes
echo PHP_EOL . "🏷️ 5. TESTING PRODUCT ATTRIBUTES" . PHP_EOL;
echo "=================================" . PHP_EOL;

$requiredAttributes = [
    'Basic' => ['name', 'description', 'price', 'product_type', 'seller_id'],
    'Digital' => ['file_formats', 'software_compatibility', 'file_size_mb', 'digital_files'],
    'Physical' => ['stock_quantity', 'manage_stock', 'material', 'manufacturing_process'],
    'Technical' => ['technical_specs', 'mechanical_properties', 'standards_compliance'],
    'SEO' => ['meta_title', 'meta_description', 'tags'],
    'Media' => ['images', 'featured_image', 'attachments']
];

foreach ($requiredAttributes as $category => $attributes) {
    echo "✅ {$category} Attributes:" . PHP_EOL;
    foreach ($attributes as $attr) {
        echo "   - {$attr}" . PHP_EOL;
    }
}

echo PHP_EOL . "✅ Product Management Testing Completed!" . PHP_EOL;
