<?php
/**
 * Fix Single Blade File Translation Keys
 * Sửa translation keys trong một file blade cụ thể
 */

if ($argc < 2) {
    echo "Usage: php fix_single_blade_file.php <blade_file_path>\n";
    echo "Example: php fix_single_blade_file.php resources/views/home.blade.php\n";
    exit(1);
}

$filePath = $argv[1];
$basePath = __DIR__ . '/..';
$fullPath = $basePath . '/' . $filePath;

if (!file_exists($fullPath)) {
    echo "❌ File not found: {$fullPath}\n";
    exit(1);
}

echo "🔧 FIXING TRANSLATION KEYS IN: {$filePath}\n";
echo str_repeat("=", 60) . "\n";

// Key mapping từ old pattern sang new pattern
$keyMappings = [
    // Navigation keys
    'nav.main.home' => 'navigation.main.home',
    'nav.home' => 'navigation.main.home',
    'nav.forums' => 'navigation.main.forums',
    'nav.marketplace' => 'navigation.main.marketplace',
    'nav.showcase' => 'navigation.main.showcase',
    'nav.auth.login' => 'navigation.auth.login',
    'nav.auth.register' => 'navigation.auth.register',
    
    // UI keys with slash notation
    'ui/navigation.auth.login' => 'navigation.auth.login',
    'ui.common.password' => 'common.form.password',
    'ui.common.forgot_password' => 'auth.login.actions.forgot_password',
    
    // Auth keys
    'auth.login.welcome_back' => 'auth.login.messages.welcome_back',
    'auth.login.email_or_username' => 'auth.login.form.email_or_username',
    'auth.login.remember' => 'auth.login.form.remember',
    'auth.login.or_login_with' => 'auth.login.messages.or_login_with',
    'auth.login.login_with_google' => 'auth.login.social.google',
    'auth.login.login_with_facebook' => 'auth.login.social.facebook',
    'auth.login.dont_have_account' => 'auth.login.messages.dont_have_account',
    'auth.register.create_business_account' => 'auth.register.actions.create_business_account',
    
    // Forum keys
    'forum.forums.title' => 'forums.title',
    'forum.threads.create' => 'forums.threads.actions.create',
    'forums.actions.create_thread' => 'forums.threads.actions.create',
    
    // Homepage keys
    'home.featured_showcases' => 'homepage.sections.featured_showcases',
    'home.featured_showcases_desc' => 'homepage.sections.featured_showcases_desc',
    
    // Common buttons
    'buttons.view_all' => 'common.buttons.view_all',
    'buttons.save' => 'common.buttons.save',
    'buttons.cancel' => 'common.buttons.cancel',
    'buttons.delete' => 'common.buttons.delete',
    'buttons.edit' => 'common.buttons.edit',
    'buttons.create' => 'common.buttons.create',
    'buttons.submit' => 'common.buttons.submit',
    'buttons.back' => 'common.buttons.back',
    'buttons.next' => 'common.buttons.next',
    'buttons.close' => 'common.buttons.close',
    
    // Content keys (problematic ones)
    'content.processing' => 'common.messages.processing',
    'content.error_occurred' => 'common.messages.error_occurred',
    
    // Coming soon keys
    'coming_soon.notify_success' => 'pages.coming_soon.notify_success',
    'coming_soon.share_text' => 'pages.coming_soon.share_text',
    'coming_soon.copied' => 'pages.coming_soon.copied',
    
    // Messages
    'messages.forgot_password' => 'auth.login.actions.forgot_password',
    'messages.success' => 'common.messages.success',
    'messages.error' => 'common.messages.error',
    'messages.loading' => 'common.messages.loading',
];

// Read file content
$content = file_get_contents($fullPath);
$originalContent = $content;

// Find existing translation keys
$existingKeys = [];
$patterns = [
    '/__(\'([^\']+)\')/m',
    '/__\("([^"]+)"\)/m',
    '/@lang\(\'([^\']+)\'\)/m',
    '/@lang\("([^"]+)"\)/m',
];

foreach ($patterns as $pattern) {
    preg_match_all($pattern, $content, $matches);
    foreach ($matches[1] as $key) {
        if (!empty($key)) {
            $existingKeys[] = $key;
        }
    }
}

$existingKeys = array_unique($existingKeys);

echo "📋 Found " . count($existingKeys) . " translation keys:\n";
foreach ($existingKeys as $key) {
    echo "  • {$key}\n";
}

// Apply key mappings
$changesCount = 0;
$changedKeys = [];

foreach ($keyMappings as $oldKey => $newKey) {
    if (in_array($oldKey, $existingKeys)) {
        // Escape special regex characters
        $escapedOldKey = preg_quote($oldKey, '/');
        
        // Replace patterns
        $patterns = [
            "/__('{$escapedOldKey}')/",
            "/__(\"{$escapedOldKey}\")/",
            "/@lang\('{$escapedOldKey}'\)/",
            "/@lang\(\"{$escapedOldKey}\"\)/",
        ];
        
        $replacements = [
            "__('$newKey')",
            "__(\"{$newKey}\")",
            "@lang('$newKey')",
            "@lang(\"{$newKey}\")",
        ];
        
        foreach ($patterns as $i => $pattern) {
            $newContent = preg_replace($pattern, $replacements[$i], $content);
            if ($newContent !== null && $newContent !== $content) {
                $content = $newContent;
                $changesCount++;
                $changedKeys[] = "{$oldKey} → {$newKey}";
            }
        }
    }
}

if ($changesCount > 0) {
    echo "\n🔄 Changes made ({$changesCount}):\n";
    foreach ($changedKeys as $change) {
        echo "  ✅ {$change}\n";
    }
    
    // Create backup
    $backupPath = $fullPath . '.backup.' . date('Y-m-d_H-i-s');
    copy($fullPath, $backupPath);
    echo "\n💾 Backup created: " . str_replace($basePath . '/', '', $backupPath) . "\n";
    
    // Write updated content
    file_put_contents($fullPath, $content);
    echo "✅ File updated successfully!\n";
} else {
    echo "\n✨ No changes needed - file is already using correct key structure!\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 SUMMARY:\n";
echo "  • Keys found: " . count($existingKeys) . "\n";
echo "  • Changes made: {$changesCount}\n";
echo "  • File: {$filePath}\n";

if ($changesCount > 0) {
    echo "\n📝 Next steps:\n";
    echo "  1. Test the page to ensure translations work\n";
    echo "  2. Check that all new keys exist in translation files\n";
    echo "  3. Run: php artisan config:clear && php artisan view:clear\n";
}

echo "\n✅ DONE!\n";
