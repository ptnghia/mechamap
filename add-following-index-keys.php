<?php

/**
 * ADD FOLLOWING INDEX KEYS
 * Thêm tất cả keys thiếu cho following/index.blade.php
 */

echo "=== ADDING FOLLOWING INDEX KEYS ===\n\n";

// All following index keys
$followingIndexKeys = [
    'people_you_follow' => ['vi' => 'Người bạn theo dõi', 'en' => 'People You Follow'],
    'not_following_anyone' => ['vi' => 'Bạn chưa theo dõi ai.', 'en' => 'You are not following anyone yet.'],
    'follow_users_to_see_updates' => ['vi' => 'Theo dõi người dùng khác để xem cập nhật của họ trong bảng tin của bạn.', 'en' => 'Follow other users to see their updates in your feed.'],
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

echo "📁 Processing following index keys for following.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/following.php";
if (addKeysToFile($viFile, $followingIndexKeys, 'vi')) {
    $totalAdded = count($followingIndexKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/following.php";
addKeysToFile($enFile, $followingIndexKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total following index keys added: " . count($followingIndexKeys) . "\n";

echo "\n✅ Following index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
