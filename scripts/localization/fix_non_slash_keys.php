<?php
/**
 * Fix Non-Slash Translation Keys
 * Convert problematic keys to proper slash notation and add missing translations
 */

echo "🔧 FIXING NON-SLASH TRANSLATION KEYS\n";
echo "====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load the failing keys report
$reportFile = $basePath . '/storage/localization/non_slash_keys_report.json';
if (!file_exists($reportFile)) {
    echo "❌ Report file not found. Please run check_non_slash_keys.php first.\n";
    exit(1);
}

$report = json_decode(file_get_contents($reportFile), true);
$failingKeys = $report['failing_keys_detail'];

echo "📋 Found " . count($failingKeys) . " failing keys to fix\n\n";

// Define key mappings and translations
$keyMappings = [
    // Forum keys
    'forum.poll.votes' => [
        'new_key' => 'forum/poll/votes',
        'translations' => [
            'en' => 'Votes',
            'vi' => 'Lượt bình chọn'
        ]
    ],
    
    // Auth keys
    'auth.login_to_view_notifications' => [
        'new_key' => 'auth/login_to_view_notifications',
        'translations' => [
            'en' => 'Login to view notifications',
            'vi' => 'Đăng nhập để xem thông báo'
        ]
    ],
    'auth.register_mechamap_account' => [
        'new_key' => 'auth/register_mechamap_account',
        'translations' => [
            'en' => 'Register MechaMap Account',
            'vi' => 'Đăng ký tài khoản MechaMap'
        ]
    ],
    
    // Language keys
    'language.switched_successfully' => [
        'new_key' => 'ui/language/switched_successfully',
        'translations' => [
            'en' => 'Language switched successfully',
            'vi' => 'Đã chuyển đổi ngôn ngữ thành công'
        ]
    ],
    'language.switch_failed' => [
        'new_key' => 'ui/language/switch_failed',
        'translations' => [
            'en' => 'Failed to switch language',
            'vi' => 'Không thể chuyển đổi ngôn ngữ'
        ]
    ],
    'language.auto_detected' => [
        'new_key' => 'ui/language/auto_detected',
        'translations' => [
            'en' => 'Language auto-detected',
            'vi' => 'Ngôn ngữ được tự động phát hiện'
        ]
    ],
    
    // Roles keys
    'roles.admin' => [
        'new_key' => 'user/roles/admin',
        'translations' => [
            'en' => 'Administrator',
            'vi' => 'Quản trị viên'
        ]
    ]
];

echo "🔄 PROCESSING KEY FIXES...\n";
echo "==========================\n";

$fixedFiles = [];
$addedTranslations = [];

foreach ($failingKeys as $oldKey => $files) {
    if (!isset($keyMappings[$oldKey])) {
        echo "⚠️  No mapping defined for key: $oldKey\n";
        continue;
    }
    
    $mapping = $keyMappings[$oldKey];
    $newKey = $mapping['new_key'];
    $translations = $mapping['translations'];
    
    echo "🔧 Fixing: $oldKey → $newKey\n";
    
    // Fix in each file
    foreach ($files as $file) {
        $fullPath = $basePath . '/' . $file;
        if (!file_exists($fullPath)) {
            echo "   ❌ File not found: $file\n";
            continue;
        }
        
        $content = file_get_contents($fullPath);
        $originalContent = $content;
        
        // Replace various patterns
        $patterns = [
            "/__(\'$oldKey\')/",
            "/__\(\"$oldKey\"\)/",
            "/@lang\(\'$oldKey\'\)/",
            "/@lang\(\"$oldKey\"\)/",
            "/trans\(\'$oldKey\'\)/",
            "/trans\(\"$oldKey\"\)/",
        ];
        
        $replacements = [
            "__('$newKey')",
            "__('$newKey')",
            "@lang('$newKey')",
            "@lang('$newKey')",
            "trans('$newKey')",
            "trans('$newKey')",
        ];
        
        $content = preg_replace($patterns, $replacements, $content);
        
        if ($content !== $originalContent) {
            file_put_contents($fullPath, $content);
            echo "   ✅ Updated: $file\n";
            $fixedFiles[] = $file;
        } else {
            echo "   ⚠️  No changes needed in: $file\n";
        }
    }
    
    // Add translations to language files
    foreach ($translations as $locale => $translation) {
        $addedTranslations[$locale][$newKey] = $translation;
    }
    
    echo "\n";
}

echo "📝 ADDING TRANSLATIONS...\n";
echo "=========================\n";

foreach ($addedTranslations as $locale => $translations) {
    echo "🌐 Adding translations for locale: $locale\n";
    
    foreach ($translations as $key => $translation) {
        // Parse the key to determine file structure
        $keyParts = explode('/', $key);
        $fileName = array_shift($keyParts);
        $nestedKey = implode('.', $keyParts);
        
        $langFile = $basePath . "/resources/lang/$locale/$fileName.php";
        
        // Ensure directory exists
        $langDir = dirname($langFile);
        if (!is_dir($langDir)) {
            mkdir($langDir, 0755, true);
            echo "   📁 Created directory: $langDir\n";
        }
        
        // Load or create language file
        $langData = [];
        if (file_exists($langFile)) {
            $langData = include $langFile;
            if (!is_array($langData)) {
                $langData = [];
            }
        }
        
        // Add the translation using dot notation for nested keys
        $keys = explode('.', $nestedKey);
        $current = &$langData;
        
        foreach ($keys as $i => $keyPart) {
            if ($i === count($keys) - 1) {
                // Last key - set the value
                $current[$keyPart] = $translation;
            } else {
                // Intermediate key - ensure array exists
                if (!isset($current[$keyPart]) || !is_array($current[$keyPart])) {
                    $current[$keyPart] = [];
                }
                $current = &$current[$keyPart];
            }
        }
        
        // Save the language file
        $langContent = "<?php\n\nreturn " . var_export($langData, true) . ";\n";
        file_put_contents($langFile, $langContent);
        
        echo "   ✅ Added: $key = '$translation' to $fileName.php\n";
    }
    echo "\n";
}

echo "📊 SUMMARY\n";
echo "==========\n";
echo "Fixed files: " . count(array_unique($fixedFiles)) . "\n";
echo "Added translations: " . array_sum(array_map('count', $addedTranslations)) . "\n";
echo "Languages updated: " . count($addedTranslations) . "\n";

// Test the fixes
echo "\n🧪 TESTING FIXES...\n";
echo "===================\n";

$allWorking = true;
foreach ($keyMappings as $oldKey => $mapping) {
    $newKey = $mapping['new_key'];
    $result = __($newKey);
    
    if ($result === $newKey) {
        echo "❌ $newKey - Still not working\n";
        $allWorking = false;
    } else {
        echo "✅ $newKey - Working: '$result'\n";
    }
}

if ($allWorking) {
    echo "\n🎉 All fixes are working correctly!\n";
} else {
    echo "\n⚠️  Some fixes may need additional attention.\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Clear Laravel cache: php artisan cache:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Test the affected pages in browser\n";
echo "4. Commit the changes if everything looks good\n";
