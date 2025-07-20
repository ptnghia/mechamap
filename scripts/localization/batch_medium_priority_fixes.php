<?php
/**
 * Batch Medium Priority Localization Fixes
 * Process MEDIUM priority directories systematically
 */

echo "🚀 BATCH MEDIUM PRIORITY LOCALIZATION FIXES\n";
echo "============================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langNewPath = $basePath . '/resources/lang_new';

// MEDIUM priority directories to process
$mediumPriorityDirs = [
    'emails' => [
        'category' => 'content',
        'fixes' => [
            'Xin chào' => 'greetings.hello',
            'Cảm ơn' => 'greetings.thank_you',
            'Trân trọng' => 'greetings.best_regards',
            'Thông báo' => 'labels.notification',
            'Xác nhận' => 'labels.confirmation',
            'Kích hoạt tài khoản' => 'actions.activate_account',
            'Đặt lại mật khẩu' => 'actions.reset_password',
            'Xác thực email' => 'actions.verify_email',
            'Liên hệ hỗ trợ' => 'actions.contact_support'
        ]
    ],
    'vendor' => [
        'category' => 'ui',
        'fixes' => [
            'Trang trước' => 'pagination.previous',
            'Trang sau' => 'pagination.next',
            'Trang đầu' => 'pagination.first',
            'Trang cuối' => 'pagination.last',
            'Hiển thị' => 'pagination.showing',
            'kết quả' => 'pagination.results',
            'của' => 'pagination.of',
            'tổng cộng' => 'pagination.total'
        ]
    ],
    'whats-new' => [
        'category' => 'content',
        'fixes' => [
            'Có gì mới' => 'titles.whats_new',
            'Xu hướng' => 'labels.trending',
            'Nổi bật' => 'labels.featured',
            'Mới nhất' => 'labels.latest',
            'Phổ biến' => 'labels.popular',
            'Chủ đề nóng' => 'labels.hot_topics',
            'Xem thêm' => 'actions.view_more',
            'Tất cả bài viết' => 'actions.all_posts'
        ]
    ],
    'pages' => [
        'category' => 'content',
        'fixes' => [
            'Về chúng tôi' => 'pages.about_us',
            'Liên hệ' => 'pages.contact',
            'Điều khoản sử dụng' => 'pages.terms_of_service',
            'Chính sách bảo mật' => 'pages.privacy_policy',
            'Câu hỏi thường gặp' => 'pages.faq',
            'Hướng dẫn' => 'pages.guide',
            'Hỗ trợ' => 'pages.support',
            'Quy tắc cộng đồng' => 'pages.community_rules'
        ]
    ],
    'supplier' => [
        'category' => 'features',
        'fixes' => [
            'Nhà cung cấp' => 'labels.supplier',
            'Sản phẩm' => 'labels.products',
            'Đơn hàng' => 'labels.orders',
            'Thống kê' => 'labels.statistics',
            'Doanh thu' => 'labels.revenue',
            'Khách hàng' => 'labels.customers',
            'Quản lý kho' => 'labels.inventory_management',
            'Báo cáo' => 'labels.reports'
        ]
    ],
    'community' => [
        'category' => 'features',
        'fixes' => [
            'Cộng đồng' => 'labels.community',
            'Sự kiện' => 'labels.events',
            'Công ty' => 'labels.companies',
            'Việc làm' => 'labels.jobs',
            'Mạng lưới' => 'labels.network',
            'Kết nối' => 'actions.connect',
            'Tham gia' => 'actions.join',
            'Chia sẻ' => 'actions.share'
        ]
    ],
    'threads' => [
        'category' => 'features',
        'fixes' => [
            'Chủ đề' => 'labels.thread',
            'Bài viết' => 'labels.post',
            'Trả lời' => 'actions.reply',
            'Chỉnh sửa' => 'actions.edit',
            'Xóa' => 'actions.delete',
            'Báo cáo' => 'actions.report',
            'Theo dõi' => 'actions.follow',
            'Bỏ theo dõi' => 'actions.unfollow'
        ]
    ]
];

$totalProcessed = 0;
$totalKeys = 0;
$totalFiles = 0;

foreach ($mediumPriorityDirs as $directory => $config) {
    echo "🎯 Processing MEDIUM PRIORITY: $directory\n";
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

// Process root level files
echo "🎯 Processing ROOT LEVEL FILES\n";
echo "------------------------------\n";
$rootConfig = [
    'category' => 'content',
    'fixes' => [
        'Chào mừng' => 'welcome.greeting',
        'Trang chủ' => 'navigation.home',
        'Dashboard' => 'navigation.dashboard',
        'Sắp ra mắt' => 'status.coming_soon',
        'Đang phát triển' => 'status.under_development',
        'Vui lòng quay lại sau' => 'messages.please_come_back_later'
    ]
];

echo "   🔍 Processing root files...\n";
$rootKeysCreated = createRootKeys($rootConfig, $langNewPath);
$rootFilesFixed = applyRootFixes($rootConfig, $basePath);
$totalKeys += $rootKeysCreated;
$totalFiles += $rootFilesFixed;
$totalProcessed++;

echo "   ✅ Root files completed ($rootKeysCreated keys, $rootFilesFixed files)\n\n";

echo "🎉 BATCH MEDIUM PRIORITY PROCESSING COMPLETED!\n";
echo "===============================================\n";
echo "📊 Directories processed: $totalProcessed\n";
echo "📊 Total translation keys created: $totalKeys\n";
echo "📊 Total files fixed: $totalFiles\n\n";

// Generate summary report
generateMediumPrioritySummaryReport($mediumPriorityDirs, $totalProcessed, $totalKeys, $totalFiles);

echo "📊 Summary report: storage/localization/batch_medium_priority_summary.md\n";

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

function createRootKeys($config, $langNewPath) {
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
    
    // Create keys in content category for root
    foreach ($keysBySubcategory as $subcategory => $keys) {
        createKeysInFile($category, 'root', $subcategory, $keys, 'vi', $langNewPath);
        createKeysInFile($category, 'root', $subcategory, $keys, 'en', $langNewPath);
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
        'Xin chào' => 'Hello',
        'Cảm ơn' => 'Thank you',
        'Trân trọng' => 'Best regards',
        'Thông báo' => 'Notification',
        'Xác nhận' => 'Confirmation',
        'Kích hoạt tài khoản' => 'Activate account',
        'Đặt lại mật khẩu' => 'Reset password',
        'Xác thực email' => 'Verify email',
        'Liên hệ hỗ trợ' => 'Contact support',
        'Trang trước' => 'Previous',
        'Trang sau' => 'Next',
        'Trang đầu' => 'First',
        'Trang cuối' => 'Last',
        'Hiển thị' => 'Showing',
        'kết quả' => 'results',
        'của' => 'of',
        'tổng cộng' => 'total',
        'Có gì mới' => 'What\'s new',
        'Xu hướng' => 'Trending',
        'Nổi bật' => 'Featured',
        'Mới nhất' => 'Latest',
        'Phổ biến' => 'Popular',
        'Chủ đề nóng' => 'Hot topics',
        'Xem thêm' => 'View more',
        'Tất cả bài viết' => 'All posts',
        'Về chúng tôi' => 'About us',
        'Liên hệ' => 'Contact',
        'Điều khoản sử dụng' => 'Terms of service',
        'Chính sách bảo mật' => 'Privacy policy',
        'Câu hỏi thường gặp' => 'FAQ',
        'Hướng dẫn' => 'Guide',
        'Hỗ trợ' => 'Support',
        'Quy tắc cộng đồng' => 'Community rules',
        'Nhà cung cấp' => 'Supplier',
        'Sản phẩm' => 'Products',
        'Đơn hàng' => 'Orders',
        'Thống kê' => 'Statistics',
        'Doanh thu' => 'Revenue',
        'Khách hàng' => 'Customers',
        'Quản lý kho' => 'Inventory management',
        'Báo cáo' => 'Reports',
        'Cộng đồng' => 'Community',
        'Sự kiện' => 'Events',
        'Công ty' => 'Companies',
        'Việc làm' => 'Jobs',
        'Mạng lưới' => 'Network',
        'Kết nối' => 'Connect',
        'Tham gia' => 'Join',
        'Chia sẻ' => 'Share',
        'Chủ đề' => 'Thread',
        'Bài viết' => 'Post',
        'Trả lời' => 'Reply',
        'Chỉnh sửa' => 'Edit',
        'Xóa' => 'Delete',
        'Báo cáo' => 'Report',
        'Theo dõi' => 'Follow',
        'Bỏ theo dõi' => 'Unfollow',
        'Chào mừng' => 'Welcome',
        'Trang chủ' => 'Home',
        'Dashboard' => 'Dashboard',
        'Sắp ra mắt' => 'Coming soon',
        'Đang phát triển' => 'Under development',
        'Vui lòng quay lại sau' => 'Please come back later'
    ];
    
    return $translations[$viText] ?? $viText;
}

function generateFileContent($category, $directory, $translations, $lang) {
    $langName = $lang === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$directory\n";
    $content .= " * Batch medium priority fixes - Updated: " . date('Y-m-d H:i:s') . "\n";
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

function applyRootFixes($config, $basePath) {
    $rootFiles = ['home.blade.php', 'dashboard.blade.php', 'welcome.blade.php', 'coming-soon.blade.php'];
    $category = $config['category'];
    $fixes = $config['fixes'];
    
    // Create backup
    $backupDir = $basePath . '/storage/localization/root_backup_' . date('Y_m_d_H_i_s');
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $fixedFiles = 0;
    
    foreach ($rootFiles as $fileName) {
        $filePath = $basePath . '/resources/views/' . $fileName;
        if (file_exists($filePath)) {
            // Backup
            copy($filePath, $backupDir . '/' . $fileName);
            
            $content = file_get_contents($filePath);
            $originalContent = $content;
            $replacements = 0;
            
            foreach ($fixes as $text => $key) {
                $parts = explode('.', $key);
                $helperCall = "{{ t_" . $category . "('" . "root." . $parts[0] . "." . $parts[1] . "') }}";
                
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
                file_put_contents($filePath, $content);
                $fixedFiles++;
            }
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

function generateMediumPrioritySummaryReport($dirs, $totalProcessed, $totalKeys, $totalFiles) {
    $report = "# Batch Medium Priority Localization - Summary Report\n\n";
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
    
    $report .= "## ✅ Status: MEDIUM PRIORITY BATCH COMPLETED\n\n";
    $report .= "All medium priority directories have been processed with localization fixes applied.\n";
    
    file_put_contents('storage/localization/batch_medium_priority_summary.md', $report);
}
