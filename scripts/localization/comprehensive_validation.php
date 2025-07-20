<?php
/**
 * Comprehensive Translation Validation
 * Test tất cả translation keys và tạo báo cáo chi tiết
 */

echo "🧪 COMPREHENSIVE TRANSLATION VALIDATION\n";
echo "=======================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/comprehensive_blade_audit.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found. Please run comprehensive_blade_audit.php first.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

echo "📊 VALIDATION OVERVIEW\n";
echo "======================\n";
echo "Total keys to validate: " . $analysis['total_keys'] . "\n";
echo "Files with keys: " . $analysis['files_with_keys'] . "\n\n";

$validationResults = [
    'working' => [],
    'failing' => [],
    'total_tested' => 0,
    'success_rate' => 0
];

echo "🔍 TESTING HELPER FUNCTION KEYS...\n";
echo "==================================\n";

// Test helper function keys
$helperPatterns = [
    'helper_t_ui' => 't_ui',
    'helper_t_core' => 't_core', 
    'helper_t_user' => 't_user',
    'helper_t_admin' => 't_admin',
    'helper_t_feature' => 't_feature',
    'helper_t_content' => 't_content',
];

foreach ($helperPatterns as $patternName => $helperFunction) {
    if (isset($analysis['patterns'][$patternName])) {
        $keys = $analysis['patterns'][$patternName]['keys'];
        echo "📋 Testing $helperFunction keys: " . count($keys) . " found\n";
        
        $tested = 0;
        $working = 0;
        
        foreach ($keys as $fullKey => $files) {
            // Extract the key without prefix
            $prefix = str_replace('t_', '', $helperFunction) . '/';
            $keyWithoutPrefix = str_replace($prefix, '', $fullKey);
            
            try {
                $result = call_user_func($helperFunction, $keyWithoutPrefix);
                $validationResults['total_tested']++;
                $tested++;
                
                if ($result !== $fullKey) {
                    $validationResults['working'][] = [
                        'function' => $helperFunction,
                        'key' => $keyWithoutPrefix,
                        'full_key' => $fullKey,
                        'result' => $result,
                        'files' => $files
                    ];
                    $working++;
                } else {
                    $validationResults['failing'][] = [
                        'function' => $helperFunction,
                        'key' => $keyWithoutPrefix,
                        'full_key' => $fullKey,
                        'result' => $result,
                        'files' => $files,
                        'reason' => 'Translation not found'
                    ];
                }
            } catch (Exception $e) {
                $validationResults['failing'][] = [
                    'function' => $helperFunction,
                    'key' => $keyWithoutPrefix,
                    'full_key' => $fullKey,
                    'result' => null,
                    'files' => $files,
                    'reason' => 'Error: ' . $e->getMessage()
                ];
                $validationResults['total_tested']++;
                $tested++;
            }
        }
        
        echo "   ✅ Working: $working / $tested (" . round(($working / max($tested, 1)) * 100, 1) . "%)\n";
    }
}

echo "\n🔍 TESTING DIRECT TRANSLATION KEYS...\n";
echo "=====================================\n";

// Test direct translation keys (sample only - too many to test all)
$directPatterns = ['direct_underscore', 'direct_trans', 'blade_lang'];

foreach ($directPatterns as $patternName) {
    if (isset($analysis['patterns'][$patternName])) {
        $keys = $analysis['patterns'][$patternName]['keys'];
        echo "📋 Testing $patternName keys: " . count($keys) . " found (sampling 50)\n";
        
        $tested = 0;
        $working = 0;
        $sampleKeys = array_slice($keys, 0, 50, true);
        
        foreach ($sampleKeys as $fullKey => $files) {
            try {
                $result = __($fullKey);
                $validationResults['total_tested']++;
                $tested++;
                
                if ($result !== $fullKey) {
                    $validationResults['working'][] = [
                        'function' => '__',
                        'key' => $fullKey,
                        'full_key' => $fullKey,
                        'result' => $result,
                        'files' => $files
                    ];
                    $working++;
                } else {
                    $validationResults['failing'][] = [
                        'function' => '__',
                        'key' => $fullKey,
                        'full_key' => $fullKey,
                        'result' => $result,
                        'files' => $files,
                        'reason' => 'Translation not found'
                    ];
                }
            } catch (Exception $e) {
                $validationResults['failing'][] = [
                    'function' => '__',
                    'key' => $fullKey,
                    'full_key' => $fullKey,
                    'result' => null,
                    'files' => $files,
                    'reason' => 'Error: ' . $e->getMessage()
                ];
                $validationResults['total_tested']++;
                $tested++;
            }
        }
        
        echo "   ✅ Working: $working / $tested (" . round(($working / max($tested, 1)) * 100, 1) . "%)\n";
    }
}

// Calculate success rate
$validationResults['success_rate'] = $validationResults['total_tested'] > 0 ? 
    round((count($validationResults['working']) / $validationResults['total_tested']) * 100, 1) : 0;

echo "\n📊 VALIDATION RESULTS\n";
echo "====================\n";
echo "Total keys tested: " . $validationResults['total_tested'] . "\n";
echo "Working keys: " . count($validationResults['working']) . "\n";
echo "Failing keys: " . count($validationResults['failing']) . "\n";
echo "Success rate: " . $validationResults['success_rate'] . "%\n\n";

echo "❌ TOP 20 FAILING KEYS\n";
echo "======================\n";

foreach (array_slice($validationResults['failing'], 0, 20) as $i => $failure) {
    echo ($i + 1) . ". {$failure['function']}('{$failure['key']}')\n";
    echo "   Full key: {$failure['full_key']}\n";
    echo "   Reason: {$failure['reason']}\n";
    echo "   Used in: " . implode(', ', array_slice($failure['files'], 0, 2)) . 
         (count($failure['files']) > 2 ? ' +' . (count($failure['files']) - 2) . ' more' : '') . "\n\n";
}

echo "✅ SAMPLE WORKING KEYS\n";
echo "======================\n";

foreach (array_slice($validationResults['working'], 0, 10) as $i => $success) {
    echo ($i + 1) . ". {$success['function']}('{$success['key']}') → '{$success['result']}'\n";
}

// Save detailed validation report
$outputFile = $basePath . '/storage/localization/validation_report.json';
$reportData = [
    'validation_date' => date('Y-m-d H:i:s'),
    'total_tested' => $validationResults['total_tested'],
    'working_count' => count($validationResults['working']),
    'failing_count' => count($validationResults['failing']),
    'success_rate' => $validationResults['success_rate'],
    'working_keys' => $validationResults['working'],
    'failing_keys' => $validationResults['failing']
];

file_put_contents($outputFile, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n💾 Detailed validation report saved to: storage/localization/validation_report.json\n\n";

echo "🎯 RECOMMENDATIONS\n";
echo "==================\n";

if ($validationResults['success_rate'] < 50) {
    echo "🔴 CRITICAL: Success rate is very low (" . $validationResults['success_rate'] . "%)\n";
    echo "   - Focus on creating missing translation files\n";
    echo "   - Check file structure and key naming\n";
    echo "   - Verify helper function implementations\n";
} elseif ($validationResults['success_rate'] < 80) {
    echo "🟡 WARNING: Success rate needs improvement (" . $validationResults['success_rate'] . "%)\n";
    echo "   - Add missing translations for failing keys\n";
    echo "   - Review key structure consistency\n";
} else {
    echo "🟢 GOOD: Success rate is acceptable (" . $validationResults['success_rate'] . "%)\n";
    echo "   - Fine-tune remaining failing keys\n";
    echo "   - Focus on quality improvements\n";
}

echo "\n🔧 NEXT ACTIONS\n";
echo "===============\n";
echo "1. Review failing keys and create missing translations\n";
echo "2. Fix file structure issues\n";
echo "3. Test critical pages in browser\n";
echo "4. Update documentation\n";
echo "5. Commit working changes\n";
