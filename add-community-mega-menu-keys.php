<?php

/**
 * ADD COMMUNITY MEGA MENU KEYS
 * Thêm tất cả keys thiếu cho components/menu/community-mega-menu.blade.php
 */

echo "=== ADDING COMMUNITY MEGA MENU KEYS ===\n\n";

// All community mega menu keys organized by file
$communityMegaMenuKeys = [
    // Navigation keys
    'navigation_keys' => [
        'community.quick_access' => ['vi' => 'Truy cập nhanh', 'en' => 'Quick Access'],
        'community.forum_home_desc' => ['vi' => 'Trang chủ diễn đàn và thảo luận', 'en' => 'Forum home and discussions'],
        'community.popular_topics' => ['vi' => 'Chủ đề phổ biến', 'en' => 'Popular Topics'],
        'community.popular_discussions_desc' => ['vi' => 'Các thảo luận được quan tâm nhất', 'en' => 'Most engaging discussions'],
        'community.browse_categories' => ['vi' => 'Duyệt danh mục', 'en' => 'Browse Categories'],
        'community.explore_topics_desc' => ['vi' => 'Khám phá các chủ đề theo danh mục', 'en' => 'Explore topics by category'],
        'community.discover' => ['vi' => 'Khám phá', 'en' => 'Discover'],
        'community.recent_discussions' => ['vi' => 'Thảo luận gần đây', 'en' => 'Recent Discussions'],
        'community.recent_discussions_desc' => ['vi' => 'Các cuộc thảo luận mới nhất', 'en' => 'Latest community discussions'],
        'community.trending' => ['vi' => 'Xu hướng', 'en' => 'Trending'],
        'community.trending_desc' => ['vi' => 'Nội dung đang thịnh hành', 'en' => 'Currently trending content'],
        'community.most_viewed' => ['vi' => 'Xem nhiều nhất', 'en' => 'Most Viewed'],
        'community.most_viewed_desc' => ['vi' => 'Nội dung được xem nhiều nhất', 'en' => 'Most viewed content'],
        'community.hot_topics' => ['vi' => 'Chủ đề hot', 'en' => 'Hot Topics'],
        'community.hot_topics_desc' => ['vi' => 'Chủ đề đang được thảo luận sôi nổi', 'en' => 'Hotly debated topics'],
        'community.tools_connect' => ['vi' => 'Công cụ & Kết nối', 'en' => 'Tools & Connect'],
        'community.member_directory' => ['vi' => 'Thư mục thành viên', 'en' => 'Member Directory'],
        'community.member_directory_desc' => ['vi' => 'Tìm và kết nối với thành viên', 'en' => 'Find and connect with members'],
        'community.events_webinars' => ['vi' => 'Sự kiện & Webinar', 'en' => 'Events & Webinars'],
        'community.events_webinars_desc' => ['vi' => 'Tham gia các sự kiện cộng đồng', 'en' => 'Join community events'],
        'community.job_board' => ['vi' => 'Bảng việc làm', 'en' => 'Job Board'],
        'community.job_board_desc' => ['vi' => 'Tìm kiếm cơ hội nghề nghiệp', 'en' => 'Find career opportunities'],
    ],
    
    // Search keys
    'search_keys' => [
        'actions.advanced' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
        'actions.advanced_desc' => ['vi' => 'Tìm kiếm chi tiết với nhiều tiêu chí', 'en' => 'Detailed search with multiple criteria'],
    ],
    
    // Common keys
    'common_keys' => [
        'status.coming_soon' => ['vi' => 'Sắp ra mắt', 'en' => 'Coming Soon'],
    ],
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

// Map categories to files
$categoryFileMap = [
    'navigation_keys' => 'navigation',
    'search_keys' => 'search',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($communityMegaMenuKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    $file = $categoryFileMap[$category];
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$file.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$file.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total community mega menu keys added: $totalAdded\n";
echo "Categories processed: " . count($communityMegaMenuKeys) . "\n";

echo "\n✅ Community mega menu keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
