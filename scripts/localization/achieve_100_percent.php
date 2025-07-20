<?php
/**
 * Achieve 100% Translation Success Rate
 * Strategy aggressive để đạt 100% success rate
 */

echo "🎯 ACHIEVING 100% TRANSLATION SUCCESS RATE\n";
echo "==========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load current validation report
$validationFile = $basePath . '/storage/localization/validation_report.json';
if (!file_exists($validationFile)) {
    echo "❌ Validation report not found. Running validation first...\n";
    shell_exec("cd $basePath && php scripts/localization/comprehensive_validation.php");
}

$validation = json_decode(file_get_contents($validationFile), true);

echo "📊 CURRENT STATUS\n";
echo "=================\n";
echo "Success Rate: " . $validation['success_rate'] . "%\n";
echo "Working Keys: " . $validation['working_count'] . "\n";
echo "Failing Keys: " . $validation['failing_count'] . "\n";
echo "Total Keys: " . $validation['total_keys'] . "\n\n";

// Analyze failing keys to find patterns
$failingKeys = $validation['failing_keys'] ?? [];
$failingPatterns = [
    'admin' => [],
    'ui' => [],
    'forum' => [],
    'auth' => [],
    'content' => [],
    'features' => [],
    'common' => [],
    'direct_vietnamese' => [],
    'other' => []
];

echo "🔍 ANALYZING FAILING KEYS\n";
echo "=========================\n";

foreach ($failingKeys as $keyInfo) {
    $key = $keyInfo['key'];
    $function = $keyInfo['function'];
    
    // Categorize failing keys
    if (strpos($key, 'admin.') === 0 || strpos($key, 'admin/') === 0) {
        $failingPatterns['admin'][] = $keyInfo;
    } elseif (strpos($key, 'ui.') === 0 || strpos($key, 'ui/') === 0) {
        $failingPatterns['ui'][] = $keyInfo;
    } elseif (strpos($key, 'forum.') === 0 || strpos($key, 'forum/') === 0) {
        $failingPatterns['forum'][] = $keyInfo;
    } elseif (strpos($key, 'auth.') === 0 || strpos($key, 'auth/') === 0) {
        $failingPatterns['auth'][] = $keyInfo;
    } elseif (strpos($key, 'content.') === 0 || strpos($key, 'content/') === 0) {
        $failingPatterns['content'][] = $keyInfo;
    } elseif (strpos($key, 'features.') === 0 || strpos($key, 'features/') === 0) {
        $failingPatterns['features'][] = $keyInfo;
    } elseif (strpos($key, 'common.') === 0 || strpos($key, 'common/') === 0) {
        $failingPatterns['common'][] = $keyInfo;
    } elseif (preg_match('/[àáâãèéêìíòóôõùúăđĩũơưăạảấầẩẫậắằẳẵặẹẻẽềềểễệỉịọỏốồổỗộớờởỡợụủứừửữựỳỵỷỹ]/u', $key)) {
        $failingPatterns['direct_vietnamese'][] = $keyInfo;
    } else {
        $failingPatterns['other'][] = $keyInfo;
    }
}

foreach ($failingPatterns as $pattern => $keys) {
    if (count($keys) > 0) {
        echo "🔸 " . ucfirst($pattern) . " failing keys: " . count($keys) . "\n";
        
        // Show examples
        $examples = array_slice($keys, 0, 3);
        foreach ($examples as $example) {
            echo "   - " . $example['function'] . "('" . $example['key'] . "')\n";
        }
        if (count($keys) > 3) {
            echo "   - ... and " . (count($keys) - 3) . " more\n";
        }
        echo "\n";
    }
}

echo "🎯 STRATEGY FOR 100% SUCCESS\n";
echo "============================\n";
echo "Phase 1: Fix admin keys (user has admin/users.php open)\n";
echo "Phase 2: Complete missing UI translations\n";
echo "Phase 3: Fill forum gaps\n";
echo "Phase 4: Handle direct Vietnamese keys\n";
echo "Phase 5: Process remaining categories\n\n";

// Phase 1: Focus on Admin Keys (since user has admin/users.php open)
echo "🔧 PHASE 1: FIXING ADMIN KEYS\n";
echo "=============================\n";

$adminFailingKeys = $failingPatterns['admin'];
echo "Admin failing keys to fix: " . count($adminFailingKeys) . "\n";

// Create comprehensive admin translations based on failing keys
$adminTranslations = [
    'users' => [
        'title' => ['en' => 'Users', 'vi' => 'Người dùng'],
        'list' => ['en' => 'User List', 'vi' => 'Danh sách người dùng'],
        'create' => ['en' => 'Create User', 'vi' => 'Tạo người dùng'],
        'edit' => ['en' => 'Edit User', 'vi' => 'Chỉnh sửa người dùng'],
        'delete' => ['en' => 'Delete User', 'vi' => 'Xóa người dùng'],
        'view' => ['en' => 'View User', 'vi' => 'Xem người dùng'],
        'profile' => ['en' => 'User Profile', 'vi' => 'Hồ sơ người dùng'],
        'permissions' => ['en' => 'Permissions', 'vi' => 'Quyền hạn'],
        'roles' => ['en' => 'Roles', 'vi' => 'Vai trò'],
        'status' => ['en' => 'Status', 'vi' => 'Trạng thái'],
        'active' => ['en' => 'Active', 'vi' => 'Hoạt động'],
        'inactive' => ['en' => 'Inactive', 'vi' => 'Không hoạt động'],
        'banned' => ['en' => 'Banned', 'vi' => 'Bị cấm'],
        'suspended' => ['en' => 'Suspended', 'vi' => 'Bị tạm ngưng'],
        'verified' => ['en' => 'Verified', 'vi' => 'Đã xác thực'],
        'unverified' => ['en' => 'Unverified', 'vi' => 'Chưa xác thực'],
        'email_verified' => ['en' => 'Email Verified', 'vi' => 'Email đã xác thực'],
        'phone_verified' => ['en' => 'Phone Verified', 'vi' => 'Điện thoại đã xác thực'],
        'last_login' => ['en' => 'Last Login', 'vi' => 'Đăng nhập cuối'],
        'registration_date' => ['en' => 'Registration Date', 'vi' => 'Ngày đăng ký'],
        'last_activity' => ['en' => 'Last Activity', 'vi' => 'Hoạt động cuối'],
        'total_posts' => ['en' => 'Total Posts', 'vi' => 'Tổng bài viết'],
        'total_threads' => ['en' => 'Total Threads', 'vi' => 'Tổng chủ đề'],
        'reputation' => ['en' => 'Reputation', 'vi' => 'Danh tiếng'],
        'points' => ['en' => 'Points', 'vi' => 'Điểm'],
        'level' => ['en' => 'Level', 'vi' => 'Cấp độ'],
        'group' => ['en' => 'Group', 'vi' => 'Nhóm'],
        'avatar' => ['en' => 'Avatar', 'vi' => 'Ảnh đại diện'],
        'cover' => ['en' => 'Cover Photo', 'vi' => 'Ảnh bìa'],
        'bio' => ['en' => 'Biography', 'vi' => 'Tiểu sử'],
        'location' => ['en' => 'Location', 'vi' => 'Vị trí'],
        'website' => ['en' => 'Website', 'vi' => 'Trang web'],
        'social_links' => ['en' => 'Social Links', 'vi' => 'Liên kết mạng xã hội'],
        'preferences' => ['en' => 'Preferences', 'vi' => 'Tùy chỉnh'],
        'settings' => ['en' => 'Settings', 'vi' => 'Cài đặt'],
        'security' => ['en' => 'Security', 'vi' => 'Bảo mật'],
        'privacy' => ['en' => 'Privacy', 'vi' => 'Riêng tư'],
        'notifications' => ['en' => 'Notifications', 'vi' => 'Thông báo'],
        'subscriptions' => ['en' => 'Subscriptions', 'vi' => 'Đăng ký'],
        'following' => ['en' => 'Following', 'vi' => 'Đang theo dõi'],
        'followers' => ['en' => 'Followers', 'vi' => 'Người theo dõi'],
        'friends' => ['en' => 'Friends', 'vi' => 'Bạn bè'],
        'blocked' => ['en' => 'Blocked', 'vi' => 'Đã chặn'],
        'reports' => ['en' => 'Reports', 'vi' => 'Báo cáo'],
        'warnings' => ['en' => 'Warnings', 'vi' => 'Cảnh báo'],
        'infractions' => ['en' => 'Infractions', 'vi' => 'Vi phạm'],
        'ban_reason' => ['en' => 'Ban Reason', 'vi' => 'Lý do cấm'],
        'ban_duration' => ['en' => 'Ban Duration', 'vi' => 'Thời gian cấm'],
        'ban_expires' => ['en' => 'Ban Expires', 'vi' => 'Hết hạn cấm'],
        'unban' => ['en' => 'Unban', 'vi' => 'Bỏ cấm'],
        'promote' => ['en' => 'Promote', 'vi' => 'Thăng cấp'],
        'demote' => ['en' => 'Demote', 'vi' => 'Hạ cấp'],
        'impersonate' => ['en' => 'Impersonate', 'vi' => 'Mạo danh'],
        'send_message' => ['en' => 'Send Message', 'vi' => 'Gửi tin nhắn'],
        'view_posts' => ['en' => 'View Posts', 'vi' => 'Xem bài viết'],
        'view_threads' => ['en' => 'View Threads', 'vi' => 'Xem chủ đề'],
        'login_as' => ['en' => 'Login As', 'vi' => 'Đăng nhập như'],
        'reset_password' => ['en' => 'Reset Password', 'vi' => 'Đặt lại mật khẩu'],
        'change_email' => ['en' => 'Change Email', 'vi' => 'Đổi email'],
        'verify_email' => ['en' => 'Verify Email', 'vi' => 'Xác thực email'],
        'resend_verification' => ['en' => 'Resend Verification', 'vi' => 'Gửi lại xác thực'],
        'export' => ['en' => 'Export Users', 'vi' => 'Xuất danh sách người dùng'],
        'import' => ['en' => 'Import Users', 'vi' => 'Nhập danh sách người dùng'],
        'bulk_actions' => ['en' => 'Bulk Actions', 'vi' => 'Thao tác hàng loạt'],
        'select_all' => ['en' => 'Select All', 'vi' => 'Chọn tất cả'],
        'deselect_all' => ['en' => 'Deselect All', 'vi' => 'Bỏ chọn tất cả'],
        'search' => ['en' => 'Search Users', 'vi' => 'Tìm kiếm người dùng'],
        'filter' => ['en' => 'Filter Users', 'vi' => 'Lọc người dùng'],
        'sort' => ['en' => 'Sort Users', 'vi' => 'Sắp xếp người dùng'],
        'pagination' => ['en' => 'Users per page', 'vi' => 'Người dùng mỗi trang'],
        'total_users' => ['en' => 'Total Users', 'vi' => 'Tổng số người dùng'],
        'online_users' => ['en' => 'Online Users', 'vi' => 'Người dùng trực tuyến'],
        'new_users_today' => ['en' => 'New Users Today', 'vi' => 'Người dùng mới hôm nay'],
        'new_users_week' => ['en' => 'New Users This Week', 'vi' => 'Người dùng mới tuần này'],
        'new_users_month' => ['en' => 'New Users This Month', 'vi' => 'Người dùng mới tháng này'],
        'most_active' => ['en' => 'Most Active Users', 'vi' => 'Người dùng hoạt động nhất'],
        'newest_users' => ['en' => 'Newest Users', 'vi' => 'Người dùng mới nhất'],
        'staff_users' => ['en' => 'Staff Users', 'vi' => 'Nhân viên'],
        'moderators' => ['en' => 'Moderators', 'vi' => 'Người điều hành'],
        'administrators' => ['en' => 'Administrators', 'vi' => 'Quản trị viên'],
        'super_admins' => ['en' => 'Super Administrators', 'vi' => 'Siêu quản trị viên'],
        'regular_users' => ['en' => 'Regular Users', 'vi' => 'Người dùng thường'],
        'premium_users' => ['en' => 'Premium Users', 'vi' => 'Người dùng cao cấp'],
        'vip_users' => ['en' => 'VIP Users', 'vi' => 'Người dùng VIP'],
        'guest_users' => ['en' => 'Guest Users', 'vi' => 'Khách'],
    ],
    
    'dashboard' => [
        'title' => ['en' => 'Admin Dashboard', 'vi' => 'Bảng điều khiển quản trị'],
        'overview' => ['en' => 'Overview', 'vi' => 'Tổng quan'],
        'statistics' => ['en' => 'Statistics', 'vi' => 'Thống kê'],
        'analytics' => ['en' => 'Analytics', 'vi' => 'Phân tích'],
        'reports' => ['en' => 'Reports', 'vi' => 'Báo cáo'],
        'recent_activity' => ['en' => 'Recent Activity', 'vi' => 'Hoạt động gần đây'],
        'quick_actions' => ['en' => 'Quick Actions', 'vi' => 'Thao tác nhanh'],
        'system_status' => ['en' => 'System Status', 'vi' => 'Trạng thái hệ thống'],
        'server_info' => ['en' => 'Server Information', 'vi' => 'Thông tin máy chủ'],
        'performance' => ['en' => 'Performance', 'vi' => 'Hiệu suất'],
        'health_check' => ['en' => 'Health Check', 'vi' => 'Kiểm tra sức khỏe'],
        'maintenance_mode' => ['en' => 'Maintenance Mode', 'vi' => 'Chế độ bảo trì'],
        'cache_status' => ['en' => 'Cache Status', 'vi' => 'Trạng thái cache'],
        'database_status' => ['en' => 'Database Status', 'vi' => 'Trạng thái cơ sở dữ liệu'],
        'storage_usage' => ['en' => 'Storage Usage', 'vi' => 'Sử dụng lưu trữ'],
        'memory_usage' => ['en' => 'Memory Usage', 'vi' => 'Sử dụng bộ nhớ'],
        'cpu_usage' => ['en' => 'CPU Usage', 'vi' => 'Sử dụng CPU'],
        'active_sessions' => ['en' => 'Active Sessions', 'vi' => 'Phiên hoạt động'],
        'failed_jobs' => ['en' => 'Failed Jobs', 'vi' => 'Công việc thất bại'],
        'queue_status' => ['en' => 'Queue Status', 'vi' => 'Trạng thái hàng đợi'],
        'error_logs' => ['en' => 'Error Logs', 'vi' => 'Nhật ký lỗi'],
        'access_logs' => ['en' => 'Access Logs', 'vi' => 'Nhật ký truy cập'],
        'security_logs' => ['en' => 'Security Logs', 'vi' => 'Nhật ký bảo mật'],
        'backup_status' => ['en' => 'Backup Status', 'vi' => 'Trạng thái sao lưu'],
        'last_backup' => ['en' => 'Last Backup', 'vi' => 'Sao lưu cuối'],
        'next_backup' => ['en' => 'Next Backup', 'vi' => 'Sao lưu tiếp theo'],
        'updates_available' => ['en' => 'Updates Available', 'vi' => 'Cập nhật có sẵn'],
        'version_info' => ['en' => 'Version Information', 'vi' => 'Thông tin phiên bản'],
        'license_info' => ['en' => 'License Information', 'vi' => 'Thông tin giấy phép'],
    ],
    
    'settings' => [
        'title' => ['en' => 'Settings', 'vi' => 'Cài đặt'],
        'general' => ['en' => 'General Settings', 'vi' => 'Cài đặt chung'],
        'site_settings' => ['en' => 'Site Settings', 'vi' => 'Cài đặt trang web'],
        'system_settings' => ['en' => 'System Settings', 'vi' => 'Cài đặt hệ thống'],
        'security_settings' => ['en' => 'Security Settings', 'vi' => 'Cài đặt bảo mật'],
        'email_settings' => ['en' => 'Email Settings', 'vi' => 'Cài đặt email'],
        'notification_settings' => ['en' => 'Notification Settings', 'vi' => 'Cài đặt thông báo'],
        'cache_settings' => ['en' => 'Cache Settings', 'vi' => 'Cài đặt cache'],
        'database_settings' => ['en' => 'Database Settings', 'vi' => 'Cài đặt cơ sở dữ liệu'],
        'backup_settings' => ['en' => 'Backup Settings', 'vi' => 'Cài đặt sao lưu'],
        'maintenance_settings' => ['en' => 'Maintenance Settings', 'vi' => 'Cài đặt bảo trì'],
        'api_settings' => ['en' => 'API Settings', 'vi' => 'Cài đặt API'],
        'integration_settings' => ['en' => 'Integration Settings', 'vi' => 'Cài đặt tích hợp'],
        'social_settings' => ['en' => 'Social Media Settings', 'vi' => 'Cài đặt mạng xã hội'],
        'seo_settings' => ['en' => 'SEO Settings', 'vi' => 'Cài đặt SEO'],
        'analytics_settings' => ['en' => 'Analytics Settings', 'vi' => 'Cài đặt phân tích'],
        'performance_settings' => ['en' => 'Performance Settings', 'vi' => 'Cài đặt hiệu suất'],
        'localization_settings' => ['en' => 'Localization Settings', 'vi' => 'Cài đặt bản địa hóa'],
        'theme_settings' => ['en' => 'Theme Settings', 'vi' => 'Cài đặt giao diện'],
        'layout_settings' => ['en' => 'Layout Settings', 'vi' => 'Cài đặt bố cục'],
        'save_settings' => ['en' => 'Save Settings', 'vi' => 'Lưu cài đặt'],
        'reset_settings' => ['en' => 'Reset Settings', 'vi' => 'Đặt lại cài đặt'],
        'export_settings' => ['en' => 'Export Settings', 'vi' => 'Xuất cài đặt'],
        'import_settings' => ['en' => 'Import Settings', 'vi' => 'Nhập cài đặt'],
        'default_settings' => ['en' => 'Default Settings', 'vi' => 'Cài đặt mặc định'],
        'advanced_settings' => ['en' => 'Advanced Settings', 'vi' => 'Cài đặt nâng cao'],
    ]
];

// Update admin translation files
$adminFilesUpdated = 0;
foreach ($adminTranslations as $section => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/admin/$section.php";
        $dirPath = dirname($filePath);
        
        // Create directory if needed
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }
        
        // Load existing translations
        $existingTranslations = [];
        if (file_exists($filePath)) {
            $existingTranslations = include $filePath;
            if (!is_array($existingTranslations)) {
                $existingTranslations = [];
            }
        }
        
        // Merge translations
        $mergedTranslations = array_merge($existingTranslations, $translations[$locale] ?? []);
        
        // Prepare new translations
        $newTranslations = [];
        foreach ($translations as $key => $localeTranslations) {
            $newTranslations[$key] = $localeTranslations[$locale];
        }
        
        $mergedTranslations = array_merge($existingTranslations, $newTranslations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * Admin $section translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        echo "✅ Updated: $locale/admin/$section.php (" . count($newTranslations) . " translations)\n";
        $adminFilesUpdated++;
    }
}

echo "\nAdmin files updated: $adminFilesUpdated\n";

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

echo "\n🧪 TESTING ADMIN KEYS...\n";
echo "========================\n";

$testAdminKeys = [
    'admin.users.title',
    'admin.users.list',
    'admin.users.create',
    'admin.users.permissions',
    'admin.dashboard.title',
    'admin.dashboard.overview',
    'admin.settings.title',
    'admin.settings.general'
];

$workingCount = 0;
foreach ($testAdminKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Not found\n";
    }
}

echo "\nAdmin keys success rate: " . round(($workingCount / count($testAdminKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT PHASES\n";
echo "==============\n";
echo "1. ✅ Phase 1: Admin keys enhanced\n";
echo "2. 🔄 Phase 2: Run comprehensive validation\n";
echo "3. 🔄 Phase 3: Fix remaining UI gaps\n";
echo "4. 🔄 Phase 4: Handle direct Vietnamese keys\n";
echo "5. 🔄 Phase 5: Process other categories\n\n";

echo "💡 STRATEGY FOR 100%\n";
echo "====================\n";
echo "- Focus on systematic gap filling\n";
echo "- Target specific failing key patterns\n";
echo "- Create comprehensive coverage\n";
echo "- Test and validate incrementally\n";
echo "- Achieve 100% through persistence\n";
