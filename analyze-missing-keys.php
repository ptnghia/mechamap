<?php

/**
 * ANALYZE MISSING KEYS
 * Phân tích chi tiết 209 keys thiếu cả 2 ngôn ngữ
 */

echo "=== ANALYZING MISSING KEYS (BOTH LANGUAGES) ===\n\n";

// Load existing translations
$viTranslations = [];
$enTranslations = [];

$languages = ['vi', 'en'];
foreach ($languages as $lang) {
    $langDir = __DIR__ . "/resources/lang/$lang/";
    $files = glob($langDir . '*.php');
    
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        try {
            $content = include $file;
            if (is_array($content)) {
                if ($lang === 'vi') {
                    $viTranslations[$filename] = $content;
                } else {
                    $enTranslations[$filename] = $content;
                }
            }
        } catch (Exception $e) {
            echo "Error loading $file: " . $e->getMessage() . "\n";
        }
    }
}

// Function to flatten array keys
function flattenKeys($array, $prefix = '') {
    $result = [];
    foreach ($array as $key => $value) {
        $newKey = $prefix ? "$prefix.$key" : $key;
        if (is_array($value)) {
            $result = array_merge($result, flattenKeys($value, $newKey));
        } else {
            $result[] = $newKey;
        }
    }
    return $result;
}

// Get all translation keys from both languages
$allViKeys = [];
$allEnKeys = [];

foreach ($viTranslations as $file => $content) {
    $keys = flattenKeys($content);
    foreach ($keys as $key) {
        $allViKeys[] = "$file.$key";
    }
}

foreach ($enTranslations as $file => $content) {
    $keys = flattenKeys($content);
    foreach ($keys as $key) {
        $allEnKeys[] = "$file.$key";
    }
}

// Scan blade files for translation calls
echo "🔍 Scanning blade files for translation calls...\n";

$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        if (strpos($path, '/admin/') === false && strpos($path, '/components/') === false) {
            $bladeFiles[] = $path;
        }
    }
}

echo "📄 Found " . count($bladeFiles) . " blade files to analyze\n\n";

// Extract translation calls
$translationCalls = [];
$patterns = [
    '/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/',
    '/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/',
    '/@lang\([\'"]([^\'"]+)[\'"]\)/',
    '/trans\([\'"]([^\'"]+)[\'"]\)/',
];

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(__DIR__ . '/', '', $file);
    
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $i => $match) {
                if (isset($matches[2])) {
                    // For t_helper functions
                    $key = $matches[1][$i] . '.' . $matches[2][$i];
                } else {
                    // For __ functions
                    $key = $match;
                }
                
                if (!isset($translationCalls[$key])) {
                    $translationCalls[$key] = [];
                }
                $translationCalls[$key][] = $relativePath;
            }
        }
    }
}

echo "🔑 Found " . count($translationCalls) . " unique translation calls\n\n";

// Find missing keys (not in both languages)
$missingKeys = [];

foreach ($translationCalls as $key => $files) {
    $inVi = in_array($key, $allViKeys);
    $inEn = in_array($key, $allEnKeys);
    
    if (!$inVi && !$inEn) {
        $missingKeys[$key] = [
            'files' => $files,
            'vi' => false,
            'en' => false
        ];
    }
}

echo "❌ Found " . count($missingKeys) . " keys missing in both languages\n\n";

// Group by category/file
$groupedMissing = [];
foreach ($missingKeys as $key => $data) {
    $parts = explode('.', $key);
    $category = $parts[0];
    
    if (!isset($groupedMissing[$category])) {
        $groupedMissing[$category] = [];
    }
    
    $groupedMissing[$category][$key] = $data;
}

// Sort by priority (most used files first)
uksort($groupedMissing, function($a, $b) use ($missingKeys) {
    $aCount = 0;
    $bCount = 0;
    
    foreach ($missingKeys as $key => $data) {
        if (strpos($key, $a . '.') === 0) {
            $aCount += count($data['files']);
        }
        if (strpos($key, $b . '.') === 0) {
            $bCount += count($data['files']);
        }
    }
    
    return $bCount - $aCount;
});

// Display results
echo "=== MISSING KEYS BY CATEGORY ===\n\n";

$totalProcessed = 0;
foreach ($groupedMissing as $category => $keys) {
    echo "📁 Category: $category (" . count($keys) . " keys)\n";
    
    $keyCount = 0;
    foreach ($keys as $key => $data) {
        if ($keyCount < 10) { // Show first 10 keys per category
            echo "   🔑 $key\n";
            echo "      📄 Used in: " . implode(', ', array_slice($data['files'], 0, 3));
            if (count($data['files']) > 3) {
                echo " (+" . (count($data['files']) - 3) . " more)";
            }
            echo "\n";
        }
        $keyCount++;
        $totalProcessed++;
    }
    
    if ($keyCount > 10) {
        echo "   ... and " . ($keyCount - 10) . " more keys\n";
    }
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total missing keys: " . count($missingKeys) . "\n";
echo "Categories affected: " . count($groupedMissing) . "\n";
echo "Most problematic categories:\n";

$topCategories = array_slice($groupedMissing, 0, 5, true);
foreach ($topCategories as $category => $keys) {
    echo "  - $category: " . count($keys) . " keys\n";
}

echo "\n✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
