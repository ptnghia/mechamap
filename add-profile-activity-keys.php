<?php

/**
 * ADD PROFILE ACTIVITY KEYS
 * Thêm tất cả keys thiếu cho profile/partials/activity-section.blade.php
 */

echo "=== ADDING PROFILE ACTIVITY KEYS ===\n\n";

// All profile activity keys
$profileActivityKeys = [
    'activity.title' => ['vi' => 'Hoạt động', 'en' => 'Activity'],
    'activity.see_all' => ['vi' => 'Xem tất cả', 'en' => 'See All'],
    'activity.created_thread' => ['vi' => 'Đã tạo chủ đề:', 'en' => 'Created thread:'],
    'activity.created_new_thread' => ['vi' => 'Đã tạo chủ đề mới', 'en' => 'Created a new thread'],
    'activity.commented_on' => ['vi' => 'Đã bình luận về:', 'en' => 'Commented on:'],
    'activity.commented_on_thread' => ['vi' => 'Đã bình luận về một chủ đề', 'en' => 'Commented on a thread'],
    'activity.liked_thread' => ['vi' => 'Đã thích chủ đề:', 'en' => 'Liked thread:'],
    'activity.liked_a_thread' => ['vi' => 'Đã thích một chủ đề', 'en' => 'Liked a thread'],
    'activity.saved_thread' => ['vi' => 'Đã lưu chủ đề:', 'en' => 'Saved thread:'],
    'activity.saved_a_thread' => ['vi' => 'Đã lưu một chủ đề', 'en' => 'Saved a thread'],
    'activity.updated_profile' => ['vi' => 'Đã cập nhật thông tin hồ sơ', 'en' => 'Updated profile information'],
    'activity.news_feed_empty' => ['vi' => 'Bảng tin hiện tại đang trống.', 'en' => 'The news feed is currently empty.'],
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

echo "📁 Processing profile activity keys for profile.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/profile.php";
if (addKeysToFile($viFile, $profileActivityKeys, 'vi')) {
    $totalAdded = count($profileActivityKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/profile.php";
addKeysToFile($enFile, $profileActivityKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total profile activity keys added: " . count($profileActivityKeys) . "\n";

echo "\n✅ Profile activity keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
