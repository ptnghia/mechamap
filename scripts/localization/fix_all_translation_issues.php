<?php
/**
 * Fix All Translation Issues
 * Comprehensive fix for all translation key problems
 */

echo "🔧 FIXING ALL TRANSLATION ISSUES\n";
echo "=================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Load the deep scan report
$reportFile = $basePath . '/storage/localization/deep_scan_report.json';
if (!file_exists($reportFile)) {
    echo "❌ Deep scan report not found. Please run deep_scan_missing_keys.php first.\n";
    exit(1);
}

$report = json_decode(file_get_contents($reportFile), true);
$failingKeys = $report['failing_keys_detail'];

echo "📊 ANALYSIS OF FAILING KEYS\n";
echo "===========================\n";
echo "Total failing keys: " . count($failingKeys) . "\n\n";

// Categorize the issues
$issues = [
    'dot_notation' => [],
    'missing_files' => [],
    'wrong_structure' => [],
    'helper_functions' => []
];

foreach ($failingKeys as $key => $files) {
    // Check for common patterns that need fixing
    if (preg_match('/^(messages|forms|buttons|common|pages|nav|forum|marketplace)\./', $key)) {
        $issues['dot_notation'][$key] = $files;
    } elseif (preg_match('/^(coming_soon|language)\./', $key)) {
        $issues['missing_files'][$key] = $files;
    } elseif (strpos($key, '.') === false) {
        $issues['wrong_structure'][$key] = $files;
    } else {
        $issues['helper_functions'][$key] = $files;
    }
}

echo "🔍 ISSUE CATEGORIES:\n";
echo "===================\n";
foreach ($issues as $category => $keys) {
    echo "$category: " . count($keys) . " keys\n";
}
echo "\n";

// Fix 1: Dot notation issues
echo "🔧 FIXING DOT NOTATION ISSUES...\n";
echo "================================\n";

$dotNotationFixes = [
    'messages.' => 'core/messages.',
    'forms.' => 'ui/forms.',
    'buttons.' => 'ui/buttons.',
    'common.' => 'ui/common.',
    'pages.' => 'content/pages.',
    'nav.' => 'ui/navigation.',
    'forum.' => 'features/forums.',
    'marketplace.' => 'features/marketplace.'
];

$filesModified = [];
$totalReplacements = 0;

foreach ($issues['dot_notation'] as $key => $files) {
    foreach ($files as $file) {
        $fullPath = $basePath . '/' . $file;
        if (!file_exists($fullPath)) continue;
        
        $content = file_get_contents($fullPath);
        $originalContent = $content;
        
        // Apply fixes based on the key pattern
        foreach ($dotNotationFixes as $pattern => $replacement) {
            if (strpos($key, $pattern) === 0) {
                $newKey = str_replace($pattern, $replacement, $key);
                
                // Replace in various formats
                $replacements = [
                    "__('$key')" => "__('$newKey')",
                    '__("' . $key . '")' => '__("' . $newKey . '")',
                    "t_ui('$key')" => "t_ui('$newKey')",
                    't_ui("' . $key . '")' => 't_ui("' . $newKey . '")',
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

echo "Modified " . count(array_unique($filesModified)) . " files with $totalReplacements replacements\n\n";

// Fix 2: Add missing translation files
echo "🔧 ADDING MISSING TRANSLATION FILES...\n";
echo "======================================\n";

$missingTranslations = [
    // Core messages
    'core/messages' => [
        'vi' => [
            'language' => [
                'switched_successfully' => 'Chuyển ngôn ngữ thành công',
                'switch_failed' => 'Chuyển ngôn ngữ thất bại',
                'auto_detect_failed' => 'Tự động phát hiện ngôn ngữ thất bại'
            ],
            'common' => [
                'loading' => 'Đang tải...',
                'load_more' => 'Tải thêm',
                'no_more_posts' => 'Không còn bài đăng nào',
                'error_occurred' => 'Có lỗi xảy ra'
            ],
            'profile_updated' => 'Cập nhật hồ sơ thành công',
            'password_updated' => 'Cập nhật mật khẩu thành công'
        ],
        'en' => [
            'language' => [
                'switched_successfully' => 'Language switched successfully',
                'switch_failed' => 'Language switch failed',
                'auto_detect_failed' => 'Auto detect language failed'
            ],
            'common' => [
                'loading' => 'Loading...',
                'load_more' => 'Load more',
                'no_more_posts' => 'No more posts',
                'error_occurred' => 'An error occurred'
            ],
            'profile_updated' => 'Profile updated successfully',
            'password_updated' => 'Password updated successfully'
        ]
    ],
    
    // UI forms
    'ui/forms' => [
        'vi' => [
            'search_conversations_placeholder' => 'Tìm kiếm cuộc trò chuyện...',
            'enter_message_placeholder' => 'Nhập tin nhắn...',
            'search_members_placeholder' => 'Tìm kiếm thành viên...'
        ],
        'en' => [
            'search_conversations_placeholder' => 'Search conversations...',
            'enter_message_placeholder' => 'Enter message...',
            'search_members_placeholder' => 'Search members...'
        ]
    ],
    
    // Coming soon
    'coming_soon' => [
        'vi' => [
            'notify_success' => 'Đăng ký thông báo thành công',
            'share_text' => 'Chia sẻ với bạn bè',
            'copied' => 'Đã sao chép'
        ],
        'en' => [
            'notify_success' => 'Notification registered successfully',
            'share_text' => 'Share with friends',
            'copied' => 'Copied'
        ]
    ]
];

foreach ($missingTranslations as $category => $languages) {
    foreach ($languages as $lang => $translations) {
        $filePath = "$basePath/resources/lang/$lang/$category.php";
        $dirPath = dirname($filePath);
        
        // Create directory if it doesn't exist
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }
        
        // Create or update the file
        if (file_exists($filePath)) {
            // Merge with existing translations
            $existing = include $filePath;
            if (is_array($existing)) {
                $translations = array_merge($existing, $translations);
            }
        }
        
        // Generate file content
        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * " . ucfirst($lang === 'vi' ? 'Vietnamese' : 'English') . " translations for $category\n";
        $content .= " * Auto-generated: " . date('Y-m-d H:i:s') . "\n";
        $content .= " * Keys: " . count($translations) . "\n";
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
    'core/messages.language.switched_successfully',
    'ui/forms.search_conversations_placeholder',
    'ui/buttons.cancel',
    'ui/common.popular_searches',
    'coming_soon.notify_success'
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
echo "Translation files created/updated: " . (count($missingTranslations) * 2) . "\n";
echo "\n🎯 NEXT: Run deep scan again to verify fixes\n";
