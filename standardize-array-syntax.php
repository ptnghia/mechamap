<?php

/**
 * STANDARDIZE ARRAY SYNTAX
 * Chuẩn hóa cú pháp array từ array() sang [] cho tất cả file translation
 */

echo "=== STANDARDIZING ARRAY SYNTAX ===\n\n";

$filesToFix = [
    'resources/lang/vi/common.php',
    'resources/lang/en/common.php',
    'resources/lang/vi/navigation.php',
    'resources/lang/en/navigation.php'
];

$totalFixed = 0;
$filesProcessed = 0;

foreach ($filesToFix as $filePath) {
    $fullPath = __DIR__ . '/' . $filePath;
    
    if (!file_exists($fullPath)) {
        echo "❌ File not found: $filePath\n";
        continue;
    }
    
    echo "🔄 Processing $filePath...\n";
    
    $content = file_get_contents($fullPath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        continue;
    }
    
    $originalContent = $content;
    
    // Convert array( to [
    $content = preg_replace('/array\s*\(\s*/', '[', $content);
    
    // Convert closing ) to ] for arrays
    // This is more complex as we need to match the correct closing parentheses
    $content = convertArrayClosing($content);
    
    // Clean up extra whitespace
    $content = preg_replace('/=>\s*\n\s*\[/', '=> [', $content);
    
    if ($content !== $originalContent) {
        // Backup original file
        $backupPath = $fullPath . '.backup.' . date('Y-m-d-H-i-s');
        file_put_contents($backupPath, $originalContent);
        
        // Write updated content
        if (file_put_contents($fullPath, $content)) {
            echo "✅ Updated $filePath\n";
            echo "   📁 Backup created: " . basename($backupPath) . "\n";
            $filesProcessed++;
            
            // Count changes
            $changes = substr_count($originalContent, 'array (') + substr_count($originalContent, 'array(');
            $totalFixed += $changes;
        } else {
            echo "❌ Failed to write $filePath\n";
        }
    } else {
        echo "ℹ️  No changes needed for $filePath\n";
    }
}

function convertArrayClosing($content) {
    // Split content into lines for easier processing
    $lines = explode("\n", $content);
    $result = [];
    $arrayDepth = 0;
    $inArray = false;
    
    foreach ($lines as $line) {
        $originalLine = $line;
        
        // Count opening brackets
        $openBrackets = substr_count($line, '[');
        $closeBrackets = substr_count($line, ']');
        
        // Count opening parentheses that are part of arrays
        $openArrayParens = 0;
        if (preg_match_all('/array\s*\(/', $line, $matches)) {
            $openArrayParens = count($matches[0]);
        }
        
        // Update array depth
        $arrayDepth += $openBrackets + $openArrayParens - $closeBrackets;
        
        if ($arrayDepth > 0) {
            $inArray = true;
        }
        
        // Convert closing parentheses to brackets if we're in an array context
        if ($inArray && $arrayDepth > 0) {
            // Look for closing parentheses that should be brackets
            if (preg_match('/^(\s*)\),?\s*$/', $line, $matches)) {
                $indent = $matches[1];
                $hasComma = strpos($line, '),') !== false;
                $line = $indent . ']' . ($hasComma ? ',' : '');
                $arrayDepth--;
            }
        }
        
        if ($arrayDepth <= 0) {
            $inArray = false;
            $arrayDepth = 0;
        }
        
        $result[] = $line;
    }
    
    return implode("\n", $result);
}

echo "\n=== SUMMARY ===\n";
echo "Files processed: $filesProcessed\n";
echo "Total array() conversions: $totalFixed\n";

// Verify syntax after changes
echo "\n🔍 Verifying syntax after changes...\n";

foreach ($filesToFix as $filePath) {
    $fullPath = __DIR__ . '/' . $filePath;
    
    if (!file_exists($fullPath)) {
        continue;
    }
    
    echo "  📄 Checking $filePath... ";
    
    try {
        $content = @include $fullPath;
        
        if ($content === false) {
            echo "❌ FAILED\n";
            continue;
        }
        
        if (!is_array($content)) {
            echo "❌ NOT ARRAY\n";
            continue;
        }
        
        echo "✅ OK\n";
        
    } catch (ParseError $e) {
        echo "❌ PARSE ERROR: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Array syntax standardization completed at " . date('Y-m-d H:i:s') . "\n";
?>
