<?php

/**
 * ADD FORUM SEARCH KEYS
 * Thêm tất cả keys thiếu cho forums/search.blade.php
 */

echo "=== ADDING FORUM SEARCH KEYS ===\n\n";

// All forum search keys organized by category
$forumSearchKeys = [
    // Forum search results keys
    'search.results' => ['vi' => 'Kết quả tìm kiếm', 'en' => 'Search Results'],
    'search.search_placeholder' => ['vi' => 'Tìm kiếm chủ đề, bài viết...', 'en' => 'Search threads, posts...'],
    'search.thread_results' => ['vi' => 'Kết quả chủ đề', 'en' => 'Thread Results'],
    'search.post_results' => ['vi' => 'Kết quả bài viết', 'en' => 'Post Results'],
    'search.no_results_message' => ['vi' => 'Không tìm thấy kết quả nào cho', 'en' => 'No results found for'],
    'search.suggestions' => ['vi' => 'Gợi ý', 'en' => 'Suggestions'],
    'search.check_spelling' => ['vi' => 'Kiểm tra chính tả của từ khóa', 'en' => 'Check spelling of keywords'],
    'search.use_general_keywords' => ['vi' => 'Sử dụng từ khóa tổng quát hơn', 'en' => 'Use more general keywords'],
    'search.try_different_keywords' => ['vi' => 'Thử từ khóa khác', 'en' => 'Try different keywords'],
    'search.browse_forum_categories' => ['vi' => 'Duyệt danh mục diễn đàn', 'en' => 'Browse forum categories'],
    'search.search_tips' => ['vi' => 'Mẹo tìm kiếm', 'en' => 'Search Tips'],
    'search.tip_quotes' => ['vi' => 'Sử dụng dấu ngoặc kép để tìm cụm từ chính xác', 'en' => 'Use quotes to search for exact phrases'],
    'search.tip_multiple_words' => ['vi' => 'Sử dụng nhiều từ khóa để thu hẹp kết quả', 'en' => 'Use multiple keywords to narrow results'],
    'search.tip_minimum_chars' => ['vi' => 'Từ khóa phải có ít nhất 3 ký tự', 'en' => 'Keywords must be at least 3 characters'],
    'search.tip_browse_categories' => ['vi' => 'Duyệt theo danh mục để tìm nội dung liên quan', 'en' => 'Browse by category to find related content'],
    'search.popular_categories' => ['vi' => 'Danh mục phổ biến', 'en' => 'Popular Categories'],
    
    // Forum stats keys
    'stats.threads' => ['vi' => 'chủ đề', 'en' => 'threads'],
    'stats.posts' => ['vi' => 'bài viết', 'en' => 'posts'],
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

echo "📁 Processing forum search keys for forum.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/forum.php";
if (addKeysToFile($viFile, $forumSearchKeys, 'vi')) {
    $totalAdded = count($forumSearchKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/forum.php";
addKeysToFile($enFile, $forumSearchKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total forum search keys added: " . count($forumSearchKeys) . "\n";

echo "\n✅ Forum search keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
