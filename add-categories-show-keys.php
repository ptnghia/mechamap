<?php

/**
 * ADD CATEGORIES SHOW KEYS
 * Thêm tất cả keys thiếu cho categories/show.blade.php
 */

echo "=== ADDING CATEGORIES SHOW KEYS ===\n\n";

// All categories show keys organized by file
$categoriesShowKeys = [
    // Navigation keys
    'navigation_keys' => [
        'main.home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
        'main.forums' => ['vi' => 'Diễn đàn', 'en' => 'Forums'],
    ],
    
    // Forum keys
    'forum_keys' => [
        'threads.threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'forums.no_forums_in_category' => ['vi' => 'Không có diễn đàn nào trong danh mục này', 'en' => 'No forums in this category'],
        'forums.no_forums_description' => ['vi' => 'Hãy là người đầu tiên tạo diễn đàn trong danh mục này', 'en' => 'Be the first to create a forum in this category'],
        'forums.forums_in_category' => ['vi' => 'Diễn đàn trong :category', 'en' => 'Forums in :category'],
        'forums.high_activity' => ['vi' => 'Hoạt động cao', 'en' => 'High Activity'],
        'forums.medium_activity' => ['vi' => 'Hoạt động trung bình', 'en' => 'Medium Activity'],
        'forums.low_activity' => ['vi' => 'Hoạt động thấp', 'en' => 'Low Activity'],
        'threads.no_posts_in_category' => ['vi' => 'Chưa có bài viết nào trong danh mục này', 'en' => 'No posts in this category yet'],
        'threads.create_first_post' => ['vi' => 'Tạo bài viết đầu tiên', 'en' => 'Create First Post'],
    ],
    
    // Common keys
    'common_keys' => [
        'new_threads' => ['vi' => 'Chủ đề mới trong :category', 'en' => 'New Threads in :category'],
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
    'forum_keys' => 'forum',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($categoriesShowKeys as $category => $keys) {
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
echo "Total categories show keys added: $totalAdded\n";
echo "Categories processed: " . count($categoriesShowKeys) . "\n";

echo "\n✅ Categories show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
