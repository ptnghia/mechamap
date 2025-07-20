<?php
/**
 * Fix Most Used Direct Translation Keys
 * Phase 1: Sửa top 100 most-used keys để có impact lớn nhất
 */

echo "🔧 FIXING MOST USED DIRECT TRANSLATION KEYS\n";
echo "===========================================\n\n";

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

echo "📊 ANALYSIS OVERVIEW\n";
echo "===================\n";
echo "Total direct keys: " . $analysis['total_keys'] . "\n";
echo "Most used keys available: " . count($analysis['most_used_keys']) . "\n\n";

// Define translations for most used keys
$mostUsedTranslations = [
    // Vietnamese keys that are already translated (simple keys)
    'Hủy' => [
        'target_file' => 'common.php',
        'key' => 'cancel',
        'translations' => [
            'en' => 'Cancel',
            'vi' => 'Hủy'
        ]
    ],
    'Xóa' => [
        'target_file' => 'common.php', 
        'key' => 'delete',
        'translations' => [
            'en' => 'Delete',
            'vi' => 'Xóa'
        ]
    ],
    'Trạng thái' => [
        'target_file' => 'common.php',
        'key' => 'status',
        'translations' => [
            'en' => 'Status',
            'vi' => 'Trạng thái'
        ]
    ],
    'Thao tác' => [
        'target_file' => 'common.php',
        'key' => 'actions',
        'translations' => [
            'en' => 'Actions',
            'vi' => 'Thao tác'
        ]
    ],
    'Thứ tự' => [
        'target_file' => 'common.php',
        'key' => 'order',
        'translations' => [
            'en' => 'Order',
            'vi' => 'Thứ tự'
        ]
    ],
    'Chỉnh sửa' => [
        'target_file' => 'common.php',
        'key' => 'edit',
        'translations' => [
            'en' => 'Edit',
            'vi' => 'Chỉnh sửa'
        ]
    ],
    'ID' => [
        'target_file' => 'common.php',
        'key' => 'id',
        'translations' => [
            'en' => 'ID',
            'vi' => 'ID'
        ]
    ],
    'Xác nhận xóa' => [
        'target_file' => 'common.php',
        'key' => 'confirm_delete',
        'translations' => [
            'en' => 'Confirm Delete',
            'vi' => 'Xác nhận xóa'
        ]
    ],
    
    // Dot notation keys that need proper file structure
    'ui.pagination.page' => [
        'target_file' => 'ui/pagination.php',
        'key' => 'page',
        'translations' => [
            'en' => 'Page',
            'vi' => 'Trang'
        ]
    ],
    'add_menu.status.coming_soon' => [
        'target_file' => 'ui/status.php',
        'key' => 'coming_soon',
        'translations' => [
            'en' => 'Coming Soon',
            'vi' => 'Sắp ra mắt'
        ]
    ],
    
    // Forum keys
    'forum.poll.closed' => [
        'target_file' => 'forum.php',
        'key' => 'poll.closed',
        'translations' => [
            'en' => 'Poll Closed',
            'vi' => 'Cuộc bình chọn đã đóng'
        ]
    ],
    'forum.poll.vote' => [
        'target_file' => 'forum.php',
        'key' => 'poll.vote',
        'translations' => [
            'en' => 'Vote',
            'vi' => 'Bình chọn'
        ]
    ],
    'forum.poll.view_results' => [
        'target_file' => 'forum.php',
        'key' => 'poll.view_results',
        'translations' => [
            'en' => 'View Results',
            'vi' => 'Xem kết quả'
        ]
    ],
    'forum.poll.total_votes' => [
        'target_file' => 'forum.php',
        'key' => 'poll.total_votes',
        'translations' => [
            'en' => 'Total Votes',
            'vi' => 'Tổng số phiếu'
        ]
    ],
    'forum.poll.change_vote' => [
        'target_file' => 'forum.php',
        'key' => 'poll.change_vote',
        'translations' => [
            'en' => 'Change Vote',
            'vi' => 'Thay đổi phiếu bầu'
        ]
    ],
    'forum.poll.update_vote' => [
        'target_file' => 'forum.php',
        'key' => 'poll.update_vote',
        'translations' => [
            'en' => 'Update Vote',
            'vi' => 'Cập nhật phiếu bầu'
        ]
    ],
    'forum.poll.voters' => [
        'target_file' => 'forum.php',
        'key' => 'poll.voters',
        'translations' => [
            'en' => 'Voters',
            'vi' => 'Người bình chọn'
        ]
    ],
    'forum.poll.loading_results' => [
        'target_file' => 'forum.php',
        'key' => 'poll.loading_results',
        'translations' => [
            'en' => 'Loading results...',
            'vi' => 'Đang tải kết quả...'
        ]
    ],
    
    // UI keys
    'ui.common.by' => [
        'target_file' => 'ui.php',
        'key' => 'common.by',
        'translations' => [
            'en' => 'By',
            'vi' => 'Bởi'
        ]
    ],
    'ui.actions.view_full_showcase' => [
        'target_file' => 'ui.php',
        'key' => 'actions.view_full_showcase',
        'translations' => [
            'en' => 'View Full Showcase',
            'vi' => 'Xem showcase đầy đủ'
        ]
    ],
    'ui.actions.view_details' => [
        'target_file' => 'ui.php',
        'key' => 'actions.view_details',
        'translations' => [
            'en' => 'View Details',
            'vi' => 'Xem chi tiết'
        ]
    ],
    
    // Showcase keys
    'showcase.related' => [
        'target_file' => 'features/showcase.php',
        'key' => 'related',
        'translations' => [
            'en' => 'Related',
            'vi' => 'Liên quan'
        ]
    ],
    'showcase.for_thread' => [
        'target_file' => 'features/showcase.php',
        'key' => 'for_thread',
        'translations' => [
            'en' => 'For Thread',
            'vi' => 'Cho chủ đề'
        ]
    ],
    'showcase.create_from_thread' => [
        'target_file' => 'features/showcase.php',
        'key' => 'create_from_thread',
        'translations' => [
            'en' => 'Create from Thread',
            'vi' => 'Tạo từ chủ đề'
        ]
    ],
    'showcase.create_showcase_info' => [
        'target_file' => 'features/showcase.php',
        'key' => 'create_showcase_info',
        'translations' => [
            'en' => 'Create Showcase Info',
            'vi' => 'Tạo thông tin showcase'
        ]
    ],
];

echo "🔧 CREATING TRANSLATION FILES...\n";
echo "================================\n";

$filesToUpdate = [];
$totalTranslations = 0;

// Group translations by file
foreach ($mostUsedTranslations as $originalKey => $config) {
    $targetFile = $config['target_file'];
    $key = $config['key'];
    $translations = $config['translations'];
    
    if (!isset($filesToUpdate[$targetFile])) {
        $filesToUpdate[$targetFile] = [];
    }
    
    $filesToUpdate[$targetFile][$key] = $translations;
    $totalTranslations++;
}

$createdFiles = 0;
$updatedFiles = 0;

foreach ($filesToUpdate as $fileName => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/$fileName";
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
        
        // Merge with existing, handling nested keys
        $mergedTranslations = $existingTranslations;
        foreach ($newTranslations as $key => $value) {
            if (strpos($key, '.') !== false) {
                // Nested key
                $keyParts = explode('.', $key);
                $current = &$mergedTranslations;
                
                foreach ($keyParts as $i => $part) {
                    if ($i === count($keyParts) - 1) {
                        $current[$part] = $value;
                    } else {
                        if (!isset($current[$part]) || !is_array($current[$part])) {
                            $current[$part] = [];
                        }
                        $current = &$current[$part];
                    }
                }
            } else {
                // Simple key
                $mergedTranslations[$key] = $value;
            }
        }
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * " . ucfirst(str_replace(['/', '.php'], [' ', ''], $fileName)) . " translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        if (count($existingTranslations) > 0) {
            echo "✅ Updated: $locale/$fileName (" . count($newTranslations) . " new translations)\n";
            $updatedFiles++;
        } else {
            echo "✅ Created: $locale/$fileName (" . count($newTranslations) . " translations)\n";
            $createdFiles++;
        }
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Translation entries processed: $totalTranslations\n";
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

echo "\n🧪 TESTING FIXED KEYS...\n";
echo "========================\n";

$testKeys = array_keys($mostUsedTranslations);
$workingCount = 0;

foreach (array_slice($testKeys, 0, 15) as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Still not working\n";
    }
}

echo "\n📈 IMPROVEMENT\n";
echo "==============\n";
echo "Keys tested: " . min(15, count($testKeys)) . "\n";
echo "Now working: $workingCount\n";
echo "Success rate: " . round(($workingCount / min(15, count($testKeys))) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Fixed most used direct translation keys\n";
echo "2. 🔄 Continue with UI keys (285 keys)\n";
echo "3. 🔄 Fix forum and auth keys\n";
echo "4. 🔄 Run comprehensive validation\n";
echo "5. 🔄 Test in browser\n";
