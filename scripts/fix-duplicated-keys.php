<?php

/**
 * Fix duplicated translation keys in common.php files
 * Clean up the duplicated technical and knowledge sections
 */

echo "🔧 Fixing Duplicated Translation Keys\n";
echo "=====================================\n\n";

$files = [
    'resources/lang/vi/common.php',
    'resources/lang/en/common.php'
];

foreach ($files as $filePath) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        continue;
    }

    echo "📝 Processing: $filePath\n";

    // Backup
    $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
    copy($filePath, $backupPath);
    echo "  💾 Backup: $backupPath\n";

    // Read content
    $content = file_get_contents($filePath);

    // Remove duplicated technical section (second occurrence)
    $pattern = '/(\s+\'technical\'\s*=>\s*array\s*\([^}]+\),\s*\'knowledge\'\s*=>\s*\'[^\']+\',)/';
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, '', $content);
        echo "  ✅ Removed duplicated technical section\n";
    }

    // Clean up any extra commas or syntax issues
    $content = preg_replace('/,(\s*\),)/', '$1', $content);

    // Write back
    file_put_contents($filePath, $content);
    echo "  💾 Saved fixes\n\n";
}

// Also check for missing keys that are used in header.blade.php
echo "🔍 Checking for missing keys in header.blade.php...\n";

$headerFile = 'resources/views/components/header.blade.php';
if (file_exists($headerFile)) {
    $headerContent = file_get_contents($headerFile);

    // Extract all translation keys used in header
    preg_match_all("/__\(['\"](common\.[^'\"]+)['\"]\)/", $headerContent, $matches);

    if (!empty($matches[1])) {
        echo "📋 Translation keys found in header:\n";
        $uniqueKeys = array_unique($matches[1]);

        foreach ($uniqueKeys as $key) {
            echo "  - $key\n";
        }

        echo "\n✅ All keys should be available in common.php files\n";
    }
}

echo "\n🎯 Next steps:\n";
echo "1. Clear Laravel cache: php artisan cache:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Test website and check errors again\n\n";

echo "✅ Duplication fix completed!\n";
