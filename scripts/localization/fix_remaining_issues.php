<?php
/**
 * Fix Remaining Translation Issues
 * Handle the remaining problematic keys
 */

echo "🔧 FIXING REMAINING TRANSLATION ISSUES\n";
echo "======================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Load the latest deep scan report
$reportFile = $basePath . '/storage/localization/deep_scan_report.json';
$report = json_decode(file_get_contents($reportFile), true);
$failingKeys = $report['failing_keys_detail'];

echo "📊 Remaining failing keys: " . count($failingKeys) . "\n\n";

// Fix specific patterns
$patternFixes = [
    'pages.pages.' => 'content/pages.',
    'marketplace.actions.' => 'features/marketplace.actions.',
    'profile.labels.' => 'user/profile.labels.',
    'status.' => 'ui/status.',
    'notifications.' => 'core/notifications.'
];

$filesModified = [];
$totalReplacements = 0;

echo "🔧 FIXING PATTERN ISSUES...\n";
echo "===========================\n";

foreach ($failingKeys as $key => $files) {
    // Skip Vietnamese text keys (no-dot pattern)
    if (strpos($key, '.') === false && preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', $key)) {
        continue;
    }
    
    foreach ($files as $file) {
        $fullPath = $basePath . '/' . $file;
        if (!file_exists($fullPath)) continue;
        
        $content = file_get_contents($fullPath);
        $originalContent = $content;
        
        // Apply pattern fixes
        foreach ($patternFixes as $pattern => $replacement) {
            if (strpos($key, $pattern) === 0) {
                $newKey = str_replace($pattern, $replacement, $key);
                
                // Replace in various formats
                $replacements = [
                    "__('$key')" => "__('$newKey')",
                    '__("' . $key . '")' => '__("' . $newKey . '")',
                    "t_ui('$key')" => "t_ui('$newKey')",
                    't_ui("' . $key . '")' => 't_ui("' . $newKey . '")',
                    "t_feature('$key')" => "t_feature('$newKey')",
                    't_feature("' . $key . '")' => 't_feature("' . $newKey . '")',
                    "t_user('$key')" => "t_user('$newKey')",
                    't_user("' . $key . '")' => 't_user("' . $newKey . '")',
                    "t_content('$key')" => "t_content('$newKey')",
                    't_content("' . $key . '")' => 't_content("' . $newKey . '")',
                ];
                
                foreach ($replacements as $old => $new) {
                    if (strpos($content, $old) !== false) {
                        $content = str_replace($old, $new, $content);
                        $totalReplacements++;
                        echo "   Fixed: $old → $new in $file\n";
                    }
                }
                break;
            }
        }
        
        if ($content !== $originalContent) {
            file_put_contents($fullPath, $content);
            $filesModified[] = $file;
        }
    }
}

echo "\n🔧 ADDING MISSING TRANSLATION KEYS...\n";
echo "=====================================\n";

// Add missing translation keys
$missingKeys = [
    'content/pages' => [
        'vi' => [
            'pages' => [
                'community_rules' => 'Quy tắc cộng đồng',
                'contact' => 'Liên hệ',
                'about_us' => 'Về chúng tôi',
                'privacy_policy' => 'Chính sách bảo mật',
                'terms_of_service' => 'Điều khoản dịch vụ'
            ]
        ],
        'en' => [
            'pages' => [
                'community_rules' => 'Community Rules',
                'contact' => 'Contact',
                'about_us' => 'About Us',
                'privacy_policy' => 'Privacy Policy',
                'terms_of_service' => 'Terms of Service'
            ]
        ]
    ],
    
    'features/marketplace' => [
        'vi' => [
            'actions' => [
                'reload' => 'Tải lại',
                'details' => 'Chi tiết',
                'cancel' => 'Hủy'
            ]
        ],
        'en' => [
            'actions' => [
                'reload' => 'Reload',
                'details' => 'Details',
                'cancel' => 'Cancel'
            ]
        ]
    ],
    
    'user/profile' => [
        'vi' => [
            'labels' => [
                'role' => 'Vai trò',
                'all_roles' => 'Tất cả vai trò'
            ]
        ],
        'en' => [
            'labels' => [
                'role' => 'Role',
                'all_roles' => 'All Roles'
            ]
        ]
    ],
    
    'ui/status' => [
        'vi' => [
            'sticky' => 'Ghim'
        ],
        'en' => [
            'sticky' => 'Sticky'
        ]
    ],
    
    'core/notifications' => [
        'vi' => [
            'marked_all_read' => 'Đã đánh dấu tất cả là đã đọc'
        ],
        'en' => [
            'marked_all_read' => 'Marked all as read'
        ]
    ]
];

foreach ($missingKeys as $category => $languages) {
    foreach ($languages as $lang => $translations) {
        $filePath = "$basePath/resources/lang/$lang/$category.php";
        $dirPath = dirname($filePath);
        
        // Create directory if it doesn't exist
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }
        
        // Merge with existing translations if file exists
        if (file_exists($filePath)) {
            $existing = include $filePath;
            if (is_array($existing)) {
                $translations = array_merge_recursive($existing, $translations);
            }
        }
        
        // Generate file content
        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * " . ucfirst($lang === 'vi' ? 'Vietnamese' : 'English') . " translations for $category\n";
        $content .= " * Auto-generated: " . date('Y-m-d H:i:s') . "\n";
        $content .= " * Keys: " . countNestedKeys($translations) . "\n";
        $content .= " */\n\n";
        $content .= "return " . var_export($translations, true) . ";\n";
        
        file_put_contents($filePath, $content);
        echo "   Created/Updated: $filePath\n";
    }
}

echo "\n🧪 TESTING FIXES...\n";
echo "===================\n";

// Bootstrap Laravel to test
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear cache
exec('cd ' . $basePath . ' && php artisan view:clear && php artisan cache:clear');

// Test some fixed keys
$testKeys = [
    'content/pages.pages.community_rules',
    'features/marketplace.actions.reload',
    'user/profile.labels.role',
    'ui/status.sticky',
    'core/notifications.marked_all_read'
];

foreach ($testKeys as $key) {
    $result = __($key);
    $status = ($result === $key) ? "❌ FAIL" : "✅ WORK";
    echo "   $status $key → '$result'\n";
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Files modified: " . count(array_unique($filesModified)) . "\n";
echo "Replacements made: $totalReplacements\n";
echo "Translation files created/updated: " . (count($missingKeys) * 2) . "\n";

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

echo "\n🎯 NEXT: Run final deep scan to verify all fixes\n";
