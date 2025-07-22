<?php

/**
 * Simplified Error Finder
 * Check translation files for array structures that cause htmlspecialchars errors
 * Run: php find-error.php
 */

echo "🔍 Finding Translation Errors...\n";
echo "================================\n\n";

// Check if translation files exist
$translationFiles = [
    'resources/lang/en/navigation.php',
    'resources/lang/vi/navigation.php',
    'resources/lang/en/sidebar.php',
    'resources/lang/vi/sidebar.php',
    'resources/lang/en/homepage.php',
    'resources/lang/vi/homepage.php',
    'resources/lang/en/footer.php',
    'resources/lang/vi/footer.php',
];

echo "📁 Checking Translation Files...\n";
echo "=================================\n";

$errors = [];
$foundFiles = [];

foreach ($translationFiles as $file) {
    if (file_exists($file)) {
        echo "✅ Found: {$file}\n";
        $foundFiles[] = $file;

        // Load and check structure
        $content = include $file;

        if (!is_array($content)) {
            $errors[] = "❌ {$file}: Not returning array";
            continue;
        }

        // Check for nested arrays that should be strings
        $problematicKeys = findArrayValues($content, $file);
        if (!empty($problematicKeys)) {
            $errors = array_merge($errors, $problematicKeys);
        }

    } else {
        echo "❌ Missing: {$file}\n";
        $errors[] = "❌ Missing file: {$file}";
    }
}

echo "\n";

// Function to find array values that should be strings
function findArrayValues($array, $filename, $prefix = '') {
    $problems = [];

    foreach ($array as $key => $value) {
        $fullKey = $prefix ? $prefix . '.' . $key : $key;

        if (is_array($value)) {
            // Check if this array should actually be a string
            if (isset($value['text']) || isset($value['title']) || isset($value['name'])) {
                $problems[] = "❌ {$filename}: '{$fullKey}' is array but should be string";
                $problems[] = "   Contains: " . print_r($value, true);
            } else {
                // Recursively check nested arrays
                $nestedProblems = findArrayValues($value, $filename, $fullKey);
                $problems = array_merge($problems, $nestedProblems);
            }
        }
    }

    return $problems;
}

// Check Blade files for problematic patterns
echo "🔍 Checking Blade Files...\n";
echo "===========================\n";

$bladeFiles = [
    'resources/views/components/footer.blade.php',
    'resources/views/components/header.blade.php',
    'resources/views/home.blade.php',
    'resources/views/components/sidebar.blade.php',
    'resources/views/components/sidebar-professional.blade.php',
    'resources/views/components/sidebar-marketplace.blade.php',
    'resources/views/components/sidebar-showcase.blade.php',
];

foreach ($bladeFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  Not found: {$file}\n";
        continue;
    }

    $content = file_get_contents($file);
    echo "📄 Checking: {$file}\n";

    // Look for problematic patterns
    $patterns = [
        '/\{\{\s*get_copyright_info\(\)\[.*?\]\s*\}\}/' => 'get_copyright_info()[key] usage',
        '/\{\{\s*get_social_links\(\)\[.*?\]\s*\}\}/' => 'get_social_links()[key] usage',
        '/\{\{\s*\$.*?\[.*?\]\s*\}\}/' => 'Variable array access',
        '/\{\{\s*__\([\'"][^\'"]*\[.*?\].*?[\'\"]\)\s*\}\}/' => 'Translation array access',
    ];

    $foundIssues = false;
    foreach ($patterns as $pattern => $description) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $foundIssues = true;
            echo "   ❌ Found {$description}:\n";
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                echo "      Line {$line}: " . trim($match[0]) . "\n";
                $errors[] = "❌ {$file} Line {$line}: " . trim($match[0]);
            }
        }
    }

    if (!$foundIssues) {
        echo "   ✅ No problematic patterns found\n";
    }
    echo "\n";
}

// Check helpers.php for function definitions
echo "🔧 Checking Helper Functions...\n";
echo "===============================\n";

if (file_exists('app/helpers.php')) {
    $helpersContent = file_get_contents('app/helpers.php');

    $requiredFunctions = [
        't_navigation',
        't_sidebar',
        't_homepage',
        't_footer',
        'get_copyright_info',
        'get_social_links',
    ];

    foreach ($requiredFunctions as $function) {
        if (strpos($helpersContent, "function {$function}(") !== false) {
            echo "✅ Found: {$function}()\n";
        } else {
            echo "❌ Missing: {$function}()\n";
            $errors[] = "❌ Missing helper function: {$function}()";
        }
    }
} else {
    echo "❌ Missing: app/helpers.php\n";
    $errors[] = "❌ Missing file: app/helpers.php";
}

echo "\n";

// Summary
echo "📊 SUMMARY\n";
echo "==========\n";

if (empty($errors)) {
    echo "🎉 No errors found! Your translation system should work correctly.\n";
    echo "If you still get htmlspecialchars() error, run:\n";
    echo "composer dump-autoload && php artisan cache:clear\n";
} else {
    echo "🚨 FOUND " . count($errors) . " ISSUES:\n";
    echo "========================\n\n";

    foreach ($errors as $error) {
        echo $error . "\n";
    }

    echo "\n🔧 COMMON FIXES:\n";
    echo "================\n";
    echo "1. Replace {{ get_copyright_info()['text'] }} with {{ t_footer('copyright.all_rights_reserved') }}\n";
    echo "2. Fix translation files that return arrays instead of strings\n";
    echo "3. Run: composer dump-autoload\n";
    echo "4. Run: php artisan cache:clear\n";
}

echo "\n🎯 Next Steps:\n";
echo "==============\n";
echo "1. Fix all issues listed above\n";
echo "2. Run: php refresh-all.php\n";
echo "3. Test your website\n";

?>