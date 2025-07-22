<?php

/**
 * ADD AUTH REGISTER KEYS
 * Thêm tất cả keys thiếu cho auth/register.blade.php
 */

echo "=== ADDING AUTH REGISTER KEYS ===\n\n";

// All auth register keys
$authRegisterKeys = [
    'create_new_account' => ['vi' => 'Tạo tài khoản mới', 'en' => 'Create New Account'],
    'welcome_to_mechamap' => ['vi' => 'Chào mừng đến với MechaMap', 'en' => 'Welcome to MechaMap'],
    'create_account_journey' => ['vi' => 'Tạo tài khoản để bắt đầu hành trình kỹ thuật của bạn', 'en' => 'Create an account to start your engineering journey'],
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

echo "📁 Processing auth register keys for auth.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/auth.php";
if (addKeysToFile($viFile, $authRegisterKeys, 'vi')) {
    $totalAdded = count($authRegisterKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/auth.php";
addKeysToFile($enFile, $authRegisterKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total auth register keys added: " . count($authRegisterKeys) . "\n";

echo "\n✅ Auth register keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
