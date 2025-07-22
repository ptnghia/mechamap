<?php

/**
 * ADD FOLLOWING FOLLOWERS KEYS
 * Thêm tất cả keys thiếu cho following/followers.blade.php
 */

echo "=== ADDING FOLLOWING FOLLOWERS KEYS ===\n\n";

// All following followers keys
$followingFollowersKeys = [
    'people_following_you' => ['vi' => 'Người theo dõi bạn', 'en' => 'People Following You'],
    'unfollow' => ['vi' => 'Bỏ theo dõi', 'en' => 'Unfollow'],
    'follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
    'no_followers_yet' => ['vi' => 'Bạn chưa có người theo dõi nào.', 'en' => 'You don\'t have any followers yet.'],
    'followers_will_appear_here' => ['vi' => 'Khi có ai đó theo dõi bạn, họ sẽ xuất hiện ở đây.', 'en' => 'When someone follows you, they will appear here.'],
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

echo "📁 Processing following followers keys for following.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/following.php";
if (addKeysToFile($viFile, $followingFollowersKeys, 'vi')) {
    $totalAdded = count($followingFollowersKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/following.php";
addKeysToFile($enFile, $followingFollowersKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total following followers keys added: " . count($followingFollowersKeys) . "\n";

echo "\n✅ Following followers keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
