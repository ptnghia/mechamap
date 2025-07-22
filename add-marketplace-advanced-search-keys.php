<?php

/**
 * ADD MARKETPLACE ADVANCED SEARCH KEYS
 * Thêm tất cả keys thiếu cho components/marketplace/advanced-search.blade.php
 */

echo "=== ADDING MARKETPLACE ADVANCED SEARCH KEYS ===\n\n";

// All marketplace advanced search keys
$marketplaceKeys = [
    // Main search
    'marketplace.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'marketplace.close' => ['vi' => 'Đóng', 'en' => 'Close'],
    'marketplace.keywords' => ['vi' => 'Từ khóa', 'en' => 'Keywords'],
    'marketplace.search_descriptions' => ['vi' => 'Tìm trong tên và mô tả sản phẩm...', 'en' => 'Search in product names and descriptions...'],
    'marketplace.use_quotes_help' => ['vi' => 'Sử dụng dấu ngoặc kép để tìm cụm từ chính xác', 'en' => 'Use quotes for exact phrase search'],
    
    // Categories and types
    'marketplace.category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'marketplace.all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    'marketplace.product_type' => ['vi' => 'Loại sản phẩm', 'en' => 'Product Type'],
    'marketplace.all_types' => ['vi' => 'Tất cả loại', 'en' => 'All Types'],
    'marketplace.physical_products' => ['vi' => 'Sản phẩm vật lý', 'en' => 'Physical Products'],
    'marketplace.digital_products' => ['vi' => 'Sản phẩm số', 'en' => 'Digital Products'],
    'marketplace.services' => ['vi' => 'Dịch vụ', 'en' => 'Services'],
    
    // Seller types
    'marketplace.seller_type' => ['vi' => 'Loại người bán', 'en' => 'Seller Type'],
    'marketplace.all_sellers' => ['vi' => 'Tất cả người bán', 'en' => 'All Sellers'],
    'marketplace.suppliers' => ['vi' => 'Nhà cung cấp', 'en' => 'Suppliers'],
    'marketplace.manufacturers' => ['vi' => 'Nhà sản xuất', 'en' => 'Manufacturers'],
    'marketplace.brands' => ['vi' => 'Thương hiệu', 'en' => 'Brands'],
    
    // Price range
    'marketplace.price_range_usd' => ['vi' => 'Khoảng giá (USD)', 'en' => 'Price Range (USD)'],
    'marketplace.min_price' => ['vi' => 'Giá tối thiểu', 'en' => 'Min Price'],
    'marketplace.max_price' => ['vi' => 'Giá tối đa', 'en' => 'Max Price'],
    
    // Materials
    'marketplace.material' => ['vi' => 'Vật liệu', 'en' => 'Material'],
    'marketplace.any_material' => ['vi' => 'Bất kỳ vật liệu', 'en' => 'Any Material'],
    'marketplace.steel' => ['vi' => 'Thép', 'en' => 'Steel'],
    'marketplace.aluminum' => ['vi' => 'Nhôm', 'en' => 'Aluminum'],
    'marketplace.stainless_steel' => ['vi' => 'Thép không gỉ', 'en' => 'Stainless Steel'],
    'marketplace.titanium' => ['vi' => 'Titan', 'en' => 'Titanium'],
    
    // File formats
    'marketplace.file_format' => ['vi' => 'Định dạng file', 'en' => 'File Format'],
    'marketplace.any_format' => ['vi' => 'Bất kỳ định dạng', 'en' => 'Any Format'],
    
    // Ratings
    'marketplace.minimum_rating' => ['vi' => 'Đánh giá tối thiểu', 'en' => 'Minimum Rating'],
    'marketplace.any_rating' => ['vi' => 'Bất kỳ đánh giá', 'en' => 'Any Rating'],
    'marketplace.4_plus_stars' => ['vi' => '4+ sao', 'en' => '4+ Stars'],
    'marketplace.3_plus_stars' => ['vi' => '3+ sao', 'en' => '3+ Stars'],
    'marketplace.2_plus_stars' => ['vi' => '2+ sao', 'en' => '2+ Stars'],
    
    // Availability
    'marketplace.availability' => ['vi' => 'Tình trạng', 'en' => 'Availability'],
    'marketplace.in_stock_only' => ['vi' => 'Chỉ còn hàng', 'en' => 'In Stock Only'],
    'marketplace.featured_only' => ['vi' => 'Chỉ sản phẩm nổi bật', 'en' => 'Featured Only'],
    'marketplace.on_sale' => ['vi' => 'Đang giảm giá', 'en' => 'On Sale'],
    
    // Sorting
    'marketplace.sort_results_by' => ['vi' => 'Sắp xếp kết quả theo', 'en' => 'Sort Results By'],
    'marketplace.relevance' => ['vi' => 'Độ liên quan', 'en' => 'Relevance'],
    'marketplace.latest' => ['vi' => 'Mới nhất', 'en' => 'Latest'],
    'marketplace.price_low_to_high' => ['vi' => 'Giá thấp đến cao', 'en' => 'Price: Low to High'],
    'marketplace.price_high_to_low' => ['vi' => 'Giá cao đến thấp', 'en' => 'Price: High to Low'],
    'marketplace.highest_rated' => ['vi' => 'Đánh giá cao nhất', 'en' => 'Highest Rated'],
    'marketplace.most_popular' => ['vi' => 'Phổ biến nhất', 'en' => 'Most Popular'],
    'marketplace.name_a_z' => ['vi' => 'Tên A-Z', 'en' => 'Name A-Z'],
    
    // Actions
    'marketplace.search_products' => ['vi' => 'Tìm sản phẩm', 'en' => 'Search Products'],
    'marketplace.clear_all' => ['vi' => 'Xóa tất cả', 'en' => 'Clear All'],
    'marketplace.filters_applied' => ['vi' => 'bộ lọc được áp dụng', 'en' => 'filters applied'],
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

echo "📁 Processing marketplace advanced search keys for common.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
if (addKeysToFile($viFile, $marketplaceKeys, 'vi')) {
    $totalAdded = count($marketplaceKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $marketplaceKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total marketplace advanced search keys added: " . count($marketplaceKeys) . "\n";

echo "\n✅ Marketplace advanced search keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
