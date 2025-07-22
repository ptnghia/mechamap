<?php

/**
 * ADD SEARCH BASIC KEYS
 * Thêm tất cả keys thiếu cho search/basic.blade.php
 */

echo "=== ADDING SEARCH BASIC KEYS ===\n\n";

// All search basic keys
$searchBasicKeys = [
    // Main search
    'basic_search' => ['vi' => 'Tìm kiếm cơ bản', 'en' => 'Basic Search'],
    'search_results' => ['vi' => 'Kết quả tìm kiếm', 'en' => 'Search Results'],
    'enter_search_terms' => ['vi' => 'Nhập từ khóa tìm kiếm...', 'en' => 'Enter search terms...'],
    'search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'search_description' => ['vi' => 'Tìm kiếm chủ đề, bài viết, người dùng và nhiều nội dung khác', 'en' => 'Search for threads, posts, users and more content'],
    
    // Search types
    'all_content' => ['vi' => 'Tất cả nội dung', 'en' => 'All Content'],
    'threads_only' => ['vi' => 'Chỉ chủ đề', 'en' => 'Threads Only'],
    'posts_only' => ['vi' => 'Chỉ bài viết', 'en' => 'Posts Only'],
    'users_only' => ['vi' => 'Chỉ người dùng', 'en' => 'Users Only'],
    'products_only' => ['vi' => 'Chỉ sản phẩm', 'en' => 'Products Only'],
    'showcases_only' => ['vi' => 'Chỉ showcase', 'en' => 'Showcases Only'],
    'documentation_only' => ['vi' => 'Chỉ tài liệu', 'en' => 'Documentation Only'],
    'materials_only' => ['vi' => 'Chỉ vật liệu', 'en' => 'Materials Only'],
    'cad_files_only' => ['vi' => 'Chỉ tệp CAD', 'en' => 'CAD Files Only'],
    
    // Results info
    'found_results' => ['vi' => 'Tìm thấy :count kết quả cho ":query"', 'en' => 'Found :count results for ":query"'],
    
    // Tab labels
    'threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
    'posts' => ['vi' => 'Bài viết', 'en' => 'Posts'],
    'users' => ['vi' => 'Người dùng', 'en' => 'Users'],
    'products' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
    'showcases' => ['vi' => 'Showcase', 'en' => 'Showcases'],
    'documentation' => ['vi' => 'Tài liệu', 'en' => 'Documentation'],
    'materials' => ['vi' => 'Vật liệu', 'en' => 'Materials'],
    'cad_files' => ['vi' => 'Tệp CAD', 'en' => 'CAD Files'],
    
    // No results messages
    'no_threads_found' => ['vi' => 'Không tìm thấy chủ đề nào', 'en' => 'No threads found'],
    'no_posts_found' => ['vi' => 'Không tìm thấy bài viết nào', 'en' => 'No posts found'],
    'no_users_found' => ['vi' => 'Không tìm thấy người dùng nào', 'en' => 'No users found'],
    'no_products_found' => ['vi' => 'Không tìm thấy sản phẩm nào', 'en' => 'No products found'],
    'no_showcases_found' => ['vi' => 'Không tìm thấy showcase nào', 'en' => 'No showcases found'],
    'no_documentation_found' => ['vi' => 'Không tìm thấy tài liệu nào', 'en' => 'No documentation found'],
    'no_materials_found' => ['vi' => 'Không tìm thấy vật liệu nào', 'en' => 'No materials found'],
    'no_cad_files_found' => ['vi' => 'Không tìm thấy tệp CAD nào', 'en' => 'No CAD files found'],
    'try_different_keywords' => ['vi' => 'Thử sử dụng từ khóa khác hoặc kiểm tra chính tả', 'en' => 'Try different keywords or check spelling'],
    
    // Actions
    'view_post' => ['vi' => 'Xem bài viết', 'en' => 'View Post'],
    
    // Additional search functionality
    'search_tips' => ['vi' => 'Mẹo tìm kiếm', 'en' => 'Search Tips'],
    'recent_searches' => ['vi' => 'Tìm kiếm gần đây', 'en' => 'Recent Searches'],
    'popular_searches' => ['vi' => 'Tìm kiếm phổ biến', 'en' => 'Popular Searches'],
    'clear_search' => ['vi' => 'Xóa tìm kiếm', 'en' => 'Clear Search'],
    'search_suggestions' => ['vi' => 'Gợi ý tìm kiếm', 'en' => 'Search Suggestions'],
    'no_results' => ['vi' => 'Không có kết quả', 'en' => 'No Results'],
    'search_placeholder' => ['vi' => 'Tìm kiếm...', 'en' => 'Search...'],
    'advanced_search_link' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'filter_results' => ['vi' => 'Lọc kết quả', 'en' => 'Filter Results'],
    'sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort By'],
    'relevance' => ['vi' => 'Độ liên quan', 'en' => 'Relevance'],
    'date' => ['vi' => 'Ngày', 'en' => 'Date'],
    'popularity' => ['vi' => 'Độ phổ biến', 'en' => 'Popularity'],
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

echo "📁 Processing search basic keys for search.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/search.php";
if (addKeysToFile($viFile, $searchBasicKeys, 'vi')) {
    $totalAdded = count($searchBasicKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/search.php";
addKeysToFile($enFile, $searchBasicKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total search basic keys added: " . count($searchBasicKeys) . "\n";

echo "\n✅ Search basic keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
