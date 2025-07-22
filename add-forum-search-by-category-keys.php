<?php

/**
 * ADD FORUM SEARCH BY CATEGORY KEYS
 * Thêm tất cả keys thiếu cho forums/search-by-category.blade.php
 */

echo "=== ADDING FORUM SEARCH BY CATEGORY KEYS ===\n\n";

// All forum search by category keys organized by category
$forumSearchByCategoryKeys = [
    // Forum search keys
    'search.search_in_category' => ['vi' => 'Tìm kiếm trong danh mục', 'en' => 'Search in Category'],
    'search.search_by_category' => ['vi' => 'Tìm kiếm theo danh mục', 'en' => 'Search by Category'],
    'search.results_for' => ['vi' => 'Kết quả cho', 'en' => 'Results for'],
    'search.search_in_forum' => ['vi' => 'Tìm kiếm trong diễn đàn', 'en' => 'Search in Forum'],
    'search.browse_all_threads_in_category' => ['vi' => 'Duyệt tất cả chủ đề trong danh mục', 'en' => 'Browse all threads in category'],
    'search.select_category_to_search' => ['vi' => 'Chọn danh mục để tìm kiếm', 'en' => 'Select category to search'],
    'search.threads_found' => ['vi' => 'chủ đề tìm thấy', 'en' => 'threads found'],
    'search.select_category' => ['vi' => 'Chọn danh mục', 'en' => 'Select Category'],
    'search.choose_category' => ['vi' => 'Chọn danh mục', 'en' => 'Choose Category'],
    'search.forums_count' => ['vi' => 'diễn đàn', 'en' => 'forums'],
    'search.search_query_optional' => ['vi' => 'Từ khóa tìm kiếm (tùy chọn)', 'en' => 'Search Query (Optional)'],
    'search.search_query_placeholder' => ['vi' => 'Nhập từ khóa...', 'en' => 'Enter keywords...'],
    'search.search_in_selected_category' => ['vi' => 'Tìm kiếm trong danh mục đã chọn', 'en' => 'Search in Selected Category'],
    'search.change_category' => ['vi' => 'Thay đổi danh mục', 'en' => 'Change Category'],
    'search.search_button' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'search.forums_in_category' => ['vi' => 'Diễn đàn trong danh mục :category', 'en' => 'Forums in :category'],
    'search.available_categories' => ['vi' => 'Danh mục có sẵn', 'en' => 'Available Categories'],
    'search.no_results_found' => ['vi' => 'Không tìm thấy kết quả nào', 'en' => 'No results found'],
    'search.no_threads_in_category' => ['vi' => 'Không tìm thấy chủ đề nào trong danh mục :category cho', 'en' => 'No threads found in :category for'],
    'search.browse_all_threads' => ['vi' => 'Duyệt tất cả chủ đề', 'en' => 'Browse All Threads'],
    'search.back_to_forums' => ['vi' => 'Quay lại diễn đàn', 'en' => 'Back to Forums'],
    'search.category_info' => ['vi' => 'Thông tin danh mục', 'en' => 'Category Info'],
    'search.other_categories' => ['vi' => 'Danh mục khác', 'en' => 'Other Categories'],
    'search.quick_actions' => ['vi' => 'Hành động nhanh', 'en' => 'Quick Actions'],
    'search.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'search.all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    
    // Forum general keys
    'threads' => ['vi' => 'chủ đề', 'en' => 'threads'],
    'no_threads' => ['vi' => 'Không có chủ đề nào', 'en' => 'No threads'],
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

echo "📁 Processing forum search by category keys for forum.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/forum.php";
if (addKeysToFile($viFile, $forumSearchByCategoryKeys, 'vi')) {
    $totalAdded = count($forumSearchByCategoryKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/forum.php";
addKeysToFile($enFile, $forumSearchByCategoryKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total forum search by category keys added: " . count($forumSearchByCategoryKeys) . "\n";

echo "\n✅ Forum search by category keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
