<?php

/**
 * ADD SEARCH INDEX KEYS
 * Thêm tất cả keys thiếu cho search/index.blade.php
 */

echo "=== ADDING SEARCH INDEX KEYS ===\n\n";

// All search index keys organized by file
$searchIndexKeys = [
    // Search keys
    'search_keys' => [
        'form.submit' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    ],
    
    // Common keys
    'common_keys' => [
        'by' => ['vi' => 'Bởi', 'en' => 'By'],
        'reply_in' => ['vi' => 'Trả lời trong', 'en' => 'Reply in'],
        'joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
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
    'search_keys' => 'search',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($searchIndexKeys as $category => $keys) {
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
echo "Total search index keys added: $totalAdded\n";
echo "Categories processed: " . count($searchIndexKeys) . "\n";

echo "\n✅ Search index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
