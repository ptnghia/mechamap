<?php

/**
 * ADD NEW CONTENT INDEX KEYS
 * Thêm tất cả keys thiếu cho new-content/index.blade.php
 */

echo "=== ADDING NEW CONTENT INDEX KEYS ===\n\n";

// All new content index keys organized by file
$newContentIndexKeys = [
    // Forum keys
    'forum_keys' => [
        'threads.newest' => ['vi' => 'Chủ đề mới nhất', 'en' => 'Newest Threads'],
        'posts.newest_replies' => ['vi' => 'Trả lời mới nhất', 'en' => 'Newest Replies'],
        'posts.reply_to' => ['vi' => 'Trả lời cho', 'en' => 'Reply to'],
        'threads.unknown_thread' => ['vi' => 'Chủ đề không xác định', 'en' => 'Unknown Thread'],
        'forums.unknown_forum' => ['vi' => 'Diễn đàn không xác định', 'en' => 'Unknown Forum'],
        'threads.no_threads_found' => ['vi' => 'Không tìm thấy chủ đề nào.', 'en' => 'No threads found.'],
        'posts.no_posts_found' => ['vi' => 'Không tìm thấy bài viết nào.', 'en' => 'No posts found.'],
    ],
    
    // Common keys
    'common_keys' => [
        'view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
        'by' => ['vi' => 'Bởi', 'en' => 'By'],
        'in' => ['vi' => 'trong', 'en' => 'in'],
        'statistics' => ['vi' => 'Thống kê', 'en' => 'Statistics'],
        'threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'posts' => ['vi' => 'Bài viết', 'en' => 'Posts'],
        'members' => ['vi' => 'Thành viên', 'en' => 'Members'],
        'newest_member' => ['vi' => 'Thành viên mới nhất', 'en' => 'Newest Member'],
        'none' => ['vi' => 'Không có', 'en' => 'None'],
        'online_now' => ['vi' => 'Đang trực tuyến', 'en' => 'Online Now'],
        'total_online' => ['vi' => 'Tổng số trực tuyến', 'en' => 'Total online'],
        'no_users_online' => ['vi' => 'Không có người dùng trực tuyến.', 'en' => 'No users online.'],
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
    'forum_keys' => 'forum',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($newContentIndexKeys as $category => $keys) {
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
echo "Total new content index keys added: $totalAdded\n";
echo "Categories processed: " . count($newContentIndexKeys) . "\n";

echo "\n✅ New content index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
