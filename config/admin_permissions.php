<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MechaMap Admin Permission System
    |--------------------------------------------------------------------------
    |
    | Định nghĩa các quyền hạn chi tiết cho admin panel MechaMap
    | Admin có full access, Moderator có quyền hạn giới hạn
    |
    */

    'permissions' => [
        // === QUẢN LÝ NGƯỜI DÙNG ===
        'users' => [
            'view_users' => [
                'name' => 'Xem danh sách người dùng',
                'description' => 'Có thể xem danh sách tất cả người dùng',
                'admin' => true,
                'moderator' => true,
            ],
            'edit_users' => [
                'name' => 'Chỉnh sửa người dùng',
                'description' => 'Có thể chỉnh sửa thông tin người dùng',
                'admin' => true,
                'moderator' => true,
            ],
            'ban_users' => [
                'name' => 'Cấm người dùng',
                'description' => 'Có thể cấm/bỏ cấm người dùng',
                'admin' => true,
                'moderator' => true,
            ],
            'delete_users' => [
                'name' => 'Xóa người dùng',
                'description' => 'Có thể xóa tài khoản người dùng',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_admins' => [
                'name' => 'Quản lý admin',
                'description' => 'Có thể quản lý tài khoản admin/moderator',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_roles' => [
                'name' => 'Quản lý vai trò',
                'description' => 'Có thể quản lý vai trò và phân quyền',
                'admin' => true,
                'moderator' => false,
            ],
        ],

        // === QUẢN LÝ NỘI DUNG ===
        'content' => [
            'view_threads' => [
                'name' => 'Xem bài viết',
                'description' => 'Có thể xem danh sách bài viết',
                'admin' => true,
                'moderator' => true,
            ],
            'edit_threads' => [
                'name' => 'Chỉnh sửa bài viết',
                'description' => 'Có thể chỉnh sửa bài viết',
                'admin' => true,
                'moderator' => true,
            ],
            'delete_threads' => [
                'name' => 'Xóa bài viết',
                'description' => 'Có thể xóa bài viết',
                'admin' => true,
                'moderator' => true,
            ],
            'approve_threads' => [
                'name' => 'Duyệt bài viết',
                'description' => 'Có thể duyệt/từ chối bài viết',
                'admin' => true,
                'moderator' => true,
            ],
            'view_comments' => [
                'name' => 'Xem bình luận',
                'description' => 'Có thể xem danh sách bình luận',
                'admin' => true,
                'moderator' => true,
            ],
            'edit_comments' => [
                'name' => 'Chỉnh sửa bình luận',
                'description' => 'Có thể chỉnh sửa bình luận',
                'admin' => true,
                'moderator' => true,
            ],
            'delete_comments' => [
                'name' => 'Xóa bình luận',
                'description' => 'Có thể xóa bình luận',
                'admin' => true,
                'moderator' => true,
            ],
            'manage_categories' => [
                'name' => 'Quản lý danh mục',
                'description' => 'Có thể quản lý danh mục diễn đàn',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_forums' => [
                'name' => 'Quản lý diễn đàn',
                'description' => 'Có thể quản lý cấu trúc diễn đàn',
                'admin' => true,
                'moderator' => false,
            ],
            'view_reports' => [
                'name' => 'Xem báo cáo vi phạm',
                'description' => 'Có thể xem danh sách báo cáo vi phạm',
                'admin' => true,
                'moderator' => true,
            ],
            'manage_reports' => [
                'name' => 'Quản lý báo cáo vi phạm',
                'description' => 'Có thể xử lý và giải quyết báo cáo vi phạm',
                'admin' => true,
                'moderator' => true,
            ],
        ],

        // === QUẢN LÝ SHOWCASE ===
        'showcase' => [
            'view_showcases' => [
                'name' => 'Xem showcase',
                'description' => 'Có thể xem danh sách showcase',
                'admin' => true,
                'moderator' => true,
            ],
            'edit_showcases' => [
                'name' => 'Chỉnh sửa showcase',
                'description' => 'Có thể chỉnh sửa showcase',
                'admin' => true,
                'moderator' => true,
            ],
            'delete_showcases' => [
                'name' => 'Xóa showcase',
                'description' => 'Có thể xóa showcase',
                'admin' => true,
                'moderator' => false,
            ],
            'approve_showcases' => [
                'name' => 'Duyệt showcase',
                'description' => 'Có thể duyệt/từ chối showcase',
                'admin' => true,
                'moderator' => true,
            ],
        ],

        // === THỊ TRƯỜNG ===
        'marketplace' => [
            'view_products' => [
                'name' => 'Xem sản phẩm',
                'description' => 'Có thể xem danh sách sản phẩm marketplace',
                'admin' => true,
                'moderator' => false,
            ],
            'edit_products' => [
                'name' => 'Chỉnh sửa sản phẩm',
                'description' => 'Có thể chỉnh sửa sản phẩm',
                'admin' => true,
                'moderator' => false,
            ],
            'delete_products' => [
                'name' => 'Xóa sản phẩm',
                'description' => 'Có thể xóa sản phẩm',
                'admin' => true,
                'moderator' => false,
            ],
            'view_orders' => [
                'name' => 'Xem đơn hàng',
                'description' => 'Có thể xem danh sách đơn hàng',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_orders' => [
                'name' => 'Quản lý đơn hàng',
                'description' => 'Có thể quản lý trạng thái đơn hàng',
                'admin' => true,
                'moderator' => false,
            ],
            'view_payments' => [
                'name' => 'Xem thanh toán',
                'description' => 'Có thể xem thông tin thanh toán',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_sellers' => [
                'name' => 'Quản lý nhà bán',
                'description' => 'Có thể quản lý nhà bán hàng',
                'admin' => true,
                'moderator' => false,
            ],
        ],

        // === QUẢN LÝ KỸ THUẬT ===
        'technical' => [
            'view_cad_files' => [
                'name' => 'Xem file CAD',
                'description' => 'Có thể xem danh sách file CAD',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_cad_files' => [
                'name' => 'Quản lý file CAD',
                'description' => 'Có thể quản lý file CAD',
                'admin' => true,
                'moderator' => false,
            ],
            'view_materials' => [
                'name' => 'Xem vật liệu',
                'description' => 'Có thể xem danh sách vật liệu',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_materials' => [
                'name' => 'Quản lý vật liệu',
                'description' => 'Có thể quản lý vật liệu',
                'admin' => true,
                'moderator' => false,
            ],
            'view_standards' => [
                'name' => 'Xem tiêu chuẩn',
                'description' => 'Có thể xem tiêu chuẩn kỹ thuật',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_standards' => [
                'name' => 'Quản lý tiêu chuẩn',
                'description' => 'Có thể quản lý tiêu chuẩn kỹ thuật',
                'admin' => true,
                'moderator' => false,
            ],
        ],

        // === THỐNG KÊ & PHÂN TÍCH ===
        'analytics' => [
            'view_statistics' => [
                'name' => 'Xem thống kê',
                'description' => 'Có thể xem thống kê cơ bản',
                'admin' => true,
                'moderator' => true,
            ],
            'view_analytics' => [
                'name' => 'Xem phân tích',
                'description' => 'Có thể xem báo cáo phân tích chi tiết',
                'admin' => true,
                'moderator' => false,
            ],
            'export_reports' => [
                'name' => 'Xuất báo cáo',
                'description' => 'Có thể xuất báo cáo',
                'admin' => true,
                'moderator' => false,
            ],
        ],

        // === GIAO TIẾP ===
        'communication' => [
            'view_messages' => [
                'name' => 'Xem tin nhắn',
                'description' => 'Có thể xem tin nhắn',
                'admin' => true,
                'moderator' => true,
            ],
            'send_messages' => [
                'name' => 'Gửi tin nhắn',
                'description' => 'Có thể gửi tin nhắn cho người dùng',
                'admin' => true,
                'moderator' => true,
            ],
            'manage_notifications' => [
                'name' => 'Quản lý thông báo',
                'description' => 'Có thể quản lý hệ thống thông báo',
                'admin' => true,
                'moderator' => false,
            ],
        ],

        // === HỆ THỐNG ===
        'system' => [
            'view_settings' => [
                'name' => 'Xem cài đặt',
                'description' => 'Có thể xem cài đặt hệ thống',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_settings' => [
                'name' => 'Quản lý cài đặt',
                'description' => 'Có thể thay đổi cài đặt hệ thống',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_seo' => [
                'name' => 'Quản lý SEO',
                'description' => 'Có thể quản lý SEO và tìm kiếm',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_performance' => [
                'name' => 'Quản lý hiệu suất',
                'description' => 'Có thể quản lý hiệu suất và bảo mật',
                'admin' => true,
                'moderator' => false,
            ],
            'manage_locations' => [
                'name' => 'Quản lý địa điểm',
                'description' => 'Có thể quản lý quốc gia và khu vực',
                'admin' => true,
                'moderator' => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Permission Mapping
    |--------------------------------------------------------------------------
    |
    | Mapping giữa routes và permissions cần thiết
    |
    */
    'route_permissions' => [
        // User management
        'admin.users.*' => 'view_users',
        'admin.users.edit' => 'edit_users',
        'admin.users.update' => 'edit_users',
        'admin.users.destroy' => 'delete_users',
        'admin.users.toggle-status' => 'ban_users',
        'admin.users.admins.*' => 'manage_admins',
        'admin.roles.*' => 'manage_roles',

        // Content management
        'admin.threads.*' => 'moderate-content',
        'admin.threads.edit' => 'moderate-content',
        'admin.threads.update' => 'moderate-content',
        'admin.threads.destroy' => 'moderate-content',
        'admin.threads.approve' => 'moderate-content',
        'admin.threads.reject' => 'moderate-content',
        'admin.comments.*' => 'moderate-content',
        'admin.categories.*' => 'manage-categories',
        'admin.forums.*' => 'manage-forums',
        'admin.reports.*' => 'view-reports',
        'admin.moderation.*' => 'moderate-content',
        'admin.moderation.reports*' => 'view-reports',
        'admin.moderation.reports.*' => 'view-reports',

        // Showcase management
        'admin.showcases.*' => 'view_showcases',
        'admin.showcases.edit' => 'edit_showcases',
        'admin.showcases.update' => 'edit_showcases',
        'admin.showcases.destroy' => 'delete_showcases',

        // Marketplace
        'admin.marketplace.products.*' => 'view_products',
        'admin.marketplace.orders.*' => 'view_orders',
        'admin.payments.*' => 'view_payments',
        'admin.marketplace.sellers.*' => 'manage_sellers',

        // Technical
        'admin.technical.cad-files.*' => 'view_cad_files',
        'admin.technical.materials.*' => 'view_materials',
        'admin.technical.standards.*' => 'view_standards',

        // Analytics
        'admin.statistics.*' => 'view_statistics',
        'admin.analytics.*' => 'view_analytics',

        // Communication
        'admin.messages.*' => 'view_messages',
        'admin.chat.*' => 'view_messages',

        // System
        'admin.settings.*' => 'view_settings',
        'admin.seo.*' => 'manage_seo',
        'admin.performance.*' => 'manage_performance',
        'admin.countries.*' => 'manage_locations',
        'admin.regions.*' => 'manage_locations',
    ],
];
