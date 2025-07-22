<?php

/**
 * ADD CONVERSATIONS SHOW KEYS
 * Thêm tất cả keys thiếu cho conversations/show.blade.php
 */

echo "=== ADDING CONVERSATIONS SHOW KEYS ===\n\n";

// All conversations show keys organized by file
$conversationsShowKeys = [
    // Conversations keys
    'conversations_keys' => [
        'conversation' => ['vi' => 'Cuộc trò chuyện', 'en' => 'Conversation'],
        'invite_participants' => ['vi' => 'Mời người tham gia', 'en' => 'Invite participants'],
        'mute_conversation' => ['vi' => 'Tắt tiếng cuộc trò chuyện', 'en' => 'Mute conversation'],
        'report' => ['vi' => 'Báo cáo', 'en' => 'Report'],
        'leave_conversation' => ['vi' => 'Rời khỏi cuộc trò chuyện', 'en' => 'Leave conversation'],
        'messages' => ['vi' => 'Tin nhắn', 'en' => 'Messages'],
        'messages_count' => ['vi' => 'tin nhắn', 'en' => 'messages'],
        'no_messages_yet' => ['vi' => 'Chưa có tin nhắn nào.', 'en' => 'No messages yet.'],
        'send_message_to_start' => ['vi' => 'Gửi tin nhắn để bắt đầu cuộc trò chuyện.', 'en' => 'Send a message to start the conversation.'],
        'type_your_message' => ['vi' => 'Nhập tin nhắn của bạn...', 'en' => 'Type your message...'],
        'send' => ['vi' => 'Gửi', 'en' => 'Send'],
    ],
    
    // Common time keys
    'common_keys' => [
        'today' => ['vi' => 'Hôm nay', 'en' => 'Today'],
        'yesterday' => ['vi' => 'Hôm qua', 'en' => 'Yesterday'],
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
    'conversations_keys' => 'conversations',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($conversationsShowKeys as $category => $keys) {
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
echo "Total conversations show keys added: $totalAdded\n";
echo "Categories processed: " . count($conversationsShowKeys) . "\n";

echo "\n✅ Conversations show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
