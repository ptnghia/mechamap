<?php

/**
 * Simple PHP Syntax Checker
 * Check if helpers.php has syntax errors
 */

echo "🔍 Checking PHP Syntax...\n";
echo "=========================\n\n";

// Check helpers.php syntax
$file = 'app/helpers.php';
if (!file_exists($file)) {
    echo "❌ File not found: {$file}\n";
    exit(1);
}

echo "📄 Checking: {$file}\n";

// Use php -l to check syntax
$output = [];
$returnCode = 0;
exec("php -l {$file} 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Syntax OK: No syntax errors detected\n";
} else {
    echo "❌ Syntax Error:\n";
    foreach ($output as $line) {
        echo "   {$line}\n";
    }
}

echo "\n🎯 Next: If syntax is OK, check translation files existence\n";

?>