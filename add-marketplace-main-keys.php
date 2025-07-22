<?php

/**
 * ADD MARKETPLACE MAIN KEYS
 * Thêm các keys marketplace.marketplace.* thiếu
 */

echo "=== ADDING MARKETPLACE MAIN KEYS ===\n\n";

// Marketplace main keys
$marketplaceKeys = [
    // Main marketplace
    'marketplace.title' => ['vi' => 'Marketplace', 'en' => 'Marketplace'],
    'marketplace.product_categories' => ['vi' => 'Danh mục sản phẩm', 'en' => 'Product Categories'],
    'marketplace.browse_products_by_category' => ['vi' => 'Duyệt sản phẩm theo danh mục', 'en' => 'Browse products by category'],
    'marketplace.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'marketplace.view_options' => ['vi' => 'Tùy chọn hiển thị', 'en' => 'View Options'],
    'marketplace.grid_view' => ['vi' => 'Xem dạng lưới', 'en' => 'Grid View'],
    'marketplace.list_view' => ['vi' => 'Xem dạng danh sách', 'en' => 'List View'],
    'marketplace.sort_by_name' => ['vi' => 'Sắp xếp theo tên', 'en' => 'Sort by Name'],
    'marketplace.sort_by_product_count' => ['vi' => 'Sắp xếp theo số sản phẩm', 'en' => 'Sort by Product Count'],
    
    // Statistics
    'marketplace.total_categories' => ['vi' => 'Tổng danh mục', 'en' => 'Total Categories'],
    'marketplace.total_products' => ['vi' => 'Tổng sản phẩm', 'en' => 'Total Products'],
    'marketplace.active_sellers' => ['vi' => 'Người bán hoạt động', 'en' => 'Active Sellers'],
    'marketplace.new_this_week' => ['vi' => 'Mới trong tuần', 'en' => 'New This Week'],
    
    // Featured
    'marketplace.featured_categories' => ['vi' => 'Danh mục nổi bật', 'en' => 'Featured Categories'],
    'marketplace.featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
    
    // Navigation
    'marketplace.browse' => ['vi' => 'Duyệt', 'en' => 'Browse'],
    'marketplace.all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    'marketplace.subcategories' => ['vi' => 'Danh mục con', 'en' => 'Subcategories'],
    'marketplace.more' => ['vi' => 'Thêm', 'en' => 'More'],
    'marketplace.browse_products' => ['vi' => 'Duyệt sản phẩm', 'en' => 'Browse Products'],
    
    // Additional info
    'marketplace.commission' => ['vi' => 'Hoa hồng', 'en' => 'Commission'],
    'marketplace.watch_for_new_products' => ['vi' => 'Theo dõi sản phẩm mới', 'en' => 'Watch for new products'],
    'marketplace.updated' => ['vi' => 'Đã cập nhật', 'en' => 'Updated'],
    'marketplace.trending' => ['vi' => 'Xu hướng', 'en' => 'Trending'],
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

echo "📁 Processing marketplace main keys\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $marketplaceKeys, 'vi')) {
    $totalAdded = count($marketplaceKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $marketplaceKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($marketplaceKeys) . "\n";
echo "Keys processed: " . count($marketplaceKeys) . "\n";

echo "\n✅ Marketplace main keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
