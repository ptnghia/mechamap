<?php

/**
 * ADD 100% MISSING KEYS
 * Thêm tất cả keys từ 5 files có 100% keys thiếu
 */

echo "=== ADDING 100% MISSING KEYS ===\n\n";

// All missing keys organized by category
$translationKeys = [
    // Conversations keys (13 keys)
    'conversations' => [
        'conversation' => ['vi' => 'Cuộc trò chuyện', 'en' => 'Conversation'],
        'invite_participants' => ['vi' => 'Mời người tham gia', 'en' => 'Invite participants'],
        'mute_conversation' => ['vi' => 'Tắt tiếng cuộc trò chuyện', 'en' => 'Mute conversation'],
        'report' => ['vi' => 'Báo cáo', 'en' => 'Report'],
        'leave_conversation' => ['vi' => 'Rời khỏi cuộc trò chuyện', 'en' => 'Leave conversation'],
        'messages' => ['vi' => 'Tin nhắn', 'en' => 'Messages'],
        'messages_count' => ['vi' => 'tin nhắn', 'en' => 'messages'],
        'today' => ['vi' => 'Hôm nay', 'en' => 'Today'],
        'yesterday' => ['vi' => 'Hôm qua', 'en' => 'Yesterday'],
        'no_messages_yet' => ['vi' => 'Chưa có tin nhắn nào.', 'en' => 'No messages yet.'],
        'send_message_to_start' => ['vi' => 'Gửi tin nhắn để bắt đầu cuộc trò chuyện.', 'en' => 'Send a message to start the conversation.'],
        'type_your_message' => ['vi' => 'Nhập tin nhắn của bạn...', 'en' => 'Type your message...'],
        'send' => ['vi' => 'Gửi', 'en' => 'Send'],
    ],
    
    // Profile Activity keys (12 keys)
    'profile' => [
        'activity' => ['vi' => 'Hoạt động', 'en' => 'Activity'],
        'see_all' => ['vi' => 'Xem tất cả', 'en' => 'See All'],
        'created_thread' => ['vi' => 'Đã tạo chủ đề:', 'en' => 'Created thread:'],
        'created_a_new_thread' => ['vi' => 'Đã tạo chủ đề mới', 'en' => 'Created a new thread'],
        'commented_on' => ['vi' => 'Đã bình luận về:', 'en' => 'Commented on:'],
        'commented_on_a_thread' => ['vi' => 'Đã bình luận về một chủ đề', 'en' => 'Commented on a thread'],
        'liked_thread' => ['vi' => 'Đã thích chủ đề:', 'en' => 'Liked thread:'],
        'liked_a_thread' => ['vi' => 'Đã thích một chủ đề', 'en' => 'Liked a thread'],
        'saved_thread' => ['vi' => 'Đã lưu chủ đề:', 'en' => 'Saved thread:'],
        'saved_a_thread' => ['vi' => 'Đã lưu một chủ đề', 'en' => 'Saved a thread'],
        'updated_profile_information' => ['vi' => 'Đã cập nhật thông tin hồ sơ', 'en' => 'Updated profile information'],
        'news_feed_empty' => ['vi' => 'Nguồn cấp tin tức hiện đang trống.', 'en' => 'The news feed is currently empty.'],
    ],
    
    // Following System keys (25 keys total from 3 files)
    'following' => [
        'following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
        'followers' => ['vi' => 'Người theo dõi', 'en' => 'Followers'],
        'followed_threads' => ['vi' => 'Chủ đề đã theo dõi', 'en' => 'Followed Threads'],
        'participated_discussions' => ['vi' => 'Thảo luận đã tham gia', 'en' => 'Participated Discussions'],
        'filters' => ['vi' => 'Bộ lọc', 'en' => 'Filters'],
        'all_forums' => ['vi' => 'Tất cả diễn đàn', 'en' => 'All Forums'],
        'unfollow' => ['vi' => 'Bỏ theo dõi', 'en' => 'Unfollow'],
        'follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
        'not_watching_any_threads' => ['vi' => 'Bạn chưa theo dõi chủ đề nào.', 'en' => 'You are not watching any threads.'],
        'follow_threads_to_see_here' => ['vi' => 'Theo dõi chủ đề để xem chúng ở đây.', 'en' => 'Follow threads to see them here.'],
        'people_following_you' => ['vi' => 'Những người đang theo dõi bạn', 'en' => 'People Following You'],
        'no_followers_yet' => ['vi' => 'Bạn chưa có người theo dõi nào.', 'en' => 'You don\'t have any followers yet.'],
        'when_someone_follows_you' => ['vi' => 'Khi ai đó theo dõi bạn, họ sẽ xuất hiện ở đây.', 'en' => 'When someone follows you, they will appear here.'],
        'people_you_follow' => ['vi' => 'Những người bạn theo dõi', 'en' => 'People You Follow'],
        'not_following_anyone_yet' => ['vi' => 'Bạn chưa theo dõi ai.', 'en' => 'You are not following anyone yet.'],
        'follow_other_users_to_see_updates' => ['vi' => 'Theo dõi người dùng khác để xem cập nhật của họ trong nguồn cấp dữ liệu của bạn.', 'en' => 'Follow other users to see their updates in your feed.'],
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
    
    // Find the last closing bracket
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        echo "❌ Could not find closing bracket in $filePath\n";
        return false;
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

$totalAdded = 0;

foreach ($translationKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$category.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$category.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total keys added: $totalAdded\n";
echo "Categories processed: " . count($translationKeys) . "\n";
echo "Files processed: conversations, profile, following\n";

echo "\n✅ 100% missing keys addition completed at " . date('Y-m-d H:i:s') . "\n";
echo "\nNext: Run scan to verify all keys are now available.\n";
?>
