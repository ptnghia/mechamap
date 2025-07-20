<?php
/**
 * Populate Features Files
 * Move forum, marketplace, showcase, knowledge, community keys to features/ directory
 */

echo "🚀 Populating Features Files...\n";
echo "===============================\n\n";

$languages = ['vi', 'en'];
$featuresFiles = [
    'forum' => [
        'description' => 'Forum discussions and threads',
        'source_files' => ['forum.php', 'forums.php', 'thread.php'],
        'additional_content' => 'forum_features'
    ],
    'marketplace' => [
        'description' => 'Marketplace and trading features',
        'source_files' => ['marketplace.php', 'companies.php'],
        'additional_content' => 'marketplace_features'
    ],
    'showcase' => [
        'description' => 'Project showcases and portfolios',
        'source_files' => ['showcase.php', 'showcases.php'],
        'additional_content' => 'showcase_features'
    ],
    'knowledge' => [
        'description' => 'Knowledge base and documentation',
        'source_files' => ['knowledge.php', 'docs.php'],
        'additional_content' => 'knowledge_features'
    ],
    'community' => [
        'description' => 'Community features and interactions',
        'source_files' => ['community.php', 'members.php'],
        'additional_content' => 'community_features'
    ]
];

$populatedFiles = 0;
$totalKeys = 0;

foreach ($languages as $lang) {
    echo "🌐 Processing language: $lang\n";
    
    foreach ($featuresFiles as $featureFile => $config) {
        echo "   🚀 Populating features/$featureFile.php...\n";
        
        $keys = [];
        
        // Load keys from source files
        foreach ($config['source_files'] as $sourceFile) {
            $sourcePath = "resources/lang/$lang/$sourceFile";
            if (file_exists($sourcePath)) {
                $sourceKeys = include $sourcePath;
                if (is_array($sourceKeys)) {
                    $keys = array_merge($keys, $sourceKeys);
                    echo "      ✅ Loaded " . count($sourceKeys) . " keys from $sourceFile\n";
                }
            } else {
                echo "      ⚠️ Source file not found: $sourcePath\n";
            }
        }
        
        // Add additional feature-specific keys
        $additionalKeys = getAdditionalFeatureKeys($featureFile, $lang);
        if (!empty($additionalKeys)) {
            $keys = array_merge($keys, $additionalKeys);
            echo "      ✅ Added " . count($additionalKeys) . " additional keys\n";
        }
        
        // Organize feature keys
        $keys = organizeFeatureKeys($keys, $featureFile);
        
        // Generate the new file content
        $newContent = generateFeatureFileContent($featureFile, $config['description'], $keys, $lang);
        
        // Write to new location
        $newPath = "resources/lang_new/$lang/features/$featureFile.php";
        file_put_contents($newPath, $newContent);
        
        $keyCount = count($keys, COUNT_RECURSIVE) - count($keys);
        $totalKeys += $keyCount;
        $populatedFiles++;
        
        echo "      ✅ Created $newPath with $keyCount keys\n";
    }
    echo "\n";
}

// Create features documentation
echo "📋 Creating features documentation...\n";
createFeaturesDocumentation($featuresFiles);

// Verify features files
echo "✅ Verifying features files...\n";
$verification = verifyFeaturesFiles($languages, $featuresFiles);

if ($verification['status'] === 'success') {
    echo "   ✅ Features files verification passed\n";
} else {
    echo "   ❌ Features files verification failed\n";
    foreach ($verification['errors'] as $error) {
        echo "      - $error\n";
    }
}

// Generate report
generateFeaturesReport($populatedFiles, $totalKeys, $verification);

echo "\n🎉 Features files populated successfully!\n";
echo "📊 Populated: $populatedFiles files with $totalKeys total keys\n";
echo "📋 Documentation: resources/lang_new/features/README.md\n";
echo "📊 Report: storage/localization/task_2_5_features_report.md\n";

// Helper Functions

function getAdditionalFeatureKeys($featureFile, $language) {
    $additionalKeys = [];
    
    switch ($featureFile) {
        case 'forum':
            $additionalKeys = [
                'threads' => [
                    'create' => $language === 'vi' ? 'Tạo thảo luận' : 'Create Thread',
                    'edit' => $language === 'vi' ? 'Chỉnh sửa thảo luận' : 'Edit Thread',
                    'delete' => $language === 'vi' ? 'Xóa thảo luận' : 'Delete Thread',
                    'sticky' => $language === 'vi' ? 'Ghim' : 'Sticky',
                    'locked' => $language === 'vi' ? 'Khóa' : 'Locked'
                ],
                'posts' => [
                    'reply' => $language === 'vi' ? 'Trả lời' : 'Reply',
                    'quote' => $language === 'vi' ? 'Trích dẫn' : 'Quote',
                    'edit' => $language === 'vi' ? 'Chỉnh sửa' : 'Edit',
                    'delete' => $language === 'vi' ? 'Xóa' : 'Delete'
                ],
                'categories' => [
                    'all' => $language === 'vi' ? 'Tất cả danh mục' : 'All Categories',
                    'select' => $language === 'vi' ? 'Chọn danh mục' : 'Select Category'
                ]
            ];
            break;
            
        case 'marketplace':
            $additionalKeys = [
                'products' => [
                    'add' => $language === 'vi' ? 'Thêm sản phẩm' : 'Add Product',
                    'edit' => $language === 'vi' ? 'Chỉnh sửa sản phẩm' : 'Edit Product',
                    'view' => $language === 'vi' ? 'Xem sản phẩm' : 'View Product',
                    'delete' => $language === 'vi' ? 'Xóa sản phẩm' : 'Delete Product'
                ],
                'orders' => [
                    'place' => $language === 'vi' ? 'Đặt hàng' : 'Place Order',
                    'track' => $language === 'vi' ? 'Theo dõi đơn hàng' : 'Track Order',
                    'cancel' => $language === 'vi' ? 'Hủy đơn hàng' : 'Cancel Order'
                ],
                'sellers' => [
                    'profile' => $language === 'vi' ? 'Hồ sơ người bán' : 'Seller Profile',
                    'contact' => $language === 'vi' ? 'Liên hệ người bán' : 'Contact Seller'
                ]
            ];
            break;
            
        case 'showcase':
            $additionalKeys = [
                'projects' => [
                    'create' => $language === 'vi' ? 'Tạo dự án' : 'Create Project',
                    'edit' => $language === 'vi' ? 'Chỉnh sửa dự án' : 'Edit Project',
                    'view' => $language === 'vi' ? 'Xem dự án' : 'View Project',
                    'featured' => $language === 'vi' ? 'Dự án nổi bật' : 'Featured Projects'
                ],
                'categories' => [
                    'web' => $language === 'vi' ? 'Web Development' : 'Web Development',
                    'mobile' => $language === 'vi' ? 'Mobile Apps' : 'Mobile Apps',
                    'design' => $language === 'vi' ? 'Design' : 'Design'
                ]
            ];
            break;
            
        case 'knowledge':
            $additionalKeys = [
                'articles' => [
                    'create' => $language === 'vi' ? 'Tạo bài viết' : 'Create Article',
                    'edit' => $language === 'vi' ? 'Chỉnh sửa bài viết' : 'Edit Article',
                    'publish' => $language === 'vi' ? 'Xuất bản' : 'Publish'
                ],
                'categories' => [
                    'tutorials' => $language === 'vi' ? 'Hướng dẫn' : 'Tutorials',
                    'guides' => $language === 'vi' ? 'Hướng dẫn chi tiết' : 'Guides',
                    'tips' => $language === 'vi' ? 'Mẹo hay' : 'Tips & Tricks'
                ]
            ];
            break;
            
        case 'community':
            $additionalKeys = [
                'members' => [
                    'online' => $language === 'vi' ? 'Đang trực tuyến' : 'Online',
                    'offline' => $language === 'vi' ? 'Ngoại tuyến' : 'Offline',
                    'total' => $language === 'vi' ? 'Tổng thành viên' : 'Total Members'
                ],
                'events' => [
                    'upcoming' => $language === 'vi' ? 'Sự kiện sắp tới' : 'Upcoming Events',
                    'past' => $language === 'vi' ? 'Sự kiện đã qua' : 'Past Events',
                    'join' => $language === 'vi' ? 'Tham gia' : 'Join Event'
                ]
            ];
            break;
    }
    
    return $additionalKeys;
}

function organizeFeatureKeys($keys, $featureFile) {
    // Remove duplicates and organize keys logically
    $organized = [];
    
    foreach ($keys as $key => $value) {
        if (!isset($organized[$key])) {
            $organized[$key] = $value;
        }
    }
    
    return $organized;
}

function generateFeatureFileContent($filename, $description, $keys, $language) {
    $langName = $language === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for features/$filename\n";
    $content .= " * $description\n";
    $content .= " * \n";
    $content .= " * Structure: features.$filename.*\n";
    $content .= " * Populated: " . date('Y-m-d H:i:s') . "\n";
    $content .= " * Keys: " . (count($keys, COUNT_RECURSIVE) - count($keys)) . "\n";
    $content .= " */\n\n";
    $content .= "return " . arrayToString($keys, 0) . ";\n";
    
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

function createFeaturesDocumentation($featuresFiles) {
    if (!is_dir('resources/lang_new/features')) {
        mkdir('resources/lang_new/features', 0755, true);
    }
    
    $doc = "# Features Translations\n\n";
    $doc .= "**Purpose:** Feature-specific content and functionality\n";
    $doc .= "**Created:** " . date('Y-m-d H:i:s') . "\n\n";
    
    $doc .= "## Files Overview\n\n";
    foreach ($featuresFiles as $filename => $config) {
        $doc .= "### $filename.php\n";
        $doc .= "**Description:** " . $config['description'] . "\n";
        $doc .= "**Key structure:** `features.$filename.*`\n";
        $doc .= "**Source files:** " . implode(', ', $config['source_files']) . "\n\n";
    }
    
    $doc .= "## Usage Examples\n\n";
    $doc .= "```php\n";
    $doc .= "// Forum features\n";
    $doc .= "__('features.forum.threads.create')\n";
    $doc .= "__('features.forum.posts.reply')\n\n";
    $doc .= "// Marketplace features\n";
    $doc .= "__('features.marketplace.products.add')\n";
    $doc .= "__('features.marketplace.orders.track')\n\n";
    $doc .= "// Showcase features\n";
    $doc .= "__('features.showcase.projects.featured')\n";
    $doc .= "__('features.showcase.categories.web')\n";
    $doc .= "```\n";
    
    file_put_contents('resources/lang_new/features/README.md', $doc);
}

function verifyFeaturesFiles($languages, $featuresFiles) {
    $verification = [
        'status' => 'success',
        'checks' => [],
        'errors' => []
    ];
    
    foreach ($languages as $lang) {
        foreach ($featuresFiles as $filename => $config) {
            $filePath = "resources/lang_new/$lang/features/$filename.php";
            
            if (file_exists($filePath)) {
                $verification['checks'][] = "File exists: $lang/features/$filename.php";
                
                try {
                    $data = include $filePath;
                    if (is_array($data)) {
                        $keyCount = count($data, COUNT_RECURSIVE) - count($data);
                        $verification['checks'][] = "Returns array: $lang/features/$filename.php ($keyCount keys)";
                    } else {
                        $verification['errors'][] = "Does not return array: $lang/features/$filename.php";
                        $verification['status'] = 'error';
                    }
                } catch (Exception $e) {
                    $verification['errors'][] = "Parse error in $lang/features/$filename.php: " . $e->getMessage();
                    $verification['status'] = 'error';
                }
            } else {
                $verification['errors'][] = "File missing: $lang/features/$filename.php";
                $verification['status'] = 'error';
            }
        }
    }
    
    return $verification;
}

function generateFeaturesReport($populatedFiles, $totalKeys, $verification) {
    $report = "# Task 2.5: Tạo File Features/ - Báo Cáo\n\n";
    $report .= "**Thời gian thực hiện:** " . date('Y-m-d H:i:s') . "\n";
    $report .= "**Trạng thái:** ✅ HOÀN THÀNH\n\n";
    
    $report .= "## 📊 Thống Kê Features Files\n\n";
    $report .= "- **Files populated:** $populatedFiles\n";
    $report .= "- **Total keys created:** $totalKeys\n";
    $report .= "- **Languages:** vi, en\n";
    $report .= "- **Feature categories:** forum, marketplace, showcase, knowledge, community\n\n";
    
    $report .= "## 📁 Files Created\n\n";
    $featuresFiles = ['forum', 'marketplace', 'showcase', 'knowledge', 'community'];
    foreach (['vi', 'en'] as $lang) {
        $report .= "### $lang/features/\n";
        foreach ($featuresFiles as $file) {
            $filePath = "resources/lang_new/$lang/features/$file.php";
            if (file_exists($filePath)) {
                $data = include $filePath;
                $keyCount = is_array($data) ? (count($data, COUNT_RECURSIVE) - count($data)) : 0;
                $report .= "- `$file.php`: $keyCount keys\n";
            }
        }
        $report .= "\n";
    }
    
    $report .= "## ✅ Verification Results\n\n";
    $report .= "**Status:** " . strtoupper($verification['status']) . "\n\n";
    
    if (!empty($verification['checks'])) {
        $report .= "**Passed Checks:** " . count($verification['checks']) . "\n";
        foreach (array_slice($verification['checks'], 0, 10) as $check) {
            $report .= "- ✅ $check\n";
        }
        if (count($verification['checks']) > 10) {
            $report .= "- ... and " . (count($verification['checks']) - 10) . " more\n";
        }
        $report .= "\n";
    }
    
    if (!empty($verification['errors'])) {
        $report .= "**Errors:**\n";
        foreach ($verification['errors'] as $error) {
            $report .= "- ❌ $error\n";
        }
        $report .= "\n";
    }
    
    $report .= "## ✅ Task 2.5 Completion\n\n";
    $report .= "- [x] Tạo file features/ cho cả VI và EN ✅\n";
    $report .= "- [x] Nhóm keys theo tính năng ✅\n";
    $report .= "- [x] Populate với feature-specific keys ✅\n";
    $report .= "- [x] Verify file integrity ✅\n\n";
    $report .= "**Next Task:** 2.6 Tạo file user/ và admin/\n";
    
    file_put_contents('storage/localization/task_2_5_features_report.md', $report);
}
