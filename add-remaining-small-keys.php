<?php

/**
 * ADD REMAINING SMALL KEYS
 * Thêm các keys còn thiếu từ các files nhỏ
 */

echo "=== ADDING REMAINING SMALL KEYS ===\n\n";

// All remaining keys organized by file
$remainingKeys = [
    // Forum keys
    'forum_keys' => [
        'threads.started_by_me' => ['vi' => 'Được tạo bởi tôi', 'en' => 'Started by me'],
    ],
    
    // Common keys for members
    'common_keys' => [
        'members.last_seen' => ['vi' => 'Lần cuối truy cập', 'en' => 'Last seen'],
        'members.no_members_online' => ['vi' => 'Không có thành viên nào đang trực tuyến', 'en' => 'No members online'],
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
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($remainingKeys as $category => $keys) {
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
echo "Total remaining keys added: $totalAdded\n";
echo "Categories processed: " . count($remainingKeys) . "\n";

echo "\n✅ Remaining keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
