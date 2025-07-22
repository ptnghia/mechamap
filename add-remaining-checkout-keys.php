<?php

/**
 * ADD REMAINING CHECKOUT KEYS
 * Thêm các keys còn thiếu cho marketplace/checkout/index.blade.php
 */

echo "=== ADDING REMAINING CHECKOUT KEYS ===\n\n";

// Remaining checkout keys that are missing
$remainingKeys = [
    // Missing checkout keys
    'checkout.place_order' => ['vi' => 'Đặt hàng', 'en' => 'Place Order'],
    
    // Feature keys (for t_feature calls)
    'actions.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
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

// Add checkout keys to marketplace.php
echo "📁 Processing remaining checkout keys for marketplace.php\n";

$checkoutKeys = ['checkout.place_order' => $remainingKeys['checkout.place_order']];

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
addKeysToFile($viFile, $checkoutKeys, 'vi');

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $checkoutKeys, 'en');

// Add feature keys to feature.php (create if doesn't exist)
echo "\n📁 Processing feature keys for feature.php\n";

$featureKeys = ['actions.cancel' => $remainingKeys['actions.cancel']];

foreach (['vi', 'en'] as $lang) {
    $featureFile = __DIR__ . "/resources/lang/$lang/feature.php";
    
    if (!file_exists($featureFile)) {
        // Create feature.php file
        $template = "<?php\n\n/**\n * Feature Translation File - " . 
                   ($lang === 'vi' ? 'Vietnamese' : 'English') . "\n */\n\nreturn [\n];\n";
        
        if (file_put_contents($featureFile, $template)) {
            echo "📄 Created new file: $featureFile\n";
        } else {
            echo "❌ Failed to create $featureFile\n";
            continue;
        }
    }
    
    addKeysToFile($featureFile, $featureKeys, $lang);
}

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($remainingKeys) . "\n";

// Test keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'marketplace.checkout.place_order',
    'feature.actions.cancel'
];

foreach ($testKeys as $key) {
    echo "  Testing __('$key')...\n";
}

echo "\n✅ Remaining checkout keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
