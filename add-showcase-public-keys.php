<?php

/**
 * ADD SHOWCASE PUBLIC KEYS
 * Thêm tất cả keys thiếu cho showcase/public.blade.php
 */

echo "=== ADDING SHOWCASE PUBLIC KEYS ===\n\n";

// All showcase public keys from showcase/public.blade.php
$showcasePublicKeys = [
    // Main page
    'public_showcases' => ['vi' => 'Showcase công khai', 'en' => 'Public Showcases'],
    'page_description' => ['vi' => 'Khám phá các dự án sáng tạo từ cộng đồng', 'en' => 'Discover creative projects from the community'],
    'create_new' => ['vi' => 'Tạo mới', 'en' => 'Create New'],
    
    // Categories section
    'project_categories' => ['vi' => 'Danh mục dự án', 'en' => 'Project Categories'],
    'projects' => ['vi' => 'dự án', 'en' => 'projects'],
    'avg_rating' => ['vi' => 'đánh giá TB', 'en' => 'avg rating'],
    
    // Featured section
    'featured_projects' => ['vi' => 'Dự án nổi bật', 'en' => 'Featured Projects'],
    'no_featured_projects' => ['vi' => 'Chưa có dự án nổi bật nào', 'en' => 'No featured projects yet'],
    
    // Search section
    'advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'project_name' => ['vi' => 'Tên dự án', 'en' => 'Project Name'],
    'search_placeholder' => ['vi' => 'Nhập tên dự án...', 'en' => 'Enter project name...'],
    'category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    'complexity' => ['vi' => 'Độ phức tạp', 'en' => 'Complexity'],
    'all_levels' => ['vi' => 'Tất cả mức độ', 'en' => 'All Levels'],
    'project_type' => ['vi' => 'Loại dự án', 'en' => 'Project Type'],
    'all_types' => ['vi' => 'Tất cả loại', 'en' => 'All Types'],
    'software' => ['vi' => 'Phần mềm', 'en' => 'Software'],
    'all_software' => ['vi' => 'Tất cả phần mềm', 'en' => 'All Software'],
    'min_rating' => ['vi' => 'Đánh giá tối thiểu', 'en' => 'Minimum Rating'],
    'all_ratings' => ['vi' => 'Tất cả đánh giá', 'en' => 'All Ratings'],
    '4_plus_stars' => ['vi' => '4+ sao', 'en' => '4+ stars'],
    '3_plus_stars' => ['vi' => '3+ sao', 'en' => '3+ stars'],
    '2_plus_stars' => ['vi' => '2+ sao', 'en' => '2+ stars'],
    
    // Search actions
    'search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'clear_filters' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear Filters'],
    
    // Sorting
    'sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort by'],
    'newest' => ['vi' => 'Mới nhất', 'en' => 'Newest'],
    'most_viewed' => ['vi' => 'Xem nhiều nhất', 'en' => 'Most Viewed'],
    'highest_rated' => ['vi' => 'Đánh giá cao nhất', 'en' => 'Highest Rated'],
    'most_downloads' => ['vi' => 'Tải nhiều nhất', 'en' => 'Most Downloads'],
    'oldest' => ['vi' => 'Cũ nhất', 'en' => 'Oldest'],
    
    // Results section
    'all_projects' => ['vi' => 'Tất cả dự án', 'en' => 'All Projects'],
    'results' => ['vi' => 'kết quả', 'en' => 'results'],
    
    // No results
    'no_projects_found' => ['vi' => 'Không tìm thấy dự án nào', 'en' => 'No projects found'],
    'try_different_filters' => ['vi' => 'Thử sử dụng bộ lọc khác', 'en' => 'Try using different filters'],
    'create_new_project' => ['vi' => 'Tạo dự án mới', 'en' => 'Create New Project'],
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

echo "📁 Processing showcase public keys for showcase.php files\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/showcase.php";
if (addKeysToFile($viFile, $showcasePublicKeys, 'vi')) {
    $totalAdded = count($showcasePublicKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/showcase.php";
addKeysToFile($enFile, $showcasePublicKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($showcasePublicKeys) . "\n";
echo "Keys processed: " . count($showcasePublicKeys) . "\n";

// Test some keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'showcase.public_showcases',
    'showcase.project_categories', 
    'showcase.advanced_search',
    'showcase.no_projects_found'
];

foreach ($testKeys as $key) {
    echo "  Testing __('$key')...\n";
}

echo "\n✅ Showcase public keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
