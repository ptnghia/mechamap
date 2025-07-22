<?php

/**
 * MechaMap Translation Key Fixer Template
 * Location: scripts/fix-translation-template.php
 *
 * Use this template to create new translation fixing scripts
 */

echo "🔧 MechaMap Translation Fix Script\n";
echo "==================================\n\n";

// Configuration
$targetFiles = [
    // Add target files here
    // 'resources/views/path/to/file.blade.php',
];

$translationFixes = [
    // Add translation patterns here
    // '/old_pattern/' => 'new_replacement',
];

// Backup function
function createBackup($filePath) {
    $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
    if (copy($filePath, $backupPath)) {
        echo "  💾 Backup: $backupPath\n";
        return true;
    }
    return false;
}

// Main processing
foreach ($targetFiles as $filePath) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        continue;
    }

    echo "📝 Processing: $filePath\n";

    // Create backup
    if (!createBackup($filePath)) {
        echo "❌ Failed to create backup for: $filePath\n";
        continue;
    }

    // Read content
    $content = file_get_contents($filePath);
    $changesCount = 0;

    // Apply fixes
    foreach ($translationFixes as $pattern => $replacement) {
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
            $changesCount++;
            echo "  ✅ Applied fix: $pattern\n";
        }
    }

    // Write back if changes were made
    if ($changesCount > 0) {
        file_put_contents($filePath, $content);
        echo "  💾 Saved $changesCount changes\n";
    } else {
        echo "  ➖ No changes needed\n";
    }

    echo "\n";
}

echo "🎯 Next steps:\n";
echo "1. Clear Laravel cache: php artisan cache:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Test functionality\n";
echo "4. Check error logs: php scripts/check-error-logs.php\n\n";

echo "✅ Translation fix completed!\n";
