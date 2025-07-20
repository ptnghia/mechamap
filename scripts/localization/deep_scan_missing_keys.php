<?php
/**
 * Deep Scan for Missing Translation Keys
 * Comprehensive scan to find ALL keys that are not loading translations
 */

echo "🔍 DEEP SCAN FOR MISSING TRANSLATION KEYS\n";
echo "=========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📊 SCANNING ALL BLADE FILES FOR TRANSLATION KEYS...\n";
echo "===================================================\n";

// Find all blade files (including admin this time)
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && 
        strpos($file->getFilename(), '.blade.') !== false) {
        $bladeFiles[] = str_replace($basePath . '/', '', $file->getPathname());
    }
}

echo "Found " . count($bladeFiles) . " Blade files to scan\n\n";

$allKeys = [];
$failingKeys = [];
$workingKeys = [];
$totalKeysFound = 0;

foreach ($bladeFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $content = file_get_contents($fullPath);
    
    // Enhanced patterns to catch all translation key formats
    $patterns = [
        // Basic __() patterns
        '/__(\'([^\']+)\')/m',
        '/__\("([^"]+)"\)/m',
        
        // Helper function patterns
        '/t_ui\(\'([^\']+)\'\)/m',
        '/t_ui\("([^"]+)"\)/m',
        '/t_core\(\'([^\']+)\'\)/m',
        '/t_core\("([^"]+)"\)/m',
        '/t_feature\(\'([^\']+)\'\)/m',
        '/t_feature\("([^"]+)"\)/m',
        '/t_content\(\'([^\']+)\'\)/m',
        '/t_content\("([^"]+)"\)/m',
        '/t_user\(\'([^\']+)\'\)/m',
        '/t_user\("([^"]+)"\)/m',
        
        // @lang directive
        '/@lang\(\'([^\']+)\'\)/m',
        '/@lang\("([^"]+)"\)/m',
        
        // Blade directives
        '/@ui\(\'([^\']+)\'\)/m',
        '/@ui\("([^"]+)"\)/m',
        '/@core\(\'([^\']+)\'\)/m',
        '/@core\("([^"]+)"\)/m',
    ];
    
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $content, $matches);
        foreach ($matches[1] as $key) {
            if (empty($key)) continue;
            
            $totalKeysFound++;
            
            if (!isset($allKeys[$key])) {
                $allKeys[$key] = [];
            }
            $allKeys[$key][] = $file;
        }
    }
}

echo "📋 TESTING ALL FOUND KEYS...\n";
echo "============================\n";

$testCount = 0;
foreach ($allKeys as $key => $files) {
    $testCount++;
    if ($testCount % 50 == 0) {
        echo "Tested $testCount keys...\n";
    }
    
    // Test the key
    $result = __($key);
    if ($result === $key) {
        // Key not found - failing
        $failingKeys[$key] = $files;
    } else {
        // Key found - working
        $workingKeys[$key] = $result;
    }
}

echo "\n📊 SCAN RESULTS\n";
echo "===============\n";
echo "Total Blade files scanned: " . count($bladeFiles) . "\n";
echo "Total translation keys found: " . count($allKeys) . "\n";
echo "Working keys: " . count($workingKeys) . "\n";
echo "Failing keys: " . count($failingKeys) . "\n";
echo "Success rate: " . round((count($workingKeys) / count($allKeys)) * 100, 1) . "%\n\n";

if (!empty($failingKeys)) {
    echo "❌ FAILING KEYS (Top 50):\n";
    echo "=========================\n";
    
    $count = 0;
    foreach ($failingKeys as $key => $files) {
        $count++;
        if ($count > 50) {
            echo "... and " . (count($failingKeys) - 50) . " more failing keys\n";
            break;
        }
        
        echo "   $key\n";
        foreach (array_slice($files, 0, 2) as $file) {
            echo "     - $file\n";
        }
        if (count($files) > 2) {
            echo "     - ... and " . (count($files) - 2) . " more files\n";
        }
        echo "\n";
    }
}

// Analyze failing keys by pattern
echo "📋 FAILING KEYS BY PATTERN:\n";
echo "===========================\n";

$patterns = [];
foreach ($failingKeys as $key => $files) {
    if (strpos($key, '.') !== false) {
        $parts = explode('.', $key);
        $pattern = $parts[0];
        if (count($parts) > 1) {
            $pattern .= '.' . $parts[1];
        }
    } else {
        $pattern = 'no-dot';
    }
    
    if (!isset($patterns[$pattern])) {
        $patterns[$pattern] = [];
    }
    $patterns[$pattern][] = $key;
}

arsort($patterns);
foreach ($patterns as $pattern => $keys) {
    echo "   $pattern: " . count($keys) . " keys\n";
    foreach (array_slice($keys, 0, 3) as $key) {
        echo "     - $key\n";
    }
    if (count($keys) > 3) {
        echo "     - ... and " . (count($keys) - 3) . " more\n";
    }
    echo "\n";
}

// Save detailed report
$outputFile = $basePath . '/storage/localization/deep_scan_report.json';
$reportData = [
    'scan_date' => date('Y-m-d H:i:s'),
    'total_files' => count($bladeFiles),
    'total_keys' => count($allKeys),
    'working_keys' => count($workingKeys),
    'failing_keys' => count($failingKeys),
    'success_rate' => round((count($workingKeys) / count($allKeys)) * 100, 1),
    'failing_keys_detail' => $failingKeys,
    'patterns' => $patterns
];

if (!is_dir(dirname($outputFile))) {
    mkdir(dirname($outputFile), 0755, true);
}

file_put_contents($outputFile, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "📄 Detailed report saved to: storage/localization/deep_scan_report.json\n";
echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Analyze the failing key patterns\n";
echo "2. Fix dot notation issues (e.g., nav.* should be nav/*)\n";
echo "3. Add missing translation files/keys\n";
echo "4. Test and validate all fixes\n";
