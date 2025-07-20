<?php
/**
 * Comprehensive Blade File Analysis for Translation Key Fixes
 * Systematically identify all .blade.php files and their translation key patterns
 */

echo "🔍 COMPREHENSIVE BLADE FILE ANALYSIS\n";
echo "====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$viewsPath = $basePath . '/resources/views';

// Find all blade files excluding admin directories
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && 
        strpos($file->getFilename(), '.blade.') !== false) {
        
        $relativePath = str_replace($basePath . '/', '', $file->getPathname());
        
        // Skip admin directories
        if (strpos($relativePath, '/admin/') !== false || 
            strpos($relativePath, 'admin.') !== false) {
            continue;
        }
        
        $bladeFiles[] = $relativePath;
    }
}

sort($bladeFiles);

echo "📁 Found " . count($bladeFiles) . " Blade files (excluding admin)\n\n";

// Analyze translation key patterns in each file
$translationPatterns = [];
$fileAnalysis = [];
$totalKeys = 0;
$problematicKeys = 0;

foreach ($bladeFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $content = file_get_contents($fullPath);
    
    // Find all translation keys
    $patterns = [
        '/__(\'([^\']+)\')/m',
        '/__\("([^"]+)"\)/m'
    ];
    
    $fileKeys = [];
    $fileProblematicKeys = [];
    
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $content, $matches);
        foreach ($matches[1] as $key) {
            if (empty($key)) continue;
            
            $fileKeys[] = $key;
            $totalKeys++;
            
            // Check if key uses problematic dot notation for nested directories
            if (preg_match('/^(form|forms|content|core|features|user|ui)\./', $key)) {
                $fileProblematicKeys[] = $key;
                $problematicKeys++;
                
                // Track patterns
                $category = explode('.', $key)[0];
                if (!isset($translationPatterns[$category])) {
                    $translationPatterns[$category] = [];
                }
                if (!in_array($key, $translationPatterns[$category])) {
                    $translationPatterns[$category][] = $key;
                }
            }
        }
    }
    
    if (!empty($fileKeys)) {
        $fileAnalysis[$file] = [
            'total_keys' => count($fileKeys),
            'problematic_keys' => count($fileProblematicKeys),
            'keys' => array_unique($fileKeys),
            'problematic' => array_unique($fileProblematicKeys)
        ];
    }
}

echo "📊 ANALYSIS RESULTS\n";
echo "===================\n";
echo "Total Blade files: " . count($bladeFiles) . "\n";
echo "Files with translation keys: " . count($fileAnalysis) . "\n";
echo "Total translation keys found: $totalKeys\n";
echo "Problematic keys (dot notation): $problematicKeys\n\n";

echo "📋 PROBLEMATIC PATTERNS BY CATEGORY\n";
echo "===================================\n";
foreach ($translationPatterns as $category => $keys) {
    echo "$category: " . count($keys) . " problematic keys\n";
    foreach (array_slice($keys, 0, 5) as $key) {
        echo "  - $key\n";
    }
    if (count($keys) > 5) {
        echo "  - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

echo "📁 FILES REQUIRING FIXES (Top 20)\n";
echo "=================================\n";
$sortedFiles = $fileAnalysis;
uasort($sortedFiles, function($a, $b) {
    return $b['problematic_keys'] - $a['problematic_keys'];
});

$count = 0;
foreach ($sortedFiles as $file => $analysis) {
    if ($analysis['problematic_keys'] > 0) {
        $count++;
        if ($count <= 20) {
            echo sprintf("%-60s %3d problematic keys\n", $file, $analysis['problematic_keys']);
        }
    }
}

if ($count > 20) {
    echo "... and " . ($count - 20) . " more files with problematic keys\n";
}

// Save detailed analysis to JSON
$outputFile = $basePath . '/storage/localization/blade_analysis_report.json';
$reportData = [
    'analysis_date' => date('Y-m-d H:i:s'),
    'total_files' => count($bladeFiles),
    'files_with_keys' => count($fileAnalysis),
    'total_keys' => $totalKeys,
    'problematic_keys' => $problematicKeys,
    'patterns' => $translationPatterns,
    'file_analysis' => $fileAnalysis,
    'blade_files' => $bladeFiles
];

if (!is_dir(dirname($outputFile))) {
    mkdir(dirname($outputFile), 0755, true);
}

file_put_contents($outputFile, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n📄 Detailed analysis saved to: storage/localization/blade_analysis_report.json\n";
echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Create individual tasks for each file requiring fixes\n";
echo "2. Systematically fix dot notation to slash notation\n";
echo "3. Verify translation files exist for all keys\n";
echo "4. Add missing translation keys where needed\n";
echo "5. Test all fixes to ensure proper translation display\n";
