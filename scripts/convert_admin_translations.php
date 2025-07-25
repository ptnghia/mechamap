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

        // Phase 2 - Complex SEO and Configuration Keys
        'Cấu hình SEO cho trang cụ thể sẽ ghi đè lên cấu hình SEO chung.' => 'Cấu hình SEO cho trang cụ thể sẽ ghi đè lên cấu hình SEO chung.',
        'Bạn có thể cấu hình SEO cho trang bằng cách sử dụng:' => 'Bạn có thể cấu hình SEO cho trang bằng cách sử dụng:',
        'Route name: Áp dụng cho một route cụ thể.' => 'Route name: Áp dụng cho một route cụ thể.',
        'URL pattern: Áp dụng cho các URL khớp với mẫu regex.' => 'URL pattern: Áp dụng cho các URL khớp với mẫu regex.',
        'Thông tin cấu hình SEO' => 'Thông tin cấu hình SEO',
        'Route name' => 'Route name',
        '-- Chọn route --' => '-- Chọn route --',
        'Chọn route name để áp dụng cấu hình SEO cho trang cụ thể.' => 'Chọn route name để áp dụng cấu hình SEO cho trang cụ thể.',
        'URL pattern (Regex)' => 'URL pattern (Regex)',
        'Nhập mẫu regex để áp dụng cấu hình SEO cho các URL khớp với mẫu.' => 'Nhập mẫu regex để áp dụng cấu hình SEO cho các URL khớp với mẫu.',
        'Bạn phải cung cấp Route name hoặc URL pattern.' => 'Bạn phải cung cấp Route name hoặc URL pattern.',
        'Cấu hình SEO cơ bản' => 'Cấu hình SEO cơ bản',
        'Tiêu đề trang' => 'Tiêu đề trang',
        'Tiêu đề hiển thị trên thanh tiêu đề trình duyệt và kết quả tìm kiếm.' => 'Tiêu đề hiển thị trên thanh tiêu đề trình duyệt và kết quả tìm kiếm.',
        'Mô tả trang' => 'Mô tả trang',
        'Mô tả ngắn gọn về trang. Hiển thị trong kết quả tìm kiếm.' => 'Mô tả ngắn gọn về trang. Hiển thị trong kết quả tìm kiếm.',
        'Các từ khóa liên quan đến trang, phân cách bằng dấu phẩy.' => 'Các từ khóa liên quan đến trang, phân cách bằng dấu phẩy.',
        'URL chính thức của trang. Để trống để sử dụng URL hiện tại.' => 'URL chính thức của trang. Để trống để sử dụng URL hiện tại.',
        'Không cho phép công cụ tìm kiếm lập chỉ mục trang này' => 'Không cho phép công cụ tìm kiếm lập chỉ mục trang này',
        'Nếu bật, trang sẽ không xuất hiện trong kết quả tìm kiếm.' => 'Nếu bật, trang sẽ không xuất hiện trong kết quả tìm kiếm.',
        'Tiêu đề khi chia sẻ trang trên Facebook và các nền tảng khác.' => 'Tiêu đề khi chia sẻ trang trên Facebook và các nền tảng khác.',
        'Mô tả khi chia sẻ trang trên Facebook và các nền tảng khác.' => 'Mô tả khi chia sẻ trang trên Facebook và các nền tảng khác.',
        'Hình ảnh khi chia sẻ trang trên Facebook và các nền tảng khác. Kích thước tối thiểu: 1200x630 pixels.' => 'Hình ảnh khi chia sẻ trang trên Facebook và các nền tảng khác. Kích thước tối thiểu: 1200x630 pixels.',
        'Cấu hình Twitter Card' => 'Cấu hình Twitter Card',
        'Tiêu đề khi chia sẻ trang trên Twitter.' => 'Tiêu đề khi chia sẻ trang trên Twitter.',
        'Mô tả khi chia sẻ trang trên Twitter.' => 'Mô tả khi chia sẻ trang trên Twitter.',
        'Hình ảnh khi chia sẻ trang trên Twitter. Kích thước tối thiểu: 1200x600 pixels.' => 'Hình ảnh khi chia sẻ trang trên Twitter. Kích thước tối thiểu: 1200x600 pixels.',
        'Meta tags bổ sung' => 'Meta tags bổ sung',
        'Các meta tags bổ sung sẽ được thêm vào thẻ <head> của trang.' => 'Các meta tags bổ sung sẽ được thêm vào thẻ <head> của trang.',
        'Kích hoạt cấu hình SEO này' => 'Kích hoạt cấu hình SEO này',
        'Nếu tắt, cấu hình SEO này sẽ không được áp dụng.' => 'Nếu tắt, cấu hình SEO này sẽ không được áp dụng.',

        // Content Management - Complex keys
        'Tạo bài viết mới' => 'Tạo bài viết mới',
        'Tóm tắt' => 'Tóm tắt',
        'Chọn danh mục' => 'Chọn danh mục',
        'Đánh dấu là bài viết nổi bật' => 'Đánh dấu là bài viết nổi bật',
        'Tiêu đề, nội dung...' => 'Tiêu đề, nội dung...',
        'Bạn có chắc chắn muốn xóa bài viết này?' => 'Bạn có chắc chắn muốn xóa bài viết này?',
        'Đổi mật khẩu' => 'Đổi mật khẩu',
        'Xem hồ sơ công khai' => 'Xem hồ sơ công khai',
        'Thông tin tài khoản' => 'Thông tin tài khoản',
        'Website' => 'Website',
        'Địa điểm' => 'Địa điểm',
        'Mật khẩu mới' => 'Mật khẩu mới',
        'Xác nhận mật khẩu mới' => 'Xác nhận mật khẩu mới',
        'Sao chép' => 'Sao chép',
        'Đã sao chép' => 'Đã sao chép',
        'Yêu cầu xác thực email' => 'Yêu cầu xác thực email',
        'Đánh dấu là bài nổi bật' => 'Đánh dấu là bài nổi bật',

        // File and Media Management
        'Thông tin file' => 'Thông tin file',
        'Loại file' => 'Loại file',
        'Đường dẫn' => 'Đường dẫn',
        'Phần mở rộng' => 'Phần mở rộng',
        'MIME Type' => 'MIME Type',
        'Kích thước file' => 'Kích thước file',
        'Chiều rộng' => 'Chiều rộng',
        'Chiều cao' => 'Chiều cao',
        'Độ phân giải' => 'Độ phân giải',
        'Thời lượng' => 'Thời lượng',
        'Bitrate' => 'Bitrate',
        'Codec' => 'Codec',
        'Tần số mẫu' => 'Tần số mẫu',
        'Kênh âm thanh' => 'Kênh âm thanh',
        'Metadata' => 'Metadata',
        'Exif Data' => 'Exif Data',
        'GPS Location' => 'GPS Location',
        'Camera Info' => 'Camera Info',
        'Ngày chụp' => 'Ngày chụp',
        'Cài đặt camera' => 'Cài đặt camera',
        'Flash' => 'Flash',
        'ISO' => 'ISO',
        'Khẩu độ' => 'Khẩu độ',
        'Tốc độ màn trập' => 'Tốc độ màn trập',
        'Tiêu cự' => 'Tiêu cự',

        // Advanced Settings
        'Cấu hình nâng cao' => 'Cấu hình nâng cao',
        'Cài đặt hệ thống' => 'Cài đặt hệ thống',
        'Tối ưu hóa' => 'Tối ưu hóa',
        'Cache' => 'Cache',
        'Session' => 'Session',
        'Database' => 'Database',
        'Queue' => 'Queue',
        'Mail' => 'Mail',
        'Storage' => 'Storage',
        'Logging' => 'Logging',
        'Debug' => 'Debug',
        'Environment' => 'Environment',
        'Maintenance' => 'Maintenance',
        'Backup' => 'Backup',
        'Restore' => 'Restore',
        'Import' => 'Import',
        'Export' => 'Export',
        'Migration' => 'Migration',
        'Seeder' => 'Seeder',
        'Artisan' => 'Artisan',
        'Composer' => 'Composer',
        'NPM' => 'NPM',
        'Webpack' => 'Webpack',
        'Vite' => 'Vite',

        // Search and Filtering
        'Tìm kiếm nâng cao' => 'Tìm kiếm nâng cao',
        'Bộ lọc nâng cao' => 'Bộ lọc nâng cao',
        'Sắp xếp theo' => 'Sắp xếp theo',
        'Thứ tự sắp xếp' => 'Thứ tự sắp xếp',
        'Tăng dần' => 'Tăng dần',
        'Giảm dần' => 'Giảm dần',
        'Từ ngày' => 'Từ ngày',
        'Đến ngày' => 'Đến ngày',
        'Khoảng thời gian' => 'Khoảng thời gian',
        'Hôm nay' => 'Hôm nay',
        'Hôm qua' => 'Hôm qua',
        'Tuần này' => 'Tuần này',
        'Tuần trước' => 'Tuần trước',
        'Tháng này' => 'Tháng này',
        'Tháng trước' => 'Tháng trước',
        'Năm này' => 'Năm này',
        'Năm trước' => 'Năm trước',
        'Tùy chỉnh' => 'Tùy chỉnh',

        // Notifications and Alerts
        'Thông báo hệ thống' => 'Thông báo hệ thống',
        'Cảnh báo bảo mật' => 'Cảnh báo bảo mật',
        'Thông báo cập nhật' => 'Thông báo cập nhật',
        'Thông báo lỗi' => 'Thông báo lỗi',
        'Thông báo thành công' => 'Thông báo thành công',
        'Thông báo cảnh báo' => 'Thông báo cảnh báo',
        'Thông báo thông tin' => 'Thông báo thông tin',
        'Đánh dấu đã đọc' => 'Đánh dấu đã đọc',
        'Đánh dấu chưa đọc' => 'Đánh dấu chưa đọc',
        'Xóa thông báo' => 'Xóa thông báo',
        'Xóa tất cả' => 'Xóa tất cả',
        'Đánh dấu tất cả đã đọc' => 'Đánh dấu tất cả đã đọc',

        // Forum specific complex keys
        'Cấu hình diễn đàn' => 'Cấu hình diễn đàn',
        'Quyền truy cập diễn đàn' => 'Quyền truy cập diễn đàn',
        'Quyền tạo chủ đề' => 'Quyền tạo chủ đề',
        'Quyền trả lời' => 'Quyền trả lời',
        'Quyền chỉnh sửa' => 'Quyền chỉnh sửa',
        'Quyền xóa' => 'Quyền xóa',
        'Quyền kiểm duyệt' => 'Quyền kiểm duyệt',
        'Quyền quản trị' => 'Quyền quản trị',
        'Diễn đàn riêng tư' => 'Diễn đàn riêng tư',
        'Nếu được chọn, chỉ những người dùng được cấp quyền mới có thể truy cập diễn đàn này.' => 'Nếu được chọn, chỉ những người dùng được cấp quyền mới có thể truy cập diễn đàn này.',
        'Riêng tư' => 'Riêng tư',
        'Công khai' => 'Công khai',
        'Yêu cầu duyệt bài' => 'Yêu cầu duyệt bài',
        'Cho phép đính kèm file' => 'Cho phép đính kèm file',
        'Cho phép emoji' => 'Cho phép emoji',
        'Cho phép BBCode' => 'Cho phép BBCode',
        'Cho phép HTML' => 'Cho phép HTML',
        'Giới hạn ký tự' => 'Giới hạn ký tự',
        'Số bài tối đa mỗi ngày' => 'Số bài tối đa mỗi ngày',
        'Thời gian chờ giữa các bài' => 'Thời gian chờ giữa các bài',

        // User management complex keys
        'Cấu hình người dùng' => 'Cấu hình người dùng',
        'Quyền hạn người dùng' => 'Quyền hạn người dùng',
        'Nhóm người dùng' => 'Nhóm người dùng',
        'Cấp độ người dùng' => 'Cấp độ người dùng',
        'Điểm thành viên' => 'Điểm thành viên',
        'Huy hiệu' => 'Huy hiệu',
        'Thành tích' => 'Thành tích',
        'Lịch sử hoạt động' => 'Lịch sử hoạt động',
        'Nhật ký đăng nhập' => 'Nhật ký đăng nhập',
        'Thiết bị đăng nhập' => 'Thiết bị đăng nhập',
        'Địa chỉ IP' => 'Địa chỉ IP',
        'User Agent' => 'User Agent',
        'Trình duyệt' => 'Trình duyệt',
        'Hệ điều hành' => 'Hệ điều hành',
        'Thiết bị' => 'Thiết bị',
        'Vị trí địa lý' => 'Vị trí địa lý',
        'Múi giờ người dùng' => 'Múi giờ người dùng',
        'Ngôn ngữ ưa thích' => 'Ngôn ngữ ưa thích',
        'Cài đặt thông báo' => 'Cài đặt thông báo',
        'Cài đặt riêng tư' => 'Cài đặt riêng tư',
        'Cài đặt bảo mật' => 'Cài đặt bảo mật',
        'Xác thực hai yếu tố' => 'Xác thực hai yếu tố',
        'Mã QR' => 'Mã QR',
        'Mã backup' => 'Mã backup',
        'Khóa API' => 'Khóa API',
        'Token truy cập' => 'Token truy cập',
        'Phiên đăng nhập' => 'Phiên đăng nhập',
        'Đăng xuất tất cả thiết bị' => 'Đăng xuất tất cả thiết bị',

        // Phase 2.1 - Notification and Alert System Keys
        'Kiểm tra thông báo' => 'Kiểm tra thông báo',
        'Thống kê thông báo' => 'Thống kê thông báo',
        'Cấu hình hệ thống thông báo để giữ người dùng được cập nhật.' => 'Cấu hình hệ thống thông báo để giữ người dùng được cập nhật.',
        'Bật thông báo real-time để tương tác tức thì.' => 'Bật thông báo real-time để tương tác tức thì.',
        'Cấu hình email để gửi thông báo quan trọng.' => 'Cấu hình email để gửi thông báo quan trọng.',
        'Theo dõi thống kê để hiểu hiệu quả thông báo.' => 'Theo dõi thống kê để hiểu hiệu quả thông báo.',
        'Cấu hình thông báo' => 'Cấu hình thông báo',
        'Bật hệ thống thông báo' => 'Bật hệ thống thông báo',
        'Cho phép gửi thông báo đến người dùng' => 'Cho phép gửi thông báo đến người dùng',
        'Thông báo thời gian thực' => 'Thông báo thời gian thực',
        'Gửi thông báo tức thì khi có sự kiện' => 'Gửi thông báo tức thì khi có sự kiện',
        'Thời gian lưu trữ' => 'Thời gian lưu trữ',
        'ngày' => 'ngày',
        'năm' => 'năm',
        'Thời gian lưu trữ thông báo trước khi tự động xóa' => 'Thời gian lưu trữ thông báo trước khi tự động xóa',
        'Số thông báo tối đa/người dùng' => 'Số thông báo tối đa/người dùng',
        'Giới hạn số thông báo tối đa cho mỗi người dùng' => 'Giới hạn số thông báo tối đa cho mỗi người dùng',
        'Loại thông báo' => 'Loại thông báo',
        'Thông báo trả lời mới' => 'Thông báo trả lời mới',
        'Thông báo khi có trả lời mới trong bài đăng đã theo dõi' => 'Thông báo khi có trả lời mới trong bài đăng đã theo dõi',
        'Thông báo nhắc đến' => 'Thông báo nhắc đến',
        'Thông báo khi được nhắc đến trong bài viết hoặc bình luận' => 'Thông báo khi được nhắc đến trong bài viết hoặc bình luận',
        'Thông báo lượt thích' => 'Thông báo lượt thích',
        'Thông báo khi bài viết được thích' => 'Thông báo khi bài viết được thích',
        'Thông báo theo dõi' => 'Thông báo theo dõi',
        'Thông báo khi có người theo dõi mới' => 'Thông báo khi có người theo dõi mới',
        'Thông báo hành động quản trị' => 'Thông báo hành động quản trị',
        'Thông báo về các hành động của quản trị viên' => 'Thông báo về các hành động của quản trị viên',
        'Cấu hình Email' => 'Cấu hình Email',
        'Bật thông báo email' => 'Bật thông báo email',
        'Gửi thông báo qua email cho các sự kiện quan trọng' => 'Gửi thông báo qua email cho các sự kiện quan trọng',
        'Tần suất gửi email' => 'Tần suất gửi email',
        'Ngay lập tức' => 'Ngay lập tức',
        'Mỗi giờ' => 'Mỗi giờ',
        'Hàng ngày' => 'Hàng ngày',
        'Hàng tuần' => 'Hàng tuần',
        'Xử lý hàng loạt' => 'Xử lý hàng loạt',
        'Nhóm các thông báo cùng loại để gửi hàng loạt' => 'Nhóm các thông báo cùng loại để gửi hàng loạt',
        'Sử dụng hàng đợi' => 'Sử dụng hàng đợi',
        'Xử lý thông báo trong hàng đợi để tăng hiệu suất' => 'Xử lý thông báo trong hàng đợi để tăng hiệu suất',
        'Tổng đã gửi' => 'Tổng đã gửi',
        'Đã nhận' => 'Đã nhận',
        'Đã xem' => 'Đã xem',
        'Đang chờ' => 'Đang chờ',

        // Forum and Category Management
        'Tạo chuyên mục' => 'Tạo chuyên mục',
        'Chỉnh sửa chuyên mục' => 'Chỉnh sửa chuyên mục',
        'Đã ghim' => 'Đã ghim',
        'Lưu ý: Hành động này không thể hoàn tác và sẽ xóa tất cả bình luận, phản hồi liên quan.' => 'Lưu ý: Hành động này không thể hoàn tác và sẽ xóa tất cả bình luận, phản hồi liên quan.',
        'Admin và Moderator có quyền truy cập trang quản trị.' => 'Admin và Moderator có quyền truy cập trang quản trị.',

        // Search and Advanced Features
        'Tìm kiếm nâng cao' => 'Tìm kiếm nâng cao',
        'Tìm kiếm toàn bộ hệ thống' => 'Tìm kiếm toàn bộ hệ thống',
        'Tìm kiếm trong nội dung' => 'Tìm kiếm trong nội dung',
        'Tìm kiếm theo tác giả' => 'Tìm kiếm theo tác giả',
        'Tìm kiếm theo ngày' => 'Tìm kiếm theo ngày',
        'Tìm kiếm theo danh mục' => 'Tìm kiếm theo danh mục',
        'Kết quả tìm kiếm' => 'Kết quả tìm kiếm',
        'Không tìm thấy kết quả' => 'Không tìm thấy kết quả',
        'Tìm kiếm với từ khóa khác' => 'Tìm kiếm với từ khóa khác',
        'Gợi ý tìm kiếm' => 'Gợi ý tìm kiếm',
        'Lịch sử tìm kiếm' => 'Lịch sử tìm kiếm',
        'Xóa lịch sử' => 'Xóa lịch sử',
        'Lưu tìm kiếm' => 'Lưu tìm kiếm',
        'Tìm kiếm đã lưu' => 'Tìm kiếm đã lưu',

        // Statistics and Analytics
        'Thống kê tổng quan' => 'Thống kê tổng quan',
        'Thống kê người dùng' => 'Thống kê người dùng',
        'Thống kê nội dung' => 'Thống kê nội dung',
        'Thống kê hoạt động' => 'Thống kê hoạt động',
        'Biểu đồ thống kê' => 'Biểu đồ thống kê',
        'Xuất báo cáo' => 'Xuất báo cáo',
        'Báo cáo hàng ngày' => 'Báo cáo hàng ngày',
        'Báo cáo hàng tuần' => 'Báo cáo hàng tuần',
        'Báo cáo hàng tháng' => 'Báo cáo hàng tháng',
        'Báo cáo hàng năm' => 'Báo cáo hàng năm',
        'Tải báo cáo' => 'Tải báo cáo',
        'Gửi báo cáo qua email' => 'Gửi báo cáo qua email',
        'Lên lịch báo cáo' => 'Lên lịch báo cáo',
        'Báo cáo tự động' => 'Báo cáo tự động',

        // SEO and Advanced Configuration
        'Cấu hình SEO nâng cao' => 'Cấu hình SEO nâng cao',
        'Tối ưu hóa công cụ tìm kiếm' => 'Tối ưu hóa công cụ tìm kiếm',
        'Phân tích SEO' => 'Phân tích SEO',
        'Kiểm tra SEO' => 'Kiểm tra SEO',
        'Đề xuất SEO' => 'Đề xuất SEO',
        'Từ khóa mục tiêu' => 'Từ khóa mục tiêu',
        'Mật độ từ khóa' => 'Mật độ từ khóa',
        'Liên kết nội bộ' => 'Liên kết nội bộ',
        'Liên kết ngoài' => 'Liên kết ngoài',
        'Cấu trúc URL' => 'Cấu trúc URL',
        'Breadcrumb' => 'Breadcrumb',
        'Schema markup' => 'Schema markup',
        'Rich snippets' => 'Rich snippets',
        'Open Graph' => 'Open Graph',
        'Twitter Cards' => 'Twitter Cards',
        'Canonical URL' => 'Canonical URL',
        'Hreflang' => 'Hreflang',
        'Noindex' => 'Noindex',
        'Nofollow' => 'Nofollow',
        'Robots meta' => 'Robots meta',

        // API and Integration
        'Cấu hình API' => 'Cấu hình API',
        'Khóa API' => 'Khóa API',
        'Tạo khóa API' => 'Tạo khóa API',
        'Xóa khóa API' => 'Xóa khóa API',
        'Quyền API' => 'Quyền API',
        'Giới hạn API' => 'Giới hạn API',
        'Nhật ký API' => 'Nhật ký API',
        'Thống kê API' => 'Thống kê API',
        'Tài liệu API' => 'Tài liệu API',
        'Test API' => 'Test API',
        'Webhook' => 'Webhook',
        'Cấu hình Webhook' => 'Cấu hình Webhook',
        'URL Webhook' => 'URL Webhook',
        'Secret Webhook' => 'Secret Webhook',
        'Sự kiện Webhook' => 'Sự kiện Webhook',
        'Nhật ký Webhook' => 'Nhật ký Webhook',
        'Test Webhook' => 'Test Webhook',

        // System and Performance
        'Hiệu suất hệ thống' => 'Hiệu suất hệ thống',
        'Giám sát hệ thống' => 'Giám sát hệ thống',
        'Tối ưu hóa hiệu suất' => 'Tối ưu hóa hiệu suất',
        'Cache hệ thống' => 'Cache hệ thống',
        'Xóa cache' => 'Xóa cache',
        'Cấu hình cache' => 'Cấu hình cache',
        'Thống kê cache' => 'Thống kê cache',
        'Tỷ lệ cache hit' => 'Tỷ lệ cache hit',
        'Kích thước cache' => 'Kích thước cache',
        'Thời gian cache' => 'Thời gian cache',
        'Cơ sở dữ liệu' => 'Cơ sở dữ liệu',
        'Tối ưu hóa database' => 'Tối ưu hóa database',
        'Sao lưu database' => 'Sao lưu database',
        'Khôi phục database' => 'Khôi phục database',
        'Thống kê database' => 'Thống kê database',
        'Kích thước database' => 'Kích thước database',
        'Số lượng bảng' => 'Số lượng bảng',
        'Số lượng bản ghi' => 'Số lượng bản ghi',

        // Phase 2.2 - Specific Content Management Keys
        'chuyên mục' => 'chuyên mục',
        'Không có chuyên mục nào.' => 'Không có chuyên mục nào.',
        'Thông tin chuyên mục' => 'Thông tin chuyên mục',
        'Chuyên mục con' => 'Chuyên mục con',
        'Bài đăng trong chuyên mục này' => 'Bài đăng trong chuyên mục này',
        'Không có bài đăng nào trong chuyên mục này.' => 'Không có bài đăng nào trong chuyên mục này.',

        // Comments Management
        'Chỉnh sửa bình luận' => 'Chỉnh sửa bình luận',
        'Đánh dấu bình luận' => 'Đánh dấu bình luận',
        'Bình luận bị đánh dấu sẽ được đánh dấu để xem xét thêm.' => 'Bình luận bị đánh dấu sẽ được đánh dấu để xem xét thêm.',
        'Bình luận bị ẩn sẽ không hiển thị cho người dùng.' => 'Bình luận bị ẩn sẽ không hiển thị cho người dùng.',
        'Thông tin bình luận' => 'Thông tin bình luận',
        'Bị báo cáo' => 'Bị báo cáo',
        'ID bài đăng' => 'ID bài đăng',
        'Nhập ID bài đăng' => 'Nhập ID bài đăng',
        'ID người dùng' => 'ID người dùng',
        'Nhập ID người dùng' => 'Nhập ID người dùng',
        'Nội dung bình luận...' => 'Nội dung bình luận...',
        'bình luận' => 'bình luận',
        'Trả lời cho bình luận #' => 'Trả lời cho bình luận #',
        'Không có bình luận nào.' => 'Không có bình luận nào.',
        'Nội dung bình luận' => 'Nội dung bình luận',
        'Bình luận cha' => 'Bình luận cha',
        'Số lượt thích' => 'Số lượt thích',
        'Phản hồi' => 'Phản hồi',
        'Xóa bình luận' => 'Xóa bình luận',
        'Bài đăng liên quan' => 'Bài đăng liên quan',
        'Đăng bởi' => 'Đăng bởi',
        'vào' => 'vào',
        'Xem bài đăng' => 'Xem bài đăng',
        'Lưu ý: Hành động này sẽ xóa cả' => 'Lưu ý: Hành động này sẽ xóa cả',
        'phản hồi của bình luận này.' => 'phản hồi của bình luận này.',

        // FAQ Categories
        'Hiện tại chỉ hỗ trợ định dạng CSV' => 'Hiện tại chỉ hỗ trợ định dạng CSV',
        'Tạo danh mục hỏi đáp mới' => 'Tạo danh mục hỏi đáp mới',
        'Chỉnh sửa danh mục hỏi đáp' => 'Chỉnh sửa danh mục hỏi đáp',

        // Forums Management
        'Tạo diễn đàn' => 'Tạo diễn đàn',
        'Chỉnh sửa diễn đàn' => 'Chỉnh sửa diễn đàn',
        'diễn đàn' => 'diễn đàn',
        'Không có diễn đàn nào.' => 'Không có diễn đàn nào.',
        'Thông tin diễn đàn' => 'Thông tin diễn đàn',
        'Diễn đàn con' => 'Diễn đàn con',
        'Bài đăng trong diễn đàn này' => 'Bài đăng trong diễn đàn này',
        'Không có bài đăng nào trong diễn đàn này.' => 'Không có bài đăng nào trong diễn đàn này.',

        // Media Management
        'Chỉnh sửa thông tin' => 'Chỉnh sửa thông tin',
        'Tên file, tiêu đề, mô tả...' => 'Tên file, tiêu đề, mô tả...',
        'Không có file nào.' => 'Không có file nào.',
        'Không thể xem trước file này.' => 'Không thể xem trước file này.',
        'URL' => 'URL',

        // Page Categories
        'Tạo danh mục trang mới' => 'Tạo danh mục trang mới',
        'Chỉnh sửa danh mục trang' => 'Chỉnh sửa danh mục trang',

        // Page SEO
        'Cấu hình SEO cho trang' => 'Cấu hình SEO cho trang',
        'Trang SEO' => 'Trang SEO',
        'Cấu hình SEO trang' => 'Cấu hình SEO trang',
        'Áp dụng cho route' => 'Áp dụng cho route',
        'Áp dụng cho URL pattern' => 'Áp dụng cho URL pattern',

        // Pages Management
        'Tạo trang mới' => 'Tạo trang mới',
        'Chỉnh sửa trang' => 'Chỉnh sửa trang',
        'Nội dung trang...' => 'Nội dung trang...',
        'Trang con' => 'Trang con',
        'Trang cha' => 'Trang cha',

        // Profile Management
        'Hồ sơ cá nhân' => 'Hồ sơ cá nhân',
        'Cập nhật hồ sơ' => 'Cập nhật hồ sơ',
        'Thông tin cá nhân' => 'Thông tin cá nhân',
        'Cài đặt tài khoản' => 'Cài đặt tài khoản',
        'Đổi mật khẩu' => 'Đổi mật khẩu',
        'Mật khẩu hiện tại' => 'Mật khẩu hiện tại',
        'Nhập mật khẩu hiện tại' => 'Nhập mật khẩu hiện tại',
        'Nhập mật khẩu mới' => 'Nhập mật khẩu mới',
        'Nhập lại mật khẩu mới' => 'Nhập lại mật khẩu mới',

        // Search Management
        'Tìm kiếm trong admin' => 'Tìm kiếm trong admin',
        'Tìm kiếm tất cả' => 'Tìm kiếm tất cả',
        'Tìm kiếm người dùng' => 'Tìm kiếm người dùng',
        'Tìm kiếm bài đăng' => 'Tìm kiếm bài đăng',
        'Tìm kiếm bình luận' => 'Tìm kiếm bình luận',
        'Tìm kiếm file' => 'Tìm kiếm file',
        'Tìm kiếm cài đặt' => 'Tìm kiếm cài đặt',
        'Kết quả tìm kiếm cho' => 'Kết quả tìm kiếm cho',
        'Tìm thấy' => 'Tìm thấy',
        'kết quả' => 'kết quả',
        'Không tìm thấy kết quả nào' => 'Không tìm thấy kết quả nào',
        'Thử tìm kiếm với từ khóa khác' => 'Thử tìm kiếm với từ khóa khác',
        'Tìm kiếm gần đây' => 'Tìm kiếm gần đây',
        'Xóa lịch sử tìm kiếm' => 'Xóa lịch sử tìm kiếm',

        // SEO Management
        'Quản lý SEO' => 'Quản lý SEO',
        'Cài đặt SEO' => 'Cài đặt SEO',
        'SEO cơ bản' => 'SEO cơ bản',
        'SEO nâng cao' => 'SEO nâng cao',
        'Robots.txt' => 'Robots.txt',
        'Sitemap.xml' => 'Sitemap.xml',
        'Social Media SEO' => 'Social Media SEO',
        'Cấu hình robots.txt' => 'Cấu hình robots.txt',
        'Nội dung robots.txt' => 'Nội dung robots.txt',
        'Cấu hình sitemap' => 'Cấu hình sitemap',
        'Tự động tạo sitemap' => 'Tự động tạo sitemap',
        'Bao gồm trang' => 'Bao gồm trang',
        'Bao gồm bài đăng' => 'Bao gồm bài đăng',
        'Bao gồm danh mục' => 'Bao gồm danh mục',
        'Tần suất cập nhật' => 'Tần suất cập nhật',
        'Độ ưu tiên' => 'Độ ưu tiên',

        // Settings Management
        'Cài đặt hệ thống' => 'Cài đặt hệ thống',
        'Cài đặt API' => 'Cài đặt API',
        'Cài đặt công ty' => 'Cài đặt công ty',
        'Cài đặt liên hệ' => 'Cài đặt liên hệ',
        'Cài đặt bản quyền' => 'Cài đặt bản quyền',
        'Cài đặt diễn đàn' => 'Cài đặt diễn đàn',
        'Cài đặt chung' => 'Cài đặt chung',
        'Cài đặt mạng xã hội' => 'Cài đặt mạng xã hội',
        'Cài đặt người dùng' => 'Cài đặt người dùng',

        // Statistics
        'Thống kê hệ thống' => 'Thống kê hệ thống',
        'Thống kê chi tiết' => 'Thống kê chi tiết',
        'Biểu đồ' => 'Biểu đồ',
        'Dữ liệu thống kê' => 'Dữ liệu thống kê',
        'Phân tích dữ liệu' => 'Phân tích dữ liệu',
        'Báo cáo thống kê' => 'Báo cáo thống kê',

        // Threads Management
        'Tạo chủ đề mới' => 'Tạo chủ đề mới',
        'Chỉnh sửa chủ đề' => 'Chỉnh sửa chủ đề',
        'Nội dung chủ đề...' => 'Nội dung chủ đề...',
        'Chủ đề nổi bật' => 'Chủ đề nổi bật',
        'Chủ đề đã ghim' => 'Chủ đề đã ghim',
        'Chủ đề đã khóa' => 'Chủ đề đã khóa',
        'Ghim chủ đề' => 'Ghim chủ đề',
        'Khóa chủ đề' => 'Khóa chủ đề',
        'Mở khóa chủ đề' => 'Mở khóa chủ đề',
        'Bỏ ghim chủ đề' => 'Bỏ ghim chủ đề',

        // Users Management
        'Quản lý người dùng' => 'Quản lý người dùng',
        'Tạo người dùng mới' => 'Tạo người dùng mới',
        'Chỉnh sửa người dùng' => 'Chỉnh sửa người dùng',
        'Thông tin người dùng' => 'Thông tin người dùng',
        'Hồ sơ người dùng' => 'Hồ sơ người dùng',
        'Hoạt động người dùng' => 'Hoạt động người dùng',
        'Quyền hạn' => 'Quyền hạn',
        'Nhóm quyền' => 'Nhóm quyền',
        'Cấp quyền' => 'Cấp quyền',
        'Thu hồi quyền' => 'Thu hồi quyền',
        'Khóa tài khoản' => 'Khóa tài khoản',
        'Mở khóa tài khoản' => 'Mở khóa tài khoản',
        'Xóa tài khoản' => 'Xóa tài khoản',
        'Khôi phục tài khoản' => 'Khôi phục tài khoản',

        // Notification specific keys with line breaks
        'Số thông báo tối đa/người dùng' => 'Số thông báo tối đa/người dùng',
        'Thông báo hành động quản trị' => 'Thông báo hành động quản trị',
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
