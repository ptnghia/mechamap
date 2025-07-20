<?php
/**
 * Comprehensive Blade Translation Keys Audit
 * Phân tích toàn diện tất cả translation keys trong các file Blade
 */

echo "🔍 COMPREHENSIVE BLADE TRANSLATION KEYS AUDIT\n";
echo "==============================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📊 SCANNING ALL BLADE FILES...\n";
echo "===============================\n";

// Find all blade files
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

echo "Found " . count($bladeFiles) . " Blade files to analyze\n\n";

// Define all possible translation patterns
$translationPatterns = [
    // Helper functions
    'helper_t_ui' => [
        'pattern' => '/t_ui\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Helper function t_ui()',
        'expected_prefix' => 'ui/',
        'keys' => []
    ],
    'helper_t_core' => [
        'pattern' => '/t_core\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Helper function t_core()',
        'expected_prefix' => 'core/',
        'keys' => []
    ],
    'helper_t_user' => [
        'pattern' => '/t_user\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Helper function t_user()',
        'expected_prefix' => 'user/',
        'keys' => []
    ],
    'helper_t_admin' => [
        'pattern' => '/t_admin\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Helper function t_admin()',
        'expected_prefix' => 'admin/',
        'keys' => []
    ],
    'helper_t_feature' => [
        'pattern' => '/t_feature\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Helper function t_feature()',
        'expected_prefix' => 'features/',
        'keys' => []
    ],
    'helper_t_content' => [
        'pattern' => '/t_content\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Helper function t_content()',
        'expected_prefix' => 'content/',
        'keys' => []
    ],
    
    // Direct translation functions
    'direct_underscore' => [
        'pattern' => '/__\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Direct __() function',
        'expected_prefix' => '',
        'keys' => []
    ],
    'direct_trans' => [
        'pattern' => '/trans\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Direct trans() function',
        'expected_prefix' => '',
        'keys' => []
    ],
    'direct_trans_choice' => [
        'pattern' => '/trans_choice\([\'"]([^\'"]+)[\'"]/',
        'description' => 'Pluralization trans_choice()',
        'expected_prefix' => '',
        'keys' => []
    ],
    
    // Blade directives
    'blade_lang' => [
        'pattern' => '/@lang\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Blade @lang directive',
        'expected_prefix' => '',
        'keys' => []
    ],
    'blade_ui' => [
        'pattern' => '/@ui\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Blade @ui directive',
        'expected_prefix' => 'ui/',
        'keys' => []
    ],
    'blade_core' => [
        'pattern' => '/@core\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Blade @core directive',
        'expected_prefix' => 'core/',
        'keys' => []
    ],
];

$totalKeysFound = 0;
$fileAnalysis = [];

echo "🔍 ANALYZING EACH BLADE FILE...\n";
echo "===============================\n";

foreach ($bladeFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $content = file_get_contents($fullPath);
    
    $fileKeys = [];
    $fileKeyCount = 0;
    
    foreach ($translationPatterns as $patternName => $patternInfo) {
        preg_match_all($patternInfo['pattern'], $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                if (empty($key)) continue;
                
                $fullKey = $patternInfo['expected_prefix'] . $key;
                
                $fileKeys[] = [
                    'type' => $patternName,
                    'key' => $key,
                    'full_key' => $fullKey,
                    'line' => 'TBD' // We'll add line numbers later if needed
                ];
                
                // Add to global pattern tracking
                if (!isset($translationPatterns[$patternName]['keys'][$fullKey])) {
                    $translationPatterns[$patternName]['keys'][$fullKey] = [];
                }
                $translationPatterns[$patternName]['keys'][$fullKey][] = $file;
                
                $fileKeyCount++;
                $totalKeysFound++;
            }
        }
    }
    
    if ($fileKeyCount > 0) {
        $fileAnalysis[$file] = [
            'key_count' => $fileKeyCount,
            'keys' => $fileKeys
        ];
        echo "📄 $file: $fileKeyCount keys\n";
    }
}

echo "\n📊 ANALYSIS SUMMARY\n";
echo "===================\n";
echo "Total files analyzed: " . count($bladeFiles) . "\n";
echo "Files with translation keys: " . count($fileAnalysis) . "\n";
echo "Total translation keys found: $totalKeysFound\n\n";

echo "📋 BREAKDOWN BY PATTERN TYPE\n";
echo "============================\n";

foreach ($translationPatterns as $patternName => $patternInfo) {
    $keyCount = count($patternInfo['keys']);
    if ($keyCount > 0) {
        echo "🔸 {$patternInfo['description']}: $keyCount unique keys\n";
        
        // Show top 5 keys for each pattern
        $keys = array_keys($patternInfo['keys']);
        foreach (array_slice($keys, 0, 5) as $key) {
            $fileCount = count($patternInfo['keys'][$key]);
            echo "   - $key (used in $fileCount file" . ($fileCount > 1 ? 's' : '') . ")\n";
        }
        if (count($keys) > 5) {
            echo "   - ... and " . (count($keys) - 5) . " more keys\n";
        }
        echo "\n";
    }
}

// Save detailed analysis
$outputDir = $basePath . '/storage/localization';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$analysisData = [
    'scan_date' => date('Y-m-d H:i:s'),
    'total_files' => count($bladeFiles),
    'files_with_keys' => count($fileAnalysis),
    'total_keys' => $totalKeysFound,
    'patterns' => $translationPatterns,
    'file_analysis' => $fileAnalysis,
    'blade_files' => $bladeFiles
];

file_put_contents($outputDir . '/comprehensive_blade_audit.json', json_encode($analysisData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "💾 Detailed analysis saved to: storage/localization/comprehensive_blade_audit.json\n\n";

echo "🎯 NEXT STEPS PLAN\n";
echo "==================\n";
echo "1. ✅ Analysis complete - " . $totalKeysFound . " keys found\n";
echo "2. 🔄 Next: Check helper function keys (t_ui, t_core, etc.)\n";
echo "3. 🔄 Next: Check direct translation keys (__, trans, @lang)\n";
echo "4. 🔄 Next: Check special functions (trans_choice, etc.)\n";
echo "5. 🔄 Next: Create missing translation files\n";
echo "6. 🔄 Next: Validate and test all keys\n";
echo "7. 🔄 Next: Generate documentation and cleanup\n\n";

echo "📈 PRIORITY AREAS\n";
echo "=================\n";

// Identify priority areas based on key counts
$priorityPatterns = [];
foreach ($translationPatterns as $patternName => $patternInfo) {
    if (count($patternInfo['keys']) > 0) {
        $priorityPatterns[$patternName] = count($patternInfo['keys']);
    }
}
arsort($priorityPatterns);

foreach ($priorityPatterns as $pattern => $count) {
    $info = $translationPatterns[$pattern];
    echo "🔥 {$info['description']}: $count keys (High Priority)\n";
}

echo "\n🚀 Ready to proceed with systematic key fixing!\n";
