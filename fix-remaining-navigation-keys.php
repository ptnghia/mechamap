<?php

/**
 * FIX REMAINING NAVIGATION KEYS
 * Sửa tất cả navigation keys còn thiếu
 */

echo "=== FIXING REMAINING NAVIGATION KEYS ===\n\n";

// Check if nav.main.marketplace exists
$viNavFile = __DIR__ . "/resources/lang/vi/navigation.php";
$enNavFile = __DIR__ . "/resources/lang/en/navigation.php";

echo "📁 Checking navigation files...\n";

// Read VI navigation file
$viContent = file_get_contents($viNavFile);
$enContent = file_get_contents($enNavFile);

// Check for missing keys
$missingKeys = [];

if (strpos($viContent, "'nav.main.marketplace'") === false) {
    $missingKeys['nav.main.marketplace'] = ['vi' => 'Thị trường', 'en' => 'Marketplace'];
}

if (strpos($viContent, "'nav.main.whats_new'") === false) {
    $missingKeys['nav.main.whats_new'] = ['vi' => 'Có gì mới', 'en' => "What's New"];
}

if (empty($missingKeys)) {
    echo "✅ All navigation keys already exist!\n";
} else {
    echo "📝 Found missing navigation keys: " . implode(', ', array_keys($missingKeys)) . "\n";
    
    // Function to add keys to file
    function addNavigationKeys($filePath, $keys, $lang) {
        $content = file_get_contents($filePath);
        
        // Find the last closing bracket
        $lastBracketPos = strrpos($content, '];');
        if ($lastBracketPos === false) {
            echo "❌ Could not find closing bracket in $filePath\n";
            return false;
        }
        
        // Build new keys string
        $newKeysString = '';
        foreach ($keys as $key => $translations) {
            if (isset($translations[$lang])) {
                $value = str_replace("'", "\\'", $translations[$lang]);
                $newKeysString .= "  '$key' => '$value',\n";
            }
        }
        
        // Insert new keys before the closing bracket
        $beforeClosing = substr($content, 0, $lastBracketPos);
        $afterClosing = substr($content, $lastBracketPos);
        
        $newContent = $beforeClosing . $newKeysString . $afterClosing;
        
        if (file_put_contents($filePath, $newContent)) {
            echo "✅ Added " . count($keys) . " keys to $filePath\n";
            return true;
        } else {
            echo "❌ Failed to write $filePath\n";
            return false;
        }
    }
    
    // Add to both files
    addNavigationKeys($viNavFile, $missingKeys, 'vi');
    addNavigationKeys($enNavFile, $missingKeys, 'en');
}

echo "\n✅ Navigation keys fix completed at " . date('Y-m-d H:i:s') . "\n";
?>
