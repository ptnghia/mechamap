<?php

/**
 * ADD SIDEBAR MARKETPLACE KEYS
 * Thêm tất cả keys thiếu cho components/sidebar-marketplace.blade.php
 */

echo "=== ADDING SIDEBAR MARKETPLACE KEYS ===\n\n";

// All sidebar marketplace keys organized by category
$sidebarMarketplaceKeys = [
    // Marketplace sidebar keys
    'marketplace.engineering_marketplace' => ['vi' => 'Thị trường Kỹ thuật', 'en' => 'Engineering Marketplace'],
    'marketplace.buy_sell_engineering_products' => ['vi' => 'Mua bán sản phẩm kỹ thuật', 'en' => 'Buy & sell engineering products'],
    'marketplace.total_products' => ['vi' => 'Tổng sản phẩm', 'en' => 'Total Products'],
    'marketplace.total_sales' => ['vi' => 'Tổng doanh số', 'en' => 'Total Sales'],
    'marketplace.avg_price_vnd' => ['vi' => 'Giá trung bình (VND)', 'en' => 'Avg Price (VND)'],
    'marketplace.active_sellers' => ['vi' => 'Người bán hoạt động', 'en' => 'Active Sellers'],
    'marketplace.list_product' => ['vi' => 'Đăng sản phẩm', 'en' => 'List Product'],
    'marketplace.join_marketplace' => ['vi' => 'Tham gia thị trường', 'en' => 'Join Marketplace'],
    'marketplace.product_categories' => ['vi' => 'Danh mục sản phẩm', 'en' => 'Product Categories'],
    'marketplace.products' => ['vi' => 'sản phẩm', 'en' => 'products'],
    'marketplace.hot_products' => ['vi' => 'Sản phẩm hot', 'en' => 'Hot Products'],
    'marketplace.top_sellers' => ['vi' => 'Người bán hàng đầu', 'en' => 'Top Sellers'],
    'marketplace.sales' => ['vi' => 'doanh số', 'en' => 'sales'],
    'marketplace.payment_methods' => ['vi' => 'Phương thức thanh toán', 'en' => 'Payment Methods'],
    'marketplace.international_cards' => ['vi' => 'Thẻ quốc tế', 'en' => 'International Cards'],
    'marketplace.vietnam_banking' => ['vi' => 'Ngân hàng Việt Nam', 'en' => 'Vietnam Banking'],
    'marketplace.secure_payment_guarantee' => ['vi' => 'Đảm bảo thanh toán an toàn', 'en' => 'Secure payment guarantee'],
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

echo "📁 Processing sidebar marketplace keys for sidebar.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/sidebar.php";
if (addKeysToFile($viFile, $sidebarMarketplaceKeys, 'vi')) {
    $totalAdded = count($sidebarMarketplaceKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/sidebar.php";
addKeysToFile($enFile, $sidebarMarketplaceKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total sidebar marketplace keys added: " . count($sidebarMarketplaceKeys) . "\n";

echo "\n✅ Sidebar marketplace keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
