<?php

/**
 * MechaMap Translation Structure Analyzer
 * Location: scripts/analyze-translation-structure.php
 *
 * Analyzes current translation files and creates migration plan
 */

echo "🔍 MechaMap Translation Structure Analysis\n";
echo "==========================================\n\n";

// Configuration
$translationDir = 'resources/lang/vi/';
$outputDir = 'docs/reports/';
$analysisFile = $outputDir . 'TRANSLATION_STRUCTURE_ANALYSIS_' . date('Y_m_d') . '.md';

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Get all translation files
$translationFiles = glob($translationDir . '*.php');
$translationFiles = array_filter($translationFiles, function($file) {
    return !strpos($file, '.backup.');
});

echo "📁 Found " . count($translationFiles) . " translation files\n\n";

$analysis = [];
$totalKeys = 0;
$problemKeys = 0;

foreach ($translationFiles as $filePath) {
    $fileName = basename($filePath, '.php');
    echo "📄 Analyzing: $fileName.php\n";

    // Load the translation array
    $translations = include $filePath;

    if (!is_array($translations)) {
        echo "  ❌ Invalid file structure (not an array)\n\n";
        continue;
    }

    $fileAnalysis = [
        'file' => $fileName,
        'total_keys' => 0,
        'structure' => [],
        'issues' => [],
        'recommended_sections' => [],
    ];

    // Analyze structure
    $fileAnalysis = analyzeArray($translations, $fileAnalysis, '');

    $totalKeys += $fileAnalysis['total_keys'];
    $problemKeys += count($fileAnalysis['issues']);

    echo "  ✅ Keys: {$fileAnalysis['total_keys']}, Issues: " . count($fileAnalysis['issues']) . "\n\n";

    $analysis[$fileName] = $fileAnalysis;
}

// Generate helper function usage analysis
echo "🔧 Analyzing helper function usage...\n";
$helperFunctions = [
    't_auth', 't_common', 't_navigation', 't_forums', 't_marketplace',
    't_user', 't_search', 't_admin', 't_pages', 't_showcase'
];

$bladeFiles = glob('resources/views/**/*.blade.php');
$helperUsage = [];
$directUsage = 0;

foreach ($bladeFiles as $bladeFile) {
    $content = file_get_contents($bladeFile);

    // Count direct __() usage
    preg_match_all('/__\(/', $content, $matches);
    $directUsage += count($matches[0]);

    // Count helper function usage
    foreach ($helperFunctions as $helper) {
        preg_match_all("/$helper\(/", $content, $matches);
        if (!isset($helperUsage[$helper])) {
            $helperUsage[$helper] = 0;
        }
        $helperUsage[$helper] += count($matches[0]);
    }
}

// Generate report
$report = generateAnalysisReport($analysis, $totalKeys, $problemKeys, $directUsage, $helperUsage);

// Write report
file_put_contents($analysisFile, $report);

echo "📊 Analysis Results:\n";
echo "==================\n";
echo "Total Keys: $totalKeys\n";
echo "Problem Keys: $problemKeys\n";
echo "Direct __() Usage: $directUsage\n";
echo "Helper Function Usage: " . array_sum($helperUsage) . "\n\n";

echo "✅ Analysis completed!\n";
echo "📄 Report saved: $analysisFile\n\n";

echo "🎯 Next Steps:\n";
echo "1. Review the analysis report\n";
echo "2. Run migration planning script\n";
echo "3. Create standardized translation files\n";

/**
 * Analyze translation array structure recursively
 */
function analyzeArray($array, $fileAnalysis, $prefix = '') {
    foreach ($array as $key => $value) {
        $fullKey = $prefix ? "$prefix.$key" : $key;
        $fileAnalysis['total_keys']++;

        if (is_array($value)) {
            // Check for problematic array structures
            if (isIndexedArray($value)) {
                $fileAnalysis['issues'][] = [
                    'key' => $fullKey,
                    'type' => 'indexed_array',
                    'value' => $value
                ];
            } else {
                // Nested associative array - analyze recursively
                $fileAnalysis = analyzeArray($value, $fileAnalysis, $fullKey);
            }
        } elseif (!is_string($value)) {
            $fileAnalysis['issues'][] = [
                'key' => $fullKey,
                'type' => 'non_string_value',
                'value' => $value
            ];
        }

        // Track structure depth
        $depth = substr_count($fullKey, '.');
        if (!isset($fileAnalysis['structure'][$depth])) {
            $fileAnalysis['structure'][$depth] = 0;
        }
        $fileAnalysis['structure'][$depth]++;
    }

    return $fileAnalysis;
}

/**
 * Check if array is indexed (problematic for translations)
 */
function isIndexedArray($array) {
    return array_keys($array) === range(0, count($array) - 1);
}

/**
 * Generate comprehensive analysis report
 */
function generateAnalysisReport($analysis, $totalKeys, $problemKeys, $directUsage, $helperUsage) {
    $report = "# MechaMap Translation Structure Analysis\n";
    $report .= "*Generated: " . date('Y-m-d H:i:s') . "*\n\n";

    $report .= "## 📊 **OVERVIEW**\n\n";
    $report .= "- **Total Translation Keys**: $totalKeys\n";
    $report .= "- **Problem Keys**: $problemKeys (" . round(($problemKeys/$totalKeys)*100, 1) . "%)\n";
    $report .= "- **Direct __() Usage**: $directUsage calls\n";
    $report .= "- **Helper Function Usage**: " . array_sum($helperUsage) . " calls\n\n";

    $report .= "## 📁 **FILE ANALYSIS**\n\n";

    foreach ($analysis as $fileName => $data) {
        $report .= "### **$fileName.php**\n";
        $report .= "- Total Keys: {$data['total_keys']}\n";
        $report .= "- Issues: " . count($data['issues']) . "\n";

        if (!empty($data['issues'])) {
            $report .= "- **Problems Found**:\n";
            foreach ($data['issues'] as $issue) {
                $report .= "  - `{$issue['key']}`: {$issue['type']}\n";
            }
        }

        $report .= "\n";
    }

    $report .= "## 🎯 **RECOMMENDATIONS**\n\n";
    $report .= "1. **Fix Problem Keys**: " . $problemKeys . " keys need structure fixes\n";
    $report .= "2. **Migrate to Helpers**: " . $directUsage . " direct __() calls to convert\n";
    $report .= "3. **Standardize Structure**: Implement 3-level key format\n";
    $report .= "4. **Add Missing Keys**: Ensure all blade usage has corresponding translations\n\n";

    $report .= "## ⚡ **PRIORITY ACTIONS**\n\n";
    $report .= "1. **HIGH**: Fix indexed arrays and non-string values\n";
    $report .= "2. **MEDIUM**: Convert direct __() calls to helper functions\n";
    $report .= "3. **LOW**: Reorganize key structure for consistency\n\n";

    $report .= "---\n";
    $report .= "*MechaMap Translation Analysis - " . date('Y-m-d') . "*\n";

    return $report;
}

echo "✅ Analysis script completed!\n";
