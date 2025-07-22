<?php

/**
 * NON-ADMIN & NON-COMPONENTS Blade Translation Analysis
 * Chỉ scan file .blade ngoài /admin và /components
 */

echo "=== NON-ADMIN/NON-COMPONENTS BLADE ANALYSIS ===\n\n";

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

echo "\n📁 Scanning NON-admin, NON-components blade files...\n\n";

// Find blade files with better exclusion
$bladeFiles = [];
$viewsDir = __DIR__ . '/resources/views/';

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

    // Find t_xxx() calls with word boundary
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
echo "\n=== ANALYSIS SUMMARY (NON-ADMIN/NON-COMPONENTS) ===\n";
echo "Total translation calls: $totalKeys\n";
echo "Problematic calls: $problematicKeys\n";
$successRate = $totalKeys > 0 ? round((($totalKeys - $problematicKeys) / $totalKeys) * 100, 1) : 100;
echo "Success rate: {$successRate}%\n\n";

// Show TOP 10 problematic files
if (!empty($fileIssues)) {
    echo "=== TOP 10 MOST PROBLEMATIC FILES ===\n\n";
    arsort($fileIssues);
    $count = 0;
    foreach ($fileIssues as $file => $problems) {
        echo "📄 $file (" . count($problems) . " issues)\n";
        $count++;
        if ($count >= 10) break;
    }
}

echo "\n✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
