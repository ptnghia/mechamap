<?php

/**
 * COMPREHENSIVE TRANSLATION COVERAGE ANALYSIS
 * Kiểm tra coverage tiếng Việt và tiếng Anh cho NON-admin blades
 */

echo "=== COMPREHENSIVE TRANSLATION COVERAGE ANALYSIS ===\n\n";

// Load Vietnamese translations
$viTranslations = [];
$viLangDir = __DIR__ . '/resources/lang/vi/';

if (is_dir($viLangDir)) {
    $files = glob($viLangDir . '*.php');
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        try {
            $translations = include $file;
            if (is_array($translations)) {
                $viTranslations[$filename] = $translations;
                echo "✅ VI Loaded: $filename.php\n";
            }
        } catch (Exception $e) {
            echo "❌ VI Error loading $filename.php: " . $e->getMessage() . "\n";
        }
    }
}

// Load English translations
$enTranslations = [];
$enLangDir = __DIR__ . '/resources/lang/en/';

if (is_dir($enLangDir)) {
    $files = glob($enLangDir . '*.php');
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        try {
            $translations = include $file;
            if (is_array($translations)) {
                $enTranslations[$filename] = $translations;
                echo "✅ EN Loaded: $filename.php\n";
            }
        } catch (Exception $e) {
            echo "❌ EN Error loading $filename.php: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n📁 Scanning NON-admin, NON-components blade files...\n\n";

// Find blade files with better exclusion
function scanBladeFilesExcluding($dir, $excludes = []) {
    $files = [];

    if (!is_dir($dir)) {
        return $files;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
            $fullPath = $file->getPathname();
            $relativePath = str_replace($dir, '', $fullPath);
            $relativePath = str_replace('\\', '/', $relativePath);

            // Better exclusion check
            $shouldSkip = false;
            foreach ($excludes as $exclude) {
                if (str_contains($relativePath, $exclude)) {
                    $shouldSkip = true;
                    break;
                }
            }

            if (!$shouldSkip) {
                $files[] = $fullPath;
            }
        }
    }

    return $files;
}

$viewsDir = __DIR__ . '/resources/views/';
$bladeFiles = scanBladeFilesExcluding($viewsDir, ['/admin/', '/components/']);

echo "📊 Tìm thấy " . count($bladeFiles) . " NON-admin/NON-components blade files\n\n";

// Helper functions for translation checking
function hasNestedKey($array, $key) {
    $keys = explode('.', $key);
    $current = $array;

    foreach ($keys as $k) {
        if (!is_array($current) || !isset($current[$k])) {
            return false;
        }
        $current = $current[$k];
    }

    return true;
}

function checkTranslationKey($key, $translationFiles) {
    if (str_contains($key, '.')) {
        $parts = explode('.', $key, 2);
        $file = $parts[0];
        $nestedKey = $parts[1];

        // Special handling for messages.* keys - check in common.messages.*
        if ($file === 'messages') {
            if (isset($translationFiles['common']) && hasNestedKey($translationFiles['common'], 'messages.' . $nestedKey)) {
                return true;
            }
        }

        if (isset($translationFiles[$file])) {
            return hasNestedKey($translationFiles[$file], $nestedKey);
        }
    } else {
        // Direct key - check in all translation files
        foreach ($translationFiles as $fileTranslations) {
            if (isset($fileTranslations[$key])) {
                return true;
            }
        }
    }

    return false;
}

function checkHelperFunction($helperName, $key, $translationFiles) {
    $helperMapping = [
        't_auth' => 'auth', 't_common' => 'common', 't_marketplace' => 'marketplace',
        't_navigation' => 'navigation', 't_nav' => 'nav', 't_setting' => 'setting',
        't_badges' => 'badges', 't_forms' => 'forms', 't_versioned' => 'versioned',
        't_language' => 'language', 't_thread' => 'thread', 't_forums' => 'forums',
        't_forum' => 'forum', 't_ui' => 'ui', 't_sidebar' => 'sidebar',
        't_footer' => 'footer', 't_content' => 'content', 't_notifications' => 'notifications',
        't_user' => 'user', 't_admin' => 'admin', 't_errors' => 'errors',
        't_emails' => 'emails', 't_pages' => 'pages', 't_search' => 'search',
        't_seo' => 'seo', 't_showcase' => 'showcase', 't_moderation' => 'moderation',
        't_validation' => 'validation', 't_homepage' => 'homepage', 't_core' => 'core',
        't_feature' => 'feature', 't_companies' => 'companies', 't_student' => 'student'
    ];

    if (isset($helperMapping[$helperName])) {
        $file = $helperMapping[$helperName];
        if (isset($translationFiles[$file])) {
            return hasNestedKey($translationFiles[$file], $key);
        }
    }

    return false;
}

// Analysis counters
$totalKeys = 0;
$viMissing = 0;
$enMissing = 0;
$bothMissing = 0;
$viOnlyMissing = 0;
$enOnlyMissing = 0;
$bothPresent = 0;

$detailedIssues = [];
$fileStats = [];

// Process each blade file
foreach ($bladeFiles as $filePath) {
    $relativePath = str_replace($viewsDir, '', $filePath);
    $relativePath = str_replace('\\', '/', $relativePath);

    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    $fileKeys = 0;
    $fileViMissing = 0;
    $fileEnMissing = 0;
    $fileBothMissing = 0;

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $key = $match[1];
        $totalKeys++;
        $fileKeys++;

        $viExists = checkTranslationKey($key, $viTranslations);
        $enExists = checkTranslationKey($key, $enTranslations);

        if (!$viExists && !$enExists) {
            $bothMissing++;
            $fileBothMissing++;
            $detailedIssues[] = "❌ BOTH MISSING: __('$key') in $relativePath";
        } elseif (!$viExists) {
            $viOnlyMissing++;
            $fileViMissing++;
            $detailedIssues[] = "🇻🇳 VI MISSING: __('$key') in $relativePath";
        } elseif (!$enExists) {
            $enOnlyMissing++;
            $fileEnMissing++;
            $detailedIssues[] = "🇺🇸 EN MISSING: __('$key') in $relativePath";
        } else {
            $bothPresent++;
        }
    }

    // Find t_xxx() calls
    preg_match_all('/\bt_(\w+)\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $helperName = 't_' . $match[1];
        $key = $match[2];
        $totalKeys++;
        $fileKeys++;

        $viExists = checkHelperFunction($helperName, $key, $viTranslations);
        $enExists = checkHelperFunction($helperName, $key, $enTranslations);

        if (!$viExists && !$enExists) {
            $bothMissing++;
            $fileBothMissing++;
            $detailedIssues[] = "❌ BOTH MISSING: {$helperName}('$key') in $relativePath";
        } elseif (!$viExists) {
            $viOnlyMissing++;
            $fileViMissing++;
            $detailedIssues[] = "🇻🇳 VI MISSING: {$helperName}('$key') in $relativePath";
        } elseif (!$enExists) {
            $enOnlyMissing++;
            $fileEnMissing++;
            $detailedIssues[] = "🇺🇸 EN MISSING: {$helperName}('$key') in $relativePath";
        } else {
            $bothPresent++;
        }
    }

    // Store file statistics
    if ($fileKeys > 0) {
        $fileStats[$relativePath] = [
            'total' => $fileKeys,
            'vi_missing' => $fileViMissing,
            'en_missing' => $fileEnMissing,
            'both_missing' => $fileBothMissing,
            'issues' => $fileViMissing + $fileEnMissing + $fileBothMissing
        ];
    }
}

// Final comprehensive report
echo "\n=== COMPREHENSIVE TRANSLATION COVERAGE REPORT ===\n";
echo "Total translation calls: $totalKeys\n";
echo "✅ Both languages present: $bothPresent (" . round(($bothPresent / $totalKeys) * 100, 1) . "%)\n";
echo "❌ Both languages missing: $bothMissing (" . round(($bothMissing / $totalKeys) * 100, 1) . "%)\n";
echo "🇻🇳 Vietnamese only missing: $viOnlyMissing (" . round(($viOnlyMissing / $totalKeys) * 100, 1) . "%)\n";
echo "🇺🇸 English only missing: $enOnlyMissing (" . round(($enOnlyMissing / $totalKeys) * 100, 1) . "%)\n\n";

$totalMissing = $bothMissing + $viOnlyMissing + $enOnlyMissing;
echo "📊 SUMMARY:\n";
echo "- Total missing translations: $totalMissing (" . round(($totalMissing / $totalKeys) * 100, 1) . "%)\n";
echo "- Vietnamese coverage: " . round((($totalKeys - $bothMissing - $viOnlyMissing) / $totalKeys) * 100, 1) . "%\n";
echo "- English coverage: " . round((($totalKeys - $bothMissing - $enOnlyMissing) / $totalKeys) * 100, 1) . "%\n\n";

// Show TOP 10 most problematic files
if (!empty($fileStats)) {
    echo "=== TOP 10 MOST PROBLEMATIC FILES ===\n\n";
    uasort($fileStats, function($a, $b) {
        return $b['issues'] <=> $a['issues'];
    });
    
    $count = 0;
    foreach ($fileStats as $file => $stats) {
        if ($stats['issues'] > 0) {
            echo "📄 $file ({$stats['issues']} issues)\n";
            echo "   - Total keys: {$stats['total']}\n";
            echo "   - VI missing: {$stats['vi_missing']}\n";
            echo "   - EN missing: {$stats['en_missing']}\n";
            echo "   - Both missing: {$stats['both_missing']}\n\n";
            $count++;
            if ($count >= 10) break;
        }
    }
}

echo "✅ Comprehensive analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
