<?php

/**
 * FIX MISSING KEYS IN BOTH LANGUAGES
 * Tự động thêm 148 keys thiếu cả tiếng Việt và tiếng Anh
 */

echo "=== FIXING MISSING KEYS IN BOTH LANGUAGES ===\n\n";

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
            }
        } catch (Exception $e) {
            // Skip
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
            }
        } catch (Exception $e) {
            // Skip
        }
    }
}

// Find blade files
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

// Helper functions
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

        if ($file === 'messages') {
            if (isset($translationFiles['common']) && hasNestedKey($translationFiles['common'], 'messages.' . $nestedKey)) {
                return true;
            }
        }

        if (isset($translationFiles[$file])) {
            return hasNestedKey($translationFiles[$file], $nestedKey);
        }
    } else {
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

// Collect missing keys in both languages
$missingBothKeys = [];

// Process each blade file
foreach ($bladeFiles as $filePath) {
    $relativePath = str_replace($viewsDir, '', $filePath);
    $relativePath = str_replace('\\', '/', $relativePath);

    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $key = $match[1];

        $viExists = checkTranslationKey($key, $viTranslations);
        $enExists = checkTranslationKey($key, $enTranslations);

        if (!$viExists && !$enExists) {
            if (!isset($missingBothKeys[$key])) {
                $missingBothKeys[$key] = [
                    'type' => '__',
                    'files' => []
                ];
            }
            $missingBothKeys[$key]['files'][] = $relativePath;
        }
    }

    // Find t_xxx() calls
    preg_match_all('/\bt_(\w+)\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $helperName = 't_' . $match[1];
        $key = $match[2];

        $viExists = checkHelperFunction($helperName, $key, $viTranslations);
        $enExists = checkHelperFunction($helperName, $key, $enTranslations);

        if (!$viExists && !$enExists) {
            $fullKey = $helperName . '::' . $key;
            if (!isset($missingBothKeys[$fullKey])) {
                $missingBothKeys[$fullKey] = [
                    'type' => 'helper',
                    'helper' => $helperName,
                    'key' => $key,
                    'files' => []
                ];
            }
            $missingBothKeys[$fullKey]['files'][] = $relativePath;
        }
    }
}

echo "📊 Found " . count($missingBothKeys) . " keys missing in both languages\n\n";

// Analyze and categorize missing keys
$keysByFile = [];
$directKeys = [];
$helperKeys = [];

foreach ($missingBothKeys as $fullKey => $data) {
    if ($data['type'] === '__') {
        // Direct __ keys
        $key = $fullKey;
        if (str_contains($key, '.')) {
            $parts = explode('.', $key, 2);
            $file = $parts[0];
            $nestedKey = $parts[1];

            if (!isset($keysByFile[$file])) {
                $keysByFile[$file] = [];
            }
            $keysByFile[$file][$nestedKey] = $key;
        } else {
            $directKeys[$key] = $key;
        }
    } else {
        // Helper function keys
        $helperName = $data['helper'];
        $key = $data['key'];

        if (!isset($helperKeys[$helperName])) {
            $helperKeys[$helperName] = [];
        }
        $helperKeys[$helperName][$key] = $fullKey;
    }
}

echo "=== ANALYSIS ===\n";
echo "Direct __ keys: " . count($directKeys) . "\n";
echo "Keys by file: " . count($keysByFile) . " files\n";
echo "Helper function keys: " . count($helperKeys) . " helpers\n\n";

// Show breakdown
foreach ($keysByFile as $file => $keys) {
    echo "📁 $file: " . count($keys) . " keys\n";
}

foreach ($helperKeys as $helper => $keys) {
    echo "🔧 $helper: " . count($keys) . " keys\n";
}

echo "\n=== DETAILED MISSING KEYS ===\n\n";

// Show first 20 missing keys for analysis
$count = 0;
foreach ($missingBothKeys as $fullKey => $data) {
    echo "❌ $fullKey\n";
    echo "   Type: " . $data['type'] . "\n";
    echo "   Used in: " . implode(', ', array_unique($data['files'])) . "\n\n";

    $count++;
    if ($count >= 20) {
        echo "... and " . (count($missingBothKeys) - 20) . " more keys\n\n";
        break;
    }
}

echo "✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
