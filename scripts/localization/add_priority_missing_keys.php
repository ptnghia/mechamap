<?php
/**
 * Add Priority Missing Keys
 * Thêm các translation keys thiếu quan trọng nhất (không bao gồm admin)
 */

echo "🔧 ADDING PRIORITY MISSING KEYS\n";
echo "===============================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎯 FOCUSING ON NON-ADMIN MISSING KEYS\n";
echo "=====================================\n";

// Priority missing keys (excluding admin-related ones)
$priorityKeys = [
    // Pagination keys (highest priority - 28 usages)
    'pagination' => [
        'previous' => [
            'en' => 'Previous',
            'vi' => 'Trước'
        ],
        'next' => [
            'en' => 'Next',
            'vi' => 'Tiếp'
        ],
        'showing' => [
            'en' => 'Showing',
            'vi' => 'Hiển thị'
        ],
        'to' => [
            'en' => 'to',
            'vi' => 'đến'
        ],
        'of' => [
            'en' => 'of',
            'vi' => 'trong'
        ],
        'results' => [
            'en' => 'results',
            'vi' => 'kết quả'
        ]
    ],

    // Core messages (15 usages)
    'core' => [
        'messages' => [
            'error_occurred' => [
                'en' => 'An error occurred',
                'vi' => 'Có lỗi xảy ra'
            ],
            'image_not_found' => [
                'en' => 'Image not found',
                'vi' => 'Không tìm thấy hình ảnh'
            ],
            'loading' => [
                'en' => 'Loading...',
                'vi' => 'Đang tải...'
            ],
            'success' => [
                'en' => 'Success',
                'vi' => 'Thành công'
            ],
            'failed' => [
                'en' => 'Failed',
                'vi' => 'Thất bại'
            ],
            'please_wait' => [
                'en' => 'Please wait',
                'vi' => 'Vui lòng đợi'
            ],
            'try_again' => [
                'en' => 'Please try again',
                'vi' => 'Vui lòng thử lại'
            ]
        ]
    ],

    // UI Status keys
    'ui' => [
        'status' => [
            'sticky' => [
                'en' => 'Sticky',
                'vi' => 'Ghim'
            ],
            'locked' => [
                'en' => 'Locked',
                'vi' => 'Khóa'
            ],
            'featured' => [
                'en' => 'Featured',
                'vi' => 'Nổi bật'
            ],
            'active' => [
                'en' => 'Active',
                'vi' => 'Hoạt động'
            ],
            'inactive' => [
                'en' => 'Inactive',
                'vi' => 'Không hoạt động'
            ]
        ]
    ],

    // Common buttons and actions
    'buttons' => [
        'view_all' => [
            'en' => 'View All',
            'vi' => 'Xem tất cả'
        ],
        'load_more' => [
            'en' => 'Load More',
            'vi' => 'Tải thêm'
        ],
        'show_more' => [
            'en' => 'Show More',
            'vi' => 'Hiển thị thêm'
        ],
        'show_less' => [
            'en' => 'Show Less',
            'vi' => 'Hiển thị ít hơn'
        ],
        'read_more' => [
            'en' => 'Read More',
            'vi' => 'Đọc thêm'
        ],
        'back' => [
            'en' => 'Back',
            'vi' => 'Quay lại'
        ],
        'continue' => [
            'en' => 'Continue',
            'vi' => 'Tiếp tục'
        ],
        'submit' => [
            'en' => 'Submit',
            'vi' => 'Gửi'
        ],
        'reset' => [
            'en' => 'Reset',
            'vi' => 'Đặt lại'
        ],
        'refresh' => [
            'en' => 'Refresh',
            'vi' => 'Làm mới'
        ]
    ],

    // Navigation keys
    'nav' => [
        'main' => [
            'home' => [
                'en' => 'Home',
                'vi' => 'Trang chủ'
            ],
            'community' => [
                'en' => 'Community',
                'vi' => 'Cộng đồng'
            ],
            'forum' => [
                'en' => 'Forum',
                'vi' => 'Diễn đàn'
            ],
            'marketplace' => [
                'en' => 'Marketplace',
                'vi' => 'Chợ'
            ],
            'showcase' => [
                'en' => 'Showcase',
                'vi' => 'Showcase'
            ]
        ]
    ],

    // Common form elements
    'forms' => [
        'search_placeholder' => [
            'en' => 'Search...',
            'vi' => 'Tìm kiếm...'
        ],
        'email_placeholder' => [
            'en' => 'Enter your email',
            'vi' => 'Nhập email của bạn'
        ],
        'password_placeholder' => [
            'en' => 'Enter your password',
            'vi' => 'Nhập mật khẩu của bạn'
        ],
        'required_field' => [
            'en' => 'This field is required',
            'vi' => 'Trường này là bắt buộc'
        ],
        'optional' => [
            'en' => 'Optional',
            'vi' => 'Tùy chọn'
        ]
    ],

    // Time and date
    'time' => [
        'just_now' => [
            'en' => 'Just now',
            'vi' => 'Vừa xong'
        ],
        'minutes_ago' => [
            'en' => 'minutes ago',
            'vi' => 'phút trước'
        ],
        'hours_ago' => [
            'en' => 'hours ago',
            'vi' => 'giờ trước'
        ],
        'days_ago' => [
            'en' => 'days ago',
            'vi' => 'ngày trước'
        ],
        'weeks_ago' => [
            'en' => 'weeks ago',
            'vi' => 'tuần trước'
        ],
        'months_ago' => [
            'en' => 'months ago',
            'vi' => 'tháng trước'
        ]
    ]
];

echo "📝 CREATING/UPDATING TRANSLATION FILES\n";
echo "======================================\n";

$totalFilesUpdated = 0;
$totalKeysAdded = 0;

foreach ($priorityKeys as $category => $categoryData) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/$category.php";
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

        // Merge new translations
        $mergedTranslations = array_merge_recursive($existingTranslations, $categoryData);

        // Generate file content
        $fileContent = "<?php\n\n/**\n * " . ucfirst($category) . " translations\n * Updated: " . date('Y-m-d H:i:s') . "\n * Priority missing keys for frontend\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";

        // Save file
        file_put_contents($filePath, $fileContent);

        $keyCount = countNestedKeys($categoryData);
        echo "✅ Updated: $locale/$category.php ($keyCount keys)\n";
        $totalFilesUpdated++;
        $totalKeysAdded += $keyCount;
    }
}

function countNestedKeys($array) {
    $count = 0;
    foreach ($array as $value) {
        if (is_array($value)) {
            $count += countNestedKeys($value);
        } else {
            $count++;
        }
    }
    return $count;
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Files updated: $totalFilesUpdated\n";
echo "Keys added: $totalKeysAdded\n";

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

echo "\n🧪 TESTING PRIORITY KEYS...\n";
echo "===========================\n";

$testKeys = [
    'pagination.previous',
    'pagination.next',
    'core.messages.error_occurred',
    'core.messages.image_not_found',
    'ui.status.sticky',
    'ui.status.locked',
    'buttons.view_all',
    'nav.main.home',
    'forms.search_placeholder',
    'time.just_now'
];

$workingCount = 0;
foreach ($testKeys as $key) {
    try {
        $result = __($key);
        if (is_string($result) && $result !== $key) {
            echo "✅ __('$key') → '$result'\n";
            $workingCount++;
        } else {
            echo "❌ __('$key') - Still not working\n";
        }
    } catch (Exception $e) {
        echo "❌ __('$key') - Error: " . $e->getMessage() . "\n";
    }
}

echo "\nPriority keys success rate: " . round(($workingCount / count($testKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Added high-priority missing keys (non-admin)\n";
echo "2. 🔄 Test in browser to verify pagination works\n";
echo "3. 🔄 Check core messages display properly\n";
echo "4. 🔄 Run full key count analysis again\n";
echo "5. 🔄 Consider cleaning up unused keys\n\n";

echo "💡 IMPACT\n";
echo "=========\n";
echo "These keys address the most frequently used missing translations:\n";
echo "- Pagination (28 usages) - Critical for user navigation\n";
echo "- Core messages (15 usages) - Important for error handling\n";
echo "- UI status (10+ usages) - Essential for content status\n";
echo "- Common buttons - Improves user experience\n";
echo "- Navigation - Core site functionality\n\n";

echo "🚫 EXCLUDED\n";
echo "===========\n";
echo "Admin-related keys were intentionally excluded as requested.\n";
echo "Focus remains on frontend user experience improvements.\n";
