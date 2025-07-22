<?php

/**
 * ADD MARKETPLACE INDEX KEYS
 * Thêm tất cả keys thiếu cho marketplace/index.blade.php
 */

echo "=== ADDING MARKETPLACE INDEX KEYS ===\n\n";

// All marketplace index keys
$marketplaceIndexKeys = [
    'marketplace.title' => ['vi' => 'Thị trường', 'en' => 'Marketplace'],
    'marketplace.subtitle' => ['vi' => 'Khám phá các sản phẩm kỹ thuật cơ khí chất lượng cao từ các nhà cung cấp uy tín', 'en' => 'Discover high-quality mechanical engineering products from trusted suppliers'],
    'marketplace.search_placeholder' => ['vi' => 'Tìm kiếm sản phẩm, nhà cung cấp...', 'en' => 'Search products, suppliers...'],
    'marketplace.products_available' => ['vi' => 'Sản phẩm có sẵn', 'en' => 'Products Available'],
    'marketplace.verified_sellers' => ['vi' => 'Nhà bán đã xác minh', 'en' => 'Verified Sellers'],
    'marketplace.browse_categories' => ['vi' => 'Duyệt danh mục', 'en' => 'Browse Categories'],
    'marketplace.view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
    'marketplace.items' => ['vi' => 'sản phẩm', 'en' => 'items'],
    'marketplace.no_categories_available' => ['vi' => 'Không có danh mục nào khả dụng', 'en' => 'No categories available'],
    'marketplace.featured_products' => ['vi' => 'Sản phẩm nổi bật', 'en' => 'Featured Products'],
    'marketplace.no_featured_products_available' => ['vi' => 'Không có sản phẩm nổi bật nào', 'en' => 'No featured products available'],
    'marketplace.check_back_later' => ['vi' => 'Vui lòng quay lại sau', 'en' => 'Please check back later'],
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

echo "📁 Processing marketplace index keys for marketplace.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $marketplaceIndexKeys, 'vi')) {
    $totalAdded = count($marketplaceIndexKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $marketplaceIndexKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total marketplace index keys added: " . count($marketplaceIndexKeys) . "\n";

echo "\n✅ Marketplace index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
