<?php

/**
 * ADD FOLLOWING THREADS KEYS
 * Thêm tất cả keys thiếu cho following/threads.blade.php
 */

echo "=== ADDING FOLLOWING THREADS KEYS ===\n\n";

// All following threads keys organized by file
$followingThreadsKeys = [
    // Following keys
    'following_keys' => [
        'following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
        'followers' => ['vi' => 'Người theo dõi', 'en' => 'Followers'],
        'followed_threads' => ['vi' => 'Chủ đề đã theo dõi', 'en' => 'Followed Threads'],
        'participated_discussions' => ['vi' => 'Thảo luận đã tham gia', 'en' => 'Participated Discussions'],
        'filters' => ['vi' => 'Bộ lọc', 'en' => 'Filters'],
        'all_forums' => ['vi' => 'Tất cả diễn đàn', 'en' => 'All Forums'],
        'unfollow' => ['vi' => 'Bỏ theo dõi', 'en' => 'Unfollow'],
        'not_watching_threads' => ['vi' => 'Bạn không theo dõi chủ đề nào.', 'en' => 'You are not watching any threads.'],
        'follow_threads_to_see' => ['vi' => 'Theo dõi chủ đề để xem chúng ở đây.', 'en' => 'Follow threads to see them here.'],
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
    'following_keys' => 'following',
];

$totalAdded = 0;

foreach ($followingThreadsKeys as $category => $keys) {
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
echo "Total following threads keys added: $totalAdded\n";
echo "Categories processed: " . count($followingThreadsKeys) . "\n";

echo "\n✅ Following threads keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
