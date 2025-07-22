<?php

/**
 * Comprehensive Blade Translation Analysis
 * Scan tất cả file .blade ngoài /admin và /components
 * Tìm key không đúng cấu trúc và key chưa có bản dịch
 */

echo "=== COMPREHENSIVE BLADE TRANSLATION ANALYSIS ===\n\n";

// Auto-detect translation files
$translationFiles = [];
$langDir = __DIR__ . '/resources/lang/vi/';

if (is_dir($langDir)) {
    $files = glob($langDir . '*.php');
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        try {
            $translations = include $file;
            if (is_array($translations)) {
                $translationFiles[$filename] = $translations;
                echo "✅ Loaded: $filename.php\n";
            }
        } catch (Exception $e) {
            echo "❌ Error loading $filename.php: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n📁 Scanning blade files (excluding /admin and /components)...\n\n";

// Find all blade files excluding admin and components
$bladeFiles = [];
$viewsDir = __DIR__ . '/resources/views/';

function scanBladeFiles($dir, $excludePaths = []) {
    $files = [];

    if (!is_dir($dir)) {
        return $files;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
            $relativePath = str_replace($dir, '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);

            // Skip excluded paths
            $shouldSkip = false;
            foreach ($excludePaths as $excludePath) {
                if (str_starts_with($relativePath, $excludePath)) {
                    $shouldSkip = true;
                    break;
                }
            }

            if (!$shouldSkip) {
                $files[] = $file->getPathname();
            }
        }
    }

    return $files;
}

$bladeFiles = scanBladeFiles($viewsDir, ['admin/', 'components/']);

echo "📊 Tìm thấy " . count($bladeFiles) . " blade files (excluding admin & components)\n\n";

// Helper function to check if a nested key exists
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

// Helper function to check translation availability
function checkTranslationKey($key, $translationFiles) {
    // For __() calls - direct key lookup
    if (str_contains($key, '.')) {
        $parts = explode('.', $key, 2);
        $file = $parts[0];
        $nestedKey = $parts[1];

        if (isset($translationFiles[$file])) {
            return hasNestedKey($translationFiles[$file], $nestedKey);
        }
    }

    return false;
}

// Helper function to check t_xxx helper availability
function checkHelperFunction($helperName, $key, $translationFiles) {
    // Map helper functions to translation files
    $helperMapping = [
        't_auth' => 'auth',
        't_common' => 'common',
        't_marketplace' => 'marketplace',
        't_navigation' => 'navigation',
        't_nav' => 'nav',
        't_setting' => 'setting',
        't_badges' => 'badges',
        't_forms' => 'forms',
        't_versioned' => 'versioned',
        't_language' => 'language',
        't_thread' => 'thread',
        't_forum' => 'forum',
        't_forums' => 'forums',
        't_ui' => 'ui',
        't_sidebar' => 'sidebar',
        't_footer' => 'footer',
        't_content' => 'content',
        't_notifications' => 'notifications',
        't_user' => 'user',
        't_admin' => 'admin',
        't_errors' => 'errors',
        't_emails' => 'emails',
        't_pages' => 'pages',
        't_search' => 'search',
        't_seo' => 'seo',
        't_showcase' => 'showcase',
        't_moderation' => 'moderation',
        't_validation' => 'validation',
        't_homepage' => 'homepage'
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
$problematicKeys = 0;
$issues = [];
$fileIssues = [];

// Process each blade file
foreach ($bladeFiles as $filePath) {
    $relativePath = str_replace($viewsDir, '', $filePath);
    $relativePath = str_replace('\\', '/', $relativePath);

    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    $fileKeys = 0;
    $fileProblems = [];

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $key = $match[1];
        $totalKeys++;
        $fileKeys++;

        if (!checkTranslationKey($key, $translationFiles)) {
            $problematicKeys++;
            $issues[] = "❌ __('$key') in $relativePath - Key not found";
            $fileProblems[] = "__('$key') - Key not found";
        }
    }

    // Find t_xxx() calls (with word boundary to avoid asset_xxx functions)
    preg_match_all('/\bt_(\w+)\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $helperName = 't_' . $match[1];
        $key = $match[2];
        $totalKeys++;
        $fileKeys++;

        if (!checkHelperFunction($helperName, $key, $translationFiles)) {
            $problematicKeys++;
            $issues[] = "❌ {$helperName}('$key') in $relativePath - Key not found";
            $fileProblems[] = "{$helperName}('$key') - Key not found";
        }
    }

    // Report file summary
    if ($fileKeys > 0) {
        echo "📄 $relativePath: $fileKeys keys";
        if (!empty($fileProblems)) {
            echo " (" . count($fileProblems) . " issues)";
        }
        echo "\n";

        if (!empty($fileProblems)) {
            $fileIssues[$relativePath] = $fileProblems;
        }
    }
}

// Final report
echo "\n=== COMPREHENSIVE SUMMARY REPORT ===\n";
echo "Total translation calls: $totalKeys\n";
echo "Problematic calls: $problematicKeys\n";
$successRate = $totalKeys > 0 ? round((($totalKeys - $problematicKeys) / $totalKeys) * 100, 1) : 100;
echo "Success rate: {$successRate}%\n\n";

// Detailed issues by file
if (!empty($fileIssues)) {
    echo "=== PROBLEMATIC KEYS BY FILE ===\n\n";
    foreach ($fileIssues as $file => $problems) {
        echo "📄 $file (" . count($problems) . " issues):\n";
        foreach ($problems as $problem) {
            echo "  $problem\n";
        }
        echo "\n";
    }
}

// Group missing keys by translation file
$missingByFile = [];
foreach ($issues as $issue) {
    // Extract key and determine target file
    if (preg_match("/(__\('([^']+)'\)|t_(\w+)\('([^']+)'\))/", $issue, $matches)) {
        if (isset($matches[2]) && $matches[2]) {
            // __() call
            $key = $matches[2];
            if (str_contains($key, '.')) {
                $parts = explode('.', $key, 2);
                $file = $parts[0];
                $nestedKey = $parts[1];
                $missingByFile[$file][] = "'$nestedKey' => '$nestedKey', // TODO: Add translation";
            }
        } elseif (isset($matches[3], $matches[4])) {
            // t_xxx() call
            $helperName = 't_' . $matches[3];
            $key = $matches[4];

            $helperMapping = [
                't_auth' => 'auth', 't_common' => 'common', 't_marketplace' => 'marketplace',
                't_navigation' => 'navigation', 't_nav' => 'nav', 't_setting' => 'setting',
                't_badges' => 'badges', 't_forms' => 'forms', 't_versioned' => 'versioned',
                't_language' => 'language', 't_thread' => 'thread', 't_forum' => 'forum',
                't_forums' => 'forums', 't_ui' => 'ui', 't_sidebar' => 'sidebar',
                't_footer' => 'footer', 't_content' => 'content', 't_notifications' => 'notifications',
                't_user' => 'user', 't_admin' => 'admin', 't_errors' => 'errors',
                't_emails' => 'emails', 't_pages' => 'pages', 't_search' => 'search',
                't_seo' => 'seo', 't_showcase' => 'showcase', 't_moderation' => 'moderation',
                't_validation' => 'validation', 't_homepage' => 'homepage'
            ];

            if (isset($helperMapping[$helperName])) {
                $file = $helperMapping[$helperName];
                $missingByFile[$file][] = "'$key' => '$key', // TODO: Add translation";
            }
        }
    }
}

if (!empty($missingByFile)) {
    echo "=== MISSING KEYS BY LANGUAGE FILE ===\n\n";
    foreach ($missingByFile as $file => $keys) {
        $uniqueKeys = array_unique($keys);
        if (!empty($uniqueKeys)) {
            echo "📝 $file.php (" . count($uniqueKeys) . " missing keys):\n";
            foreach ($uniqueKeys as $key) {
                echo "  $key\n";
            }
            echo "\n";
        }
    }
}

if ($problematicKeys === 0) {
    echo "🎉 All translation keys are properly configured!\n";
}

echo "✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
