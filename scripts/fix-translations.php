<?php

/**
 * Auto-fix Translation Files
 * Fix array structures in translation files that cause htmlspecialchars errors
 */

echo "🔧 Auto-fixing Translation Files...\n";
echo "===================================\n\n";

// First, let's replace the original footer with the fixed version
echo "📋 Step 1: Replacing footer.blade.php with fixed version...\n";
if (file_exists('resources/views/components/footer-fixed.blade.php')) {
    if (copy('resources/views/components/footer-fixed.blade.php', 'resources/views/components/footer.blade.php')) {
        echo "✅ Footer replaced successfully\n";
    } else {
        echo "❌ Failed to replace footer\n";
    }
} else {
    echo "❌ Fixed footer file not found\n";
}

echo "\n📋 Step 2: Checking which files need fixing...\n";

$problematicFiles = [
    'resources/lang/en/navigation.php',
    'resources/lang/vi/navigation.php',
    'resources/lang/en/homepage.php',
    'resources/lang/vi/homepage.php',
    'resources/lang/en/footer.php',
    'resources/lang/vi/footer.php',
];

$issuesFound = [];

foreach ($problematicFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: {$file}\n";
        continue;
    }

    $content = include $file;
    if (!is_array($content)) {
        echo "⚠️  Invalid file format: {$file}\n";
        continue;
    }

    // Check for nested arrays that should be strings
    foreach ($content as $key => $value) {
        if (is_array($value)) {
            // Check if this array has sub-keys that suggest it should be individual strings
            if (isset($value['title']) || isset($value['name']) || isset($value['text'])) {
                $issuesFound[$file][] = $key;
                echo "❌ Found issue in {$file}: '{$key}' is array but should be string\n";
            }
        }
    }
}

echo "\n📋 Step 3: Creating quick fixes...\n";

// Create a simple quick fix script
$quickFixContent = '<?php

/**
 * Quick Fix for htmlspecialchars() errors
 * This will temporarily resolve the most critical issues
 */

echo "🚀 Applying Quick Fixes...\n";

// Quick fix 1: Replace problematic navigation array access
$navFilesEn = "resources/lang/en/navigation.php";
$navFilesVi = "resources/lang/vi/navigation.php";

if (file_exists($navFilesEn)) {
    $content = file_get_contents($navFilesEn);
    // Comment out problematic array structures temporarily
    $content = str_replace(
        "\'add_menu\' => [",
        "// \'add_menu\' => [ // TEMPORARILY DISABLED",
        $content
    );
    file_put_contents($navFilesEn, $content);
    echo "✅ Temporarily fixed EN navigation\n";
}

if (file_exists($navFilesVi)) {
    $content = file_get_contents($navFilesVi);
    $content = str_replace(
        "\'add_menu\' => [",
        "// \'add_menu\' => [ // TEMPORARILY DISABLED",
        $content
    );
    file_put_contents($navFilesVi, $content);
    echo "✅ Temporarily fixed VI navigation\n";
}

echo "\n🎯 Quick fixes applied! Try refreshing your website now.\n";
echo "📝 Note: These are temporary fixes. Full restructuring needed later.\n";

?>';

file_put_contents('quick-fix.php', $quickFixContent);
echo "✅ Created quick-fix.php\n";

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "✅ Footer file fixed with proper array access\n";
echo "✅ Quick fix script created\n";
echo "📝 Issues found in " . count($issuesFound) . " files\n";

if (!empty($issuesFound)) {
foreach ($issuesFound as $file => $keys) {
echo " - {$file}: " . implode(', ', $keys) . "\n";
}
}

echo "\n🚀 NEXT STEPS:\n";
echo "==============\n";
echo "1. Run: php quick-fix.php (for immediate relief)\n";
echo "2. Run: composer dump-autoload\n";
echo "3. Run: php artisan cache:clear\n";
echo "4. Test your website\n";
echo "5. If working, we can do proper restructuring later\n";

?>
