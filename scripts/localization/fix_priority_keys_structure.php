<?php
/**
 * Fix Priority Keys Structure
 * Sửa cấu trúc file translation để đúng format Laravel
 */

echo "🔧 FIXING PRIORITY KEYS STRUCTURE\n";
echo "=================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📝 CREATING CORRECT TRANSLATION STRUCTURE\n";
echo "=========================================\n";

// Define translations with correct structure
$translations = [
    'pagination' => [
        'en' => [
            'previous' => 'Previous',
            'next' => 'Next',
            'showing' => 'Showing',
            'to' => 'to',
            'of' => 'of',
            'results' => 'results',
            'first' => 'First',
            'last' => 'Last',
            'page' => 'Page',
            'pages' => 'Pages',
            'per_page' => 'per page',
            'no_results' => 'No results found'
        ],
        'vi' => [
            'previous' => 'Trước',
            'next' => 'Tiếp',
            'showing' => 'Hiển thị',
            'to' => 'đến',
            'of' => 'trong',
            'results' => 'kết quả',
            'first' => 'Đầu',
            'last' => 'Cuối',
            'page' => 'Trang',
            'pages' => 'Trang',
            'per_page' => 'mỗi trang',
            'no_results' => 'Không tìm thấy kết quả'
        ]
    ],
    
    'buttons' => [
        'en' => [
            'view_all' => 'View All',
            'load_more' => 'Load More',
            'show_more' => 'Show More',
            'show_less' => 'Show Less',
            'read_more' => 'Read More',
            'back' => 'Back',
            'continue' => 'Continue',
            'submit' => 'Submit',
            'reset' => 'Reset',
            'refresh' => 'Refresh',
            'close' => 'Close',
            'open' => 'Open',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'confirm' => 'Confirm'
        ],
        'vi' => [
            'view_all' => 'Xem tất cả',
            'load_more' => 'Tải thêm',
            'show_more' => 'Hiển thị thêm',
            'show_less' => 'Hiển thị ít hơn',
            'read_more' => 'Đọc thêm',
            'back' => 'Quay lại',
            'continue' => 'Tiếp tục',
            'submit' => 'Gửi',
            'reset' => 'Đặt lại',
            'refresh' => 'Làm mới',
            'close' => 'Đóng',
            'open' => 'Mở',
            'edit' => 'Sửa',
            'delete' => 'Xóa',
            'confirm' => 'Xác nhận'
        ]
    ],
    
    'nav' => [
        'en' => [
            'main' => [
                'home' => 'Home',
                'community' => 'Community',
                'forum' => 'Forum',
                'marketplace' => 'Marketplace',
                'showcase' => 'Showcase',
                'search' => 'Search',
                'profile' => 'Profile',
                'settings' => 'Settings',
                'logout' => 'Logout',
                'login' => 'Login'
            ]
        ],
        'vi' => [
            'main' => [
                'home' => 'Trang chủ',
                'community' => 'Cộng đồng',
                'forum' => 'Diễn đàn',
                'marketplace' => 'Chợ',
                'showcase' => 'Showcase',
                'search' => 'Tìm kiếm',
                'profile' => 'Hồ sơ',
                'settings' => 'Cài đặt',
                'logout' => 'Đăng xuất',
                'login' => 'Đăng nhập'
            ]
        ]
    ],
    
    'forms' => [
        'en' => [
            'search_placeholder' => 'Search...',
            'email_placeholder' => 'Enter your email',
            'password_placeholder' => 'Enter your password',
            'required_field' => 'This field is required',
            'optional' => 'Optional',
            'choose_file' => 'Choose file',
            'no_file_chosen' => 'No file chosen',
            'upload' => 'Upload',
            'browse' => 'Browse',
            'clear' => 'Clear'
        ],
        'vi' => [
            'search_placeholder' => 'Tìm kiếm...',
            'email_placeholder' => 'Nhập email của bạn',
            'password_placeholder' => 'Nhập mật khẩu của bạn',
            'required_field' => 'Trường này là bắt buộc',
            'optional' => 'Tùy chọn',
            'choose_file' => 'Chọn file',
            'no_file_chosen' => 'Chưa chọn file',
            'upload' => 'Tải lên',
            'browse' => 'Duyệt',
            'clear' => 'Xóa'
        ]
    ],
    
    'time' => [
        'en' => [
            'just_now' => 'Just now',
            'minutes_ago' => 'minutes ago',
            'hours_ago' => 'hours ago',
            'days_ago' => 'days ago',
            'weeks_ago' => 'weeks ago',
            'months_ago' => 'months ago',
            'years_ago' => 'years ago',
            'yesterday' => 'Yesterday',
            'today' => 'Today',
            'tomorrow' => 'Tomorrow',
            'last_week' => 'Last week',
            'next_week' => 'Next week'
        ],
        'vi' => [
            'just_now' => 'Vừa xong',
            'minutes_ago' => 'phút trước',
            'hours_ago' => 'giờ trước',
            'days_ago' => 'ngày trước',
            'weeks_ago' => 'tuần trước',
            'months_ago' => 'tháng trước',
            'years_ago' => 'năm trước',
            'yesterday' => 'Hôm qua',
            'today' => 'Hôm nay',
            'tomorrow' => 'Ngày mai',
            'last_week' => 'Tuần trước',
            'next_week' => 'Tuần tới'
        ]
    ]
];

// Also update core.php to fix nested structure
$coreTranslations = [
    'core' => [
        'en' => [
            'messages' => [
                'error_occurred' => 'An error occurred',
                'image_not_found' => 'Image not found',
                'loading' => 'Loading...',
                'success' => 'Success',
                'failed' => 'Failed',
                'please_wait' => 'Please wait',
                'try_again' => 'Please try again',
                'not_found' => 'Not found',
                'access_denied' => 'Access denied',
                'invalid_request' => 'Invalid request',
                'server_error' => 'Server error',
                'network_error' => 'Network error',
                'timeout' => 'Request timeout',
                'cancelled' => 'Cancelled'
            ]
        ],
        'vi' => [
            'messages' => [
                'error_occurred' => 'Có lỗi xảy ra',
                'image_not_found' => 'Không tìm thấy hình ảnh',
                'loading' => 'Đang tải...',
                'success' => 'Thành công',
                'failed' => 'Thất bại',
                'please_wait' => 'Vui lòng đợi',
                'try_again' => 'Vui lòng thử lại',
                'not_found' => 'Không tìm thấy',
                'access_denied' => 'Truy cập bị từ chối',
                'invalid_request' => 'Yêu cầu không hợp lệ',
                'server_error' => 'Lỗi máy chủ',
                'network_error' => 'Lỗi mạng',
                'timeout' => 'Hết thời gian chờ',
                'cancelled' => 'Đã hủy'
            ]
        ]
    ]
];

// Merge core translations
$allTranslations = array_merge($translations, $coreTranslations);

$totalFilesUpdated = 0;
$totalKeysAdded = 0;

foreach ($allTranslations as $category => $langData) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/$category.php";
        $dirPath = dirname($filePath);
        
        // Create directory if needed
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
            echo "📁 Created directory: " . str_replace($basePath . '/', '', $dirPath) . "\n";
        }
        
        // Get translations for this locale
        $localeTranslations = $langData[$locale];
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * " . ucfirst($category) . " translations ($locale)\n * Updated: " . date('Y-m-d H:i:s') . "\n * Fixed structure for Laravel compatibility\n */\n\nreturn " . var_export($localeTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        $keyCount = countNestedKeys($localeTranslations);
        echo "✅ Fixed: $locale/$category.php ($keyCount keys)\n";
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
echo "Files fixed: $totalFilesUpdated\n";
echo "Total keys: $totalKeysAdded\n";

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

$testKeys = [
    'pagination.previous',
    'pagination.next',
    'core.messages.error_occurred',
    'core.messages.image_not_found',
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

echo "\nFixed keys success rate: " . round(($workingCount / count($testKeys)) * 100, 1) . "%\n";

echo "\n🎯 IMPACT\n";
echo "=========\n";
echo "Fixed translation file structure to be Laravel-compatible.\n";
echo "These keys should now work properly in Blade templates.\n";
echo "Priority focus on pagination, buttons, navigation, and core messages.\n";
