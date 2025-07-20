<?php
/**
 * Apply Components Localization Fixes
 * Apply the priority fixes for components directory
 */

echo "🔧 Applying Components Localization Fixes...\n";
echo "==========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langNewPath = $basePath . '/resources/lang_new';

// Priority fixes from improved audit
$priorityFixes = [
    'Hủy' => 'buttons.cancel',
    'Đăng nhập để xem thông báo' => 'auth.login_to_view_notifications',
    'Đăng ký tài khoản MechaMap' => 'auth.register_mechamap_account',
    'Đang lưu tự động...' => 'common.auto_saving',
    'Nhập tin nhắn...' => 'forms.enter_message_placeholder',
    'Tin nhắn mới' => 'messages.new_message',
    'Tìm kiếm cuộc trò chuyện...' => 'forms.search_conversations_placeholder',
    'Tìm kiếm thành viên...' => 'forms.search_members_placeholder',
    'Gửi tin nhắn' => 'buttons.send_message',
    'Bản quyền thuộc về Công ty Cổ phần Công nghệ MechaMap.' => 'common.copyright_text',
    'Chuyển chế độ sáng/tối' => 'buttons.toggle_theme',
    'Thêm' => 'buttons.add',
    'Chế độ sáng' => 'common.light_mode',
    'Chế độ tối' => 'common.dark_mode',
    'Đã đánh dấu tất cả thông báo là đã đọc' => 'notifications.marked_all_read',
    'Quản trị' => 'roles.admin',
    'Nhà cung cấp' => 'roles.supplier',
    'Thương hiệu' => 'roles.brand',
    'No results found' => 'common.no_results_found',
    'Popular Searches' => 'common.popular_searches'
];

// Create translation keys in both VI and EN
echo "🔑 Creating translation keys...\n";
createTranslationKeys($priorityFixes, $langNewPath);

// Apply fixes to Blade files
echo "🔧 Applying fixes to Blade files...\n";
$fixedFiles = applyFixesToBladeFiles($priorityFixes, $basePath);

echo "\n🎉 Components localization fixes completed!\n";
echo "📊 Translation keys created: " . count($priorityFixes) . "\n";
echo "📊 Files potentially affected: $fixedFiles\n";

function createTranslationKeys($fixes, $langNewPath) {
    $keysByCategory = [];
    
    // Group keys by category
    foreach ($fixes as $text => $key) {
        $parts = explode('.', $key);
        $category = 'ui'; // Components are UI category
        $subcategory = $parts[0];
        $keyName = $parts[1];
        
        if (!isset($keysByCategory[$subcategory])) {
            $keysByCategory[$subcategory] = [];
        }
        $keysByCategory[$subcategory][$keyName] = $text;
    }
    
    // Create keys in both languages
    foreach ($keysByCategory as $subcategory => $keys) {
        createKeysInFile('ui', $subcategory, $keys, 'vi', $langNewPath);
        createKeysInFile('ui', $subcategory, $keys, 'en', $langNewPath);
    }
}

function createKeysInFile($category, $subcategory, $keys, $lang, $langNewPath) {
    $filePath = "$langNewPath/$lang/$category/$subcategory.php";
    
    // Load existing translations
    $translations = [];
    if (file_exists($filePath)) {
        $translations = include $filePath;
        if (!is_array($translations)) {
            $translations = [];
        }
    }
    
    // Add new keys
    foreach ($keys as $keyName => $viText) {
        if (!isset($translations[$keyName])) {
            if ($lang === 'vi') {
                $translations[$keyName] = $viText;
            } else {
                // Generate English translation
                $translations[$keyName] = generateEnglishTranslation($viText);
            }
        }
    }
    
    // Sort keys alphabetically
    ksort($translations);
    
    // Generate file content
    $content = generateTranslationFileContent($category, $subcategory, $translations, $lang);
    
    // Ensure directory exists
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Write file
    file_put_contents($filePath, $content);
    echo "   ✅ Updated: $lang/$category/$subcategory.php (" . count($keys) . " keys)\n";
}

function generateEnglishTranslation($viText) {
    // Simple translation mapping for common terms
    $translations = [
        'Hủy' => 'Cancel',
        'Đăng nhập để xem thông báo' => 'Login to view notifications',
        'Đăng ký tài khoản MechaMap' => 'Register MechaMap account',
        'Đang lưu tự động...' => 'Auto saving...',
        'Nhập tin nhắn...' => 'Enter message...',
        'Tin nhắn mới' => 'New message',
        'Tìm kiếm cuộc trò chuyện...' => 'Search conversations...',
        'Tìm kiếm thành viên...' => 'Search members...',
        'Gửi tin nhắn' => 'Send message',
        'Bản quyền thuộc về Công ty Cổ phần Công nghệ MechaMap.' => 'Copyright belongs to MechaMap Technology Joint Stock Company.',
        'Chuyển chế độ sáng/tối' => 'Toggle light/dark mode',
        'Thêm' => 'Add',
        'Chế độ sáng' => 'Light mode',
        'Chế độ tối' => 'Dark mode',
        'Đã đánh dấu tất cả thông báo là đã đọc' => 'Marked all notifications as read',
        'Quản trị' => 'Admin',
        'Nhà cung cấp' => 'Supplier',
        'Thương hiệu' => 'Brand',
    ];
    
    return $translations[$viText] ?? $viText;
}

function generateTranslationFileContent($category, $subcategory, $translations, $lang) {
    $langName = $lang === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$subcategory\n";
    $content .= " * Components localization - Updated: " . date('Y-m-d H:i:s') . "\n";
    $content .= " * Keys: " . count($translations) . "\n";
    $content .= " */\n\n";
    $content .= "return " . arrayToString($translations, 0) . ";\n";
    
    return $content;
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

function applyFixesToBladeFiles($fixes, $basePath) {
    $componentsPath = $basePath . '/resources/views/components';
    $fixedFiles = 0;
    
    // Create backup directory
    $backupDir = $basePath . '/storage/localization/components_backup_' . date('Y_m_d_H_i_s');
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    // Find all blade files in components
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($componentsPath)
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
    copyDirectory($componentsPath, $backupDir);
    
    // Apply fixes to each file
    foreach ($bladeFiles as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;
        $replacements = 0;
        
        foreach ($fixes as $text => $key) {
            // Create helper function call
            $parts = explode('.', $key);
            $helperCall = "{{ t_ui('{$parts[0]}.{$parts[1]}') }}";
            
            // Try to replace hardcoded text
            $patterns = [
                // Simple quoted strings
                '/["\']' . preg_quote($text, '/') . '["\']/',
                // In HTML content
                '/>' . preg_quote($text, '/') . '</',
                // In placeholders
                '/placeholder\s*=\s*["\']' . preg_quote($text, '/') . '["\']/',
                // In titles
                '/title\s*=\s*["\']' . preg_quote($text, '/') . '["\']/',
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
