<?php

/**
 * Fix auth-modal.blade.php translation keys
 * Fix incorrect patterns like ui/navigation.auth.login and content/alerts.processing
 */

echo "🔧 Fixing Auth Modal Translation Keys\n";
echo "=====================================\n\n";

$authModalFile = 'resources/views/components/auth-modal.blade.php';

if (!file_exists($authModalFile)) {
    echo "❌ File not found: $authModalFile\n";
    exit(1);
}

echo "📝 Processing: $authModalFile\n";

// Backup
$backupPath = $authModalFile . '.backup.' . date('Y-m-d-H-i-s');
copy($authModalFile, $backupPath);
echo "  💾 Backup: $backupPath\n";

// Read content
$content = file_get_contents($authModalFile);

// Pattern fixes
$fixes = [
    // Fix ui/navigation.auth.login -> navigation.auth.login
    "/__\('ui\/navigation\.auth\.login'\)/" => "__('navigation.auth.login')",
    '/__\("ui\/navigation\.auth\.login"\)/' => '__("navigation.auth.login")',

    // Fix content/alerts.processing -> common.messages.processing
    "/__\([\"']content\/alerts\.processing[\"']\)/" => "__('common.messages.processing')",

    // Fix common.messages.forgot_password (we'll add this key to common.php)
    // This pattern should be fine, we just need to add the key
];

$changesCount = 0;

foreach ($fixes as $pattern => $replacement) {
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        $changesCount++;
        echo "  ✅ Applied fix: $pattern\n";
    }
}

// Write back
file_put_contents($authModalFile, $content);
echo "  💾 Saved $changesCount changes\n\n";

// Now add missing keys to common.php files
echo "📝 Adding missing keys to common.php files...\n";

$commonFiles = [
    'resources/lang/vi/common.php' => [
        'forgot_password' => 'Quên mật khẩu'
    ],
    'resources/lang/en/common.php' => [
        'forgot_password' => 'Forgot Password'
    ]
];

foreach ($commonFiles as $filePath => $keys) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        continue;
    }

    echo "  📝 Processing: $filePath\n";

    // Backup
    $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
    copy($filePath, $backupPath);
    echo "    💾 Backup: $backupPath\n";

    $content = file_get_contents($filePath);

    // Find the messages section and add forgot_password if not exists
    if (strpos($content, "'forgot_password'") === false) {
        // Look for the messages section and add the key before the closing bracket
        $pattern = "/(\'messages\'\s*=>\s*array\s*\([^}]+)(\s*\)\s*,)/";
        if (preg_match($pattern, $content)) {
            $replacement = "$1      'forgot_password' => '{$keys['forgot_password']}',\n$2";
            $content = preg_replace($pattern, $replacement, $content);
            echo "    ✅ Added forgot_password key\n";
        }
    }

    file_put_contents($filePath, $content);
    echo "    💾 Saved changes\n";
}

echo "\n🎯 Next steps:\n";
echo "1. Clear Laravel cache: php artisan cache:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Test auth modal functionality\n";
echo "4. Check error logs: php check-error-logs.php\n\n";

echo "✅ Auth modal fixes completed!\n";
