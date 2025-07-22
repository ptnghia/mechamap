<?php

/**
 * ANALYZE KEYS BY BLADE FILE
 * Thống kê translation keys theo từng file blade để xử lý có hệ thống
 */

echo "=== ANALYZING TRANSLATION KEYS BY BLADE FILE ===\n\n";

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
        $relativePath = str_replace(__DIR__ . '/', '', $path);
        
        // Skip admin and component files for now
        if (strpos($relativePath, '/admin/') === false && 
            strpos($relativePath, '/components/') === false &&
            strpos($relativePath, '/emails/') === false) {
            $bladeFiles[] = $relativePath;
        }
    }
}

echo "📄 Found " . count($bladeFiles) . " blade files to analyze\n\n";

// Extract translation calls per file
$fileAnalysis = [];
$patterns = [
    '/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => 'direct',
    '/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => 'helper',
    '/@lang\([\'"]([^\'"]+)[\'"]\)/' => 'directive',
    '/trans\([\'"]([^\'"]+)[\'"]\)/' => 'function',
    '/\{\{\s*trans\([\'"]([^\'"]+)[\'"]\)\s*\}\}/' => 'function_blade',
];

foreach ($bladeFiles as $file) {
    $content = file_get_contents(__DIR__ . '/' . $file);
    $fileAnalysis[$file] = [
        'total_keys' => 0,
        'missing_keys' => [],
        'existing_keys' => [],
        'hardcoded_text' => []
    ];
    
    foreach ($patterns as $pattern => $type) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $i => $match) {
                if ($type === 'helper' && isset($matches[2])) {
                    // For t_helper functions
                    $key = $matches[1][$i] . '.' . $matches[2][$i];
                } else {
                    // For __ functions and others
                    $key = $match;
                }
                
                $fileAnalysis[$file]['total_keys']++;
                
                // Check if key exists in both languages
                $inVi = in_array($key, $allViKeys);
                $inEn = in_array($key, $allEnKeys);
                
                if (!$inVi && !$inEn) {
                    $fileAnalysis[$file]['missing_keys'][] = $key;
                } else {
                    $fileAnalysis[$file]['existing_keys'][] = $key;
                }
            }
        }
    }
    
    // Look for hardcoded Vietnamese text
    $vietnamesePattern = '/\{\{\s*[\'"]([^\'\"]*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^\'\"]*)[\'\"]\s*\}\}/u';
    if (preg_match_all($vietnamesePattern, $content, $matches)) {
        $fileAnalysis[$file]['hardcoded_text'] = array_unique($matches[1]);
    }
    
    // Remove duplicates
    $fileAnalysis[$file]['missing_keys'] = array_unique($fileAnalysis[$file]['missing_keys']);
    $fileAnalysis[$file]['existing_keys'] = array_unique($fileAnalysis[$file]['existing_keys']);
}

// Sort files by number of missing keys (highest first)
uasort($fileAnalysis, function($a, $b) {
    return count($b['missing_keys']) - count($a['missing_keys']);
});

// Display results
echo "=== ANALYSIS RESULTS BY FILE ===\n\n";

$totalMissingKeys = 0;
$totalHardcodedText = 0;
$filesWithIssues = 0;

foreach ($fileAnalysis as $file => $analysis) {
    $missingCount = count($analysis['missing_keys']);
    $existingCount = count($analysis['existing_keys']);
    $hardcodedCount = count($analysis['hardcoded_text']);
    
    if ($missingCount > 0 || $hardcodedCount > 0) {
        $filesWithIssues++;
        $totalMissingKeys += $missingCount;
        $totalHardcodedText += $hardcodedCount;
        
        echo "📄 File: $file\n";
        echo "   📊 Total keys: {$analysis['total_keys']}\n";
        echo "   ✅ Existing keys: $existingCount\n";
        echo "   ❌ Missing keys: $missingCount\n";
        echo "   🔤 Hardcoded text: $hardcodedCount\n";
        
        if ($missingCount > 0) {
            echo "   🔑 Missing keys:\n";
            foreach (array_slice($analysis['missing_keys'], 0, 5) as $key) {
                echo "      - $key\n";
            }
            if ($missingCount > 5) {
                echo "      ... and " . ($missingCount - 5) . " more\n";
            }
        }
        
        if ($hardcodedCount > 0) {
            echo "   🔤 Hardcoded Vietnamese text:\n";
            foreach (array_slice($analysis['hardcoded_text'], 0, 3) as $text) {
                echo "      - \"$text\"\n";
            }
            if ($hardcodedCount > 3) {
                echo "      ... and " . ($hardcodedCount - 3) . " more\n";
            }
        }
        
        echo "\n";
    }
}

// Summary
echo "=== SUMMARY ===\n";
echo "Total files analyzed: " . count($bladeFiles) . "\n";
echo "Files with issues: $filesWithIssues\n";
echo "Total missing keys: $totalMissingKeys\n";
echo "Total hardcoded text: $totalHardcodedText\n";

// Top priority files to fix
echo "\n🎯 TOP PRIORITY FILES TO FIX:\n";
$topFiles = array_slice($fileAnalysis, 0, 10, true);
$priority = 1;

foreach ($topFiles as $file => $analysis) {
    $missingCount = count($analysis['missing_keys']);
    $hardcodedCount = count($analysis['hardcoded_text']);
    
    if ($missingCount > 0 || $hardcodedCount > 0) {
        echo "$priority. $file\n";
        echo "   Missing: $missingCount keys, Hardcoded: $hardcodedCount texts\n";
        $priority++;
        
        if ($priority > 10) break;
    }
}

echo "\n✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
