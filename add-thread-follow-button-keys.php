<?php

/**
 * ADD THREAD FOLLOW BUTTON KEYS
 * Thêm tất cả keys thiếu cho components/thread-follow-button.blade.php
 */

echo "=== ADDING THREAD FOLLOW BUTTON KEYS ===\n\n";

// All thread follow button keys organized by file
$threadFollowButtonKeys = [
    // Forum actions keys
    'forum_keys' => [
        'actions.following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
        'actions.unfollow_thread' => ['vi' => 'Bỏ theo dõi chủ đề', 'en' => 'Unfollow thread'],
        'actions.follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
        'actions.follow_thread' => ['vi' => 'Theo dõi chủ đề', 'en' => 'Follow thread'],
        'actions.login_to_follow' => ['vi' => 'Đăng nhập để theo dõi', 'en' => 'Login to follow'],
        'actions.error_occurred' => ['vi' => 'Đã xảy ra lỗi', 'en' => 'An error occurred'],
        'actions.request_error' => ['vi' => 'Lỗi yêu cầu', 'en' => 'Request error'],
    ],
    
    // Thread keys
    'thread_keys' => [
        'following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
        'follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
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
    'thread_keys' => 'thread',
];

$totalAdded = 0;

foreach ($threadFollowButtonKeys as $category => $keys) {
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
echo "Total thread follow button keys added: $totalAdded\n";
echo "Categories processed: " . count($threadFollowButtonKeys) . "\n";

echo "\n✅ Thread follow button keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
