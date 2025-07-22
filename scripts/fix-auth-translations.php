<?php

/**
 * Fix Auth Translation File
 * Fix specific array issues in auth.php files
 */

echo "🔧 Fixing Auth Translation Files\n";
echo "================================\n\n";

$authFiles = [
    'resources/lang/vi/auth.php',
    'resources/lang/en/auth.php',
];

foreach ($authFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  Skipping {$file} - not found\n";
        continue;
    }

    echo "🔍 Processing: {$file}\n";

    // Backup
    $backupFile = $file . '.backup.' . date('Y-m-d-H-i-s');
    copy($file, $backupFile);
    echo "💾 Backup created: {$backupFile}\n";

    // Read and fix content
    $content = file_get_contents($file);
    $originalContent = $content;

    // Specific fixes for auth.php patterns
    $fixes = [
        // Fix title array
        "'title' =>\n    array (\n      0 => 'Đăng nhập',\n      1 => 'Đăng nhập',\n    )," => "'title' => 'Đăng nhập',",

        // Alternative patterns
        "'title' => \n  array (\n    0 => 'Đăng nhập',\n    1 => 'Đăng nhập',\n  )," => "'title' => 'Đăng nhập',",

        // English version
        "'title' =>\n    array (\n      0 => 'Login',\n      1 => 'Login',\n    )," => "'title' => 'Login',",
        "'title' => \n  array (\n    0 => 'Login',\n    1 => 'Login',\n  )," => "'title' => 'Login',",
    ];

    $changeCount = 0;
    foreach ($fixes as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $changeCount++;
            echo "  ✅ Fixed title array pattern\n";
        }
    }

    // Additional regex patterns for any remaining issues
    $patterns = [
        // Pattern: 'key' => array(0 => 'value', 1 => 'value')
        "/('[\w_]+'\s*=>\s*)array\s*\(\s*0\s*=>\s*'([^']+)',\s*1\s*=>\s*'[^']*',?\s*\)/i" => "$1'$2'",
        // Pattern with more spaces/newlines
        "/('[\w_]+'\s*=>\s*)\n\s*array\s*\(\s*\n\s*0\s*=>\s*'([^']+)',\s*\n\s*1\s*=>\s*'[^']*',?\s*\n\s*\)/i" => "$1'$2'",
    ];

    foreach ($patterns as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            $content = $newContent;
            $changeCount++;
            echo "  ✅ Fixed via regex pattern\n";
        }
    }

    // Write back
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "✅ Fixed {$changeCount} issues in {$file}\n";
    } else {
        echo "✅ No issues found in {$file}\n";
        unlink($backupFile);
    }

    echo "\n";
}

echo "🧪 Testing fixed files...\n";
echo "=========================\n";

foreach ($authFiles as $file) {
    if (!file_exists($file)) continue;

    echo "Testing {$file}:\n";

    // Test syntax
    $tempContent = file_get_contents($file);
    $tempFile = tempnam(sys_get_temp_dir(), 'auth_test');
    file_put_contents($tempFile, $tempContent);

    ob_start();
    $result = @include $tempFile;
    $errors = ob_get_clean();
    unlink($tempFile);

    if ($result === false || !empty($errors)) {
        echo "❌ Syntax error detected\n";
        if (!empty($errors)) echo "   Error: {$errors}\n";
    } else {
        echo "✅ Valid PHP syntax\n";

        // Check for remaining problematic patterns
        $remaining = preg_match_all("/0\s*=>/", $tempContent);
        if ($remaining > 0) {
            echo "⚠️  Still contains {$remaining} indexed array patterns\n";
        } else {
            echo "✅ No problematic array patterns found\n";
        }
    }
    echo "\n";
}

echo "🎯 COMPLETED\n";
echo "============\n";
echo "Auth translation files have been processed.\n";
echo "Next: Clear cache and test the website.\n\n";

?>
