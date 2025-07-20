<?php
/**
 * Fix Helper Function Translation Keys
 * Sửa các keys sử dụng helper functions để tránh double prefix
 */

echo "🔧 FIXING HELPER FUNCTION TRANSLATION KEYS\n";
echo "==========================================\n\n";

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

echo "📋 LOADED ANALYSIS DATA\n";
echo "=======================\n";
echo "Total keys found: " . $analysis['total_keys'] . "\n";
echo "Files with keys: " . $analysis['files_with_keys'] . "\n\n";

// Define helper function patterns to fix
$helperPatterns = [
    't_ui' => [
        'expected_prefix' => 'ui/',
        'wrong_patterns' => [
            't_ui(\'ui/',
            't_ui("ui/',
        ],
        'correct_patterns' => [
            't_ui(\'',
            't_ui("',
        ]
    ],
    't_core' => [
        'expected_prefix' => 'core/',
        'wrong_patterns' => [
            't_core(\'core/',
            't_core("core/',
        ],
        'correct_patterns' => [
            't_core(\'',
            't_core("',
        ]
    ],
    't_user' => [
        'expected_prefix' => 'user/',
        'wrong_patterns' => [
            't_user(\'user/',
            't_user("user/',
        ],
        'correct_patterns' => [
            't_user(\'',
            't_user("',
        ]
    ],
    't_admin' => [
        'expected_prefix' => 'admin/',
        'wrong_patterns' => [
            't_admin(\'admin/',
            't_admin("admin/',
        ],
        'correct_patterns' => [
            't_admin(\'',
            't_admin("',
        ]
    ],
    't_feature' => [
        'expected_prefix' => 'features/',
        'wrong_patterns' => [
            't_feature(\'features/',
            't_feature("features/',
        ],
        'correct_patterns' => [
            't_feature(\'',
            't_feature("',
        ]
    ],
    't_content' => [
        'expected_prefix' => 'content/',
        'wrong_patterns' => [
            't_content(\'content/',
            't_content("content/',
        ],
        'correct_patterns' => [
            't_content(\'',
            't_content("',
        ]
    ]
];

echo "🔍 IDENTIFYING FILES TO FIX...\n";
echo "==============================\n";

$filesToFix = [];
$totalReplacements = 0;

// Get files that have helper function keys with double prefixes
foreach ($analysis['patterns'] as $patternName => $patternData) {
    if (strpos($patternName, 'helper_') === 0) {
        $helperName = str_replace('helper_', '', $patternName);

        if (isset($helperPatterns[$helperName])) {
            foreach ($patternData['keys'] as $fullKey => $files) {
                $expectedPrefix = $helperPatterns[$helperName]['expected_prefix'];

                // Check if key has double prefix
                if (strpos($fullKey, $expectedPrefix . $expectedPrefix) === 0) {
                    foreach ($files as $file) {
                        if (!isset($filesToFix[$file])) {
                            $filesToFix[$file] = [];
                        }
                        $filesToFix[$file][] = [
                            'helper' => $helperName,
                            'wrong_key' => $fullKey,
                            'correct_key' => str_replace($expectedPrefix . $expectedPrefix, $expectedPrefix, $fullKey)
                        ];
                    }
                }
            }
        }
    }
}

echo "Found " . count($filesToFix) . " files that need fixing\n\n";

echo "🔧 APPLYING FIXES...\n";
echo "====================\n";

foreach ($filesToFix as $file => $fixes) {
    $fullPath = $basePath . '/' . $file;

    if (!file_exists($fullPath)) {
        echo "⚠️ File not found: $file\n";
        continue;
    }

    echo "📄 Processing: $file\n";

    $content = file_get_contents($fullPath);
    $originalContent = $content;
    $fileReplacements = 0;

    foreach ($fixes as $fix) {
        $helper = $fix['helper'];
        $pattern = $helperPatterns[$helper];

        // Apply replacements for both single and double quotes
        foreach ($pattern['wrong_patterns'] as $i => $wrongPattern) {
            $correctPattern = $pattern['correct_patterns'][$i];

            $oldCount = substr_count($content, $wrongPattern);
            $content = str_replace($wrongPattern, $correctPattern, $content);
            $newCount = substr_count($content, $wrongPattern);

            $replacements = $oldCount - $newCount;
            if ($replacements > 0) {
                echo "   ✅ Fixed $replacements instances of $wrongPattern\n";
                $fileReplacements += $replacements;
                $totalReplacements += $replacements;
            }
        }
    }

    if ($content !== $originalContent) {
        file_put_contents($fullPath, $content);
        echo "   💾 Saved $file with $fileReplacements fixes\n";
    } else {
        echo "   ⚠️ No changes needed in $file\n";
    }

    echo "\n";
}

echo "📊 SUMMARY\n";
echo "==========\n";
echo "Files processed: " . count($filesToFix) . "\n";
echo "Total replacements: $totalReplacements\n\n";

// Now test some helper functions to see if they work
echo "🧪 TESTING HELPER FUNCTIONS...\n";
echo "==============================\n";

$testCases = [
    ['function' => 't_ui', 'key' => 'forms.search_conversations_placeholder'],
    ['function' => 't_ui', 'key' => 'buttons.cancel'],
    ['function' => 't_ui', 'key' => 'common.popular_searches'],
    ['function' => 't_content', 'key' => 'home.welcome_message'],
    ['function' => 't_user', 'key' => 'profile.edit_profile'],
];

foreach ($testCases as $test) {
    try {
        $result = call_user_func($test['function'], $test['key']);
        $expectedKey = $test['function'] === 't_ui' ? 'ui/' . $test['key'] :
                      ($test['function'] === 't_content' ? 'content/' . $test['key'] :
                      ($test['function'] === 't_user' ? 'user/' . $test['key'] : $test['key']));

        if ($result === $expectedKey) {
            echo "❌ {$test['function']}('{$test['key']}') - Translation missing\n";
        } else {
            echo "✅ {$test['function']}('{$test['key']}') - Working: '$result'\n";
        }
    } catch (Exception $e) {
        echo "❌ {$test['function']}('{$test['key']}') - Error: " . $e->getMessage() . "\n";
    }
}

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Fixed helper function double prefixes\n";
echo "2. 🔄 Next: Create missing translation files for helper functions\n";
echo "3. 🔄 Next: Check direct translation keys (__(), trans(), @lang())\n";
echo "4. 🔄 Next: Validate all translations work correctly\n\n";

echo "💡 RECOMMENDATION\n";
echo "=================\n";
echo "Run the comprehensive audit again to see the updated key structure\n";
echo "Then proceed with creating the missing translation files\n";
