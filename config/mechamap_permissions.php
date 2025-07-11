<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MechaMap Permission System Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình hệ thống phân quyền MechaMap theo kế hoạch tái cấu trúc
    | 4 nhóm chính với 14 roles và 64 permissions
    |
    */

    'role_groups' => [
        'system_management' => [
            'name' => 'Quản lý hệ thống',
            'description' => 'Nhóm quản lý hệ thống và infrastructure',
            'roles' => ['super_admin', 'system_admin', 'content_admin'],
            'color' => 'danger',
            'icon' => 'fas fa-cogs',
        ],
        'community_management' => [
            'name' => 'Quản lý cộng đồng',
            'description' => 'Nhóm quản lý và kiểm duyệt cộng đồng',
            'roles' => ['content_moderator', 'marketplace_moderator', 'community_moderator'],
            'color' => 'warning',
            'icon' => 'fas fa-users-cog',
        ],
        'community_members' => [
            'name' => 'Thành viên cộng đồng',
            'description' => 'Các thành viên tham gia cộng đồng',
            'roles' => ['senior_member', 'member', 'guest'],
            'color' => 'primary',
            'icon' => 'fas fa-users',
        ],
        'business_partners' => [
            'name' => 'Đối tác kinh doanh',
            'description' => 'Các đối tác kinh doanh và marketplace',
            'roles' => ['manufacturer', 'supplier', 'brand', 'verified_partner'],
            'color' => 'success',
            'icon' => 'fas fa-handshake',
        ],
    ],

    'role_hierarchy' => [
        // System Management (Level 1-3)
        'super_admin' => 1,
        'system_admin' => 2,
        'content_admin' => 3,

        // Community Management (Level 4-6)
        'content_moderator' => 4,
        'marketplace_moderator' => 5,
        'community_moderator' => 6,

        // Community Members (Level 7-9)
        'senior_member' => 7,
        'member' => 8,
        'guest' => 9,

        // Business Partners (Level 10-13)
        'verified_partner' => 10,
        'manufacturer' => 11,
        'supplier' => 12,
        'brand' => 13,
    ],

    'permission_groups' => [
        'system' => [
            'name' => 'Quản lý hệ thống',
            'permissions' => [
                'manage-system',
                'manage-infrastructure',
                'manage-database',
                'manage-security',
                'access-super-admin',
                'view-system-logs',
                'manage-backups',
            ],
        ],
        'users' => [
            'name' => 'Quản lý người dùng',
            'permissions' => [
                'view-users',
                'create-users',
                'update-users',
                'delete-users',
                'ban-users',
                'manage-user-roles',
                'verify-business-accounts',
                'manage-subscriptions',
            ],
        ],
        'content' => [
            'name' => 'Quản lý nội dung',
            'permissions' => [
                'manage-content',
                'moderate-content',
                'approve-content',
                'delete-content',
                'manage-categories',
                'manage-forums',
                'pin-threads',
                'lock-threads',
                'feature-content',
            ],
        ],
        'marketplace' => [
            'name' => 'Quản lý marketplace',
            'permissions' => [
                'manage-marketplace',
                'approve-products',
                'manage-orders',
                'manage-payments',
                'view-marketplace-analytics',
                'manage-seller-accounts',
                'handle-disputes',
                'manage-commissions',
            ],
        ],
        'community' => [
            'name' => 'Quản lý cộng đồng',
            'permissions' => [
                'manage-community',
                'moderate-discussions',
                'manage-events',
                'send-announcements',
                'manage-user-groups',
            ],
        ],
        'analytics' => [
            'name' => 'Báo cáo & Phân tích',
            'permissions' => [
                'view-analytics',
                'view-reports',
                'export-data',
                'manage-reports',
            ],
        ],
        'admin_access' => [
            'name' => 'Truy cập Admin',
            'permissions' => [
                'access-admin-panel',
                'access-system-admin',
                'access-content-admin',
                'access-marketplace-admin',
                'access-community-admin',
            ],
        ],
        'basic' => [
            'name' => 'Quyền cơ bản',
            'permissions' => [
                'view-content',
                'create-threads',
                'create-comments',
                'upload-files',
                'send-messages',
                'create-polls',
                'rate-products',
                'write-reviews',
            ],
        ],
        'guest' => [
            'name' => 'Quyền khách',
            'permissions' => [
                'view-content',           // ✅ Xem nội dung công khai
                'follow-users',           // ✅ Theo dõi Member
                'follow-threads',         // ✅ Theo dõi bài đăng
                'follow-showcases',       // ✅ Theo dõi showcases
                'receive-notifications',  // ✅ Nhận thông báo
                'browse-forums',          // ✅ Duyệt forums
                'browse-showcases',       // ✅ Xem showcases
            ],
        ],
        'business' => [
            'name' => 'Quyền kinh doanh',
            'permissions' => [
                'sell-products',
                'manage-own-products',
                'view-sales-analytics',
                'manage-business-profile',
                'access-seller-dashboard',
                'upload-technical-files',
                'manage-cad-files',
                'access-b2b-features',
            ],
        ],
    ],

    'admin_routes' => [
        'system_management' => [
            'admin.dashboard',
            'admin.users.*',
            'admin.settings.*',
            'admin.system.*',
            'admin.logs.*',
        ],
        'community_management' => [
            'admin.dashboard',
            'admin.content.*',
            'admin.forums.*',
            'admin.marketplace.*',
            'admin.community.*',
        ],
    ],

    'marketplace_features' => [
        'manufacturer' => [
            'can_sell_technical_files' => true,
            'can_sell_cad_files' => true,
            'can_sell_physical_products' => true,
            'commission_rate' => 5.0,
        ],
        'supplier' => [
            'can_sell_physical_products' => true,
            'can_buy_products' => true,
            'commission_rate' => 3.0,
        ],
        'brand' => [
            'can_view_only' => true,
            'can_promote' => true,
            'advertising_features' => true,
        ],
        'verified_partner' => [
            'can_sell_technical_files' => true,
            'can_sell_cad_files' => true,
            'can_sell_physical_products' => true,
            'priority_support' => true,
            'commission_rate' => 2.0,
        ],
    ],
];
