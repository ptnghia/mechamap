<?php

/**
 * ADD FEATURE MARKETPLACE CANCEL KEY
 * Thêm key feature.marketplace.actions.cancel
 */

echo "=== ADDING FEATURE MARKETPLACE CANCEL KEY ===\n\n";

// The missing key
$cancelKey = [
    'feature.marketplace.actions.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
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

echo "📁 Processing feature marketplace cancel key for common.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
if (addKeysToFile($viFile, $cancelKey, 'vi')) {
    $totalAdded = count($cancelKey);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $cancelKey, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total feature marketplace cancel keys added: " . count($cancelKey) . "\n";

echo "\n✅ Feature marketplace cancel key addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
