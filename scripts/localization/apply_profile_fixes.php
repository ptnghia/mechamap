<?php
/**
 * Apply Profile Localization Fixes
 * Quick fixes for profile directory
 */

echo "🔧 Applying Profile Localization Fixes...\n";
echo "========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langNewPath = $basePath . '/resources/lang_new';

// Priority fixes for profile
$priorityFixes = [
    'Đã hủy' => 'status.cancelled',
    'Hủy đơn' => 'actions.cancel_order',
    'Tìm kiếm' => 'actions.search',
    'Vai trò' => 'labels.role',
    'Tất cả vai trò' => 'labels.all_roles',
    'Hoạt động' => 'labels.activity',
    'Đơn hàng' => 'labels.orders',
    'Cài đặt' => 'labels.settings',
    'Thông tin cá nhân' => 'labels.personal_info',
    'Mật khẩu' => 'labels.password',
    'Bảo mật' => 'labels.security',
    'Thông báo' => 'labels.notifications',
    'Lịch sử' => 'labels.history',
    'Yêu thích' => 'labels.favorites',
    'Đánh giá' => 'labels.reviews'
];

// Create translation keys
echo "🔑 Creating profile translation keys...\n";
createProfileKeys($priorityFixes, $langNewPath);

// Apply fixes to Blade files
echo "🔧 Applying fixes to profile Blade files...\n";
$fixedFiles = applyProfileFixes($priorityFixes, $basePath);

echo "\n🎉 Profile localization fixes completed!\n";
echo "📊 Translation keys created: " . count($priorityFixes) . "\n";
echo "📊 Files potentially affected: $fixedFiles\n";

function createProfileKeys($fixes, $langNewPath) {
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
    
    // Create keys in user category for profile
    foreach ($keysBySubcategory as $subcategory => $keys) {
        createProfileKeysInFile('user', 'profile', $subcategory, $keys, 'vi', $langNewPath);
        createProfileKeysInFile('user', 'profile', $subcategory, $keys, 'en', $langNewPath);
    }
}

function createProfileKeysInFile($category, $mainCategory, $subcategory, $keys, $lang, $langNewPath) {
    $filePath = "$langNewPath/$lang/$category/$mainCategory.php";
    
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
                $translations[$subcategory][$keyName] = generateProfileEnglishTranslation($viText);
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
    $content = generateProfileFileContent($category, $mainCategory, $translations, $lang);
    
    // Ensure directory exists
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Write file
    file_put_contents($filePath, $content);
    echo "   ✅ Updated: $lang/$category/$mainCategory.php (subcategory: $subcategory)\n";
}

function generateProfileEnglishTranslation($viText) {
    $translations = [
        'Đã hủy' => 'Cancelled',
        'Hủy đơn' => 'Cancel order',
        'Tìm kiếm' => 'Search',
        'Vai trò' => 'Role',
        'Tất cả vai trò' => 'All roles',
        'Hoạt động' => 'Activity',
        'Đơn hàng' => 'Orders',
        'Cài đặt' => 'Settings',
        'Thông tin cá nhân' => 'Personal information',
        'Mật khẩu' => 'Password',
        'Bảo mật' => 'Security',
        'Thông báo' => 'Notifications',
        'Lịch sử' => 'History',
        'Yêu thích' => 'Favorites',
        'Đánh giá' => 'Reviews'
    ];
    
    return $translations[$viText] ?? $viText;
}

function generateProfileFileContent($category, $mainCategory, $translations, $lang) {
    $langName = $lang === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$mainCategory\n";
    $content .= " * Profile localization - Updated: " . date('Y-m-d H:i:s') . "\n";
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

function applyProfileFixes($fixes, $basePath) {
    $profilePath = $basePath . '/resources/views/profile';
    $fixedFiles = 0;
    
    // Create backup directory
    $backupDir = $basePath . '/storage/localization/profile_backup_' . date('Y_m_d_H_i_s');
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    // Find all blade files
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($profilePath)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && 
            strpos($file->getFilename(), '.blade.') !== false) {
            $bladeFiles[] = $file->getPathname();
        }
    }
    
    echo "   📁 Found " . count($bladeFiles) . " Blade files\n";
    echo "   💾 Creating backup in: $backupDir\n";
    
    // Create backup
    copyDirectory($profilePath, $backupDir);
    
    // Apply fixes to each file
    foreach ($bladeFiles as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;
        $replacements = 0;
        
        foreach ($fixes as $text => $key) {
            // Create helper function call
            $parts = explode('.', $key);
            $helperCall = "{{ t_user('profile.{$parts[0]}.{$parts[1]}') }}";
            
            // Try to replace hardcoded text
            $patterns = [
                // Simple quoted strings
                '/["\']' . preg_quote($text, '/') . '["\']/',
                // In HTML content
                '/>' . preg_quote($text, '/') . '</',
                // In placeholders
                '/placeholder\s*=\s*["\']' . preg_quote($text, '/') . '["\']/',
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
        
        // Save if changes were made
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $fixedFiles++;
            echo "   ✅ Fixed: " . basename($file) . " ($replacements replacements)\n";
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
