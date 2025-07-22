<?php

/**
 * ADD HELPER FUNCTION KEYS
 * Thêm các keys cho helper functions (t_xxx)
 */

echo "=== ADDING HELPER FUNCTION KEYS ===\n\n";

// Helper function mappings
$helperMapping = [
    't_features' => 'features',
    't_common' => 'common',
    't_ui' => 'ui',
    't_feature' => 'feature',
    't_content' => 'content',
    't_user' => 'user'
];

// Define missing helper function keys
$helperKeys = [
    'features' => [
        'vi' => [
            'brand' => [
                'actions' => [
                    'search' => 'Tìm kiếm thương hiệu'
                ]
            ]
        ],
        'en' => [
            'brand' => [
                'actions' => [
                    'search' => 'Search Brand'
                ]
            ]
        ]
    ],

    'common' => [
        'vi' => [
            'site' => [
                'tagline' => 'Cộng đồng kỹ thuật hàng đầu Việt Nam'
            ],
            'views' => 'Lượt xem',
            'comments' => 'Bình luận',
            'updated' => 'Đã cập nhật',
            'replies' => 'Trả lời',
            'posts' => 'Bài viết',
            'threads' => 'Chủ đề',
            'members' => 'Thành viên',
            'latest_post' => 'Bài viết mới nhất',
            'last_activity' => 'Hoạt động cuối',
            'created_at' => 'Tạo lúc',
            'author' => 'Tác giả',
            'category' => 'Danh mục'
        ],
        'en' => [
            'site' => [
                'tagline' => 'Vietnam\'s Leading Engineering Community'
            ],
            'views' => 'Views',
            'comments' => 'Comments',
            'updated' => 'Updated',
            'replies' => 'Replies',
            'posts' => 'Posts',
            'threads' => 'Threads',
            'members' => 'Members',
            'latest_post' => 'Latest Post',
            'last_activity' => 'Last Activity',
            'created_at' => 'Created At',
            'author' => 'Author',
            'category' => 'Category'
        ]
    ],

    'ui' => [
        'vi' => [
            'common' => [
                'loading' => 'Đang tải...'
            ]
        ],
        'en' => [
            'common' => [
                'loading' => 'Loading...'
            ]
        ]
    ],

    'feature' => [
        'vi' => [
            'premium_support' => 'Hỗ trợ cao cấp',
            'advanced_analytics' => 'Phân tích nâng cao',
            'priority_listing' => 'Danh sách ưu tiên',
            'custom_branding' => 'Thương hiệu tùy chỉnh'
        ],
        'en' => [
            'premium_support' => 'Premium Support',
            'advanced_analytics' => 'Advanced Analytics',
            'priority_listing' => 'Priority Listing',
            'custom_branding' => 'Custom Branding'
        ]
    ],

    'content' => [
        'vi' => [
            'welcome_message' => 'Chào mừng đến với MechaMap',
            'getting_started' => 'Bắt đầu',
            'explore_features' => 'Khám phá tính năng',
            'join_community' => 'Tham gia cộng đồng',
            'learn_more' => 'Tìm hiểu thêm'
        ],
        'en' => [
            'welcome_message' => 'Welcome to MechaMap',
            'getting_started' => 'Getting Started',
            'explore_features' => 'Explore Features',
            'join_community' => 'Join Community',
            'learn_more' => 'Learn More'
        ]
    ],

    'user' => [
        'vi' => [
            'profile_settings' => 'Cài đặt hồ sơ',
            'account_preferences' => 'Tùy chọn tài khoản',
            'privacy_settings' => 'Cài đặt riêng tư',
            'notification_preferences' => 'Tùy chọn thông báo'
        ],
        'en' => [
            'profile_settings' => 'Profile Settings',
            'account_preferences' => 'Account Preferences',
            'privacy_settings' => 'Privacy Settings',
            'notification_preferences' => 'Notification Preferences'
        ]
    ]
];

// Function to add keys to translation file
function addKeysToFile($filePath, $newKeys, $language) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }

    try {
        $existingTranslations = include $filePath;
        if (!is_array($existingTranslations)) {
            echo "❌ Invalid translation file: $filePath\n";
            return false;
        }

        // Merge new keys recursively
        $updatedTranslations = array_merge_recursive($existingTranslations, $newKeys);

        // Generate file content
        $fileContent = "<?php\n\n";
        $fileContent .= "/**\n";
        $fileContent .= " * " . ucfirst(basename($filePath, '.php')) . " Translation File - " . ucfirst($language) . " (COMPREHENSIVE)\n";
        $fileContent .= " * Complete translation coverage for " . basename($filePath, '.php') . " functionality\n";
        $fileContent .= " * Auto-updated: " . date('Y-m-d H:i:s') . "\n";
        $fileContent .= " */\n\n";
        $fileContent .= "return " . var_export($updatedTranslations, true) . ";\n";

        if (file_put_contents($filePath, $fileContent)) {
            echo "✅ Updated $filePath with helper function keys\n";
            return true;
        } else {
            echo "❌ Failed to write to $filePath\n";
            return false;
        }

    } catch (Exception $e) {
        echo "❌ Error updating $filePath: " . $e->getMessage() . "\n";
        return false;
    }
}

// Function to count nested keys
function countNestedKeys($array) {
    $count = 0;
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $count += countNestedKeys($value);
        } else {
            $count++;
        }
    }
    return $count;
}

$viDir = __DIR__ . '/resources/lang/vi/';
$enDir = __DIR__ . '/resources/lang/en/';

$totalAdded = 0;
$filesUpdated = 0;

// Process each helper function file
foreach ($helperKeys as $fileName => $languages) {
    echo "\n🔄 Processing $fileName.php for helper functions...\n";

    // Update Vietnamese file
    $viFile = $viDir . $fileName . '.php';
    if (isset($languages['vi'])) {
        $keyCount = countNestedKeys($languages['vi']);
        if (addKeysToFile($viFile, $languages['vi'], 'vietnamese')) {
            $filesUpdated++;
            $totalAdded += $keyCount;
        }
    }

    // Update English file
    $enFile = $enDir . $fileName . '.php';
    if (isset($languages['en'])) {
        $keyCount = countNestedKeys($languages['en']);
        if (addKeysToFile($enFile, $languages['en'], 'english')) {
            $filesUpdated++;
            $totalAdded += $keyCount;
        }
    }
}

// Create missing files if they don't exist
$missingFiles = ['features', 'feature'];

foreach ($missingFiles as $fileName) {
    $viFile = $viDir . $fileName . '.php';
    $enFile = $enDir . $fileName . '.php';

    // Create VI file if missing
    if (!file_exists($viFile) && isset($helperKeys[$fileName]['vi'])) {
        echo "\n🆕 Creating missing VI file: $fileName.php\n";
        
        $fileContent = "<?php\n\n";
        $fileContent .= "/**\n";
        $fileContent .= " * " . ucfirst($fileName) . " Translation File - Vietnamese (COMPREHENSIVE)\n";
        $fileContent .= " * Complete translation coverage for $fileName functionality\n";
        $fileContent .= " * Auto-created: " . date('Y-m-d H:i:s') . "\n";
        $fileContent .= " */\n\n";
        $fileContent .= "return " . var_export($helperKeys[$fileName]['vi'], true) . ";\n";
        
        if (file_put_contents($viFile, $fileContent)) {
            echo "✅ Created $viFile\n";
            $filesUpdated++;
            $totalAdded += countNestedKeys($helperKeys[$fileName]['vi']);
        }
    }

    // Create EN file if missing
    if (!file_exists($enFile) && isset($helperKeys[$fileName]['en'])) {
        echo "\n🆕 Creating missing EN file: $fileName.php\n";
        
        $fileContent = "<?php\n\n";
        $fileContent .= "/**\n";
        $fileContent .= " * " . ucfirst($fileName) . " Translation File - English (COMPREHENSIVE)\n";
        $fileContent .= " * Complete translation coverage for $fileName functionality\n";
        $fileContent .= " * Auto-created: " . date('Y-m-d H:i:s') . "\n";
        $fileContent .= " */\n\n";
        $fileContent .= "return " . var_export($helperKeys[$fileName]['en'], true) . ";\n";
        
        if (file_put_contents($enFile, $fileContent)) {
            echo "✅ Created $enFile\n";
            $filesUpdated++;
            $totalAdded += countNestedKeys($helperKeys[$fileName]['en']);
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Files updated/created: $filesUpdated\n";
echo "Total helper function keys added: $totalAdded\n";

echo "\n✅ Helper function keys process completed at " . date('Y-m-d H:i:s') . "\n";
?>
