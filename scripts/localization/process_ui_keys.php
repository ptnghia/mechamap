<?php
/**
 * Process UI Translation Keys
 * Xử lý 280 UI keys - highest user visibility impact
 */

echo "🎨 PROCESSING UI TRANSLATION KEYS\n";
echo "=================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/direct_keys_analysis.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

// Extract UI keys
$uiKeys = [];
$allDirectKeys = array_merge(
    $analysis['detailed_patterns']['dot_notation'] ?? [],
    $analysis['detailed_patterns']['slash_notation'] ?? [],
    $analysis['detailed_patterns']['simple_keys'] ?? [],
    $analysis['detailed_patterns']['mixed_notation'] ?? []
);

foreach ($allDirectKeys as $key => $files) {
    if (strpos($key, 'ui.') === 0 || strpos($key, 'ui/') === 0) {
        $uiKeys[$key] = $files;
    }
}

echo "📊 UI KEYS ANALYSIS\n";
echo "===================\n";
echo "Total UI keys found: " . count($uiKeys) . "\n";

// Analyze UI key patterns
$uiPatterns = [];
foreach ($uiKeys as $key => $files) {
    // Extract pattern: ui.section.subsection.key
    if (preg_match('/^ui\.([^.]+)\.(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($uiPatterns[$section])) {
            $uiPatterns[$section] = [];
        }
        $uiPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    } elseif (preg_match('/^ui\/([^\/]+)\/(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($uiPatterns[$section])) {
            $uiPatterns[$section] = [];
        }
        $uiPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    }
}

echo "\n📋 UI SECTIONS FOUND\n";
echo "====================\n";
foreach ($uiPatterns as $section => $keys) {
    echo "🔸 ui.$section: " . count($keys) . " keys\n";
    
    // Show examples
    $examples = array_slice(array_keys($keys), 0, 5);
    foreach ($examples as $example) {
        echo "   - ui.$section.$example\n";
    }
    if (count($keys) > 5) {
        echo "   - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

// Define comprehensive UI translations
$uiTranslations = [
    'common' => [
        'loading' => ['en' => 'Loading...', 'vi' => 'Đang tải...'],
        'save' => ['en' => 'Save', 'vi' => 'Lưu'],
        'cancel' => ['en' => 'Cancel', 'vi' => 'Hủy'],
        'delete' => ['en' => 'Delete', 'vi' => 'Xóa'],
        'edit' => ['en' => 'Edit', 'vi' => 'Chỉnh sửa'],
        'view' => ['en' => 'View', 'vi' => 'Xem'],
        'add' => ['en' => 'Add', 'vi' => 'Thêm'],
        'remove' => ['en' => 'Remove', 'vi' => 'Gỡ bỏ'],
        'search' => ['en' => 'Search', 'vi' => 'Tìm kiếm'],
        'filter' => ['en' => 'Filter', 'vi' => 'Lọc'],
        'sort' => ['en' => 'Sort', 'vi' => 'Sắp xếp'],
        'refresh' => ['en' => 'Refresh', 'vi' => 'Làm mới'],
        'close' => ['en' => 'Close', 'vi' => 'Đóng'],
        'open' => ['en' => 'Open', 'vi' => 'Mở'],
        'submit' => ['en' => 'Submit', 'vi' => 'Gửi'],
        'reset' => ['en' => 'Reset', 'vi' => 'Đặt lại'],
        'clear' => ['en' => 'Clear', 'vi' => 'Xóa'],
        'select' => ['en' => 'Select', 'vi' => 'Chọn'],
        'confirm' => ['en' => 'Confirm', 'vi' => 'Xác nhận'],
        'back' => ['en' => 'Back', 'vi' => 'Quay lại'],
        'next' => ['en' => 'Next', 'vi' => 'Tiếp theo'],
        'previous' => ['en' => 'Previous', 'vi' => 'Trước đó'],
        'first' => ['en' => 'First', 'vi' => 'Đầu tiên'],
        'last' => ['en' => 'Last', 'vi' => 'Cuối cùng'],
        'page' => ['en' => 'Page', 'vi' => 'Trang'],
        'of' => ['en' => 'of', 'vi' => 'của'],
        'total' => ['en' => 'Total', 'vi' => 'Tổng'],
        'showing' => ['en' => 'Showing', 'vi' => 'Hiển thị'],
        'results' => ['en' => 'results', 'vi' => 'kết quả'],
        'no_results' => ['en' => 'No results found', 'vi' => 'Không tìm thấy kết quả'],
        'all' => ['en' => 'All', 'vi' => 'Tất cả'],
        'none' => ['en' => 'None', 'vi' => 'Không có'],
        'yes' => ['en' => 'Yes', 'vi' => 'Có'],
        'no' => ['en' => 'No', 'vi' => 'Không'],
        'ok' => ['en' => 'OK', 'vi' => 'Đồng ý'],
        'error' => ['en' => 'Error', 'vi' => 'Lỗi'],
        'success' => ['en' => 'Success', 'vi' => 'Thành công'],
        'warning' => ['en' => 'Warning', 'vi' => 'Cảnh báo'],
        'info' => ['en' => 'Information', 'vi' => 'Thông tin'],
        'by' => ['en' => 'By', 'vi' => 'Bởi'],
        'on' => ['en' => 'on', 'vi' => 'vào'],
        'at' => ['en' => 'at', 'vi' => 'lúc'],
        'in' => ['en' => 'in', 'vi' => 'trong'],
        'replies' => ['en' => 'Replies', 'vi' => 'Phản hồi'],
        'views' => ['en' => 'Views', 'vi' => 'Lượt xem'],
        'likes' => ['en' => 'Likes', 'vi' => 'Lượt thích'],
        'shares' => ['en' => 'Shares', 'vi' => 'Chia sẻ'],
        'comments' => ['en' => 'Comments', 'vi' => 'Bình luận'],
        'posts' => ['en' => 'Posts', 'vi' => 'Bài viết'],
        'topics' => ['en' => 'Topics', 'vi' => 'Chủ đề'],
        'categories' => ['en' => 'Categories', 'vi' => 'Danh mục'],
        'tags' => ['en' => 'Tags', 'vi' => 'Thẻ'],
        'users' => ['en' => 'Users', 'vi' => 'Người dùng'],
        'members' => ['en' => 'Members', 'vi' => 'Thành viên'],
        'online' => ['en' => 'Online', 'vi' => 'Trực tuyến'],
        'offline' => ['en' => 'Offline', 'vi' => 'Ngoại tuyến'],
        'active' => ['en' => 'Active', 'vi' => 'Hoạt động'],
        'inactive' => ['en' => 'Inactive', 'vi' => 'Không hoạt động'],
        'status' => ['en' => 'Status', 'vi' => 'Trạng thái'],
        'actions' => ['en' => 'Actions', 'vi' => 'Thao tác'],
        'options' => ['en' => 'Options', 'vi' => 'Tùy chọn'],
        'settings' => ['en' => 'Settings', 'vi' => 'Cài đặt'],
        'preferences' => ['en' => 'Preferences', 'vi' => 'Tùy chỉnh'],
        'profile' => ['en' => 'Profile', 'vi' => 'Hồ sơ'],
        'account' => ['en' => 'Account', 'vi' => 'Tài khoản'],
        'dashboard' => ['en' => 'Dashboard', 'vi' => 'Bảng điều khiển'],
        'home' => ['en' => 'Home', 'vi' => 'Trang chủ'],
        'menu' => ['en' => 'Menu', 'vi' => 'Menu'],
        'navigation' => ['en' => 'Navigation', 'vi' => 'Điều hướng'],
        'breadcrumb' => ['en' => 'Breadcrumb', 'vi' => 'Đường dẫn'],
        'sidebar' => ['en' => 'Sidebar', 'vi' => 'Thanh bên'],
        'header' => ['en' => 'Header', 'vi' => 'Đầu trang'],
        'footer' => ['en' => 'Footer', 'vi' => 'Chân trang'],
        'content' => ['en' => 'Content', 'vi' => 'Nội dung'],
        'title' => ['en' => 'Title', 'vi' => 'Tiêu đề'],
        'description' => ['en' => 'Description', 'vi' => 'Mô tả'],
        'name' => ['en' => 'Name', 'vi' => 'Tên'],
        'email' => ['en' => 'Email', 'vi' => 'Email'],
        'password' => ['en' => 'Password', 'vi' => 'Mật khẩu'],
        'username' => ['en' => 'Username', 'vi' => 'Tên đăng nhập'],
        'login' => ['en' => 'Login', 'vi' => 'Đăng nhập'],
        'logout' => ['en' => 'Logout', 'vi' => 'Đăng xuất'],
        'register' => ['en' => 'Register', 'vi' => 'Đăng ký'],
        'forgot_password' => ['en' => 'Forgot Password', 'vi' => 'Quên mật khẩu'],
        'remember_me' => ['en' => 'Remember Me', 'vi' => 'Ghi nhớ đăng nhập'],
    ],
    
    'buttons' => [
        'save' => ['en' => 'Save', 'vi' => 'Lưu'],
        'save_changes' => ['en' => 'Save Changes', 'vi' => 'Lưu thay đổi'],
        'save_and_continue' => ['en' => 'Save & Continue', 'vi' => 'Lưu và tiếp tục'],
        'cancel' => ['en' => 'Cancel', 'vi' => 'Hủy'],
        'delete' => ['en' => 'Delete', 'vi' => 'Xóa'],
        'edit' => ['en' => 'Edit', 'vi' => 'Chỉnh sửa'],
        'create' => ['en' => 'Create', 'vi' => 'Tạo'],
        'update' => ['en' => 'Update', 'vi' => 'Cập nhật'],
        'submit' => ['en' => 'Submit', 'vi' => 'Gửi'],
        'send' => ['en' => 'Send', 'vi' => 'Gửi'],
        'upload' => ['en' => 'Upload', 'vi' => 'Tải lên'],
        'download' => ['en' => 'Download', 'vi' => 'Tải xuống'],
        'export' => ['en' => 'Export', 'vi' => 'Xuất'],
        'import' => ['en' => 'Import', 'vi' => 'Nhập'],
        'print' => ['en' => 'Print', 'vi' => 'In'],
        'copy' => ['en' => 'Copy', 'vi' => 'Sao chép'],
        'paste' => ['en' => 'Paste', 'vi' => 'Dán'],
        'cut' => ['en' => 'Cut', 'vi' => 'Cắt'],
        'undo' => ['en' => 'Undo', 'vi' => 'Hoàn tác'],
        'redo' => ['en' => 'Redo', 'vi' => 'Làm lại'],
        'preview' => ['en' => 'Preview', 'vi' => 'Xem trước'],
        'publish' => ['en' => 'Publish', 'vi' => 'Xuất bản'],
        'draft' => ['en' => 'Draft', 'vi' => 'Bản nháp'],
        'archive' => ['en' => 'Archive', 'vi' => 'Lưu trữ'],
        'restore' => ['en' => 'Restore', 'vi' => 'Khôi phục'],
        'approve' => ['en' => 'Approve', 'vi' => 'Duyệt'],
        'reject' => ['en' => 'Reject', 'vi' => 'Từ chối'],
        'enable' => ['en' => 'Enable', 'vi' => 'Bật'],
        'disable' => ['en' => 'Disable', 'vi' => 'Tắt'],
        'activate' => ['en' => 'Activate', 'vi' => 'Kích hoạt'],
        'deactivate' => ['en' => 'Deactivate', 'vi' => 'Vô hiệu hóa'],
        'lock' => ['en' => 'Lock', 'vi' => 'Khóa'],
        'unlock' => ['en' => 'Unlock', 'vi' => 'Mở khóa'],
        'pin' => ['en' => 'Pin', 'vi' => 'Ghim'],
        'unpin' => ['en' => 'Unpin', 'vi' => 'Bỏ ghim'],
        'feature' => ['en' => 'Feature', 'vi' => 'Nổi bật'],
        'unfeature' => ['en' => 'Unfeature', 'vi' => 'Bỏ nổi bật'],
        'hide' => ['en' => 'Hide', 'vi' => 'Ẩn'],
        'show' => ['en' => 'Show', 'vi' => 'Hiện'],
        'expand' => ['en' => 'Expand', 'vi' => 'Mở rộng'],
        'collapse' => ['en' => 'Collapse', 'vi' => 'Thu gọn'],
        'maximize' => ['en' => 'Maximize', 'vi' => 'Phóng to'],
        'minimize' => ['en' => 'Minimize', 'vi' => 'Thu nhỏ'],
        'fullscreen' => ['en' => 'Fullscreen', 'vi' => 'Toàn màn hình'],
        'exit_fullscreen' => ['en' => 'Exit Fullscreen', 'vi' => 'Thoát toàn màn hình'],
    ],
    
    'forms' => [
        'required' => ['en' => 'Required', 'vi' => 'Bắt buộc'],
        'optional' => ['en' => 'Optional', 'vi' => 'Tùy chọn'],
        'placeholder' => ['en' => 'Enter text...', 'vi' => 'Nhập văn bản...'],
        'search_placeholder' => ['en' => 'Search...', 'vi' => 'Tìm kiếm...'],
        'email_placeholder' => ['en' => 'Enter your email', 'vi' => 'Nhập email của bạn'],
        'password_placeholder' => ['en' => 'Enter your password', 'vi' => 'Nhập mật khẩu của bạn'],
        'confirm_password' => ['en' => 'Confirm Password', 'vi' => 'Xác nhận mật khẩu'],
        'choose_file' => ['en' => 'Choose File', 'vi' => 'Chọn tệp'],
        'no_file_chosen' => ['en' => 'No file chosen', 'vi' => 'Chưa chọn tệp'],
        'select_option' => ['en' => 'Select an option', 'vi' => 'Chọn một tùy chọn'],
        'select_multiple' => ['en' => 'Select multiple options', 'vi' => 'Chọn nhiều tùy chọn'],
        'search_conversations_placeholder' => ['en' => 'Search conversations...', 'vi' => 'Tìm kiếm cuộc trò chuyện...'],
        'validation_error' => ['en' => 'Please correct the errors below', 'vi' => 'Vui lòng sửa các lỗi bên dưới'],
        'field_required' => ['en' => 'This field is required', 'vi' => 'Trường này là bắt buộc'],
        'invalid_email' => ['en' => 'Please enter a valid email address', 'vi' => 'Vui lòng nhập địa chỉ email hợp lệ'],
        'password_mismatch' => ['en' => 'Passwords do not match', 'vi' => 'Mật khẩu không khớp'],
        'file_too_large' => ['en' => 'File is too large', 'vi' => 'Tệp quá lớn'],
        'invalid_file_type' => ['en' => 'Invalid file type', 'vi' => 'Loại tệp không hợp lệ'],
    ],
    
    'pagination' => [
        'previous' => ['en' => 'Previous', 'vi' => 'Trước'],
        'next' => ['en' => 'Next', 'vi' => 'Tiếp'],
        'first' => ['en' => 'First', 'vi' => 'Đầu'],
        'last' => ['en' => 'Last', 'vi' => 'Cuối'],
        'page' => ['en' => 'Page', 'vi' => 'Trang'],
        'of' => ['en' => 'of', 'vi' => 'của'],
        'showing' => ['en' => 'Showing', 'vi' => 'Hiển thị'],
        'to' => ['en' => 'to', 'vi' => 'đến'],
        'results' => ['en' => 'results', 'vi' => 'kết quả'],
        'per_page' => ['en' => 'per page', 'vi' => 'mỗi trang'],
        'go_to_page' => ['en' => 'Go to page', 'vi' => 'Đi đến trang'],
    ],
    
    'actions' => [
        'view_full_showcase' => ['en' => 'View Full Showcase', 'vi' => 'Xem showcase đầy đủ'],
        'view_details' => ['en' => 'View Details', 'vi' => 'Xem chi tiết'],
        'read_more' => ['en' => 'Read More', 'vi' => 'Đọc thêm'],
        'show_less' => ['en' => 'Show Less', 'vi' => 'Hiện ít hơn'],
        'load_more' => ['en' => 'Load More', 'vi' => 'Tải thêm'],
        'see_all' => ['en' => 'See All', 'vi' => 'Xem tất cả'],
        'view_all' => ['en' => 'View All', 'vi' => 'Xem tất cả'],
        'show_all' => ['en' => 'Show All', 'vi' => 'Hiện tất cả'],
        'hide_all' => ['en' => 'Hide All', 'vi' => 'Ẩn tất cả'],
        'select_all' => ['en' => 'Select All', 'vi' => 'Chọn tất cả'],
        'deselect_all' => ['en' => 'Deselect All', 'vi' => 'Bỏ chọn tất cả'],
        'mark_all_read' => ['en' => 'Mark All as Read', 'vi' => 'Đánh dấu tất cả đã đọc'],
        'mark_as_read' => ['en' => 'Mark as Read', 'vi' => 'Đánh dấu đã đọc'],
        'mark_as_unread' => ['en' => 'Mark as Unread', 'vi' => 'Đánh dấu chưa đọc'],
        'reply' => ['en' => 'Reply', 'vi' => 'Trả lời'],
        'quote' => ['en' => 'Quote', 'vi' => 'Trích dẫn'],
        'report' => ['en' => 'Report', 'vi' => 'Báo cáo'],
        'share' => ['en' => 'Share', 'vi' => 'Chia sẻ'],
        'bookmark' => ['en' => 'Bookmark', 'vi' => 'Đánh dấu'],
        'unbookmark' => ['en' => 'Remove Bookmark', 'vi' => 'Bỏ đánh dấu'],
        'follow' => ['en' => 'Follow', 'vi' => 'Theo dõi'],
        'unfollow' => ['en' => 'Unfollow', 'vi' => 'Bỏ theo dõi'],
        'subscribe' => ['en' => 'Subscribe', 'vi' => 'Đăng ký'],
        'unsubscribe' => ['en' => 'Unsubscribe', 'vi' => 'Hủy đăng ký'],
        'like' => ['en' => 'Like', 'vi' => 'Thích'],
        'unlike' => ['en' => 'Unlike', 'vi' => 'Bỏ thích'],
        'upvote' => ['en' => 'Upvote', 'vi' => 'Bình chọn tích cực'],
        'downvote' => ['en' => 'Downvote', 'vi' => 'Bình chọn tiêu cực'],
    ]
];

echo "\n🔧 CREATING UI TRANSLATION FILES...\n";
echo "===================================\n";

$createdFiles = 0;
$totalTranslations = 0;

foreach ($uiTranslations as $section => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/ui/$section.php";
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
        
        // Add new translations
        $newTranslations = [];
        foreach ($translations as $key => $localeTranslations) {
            $newTranslations[$key] = $localeTranslations[$locale];
        }
        
        // Merge with existing
        $mergedTranslations = array_merge($existingTranslations, $newTranslations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * UI $section translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        echo "✅ Updated: $locale/ui/$section.php (" . count($newTranslations) . " translations)\n";
        $createdFiles++;
        $totalTranslations += count($newTranslations);
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "UI sections processed: " . count($uiTranslations) . "\n";
echo "Translation files created/updated: $createdFiles\n";
echo "Total translations added: $totalTranslations\n";

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

echo "\n🧪 TESTING UI KEYS...\n";
echo "=====================\n";

$testUIKeys = [
    'ui.common.loading',
    'ui.common.save',
    'ui.common.cancel',
    'ui.buttons.edit',
    'ui.forms.required',
    'ui.forms.search_conversations_placeholder',
    'ui.pagination.page',
    'ui.actions.view_full_showcase',
    'ui.actions.view_details'
];

$workingCount = 0;
foreach ($testUIKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Not found\n";
    }
}

echo "\nUI keys success rate: " . round(($workingCount / count($testUIKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Created comprehensive UI translations\n";
echo "2. 🔄 Process forum keys (129 keys) next\n";
echo "3. 🔄 Process auth keys (138 keys)\n";
echo "4. 🔄 Run comprehensive validation\n";
echo "5. 🔄 Test in browser for visual confirmation\n\n";

echo "💡 IMPACT ASSESSMENT\n";
echo "====================\n";
echo "UI keys have the highest user visibility impact.\n";
echo "Success with these keys will immediately improve user experience.\n";
echo "Focus on testing critical UI elements in browser next.\n";
