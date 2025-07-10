<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\PermissionService;

echo "🚀 TESTING MECHAMAP PERMISSION SYSTEM\n";
echo "=====================================\n\n";

// Test tạo business users
echo "📊 CREATING TEST BUSINESS USERS:\n";

try {
    // Tạo supplier test
    $supplier = User::create([
        'name' => 'Test Supplier Company',
        'username' => 'test_supplier',
        'email' => 'test@supplier.com',
        'password' => bcrypt('password'),
        'role' => 'supplier',
        'company_name' => 'Test Supplier Co., Ltd',
        'business_license' => 'TEST123456',
        'tax_code' => 'TEST123456-001',
        'business_description' => 'Test supplier for permission testing',
        'business_categories' => ['test', 'materials'],
        'is_verified_business' => true,
        'subscription_level' => 'premium',
        'business_rating' => 4.5,
        'email_verified_at' => now(),
        'status' => 'active',
    ]);
    echo "✅ Supplier created: {$supplier->name}\n";

    // Tạo manufacturer test
    $manufacturer = User::create([
        'name' => 'Test Manufacturer Inc',
        'username' => 'test_manufacturer',
        'email' => 'test@manufacturer.com',
        'password' => bcrypt('password'),
        'role' => 'manufacturer',
        'company_name' => 'Test Manufacturer Inc',
        'business_license' => 'TEST789012',
        'tax_code' => 'TEST789012-002',
        'business_description' => 'Test manufacturer for permission testing',
        'business_categories' => ['manufacturing', 'cnc'],
        'is_verified_business' => true,
        'subscription_level' => 'enterprise',
        'business_rating' => 4.8,
        'email_verified_at' => now(),
        'status' => 'active',
    ]);
    echo "✅ Manufacturer created: {$manufacturer->name}\n";

    // Tạo brand test
    $brand = User::create([
        'name' => 'Test Brand Global',
        'username' => 'test_brand',
        'email' => 'test@brand.com',
        'password' => bcrypt('password'),
        'role' => 'brand',
        'company_name' => 'Test Brand Global Ltd',
        'business_license' => 'TEST345678',
        'tax_code' => 'TEST345678-003',
        'business_description' => 'Test brand for permission testing',
        'business_categories' => ['brand', 'distribution'],
        'is_verified_business' => true,
        'subscription_level' => 'enterprise',
        'business_rating' => 4.9,
        'email_verified_at' => now(),
        'status' => 'active',
    ]);
    echo "✅ Brand created: {$brand->name}\n\n";

} catch (Exception $e) {
    echo "❌ Error creating users: " . $e->getMessage() . "\n\n";
}

// Test permissions
echo "🔐 TESTING PERMISSIONS:\n";
echo "======================\n\n";

$testUsers = [
    'Admin' => User::where('role', 'admin')->first(),
    'Moderator' => User::where('role', 'moderator')->first(),
    'Senior' => User::where('role', 'senior')->first(),
    'Member' => User::where('role', 'member')->first(),
    'Guest' => User::where('role', 'guest')->first(),
    'Supplier' => $supplier ?? User::where('role', 'supplier')->first(),
    'Manufacturer' => $manufacturer ?? User::where('role', 'manufacturer')->first(),
    'Brand' => $brand ?? User::where('role', 'brand')->first(),
];

$testPermissions = [
    'manage_system',
    'moderate_content',
    'create_posts',
    'comment_posts',
    'buy_products',
    'sell_products',
    'follow_users',
];

foreach ($testPermissions as $permission) {
    echo "🔍 Testing permission: {$permission}\n";
    foreach ($testUsers as $roleName => $user) {
        if ($user) {
            $hasPermission = PermissionService::hasPermission($user, $permission);
            $status = $hasPermission ? '✅' : '❌';
            echo "  {$status} {$roleName}: " . ($hasPermission ? 'ALLOWED' : 'DENIED') . "\n";
        } else {
            echo "  ⚠️  {$roleName}: USER NOT FOUND\n";
        }
    }
    echo "\n";
}

// Test business methods
echo "🏢 TESTING BUSINESS METHODS:\n";
echo "============================\n\n";

foreach ($testUsers as $roleName => $user) {
    if ($user) {
        echo "👤 {$roleName} ({$user->name}):\n";
        echo "  - isBusiness(): " . ($user->isBusiness() ? 'YES' : 'NO') . "\n";
        echo "  - canAccessMarketplace(): " . ($user->canAccessMarketplace() ? 'YES' : 'NO') . "\n";
        echo "  - canSell(): " . ($user->canSell() ? 'YES' : 'NO') . "\n";
        echo "  - canBuy(): " . ($user->canBuy() ? 'YES' : 'NO') . "\n";
        echo "  - Role Color: " . $user->getRoleColor() . "\n";
        echo "  - Role Display: " . $user->getRoleDisplayName() . "\n";
        
        if ($user->isBusiness()) {
            echo "  - Business Verified: " . ($user->isVerifiedBusiness() ? 'YES' : 'NO') . "\n";
            echo "  - Subscription: " . $user->subscription_level . "\n";
            echo "  - Rating: " . $user->business_rating . "/5.0\n";
        }
        echo "\n";
    }
}

echo "🎉 PERMISSION SYSTEM TEST COMPLETED!\n";
