<?php

/**
 * LIST MISSING TRANSLATION KEYS
 * Liệt kê chi tiết các key còn thiếu bản dịch
 */

echo "=== MISSING TRANSLATION KEYS DETAILED LIST ===\n\n";

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

// Collect all missing keys
$missingKeys = [
    'both_missing' => [],
    'vi_missing' => [],
    'en_missing' => []
];

$allKeys = [];

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
        $fullKey = "__('$key')";
        
        if (!isset($allKeys[$fullKey])) {
            $allKeys[$fullKey] = [];
        }
        $allKeys[$fullKey][] = $relativePath;

        $viExists = checkTranslationKey($key, $viTranslations);
        $enExists = checkTranslationKey($key, $enTranslations);

        if (!$viExists && !$enExists) {
            $missingKeys['both_missing'][$fullKey] = $allKeys[$fullKey];
        } elseif (!$viExists) {
            $missingKeys['vi_missing'][$fullKey] = $allKeys[$fullKey];
        } elseif (!$enExists) {
            $missingKeys['en_missing'][$fullKey] = $allKeys[$fullKey];
        }
    }

    // Find t_xxx() calls
    preg_match_all('/\bt_(\w+)\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $helperName = 't_' . $match[1];
        $key = $match[2];
        $fullKey = "{$helperName}('$key')";
        
        if (!isset($allKeys[$fullKey])) {
            $allKeys[$fullKey] = [];
        }
        $allKeys[$fullKey][] = $relativePath;

        $viExists = checkHelperFunction($helperName, $key, $viTranslations);
        $enExists = checkHelperFunction($helperName, $key, $enTranslations);

        if (!$viExists && !$enExists) {
            $missingKeys['both_missing'][$fullKey] = $allKeys[$fullKey];
        } elseif (!$viExists) {
            $missingKeys['vi_missing'][$fullKey] = $allKeys[$fullKey];
        } elseif (!$enExists) {
            $missingKeys['en_missing'][$fullKey] = $allKeys[$fullKey];
        }
    }
}

// Display results
echo "=== KEYS MISSING IN BOTH LANGUAGES (" . count($missingKeys['both_missing']) . " keys) ===\n\n";
foreach ($missingKeys['both_missing'] as $key => $files) {
    echo "❌ $key\n";
    echo "   Used in: " . implode(', ', array_unique($files)) . "\n\n";
}

echo "\n=== KEYS MISSING IN VIETNAMESE ONLY (" . count($missingKeys['vi_missing']) . " keys) ===\n\n";
foreach ($missingKeys['vi_missing'] as $key => $files) {
    echo "🇻🇳 $key\n";
    echo "   Used in: " . implode(', ', array_unique($files)) . "\n\n";
}

echo "\n=== KEYS MISSING IN ENGLISH ONLY (First 50 of " . count($missingKeys['en_missing']) . " keys) ===\n\n";
$count = 0;
foreach ($missingKeys['en_missing'] as $key => $files) {
    echo "🇺🇸 $key\n";
    echo "   Used in: " . implode(', ', array_unique($files)) . "\n\n";
    $count++;
    if ($count >= 50) {
        echo "... and " . (count($missingKeys['en_missing']) - 50) . " more keys missing in English\n\n";
        break;
    }
}

echo "✅ Missing keys analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
