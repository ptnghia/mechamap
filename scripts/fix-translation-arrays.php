<?php

/**
 * Fix Translation Array Issues
 * Convert problematic translation arrays to proper strings
 */

echo "🔧 MechaMap Translation Array Fixer\n";
echo "==================================\n\n";

$translationFiles = [
    'resources/lang/vi/common.php',
    'resources/lang/en/common.php',
    'resources/lang/vi/navigation.php',
    'resources/lang/en/navigation.php',
    'resources/lang/vi/ui/common.php',
    'resources/lang/en/ui/common.php',
];

foreach ($translationFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  Skipping {$file} - not found\n";
        continue;
    }

    echo "🔍 Processing: {$file}\n";

    // Read file content
    $content = file_get_contents($file);
    $originalContent = $content;

    // Backup file
    $backupFile = $file . '.backup.' . date('Y-m-d-H-i-s');
    file_put_contents($backupFile, $content);
    echo "💾 Backup created: {$backupFile}\n";

    // Fix array structures that should be strings
    $fixes = [
        // Pattern: 'key' => array(0 => 'value', 1 => 'value')
        // Replace with: 'key' => 'value'
        "/('[\w_]+'\s*=>\s*)array\s*\(\s*0\s*=>\s*'([^']+)',\s*1\s*=>\s*'[^']+',?\s*\)/i" => "$1'$2'",

        // Pattern: 'key' => array(0 => 'value')
        "/('[\w_]+'\s*=>\s*)array\s*\(\s*0\s*=>\s*'([^']+)',?\s*\)/i" => "$1'$2'",

        // Pattern: "key" => array(0 => "value", 1 => "value")
        "/(\"[\w_]+\"\s*=>\s*)array\s*\(\s*0\s*=>\s*\"([^\"]+)\",\s*1\s*=>\s*\"[^\"]+\",?\s*\)/i" => "$1\"$2\"",

        // Pattern: "key" => array(0 => "value")
        "/(\"[\w_]+\"\s*=>\s*)array\s*\(\s*0\s*=>\s*\"([^\"]+)\",?\s*\)/i" => "$1\"$2\"",
    ];

    $changeCount = 0;
    foreach ($fixes as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            $matches = preg_match_all($pattern, $content);
            $changeCount += $matches;
            $content = $newContent;
        }
    }

    // Additional manual fixes for specific problematic patterns
    $manualFixes = [
        // Fix specific problematic keys we found
        "'save' =>\n  array (\n    0 => 'Lưu',\n    1 => 'Lưu',\n  )," => "'save' => 'Lưu',",
        "'cancel' =>\n  array (\n    0 => 'Hủy',\n    1 => 'Hủy',\n  )," => "'cancel' => 'Hủy',",
        "'delete' =>\n  array (\n    0 => 'Xóa',\n    1 => 'Xóa',\n  )," => "'delete' => 'Xóa',",
        "'edit' =>\n  array (\n    0 => 'Sửa',\n    1 => 'Sửa',\n  )," => "'edit' => 'Sửa',",
        "'create' =>\n  array (\n    0 => 'Tạo',\n    1 => 'Tạo',\n  )," => "'create' => 'Tạo',",
        "'submit' =>\n  array (\n    0 => 'Gửi',\n    1 => 'Gửi',\n  )," => "'submit' => 'Gửi',",
        "'back' =>\n  array (\n    0 => 'Quay lại',\n    1 => 'Quay lại',\n  )," => "'back' => 'Quay lại',",
        "'next' =>\n  array (\n    0 => 'Tiếp theo',\n    1 => 'Tiếp theo',\n  )," => "'next' => 'Tiếp theo',",
        "'close' =>\n  array (\n    0 => 'Đóng',\n    1 => 'Đóng',\n  )," => "'close' => 'Đóng',",
    ];

    foreach ($manualFixes as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $changeCount++;
        }
    }

    // Write back if changed
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "✅ Fixed {$changeCount} translation array issues in {$file}\n";
    } else {
        echo "✅ No issues found in {$file}\n";
        // Remove backup if no changes
        unlink($backupFile);
    }

    echo "\n";
}

echo "🔍 Validating translation files...\n";
echo "==================================\n";

// Validate all translation files can be loaded
foreach ($translationFiles as $file) {
    if (!file_exists($file)) continue;

    echo "🧪 Testing: {$file}\n";

    // Test if file can be loaded as PHP
    $testContent = file_get_contents($file);
    $tempFile = tempnam(sys_get_temp_dir(), 'translation_test');
    file_put_contents($tempFile, $testContent);

    ob_start();
    $result = @include $tempFile;
    $output = ob_get_clean();
    unlink($tempFile);

    if ($result === false) {
        echo "❌ Syntax error in {$file}\n";
    } else {
        echo "✅ Valid PHP syntax in {$file}\n";

        // Check for remaining array issues
        $arrayCount = substr_count($testContent, 'array (');
        if ($arrayCount > 0) {
            echo "⚠️  Still contains {$arrayCount} array declarations (may be legitimate)\n";
        }
    }
}

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Clear Laravel's translation cache: php artisan cache:clear\n";
echo "2. Test your website to see if htmlspecialchars errors are resolved\n";
echo "3. If errors persist, run: php check-error-logs.php\n";
echo "4. Check specific translation keys mentioned in the error logs\n\n";

echo "📝 Common problematic patterns fixed:\n";
echo "- 'key' => array(0 => 'value', 1 => 'value') → 'key' => 'value'\n";
echo "- 'key' => array(0 => 'value') → 'key' => 'value'\n";
echo "- Fixed duplicate entries in button translations\n\n";

?>
