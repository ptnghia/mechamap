<?php
/**
 * Fix Blade Translation Keys Script
 * Chuẩn hóa translation keys trong tất cả file .blade.php theo Laravel 11 standard
 */

echo "🔧 MECHAMAP BLADE TRANSLATION KEY STANDARDIZATION\n";
echo "================================================\n\n";

$basePath = __DIR__ . '/..';
$viewsPath = $basePath . '/resources/views';

// Key mapping từ old pattern sang new pattern
$keyMappings = [
    // Navigation keys - chuẩn hóa navigation
    'nav.main.home' => 'navigation.main.home',
    'nav.home' => 'navigation.main.home',
    'nav.forums' => 'navigation.main.forums',
    'nav.marketplace' => 'navigation.main.marketplace',
    'nav.showcase' => 'navigation.main.showcase',
    'nav.auth.login' => 'navigation.auth.login',
    'nav.auth.register' => 'navigation.auth.register',

    // UI keys with slash notation - loại bỏ slash
    'ui/navigation.auth.login' => 'navigation.auth.login',
    'ui.common.password' => 'common.form.password',
    'ui.common.forgot_password' => 'auth.login.actions.forgot_password',

    // Auth keys - chuẩn hóa auth structure
    'auth.login.title' => 'auth.login.title',
    'auth.login.welcome_back' => 'auth.login.messages.welcome_back',
    'auth.login.email_or_username' => 'auth.login.form.email_or_username',
    'auth.login.remember' => 'auth.login.form.remember',
    'auth.login.or_login_with' => 'auth.login.messages.or_login_with',
    'auth.login.login_with_google' => 'auth.login.social.google',
    'auth.login.login_with_facebook' => 'auth.login.social.facebook',
    'auth.login.dont_have_account' => 'auth.login.messages.dont_have_account',
    'auth.register.create_business_account' => 'auth.register.actions.create_business_account',

    // Forum keys - chuẩn hóa forum structure
    'forum.forums.title' => 'forums.title',
    'forum.threads.create' => 'forums.threads.actions.create',
    'forums.description' => 'forums.description',
    'forums.actions.create_thread' => 'forums.threads.actions.create',

    // Homepage keys - di chuyển vào homepage file
    'home.featured_showcases' => 'homepage.sections.featured_showcases',
    'home.featured_showcases_desc' => 'homepage.sections.featured_showcases_desc',

    // Common buttons - chuẩn hóa buttons
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

    // Content keys (problematic ones) - di chuyển vào common
    'content.processing' => 'common.messages.processing',
    'content.error_occurred' => 'common.messages.error_occurred',

    // Coming soon keys - di chuyển vào pages
    'coming_soon.notify_success' => 'pages.coming_soon.notify_success',
    'coming_soon.share_text' => 'pages.coming_soon.share_text',
    'coming_soon.copied' => 'pages.coming_soon.copied',

    // Messages - phân loại theo context
    'messages.forgot_password' => 'auth.login.actions.forgot_password',
    'messages.success' => 'common.messages.success',
    'messages.error' => 'common.messages.error',
    'messages.loading' => 'common.messages.loading',

    // Showcase keys
    'showcase.title' => 'showcase.title',
    'showcase.create' => 'showcase.actions.create',
    'showcase.edit' => 'showcase.actions.edit',

    // User keys
    'user.profile' => 'user.profile.title',
    'user.settings' => 'user.settings.title',
    'user.dashboard' => 'user.dashboard.title',

    // Search keys
    'search.placeholder' => 'search.form.placeholder',
    'search.results' => 'search.results.title',
    'search.no_results' => 'search.results.no_results',
];

// Tìm tất cả file blade (không bao gồm admin)
function findBladeFiles($directory) {
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() &&
            $file->getExtension() === 'php' &&
            strpos($file->getFilename(), '.blade.') !== false) {

            $relativePath = str_replace(__DIR__ . '/../', '', $file->getPathname());

            // Skip admin directories
            if (strpos($relativePath, '/admin/') !== false ||
                strpos($relativePath, 'admin.') !== false) {
                continue;
            }

            $bladeFiles[] = $file->getPathname();
        }
    }

    return $bladeFiles;
}

// Phân tích translation keys trong file
function analyzeTranslationKeys($content) {
    $keys = [];

    // Patterns để tìm translation keys
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
                $keys[] = $key;
            }
        }
    }

    return array_unique($keys);
}

// Sửa translation keys trong content
function fixTranslationKeys($content, $keyMappings) {
    $updatedContent = $content;
    $changesCount = 0;

    foreach ($keyMappings as $oldKey => $newKey) {
        // Escape special regex characters in keys
        $escapedOldKey = preg_quote($oldKey, '/');

        // Patterns để replace
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
            $newContent = preg_replace($pattern, $replacements[$i], $updatedContent);
            if ($newContent !== null && $newContent !== $updatedContent) {
                $updatedContent = $newContent;
                $changesCount++;
            }
        }
    }

    return [$updatedContent, $changesCount];
}

// Main execution
echo "🔍 Scanning blade files...\n";
$bladeFiles = findBladeFiles($viewsPath);
echo "📁 Found " . count($bladeFiles) . " blade files\n\n";

$totalFiles = 0;
$totalChanges = 0;
$processedFiles = [];

foreach ($bladeFiles as $filePath) {
    $relativePath = str_replace($basePath . '/', '', $filePath);

    if (!file_exists($filePath)) {
        continue;
    }

    $content = file_get_contents($filePath);
    $originalKeys = analyzeTranslationKeys($content);

    if (empty($originalKeys)) {
        continue; // Skip files without translation keys
    }

    [$updatedContent, $changesCount] = fixTranslationKeys($content, $keyMappings);

    if ($changesCount > 0) {
        // Backup original file
        $backupPath = $filePath . '.backup.' . date('Y-m-d_H-i-s');
        copy($filePath, $backupPath);

        // Write updated content
        file_put_contents($filePath, $updatedContent);

        $processedFiles[] = [
            'file' => $relativePath,
            'changes' => $changesCount,
            'backup' => str_replace($basePath . '/', '', $backupPath),
            'keys' => $originalKeys
        ];

        $totalChanges += $changesCount;
        echo "✅ {$relativePath}: {$changesCount} changes\n";
    }

    $totalFiles++;
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "Total files scanned: {$totalFiles}\n";
echo "Files modified: " . count($processedFiles) . "\n";
echo "Total changes made: {$totalChanges}\n";

if (!empty($processedFiles)) {
    echo "\n📝 DETAILED CHANGES:\n";
    foreach ($processedFiles as $file) {
        echo "  • {$file['file']}: {$file['changes']} changes\n";
        echo "    Backup: {$file['backup']}\n";
    }
}

echo "\n✅ BLADE TRANSLATION KEY STANDARDIZATION COMPLETE!\n";
