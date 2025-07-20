<?php
/**
 * Process Remaining Direct Translation Keys
 * Strategy để xử lý 2,000+ direct keys còn lại một cách có hệ thống
 */

echo "🚀 PROCESSING REMAINING DIRECT TRANSLATION KEYS\n";
echo "===============================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/direct_keys_analysis.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found. Please run analyze_direct_translation_keys.php first.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

echo "📊 REMAINING KEYS OVERVIEW\n";
echo "==========================\n";
echo "Total direct keys: " . $analysis['total_keys'] . "\n";
echo "Current sample success rate: " . $analysis['sample_test_results']['success_rate'] . "%\n\n";

// Load current validation to see what's already working
$validationFile = $basePath . '/storage/localization/validation_report.json';
$workingKeys = [];
if (file_exists($validationFile)) {
    $validation = json_decode(file_get_contents($validationFile), true);
    foreach ($validation['working_keys'] as $key) {
        if ($key['function'] === '__') {
            $workingKeys[] = $key['key'];
        }
    }
}

echo "📋 STRATEGY: BATCH PROCESSING\n";
echo "=============================\n";
echo "Working keys already: " . count($workingKeys) . "\n";
echo "Remaining to process: ~" . ($analysis['total_keys'] - count($workingKeys)) . "\n\n";

// Categorize keys by priority and pattern
$keyCategories = [
    'ui_keys' => [],
    'forum_keys' => [],
    'auth_keys' => [],
    'admin_keys' => [],
    'content_keys' => [],
    'common_keys' => [],
    'simple_vietnamese' => [],
    'other_keys' => []
];

// Load all direct keys from analysis
$allDirectKeys = $analysis['detailed_patterns']['dot_notation'] ?? [];
$allDirectKeys = array_merge($allDirectKeys, $analysis['detailed_patterns']['slash_notation'] ?? []);
$allDirectKeys = array_merge($allDirectKeys, $analysis['detailed_patterns']['simple_keys'] ?? []);

foreach ($allDirectKeys as $key => $files) {
    // Skip already working keys
    if (in_array($key, $workingKeys)) continue;
    
    // Categorize by prefix/pattern
    if (strpos($key, 'ui.') === 0 || strpos($key, 'ui/') === 0) {
        $keyCategories['ui_keys'][$key] = $files;
    } elseif (strpos($key, 'forum.') === 0 || strpos($key, 'forum/') === 0) {
        $keyCategories['forum_keys'][$key] = $files;
    } elseif (strpos($key, 'auth.') === 0 || strpos($key, 'auth/') === 0) {
        $keyCategories['auth_keys'][$key] = $files;
    } elseif (strpos($key, 'admin.') === 0 || strpos($key, 'admin/') === 0) {
        $keyCategories['admin_keys'][$key] = $files;
    } elseif (strpos($key, 'content.') === 0 || strpos($key, 'content/') === 0) {
        $keyCategories['content_keys'][$key] = $files;
    } elseif (strpos($key, 'common.') === 0 || strpos($key, 'common/') === 0) {
        $keyCategories['common_keys'][$key] = $files;
    } elseif (preg_match('/^[A-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪỬỮỰỲỴỶỸ]/u', $key)) {
        // Vietnamese text keys
        $keyCategories['simple_vietnamese'][$key] = $files;
    } else {
        $keyCategories['other_keys'][$key] = $files;
    }
}

echo "📊 CATEGORIZATION RESULTS\n";
echo "=========================\n";
foreach ($keyCategories as $category => $keys) {
    echo "🔸 " . ucfirst(str_replace('_', ' ', $category)) . ": " . count($keys) . " keys\n";
}

echo "\n🎯 PROCESSING PLAN\n";
echo "==================\n";
echo "Phase 1: UI keys (highest user visibility)\n";
echo "Phase 2: Admin keys (you have admin/users.php open)\n";
echo "Phase 3: Forum keys (core functionality)\n";
echo "Phase 4: Auth keys (user experience)\n";
echo "Phase 5: Content keys (static pages)\n";
echo "Phase 6: Simple Vietnamese keys\n";
echo "Phase 7: Other keys\n\n";

// Ask user which phase to start with
echo "🤔 WHICH PHASE TO START?\n";
echo "========================\n";
echo "1. UI keys (" . count($keyCategories['ui_keys']) . " keys) - Highest visibility\n";
echo "2. Admin keys (" . count($keyCategories['admin_keys']) . " keys) - You have admin/users.php open\n";
echo "3. Forum keys (" . count($keyCategories['forum_keys']) . " keys) - Core functionality\n";
echo "4. Simple Vietnamese (" . count($keyCategories['simple_vietnamese']) . " keys) - Quick wins\n";
echo "5. All phases automatically (will take longer)\n\n";

// For now, let's start with Admin keys since user has admin/users.php open
echo "🔧 STARTING WITH ADMIN KEYS (Phase 2)\n";
echo "=====================================\n";

$adminKeys = $keyCategories['admin_keys'];
echo "Processing " . count($adminKeys) . " admin keys...\n\n";

// Analyze admin keys patterns
$adminPatterns = [];
foreach ($adminKeys as $key => $files) {
    // Extract pattern
    if (preg_match('/^admin\.([^.]+)\.(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($adminPatterns[$section])) {
            $adminPatterns[$section] = [];
        }
        $adminPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    }
}

echo "📋 ADMIN KEY PATTERNS\n";
echo "=====================\n";
foreach ($adminPatterns as $section => $keys) {
    echo "🔸 admin.$section: " . count($keys) . " keys\n";
    
    // Show examples
    $examples = array_slice(array_keys($keys), 0, 3);
    foreach ($examples as $example) {
        echo "   - admin.$section.$example\n";
    }
    if (count($keys) > 3) {
        echo "   - ... and " . (count($keys) - 3) . " more\n";
    }
    echo "\n";
}

// Generate admin translations based on patterns
$adminTranslations = [];

// Common admin translations
$commonAdminTranslations = [
    'users' => [
        'title' => ['en' => 'Users', 'vi' => 'Người dùng'],
        'list' => ['en' => 'User List', 'vi' => 'Danh sách người dùng'],
        'create' => ['en' => 'Create User', 'vi' => 'Tạo người dùng'],
        'edit' => ['en' => 'Edit User', 'vi' => 'Chỉnh sửa người dùng'],
        'delete' => ['en' => 'Delete User', 'vi' => 'Xóa người dùng'],
        'profile' => ['en' => 'User Profile', 'vi' => 'Hồ sơ người dùng'],
        'permissions' => ['en' => 'Permissions', 'vi' => 'Quyền hạn'],
        'roles' => ['en' => 'Roles', 'vi' => 'Vai trò'],
        'status' => ['en' => 'Status', 'vi' => 'Trạng thái'],
        'active' => ['en' => 'Active', 'vi' => 'Hoạt động'],
        'inactive' => ['en' => 'Inactive', 'vi' => 'Không hoạt động'],
        'banned' => ['en' => 'Banned', 'vi' => 'Bị cấm'],
        'email_verified' => ['en' => 'Email Verified', 'vi' => 'Email đã xác thực'],
        'last_login' => ['en' => 'Last Login', 'vi' => 'Đăng nhập cuối'],
        'registration_date' => ['en' => 'Registration Date', 'vi' => 'Ngày đăng ký'],
    ],
    'dashboard' => [
        'title' => ['en' => 'Dashboard', 'vi' => 'Bảng điều khiển'],
        'overview' => ['en' => 'Overview', 'vi' => 'Tổng quan'],
        'statistics' => ['en' => 'Statistics', 'vi' => 'Thống kê'],
        'recent_activity' => ['en' => 'Recent Activity', 'vi' => 'Hoạt động gần đây'],
    ],
    'system' => [
        'title' => ['en' => 'System', 'vi' => 'Hệ thống'],
        'settings' => ['en' => 'Settings', 'vi' => 'Cài đặt'],
        'configuration' => ['en' => 'Configuration', 'vi' => 'Cấu hình'],
        'maintenance' => ['en' => 'Maintenance', 'vi' => 'Bảo trì'],
        'logs' => ['en' => 'Logs', 'vi' => 'Nhật ký'],
    ]
];

// Create translation files for admin sections
foreach ($commonAdminTranslations as $section => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/admin/$section.php";
        $dirPath = dirname($filePath);
        
        // Create directory if needed
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
            echo "📁 Created directory: " . str_replace($basePath . '/', '', $dirPath) . "\n";
        }
        
        // Load existing translations
        $existingTranslations = [];
        if (file_exists($filePath)) {
            $existingTranslations = include $filePath;
            if (!is_array($existingTranslations)) {
                $existingTranslations = [];
            }
        }
        
        // Add new translations
        $newTranslations = [];
        foreach ($translations as $key => $localeTranslations) {
            $newTranslations[$key] = $localeTranslations[$locale];
        }
        
        // Merge with existing
        $mergedTranslations = array_merge($existingTranslations, $newTranslations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * Admin $section translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        echo "✅ Updated: $locale/admin/$section.php (" . count($newTranslations) . " translations)\n";
    }
}

echo "\n🧪 TESTING ADMIN KEYS...\n";
echo "========================\n";

$testAdminKeys = [
    'admin.users.title',
    'admin.users.list', 
    'admin.users.create',
    'admin.users.permissions',
    'admin.dashboard.title',
    'admin.system.settings'
];

$workingCount = 0;
foreach ($testAdminKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Not found\n";
    }
}

echo "\nAdmin keys success rate: " . round(($workingCount / count($testAdminKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Started with admin keys\n";
echo "2. 🔄 Continue with UI keys (highest visibility)\n";
echo "3. 🔄 Process forum keys (core functionality)\n";
echo "4. 🔄 Handle simple Vietnamese keys\n";
echo "5. 🔄 Run comprehensive validation\n\n";

echo "💡 RECOMMENDATION\n";
echo "=================\n";
echo "Focus on one category at a time for maximum efficiency.\n";
echo "Admin keys are a good start since you have admin/users.php open.\n";
echo "Next, tackle UI keys for maximum user impact.\n";
