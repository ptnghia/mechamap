<?php

/**
 * Test script for MechaMap Marketplace Restructure
 * Tests the new 3-type product system and permission matrix
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Services\MarketplacePermissionService;

echo "🧪 TESTING MECHAMAP MARKETPLACE RESTRUCTURE\n";
echo "==========================================\n\n";

// Test 1: Verify product types
echo "1️⃣ TESTING PRODUCT TYPES:\n";
$productTypes = MarketplaceProduct::select('product_type')->distinct()->pluck('product_type');
echo "Available product types: " . $productTypes->implode(', ') . "\n";

$expectedTypes = ['digital', 'new_product', 'used_product'];
$missingTypes = array_diff($expectedTypes, $productTypes->toArray());
if (empty($missingTypes)) {
    echo "✅ All expected product types are available\n";
} else {
    echo "❌ Missing product types: " . implode(', ', $missingTypes) . "\n";
}

// Count products by type
$counts = MarketplaceProduct::selectRaw('product_type, count(*) as count')
    ->groupBy('product_type')
    ->get();

echo "\nProduct distribution:\n";
foreach ($counts as $count) {
    echo "- {$count->product_type}: {$count->count} products\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Verify user roles and permissions
echo "2️⃣ TESTING PERMISSION MATRIX:\n";

$testUsers = [
    'guest' => User::where('role', 'guest')->first(),
    'member' => User::where('role', 'member')->first(),
    'supplier' => User::where('role', 'supplier')->first(),
    'manufacturer' => User::where('role', 'manufacturer')->first(),
    'brand' => User::where('role', 'brand')->first(),
];

$productTypes = ['digital', 'new_product', 'used_product'];

echo "Permission Matrix Test Results:\n";
echo sprintf("%-15s %-12s %-12s %-12s %-12s %-12s %-12s\n", 
    'Role', 'Buy Digital', 'Sell Digital', 'Buy New', 'Sell New', 'Buy Used', 'Sell Used');
echo str_repeat("-", 85) . "\n";

foreach ($testUsers as $role => $user) {
    if (!$user) {
        echo sprintf("%-15s %s\n", $role, "❌ User not found");
        continue;
    }

    $permissions = [];
    foreach ($productTypes as $type) {
        $permissions["buy_{$type}"] = MarketplacePermissionService::canBuy($user, $type) ? "✅" : "❌";
        $permissions["sell_{$type}"] = MarketplacePermissionService::canSell($user, $type) ? "✅" : "❌";
    }

    echo sprintf("%-15s %-12s %-12s %-12s %-12s %-12s %-12s\n",
        $role,
        $permissions['buy_digital'],
        $permissions['sell_digital'],
        $permissions['buy_new_product'],
        $permissions['sell_new_product'],
        $permissions['buy_used_product'],
        $permissions['sell_used_product']
    );
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 3: Verify expected permission rules
echo "3️⃣ TESTING EXPECTED PERMISSION RULES:\n";

$expectedRules = [
    'guest' => ['buy' => ['digital'], 'sell' => ['digital']],
    'member' => ['buy' => ['digital'], 'sell' => ['digital']],
    'supplier' => ['buy' => ['digital'], 'sell' => ['digital', 'new_product']],
    'manufacturer' => ['buy' => ['digital', 'new_product'], 'sell' => ['digital']],
    'brand' => ['buy' => [], 'sell' => []],
];

$allTestsPassed = true;

foreach ($expectedRules as $role => $rules) {
    $user = $testUsers[$role];
    if (!$user) continue;

    echo "Testing {$role} permissions:\n";
    
    // Test buy permissions
    foreach ($productTypes as $type) {
        $expected = in_array($type, $rules['buy']);
        $actual = MarketplacePermissionService::canBuy($user, $type);
        
        if ($expected === $actual) {
            echo "  ✅ Buy {$type}: " . ($actual ? "allowed" : "denied") . "\n";
        } else {
            echo "  ❌ Buy {$type}: expected " . ($expected ? "allowed" : "denied") . 
                 ", got " . ($actual ? "allowed" : "denied") . "\n";
            $allTestsPassed = false;
        }
    }
    
    // Test sell permissions
    foreach ($productTypes as $type) {
        $expected = in_array($type, $rules['sell']);
        $actual = MarketplacePermissionService::canSell($user, $type);
        
        if ($expected === $actual) {
            echo "  ✅ Sell {$type}: " . ($actual ? "allowed" : "denied") . "\n";
        } else {
            echo "  ❌ Sell {$type}: expected " . ($expected ? "allowed" : "denied") . 
                 ", got " . ($actual ? "allowed" : "denied") . "\n";
            $allTestsPassed = false;
        }
    }
    echo "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Database integrity
echo "4️⃣ TESTING DATABASE INTEGRITY:\n";

// Check if old products table exists
try {
    DB::table('products')->count();
    echo "❌ Old 'products' table still exists - should be dropped\n";
} catch (Exception $e) {
    echo "✅ Old 'products' table successfully removed\n";
}

// Check marketplace_products structure
$columns = DB::select('DESCRIBE marketplace_products');
$requiredColumns = ['product_type', 'seller_type', 'digital_files'];
$existingColumns = array_column($columns, 'Field');

foreach ($requiredColumns as $column) {
    if (in_array($column, $existingColumns)) {
        echo "✅ Column '{$column}' exists\n";
    } else {
        echo "❌ Column '{$column}' missing\n";
        $allTestsPassed = false;
    }
}

// Check enum values
$productTypeEnum = collect($columns)->firstWhere('Field', 'product_type');
if ($productTypeEnum && strpos($productTypeEnum->Type, 'digital,new_product,used_product') !== false) {
    echo "✅ Product type enum updated correctly\n";
} else {
    echo "❌ Product type enum not updated correctly\n";
    $allTestsPassed = false;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Final summary
echo "🎯 FINAL SUMMARY:\n";
echo "Total products: " . MarketplaceProduct::count() . "\n";
echo "Digital products: " . MarketplaceProduct::where('product_type', 'digital')->count() . "\n";
echo "New products: " . MarketplaceProduct::where('product_type', 'new_product')->count() . "\n";
echo "Used products: " . MarketplaceProduct::where('product_type', 'used_product')->count() . "\n";

if ($allTestsPassed) {
    echo "\n🎉 ALL TESTS PASSED! Marketplace restructure successful!\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED! Please review the issues above.\n";
}

echo "\n==========================================\n";
echo "✅ Testing completed!\n";
