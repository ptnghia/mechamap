<?php

/**
 * Fix Array Syntax in Language Files
 * Convert array() to [] syntax for Laravel 11 compliance
 */

echo "🔧 FIXING ARRAY SYNTAX IN LANGUAGE FILES\n";
echo "========================================\n";

$basePath = __DIR__ . '/../';
$langPath = $basePath . 'resources/lang';

// Find all PHP files with array() syntax
$command = "find $langPath -name '*.php' -exec grep -l 'return array' {} \\;";
$files = shell_exec($command);
$fileList = array_filter(explode("\n", trim($files)));

echo "Found " . count($fileList) . " files to fix:\n\n";

$fixed = 0;
$errors = 0;

foreach ($fileList as $file) {
    $relativePath = str_replace($basePath, '', $file);
    echo "Processing: $relativePath\n";
    
    try {
        $content = file_get_contents($file);
        
        // Fix array() syntax patterns
        $patterns = [
            // Main return array
            '/return array \(/' => 'return [',
            
            // Nested arrays with proper spacing
            '/(\s+)array \(/' => '$1[',
            
            // Close array parentheses - be careful with nested structures
            '/\);(\s*)$/' => '];$1',
            
            // Fix closing parentheses for nested arrays (more complex)
            '/(\s+)\),(\s*)/' => '$1],$2',
            '/(\s+)\)(\s*)$/' => '$1]$2',
        ];
        
        $originalContent = $content;
        
        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        
        // Additional cleanup for complex nested structures
        $content = preg_replace('/\),\s*\n(\s*)\);/', '],\n$1];', $content);
        $content = preg_replace('/\)\s*\n(\s*)\);/', ']\n$1];', $content);
        
        // Final cleanup - fix any remaining );
        $content = preg_replace('/\);(\s*)$/', '];$1', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "  ✅ Fixed array syntax\n";
            $fixed++;
        } else {
            echo "  ⚠️  No changes needed\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
        $errors++;
    }
    
    echo "\n";
}

echo "🎉 SUMMARY:\n";
echo "===========\n";
echo "Files processed: " . count($fileList) . "\n";
echo "Files fixed: $fixed\n";
echo "Errors: $errors\n";

if ($errors === 0) {
    echo "\n✅ All files processed successfully!\n";
} else {
    echo "\n⚠️  Some files had errors. Please check manually.\n";
}

echo "\n🔍 Verifying syntax...\n";

// Verify PHP syntax for all fixed files
$syntaxErrors = 0;
foreach ($fileList as $file) {
    $output = [];
    $returnCode = 0;
    exec("php -l \"$file\" 2>&1", $output, $returnCode);
    
    if ($returnCode !== 0) {
        echo "❌ Syntax error in $file\n";
        $syntaxErrors++;
    }
}

if ($syntaxErrors === 0) {
    echo "✅ All files have valid PHP syntax!\n";
} else {
    echo "❌ $syntaxErrors files have syntax errors!\n";
}
