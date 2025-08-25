<?php

/**
 * Script test tính năng Seller Marketplace
 * Kiểm tra các tài khoản seller và quyền của chúng
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Services\UnifiedMarketplacePermissionService;
use App\Services\MarketplacePermissionService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MARKETPLACE SELLER TESTING SCRIPT ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "=========================================" . PHP_EOL . PHP_EOL;

// Test accounts
$testAccounts = [
    'supplier01' => [
        'password' => 'O!0omj-kJ6yP',
        'expected_role' => 'supplier',
        'can_sell' => ['digital', 'new_product'],
        'commission_rate' => 3.0
    ],
    'manufacturer01' => [
        'password' => 'O!0omj-kJ6yP',
        'expected_role' => 'manufacturer',
        'can_sell' => ['digital'],
        'commission_rate' => 5.0
    ],
    'brand01' => [
        'password' => 'O!0omj-kJ6yP',
        'expected_role' => 'brand',
        'can_sell' => [],
        'commission_rate' => 0
    ]
];

foreach ($testAccounts as $username => $config) {
    echo "🧪 Testing Account: {$username}" . PHP_EOL;
    echo "================================" . PHP_EOL;

    // Find user
    $user = User::where('username', $username)->first();
    if (!$user) {
        echo "❌ User not found: {$username}" . PHP_EOL . PHP_EOL;
        continue;
    }

    echo "✅ User found: {$user->name} ({$user->email})" . PHP_EOL;
    echo "   Role: {$user->role}" . PHP_EOL;

    // Check role
    if ($user->role !== $config['expected_role']) {
        echo "❌ Role mismatch! Expected: {$config['expected_role']}, Got: {$user->role}" . PHP_EOL;
    } else {
        echo "✅ Role correct: {$user->role}" . PHP_EOL;
    }

    // Find seller profile
    $seller = MarketplaceSeller::where('user_id', $user->id)->first();
    if (!$seller) {
        echo "❌ Seller profile not found" . PHP_EOL . PHP_EOL;
        continue;
    }

    echo "✅ Seller profile found: {$seller->business_name}" . PHP_EOL;
    echo "   Seller Type: {$seller->seller_type}" . PHP_EOL;
    echo "   Verification: {$seller->verification_status}" . PHP_EOL;
    echo "   Status: {$seller->status}" . PHP_EOL;
    echo "   Commission Rate: {$seller->commission_rate}%" . PHP_EOL;

    // Test selling permissions
    echo PHP_EOL . "🔐 Testing Selling Permissions:" . PHP_EOL;
    $productTypes = ['digital', 'new_product', 'used_product'];

    foreach ($productTypes as $type) {
        // Test both services
        $canSellUnified = UnifiedMarketplacePermissionService::canSell($user, $type);
        $canSellBasic = MarketplacePermissionService::canSell($user, $type);
        $expected = in_array($type, $config['can_sell']);

        // Debug unified service
        $permissions = UnifiedMarketplacePermissionService::getUserPermissions($user);
        $summary = UnifiedMarketplacePermissionService::getPermissionSummary($user);
        $effectiveRole = $summary['effective_role'];
        $isVerified = UnifiedMarketplacePermissionService::isBusinessVerified($user);

        // Test direct matrix access
        $reflection = new ReflectionClass(UnifiedMarketplacePermissionService::class);
        $matrixMethod = $reflection->getMethod('getPermissionMatrix');
        $matrixMethod->setAccessible(true);
        $matrix = $matrixMethod->invoke(null);
        $matrixHasRole = isset($matrix[$effectiveRole]);

        echo "   {$type}:" . PHP_EOL;
        echo "     Unified Service: " . ($canSellUnified ? "✅ CAN" : "❌ CANNOT") . " sell" . PHP_EOL;
        echo "     Basic Service: " . ($canSellBasic ? "✅ CAN" : "❌ CANNOT") . " sell" . PHP_EOL;
        echo "     Expected: " . ($expected ? "CAN" : "CANNOT") . " sell" . PHP_EOL;
        echo "     Debug - Effective Role: {$effectiveRole}" . PHP_EOL;
        echo "     Debug - Is Verified: " . ($isVerified ? "Yes" : "No") . PHP_EOL;
        echo "     Debug - Matrix Has Role: " . ($matrixHasRole ? "Yes" : "No") . PHP_EOL;
        echo "     Debug - Sell Permissions: " . implode(', ', $permissions['sell'] ?? []) . PHP_EOL;
        if ($matrixHasRole) {
            echo "     Debug - Matrix Sell for Role: " . implode(', ', $matrix[$effectiveRole]['sell'] ?? []) . PHP_EOL;
        }

        if ($canSellBasic === $expected) {
            echo "     Status: ✅ CORRECT (Basic Service)" . PHP_EOL;
        } elseif ($canSellUnified === $expected) {
            echo "     Status: ✅ CORRECT (Unified Service)" . PHP_EOL;
        } else {
            echo "     Status: ❌ BOTH SERVICES INCORRECT" . PHP_EOL;
        }
    }

    // Check products
    $productCount = MarketplaceProduct::where('seller_id', $seller->id)->count();
    echo PHP_EOL . "📦 Products: {$productCount} total" . PHP_EOL;

    if ($productCount > 0) {
        $products = MarketplaceProduct::where('seller_id', $seller->id)
            ->select('name', 'product_type', 'status', 'price')
            ->limit(3)
            ->get();

        foreach ($products as $product) {
            echo "   - {$product->name} ({$product->product_type}) - {$product->status} - {$product->price} VND" . PHP_EOL;
        }
    }

    echo PHP_EOL . "📊 Seller Statistics:" . PHP_EOL;
    echo "   Total Sales: " . number_format($seller->total_sales ?? 0) . " VND" . PHP_EOL;
    echo "   Total Products: {$seller->total_products}" . PHP_EOL;
    echo "   Active Products: {$seller->active_products}" . PHP_EOL;
    echo "   Rating: " . ($seller->rating_average ?? 0) . "/5 ({$seller->rating_count} reviews)" . PHP_EOL;

    echo PHP_EOL . "💰 Earnings:" . PHP_EOL;
    echo "   Pending: " . number_format($seller->pending_earnings ?? 0) . " VND" . PHP_EOL;
    echo "   Available: " . number_format($seller->available_earnings ?? 0) . " VND" . PHP_EOL;
    echo "   Total: " . number_format($seller->total_earnings ?? 0) . " VND" . PHP_EOL;

    echo PHP_EOL . "⚙️ Settings:" . PHP_EOL;
    echo "   Auto-approve orders: " . ($seller->auto_approve_orders ? 'Yes' : 'No') . PHP_EOL;
    echo "   Processing time: {$seller->processing_time_days} days" . PHP_EOL;

    echo PHP_EOL . str_repeat("=", 50) . PHP_EOL . PHP_EOL;
}

// Overall marketplace stats
echo "📈 MARKETPLACE OVERVIEW" . PHP_EOL;
echo "======================" . PHP_EOL;
echo "Total Products: " . MarketplaceProduct::count() . PHP_EOL;
echo "Active Products: " . MarketplaceProduct::where('status', 'approved')->where('is_active', true)->count() . PHP_EOL;
echo "Total Sellers: " . MarketplaceSeller::count() . PHP_EOL;
echo "Active Sellers: " . MarketplaceSeller::where('status', 'active')->count() . PHP_EOL;
echo "Verified Sellers: " . MarketplaceSeller::where('verification_status', 'verified')->count() . PHP_EOL;

echo PHP_EOL . "✅ Seller testing completed!" . PHP_EOL;
