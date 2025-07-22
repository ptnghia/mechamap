<?php

/**
 * ADD CHAT WIDGET KEYS
 * Thêm tất cả keys thiếu cho components/chat-widget.blade.php
 */

echo "=== ADDING CHAT WIDGET KEYS ===\n\n";

// All chat widget keys
$chatWidgetKeys = [
    'forms.search_conversations_placeholder' => ['vi' => 'Tìm kiếm cuộc trò chuyện...', 'en' => 'Search conversations...'],
    'forms.enter_message_placeholder' => ['vi' => 'Nhập tin nhắn...', 'en' => 'Enter message...'],
    'forms.search_members_placeholder' => ['vi' => 'Tìm kiếm thành viên...', 'en' => 'Search members...'],
    'buttons.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
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

echo "📁 Processing chat widget keys for ui.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/ui.php";
if (addKeysToFile($viFile, $chatWidgetKeys, 'vi')) {
    $totalAdded = count($chatWidgetKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/ui.php";
addKeysToFile($enFile, $chatWidgetKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total chat widget keys added: " . count($chatWidgetKeys) . "\n";

echo "\n✅ Chat widget keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
