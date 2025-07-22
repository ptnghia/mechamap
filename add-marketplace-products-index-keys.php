<?php

/**
 * ADD MARKETPLACE PRODUCTS INDEX KEYS
 * Thêm tất cả keys thiếu cho marketplace/products/index.blade.php
 */

echo "=== ADDING MARKETPLACE PRODUCTS INDEX KEYS ===\n\n";

// All marketplace products index keys
$marketplaceProductsIndexKeys = [
    'marketplace.discover_products' => ['vi' => 'Khám phá sản phẩm', 'en' => 'Discover Products'],
    'marketplace.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'marketplace.sort' => ['vi' => 'Sắp xếp', 'en' => 'Sort'],
    'marketplace.relevance' => ['vi' => 'Độ liên quan', 'en' => 'Relevance'],
    'marketplace.latest' => ['vi' => 'Mới nhất', 'en' => 'Latest'],
    'marketplace.price_low_to_high' => ['vi' => 'Giá thấp đến cao', 'en' => 'Price: Low to High'],
    'marketplace.price_high_to_low' => ['vi' => 'Giá cao đến thấp', 'en' => 'Price: High to Low'],
    'marketplace.highest_rated' => ['vi' => 'Đánh giá cao nhất', 'en' => 'Highest Rated'],
    'marketplace.most_popular' => ['vi' => 'Phổ biến nhất', 'en' => 'Most Popular'],
    'marketplace.name_a_z' => ['vi' => 'Tên A-Z', 'en' => 'Name A-Z'],
    'marketplace.view' => ['vi' => 'Xem', 'en' => 'View'],
    'marketplace.no_products_found' => ['vi' => 'Không tìm thấy sản phẩm nào', 'en' => 'No products found'],
    'marketplace.try_adjusting_filters' => ['vi' => 'Thử điều chỉnh bộ lọc của bạn', 'en' => 'Try adjusting your filters'],
    'marketplace.view_all_products' => ['vi' => 'Xem tất cả sản phẩm', 'en' => 'View All Products'],
];

// Navigation keys for nav.main.marketplace
$navigationKeys = [
    'main.marketplace' => ['vi' => 'Thị trường', 'en' => 'Marketplace'],
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

echo "📁 Processing marketplace products index keys for marketplace.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $marketplaceProductsIndexKeys, 'vi')) {
    $totalAdded = count($marketplaceProductsIndexKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $marketplaceProductsIndexKeys, 'en');

echo "\n📁 Processing navigation keys for navigation.php\n";

// Add to Vietnamese navigation file
$viNavFile = __DIR__ . "/resources/lang/vi/navigation.php";
addKeysToFile($viNavFile, $navigationKeys, 'vi');

// Add to English navigation file
$enNavFile = __DIR__ . "/resources/lang/en/navigation.php";
addKeysToFile($enNavFile, $navigationKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total marketplace products index keys added: " . count($marketplaceProductsIndexKeys) . "\n";
echo "Total navigation keys added: " . count($navigationKeys) . "\n";
echo "Total keys added: " . (count($marketplaceProductsIndexKeys) + count($navigationKeys)) . "\n";

echo "\n✅ Marketplace products index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
