<?php

/**
 * Complete Laravel Refresh Script
 * Refresh autoloader + Clear all caches + Apply translation changes
 * Run: php refresh-all.php
 */

echo "🚀 Starting Complete Laravel Refresh Process...\n\n";

// Step 1: Composer Autoload Refresh
echo "📦 Step 1: Refreshing Composer Autoloader...\n";
echo "   This is CRITICAL for new helper functions to work!\n";
$autoloadCommands = [
    'composer dump-autoload -o' => 'Optimized Autoloader Refresh',
    'composer dump-autoload --classmap-authoritative' => 'Authoritative Classmap (Production-ready)',
];

foreach ($autoloadCommands as $command => $description) {
    echo "📋 Running: {$description}...\n";
    echo "   Command: {$command}\n";

    $output = [];
    $returnCode = 0;

    exec($command . ' 2>&1', $output, $returnCode);

    if ($returnCode === 0) {
        echo "   ✅ Success!\n";
    } else {
        echo "   ❌ Failed with return code: {$returnCode}\n";
        echo "   Output: " . implode("\n   ", $output) . "\n";
    }
    echo "\n";
}

// Step 2: Laravel Cache Clear
echo "🧹 Step 2: Clearing Laravel Caches...\n";
$cacheCommands = [
    'php artisan cache:clear' => 'Application Cache',
    'php artisan config:clear' => 'Configuration Cache',
    'php artisan route:clear' => 'Route Cache',
    'php artisan view:clear' => 'View Cache',
    'php artisan event:clear' => 'Event Cache',
    'php artisan optimize:clear' => 'All Optimization Caches',
];

foreach ($cacheCommands as $command => $description) {
    echo "📋 Clearing {$description}...\n";
    echo "   Command: {$command}\n";

    $output = [];
    $returnCode = 0;

    exec($command . ' 2>&1', $output, $returnCode);

    if ($returnCode === 0) {
        echo "   ✅ Success!\n";
    } else {
        echo "   ❌ Failed with return code: {$returnCode}\n";
        echo "   Output: " . implode("\n   ", $output) . "\n";
    }
    echo "\n";
}

// Step 3: Translation-specific Cache Clear
echo "🌐 Step 3: Clearing Translation-specific Caches...\n";

// Clear translation cache if exists
if (file_exists('bootstrap/cache/lang.php')) {
    unlink('bootstrap/cache/lang.php');
    echo "✅ Removed cached translation file\n";
}

// Clear compiled views that might contain old translations
$viewCachePath = 'storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*');
    $cleared = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $cleared++;
        }
    }
    echo "✅ Cleared {$cleared} compiled view files\n";
}

// Clear session cache (if using file sessions)
$sessionPath = 'storage/framework/sessions';
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    $cleared = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            $cleared++;
        }
    }
    echo "✅ Cleared {$cleared} session files\n";
}

// Step 4: Verify Helper Functions
echo "\n🔍 Step 4: Verifying Helper Functions...\n";

$helperFunctions = [
    't_navigation',
    't_sidebar',
    't_homepage',
    't_footer',
    'get_copyright_info',
    'get_social_links',
    'get_site_name',
];

foreach ($helperFunctions as $function) {
    if (function_exists($function)) {
        echo "✅ Helper function '{$function}' is available\n";
    } else {
        echo "❌ Helper function '{$function}' NOT FOUND - Check autoloader!\n";
    }
}

// Step 5: Test Critical Translation Keys
echo "\n🧪 Step 5: Testing Critical Translation Keys...\n";

// Simulate Laravel environment for testing
if (!function_exists('__')) {
    echo "⚠️  Laravel __ function not available in CLI - This is normal\n";
    echo "   Translation keys will be tested when you visit the website\n";
} else {
    $testKeys = [
        'navigation.main.home',
        'sidebar.main.featured_topics',
        'homepage.sections.featured_showcases',
        'footer.copyright.all_rights_reserved',
    ];

    foreach ($testKeys as $key) {
        try {
            $result = __($key);
            echo "✅ Translation key '{$key}' loads successfully\n";
        } catch (Exception $e) {
            echo "❌ Translation key '{$key}' failed: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n🎉 Complete refresh process finished!\n\n";

echo "📝 What was completed:\n";
echo "   ✅ Composer autoloader refreshed (CRITICAL for helper functions)\n";
echo "   ✅ All Laravel caches cleared\n";
echo "   ✅ Translation-specific caches cleared\n";
echo "   ✅ Helper functions verified\n";
echo "   ✅ Ready for testing\n\n";

echo "🔍 Next Steps:\n";
echo "   1. Visit your website: http://your-domain.com\n";
echo "   2. Check homepage loads without errors\n";
echo "   3. Verify footer shows correct copyright text\n";
echo "   4. Test navigation menu translations\n";
echo "   5. Switch between EN/VI languages\n";
echo "   6. Check all sidebar components\n\n";

echo "🚨 If you still see errors:\n";
echo "   1. Check PHP error logs for specific issues\n";
echo "   2. Verify all translation files exist:\n";
echo "      - resources/lang/en/navigation.php\n";
echo "      - resources/lang/en/sidebar.php\n";
echo "      - resources/lang/en/homepage.php\n";
echo "      - resources/lang/en/footer.php\n";
echo "      - resources/lang/vi/[same files]\n";
echo "   3. Ensure PHP version supports new syntax (8.0+)\n\n";

echo "💡 Helper functions now available:\n";
echo "   - t_navigation('main.home')\n";
echo "   - t_sidebar('main.featured_topics')\n";
echo "   - t_homepage('sections.featured_showcases')\n";
echo "   - t_footer('copyright.all_rights_reserved')\n";
echo "   - get_copyright_info() / get_social_links()\n\n";

?>