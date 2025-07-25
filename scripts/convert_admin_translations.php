<?php
/**
 * Admin Panel Translation Conversion Script
 * Chuyển đổi translation keys thành hardcoded Vietnamese text
 */

echo "🔄 ADMIN PANEL TRANSLATION CONVERSION\n";
echo "=====================================\n\n";

$basePath = __DIR__ . '/../';
$adminViewsPath = $basePath . 'resources/views/admin/';

// Load translation mappings từ file JSON audit
$auditFile = $basePath . 'storage/admin_translation_audit.json';
if (!file_exists($auditFile)) {
    echo "❌ File audit không tồn tại. Vui lòng chạy admin_translation_audit.php trước.\n";
    exit(1);
}

$auditData = json_decode(file_get_contents($auditFile), true);
echo "📊 Loaded audit data: {$auditData['summary']['total_keys']} keys trong {$auditData['summary']['total_files']} files\n\n";

// Function để load translation mapping
function loadTranslationMapping() {
    return [
        // Common actions - Các hành động phổ biến
        'Hủy' => 'Hủy',
        'Xóa' => 'Xóa',
        'Lưu' => 'Lưu',
        'Cập nhật' => 'Cập nhật',
        'Tạo' => 'Tạo',
        'Chỉnh sửa' => 'Chỉnh sửa',
        'Xem' => 'Xem',
        'Sửa' => 'Sửa',
        'Quay lại' => 'Quay lại',
        'Lưu cấu hình' => 'Lưu cấu hình',
        'Lưu thay đổi' => 'Lưu thay đổi',
        'Duyệt' => 'Duyệt',
        'Từ chối' => 'Từ chối',
        'Tải lên' => 'Tải lên',
        'Tải xuống' => 'Tải xuống',
        'Xuất báo cáo' => 'Xuất báo cáo',
        'Lọc' => 'Lọc',
        'Xóa bộ lọc' => 'Xóa bộ lọc',

        // Common fields - Các trường phổ biến
        'Tiêu đề' => 'Tiêu đề',
        'Mô tả' => 'Mô tả',
        'Tên' => 'Tên',
        'ID' => 'ID',
        'Trạng thái' => 'Trạng thái',
        'Thứ tự' => 'Thứ tự',
        'Ngày tạo' => 'Ngày tạo',
        'Cập nhật lần cuối' => 'Cập nhật lần cuối',
        'Thao tác' => 'Thao tác',
        'Nội dung' => 'Nội dung',
        'Người dùng' => 'Người dùng',
        'Tác giả' => 'Tác giả',
        'Email' => 'Email',
        'Họ tên' => 'Họ tên',
        'Tên đăng nhập' => 'Tên đăng nhập',
        'Mật khẩu' => 'Mật khẩu',
        'Xác nhận mật khẩu' => 'Xác nhận mật khẩu',

        // Status - Trạng thái
        'Bản nháp' => 'Bản nháp',
        'Chờ duyệt' => 'Chờ duyệt',
        'Đã xuất bản' => 'Đã xuất bản',
        'Đã từ chối' => 'Đã từ chối',
        'Hoạt động' => 'Hoạt động',
        'Không hoạt động' => 'Không hoạt động',
        'Đang hoạt động' => 'Đang hoạt động',
        'Bị cấm' => 'Bị cấm',
        'Online' => 'Online',
        'Offline' => 'Offline',
        'Kích hoạt' => 'Kích hoạt',

        // User roles - Vai trò người dùng
        'Admin' => 'Admin',
        'Moderator' => 'Moderator',
        'Senior' => 'Senior',
        'Member' => 'Thành viên',
        'Khách' => 'Khách',
        'Thành viên' => 'Thành viên',
        'Quản trị viên' => 'Quản trị viên',

        // Confirmations - Xác nhận
        'Xác nhận xóa' => 'Xác nhận xóa',
        'Bạn có chắc chắn muốn xóa' => 'Bạn có chắc chắn muốn xóa',
        'Bạn có chắc chắn muốn xóa chuyên mục này?' => 'Bạn có chắc chắn muốn xóa chuyên mục này?',
        'Bạn có chắc chắn muốn xóa diễn đàn này?' => 'Bạn có chắc chắn muốn xóa diễn đàn này?',
        'Bạn có chắc chắn muốn xóa bài đăng này?' => 'Bạn có chắc chắn muốn xóa bài đăng này?',
        'Bạn có chắc chắn muốn xóa bình luận này?' => 'Bạn có chắc chắn muốn xóa bình luận này?',
        'Bạn có chắc chắn muốn xóa file này?' => 'Bạn có chắc chắn muốn xóa file này?',
        'Bạn có chắc chắn muốn xóa thành viên này?' => 'Bạn có chắc chắn muốn xóa thành viên này?',

        // Forms - Biểu mẫu
        'Tên chuyên mục' => 'Tên chuyên mục',
        'Tên diễn đàn' => 'Tên diễn đàn',
        'Chuyên mục cha' => 'Chuyên mục cha',
        'Diễn đàn cha' => 'Diễn đàn cha',
        'Không có' => 'Không có',
        'Chọn chuyên mục' => 'Chọn chuyên mục',
        'Chọn diễn đàn' => 'Chọn diễn đàn',
        'Chọn vai trò' => 'Chọn vai trò',
        'Vai trò' => 'Vai trò',
        'Chuyên mục' => 'Chuyên mục',

        // Settings - Cài đặt
        'Cấu hình chung' => 'Cấu hình chung',
        'Thông tin công ty' => 'Thông tin công ty',
        'Thông tin liên hệ' => 'Thông tin liên hệ',
        'API Keys' => 'API Keys',
        'Bản quyền' => 'Bản quyền',
        'Mạng xã hội' => 'Mạng xã hội',
        'Cài đặt' => 'Cài đặt',
        'Bảo mật' => 'Bảo mật',

        // Navigation - Điều hướng
        'Dashboard' => 'Bảng điều khiển',
        'Bảng điều khiển' => 'Bảng điều khiển',

        // Complex keys with context - Các key phức tạp
        'forum.forums.title' => 'Diễn đàn',
        'forum.threads.pinned' => 'Đã ghim',
        'forum.threads.locked' => 'Đã khóa',
        'nav.auth.register' => 'Đăng ký',
        'common.views' => 'Lượt xem',
        'ui.actions.search' => 'Tìm kiếm',
        'buttons.view_details' => 'Xem chi tiết',

        // Error messages - Thông báo lỗi
        'Error!' => 'Lỗi!',
        'Please check the form for errors.' => 'Vui lòng kiểm tra lỗi trong form.',

        // Content management - Quản lý nội dung
        'Danh sách chuyên mục' => 'Danh sách chuyên mục',
        'Danh sách diễn đàn' => 'Danh sách diễn đàn',
        'Danh sách bài đăng' => 'Danh sách bài đăng',
        'Danh sách bình luận' => 'Danh sách bình luận',
        'Danh sách thành viên' => 'Danh sách thành viên',
        'Tạo chuyên mục mới' => 'Tạo chuyên mục mới',
        'Tạo diễn đàn mới' => 'Tạo diễn đàn mới',
        'Tạo bài đăng mới' => 'Tạo bài đăng mới',
        'Thêm thành viên' => 'Thêm thành viên',
        'Tạo thành viên' => 'Tạo thành viên',

        // Statistics - Thống kê
        'Thống kê' => 'Thống kê',
        'Tổng số' => 'Tổng số',
        'Tổng' => 'Tổng',
        'Bài đăng' => 'Bài đăng',
        'Bình luận' => 'Bình luận',
        'Threads' => 'Chủ đề',
        'Posts' => 'Bài viết',

        // Filters - Bộ lọc
        'Bộ lọc' => 'Bộ lọc',
        'Tất cả' => 'Tất cả',
        'Tất cả trạng thái' => 'Tất cả trạng thái',
        'Tất cả vai trò' => 'Tất cả vai trò',

        // Media - Media
        'Thư viện Media' => 'Thư viện Media',
        'files' => 'files',
        'Loại file' => 'Loại file',
        'Hình ảnh' => 'Hình ảnh',
        'Video' => 'Video',
        'Âm thanh' => 'Âm thanh',
        'Tài liệu' => 'Tài liệu',
        'Tên file' => 'Tên file',
        'Kích thước' => 'Kích thước',
        'Ngày tải lên' => 'Ngày tải lên',
        'Người tải lên' => 'Người tải lên',

        // Time and dates - Thời gian
        'Ngày tham gia' => 'Ngày tham gia',
        'Hoạt động lần cuối' => 'Hoạt động lần cuối',
        'Hoạt động gần đây' => 'Hoạt động gần đây',
        'Chưa có' => 'Chưa có',

        // Special features - Tính năng đặc biệt
        'Nổi bật' => 'Nổi bật',
        'Ghim bài đăng' => 'Ghim bài đăng',
        'Bỏ ghim' => 'Bỏ ghim',
        'Khóa bài đăng' => 'Khóa bài đăng',
        'Mở khóa' => 'Mở khóa',
        'Đánh dấu nổi bật' => 'Đánh dấu nổi bật',
        'Bỏ nổi bật' => 'Bỏ nổi bật',
        'Duyệt bài đăng' => 'Duyệt bài đăng',
        'Từ chối bài đăng' => 'Từ chối bài đăng',

        // Moderation - Kiểm duyệt
        'Moderation' => 'Kiểm duyệt',
        'Đã đánh dấu' => 'Đã đánh dấu',
        'Đã ẩn' => 'Đã ẩn',
        'Hiện bình luận' => 'Hiện bình luận',
        'Ẩn bình luận' => 'Ẩn bình luận',
        'Đánh dấu' => 'Đánh dấu',
        'Bỏ đánh dấu' => 'Bỏ đánh dấu',
        'Cấm' => 'Cấm',
        'Bỏ cấm' => 'Bỏ cấm',
        'Cấm thành viên' => 'Cấm thành viên',
        'Bỏ cấm thành viên' => 'Bỏ cấm thành viên',
        'Lý do cấm' => 'Lý do cấm',
        'Lý do từ chối' => 'Lý do từ chối',

        // Additional keys from remaining scan
        'Hướng dẫn' => 'Hướng dẫn',
        'Cấu hình nâng cao' => 'Cấu hình nâng cao',
        'Điều hướng' => 'Điều hướng',
        'Xem trước' => 'Xem trước',
        'Ảnh đại diện' => 'Ảnh đại diện',
        'Robots.txt' => 'Robots.txt',
        'Sitemap' => 'Sitemap',
        'Social Media' => 'Social Media',
        'Slug' => 'Slug',
        'Tìm kiếm' => 'Tìm kiếm',
        'Tên danh mục' => 'Tên danh mục',
        'Danh mục' => 'Danh mục',
        'Lượt xem' => 'Lượt xem',
        'bài đăng' => 'bài đăng',
        'Từ khóa' => 'Từ khóa',
        'Canonical URL' => 'Canonical URL',
        'Cấu hình Open Graph (Facebook, LinkedIn, ...)' => 'Cấu hình Open Graph (Facebook, LinkedIn, ...)',
        'Tiêu đề Open Graph' => 'Tiêu đề Open Graph',
        'Mô tả Open Graph' => 'Mô tả Open Graph',
        'Hình ảnh Open Graph' => 'Hình ảnh Open Graph',
        'Tiêu đề Twitter' => 'Tiêu đề Twitter',
        'Mô tả Twitter' => 'Mô tả Twitter',
        'Hình ảnh Twitter' => 'Hình ảnh Twitter',
        'Xuất bản' => 'Xuất bản',
        'Meta Title' => 'Meta Title',
        'Meta Description' => 'Meta Description',
        'Meta Keywords' => 'Meta Keywords',
        'Chấp nhận các định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB.' => 'Chấp nhận các định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB.',
        'Giới thiệu' => 'Giới thiệu',
        'Chữ ký' => 'Chữ ký',
        'Đặt lại' => 'Đặt lại',
        'Số bài đăng' => 'Số bài đăng',
        'Thông tin chi tiết' => 'Thông tin chi tiết',
        'Xem chi tiết' => 'Xem chi tiết',
        'Bình luận này đã bị ẩn' => 'Bình luận này đã bị ẩn',
        'Bình luận này đã bị đánh dấu' => 'Bình luận này đã bị đánh dấu',
        'Thông tin tác giả' => 'Thông tin tác giả',
        'Xem hồ sơ' => 'Xem hồ sơ',
        'Xuất báo cáo thống kê' => 'Xuất báo cáo thống kê',
        'Loại báo cáo' => 'Loại báo cáo',
        'Tổng quan' => 'Tổng quan',
        'Tương tác' => 'Tương tác',
        'Định dạng' => 'Định dạng',
        'Tạo danh mục' => 'Tạo danh mục',
        'Diễn đàn riêng tư' => 'Diễn đàn riêng tư',
        'Nếu được chọn, chỉ những người dùng được cấp quyền mới có thể truy cập diễn đàn này.' => 'Nếu được chọn, chỉ những người dùng được cấp quyền mới có thể truy cập diễn đàn này.',
        'Riêng tư' => 'Riêng tư',
        'Công khai' => 'Công khai',
        'Trình duyệt của bạn không hỗ trợ video.' => 'Trình duyệt của bạn không hỗ trợ video.',
        'Trình duyệt của bạn không hỗ trợ audio.' => 'Trình duyệt của bạn không hỗ trợ audio.',

        // User specific keys
        'Tổng thành viên' => 'Tổng thành viên',
        'Đang online' => 'Đang online',
        'Tìm theo tên, username, email...' => 'Tìm theo tên, username, email...',
        'Tất cả vai trò' => 'Tất cả vai trò',
        'Tất cả trạng thái' => 'Tất cả trạng thái',
        'Ngày tham gia' => 'Ngày tham gia',
        'Hoạt động gần đây' => 'Hoạt động gần đây',
        'Số bài viết' => 'Số bài viết',
        'Giảm dần' => 'Giảm dần',
        'Tăng dần' => 'Tăng dần',
        'Không tìm thấy thành viên nào' => 'Không tìm thấy thành viên nào',
        'Bạn có chắc chắn muốn bỏ cấm thành viên này?' => 'Bạn có chắc chắn muốn bỏ cấm thành viên này?',
        'Lý do cấm:' => 'Lý do cấm:',
        'Bạn có chắc chắn muốn cấm thành viên này?' => 'Bạn có chắc chắn muốn cấm thành viên này?',

        // Settings and configuration
        'Tên trang web' => 'Tên trang web',
        'Khẩu hiệu' => 'Khẩu hiệu',
        'Logo' => 'Logo',
        'Favicon' => 'Favicon',
        'Banner đầu trang' => 'Banner đầu trang',
        'Tên miền' => 'Tên miền',
        'Ngôn ngữ' => 'Ngôn ngữ',
        'Múi giờ' => 'Múi giờ',
        'Bật chế độ bảo trì' => 'Bật chế độ bảo trì',
        'Thông báo bảo trì' => 'Thông báo bảo trì',

        // Company info
        'Tên công ty' => 'Tên công ty',
        'Địa chỉ' => 'Địa chỉ',
        'Số điện thoại' => 'Số điện thoại',
        'Mã số thuế' => 'Mã số thuế',
        'Số đăng ký kinh doanh' => 'Số đăng ký kinh doanh',
        'Năm thành lập' => 'Năm thành lập',
        'Giới thiệu công ty' => 'Giới thiệu công ty',

        // Contact info
        'Email liên hệ' => 'Email liên hệ',
        'Số điện thoại liên hệ' => 'Số điện thoại liên hệ',
        'Địa chỉ liên hệ' => 'Địa chỉ liên hệ',
        'Giờ làm việc' => 'Giờ làm việc',
        'Mã nhúng Google Maps' => 'Mã nhúng Google Maps',
        'Vĩ độ (Latitude)' => 'Vĩ độ (Latitude)',
        'Kinh độ (Longitude)' => 'Kinh độ (Longitude)',

        // Social media
        'Facebook' => 'Facebook',
        'Twitter / X' => 'Twitter / X',
        'Instagram' => 'Instagram',
        'LinkedIn' => 'LinkedIn',
        'YouTube' => 'YouTube',
        'TikTok' => 'TikTok',
        'Pinterest' => 'Pinterest',
        'GitHub' => 'GitHub',

        // API settings
        'Google Login' => 'Google Login',
        'Google Client ID' => 'Google Client ID',
        'Google Client Secret' => 'Google Client Secret',
        'Facebook Login' => 'Facebook Login',
        'Facebook App ID' => 'Facebook App ID',
        'Facebook App Secret' => 'Facebook App Secret',
        'Google reCAPTCHA' => 'Google reCAPTCHA',
        'reCAPTCHA Site Key' => 'reCAPTCHA Site Key',
        'reCAPTCHA Secret Key' => 'reCAPTCHA Secret Key',

        // Copyright
        'Nội dung bản quyền' => 'Nội dung bản quyền',
        'Chủ sở hữu bản quyền' => 'Chủ sở hữu bản quyền',
        'Năm bản quyền' => 'Năm bản quyền',
    ];
}

// Load comprehensive translation mapping
$translationMap = loadTranslationMapping();

// Function để chuyển đổi file
function convertFile($filePath, $translationMap) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $conversions = 0;

    // Pattern để tìm translation keys
    $patterns = [
        '/__\([\'"]([^\'"]+)[\'"]\)/' => function($matches) use ($translationMap, &$conversions) {
            $key = $matches[1];
            if (isset($translationMap[$key])) {
                $conversions++;
                return "'" . $translationMap[$key] . "'";
            }
            return $matches[0]; // Giữ nguyên nếu không tìm thấy mapping
        },
        '/@lang\([\'"]([^\'"]+)[\'"]\)/' => function($matches) use ($translationMap, &$conversions) {
            $key = $matches[1];
            if (isset($translationMap[$key])) {
                $conversions++;
                return "'" . $translationMap[$key] . "'";
            }
            return $matches[0];
        },
        '/trans\([\'"]([^\'"]+)[\'"]\)/' => function($matches) use ($translationMap, &$conversions) {
            $key = $matches[1];
            if (isset($translationMap[$key])) {
                $conversions++;
                return "'" . $translationMap[$key] . "'";
            }
            return $matches[0];
        }
    ];

    foreach ($patterns as $pattern => $callback) {
        $content = preg_replace_callback($pattern, $callback, $content);
    }

    return [
        'content' => $content,
        'conversions' => $conversions,
        'changed' => $content !== $originalContent
    ];
}

// Lấy danh sách file cần chuyển đổi (có translation keys)
$filesToConvert = [];
foreach ($auditData['files'] as $relativePath => $fileData) {
    if ($fileData['key_count'] > 0) {
        $filesToConvert[] = [
            'path' => $fileData['path'],
            'relative' => $relativePath,
            'key_count' => $fileData['key_count']
        ];
    }
}

// Sắp xếp theo số lượng keys (nhiều nhất trước)
usort($filesToConvert, function($a, $b) {
    return $b['key_count'] <=> $a['key_count'];
});

echo "🎯 Sẽ chuyển đổi " . count($filesToConvert) . " files có translation keys\n\n";

// Thực hiện chuyển đổi
$totalConversions = 0;
$processedFiles = 0;
$changedFiles = 0;

foreach ($filesToConvert as $fileInfo) {
    echo "📄 Đang xử lý: {$fileInfo['relative']} ({$fileInfo['key_count']} keys)\n";

    $result = convertFile($fileInfo['path'], $translationMap);

    if ($result['changed']) {
        // Backup file gốc
        $backupPath = $fileInfo['path'] . '.backup.' . date('Y-m-d-H-i-s');
        copy($fileInfo['path'], $backupPath);

        // Ghi file mới
        file_put_contents($fileInfo['path'], $result['content']);

        echo "   ✅ Đã chuyển đổi {$result['conversions']} keys\n";
        echo "   💾 Backup: " . basename($backupPath) . "\n";

        $changedFiles++;
        $totalConversions += $result['conversions'];
    } else {
        echo "   ⚠️  Không có key nào được chuyển đổi\n";
    }

    $processedFiles++;
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 HOÀN THÀNH CHUYỂN ĐỔI\n";
echo str_repeat("=", 50) . "\n";
echo "📁 Files đã xử lý: {$processedFiles}\n";
echo "📝 Files đã thay đổi: {$changedFiles}\n";
echo "🔄 Tổng số conversions: {$totalConversions}\n";
echo "💾 Backup files được tạo trong cùng thư mục với extension .backup.YYYY-MM-DD-HH-MM-SS\n";

if ($changedFiles > 0) {
    echo "\n⚠️  LƯU Ý:\n";
    echo "- Kiểm tra kỹ các file đã chuyển đổi\n";
    echo "- Test admin panel để đảm bảo không có lỗi\n";
    echo "- Commit changes sau khi kiểm tra\n";
    echo "- Có thể restore từ backup files nếu cần\n";
}

echo "\n✅ Conversion hoàn thành!\n";
