<?php

/**
 * ADD FORUM SEARCH ADVANCED KEYS
 * Thêm tất cả keys thiếu cho forums/search-advanced.blade.php
 */

echo "=== ADDING FORUM SEARCH ADVANCED KEYS ===\n\n";

// All forum search advanced keys
$forumSearchKeys = [
    // Main search
    'search.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'search.advanced_title' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'search.advanced_description' => ['vi' => 'Tìm kiếm chi tiết với nhiều bộ lọc và tùy chọn', 'en' => 'Detailed search with multiple filters and options'],
    'search.search_filters' => ['vi' => 'Bộ lọc tìm kiếm', 'en' => 'Search Filters'],
    
    // Search fields
    'search.keywords' => ['vi' => 'Từ khóa', 'en' => 'Keywords'],
    'search.keywords_placeholder' => ['vi' => 'Nhập từ khóa tìm kiếm...', 'en' => 'Enter search keywords...'],
    'search.author' => ['vi' => 'Tác giả', 'en' => 'Author'],
    'search.author_placeholder' => ['vi' => 'Tên tác giả...', 'en' => 'Author name...'],
    'search.category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'search.all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    'search.forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
    'search.all_forums' => ['vi' => 'Tất cả diễn đàn', 'en' => 'All Forums'],
    
    // Date filters
    'search.date_from' => ['vi' => 'Từ ngày', 'en' => 'Date From'],
    'search.date_to' => ['vi' => 'Đến ngày', 'en' => 'Date To'],
    
    // Sorting
    'search.sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort By'],
    'search.date' => ['vi' => 'Ngày', 'en' => 'Date'],
    'search.replies' => ['vi' => 'Phản hồi', 'en' => 'Replies'],
    'search.views' => ['vi' => 'Lượt xem', 'en' => 'Views'],
    'search.relevance' => ['vi' => 'Độ liên quan', 'en' => 'Relevance'],
    'search.sort_direction' => ['vi' => 'Hướng sắp xếp', 'en' => 'Sort Direction'],
    'search.descending' => ['vi' => 'Giảm dần', 'en' => 'Descending'],
    'search.ascending' => ['vi' => 'Tăng dần', 'en' => 'Ascending'],
    'search.desc' => ['vi' => 'giảm dần', 'en' => 'descending'],
    'search.asc' => ['vi' => 'tăng dần', 'en' => 'ascending'],
    
    // Actions
    'search.search_button' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'search.clear_filters' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear Filters'],
    'search.title' => ['vi' => 'Tìm kiếm cơ bản', 'en' => 'Basic Search'],
    
    // Results
    'search.results' => ['vi' => 'Kết quả tìm kiếm', 'en' => 'Search Results'],
    'search.no_results_found_advanced' => ['vi' => 'Không tìm thấy kết quả nào', 'en' => 'No results found'],
    'search.no_results_message_advanced' => ['vi' => 'Thử điều chỉnh bộ lọc hoặc sử dụng từ khóa khác để có kết quả tốt hơn.', 'en' => 'Try adjusting your filters or using different keywords for better results.'],
    
    // Search tips
    'search.search_tips' => ['vi' => 'Mẹo tìm kiếm', 'en' => 'Search Tips'],
    'search.tip_quotes' => ['vi' => 'Sử dụng dấu ngoặc kép để tìm cụm từ chính xác', 'en' => 'Use quotes for exact phrase search'],
    'search.tip_minimum_chars' => ['vi' => 'Tối thiểu 3 ký tự', 'en' => 'Minimum 3 characters'],
    'search.tip_browse_categories' => ['vi' => 'Duyệt theo danh mục để thu hẹp kết quả', 'en' => 'Browse by category to narrow results'],
    'search.tip_multiple_words' => ['vi' => 'Sử dụng nhiều từ khóa để tìm kiếm chính xác hơn', 'en' => 'Use multiple keywords for more precise search'],
    
    // Quick filters
    'search.quick_filters' => ['vi' => 'Bộ lọc nhanh', 'en' => 'Quick Filters'],
    'search.latest_threads' => ['vi' => 'Chủ đề mới nhất', 'en' => 'Latest Threads'],
    'search.most_replies' => ['vi' => 'Nhiều phản hồi nhất', 'en' => 'Most Replies'],
    'search.most_viewed' => ['vi' => 'Xem nhiều nhất', 'en' => 'Most Viewed'],
    'search.this_week' => ['vi' => 'Tuần này', 'en' => 'This Week'],
    
    // Additional sort options
    'search.repliesending' => ['vi' => 'phản hồi giảm dần', 'en' => 'replies descending'],
    'search.viewsending' => ['vi' => 'lượt xem giảm dần', 'en' => 'views descending'],
    'search.relevanceending' => ['vi' => 'độ liên quan giảm dần', 'en' => 'relevance descending'],
    'search.dateending' => ['vi' => 'ngày giảm dần', 'en' => 'date descending'],
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

echo "📁 Processing forum search advanced keys for forum.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/forum.php";
if (addKeysToFile($viFile, $forumSearchKeys, 'vi')) {
    $totalAdded = count($forumSearchKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/forum.php";
addKeysToFile($enFile, $forumSearchKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total forum search advanced keys added: " . count($forumSearchKeys) . "\n";

echo "\n✅ Forum search advanced keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
