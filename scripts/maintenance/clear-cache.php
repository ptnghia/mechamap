<?php

/**
 * Laravel Cache Clear Script
 * Clear all Laravel caches to apply translation changes
 * Run: php clear-cache.php
 */

echo "🚀 Starting Laravel Cache Clear Process...\n\n";

$commands = [
    'php artisan cache:clear' => 'Application Cache',
    'php artisan config:clear' => 'Configuration Cache',
    'php artisan route:clear' => 'Route Cache',
    'php artisan view:clear' => 'View Cache',
    'php artisan event:clear' => 'Event Cache',
    'php artisan queue:clear' => 'Queue Cache',
    'php artisan optimize:clear' => 'All Optimization Caches',
    'composer dump-autoload' => 'Autoloader Cache',
];

foreach ($commands as $command => $description) {
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

// Additional steps for translation cache
echo "🌐 Clearing Translation-specific Caches...\n\n";

// Clear translation cache if exists
if (file_exists('bootstrap/cache/lang.php')) {
    unlink('bootstrap/cache/lang.php');
    echo "✅ Removed cached translation file\n";
}

// Clear compiled views that might contain old translations
$viewCachePath = 'storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Cleared compiled view files\n";
}

// Clear session cache (if using file sessions)
$sessionPath = 'storage/framework/sessions';
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
        }
    }
    echo "✅ Cleared session files\n";
}

echo "\n🎉 Cache clearing completed!\n";
echo "💡 Your translation changes should now be fully applied.\n\n";

echo "📝 Summary of changes applied:\n";
echo "   ✅ Navigation & Menu translations standardized\n";
echo "   ✅ Sidebar translations (6 types) standardized\n";
echo "   ✅ Homepage & Partials translations standardized\n";
echo "   ✅ Footer translations standardized\n";
echo "   ✅ Helper functions added: t_footer(), get_copyright_info(), get_social_links()\n";
echo "   ✅ All translation files created (EN/VI)\n\n";

echo "🔍 To verify changes:\n";
echo "   1. Visit your website homepage\n";
echo "   2. Check footer, navigation, and sidebar elements\n";
echo "   3. Switch between EN/VI languages\n";
echo "   4. All text should now use standardized translation functions\n\n";

echo "🚨 If you still see the htmlspecialchars() error:\n";
echo "   1. Make sure you're using PHP 8.0+ for proper array syntax\n";
echo "   2. Check if any custom translation keys are missing\n";
echo "   3. Verify footer.php translation files exist in both EN/VI\n\n";

?>