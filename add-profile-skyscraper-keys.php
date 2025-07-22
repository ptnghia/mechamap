<?php

/**
 * ADD PROFILE SKYSCRAPER KEYS
 * Thêm tất cả keys thiếu cho profile/show-skyscraper.blade.php
 */

echo "=== ADDING PROFILE SKYSCRAPER KEYS ===\n\n";

// All profile skyscraper keys organized by category
$profileSkyscraperKeys = [
    // Profile skyscraper keys
    'registered' => ['vi' => 'Đã đăng ký', 'en' => 'Registered'],
    'joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
    'last_seen' => ['vi' => 'Lần cuối truy cập', 'en' => 'Last seen'],
    'never' => ['vi' => 'Chưa bao giờ', 'en' => 'Never'],
    'viewing_member_profile' => ['vi' => 'Đang xem hồ sơ thành viên', 'en' => 'Viewing member profile'],
    'report' => ['vi' => 'Báo cáo', 'en' => 'Report'],
    'replies' => ['vi' => 'Trả lời', 'en' => 'Replies'],
    'discussions_created' => ['vi' => 'Thảo luận đã tạo', 'en' => 'Discussions Created'],
    'reaction_score' => ['vi' => 'Điểm phản ứng', 'en' => 'Reaction score'],
    'points' => ['vi' => 'Điểm', 'en' => 'Points'],
    'get_set_up_title' => ['vi' => 'Thiết lập trên MechaMap Forum!', 'en' => 'Get set up on MechaMap Forum!'],
    'get_set_up_description' => ['vi' => 'Không chắc phải làm gì tiếp theo? Đây là một số ý tưởng để bạn làm quen với cộng đồng!', 'en' => 'Not sure what to do next? Here are some ideas to get you familiar with the community!'],
    'verify_email' => ['vi' => 'Xác minh email của bạn', 'en' => 'Verify your email'],
    'add_avatar' => ['vi' => 'Thêm ảnh đại diện', 'en' => 'Add an avatar'],
    'like_post' => ['vi' => 'Thích một bài viết', 'en' => 'Like a post'],
    'overview' => ['vi' => 'Tổng quan', 'en' => 'Overview'],
    'about' => ['vi' => 'Giới thiệu', 'en' => 'About'],
    'profile_posts' => ['vi' => 'Bài viết trên trang cá nhân', 'en' => 'Profile posts'],
    'activity' => ['vi' => 'Hoạt động', 'en' => 'Activity'],
    'gallery' => ['vi' => 'Thư viện', 'en' => 'Gallery'],
    'no_media_to_display' => ['vi' => 'Không có phương tiện nào để hiển thị.', 'en' => 'No media to display.'],
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

echo "📁 Processing profile skyscraper keys for profile.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/profile.php";
if (addKeysToFile($viFile, $profileSkyscraperKeys, 'vi')) {
    $totalAdded = count($profileSkyscraperKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/profile.php";
addKeysToFile($enFile, $profileSkyscraperKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total profile skyscraper keys added: " . count($profileSkyscraperKeys) . "\n";

echo "\n✅ Profile skyscraper keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
