<?php

/**
 * Vietnamese Translation Messages
 * 
 * ⚠️  IMPORTANT: MechaMap sử dụng Database-Based Translations
 * 
 * Dự án MechaMap đã chuyển sang sử dụng hệ thống translation dựa trên database
 * thay vì file PHP truyền thống. Tất cả translation keys được lưu trong bảng 'translations'.
 * 
 * Cách sử dụng:
 * - Sử dụng helper functions: t_core(), t_ui(), t_content(), t_feature(), t_user()
 * - Hoặc sử dụng __() function với key format: 'category.section.key'
 * 
 * Quản lý translations:
 * - Truy cập: https://mechamap.test/translations
 * - Command: php artisan translations:add-dashboard
 * 
 * Cấu trúc key:
 * - core.*     : Chức năng cốt lõi (auth, validation, errors)
 * - ui.*       : Giao diện người dùng (buttons, labels, navigation)
 * - content.*  : Nội dung trang (titles, descriptions, messages)
 * - feature.*  : Tính năng cụ thể (forum, marketplace, showcase)
 * - user.*     : Quản lý người dùng (profile, settings, dashboard)
 * 
 * File này chỉ chứa một số key cơ bản cho fallback.
 */

return [
    // Fallback messages - chỉ sử dụng khi database không khả dụng
    'fallback' => [
        'database_unavailable' => 'Hệ thống translation tạm thời không khả dụng',
        'loading' => 'Đang tải...',
        'error' => 'Đã xảy ra lỗi',
        'success' => 'Thành công',
        'warning' => 'Cảnh báo',
        'info' => 'Thông tin',
    ],
    
    // Basic navigation - cho trường hợp khẩn cấp
    'nav' => [
        'home' => 'Trang chủ',
        'dashboard' => 'Bảng điều khiển',
        'profile' => 'Hồ sơ',
        'settings' => 'Cài đặt',
        'logout' => 'Đăng xuất',
    ],
    
    // Basic actions
    'actions' => [
        'save' => 'Lưu',
        'cancel' => 'Hủy',
        'delete' => 'Xóa',
        'edit' => 'Sửa',
        'view' => 'Xem',
        'back' => 'Quay lại',
        'next' => 'Tiếp theo',
        'previous' => 'Trước',
        'submit' => 'Gửi',
        'confirm' => 'Xác nhận',
    ],
    
    // Basic status
    'status' => [
        'active' => 'Hoạt động',
        'inactive' => 'Không hoạt động',
        'pending' => 'Chờ xử lý',
        'approved' => 'Đã duyệt',
        'rejected' => 'Bị từ chối',
        'draft' => 'Bản nháp',
        'published' => 'Đã xuất bản',
    ],
    
    // Database translation info
    'database_info' => [
        'enabled' => true,
        'table' => 'translations',
        'cache_enabled' => true,
        'fallback_to_file' => true,
        'management_url' => '/translations',
    ],
];
