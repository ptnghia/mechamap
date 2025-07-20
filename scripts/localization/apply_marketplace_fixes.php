<?php
/**
 * Apply Marketplace Localization Fixes
 * Apply priority fixes for marketplace directory with improved key generation
 */

echo "🔧 Applying Marketplace Localization Fixes...\n";
echo "============================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$langNewPath = $basePath . '/resources/lang_new';

// Priority fixes for marketplace with better keys
$priorityFixes = [
    'Hủy' => 'actions.cancel',
    'Tải lại' => 'actions.reload',
    'Chi tiết' => 'actions.details',
    'Vừa xong' => 'time.just_now',
    'phút trước' => 'time.minutes_ago',
    'giờ trước' => 'time.hours_ago',
    'ngày trước' => 'time.days_ago',
    'Xem thêm' => 'actions.view_more',
    'Thêm vào giỏ hàng' => 'actions.add_to_cart',
    'Mua ngay' => 'actions.buy_now',
    'Liên hệ' => 'actions.contact',
    'Chia sẻ' => 'actions.share',
    'Yêu thích' => 'actions.favorite',
    'So sánh' => 'actions.compare',
    'Đánh giá' => 'actions.review',
    'Báo cáo' => 'actions.report',
    'Sản phẩm' => 'labels.product',
    'Giá' => 'labels.price',
    'Còn hàng' => 'status.in_stock',
    'Hết hàng' => 'status.out_of_stock',
    'Miễn phí vận chuyển' => 'shipping.free_shipping',
    'Giao hàng nhanh' => 'shipping.fast_delivery',
    'Bảo hành' => 'labels.warranty',
    'Đổi trả' => 'labels.return_policy',
    'Khuyến mãi' => 'labels.promotion',
    'Giảm giá' => 'labels.discount',
    'Mới' => 'status.new',
    'Bán chạy' => 'status.bestseller',
    'Đề xuất' => 'status.recommended',
    'Tìm kiếm sản phẩm...' => 'placeholders.search_products'
];

// Create translation keys
echo "🔑 Creating marketplace translation keys...\n";
createMarketplaceKeys($priorityFixes, $langNewPath);

// Apply fixes to Blade files
echo "🔧 Applying fixes to marketplace Blade files...\n";
$fixedFiles = applyMarketplaceFixes($priorityFixes, $basePath);

echo "\n🎉 Marketplace localization fixes completed!\n";
echo "📊 Translation keys created: " . count($priorityFixes) . "\n";
echo "📊 Files potentially affected: $fixedFiles\n";

function createMarketplaceKeys($fixes, $langNewPath) {
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

    // Create keys in features category for marketplace
    foreach ($keysBySubcategory as $subcategory => $keys) {
        createMarketplaceKeysInFile('features', 'marketplace', $subcategory, $keys, 'vi', $langNewPath);
        createMarketplaceKeysInFile('features', 'marketplace', $subcategory, $keys, 'en', $langNewPath);
    }
}

function createMarketplaceKeysInFile($category, $mainCategory, $subcategory, $keys, $lang, $langNewPath) {
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
                $translations[$subcategory][$keyName] = generateMarketplaceEnglishTranslation($viText);
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
    $content = generateMarketplaceFileContent($category, $mainCategory, $translations, $lang);

    // Ensure directory exists
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Write file
    file_put_contents($filePath, $content);
    echo "   ✅ Updated: $lang/$category/$mainCategory.php (subcategory: $subcategory)\n";
}

function generateMarketplaceEnglishTranslation($viText) {
    $translations = [
        'Hủy' => 'Cancel',
        'Tải lại' => 'Reload',
        'Chi tiết' => 'Details',
        'Vừa xong' => 'Just now',
        'phút trước' => 'minutes ago',
        'giờ trước' => 'hours ago',
        'ngày trước' => 'days ago',
        'Xem thêm' => 'View more',
        'Thêm vào giỏ hàng' => 'Add to cart',
        'Mua ngay' => 'Buy now',
        'Liên hệ' => 'Contact',
        'Chia sẻ' => 'Share',
        'Yêu thích' => 'Favorite',
        'So sánh' => 'Compare',
        'Đánh giá' => 'Review',
        'Báo cáo' => 'Report',
        'Sản phẩm' => 'Product',
        'Giá' => 'Price',
        'Còn hàng' => 'In stock',
        'Hết hàng' => 'Out of stock',
        'Miễn phí vận chuyển' => 'Free shipping',
        'Giao hàng nhanh' => 'Fast delivery',
        'Bảo hành' => 'Warranty',
        'Đổi trả' => 'Return policy',
        'Khuyến mãi' => 'Promotion',
        'Giảm giá' => 'Discount',
        'Mới' => 'New',
        'Bán chạy' => 'Bestseller',
        'Đề xuất' => 'Recommended',
        'Tìm kiếm sản phẩm...' => 'Search products...'
    ];

    return $translations[$viText] ?? $viText;
}

function generateMarketplaceFileContent($category, $mainCategory, $translations, $lang) {
    $langName = $lang === 'vi' ? 'Vietnamese' : 'English';

    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$mainCategory\n";
    $content .= " * Marketplace localization - Updated: " . date('Y-m-d H:i:s') . "\n";
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

function applyMarketplaceFixes($fixes, $basePath) {
    $marketplacePath = $basePath . '/resources/views/marketplace';
    $fixedFiles = 0;

    // Create backup directory
    $backupDir = $basePath . '/storage/localization/marketplace_backup_' . date('Y_m_d_H_i_s');
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    // Find all blade files
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($marketplacePath)
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
    copyDirectory($marketplacePath, $backupDir);

    // Apply fixes to each file
    foreach ($bladeFiles as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;
        $replacements = 0;

        foreach ($fixes as $text => $key) {
            // Create helper function call
            $parts = explode('.', $key);
            $helperCall = "{{ t_feature('marketplace.{$parts[0]}.{$parts[1]}') }}";

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
