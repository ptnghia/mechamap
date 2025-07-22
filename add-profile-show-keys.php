<?php

/**
 * ADD PROFILE SHOW KEYS
 * Thêm tất cả keys thiếu cho profile/show.blade.php
 */

echo "=== ADDING PROFILE SHOW KEYS ===\n\n";

// All profile show keys organized by category
$profileShowKeys = [
    // Profile keys
    'profile.last_seen' => ['vi' => 'Lần cuối truy cập:', 'en' => 'Last seen:'],
    'profile.replies' => ['vi' => 'Trả lời', 'en' => 'Replies'],
    'profile.threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
    'profile.reactions' => ['vi' => 'Phản ứng', 'en' => 'Reactions'],
    'profile.about' => ['vi' => 'Giới thiệu', 'en' => 'About'],
    'profile.no_information_provided' => ['vi' => 'Chưa có thông tin được cung cấp.', 'en' => 'No information provided.'],
    'profile.joined' => ['vi' => 'Tham gia:', 'en' => 'Joined:'],
    'profile.following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
    'profile.followers' => ['vi' => 'Người theo dõi', 'en' => 'Followers'],
    'profile.get_set_up_title' => ['vi' => 'Thiết lập trên MechaMap Forum!', 'en' => 'Get set up on MechaMap Forum!'],
    'profile.get_set_up_description' => ['vi' => 'Không chắc phải làm gì tiếp theo? Đây là một số ý tưởng để bạn làm quen với cộng đồng!', 'en' => 'Not sure what to do next? Here are some ideas to get you familiar with the community!'],
    'profile.verify_email' => ['vi' => 'Xác minh email của bạn', 'en' => 'Verify your email'],
    'profile.add_avatar' => ['vi' => 'Thêm ảnh đại diện', 'en' => 'Add an avatar'],
    'profile.add_information' => ['vi' => 'Thêm thông tin về bản thân', 'en' => 'Add information about yourself'],
    'profile.add_location' => ['vi' => 'Thêm vị trí của bạn', 'en' => 'Add your location'],
    'profile.create_post_reply' => ['vi' => 'Tạo bài viết hoặc trả lời chủ đề', 'en' => 'Create a post or reply to a thread'],
    'profile.profile_posts' => ['vi' => 'Bài viết trên trang cá nhân', 'en' => 'Profile Posts'],
    'profile.write_something_on' => ['vi' => 'Viết gì đó trên', 'en' => 'Write something on'],
    'profile.profile' => ['vi' => 'trang cá nhân', 'en' => 'profile'],
    'profile.post' => ['vi' => 'Đăng', 'en' => 'Post'],
    'profile.no_profile_posts' => ['vi' => 'Chưa có bài viết trên trang cá nhân.', 'en' => 'No profile posts yet.'],
    'profile.recent_activity' => ['vi' => 'Hoạt động gần đây', 'en' => 'Recent Activity'],
    'profile.created_new_thread' => ['vi' => 'Đã tạo chủ đề mới', 'en' => 'Created a new thread'],
    'profile.replied_to_thread' => ['vi' => 'Đã trả lời chủ đề', 'en' => 'Replied to a thread'],
    'profile.updated_profile_info' => ['vi' => 'Đã cập nhật thông tin cá nhân', 'en' => 'Updated profile information'],
    'profile.no_recent_activity' => ['vi' => 'Không có hoạt động gần đây.', 'en' => 'No recent activity.'],
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

echo "📁 Processing profile show keys for profile.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/profile.php";
if (addKeysToFile($viFile, $profileShowKeys, 'vi')) {
    $totalAdded = count($profileShowKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/profile.php";
addKeysToFile($enFile, $profileShowKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total profile show keys added: " . count($profileShowKeys) . "\n";

echo "\n✅ Profile show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
