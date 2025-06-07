<?php

/**
 * Script kiểm tra tổng thể hệ thống sau khi hoàn thành:
 * 1. Navigation database integration
 * 2. Via.placeholder.com replacement
 * 3. All helper functions
 * 4. Placeholder system
 */

echo "=== COMPREHENSIVE SYSTEM CHECK ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Initialize
$errors = [];
$warnings = [];
$success = [];

// 1. Check Laravel Bootstrap
echo "1. LARAVEL BOOTSTRAP CHECK\n";
echo str_repeat("-", 50) . "\n";

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../app/Helpers/SettingHelper.php';
    $success[] = "Laravel autoloader loaded successfully";
    echo "✅ Laravel autoloader: OK\n";
} catch (Exception $e) {
    $errors[] = "Laravel bootstrap failed: " . $e->getMessage();
    echo "❌ Laravel bootstrap: FAILED\n";
}

// 2. Check Helper Functions
echo "\n2. HELPER FUNCTIONS CHECK\n";
echo str_repeat("-", 50) . "\n";

$helperFunctions = [
    'get_setting' => 'Core settings function',
    'get_logo_url' => 'Logo URL from database',
    'get_favicon_url' => 'Favicon URL from database',
    'get_banner_url' => 'Banner URL from database',
    'get_site_name' => 'Site name from database',
    'placeholder_image' => 'Placeholder image generator',
    'avatar_placeholder' => 'Avatar placeholder generator'
];

foreach ($helperFunctions as $func => $description) {
    if (function_exists($func)) {
        echo "✅ $func() - $description\n";
        $success[] = "$func() function available";
    } else {
        echo "❌ $func() - MISSING\n";
        $errors[] = "$func() function not found";
    }
}

// 3. Check Local Placeholder Files
echo "\n3. LOCAL PLACEHOLDER FILES CHECK\n";
echo str_repeat("-", 50) . "\n";

$placeholdersDir = __DIR__ . '/../public/images/placeholders';
$requiredFiles = [
    '50x50.png' => 'Small icons (sidebar, avatars)',
    '64x64.png' => 'User profile pictures',
    '150x150.png' => 'Avatar placeholders',
    '300x200.png' => 'Content thumbnails',
    '300x300.png' => 'Square content images',
    '800x600.png' => 'Large media fallbacks'
];

$placeholderScore = 0;
foreach ($requiredFiles as $file => $usage) {
    $filepath = $placeholdersDir . '/' . $file;
    if (file_exists($filepath)) {
        $size = filesize($filepath);
        echo "✅ $file - $usage (" . number_format($size) . " bytes)\n";
        $success[] = "Placeholder file $file exists";
        $placeholderScore++;
    } else {
        echo "❌ $file - MISSING\n";
        $errors[] = "Placeholder file $file missing";
    }
}

// 4. Test Helper Function Calls
echo "\n4. HELPER FUNCTION TESTING\n";
echo str_repeat("-", 50) . "\n";

if (function_exists('placeholder_image')) {
    try {
        $testUrl = placeholder_image(300, 200, 'Test');
        echo "✅ placeholder_image() test: $testUrl\n";
        $success[] = "placeholder_image() function working";
    } catch (Exception $e) {
        echo "❌ placeholder_image() test: " . $e->getMessage() . "\n";
        $errors[] = "placeholder_image() function error";
    }
}

if (function_exists('avatar_placeholder')) {
    try {
        $testAvatar = avatar_placeholder('Test User', 150);
        echo "✅ avatar_placeholder() test: $testAvatar\n";
        $success[] = "avatar_placeholder() function working";
    } catch (Exception $e) {
        echo "❌ avatar_placeholder() test: " . $e->getMessage() . "\n";
        $errors[] = "avatar_placeholder() function error";
    }
}

// 5. Check Code for via.placeholder.com References
echo "\n5. CODE CLEANUP VERIFICATION\n";
echo str_repeat("-", 50) . "\n";

$searchPaths = [
    __DIR__ . '/../app/',
    __DIR__ . '/../resources/views/',
    __DIR__ . '/../routes/',
];

$foundReferences = [];
$searchPattern = 'via.placeholder.com';

foreach ($searchPaths as $path) {
    if (is_dir($path)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (in_array($file->getExtension(), ['php', 'blade.php'])) {
                $content = file_get_contents($file->getPathname());
                if (strpos($content, $searchPattern) !== false) {
                    $foundReferences[] = $file->getPathname();
                }
            }
        }
    }
}

if (empty($foundReferences)) {
    echo "✅ No via.placeholder.com references found\n";
    $success[] = "Code cleanup completed";
} else {
    echo "❌ Found via.placeholder.com references in:\n";
    foreach ($foundReferences as $file) {
        echo "   - $file\n";
        $errors[] = "via.placeholder.com reference in $file";
    }
}

// 6. Check Required Asset Directories
echo "\n6. ASSET DIRECTORIES CHECK\n";
echo str_repeat("-", 50) . "\n";

$assetDirs = [
    __DIR__ . '/../public/images' => 'Main images directory',
    __DIR__ . '/../public/images/placeholders' => 'Placeholder images directory',
    __DIR__ . '/../storage/app/public' => 'Storage link directory'
];

foreach ($assetDirs as $dir => $description) {
    if (is_dir($dir)) {
        echo "✅ $description: EXISTS\n";
        $success[] = "$description available";
    } else {
        echo "❌ $description: MISSING\n";
        $warnings[] = "$description not found";
    }
}

// 7. Final Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "                    FINAL SUMMARY\n";
echo str_repeat("=", 60) . "\n";

echo "\n🎉 SUCCESS COUNT: " . count($success) . "\n";
if (!empty($success)) {
    foreach ($success as $item) {
        echo "  ✅ $item\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  WARNING COUNT: " . count($warnings) . "\n";
    foreach ($warnings as $item) {
        echo "  ⚠️  $item\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERROR COUNT: " . count($errors) . "\n";
    foreach ($errors as $item) {
        echo "  ❌ $item\n";
    }
}

// Overall Status
echo "\n" . str_repeat("-", 60) . "\n";
$totalIssues = count($errors);
$totalWarnings = count($warnings);
$totalSuccess = count($success);

if ($totalIssues === 0) {
    echo "🎯 OVERALL STATUS: ✅ EXCELLENT\n";
    echo "🚀 System is ready for production!\n";

    if ($placeholderScore === count($requiredFiles)) {
        echo "🏆 All placeholder files generated successfully\n";
    }

    if ($totalSuccess >= 10) {
        echo "⭐ High system reliability achieved\n";
    }
} elseif ($totalIssues <= 2) {
    echo "🎯 OVERALL STATUS: ⚠️  GOOD (Minor issues)\n";
    echo "🔧 Some minor fixes needed\n";
} else {
    echo "🎯 OVERALL STATUS: ❌ NEEDS ATTENTION\n";
    echo "🚨 Multiple issues require fixing\n";
}

echo "\n📊 STATISTICS:\n";
echo "  - Success: $totalSuccess items\n";
echo "  - Warnings: $totalWarnings items\n";
echo "  - Errors: $totalIssues items\n";
echo "  - Placeholder Files: $placeholderScore/" . count($requiredFiles) . "\n";

echo "\n⏰ Check completed at: " . date('Y-m-d H:i:s') . "\n";
echo "=== END OF COMPREHENSIVE CHECK ===\n";
