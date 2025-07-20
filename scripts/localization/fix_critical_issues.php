<?php
/**
 * Fix Critical Translation Issues
 * Sửa các vấn đề quan trọng dựa trên validation report
 */

echo "🔧 FIXING CRITICAL TRANSLATION ISSUES\n";
echo "=====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load validation report
$reportFile = $basePath . '/storage/localization/validation_report.json';
if (!file_exists($reportFile)) {
    echo "❌ Validation report not found. Please run comprehensive_validation.php first.\n";
    exit(1);
}

$report = json_decode(file_get_contents($reportFile), true);

echo "📊 VALIDATION REPORT OVERVIEW\n";
echo "=============================\n";
echo "Success rate: " . $report['success_rate'] . "%\n";
echo "Failing keys: " . $report['failing_count'] . "\n\n";

// Define critical fixes
$criticalFixes = [
    // Fix wrong helper function usage
    [
        'type' => 'file_replacement',
        'file' => 'resources/views/components/header.blade.php',
        'old' => "t_ui('core/notifications.marked_all_read')",
        'new' => "t_core('notifications.marked_all_read')",
        'description' => 'Fix wrong helper function for core notifications'
    ],
    [
        'type' => 'file_replacement', 
        'file' => 'resources/views/components/menu/admin-menu.blade.php',
        'old' => "t_ui('user/roles/admin')",
        'new' => "t_user('roles.admin')",
        'description' => 'Fix wrong helper function for user roles'
    ],
];

// Define missing translations to add
$missingTranslations = [
    // UI translations
    'ui/auth.php' => [
        'en' => [
            'login_to_view_notifications' => 'Login to view notifications',
            'register_mechamap_account' => 'Register MechaMap Account'
        ],
        'vi' => [
            'login_to_view_notifications' => 'Đăng nhập để xem thông báo',
            'register_mechamap_account' => 'Đăng ký tài khoản MechaMap'
        ]
    ],
    
    // Core translations
    'core/notifications.php' => [
        'en' => [
            'marked_all_read' => 'All notifications marked as read'
        ],
        'vi' => [
            'marked_all_read' => 'Đã đánh dấu tất cả thông báo là đã đọc'
        ]
    ],
    
    // User translations
    'user/roles.php' => [
        'en' => [
            'admin' => 'Administrator'
        ],
        'vi' => [
            'admin' => 'Quản trị viên'
        ]
    ],
    
    // Content translations
    'content/pages.php' => [
        'en' => [
            'community_rules' => 'Community Rules',
            'about_us' => 'About Us',
            'terms_of_service' => 'Terms of Service',
            'privacy_policy' => 'Privacy Policy',
            'contact' => 'Contact'
        ],
        'vi' => [
            'community_rules' => 'Quy tắc cộng đồng',
            'about_us' => 'Về chúng tôi',
            'terms_of_service' => 'Điều khoản dịch vụ',
            'privacy_policy' => 'Chính sách bảo mật',
            'contact' => 'Liên hệ'
        ]
    ],
    
    // Forum translations
    'forum/threads.php' => [
        'en' => [
            'sticky' => 'Sticky',
            'locked' => 'Locked'
        ],
        'vi' => [
            'sticky' => 'Ghim',
            'locked' => 'Khóa'
        ]
    ],
    
    'forum/poll.php' => [
        'en' => [
            'votes' => 'Votes',
            'closed' => 'Poll Closed',
            'vote' => 'Vote',
            'view_results' => 'View Results',
            'total_votes' => 'Total Votes',
            'change_vote' => 'Change Vote',
            'update_vote' => 'Update Vote',
            'voters' => 'Voters'
        ],
        'vi' => [
            'votes' => 'Lượt bình chọn',
            'closed' => 'Cuộc bình chọn đã đóng',
            'vote' => 'Bình chọn',
            'view_results' => 'Xem kết quả',
            'total_votes' => 'Tổng số phiếu',
            'change_vote' => 'Thay đổi phiếu bầu',
            'update_vote' => 'Cập nhật phiếu bầu',
            'voters' => 'Người bình chọn'
        ]
    ],
    
    // UI Common translations
    'ui/common.php' => [
        'en' => [
            'replies' => 'Replies',
            'loading' => 'Loading...'
        ],
        'vi' => [
            'replies' => 'Phản hồi',
            'loading' => 'Đang tải...'
        ]
    ],
    
    // Common translations
    'common.php' => [
        'en' => [
            'cancel' => 'Cancel'
        ],
        'vi' => [
            'cancel' => 'Hủy'
        ]
    ]
];

echo "🔧 APPLYING FILE REPLACEMENTS...\n";
echo "================================\n";

foreach ($criticalFixes as $fix) {
    if ($fix['type'] === 'file_replacement') {
        $filePath = $basePath . '/' . $fix['file'];
        
        if (!file_exists($filePath)) {
            echo "⚠️ File not found: {$fix['file']}\n";
            continue;
        }
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        $content = str_replace($fix['old'], $fix['new'], $content);
        
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            echo "✅ {$fix['description']}\n";
            echo "   File: {$fix['file']}\n";
            echo "   Changed: {$fix['old']} → {$fix['new']}\n\n";
        } else {
            echo "⚠️ No changes needed for: {$fix['file']}\n\n";
        }
    }
}

echo "📁 CREATING MISSING TRANSLATION FILES...\n";
echo "========================================\n";

$createdFiles = 0;
$updatedFiles = 0;

foreach ($missingTranslations as $filePath => $localeData) {
    foreach ($localeData as $locale => $translations) {
        $fullPath = $basePath . "/resources/lang/$locale/$filePath";
        $dirPath = dirname($fullPath);
        
        // Create directory if needed
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
            echo "📁 Created directory: " . str_replace($basePath . '/', '', $dirPath) . "\n";
        }
        
        // Load existing translations
        $existingTranslations = [];
        if (file_exists($fullPath)) {
            $existingTranslations = include $fullPath;
            if (!is_array($existingTranslations)) {
                $existingTranslations = [];
            }
        }
        
        // Merge translations
        $mergedTranslations = array_merge($existingTranslations, $translations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * " . ucfirst(str_replace(['/', '.php'], [' ', ''], $filePath)) . " translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($fullPath, $fileContent);
        
        if (count($existingTranslations) > 0) {
            echo "✅ Updated: $locale/$filePath (" . count($translations) . " new translations)\n";
            $updatedFiles++;
        } else {
            echo "✅ Created: $locale/$filePath (" . count($translations) . " translations)\n";
            $createdFiles++;
        }
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "File replacements: " . count($criticalFixes) . "\n";
echo "Files created: $createdFiles\n";
echo "Files updated: $updatedFiles\n";

// Clear caches
echo "\n🧹 CLEARING CACHES...\n";
echo "=====================\n";

$commands = [
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan config:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = shell_exec("cd $basePath && $command 2>&1");
    echo "   " . trim($output) . "\n";
}

echo "\n🧪 TESTING CRITICAL KEYS...\n";
echo "===========================\n";

$testKeys = [
    ['function' => 't_core', 'key' => 'notifications.marked_all_read'],
    ['function' => 't_ui', 'key' => 'auth.login_to_view_notifications'],
    ['function' => 't_user', 'key' => 'roles.admin'],
    ['function' => 't_content', 'key' => 'pages.about_us'],
    ['function' => '__', 'key' => 'forum.threads.sticky'],
    ['function' => '__', 'key' => 'forum.poll.votes'],
    ['function' => '__', 'key' => 'ui.common.replies'],
    ['function' => '__', 'key' => 'common.cancel'],
];

$workingCount = 0;
foreach ($testKeys as $test) {
    try {
        if ($test['function'] === '__') {
            $result = __($test['key']);
        } else {
            $result = call_user_func($test['function'], $test['key']);
        }
        
        $expectedKey = $test['function'] === '__' ? $test['key'] : 
                      (str_replace('t_', '', $test['function']) . '/' . str_replace('.', '/', $test['key']));
        
        if ($result !== $expectedKey && $result !== $test['key']) {
            echo "✅ {$test['function']}('{$test['key']}') → '$result'\n";
            $workingCount++;
        } else {
            echo "❌ {$test['function']}('{$test['key']}') - Still not working\n";
        }
    } catch (Exception $e) {
        echo "❌ {$test['function']}('{$test['key']}') - Error: " . $e->getMessage() . "\n";
    }
}

echo "\n📈 IMPROVEMENT\n";
echo "==============\n";
echo "Critical keys tested: " . count($testKeys) . "\n";
echo "Now working: $workingCount\n";
echo "Success rate: " . round(($workingCount / count($testKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Fixed critical translation issues\n";
echo "2. 🔄 Run comprehensive validation again\n";
echo "3. 🔄 Test affected pages in browser\n";
echo "4. 🔄 Continue with remaining failing keys\n";
echo "5. 🔄 Document the translation structure\n";
