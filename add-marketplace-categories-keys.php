<?php

/**
 * ADD MARKETPLACE CATEGORIES KEYS
 * Thêm keys thiếu cho marketplace/categories/index.blade.php
 */

echo "=== ADDING MARKETPLACE CATEGORIES KEYS ===\n\n";

// Extract keys from marketplace/categories/index.blade.php
$categoriesFile = __DIR__ . '/resources/views/marketplace/categories/index.blade.php';

if (!file_exists($categoriesFile)) {
    echo "❌ File not found: $categoriesFile\n";
    exit(1);
}

$content = file_get_contents($categoriesFile);

// Extract all translation keys
preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $directMatches);
preg_match_all('/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $helperMatches);

$allKeys = [];

// Process direct __ calls
foreach ($directMatches[1] as $key) {
    $allKeys[] = $key;
}

// Process t_helper calls
foreach ($helperMatches[1] as $i => $helper) {
    $key = $helperMatches[1][$i] . '.' . $helperMatches[2][$i];
    $allKeys[] = $key;
}

$allKeys = array_unique($allKeys);

echo "Found " . count($allKeys) . " unique keys in marketplace categories file:\n";
foreach ($allKeys as $key) {
    echo "  - $key\n";
}

// Define translations for marketplace categories keys
$categoriesKeys = [
    // Main page
    'categories.title' => ['vi' => 'Danh mục sản phẩm', 'en' => 'Product Categories'],
    'categories.page_title' => ['vi' => 'Danh mục Marketplace', 'en' => 'Marketplace Categories'],
    'categories.description' => ['vi' => 'Khám phá các danh mục sản phẩm trong marketplace', 'en' => 'Explore product categories in the marketplace'],
    'categories.browse_categories' => ['vi' => 'Duyệt danh mục', 'en' => 'Browse Categories'],
    'categories.all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    
    // Category display
    'categories.products_count' => ['vi' => 'sản phẩm', 'en' => 'products'],
    'categories.view_category' => ['vi' => 'Xem danh mục', 'en' => 'View Category'],
    'categories.view_all_products' => ['vi' => 'Xem tất cả sản phẩm', 'en' => 'View All Products'],
    'categories.no_products' => ['vi' => 'Chưa có sản phẩm', 'en' => 'No products yet'],
    'categories.popular_categories' => ['vi' => 'Danh mục phổ biến', 'en' => 'Popular Categories'],
    'categories.featured_categories' => ['vi' => 'Danh mục nổi bật', 'en' => 'Featured Categories'],
    
    // Search and filter
    'categories.search_categories' => ['vi' => 'Tìm kiếm danh mục', 'en' => 'Search Categories'],
    'categories.search_placeholder' => ['vi' => 'Nhập tên danh mục...', 'en' => 'Enter category name...'],
    'categories.filter_by' => ['vi' => 'Lọc theo', 'en' => 'Filter by'],
    'categories.sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort by'],
    'categories.sort_name' => ['vi' => 'Tên', 'en' => 'Name'],
    'categories.sort_products_count' => ['vi' => 'Số sản phẩm', 'en' => 'Product Count'],
    'categories.sort_popularity' => ['vi' => 'Độ phổ biến', 'en' => 'Popularity'],
    
    // Category types
    'categories.main_categories' => ['vi' => 'Danh mục chính', 'en' => 'Main Categories'],
    'categories.subcategories' => ['vi' => 'Danh mục con', 'en' => 'Subcategories'],
    'categories.parent_category' => ['vi' => 'Danh mục cha', 'en' => 'Parent Category'],
    
    // Actions
    'categories.explore' => ['vi' => 'Khám phá', 'en' => 'Explore'],
    'categories.browse' => ['vi' => 'Duyệt', 'en' => 'Browse'],
    'categories.view_details' => ['vi' => 'Xem chi tiết', 'en' => 'View Details'],
    
    // Stats
    'categories.total_categories' => ['vi' => 'Tổng số danh mục', 'en' => 'Total Categories'],
    'categories.active_categories' => ['vi' => 'Danh mục hoạt động', 'en' => 'Active Categories'],
    'categories.new_this_month' => ['vi' => 'Mới trong tháng', 'en' => 'New This Month'],
    
    // Empty states
    'categories.no_categories_found' => ['vi' => 'Không tìm thấy danh mục nào', 'en' => 'No categories found'],
    'categories.try_different_search' => ['vi' => 'Thử tìm kiếm khác', 'en' => 'Try a different search'],
    'categories.browse_all' => ['vi' => 'Duyệt tất cả', 'en' => 'Browse All'],
    
    // Category info
    'categories.category_info' => ['vi' => 'Thông tin danh mục', 'en' => 'Category Information'],
    'categories.created_date' => ['vi' => 'Ngày tạo', 'en' => 'Created Date'],
    'categories.last_updated' => ['vi' => 'Cập nhật lần cuối', 'en' => 'Last Updated'],
    'categories.status' => ['vi' => 'Trạng thái', 'en' => 'Status'],
    'categories.active' => ['vi' => 'Hoạt động', 'en' => 'Active'],
    'categories.inactive' => ['vi' => 'Không hoạt động', 'en' => 'Inactive'],
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

echo "\n📁 Processing marketplace categories keys\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $categoriesKeys, 'vi')) {
    $totalAdded = count($categoriesKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $categoriesKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($categoriesKeys) . "\n";
echo "Keys processed: " . count($categoriesKeys) . "\n";

echo "\n✅ Marketplace categories keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
