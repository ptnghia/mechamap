<?php

/**
 * ADD MARKETPLACE SELLER DASHBOARD KEYS
 * Thêm tất cả keys thiếu cho marketplace/seller/dashboard.blade.php
 */

echo "=== ADDING MARKETPLACE SELLER DASHBOARD KEYS ===\n\n";

// All marketplace seller dashboard keys organized by category
$marketplaceSellerDashboardKeys = [
    // Seller dashboard keys
    'seller_dashboard' => ['vi' => 'Bảng điều khiển người bán', 'en' => 'Seller Dashboard'],
    'seller_menu' => ['vi' => 'Menu người bán', 'en' => 'Seller Menu'],
    'dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
    'my_products' => ['vi' => 'Sản phẩm của tôi', 'en' => 'My Products'],
    'my_orders' => ['vi' => 'Đơn hàng của tôi', 'en' => 'My Orders'],
    'analytics' => ['vi' => 'Phân tích', 'en' => 'Analytics'],
    'seller_info' => ['vi' => 'Thông tin người bán', 'en' => 'Seller Info'],
    'status' => ['vi' => 'Trạng thái', 'en' => 'Status'],
    'status.active' => ['vi' => 'Hoạt động', 'en' => 'Active'],
    'status.pending' => ['vi' => 'Chờ duyệt', 'en' => 'Pending'],
    'status.approved' => ['vi' => 'Đã duyệt', 'en' => 'Approved'],
    'welcome_seller' => ['vi' => 'Chào mừng, :name!', 'en' => 'Welcome, :name!'],
    'seller_dashboard_desc' => ['vi' => 'Quản lý sản phẩm, đơn hàng và theo dõi doanh số của bạn.', 'en' => 'Manage your products, orders and track your sales.'],
    'add_product' => ['vi' => 'Thêm sản phẩm', 'en' => 'Add Product'],
    'total_products' => ['vi' => 'Tổng sản phẩm', 'en' => 'Total Products'],
    'total_sales' => ['vi' => 'Tổng doanh số', 'en' => 'Total Sales'],
    'total_orders' => ['vi' => 'Tổng đơn hàng', 'en' => 'Total Orders'],
    'this_month_sales' => ['vi' => 'Doanh số tháng này', 'en' => 'This Month Sales'],
    'quick_actions' => ['vi' => 'Hành động nhanh', 'en' => 'Quick Actions'],
    'add_new_product' => ['vi' => 'Thêm sản phẩm mới', 'en' => 'Add New Product'],
    'manage_products' => ['vi' => 'Quản lý sản phẩm', 'en' => 'Manage Products'],
    'view_orders' => ['vi' => 'Xem đơn hàng', 'en' => 'View Orders'],
    'product_status' => ['vi' => 'Trạng thái sản phẩm', 'en' => 'Product Status'],
    'active' => ['vi' => 'Hoạt động', 'en' => 'Active'],
    'pending' => ['vi' => 'Chờ duyệt', 'en' => 'Pending'],
    'total' => ['vi' => 'Tổng cộng', 'en' => 'Total'],
    'recent_products' => ['vi' => 'Sản phẩm gần đây', 'en' => 'Recent Products'],
    'view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
    'no_products_yet' => ['vi' => 'Chưa có sản phẩm nào.', 'en' => 'No products yet.'],
    'recent_orders' => ['vi' => 'Đơn hàng gần đây', 'en' => 'Recent Orders'],
    'order_status.completed' => ['vi' => 'Hoàn thành', 'en' => 'Completed'],
    'order_status.pending' => ['vi' => 'Chờ xử lý', 'en' => 'Pending'],
    'order_status.processing' => ['vi' => 'Đang xử lý', 'en' => 'Processing'],
    'order_status.shipped' => ['vi' => 'Đã gửi', 'en' => 'Shipped'],
    'no_orders_yet' => ['vi' => 'Chưa có đơn hàng nào.', 'en' => 'No orders yet.'],
];

// Function to add keys to translation files
function addKeysToFile($filePath, $keys, $lang) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    // Find the last closing bracket (either ]; or );)
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        $lastBracketPos = strrpos($content, ');');
        if ($lastBracketPos === false) {
            echo "❌ Could not find closing bracket in $filePath\n";
            return false;
        }
    }
    
    // Build new keys string
    $newKeysString = '';
    $addedCount = 0;
    
    foreach ($keys as $key => $translations) {
        if (isset($translations[$lang])) {
            $value = $translations[$lang];
            // Escape single quotes in the value
            $value = str_replace("'", "\\'", $value);
            $newKeysString .= "  '$key' => '$value',\n";
            $addedCount++;
        }
    }
    
    if (empty($newKeysString)) {
        echo "ℹ️  No keys to add for $lang\n";
        return true;
    }
    
    // Insert new keys before the closing bracket
    $beforeClosing = substr($content, 0, $lastBracketPos);
    $afterClosing = substr($content, $lastBracketPos);
    
    $newContent = $beforeClosing . $newKeysString . $afterClosing;
    
    // Write back to file
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Added $addedCount keys to $filePath\n";
        return true;
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

echo "📁 Processing marketplace seller dashboard keys for marketplace.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $marketplaceSellerDashboardKeys, 'vi')) {
    $totalAdded = count($marketplaceSellerDashboardKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $marketplaceSellerDashboardKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total marketplace seller dashboard keys added: " . count($marketplaceSellerDashboardKeys) . "\n";

echo "\n✅ Marketplace seller dashboard keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
