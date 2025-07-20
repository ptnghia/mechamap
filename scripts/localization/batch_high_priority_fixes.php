<?php
/**
 * Batch High Priority Localization Fixes
 * Process remaining HIGH priority directories quickly
 */

echo "🚀 BATCH HIGH PRIORITY LOCALIZATION FIXES\n";
echo "==========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langNewPath = $basePath . '/resources/lang_new';

// Remaining HIGH priority directories to process
$highPriorityDirs = [
    'user' => [
        'category' => 'user',
        'fixes' => [
            'Đăng xuất' => 'actions.logout',
            'Cài đặt' => 'navigation.settings',
            'Hồ sơ' => 'navigation.profile',
            'Dashboard' => 'navigation.dashboard',
            'Thông báo' => 'navigation.notifications',
            'Tin nhắn' => 'navigation.messages',
            'Hoạt động' => 'labels.activity',
            'Thống kê' => 'labels.statistics',
            'Báo cáo' => 'labels.reports'
        ]
    ],
    'forums' => [
        'category' => 'features',
        'fixes' => [
            'Tạo chủ đề mới' => 'actions.create_new_topic',
            'Trả lời' => 'actions.reply',
            'Chỉnh sửa' => 'actions.edit',
            'Xóa' => 'actions.delete',
            'Báo cáo' => 'actions.report',
            'Theo dõi' => 'actions.follow',
            'Chia sẻ' => 'actions.share',
            'Thích' => 'actions.like',
            'Không thích' => 'actions.dislike',
            'Lưu' => 'actions.save'
        ]
    ],
    'layouts' => [
        'category' => 'ui',
        'fixes' => [
            'Trang chủ' => 'navigation.home',
            'Diễn đàn' => 'navigation.forums',
            'Thị trường' => 'navigation.marketplace',
            'Cộng đồng' => 'navigation.community',
            'Hỗ trợ' => 'navigation.support',
            'Liên hệ' => 'navigation.contact',
            'Về chúng tôi' => 'navigation.about',
            'Điều khoản' => 'navigation.terms',
            'Chính sách' => 'navigation.privacy'
        ]
    ],
    'partials' => [
        'category' => 'ui',
        'fixes' => [
            'Tìm kiếm...' => 'placeholders.search',
            'Nhập từ khóa...' => 'placeholders.enter_keywords',
            'Chọn danh mục...' => 'placeholders.select_category',
            'Tất cả' => 'options.all',
            'Mới nhất' => 'options.newest',
            'Phổ biến' => 'options.popular',
            'Xem tất cả' => 'actions.view_all'
        ]
    ]
];

$totalProcessed = 0;
$totalKeys = 0;
$totalFiles = 0;

foreach ($highPriorityDirs as $directory => $config) {
    echo "🎯 Processing HIGH PRIORITY: $directory\n";
    echo "----------------------------------------\n";
    
    // Run improved audit
    echo "   🔍 Running audit...\n";
    $auditCommand = "php scripts/localization/improved_blade_audit.php $directory";
    exec($auditCommand, $auditOutput, $auditReturn);
    
    if ($auditReturn === 0) {
        echo "   ✅ Audit completed\n";
    } else {
        echo "   ❌ Audit failed\n";
        continue;
    }
    
    // Create translation keys
    echo "   🔑 Creating translation keys...\n";
    $keysCreated = createDirectoryKeys($directory, $config, $langNewPath);
    $totalKeys += $keysCreated;
    
    // Apply fixes to Blade files
    echo "   🔧 Applying fixes to Blade files...\n";
    $filesFixed = applyDirectoryFixes($directory, $config, $basePath);
    $totalFiles += $filesFixed;
    
    $totalProcessed++;
    echo "   ✅ $directory completed ($keysCreated keys, $filesFixed files)\n\n";
}

echo "🎉 BATCH HIGH PRIORITY PROCESSING COMPLETED!\n";
echo "=============================================\n";
echo "📊 Directories processed: $totalProcessed\n";
echo "📊 Total translation keys created: $totalKeys\n";
echo "📊 Total files fixed: $totalFiles\n\n";

// Generate summary report
generateBatchSummaryReport($highPriorityDirs, $totalProcessed, $totalKeys, $totalFiles);

echo "📊 Summary report: storage/localization/batch_high_priority_summary.md\n";

function createDirectoryKeys($directory, $config, $langNewPath) {
    $category = $config['category'];
    $fixes = $config['fixes'];
    
    $keysBySubcategory = [];
    
    // Group keys by subcategory
    foreach ($fixes as $text => $key) {
        $parts = explode('.', $key);
        $subcategory = $parts[0];
        $keyName = $parts[1];
        
        if (!isset($keysBySubcategory[$subcategory])) {
            $keysBySubcategory[$subcategory] = [];
        }
        $keysBySubcategory[$subcategory][$keyName] = $text;
    }
    
    $keysCreated = 0;
    
    // Create keys in appropriate category
    foreach ($keysBySubcategory as $subcategory => $keys) {
        createKeysInFile($category, $directory, $subcategory, $keys, 'vi', $langNewPath);
        createKeysInFile($category, $directory, $subcategory, $keys, 'en', $langNewPath);
        $keysCreated += count($keys);
    }
    
    return $keysCreated;
}

function createKeysInFile($category, $directory, $subcategory, $keys, $lang, $langNewPath) {
    $filePath = "$langNewPath/$lang/$category/$directory.php";
    
    // Load existing translations
    $translations = [];
    if (file_exists($filePath)) {
        $translations = include $filePath;
        if (!is_array($translations)) {
            $translations = [];
        }
    }
    
    // Initialize subcategory if not exists
    if (!isset($translations[$subcategory])) {
        $translations[$subcategory] = [];
    }
    
    // Ensure subcategory is array
    if (!is_array($translations[$subcategory])) {
        $translations[$subcategory] = [];
    }
    
    // Add new keys
    foreach ($keys as $keyName => $viText) {
        if (!isset($translations[$subcategory][$keyName])) {
            if ($lang === 'vi') {
                $translations[$subcategory][$keyName] = $viText;
            } else {
                $translations[$subcategory][$keyName] = generateEnglishTranslation($viText);
            }
        }
    }
    
    // Sort keys
    ksort($translations);
    foreach ($translations as &$subArray) {
        if (is_array($subArray)) {
            ksort($subArray);
        }
    }
    
    // Generate file content
    $content = generateFileContent($category, $directory, $translations, $lang);
    
    // Ensure directory exists
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Write file
    file_put_contents($filePath, $content);
}

function generateEnglishTranslation($viText) {
    $translations = [
        'Đăng xuất' => 'Logout',
        'Cài đặt' => 'Settings',
        'Hồ sơ' => 'Profile',
        'Dashboard' => 'Dashboard',
        'Thông báo' => 'Notifications',
        'Tin nhắn' => 'Messages',
        'Hoạt động' => 'Activity',
        'Thống kê' => 'Statistics',
        'Báo cáo' => 'Reports',
        'Tạo chủ đề mới' => 'Create new topic',
        'Trả lời' => 'Reply',
        'Chỉnh sửa' => 'Edit',
        'Xóa' => 'Delete',
        'Báo cáo' => 'Report',
        'Theo dõi' => 'Follow',
        'Chia sẻ' => 'Share',
        'Thích' => 'Like',
        'Không thích' => 'Dislike',
        'Lưu' => 'Save',
        'Trang chủ' => 'Home',
        'Diễn đàn' => 'Forums',
        'Thị trường' => 'Marketplace',
        'Cộng đồng' => 'Community',
        'Hỗ trợ' => 'Support',
        'Liên hệ' => 'Contact',
        'Về chúng tôi' => 'About us',
        'Điều khoản' => 'Terms',
        'Chính sách' => 'Privacy',
        'Tìm kiếm...' => 'Search...',
        'Nhập từ khóa...' => 'Enter keywords...',
        'Chọn danh mục...' => 'Select category...',
        'Tất cả' => 'All',
        'Mới nhất' => 'Newest',
        'Phổ biến' => 'Popular',
        'Xem tất cả' => 'View all'
    ];
    
    return $translations[$viText] ?? $viText;
}

function generateFileContent($category, $directory, $translations, $lang) {
    $langName = $lang === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$directory\n";
    $content .= " * Batch high priority fixes - Updated: " . date('Y-m-d H:i:s') . "\n";
    $content .= " * Total keys: " . countNestedKeys($translations) . "\n";
    $content .= " */\n\n";
    $content .= "return " . arrayToString($translations, 0) . ";\n";
    
    return $content;
}

function countNestedKeys($array) {
    $count = 0;
    foreach ($array as $value) {
        if (is_array($value)) {
            $count += count($value);
        } else {
            $count++;
        }
    }
    return $count;
}

function arrayToString($array, $indent = 0) {
    if (empty($array)) {
        return '[]';
    }
    
    $spaces = str_repeat('    ', $indent);
    $result = "[\n";
    
    foreach ($array as $key => $value) {
        $result .= $spaces . "    ";
        
        if (is_string($key)) {
            $result .= "'" . addslashes($key) . "' => ";
        }
        
        if (is_array($value)) {
            $result .= arrayToString($value, $indent + 1);
        } else {
            $result .= "'" . addslashes($value) . "'";
        }
        
        $result .= ",\n";
    }
    
    $result .= $spaces . "]";
    return $result;
}

function applyDirectoryFixes($directory, $config, $basePath) {
    $dirPath = $basePath . '/resources/views/' . $directory;
    $category = $config['category'];
    $fixes = $config['fixes'];
    
    if (!is_dir($dirPath)) {
        return 0;
    }
    
    // Create backup
    $backupDir = $basePath . '/storage/localization/' . $directory . '_backup_' . date('Y_m_d_H_i_s');
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    copyDirectory($dirPath, $backupDir);
    
    // Find blade files
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && 
            strpos($file->getFilename(), '.blade.') !== false) {
            $bladeFiles[] = $file->getPathname();
        }
    }
    
    $fixedFiles = 0;
    
    // Apply fixes
    foreach ($bladeFiles as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;
        $replacements = 0;
        
        foreach ($fixes as $text => $key) {
            $parts = explode('.', $key);
            $helperCall = "{{ t_" . $category . "('" . $directory . "." . $parts[0] . "." . $parts[1] . "') }}";
            
            $patterns = [
                '/["\']' . preg_quote($text, '/') . '["\']/',
                '/>' . preg_quote($text, '/') . '</',
            ];
            
            foreach ($patterns as $pattern) {
                $newContent = preg_replace($pattern, $helperCall, $content, 1);
                if ($newContent !== $content) {
                    $content = $newContent;
                    $replacements++;
                    break;
                }
            }
        }
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $fixedFiles++;
        }
    }
    
    return $fixedFiles;
}

function copyDirectory($source, $destination) {
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            copy($item, $target);
        }
    }
}

function generateBatchSummaryReport($dirs, $totalProcessed, $totalKeys, $totalFiles) {
    $report = "# Batch High Priority Localization - Summary Report\n\n";
    $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
    $report .= "**Directories processed:** $totalProcessed\n";
    $report .= "**Total translation keys created:** $totalKeys\n";
    $report .= "**Total files fixed:** $totalFiles\n\n";
    
    $report .= "## 🎯 Processed Directories\n\n";
    foreach ($dirs as $dir => $config) {
        $report .= "### $dir ({$config['category']} category)\n";
        $report .= "- Keys created: " . count($config['fixes']) . "\n";
        $report .= "- Category: {$config['category']}\n\n";
    }
    
    $report .= "## ✅ Status: HIGH PRIORITY BATCH COMPLETED\n\n";
    $report .= "All high priority directories have been processed with localization fixes applied.\n";
    
    file_put_contents('storage/localization/batch_high_priority_summary.md', $report);
}
