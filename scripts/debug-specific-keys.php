<?php

/**
 * Debug specific translation keys that might return arrays
 * Location: scripts/debug-specific-keys.php
 */

echo "🔍 Debug Specific Translation Keys\n";
echo "===================================\n\n";

// List of problematic keys from header.blade.php
$suspiciousKeys = [
    'search.form.placeholder',
    'search.scope.all_content',
    'search.scope.in_thread',
    'search.scope.in_forum',
    'search.actions.advanced',
    'navigation.main.marketplace',
    'common.technical.resources',
    'common.knowledge.title',
    'marketplace.cart.shopping_cart',
    'forum.search.recent_searches',
];

echo "🔑 Testing translation keys for array returns...\n\n";

foreach ($suspiciousKeys as $key) {
    echo "Testing: $key\n";

    try {
        $value = __($key);

        if (is_array($value)) {
            echo "  ❌ RETURNS ARRAY: " . json_encode($value) . "\n";
        } elseif (is_string($value)) {
            if ($value === $key) {
                echo "  ⚠️  KEY NOT FOUND (returns key itself)\n";
            } else {
                echo "  ✅ OK: $value\n";
            }
        } else {
            echo "  ❓ UNKNOWN TYPE: " . gettype($value) . "\n";
        }
    } catch (Exception $e) {
        echo "  💥 ERROR: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "🔍 Checking specific translation files for array structures...\n\n";

$filesToCheck = [
    'resources/lang/vi/search.php',
    'resources/lang/en/search.php',
    'resources/lang/vi/navigation.php',
    'resources/lang/en/navigation.php',
    'resources/lang/vi/marketplace.php',
    'resources/lang/en/marketplace.php',
    'resources/lang/vi/forum.php',
    'resources/lang/en/forum.php',
];

foreach ($filesToCheck as $file) {
    echo "📄 Checking: $file\n";

    if (!file_exists($file)) {
        echo "  ❌ File not found\n\n";
        continue;
    }

    $content = file_get_contents($file);

    // Look for potential array structures that should be strings
    $patterns = [
        '/array\s*\(\s*0\s*=>\s*[\'"][^\'"]+[\'"]\s*,?\s*\)/' => 'Indexed array pattern',
        '/\=>\s*array\s*\(\s*[\'"][^\'"]*[\'"]\s*\=\>/' => 'Nested array pattern',
    ];

    $foundIssues = false;
    foreach ($patterns as $pattern => $description) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $foundIssues = true;
            echo "  ⚠️  Found $description:\n";
            foreach ($matches[0] as $match) {
                $lineNum = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                echo "    Line $lineNum: " . trim($match[0]) . "\n";
            }
        }
    }

    if (!$foundIssues) {
        echo "  ✅ No obvious array issues found\n";
    }

    echo "\n";
}

echo "🎯 Summary:\n";
echo "- Check any keys that return arrays or are not found\n";
echo "- Fix array structures in translation files\n";
echo "- Test again with: php scripts/check-error-logs.php\n\n";

echo "✅ Debug completed!\n";
