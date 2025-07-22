<?php

/**
 * ADD STUDENT LEARNING HUB KEYS
 * Thêm tất cả keys thiếu cho student/learning-hub.blade.php
 */

echo "=== ADDING STUDENT LEARNING HUB KEYS ===\n\n";

// All student learning hub keys
$studentLearningHubKeys = [
    // Common button keys
    'buttons.explore' => ['vi' => 'Khám phá', 'en' => 'Explore'],
    'buttons.create' => ['vi' => 'Tạo mới', 'en' => 'Create'],
    'buttons.join' => ['vi' => 'Tham gia', 'en' => 'Join'],
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

echo "📁 Processing student learning hub keys for common.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
if (addKeysToFile($viFile, $studentLearningHubKeys, 'vi')) {
    $totalAdded = count($studentLearningHubKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $studentLearningHubKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total student learning hub keys added: " . count($studentLearningHubKeys) . "\n";

echo "\n✅ Student learning hub keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
