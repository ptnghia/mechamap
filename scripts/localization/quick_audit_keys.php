<?php
/**
 * Quick Audit Translation Keys
 * Check actual key counts and functionality
 */

echo "🔍 QUICK AUDIT TRANSLATION KEYS\n";
echo "===============================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langPath = $basePath . '/resources/lang';

// Bootstrap Laravel to test translations
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📊 Counting translation files and keys...\n";

$languages = ['vi', 'en'];
$categories = ['admin', 'content', 'core', 'features', 'ui', 'user'];

$totalFiles = 0;
$totalKeys = 0;
$keysByCategory = [];
$emptyFiles = [];
$workingKeys = [];
$failingKeys = [];

foreach ($languages as $lang) {
    echo "\n🌐 Language: " . strtoupper($lang) . "\n";
    echo str_repeat('-', 30) . "\n";
    
    foreach ($categories as $category) {
        $categoryPath = "$langPath/$lang/$category";
        
        if (!is_dir($categoryPath)) {
            echo "   ⚠️ Missing category: $category\n";
            continue;
        }
        
        $files = glob($categoryPath . '/*.php');
        $categoryKeys = 0;
        
        foreach ($files as $file) {
            $totalFiles++;
            $fileName = basename($file, '.php');
            
            try {
                $translations = include $file;
                if (!is_array($translations)) {
                    $emptyFiles[] = "$lang/$category/$fileName.php";
                    continue;
                }
                
                $fileKeys = countNestedKeys($translations);
                $categoryKeys += $fileKeys;
                $totalKeys += $fileKeys;
                
                // Test some keys
                if ($lang === 'vi') { // Test with Vietnamese
                    $testKeys = getTestKeys($translations, "$category/$fileName");
                    foreach ($testKeys as $testKey) {
                        $fullKey = "$category/$fileName.$testKey";
                        try {
                            $result = __($fullKey);
                            if ($result === $fullKey) {
                                $failingKeys[] = $fullKey;
                            } else {
                                $workingKeys[] = $fullKey;
                            }
                        } catch (Exception $e) {
                            $failingKeys[] = $fullKey . " (Error: " . $e->getMessage() . ")";
                        }
                    }
                }
                
            } catch (Exception $e) {
                $emptyFiles[] = "$lang/$category/$fileName.php (Error: " . $e->getMessage() . ")";
            }
        }
        
        if (!isset($keysByCategory[$category])) {
            $keysByCategory[$category] = 0;
        }
        $keysByCategory[$category] += $categoryKeys;
        
        echo "   📁 $category: " . count($files) . " files, $categoryKeys keys\n";
    }
}

echo "\n📊 SUMMARY STATISTICS\n";
echo "=====================\n";
echo "Total files: $totalFiles\n";
echo "Total keys: $totalKeys\n";
echo "Empty/error files: " . count($emptyFiles) . "\n";
echo "Working keys tested: " . count($workingKeys) . "\n";
echo "Failing keys tested: " . count($failingKeys) . "\n";

echo "\n📋 Keys by Category:\n";
foreach ($keysByCategory as $category => $count) {
    echo "   $category: $count keys\n";
}

if (!empty($emptyFiles)) {
    echo "\n⚠️ EMPTY/ERROR FILES:\n";
    foreach (array_slice($emptyFiles, 0, 10) as $file) {
        echo "   - $file\n";
    }
    if (count($emptyFiles) > 10) {
        echo "   ... and " . (count($emptyFiles) - 10) . " more\n";
    }
}

if (!empty($failingKeys)) {
    echo "\n❌ FAILING KEYS (sample):\n";
    foreach (array_slice($failingKeys, 0, 10) as $key) {
        echo "   - $key\n";
    }
    if (count($failingKeys) > 10) {
        echo "   ... and " . (count($failingKeys) - 10) . " more\n";
    }
}

if (!empty($workingKeys)) {
    echo "\n✅ WORKING KEYS (sample):\n";
    foreach (array_slice($workingKeys, 0, 5) as $key) {
        $translation = __($key);
        echo "   - $key → '$translation'\n";
    }
}

// Test helper functions
echo "\n🧪 TESTING HELPER FUNCTIONS:\n";
echo "=============================\n";

$helperTests = [
    't_ui' => ['buttons.cancel', 'common.light_mode'],
    't_core' => ['auth.login', 'validation.required'],
    't_feature' => ['marketplace.actions.add_to_cart', 'forums.actions.reply'],
    't_content' => ['pages.about_us', 'welcome.greeting'],
    't_user' => ['profile.labels.personal_info', 'dashboard.navigation.settings']
];

foreach ($helperTests as $helper => $keys) {
    echo "\nTesting $helper():\n";
    foreach ($keys as $key) {
        try {
            $result = call_user_func($helper, $key);
            $status = ($result === "$helper.$key" || strpos($result, $key) !== false) ? "❌ FAIL" : "✅ WORK";
            echo "   $status $helper('$key') → '$result'\n";
        } catch (Exception $e) {
            echo "   ❌ ERROR $helper('$key') → " . $e->getMessage() . "\n";
        }
    }
}

// Check for common UI keys that should exist
echo "\n🔍 CHECKING COMMON UI KEYS:\n";
echo "===========================\n";

$commonKeys = [
    'UI.COMMON.COMMUNITY',
    'UI.COMMON.SHOWCASE', 
    'UI.COMMON.MARKETPLACE',
    'UI.COMMON.ADD',
    'ui/common.community',
    'ui/common.showcase',
    'ui/common.marketplace',
    'ui/common.add',
    'buttons.cancel',
    'buttons.save',
    'common.light_mode',
    'common.dark_mode'
];

foreach ($commonKeys as $key) {
    $result = __($key);
    $status = ($result === $key) ? "❌ NOT FOUND" : "✅ FOUND";
    echo "   $status '$key' → '$result'\n";
}

function countNestedKeys($array) {
    $count = 0;
    foreach ($array as $value) {
        if (is_array($value)) {
            $count += countNestedKeys($value);
        } else {
            $count++;
        }
    }
    return $count;
}

function getTestKeys($array, $prefix = '', $maxKeys = 3) {
    $keys = [];
    $count = 0;
    
    foreach ($array as $key => $value) {
        if ($count >= $maxKeys) break;
        
        if (is_array($value)) {
            $subKeys = getTestKeys($value, $prefix ? "$prefix.$key" : $key, 2);
            $keys = array_merge($keys, $subKeys);
        } else {
            $keys[] = $prefix ? "$prefix.$key" : $key;
        }
        $count++;
    }
    
    return $keys;
}
