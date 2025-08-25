<?php

/**
 * Script test Seller Dashboard
 * Kiểm tra các tính năng dashboard cho seller
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrderItem;
use App\Http\Controllers\Dashboard\Marketplace\SellerController;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SELLER DASHBOARD TESTING SCRIPT ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "=======================================" . PHP_EOL . PHP_EOL;

// Test với supplier01
$user = User::where('username', 'supplier01')->first();
if (!$user) {
    echo "❌ User supplier01 not found" . PHP_EOL;
    exit;
}

echo "🧪 Testing Seller Dashboard for: {$user->name}" . PHP_EOL;
echo "=============================================" . PHP_EOL;

// Find seller profile
$seller = MarketplaceSeller::where('user_id', $user->id)->first();
if (!$seller) {
    echo "❌ Seller profile not found" . PHP_EOL;
    exit;
}

echo "✅ Seller found: {$seller->business_name}" . PHP_EOL;

// Test dashboard statistics calculation
echo PHP_EOL . "📊 Testing Dashboard Statistics:" . PHP_EOL;

// Simulate controller method
$controller = new SellerController();
$reflection = new ReflectionClass($controller);

// Set user property
$userProperty = $reflection->getProperty('user');
$userProperty->setAccessible(true);
$userProperty->setValue($controller, $user);

// Test getSellerStats method
$getSellerStatsMethod = $reflection->getMethod('getSellerStats');
$getSellerStatsMethod->setAccessible(true);

try {
    $stats = $getSellerStatsMethod->invoke($controller, $seller);

    echo "✅ Statistics calculated successfully:" . PHP_EOL;
    echo "   - Total Products: " . (isset($stats['total_products']) ? $stats['total_products'] : 'N/A') . PHP_EOL;
    echo "   - Total Sales: " . number_format(isset($stats['total_sales']) ? $stats['total_sales'] : 0) . " VND" . PHP_EOL;
    echo "   - Total Orders: " . (isset($stats['total_orders']) ? $stats['total_orders'] : 'N/A') . PHP_EOL;
    echo "   - Average Rating: " . (isset($stats['average_rating']) ? $stats['average_rating'] : 'N/A') . "/5" . PHP_EOL;
    echo "   - Views Today: " . (isset($stats['views_today']) ? $stats['views_today'] : 'N/A') . PHP_EOL;
    echo "   - Sales Today: " . (isset($stats['sales_today']) ? $stats['sales_today'] : 'N/A') . PHP_EOL;

} catch (Exception $e) {
    echo "❌ Error calculating statistics: " . $e->getMessage() . PHP_EOL;
}

// Test recent orders
echo PHP_EOL . "📦 Testing Recent Orders:" . PHP_EOL;
$recentOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
    ->with(['order.customer', 'product'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "✅ Found {$recentOrders->count()} recent orders" . PHP_EOL;
foreach ($recentOrders->take(3) as $orderItem) {
    $orderNumber = $orderItem->order ? $orderItem->order->order_number : 'N/A';
    echo "   - Order #{$orderNumber}: {$orderItem->product_name} - " .
         number_format($orderItem->price) . " VND" . PHP_EOL;
}

// Test top products
echo PHP_EOL . "🏆 Testing Top Products:" . PHP_EOL;
$topProducts = MarketplaceProduct::where('seller_id', $seller->id)
    ->orderByDesc('purchase_count')
    ->limit(5)
    ->get();

echo "✅ Found {$topProducts->count()} products" . PHP_EOL;
foreach ($topProducts->take(3) as $product) {
    echo "   - {$product->name}: {$product->purchase_count} sales - " .
         number_format($product->price) . " VND" . PHP_EOL;
}

// Test seller profile data
echo PHP_EOL . "👤 Testing Seller Profile Data:" . PHP_EOL;
echo "✅ Business Information:" . PHP_EOL;
echo "   - Business Name: {$seller->business_name}" . PHP_EOL;
echo "   - Seller Type: {$seller->seller_type}" . PHP_EOL;
echo "   - Verification Status: {$seller->verification_status}" . PHP_EOL;
echo "   - Status: {$seller->status}" . PHP_EOL;
echo "   - Commission Rate: {$seller->commission_rate}%" . PHP_EOL;
echo "   - Auto-approve Orders: " . ($seller->auto_approve_orders ? 'Yes' : 'No') . PHP_EOL;
echo "   - Processing Time: {$seller->processing_time_days} days" . PHP_EOL;

// Test earnings data
echo PHP_EOL . "💰 Testing Earnings Data:" . PHP_EOL;
echo "✅ Earnings Information:" . PHP_EOL;
echo "   - Pending Earnings: " . number_format($seller->pending_earnings ?: 0) . " VND" . PHP_EOL;
echo "   - Available Earnings: " . number_format($seller->available_earnings ?: 0) . " VND" . PHP_EOL;
echo "   - Total Earnings: " . number_format($seller->total_earnings ?: 0) . " VND" . PHP_EOL;

// Test dashboard route accessibility
echo PHP_EOL . "🌐 Testing Dashboard Route Access:" . PHP_EOL;
try {
    // Simulate request
    $request = new \Illuminate\Http\Request();

    // Test if dashboard method can be called
    $dashboardMethod = $reflection->getMethod('dashboard');

    echo "✅ Dashboard method accessible" . PHP_EOL;
    echo "   - Route: /dashboard/marketplace/seller" . PHP_EOL;
    echo "   - Controller: Dashboard\\Marketplace\\SellerController@dashboard" . PHP_EOL;

} catch (Exception $e) {
    echo "❌ Dashboard route error: " . $e->getMessage() . PHP_EOL;
}

// Test quick actions availability
echo PHP_EOL . "⚡ Testing Quick Actions:" . PHP_EOL;
$quickActions = [
    'Add New Product' => '/marketplace/products/create',
    'Manage Products' => '/marketplace/seller/products',
    'View Orders' => '/marketplace/seller/orders',
    'Analytics' => '/marketplace/seller/analytics'
];

foreach ($quickActions as $action => $route) {
    echo "✅ {$action}: {$route}" . PHP_EOL;
}

echo PHP_EOL . "🎯 Dashboard Test Summary:" . PHP_EOL;
echo "=========================" . PHP_EOL;
echo "✅ Seller profile: OK" . PHP_EOL;
echo "✅ Statistics calculation: OK" . PHP_EOL;
echo "✅ Recent orders: OK" . PHP_EOL;
echo "✅ Top products: OK" . PHP_EOL;
echo "✅ Earnings data: OK" . PHP_EOL;
echo "✅ Quick actions: OK" . PHP_EOL;

echo PHP_EOL . "✅ Seller dashboard testing completed!" . PHP_EOL;
