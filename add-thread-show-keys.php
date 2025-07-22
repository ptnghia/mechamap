<?php

/**
 * ADD THREAD SHOW KEYS
 * Thêm tất cả keys thiếu cho threads/show.blade.php
 */

echo "=== ADDING THREAD SHOW KEYS ===\n\n";

// All thread show keys
$threadShowKeys = [
    // Thread keys
    'last_post_by' => ['vi' => 'Bài cuối bởi', 'en' => 'Last post by'],
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

echo "📁 Processing thread show keys for thread.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/thread.php";
if (addKeysToFile($viFile, $threadShowKeys, 'vi')) {
    $totalAdded = count($threadShowKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/thread.php";
addKeysToFile($enFile, $threadShowKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total thread show keys added: " . count($threadShowKeys) . "\n";

echo "\n✅ Thread show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
