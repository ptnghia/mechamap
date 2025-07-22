<?php

/**
 * ADD MEMBERS STAFF KEYS
 * Thêm tất cả keys thiếu cho members/staff.blade.php
 */

echo "=== ADDING MEMBERS STAFF KEYS ===\n\n";

// All members staff keys
$membersStaffKeys = [
    'members.administrator' => ['vi' => 'Quản trị viên', 'en' => 'Administrator'],
    'members.no_administrators_found' => ['vi' => 'Không tìm thấy quản trị viên nào', 'en' => 'No administrators found'],
    'members.no_moderators_found' => ['vi' => 'Không tìm thấy điều hành viên nào', 'en' => 'No moderators found'],
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

echo "📁 Processing members staff keys for common.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
if (addKeysToFile($viFile, $membersStaffKeys, 'vi')) {
    $totalAdded = count($membersStaffKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $membersStaffKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total members staff keys added: " . count($membersStaffKeys) . "\n";

echo "\n✅ Members staff keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
