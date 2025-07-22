<?php

/**
 * ADD PROFILE ABOUT SECTION KEYS
 * Thêm tất cả keys thiếu cho profile/partials/about-section.blade.php
 */

echo "=== ADDING PROFILE ABOUT SECTION KEYS ===\n\n";

// All profile about section keys
$profileAboutSectionKeys = [
    'about' => ['vi' => 'Giới thiệu', 'en' => 'About'],
    'see_all' => ['vi' => 'Xem tất cả', 'en' => 'See All'],
    'about_me' => ['vi' => 'Về tôi', 'en' => 'About Me'],
    'edit_in_account_settings' => ['vi' => 'Chỉnh sửa trong cài đặt tài khoản', 'en' => 'Edit in account settings'],
    'website' => ['vi' => 'Website', 'en' => 'Website'],
    'location' => ['vi' => 'Vị trí', 'en' => 'Location'],
    'signature' => ['vi' => 'Chữ ký', 'en' => 'Signature'],
    'following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
    'members' => ['vi' => 'thành viên', 'en' => 'members'],
    'follow_others_message' => ['vi' => 'Theo dõi người khác để cập nhật những gì họ đăng', 'en' => 'Follow others to stay up to date on what they post'],
    'followers' => ['vi' => 'Người theo dõi', 'en' => 'Followers'],
    'no_followers_yet' => ['vi' => 'Chưa có người theo dõi nào', 'en' => 'No followers yet'],
    'joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
    'last_seen' => ['vi' => 'Lần cuối truy cập', 'en' => 'Last Seen'],
    'never' => ['vi' => 'Chưa bao giờ', 'en' => 'Never'],
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

echo "📁 Processing profile about section keys for profile.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/profile.php";
if (addKeysToFile($viFile, $profileAboutSectionKeys, 'vi')) {
    $totalAdded = count($profileAboutSectionKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/profile.php";
addKeysToFile($enFile, $profileAboutSectionKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total profile about section keys added: " . count($profileAboutSectionKeys) . "\n";

echo "\n✅ Profile about section keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
