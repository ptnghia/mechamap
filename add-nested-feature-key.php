<?php

/**
 * ADD NESTED FEATURE KEY
 * Thêm key feature.marketplace.actions.cancel với cấu trúc nested
 */

echo "=== ADDING NESTED FEATURE KEY ===\n\n";

// Function to add nested keys to translation files
function addNestedKeysToFile($filePath, $lang) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    // Check if 'feature' key already exists
    if (strpos($content, "'feature' =>") !== false) {
        echo "ℹ️  'feature' key already exists in $filePath\n";
        
        // Check if marketplace exists under feature
        if (strpos($content, "'marketplace' =>") !== false) {
            echo "ℹ️  'marketplace' key already exists under feature\n";
            
            // Check if actions exists under marketplace
            if (strpos($content, "'actions' =>") !== false) {
                echo "ℹ️  'actions' key already exists under marketplace\n";
                
                // Check if cancel exists
                if (strpos($content, "'cancel' =>") !== false) {
                    echo "ℹ️  'cancel' key already exists\n";
                    return true;
                } else {
                    // Add cancel to existing actions
                    $cancelValue = $lang === 'vi' ? 'Hủy' : 'Cancel';
                    $pattern = "/('actions' => \[[\s\S]*?)(    \])/";
                    $replacement = "$1        'cancel' => '$cancelValue',\n$2";
                    $newContent = preg_replace($pattern, $replacement, $content);
                    
                    if ($newContent && $newContent !== $content) {
                        file_put_contents($filePath, $newContent);
                        echo "✅ Added 'cancel' to existing actions in $filePath\n";
                        return true;
                    }
                }
            } else {
                // Add actions with cancel to existing marketplace
                $cancelValue = $lang === 'vi' ? 'Hủy' : 'Cancel';
                $actionsArray = "        'actions' => [\n            'cancel' => '$cancelValue',\n        ],";
                
                $pattern = "/('marketplace' => \[[\s\S]*?)(    \])/";
                $replacement = "$1$actionsArray\n$2";
                $newContent = preg_replace($pattern, $replacement, $content);
                
                if ($newContent && $newContent !== $content) {
                    file_put_contents($filePath, $newContent);
                    echo "✅ Added 'actions' with 'cancel' to existing marketplace in $filePath\n";
                    return true;
                }
            }
        } else {
            // Add marketplace with actions and cancel to existing feature
            $cancelValue = $lang === 'vi' ? 'Hủy' : 'Cancel';
            $marketplaceArray = "        'marketplace' => [\n            'actions' => [\n                'cancel' => '$cancelValue',\n            ],\n        ],";
            
            $pattern = "/('feature' => \[[\s\S]*?)(    \])/";
            $replacement = "$1$marketplaceArray\n$2";
            $newContent = preg_replace($pattern, $replacement, $content);
            
            if ($newContent && $newContent !== $content) {
                file_put_contents($filePath, $newContent);
                echo "✅ Added 'marketplace' with nested structure to existing feature in $filePath\n";
                return true;
            }
        }
    } else {
        // Add complete feature structure
        $cancelValue = $lang === 'vi' ? 'Hủy' : 'Cancel';
        $featureArray = "  'feature' => [\n    'marketplace' => [\n      'actions' => [\n        'cancel' => '$cancelValue',\n      ],\n    ],\n  ],\n";
        
        // Find the last closing bracket
        $lastBracketPos = strrpos($content, '];');
        if ($lastBracketPos === false) {
            $lastBracketPos = strrpos($content, ');');
            if ($lastBracketPos === false) {
                echo "❌ Could not find closing bracket in $filePath\n";
                return false;
            }
        }
        
        // Insert new structure before the closing bracket
        $beforeClosing = substr($content, 0, $lastBracketPos);
        $afterClosing = substr($content, $lastBracketPos);
        
        $newContent = $beforeClosing . $featureArray . $afterClosing;
        
        if (file_put_contents($filePath, $newContent)) {
            echo "✅ Added complete 'feature' structure to $filePath\n";
            return true;
        } else {
            echo "❌ Failed to write $filePath\n";
            return false;
        }
    }
    
    return false;
}

echo "📁 Processing nested feature key for common.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
addNestedKeysToFile($viFile, 'vi');

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addNestedKeysToFile($enFile, 'en');

echo "\n✅ Nested feature key addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
