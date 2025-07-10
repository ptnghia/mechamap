<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

echo "🔍 COMPREHENSIVE DATABASE AUDIT FOR MECHAMAP\n";
echo "=============================================\n\n";

// 1. Current User System Status
echo "👥 USER SYSTEM STATUS:\n";
echo "======================\n";

$userStats = [
    'admin' => User::where('role', 'admin')->count(),
    'moderator' => User::where('role', 'moderator')->count(),
    'senior' => User::where('role', 'senior')->count(),
    'member' => User::where('role', 'member')->count(),
    'guest' => User::where('role', 'guest')->count(),
    'supplier' => User::where('role', 'supplier')->count(),
    'manufacturer' => User::where('role', 'manufacturer')->count(),
    'brand' => User::where('role', 'brand')->count(),
];

$totalUsers = array_sum($userStats);
echo "Total Users: {$totalUsers}\n";
foreach ($userStats as $role => $count) {
    $emoji = match($role) {
        'admin' => '👑',
        'moderator' => '🛡️',
        'senior' => '⭐',
        'member' => '👤',
        'guest' => '👁️',
        'supplier' => '🏪',
        'manufacturer' => '🏭',
        'brand' => '🏷️',
        default => '❓'
    };
    echo "  {$emoji} {$role}: {$count} users\n";
}

// 2. Content Tables Analysis
echo "\n📊 CONTENT TABLES ANALYSIS:\n";
echo "===========================\n";

$contentTables = [
    'categories' => 'Forum categories',
    'forums' => 'Forum sections',
    'threads' => 'Discussion threads',
    'posts' => 'Forum posts',
    'comments' => 'Comments',
    'reactions' => 'User reactions',
    'bookmarks' => 'User bookmarks',
    'followers' => 'User follows',
    'showcases' => 'User showcases',
    'media' => 'Media files',
    'user_activities' => 'User activities'
];

$missingData = [];
foreach ($contentTables as $table => $description) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        $status = $count > 0 ? '✅' : '❌';
        echo "  {$status} {$description}: {$count} records\n";
        
        if ($count === 0) {
            $missingData[] = $table;
        }
    } else {
        echo "  ⚠️ {$description}: Table does not exist\n";
        $missingData[] = $table;
    }
}

// 3. Marketplace/Product Tables Analysis
echo "\n🛒 MARKETPLACE SYSTEM ANALYSIS:\n";
echo "===============================\n";

$marketplaceTables = [
    'products' => 'Products catalog',
    'product_categories' => 'Product categories',
    'orders' => 'Customer orders',
    'order_items' => 'Order line items',
    'shopping_carts' => 'Shopping carts',
    'payment_transactions' => 'Payment records',
    'technical_products' => 'Technical/CAD files',
    'product_purchases' => 'Product purchases',
    'seller_earnings' => 'Seller earnings',
    'seller_payouts' => 'Seller payouts'
];

$marketplaceStatus = [];
foreach ($marketplaceTables as $table => $description) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        $status = $count > 0 ? '✅' : '❌';
        echo "  {$status} {$description}: {$count} records\n";
        $marketplaceStatus[$table] = $count;
    } else {
        echo "  ❓ {$description}: Table does not exist\n";
        $marketplaceStatus[$table] = 'missing';
    }
}

// 4. Seeder Status Analysis
echo "\n🌱 SEEDER STATUS ANALYSIS:\n";
echo "=========================\n";

$requiredSeeders = [
    'RolesAndPermissionsSeeder' => 'User roles and permissions',
    'MechaMapCategorySeeder' => 'Forum categories',
    'MechaMapUserSeeder' => 'Community users',
    'BusinessUserSeeder' => 'Business users',
    'MechanicalEngineeringDataSeeder' => 'Forum content',
    'MediaSeeder' => 'Media files',
    'ProductCategorySeeder' => 'Product categories (needed)',
    'SampleProductSeeder' => 'Sample products (needed)',
    'MarketplaceDataSeeder' => 'Marketplace data (needed)'
];

foreach ($requiredSeeders as $seeder => $description) {
    $seederFile = "database/seeders/{$seeder}.php";
    if (file_exists($seederFile)) {
        echo "  ✅ {$description}: Seeder exists\n";
    } else {
        echo "  ❌ {$description}: Seeder missing - NEEDS CREATION\n";
    }
}

// 5. Data Integrity Check
echo "\n🔗 DATA INTEGRITY CHECK:\n";
echo "========================\n";

// Check for orphaned data
if (Schema::hasTable('threads') && Schema::hasTable('users')) {
    $orphanedThreads = DB::table('threads')
        ->leftJoin('users', 'threads.user_id', '=', 'users.id')
        ->whereNull('users.id')
        ->count();
    echo "  Orphaned threads: {$orphanedThreads}\n";
}

if (Schema::hasTable('posts') && Schema::hasTable('users')) {
    $orphanedPosts = DB::table('posts')
        ->leftJoin('users', 'posts.user_id', '=', 'users.id')
        ->whereNull('users.id')
        ->count();
    echo "  Orphaned posts: {$orphanedPosts}\n";
}

// 6. Recommendations
echo "\n💡 RECOVERY RECOMMENDATIONS:\n";
echo "============================\n";

if (!empty($missingData)) {
    echo "🔄 IMMEDIATE ACTIONS NEEDED:\n";
    echo "1. Re-run content seeders to restore:\n";
    foreach ($missingData as $table) {
        echo "   - {$table}\n";
    }
    echo "\n";
}

echo "🛒 MARKETPLACE DEVELOPMENT NEEDED:\n";
echo "1. Create product management system\n";
echo "2. Implement marketplace for 3 business roles\n";
echo "3. Enhance CAD/technical file trading\n";
echo "4. Add shopping cart and order management\n";
echo "5. Implement payment and transaction system\n";

echo "\n📋 RECOMMENDED SEEDER EXECUTION ORDER:\n";
echo "1. php artisan db:seed --class=RolesAndPermissionsSeeder\n";
echo "2. php artisan db:seed --class=MechaMapCategorySeeder\n";
echo "3. php artisan db:seed --class=MechaMapUserSeeder\n";
echo "4. php artisan db:seed --class=BusinessUserSeeder\n";
echo "5. php artisan db:seed --class=MechanicalEngineeringDataSeeder\n";
echo "6. php artisan db:seed --class=MediaSeeder\n";
echo "7. [CREATE] ProductCategorySeeder\n";
echo "8. [CREATE] SampleProductSeeder\n";
echo "9. [CREATE] MarketplaceDataSeeder\n";

echo "\n🎯 AUDIT COMPLETE - READY FOR RECOVERY AND DEVELOPMENT!\n";
