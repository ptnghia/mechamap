<?php
/**
 * Find Missing Translation Keys
 * Scan Blade files for translation keys and check if they exist
 */

echo "🔍 FINDING MISSING TRANSLATION KEYS\n";
echo "===================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$missingKeys = [];
$foundKeys = [];
$totalChecked = 0;

// Scan Blade files for translation keys
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && 
        strpos($file->getFilename(), '.blade.') !== false) {
        $bladeFiles[] = $file->getPathname();
    }
}

echo "📁 Found " . count($bladeFiles) . " Blade files to scan\n\n";

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($basePath . '/', '', $file);
    
    // Find all __() calls
    preg_match_all('/__(\'([^\']+)\'|\\"([^\\"]+)\\")/', $content, $matches);
    
    foreach ($matches[2] as $key) {
        if (empty($key)) continue;
        
        $totalChecked++;
        
        // Test if key exists
        $result = __($key);
        if ($result === $key) {
            // Key not found
            if (!isset($missingKeys[$key])) {
                $missingKeys[$key] = [];
            }
            $missingKeys[$key][] = $relativePath;
        } else {
            // Key found
            $foundKeys[] = $key;
        }
    }
    
    foreach ($matches[3] as $key) {
        if (empty($key)) continue;
        
        $totalChecked++;
        
        // Test if key exists
        $result = __($key);
        if ($result === $key) {
            // Key not found
            if (!isset($missingKeys[$key])) {
                $missingKeys[$key] = [];
            }
            $missingKeys[$key][] = $relativePath;
        } else {
            // Key found
            $foundKeys[] = $key;
        }
    }
}

echo "📊 SCAN RESULTS\n";
echo "===============\n";
echo "Total keys checked: $totalChecked\n";
echo "Found keys: " . count($foundKeys) . "\n";
echo "Missing keys: " . count($missingKeys) . "\n\n";

if (!empty($missingKeys)) {
    echo "❌ MISSING KEYS:\n";
    echo "================\n";
    
    $count = 0;
    foreach ($missingKeys as $key => $files) {
        $count++;
        if ($count > 50) {
            echo "... and " . (count($missingKeys) - 50) . " more missing keys\n";
            break;
        }
        
        echo "   $key\n";
        foreach (array_slice($files, 0, 3) as $file) {
            echo "     - $file\n";
        }
        if (count($files) > 3) {
            echo "     - ... and " . (count($files) - 3) . " more files\n";
        }
        echo "\n";
    }
}

// Group missing keys by category
echo "📋 MISSING KEYS BY CATEGORY:\n";
echo "============================\n";

$keysByCategory = [];
foreach ($missingKeys as $key => $files) {
    $parts = explode('.', $key);
    if (count($parts) >= 2) {
        $category = $parts[0] . '.' . $parts[1];
    } else {
        $category = $parts[0] ?? 'unknown';
    }
    
    if (!isset($keysByCategory[$category])) {
        $keysByCategory[$category] = [];
    }
    $keysByCategory[$category][] = $key;
}

foreach ($keysByCategory as $category => $keys) {
    echo "   $category: " . count($keys) . " missing keys\n";
    foreach (array_slice($keys, 0, 5) as $key) {
        echo "     - $key\n";
    }
    if (count($keys) > 5) {
        echo "     - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

// Check common problematic patterns
echo "🔍 CHECKING COMMON PATTERNS:\n";
echo "============================\n";

$commonPatterns = [
    'UI.COMMON.' => 'Old uppercase format - should be ui/common.',
    'ui.common.' => 'Dot notation - should be ui/common.',
    'nav.' => 'Navigation keys - check if they exist',
    'forum.' => 'Forum keys - check if they exist',
    'marketplace.' => 'Marketplace keys - check if they exist'
];

foreach ($commonPatterns as $pattern => $description) {
    $patternKeys = array_filter(array_keys($missingKeys), function($key) use ($pattern) {
        return strpos($key, $pattern) === 0;
    });
    
    if (!empty($patternKeys)) {
        echo "   Pattern '$pattern': " . count($patternKeys) . " keys ($description)\n";
        foreach (array_slice($patternKeys, 0, 3) as $key) {
            echo "     - $key\n";
        }
        if (count($patternKeys) > 3) {
            echo "     - ... and " . (count($patternKeys) - 3) . " more\n";
        }
        echo "\n";
    }
}

echo "🎯 RECOMMENDATIONS:\n";
echo "===================\n";
echo "1. Fix uppercase keys (UI.COMMON.*) to use proper Laravel format\n";
echo "2. Add missing navigation keys to appropriate translation files\n";
echo "3. Check if keys use correct slash notation (ui/common.key) vs dot notation (ui.common.key)\n";
echo "4. Review and add missing marketplace, forum, and nav keys\n";
echo "5. Consider creating a key migration script for systematic fixes\n";

// Save results to file
$outputFile = $basePath . '/storage/localization/missing_keys_report.json';
$reportData = [
    'scan_date' => date('Y-m-d H:i:s'),
    'total_checked' => $totalChecked,
    'found_count' => count($foundKeys),
    'missing_count' => count($missingKeys),
    'missing_keys' => $missingKeys,
    'keys_by_category' => $keysByCategory
];

if (!is_dir(dirname($outputFile))) {
    mkdir(dirname($outputFile), 0755, true);
}

file_put_contents($outputFile, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n📄 Detailed report saved to: $outputFile\n";
