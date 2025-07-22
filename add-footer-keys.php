<?php

/**
 * ADD FOOTER KEYS
 * Thêm tất cả keys thiếu cho components/footer-fixed.blade.php
 */

echo "=== ADDING FOOTER KEYS ===\n\n";

// All footer keys
$footerKeys = [
    'copyright.all_rights_reserved' => ['vi' => 'Tất cả quyền được bảo lưu.', 'en' => 'All rights reserved.'],
    'social.facebook' => ['vi' => 'Facebook', 'en' => 'Facebook'],
    'social.twitter' => ['vi' => 'Twitter', 'en' => 'Twitter'],
    'social.instagram' => ['vi' => 'Instagram', 'en' => 'Instagram'],
    'social.linkedin' => ['vi' => 'LinkedIn', 'en' => 'LinkedIn'],
    'social.youtube' => ['vi' => 'YouTube', 'en' => 'YouTube'],
    'tools.toggle_theme' => ['vi' => 'Chuyển đổi chủ đề', 'en' => 'Toggle theme'],
    'tools.dark_mode' => ['vi' => 'Chế độ tối', 'en' => 'Dark mode'],
    'tools.light_mode' => ['vi' => 'Chế độ sáng', 'en' => 'Light mode'],
    'accessibility.toggle_navigation' => ['vi' => 'Chuyển đổi điều hướng', 'en' => 'Toggle navigation'],
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

echo "📁 Processing footer keys for footer.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/footer.php";
if (addKeysToFile($viFile, $footerKeys, 'vi')) {
    $totalAdded = count($footerKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/footer.php";
addKeysToFile($enFile, $footerKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total footer keys added: " . count($footerKeys) . "\n";

echo "\n✅ Footer keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
