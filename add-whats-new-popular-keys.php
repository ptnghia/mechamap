<?php

/**
 * ADD WHATS NEW POPULAR KEYS
 * Thêm tất cả keys thiếu cho whats-new/popular.blade.php
 */

echo "=== ADDING WHATS NEW POPULAR KEYS ===\n\n";

// All whats new popular keys organized by file
$whatsNewPopularKeys = [
    // Navigation keys
    'navigation_keys' => [
        'main.whats_new' => ['vi' => 'Có gì mới', 'en' => "What's New"],
    ],
    
    // Common time keys
    'common_keys' => [
        'time.today' => ['vi' => 'Hôm nay', 'en' => 'Today'],
        'time.this_week' => ['vi' => 'Tuần này', 'en' => 'This Week'],
        'time.this_month' => ['vi' => 'Tháng này', 'en' => 'This Month'],
        'time.this_year' => ['vi' => 'Năm này', 'en' => 'This Year'],
        'time.all_time' => ['vi' => 'Mọi thời gian', 'en' => 'All Time'],
    ],
    
    // UI keys
    'ui_keys' => [
        'pagination.go_to_page' => ['vi' => 'Đi đến trang', 'en' => 'Go to Page'],
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
    'navigation_keys' => 'navigation',
    'common_keys' => 'common',
    'ui_keys' => 'ui',
];

$totalAdded = 0;

foreach ($whatsNewPopularKeys as $category => $keys) {
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
echo "Total whats new popular keys added: $totalAdded\n";
echo "Categories processed: " . count($whatsNewPopularKeys) . "\n";

echo "\n✅ Whats new popular keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
