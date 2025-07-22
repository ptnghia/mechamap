<?php

/**
 * FIX ALL NAVIGATION KEYS
 * Sửa tất cả navigation keys còn thiếu để đạt 100%
 */

echo "=== FIXING ALL NAVIGATION KEYS ===\n\n";

// All missing navigation keys
$missingNavKeys = [
    'nav.main.marketplace' => ['vi' => 'Thị trường', 'en' => 'Marketplace'],
    'nav.main.whats_new' => ['vi' => 'Có gì mới', 'en' => "What's New"],
    'nav.main.home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
    'nav.main.forums' => ['vi' => 'Diễn đàn', 'en' => 'Forums'],
];

// Function to check and add missing keys
function addMissingNavigationKeys($filePath, $keys, $lang) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    $keysToAdd = [];
    $addedCount = 0;
    
    // Check which keys are missing
    foreach ($keys as $key => $translations) {
        if (strpos($content, "'$key'") === false && isset($translations[$lang])) {
            $keysToAdd[$key] = $translations[$lang];
        }
    }
    
    if (empty($keysToAdd)) {
        echo "✅ All navigation keys already exist in $filePath\n";
        return true;
    }
    
    // Find the last closing bracket
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        echo "❌ Could not find closing bracket in $filePath\n";
        return false;
    }
    
    // Build new keys string
    $newKeysString = '';
    foreach ($keysToAdd as $key => $value) {
        $value = str_replace("'", "\\'", $value);
        $newKeysString .= "  '$key' => '$value',\n";
        $addedCount++;
    }
    
    // Insert new keys before the closing bracket
    $beforeClosing = substr($content, 0, $lastBracketPos);
    $afterClosing = substr($content, $lastBracketPos);
    
    $newContent = $beforeClosing . $newKeysString . $afterClosing;
    
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Added $addedCount navigation keys to $filePath\n";
        return true;
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

echo "📁 Processing navigation keys...\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/navigation.php";
addMissingNavigationKeys($viFile, $missingNavKeys, 'vi');

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/navigation.php";
addMissingNavigationKeys($enFile, $missingNavKeys, 'en');

echo "\n✅ Navigation keys fix completed at " . date('Y-m-d H:i:s') . "\n";
?>
