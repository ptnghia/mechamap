<?php

/**
 * ADD SEARCH ADVANCED RESULTS KEYS
 * Thêm tất cả keys thiếu cho search/advanced-results.blade.php
 */

echo "=== ADDING SEARCH ADVANCED RESULTS KEYS ===\n\n";

// All search advanced results keys organized by category
$searchAdvancedResultsKeys = [
    // Search criteria keys
    'search_criteria' => ['vi' => 'Tiêu chí tìm kiếm', 'en' => 'Search Criteria'],
    'keywords' => ['vi' => 'Từ khóa', 'en' => 'Keywords'],
    'author' => ['vi' => 'Tác giả', 'en' => 'Author'],
    'forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
    'unknown' => ['vi' => 'Không xác định', 'en' => 'Unknown'],
    'date_range' => ['vi' => 'Khoảng thời gian', 'en' => 'Date Range'],
    'any' => ['vi' => 'Bất kỳ', 'en' => 'Any'],
    'to' => ['vi' => 'đến', 'en' => 'to'],
    'present' => ['vi' => 'Hiện tại', 'en' => 'Present'],
    'modify_search' => ['vi' => 'Chỉnh sửa tìm kiếm', 'en' => 'Modify Search'],
    
    // Sort and filter keys
    'sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort by'],
    'descending' => ['vi' => 'Giảm dần', 'en' => 'Descending'],
    'ascending' => ['vi' => 'Tăng dần', 'en' => 'Ascending'],
    'relevance' => ['vi' => 'Độ liên quan', 'en' => 'Relevance'],
    'date' => ['vi' => 'Ngày', 'en' => 'Date'],
    'replies' => ['vi' => 'Trả lời', 'en' => 'Replies'],
    
    // Results tabs
    'threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
    'posts' => ['vi' => 'Bài viết', 'en' => 'Posts'],
    
    // Content metadata
    'by' => ['vi' => 'Bởi', 'en' => 'By'],
    'in' => ['vi' => 'trong', 'en' => 'in'],
    'views' => ['vi' => 'lượt xem', 'en' => 'views'],
    'reply_in' => ['vi' => 'Trả lời trong', 'en' => 'Reply in'],
    
    // No results messages
    'no_threads_found' => ['vi' => 'Không tìm thấy chủ đề nào phù hợp với tiêu chí tìm kiếm của bạn.', 'en' => 'No threads found matching your search criteria.'],
    'no_posts_found' => ['vi' => 'Không tìm thấy bài viết nào phù hợp với tiêu chí tìm kiếm của bạn.', 'en' => 'No posts found matching your search criteria.'],
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

echo "📁 Processing search advanced results keys for search.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/search.php";
if (addKeysToFile($viFile, $searchAdvancedResultsKeys, 'vi')) {
    $totalAdded = count($searchAdvancedResultsKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/search.php";
addKeysToFile($enFile, $searchAdvancedResultsKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total search advanced results keys added: " . count($searchAdvancedResultsKeys) . "\n";

echo "\n✅ Search advanced results keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
