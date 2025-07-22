<?php

/**
 * SMART ARRAY SYNTAX FIXER
 * Sửa cú pháp array một cách thông minh và chính xác
 */

echo "=== SMART ARRAY SYNTAX FIXER ===\n\n";

function fixArraySyntax($filePath) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    // Backup original
    $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
    file_put_contents($backupPath, $content);
    
    // Step 1: Convert return array( to return [
    $content = preg_replace('/^return\s+array\s*\(\s*$/m', 'return [', $content);
    
    // Step 2: Convert array ( to [
    $content = preg_replace('/array\s*\(\s*$/m', '[', $content);
    
    // Step 3: Find and convert closing parentheses
    $lines = explode("\n", $content);
    $result = [];
    $arrayStack = [];
    
    foreach ($lines as $lineNum => $line) {
        $trimmed = trim($line);
        
        // Track array opening
        if (preg_match('/\[\s*$/', $line)) {
            $arrayStack[] = $lineNum;
        }
        
        // Convert closing parentheses to brackets
        if (preg_match('/^(\s*)\),?\s*$/', $line, $matches)) {
            if (!empty($arrayStack)) {
                array_pop($arrayStack);
                $indent = $matches[1];
                $hasComma = strpos($line, '),') !== false;
                $line = $indent . ']' . ($hasComma ? ',' : '');
            }
        }
        
        // Handle final closing
        if (preg_match('/^(\s*)\);\s*$/', $line, $matches)) {
            if (!empty($arrayStack)) {
                $indent = $matches[1];
                $line = $indent . '];';
            }
        }
        
        $result[] = $line;
    }
    
    $newContent = implode("\n", $result);
    
    // Write the fixed content
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Fixed $filePath\n";
        echo "   📁 Backup: " . basename($backupPath) . "\n";
        
        // Verify syntax
        try {
            $testContent = @include $filePath;
            if (is_array($testContent)) {
                echo "   ✅ Syntax verified\n";
                return true;
            } else {
                echo "   ❌ Syntax verification failed\n";
                // Restore backup
                file_put_contents($filePath, $content);
                return false;
            }
        } catch (Exception $e) {
            echo "   ❌ Parse error: " . $e->getMessage() . "\n";
            // Restore backup
            file_put_contents($filePath, $content);
            return false;
        }
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

// Files to fix
$filesToFix = [
    'resources/lang/vi/common.php',
    'resources/lang/en/common.php',
    'resources/lang/vi/navigation.php',
    'resources/lang/en/navigation.php'
];

$successCount = 0;

foreach ($filesToFix as $file) {
    $fullPath = __DIR__ . '/' . $file;
    echo "🔄 Processing $file...\n";
    
    if (fixArraySyntax($fullPath)) {
        $successCount++;
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Files processed: " . count($filesToFix) . "\n";
echo "Successfully fixed: $successCount\n";

echo "\n✅ Smart array syntax fix completed at " . date('Y-m-d H:i:s') . "\n";
?>
