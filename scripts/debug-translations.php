<?php

/**
 * Laravel Translation Debug Tool
 * Find which translation keys are returning arrays instead of strings
 * Run: php debug-translations.php
 */

// Simulate Laravel environment
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Laravel Translation Debug Tool\n";
echo "=================================\n\n";

// Get current locale
$currentLocale = app()->getLocale();
echo "📍 Current Locale: {$currentLocale}\n\n";

// Test all our new translation files
$translationFiles = [
    'navigation' => [
        'main.home',
        'main.forums',
        'main.showcase',
        'main.marketplace',
        'main.community',
        'mega_menu.forums.title',
        'mega_menu.showcase.title',
        'mega_menu.marketplace.title',
    ],
    'sidebar' => [
        'main.featured_topics',
        'main.latest_discussions',
        'main.quick_links',
        'showcase.featured_projects',
        'marketplace.featured_products',
    ],
    'homepage' => [
        'sections.featured_showcases',
        'sections.featured_showcases_desc',
        'hero.title',
        'stats.total_members',
    ],
    'footer' => [
        'copyright.all_rights_reserved',
        'social.facebook',
        'tools.toggle_theme',
        'tools.dark_mode',
        'accessibility.toggle_navigation',
    ],
];

echo "🧪 Testing Translation Keys...\n";
echo "===============================\n\n";

$errorCount = 0;
$successCount = 0;

foreach ($translationFiles as $fileKey => $keys) {
    echo "📂 Testing {$fileKey}.php:\n";
    echo str_repeat('-', 30) . "\n";

    foreach ($keys as $key) {
        $fullKey = "{$fileKey}.{$key}";

        try {
            // Test the translation
            $result = __($fullKey);

            // Check if result is array
            if (is_array($result)) {
                echo "❌ ARRAY ERROR: '{$fullKey}' returns array:\n";
                echo "   " . print_r($result, true) . "\n";
                $errorCount++;
            } elseif (is_string($result)) {
                echo "✅ OK: '{$fullKey}' = '{$result}'\n";
                $successCount++;
            } else {
                echo "⚠️  UNKNOWN: '{$fullKey}' returns " . gettype($result) . "\n";
                var_dump($result);
                $errorCount++;
            }

        } catch (Exception $e) {
            echo "💥 EXCEPTION: '{$fullKey}' - " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
    echo "\n";
}

// Test helper functions
echo "🔧 Testing Helper Functions...\n";
echo "===============================\n\n";

$helperTests = [
    't_navigation' => ['main.home'],
    't_sidebar' => ['main.featured_topics'],
    't_homepage' => ['sections.featured_showcases'],
    't_footer' => ['copyright.all_rights_reserved'],
];

foreach ($helperTests as $function => $testKeys) {
    echo "🔍 Testing {$function}():\n";

    if (!function_exists($function)) {
        echo "❌ Function {$function}() NOT FOUND!\n";
        echo "   Run: composer dump-autoload\n\n";
        $errorCount++;
        continue;
    }

    foreach ($testKeys as $key) {
        try {
            $result = $function($key);

            if (is_array($result)) {
                echo "❌ ARRAY ERROR: {$function}('{$key}') returns array:\n";
                echo "   " . print_r($result, true) . "\n";
                $errorCount++;
            } elseif (is_string($result)) {
                echo "✅ OK: {$function}('{$key}') = '{$result}'\n";
                $successCount++;
            } else {
                echo "⚠️  UNKNOWN: {$function}('{$key}') returns " . gettype($result) . "\n";
                var_dump($result);
                $errorCount++;
            }
        } catch (Exception $e) {
            echo "💥 EXCEPTION: {$function}('{$key}') - " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
    echo "\n";
}

// Test problematic functions
echo "🚨 Testing Potentially Problematic Functions...\n";
echo "================================================\n\n";

$problematicTests = [
    'get_copyright_info' => 'Should return array with text key',
    'get_social_links' => 'Should return array of social links',
    'get_site_name' => 'Should return string',
];

foreach ($problematicTests as $function => $description) {
    echo "🔍 Testing {$function}(): {$description}\n";

    if (!function_exists($function)) {
        echo "❌ Function {$function}() NOT FOUND!\n\n";
        $errorCount++;
        continue;
    }

    try {
        $result = $function();
        echo "   Type: " . gettype($result) . "\n";

        if (is_array($result)) {
            echo "   Array keys: " . implode(', ', array_keys($result)) . "\n";
            echo "   Values: " . print_r($result, true) . "\n";
        } else {
            echo "   Value: '{$result}'\n";
        }

        $successCount++;
    } catch (Exception $e) {
        echo "💥 EXCEPTION: {$function}() - " . $e->getMessage() . "\n";
        $errorCount++;
    }
    echo "\n";
}

// Summary
echo "📊 SUMMARY\n";
echo "==========\n";
echo "✅ Successful tests: {$successCount}\n";
echo "❌ Failed tests: {$errorCount}\n\n";

if ($errorCount > 0) {
    echo "🚨 ISSUES FOUND!\n";
    echo "================\n";
    echo "Common causes:\n";
    echo "1. Translation key returns nested array instead of string\n";
    echo "2. Helper function not loaded (run: composer dump-autoload)\n";
    echo "3. Translation file has wrong structure\n";
    echo "4. Missing translation key\n\n";

    echo "🔧 HOW TO FIX:\n";
    echo "==============\n";
    echo "1. Check the translation files for array keys that should be strings\n";
    echo "2. Look for {{ get_copyright_info()['text'] }} - this is problematic!\n";
    echo "3. Replace with {{ t_footer('copyright.all_rights_reserved') }}\n";
    echo "4. Run: composer dump-autoload && php artisan cache:clear\n\n";

} else {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "====================\n";
    echo "Your translation system is working correctly!\n\n";
}

// Check for blade files that might have problematic syntax
echo "🔍 Scanning Blade Files for Problematic Patterns...\n";
echo "====================================================\n\n";

$bladeFiles = [
    'resources/views/components/footer.blade.php',
    'resources/views/components/header.blade.php',
    'resources/views/home.blade.php',
    'resources/views/components/sidebar.blade.php',
];

$problematicPatterns = [
    '/\{\{\s*get_copyright_info\(\)\[.*?\]\s*\}\}/' => 'get_copyright_info()[...] usage',
    '/\{\{\s*get_social_links\(\)\[.*?\]\s*\}\}/' => 'get_social_links()[...] usage',
    '/\{\{\s*__\([\'"].*?\[.*?\].*?[\'\"]\)\s*\}\}/' => 'Translation array access',
];

foreach ($bladeFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: {$file}\n";
        continue;
    }

    $content = file_get_contents($file);
    echo "📄 Checking {$file}:\n";

    $foundIssues = false;
    foreach ($problematicPatterns as $pattern => $description) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $foundIssues = true;
            echo "   ❌ Found {$description}:\n";
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                echo "      Line {$line}: " . trim($match[0]) . "\n";
            }
        }
    }

    if (!$foundIssues) {
        echo "   ✅ No problematic patterns found\n";
    }
    echo "\n";
}

echo "🎯 Debug completed! Check the output above for specific issues.\n";

?>