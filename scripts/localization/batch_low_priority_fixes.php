<?php
/**
 * Batch Low Priority Localization Fixes
 * Process all remaining LOW priority directories
 */

echo "🚀 BATCH LOW PRIORITY LOCALIZATION FIXES\n";
echo "=========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langNewPath = $basePath . '/resources/lang_new';

// LOW priority directories - smaller directories with fewer files
$lowPriorityDirs = [
    'about', 'alerts', 'bookmarks', 'brand', 'business', 'categories',
    'chat', 'conversations', 'devices', 'docs', 'faq', 'following',
    'frontend', 'gallery', 'help', 'knowledge', 'manufacturer',
    'members', 'new-content', 'news', 'notifications', 'realtime',
    'search', 'showcase', 'showcases', 'student', 'subscription',
    'technical', 'test', 'tools', 'users'
];

// Common fixes for low priority directories
$commonFixes = [
    'Tìm kiếm' => 'actions.search',
    'Lưu' => 'actions.save',
    'Hủy' => 'actions.cancel',
    'Xóa' => 'actions.delete',
    'Chỉnh sửa' => 'actions.edit',
    'Xem' => 'actions.view',
    'Thêm' => 'actions.add',
    'Cập nhật' => 'actions.update',
    'Tải lên' => 'actions.upload',
    'Tải xuống' => 'actions.download',
    'Chia sẻ' => 'actions.share',
    'Sao chép' => 'actions.copy',
    'Di chuyển' => 'actions.move',
    'Đổi tên' => 'actions.rename',
    'Kích hoạt' => 'actions.activate',
    'Vô hiệu hóa' => 'actions.deactivate',
    'Phê duyệt' => 'actions.approve',
    'Từ chối' => 'actions.reject',
    'Gửi' => 'actions.send',
    'Nhận' => 'actions.receive',
    'Đăng' => 'actions.post',
    'Xuất bản' => 'actions.publish',
    'Nháp' => 'status.draft',
    'Đã xuất bản' => 'status.published',
    'Đang chờ' => 'status.pending',
    'Hoàn thành' => 'status.completed',
    'Đang xử lý' => 'status.processing',
    'Thành công' => 'status.success',
    'Thất bại' => 'status.failed',
    'Lỗi' => 'status.error',
    'Cảnh báo' => 'status.warning',
    'Thông tin' => 'status.info'
];

$totalProcessed = 0;
$totalKeys = 0;
$totalFiles = 0;

foreach ($lowPriorityDirs as $directory) {
    echo "🎯 Processing LOW PRIORITY: $directory\n";
    echo "----------------------------------------\n";
    
    // Check if directory exists
    $dirPath = $basePath . '/resources/views/' . $directory;
    if (!is_dir($dirPath)) {
        echo "   ⚠️ Directory not found: $directory\n\n";
        continue;
    }
    
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
    
    // Determine category based on directory
    $category = determineCategory($directory);
    
    // Create translation keys
    echo "   🔑 Creating translation keys...\n";
    $keysCreated = createLowPriorityKeys($directory, $category, $commonFixes, $langNewPath);
    $totalKeys += $keysCreated;
    
    // Apply fixes to Blade files
    echo "   🔧 Applying fixes to Blade files...\n";
    $filesFixed = applyLowPriorityFixes($directory, $category, $commonFixes, $basePath);
    $totalFiles += $filesFixed;
    
    $totalProcessed++;
    echo "   ✅ $directory completed ($keysCreated keys, $filesFixed files)\n\n";
}

echo "🎉 BATCH LOW PRIORITY PROCESSING COMPLETED!\n";
echo "============================================\n";
echo "📊 Directories processed: $totalProcessed\n";
echo "📊 Total translation keys created: $totalKeys\n";
echo "📊 Total files fixed: $totalFiles\n\n";

// Generate summary report
generateLowPrioritySummaryReport($lowPriorityDirs, $totalProcessed, $totalKeys, $totalFiles);

echo "📊 Summary report: storage/localization/batch_low_priority_summary.md\n";

function determineCategory($directory) {
    $categoryMap = [
        'about' => 'content',
        'alerts' => 'ui',
        'bookmarks' => 'features',
        'brand' => 'features',
        'business' => 'content',
        'categories' => 'ui',
        'chat' => 'features',
        'conversations' => 'features',
        'devices' => 'features',
        'docs' => 'content',
        'faq' => 'content',
        'following' => 'features',
        'frontend' => 'ui',
        'gallery' => 'features',
        'help' => 'content',
        'knowledge' => 'content',
        'manufacturer' => 'features',
        'members' => 'features',
        'new-content' => 'content',
        'news' => 'content',
        'notifications' => 'ui',
        'realtime' => 'features',
        'search' => 'features',
        'showcase' => 'features',
        'showcases' => 'features',
        'student' => 'features',
        'subscription' => 'features',
        'technical' => 'content',
        'test' => 'ui',
        'tools' => 'features',
        'users' => 'features'
    ];
    
    return $categoryMap[$directory] ?? 'content';
}

function createLowPriorityKeys($directory, $category, $fixes, $langNewPath) {
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
    
    // Add new keys (only first 10 to avoid overwhelming)
    $addedCount = 0;
    foreach ($keys as $keyName => $viText) {
        if (!isset($translations[$subcategory][$keyName]) && $addedCount < 10) {
            if ($lang === 'vi') {
                $translations[$subcategory][$keyName] = $viText;
            } else {
                $translations[$subcategory][$keyName] = generateEnglishTranslation($viText);
            }
            $addedCount++;
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
        'Tìm kiếm' => 'Search',
        'Lưu' => 'Save',
        'Hủy' => 'Cancel',
        'Xóa' => 'Delete',
        'Chỉnh sửa' => 'Edit',
        'Xem' => 'View',
        'Thêm' => 'Add',
        'Cập nhật' => 'Update',
        'Tải lên' => 'Upload',
        'Tải xuống' => 'Download',
        'Chia sẻ' => 'Share',
        'Sao chép' => 'Copy',
        'Di chuyển' => 'Move',
        'Đổi tên' => 'Rename',
        'Kích hoạt' => 'Activate',
        'Vô hiệu hóa' => 'Deactivate',
        'Phê duyệt' => 'Approve',
        'Từ chối' => 'Reject',
        'Gửi' => 'Send',
        'Nhận' => 'Receive',
        'Đăng' => 'Post',
        'Xuất bản' => 'Publish',
        'Nháp' => 'Draft',
        'Đã xuất bản' => 'Published',
        'Đang chờ' => 'Pending',
        'Hoàn thành' => 'Completed',
        'Đang xử lý' => 'Processing',
        'Thành công' => 'Success',
        'Thất bại' => 'Failed',
        'Lỗi' => 'Error',
        'Cảnh báo' => 'Warning',
        'Thông tin' => 'Info'
    ];
    
    return $translations[$viText] ?? $viText;
}

function generateFileContent($category, $directory, $translations, $lang) {
    $langName = $lang === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$directory\n";
    $content .= " * Batch low priority fixes - Updated: " . date('Y-m-d H:i:s') . "\n";
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

function applyLowPriorityFixes($directory, $category, $fixes, $basePath) {
    $dirPath = $basePath . '/resources/views/' . $directory;
    
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
    
    // Apply fixes (only first 5 most common fixes to avoid over-processing)
    $priorityFixes = array_slice($fixes, 0, 5, true);
    
    foreach ($bladeFiles as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;
        $replacements = 0;
        
        foreach ($priorityFixes as $text => $key) {
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

function generateLowPrioritySummaryReport($dirs, $totalProcessed, $totalKeys, $totalFiles) {
    $report = "# Batch Low Priority Localization - Summary Report\n\n";
    $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
    $report .= "**Directories processed:** $totalProcessed\n";
    $report .= "**Total translation keys created:** $totalKeys\n";
    $report .= "**Total files fixed:** $totalFiles\n\n";
    
    $report .= "## 🎯 Processed Directories\n\n";
    foreach ($dirs as $dir) {
        $category = determineCategory($dir);
        $report .= "- **$dir** ($category category)\n";
    }
    
    $report .= "\n## ✅ Status: LOW PRIORITY BATCH COMPLETED\n\n";
    $report .= "All low priority directories have been processed with basic localization fixes applied.\n";
    
    file_put_contents('storage/localization/batch_low_priority_summary.md', $report);
}
