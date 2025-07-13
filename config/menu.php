<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MechaMap Menu Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình menu items, permissions, và metadata cho từng role
    | Sử dụng bởi MenuService để build menu components
    |
    */

    'cache_ttl' => env('MENU_CACHE_TTL', 3600), // 1 hour
    'enable_caching' => env('MENU_ENABLE_CACHING', true),
    'enable_route_validation' => env('MENU_ENABLE_ROUTE_VALIDATION', true),

    /*
    |--------------------------------------------------------------------------
    | Role Menu Mappings
    |--------------------------------------------------------------------------
    |
    | Map từng role với menu component tương ứng
    |
    */
    'role_components' => [
        // System Management
        'super_admin' => 'menu.admin-menu',
        'system_admin' => 'menu.admin-menu',
        'content_admin' => 'menu.admin-menu',
        
        // Community Management
        'content_moderator' => 'menu.admin-menu',
        'marketplace_moderator' => 'menu.admin-menu',
        'community_moderator' => 'menu.admin-menu',
        
        // Business Partners
        'verified_partner' => 'menu.business-menu',
        'manufacturer' => 'menu.business-menu',
        'supplier' => 'menu.business-menu',
        'brand' => 'menu.business-menu',
        
        // Community Members
        'senior_member' => 'menu.member-menu',
        'member' => 'menu.member-menu',
        'guest' => 'menu.member-menu',
        
        // Default
        'default' => 'menu.guest-menu',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common Menu Items
    |--------------------------------------------------------------------------
    |
    | Menu items được sử dụng chung cho nhiều roles
    |
    */
    'common_items' => [
        'home' => [
            'title' => 'nav.home',
            'route' => 'home',
            'icon' => 'fas fa-home',
            'permission' => null,
            'description' => 'Trang chủ MechaMap',
            'order' => 1,
        ],
        'forums' => [
            'title' => 'nav.forums',
            'route' => 'forums.index',
            'icon' => 'fas fa-comments',
            'permission' => null,
            'description' => 'Diễn đàn cộng đồng',
            'order' => 2,
        ],
        'showcases' => [
            'title' => 'nav.showcases',
            'route' => 'showcases.index',
            'icon' => 'fas fa-star',
            'permission' => null,
            'description' => 'Showcase sản phẩm',
            'order' => 3,
        ],
        'marketplace' => [
            'title' => 'nav.marketplace',
            'route' => 'marketplace.index',
            'icon' => 'fas fa-store',
            'permission' => null,
            'description' => 'Marketplace',
            'order' => 4,
        ],
        'docs' => [
            'title' => 'nav.docs',
            'route' => 'docs.index',
            'icon' => 'fas fa-book',
            'permission' => null,
            'description' => 'Tài liệu hướng dẫn',
            'order' => 5,
            'roles' => ['senior_member', 'member'], // Chỉ hiển thị cho registered users
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role-Specific Menu Items
    |--------------------------------------------------------------------------
    |
    | Menu items riêng cho từng role hoặc nhóm roles
    |
    */
    'role_specific_items' => [
        // Admin roles
        'admin_roles' => [
            'admin_dashboard' => [
                'title' => 'nav.admin.dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'permission' => 'access-admin-panel',
                'description' => 'Bảng điều khiển admin',
                'order' => 10,
                'roles' => ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'],
            ],
            'admin_users' => [
                'title' => 'nav.admin.users',
                'route' => 'admin.users.index',
                'icon' => 'fas fa-users',
                'permission' => 'view-users',
                'description' => 'Quản lý người dùng',
                'order' => 11,
                'roles' => ['super_admin', 'system_admin'],
            ],
            'admin_content' => [
                'title' => 'nav.admin.content',
                'route' => 'admin.content.index',
                'icon' => 'fas fa-file-alt',
                'permission' => 'manage-content',
                'description' => 'Quản lý nội dung',
                'order' => 12,
                'roles' => ['super_admin', 'system_admin', 'content_admin', 'content_moderator'],
            ],
            'admin_marketplace' => [
                'title' => 'nav.admin.marketplace',
                'route' => 'admin.marketplace.index',
                'icon' => 'fas fa-store-alt',
                'permission' => 'manage-marketplace',
                'description' => 'Quản lý marketplace',
                'order' => 13,
                'roles' => ['super_admin', 'system_admin', 'marketplace_moderator'],
            ],
            'admin_settings' => [
                'title' => 'nav.admin.settings',
                'route' => 'admin.settings.index',
                'icon' => 'fas fa-cogs',
                'permission' => 'manage-system',
                'description' => 'Cài đặt hệ thống',
                'order' => 14,
                'roles' => ['super_admin', 'system_admin'],
            ],
        ],

        // Business roles
        'business_roles' => [
            'verified_partner' => [
                'partner_dashboard' => [
                    'title' => 'nav.business.partner_dashboard',
                    'route' => 'partner.dashboard',
                    'icon' => 'fas fa-briefcase',
                    'permission' => null,
                    'description' => 'Dashboard đối tác',
                    'order' => 20,
                ],
                'partner_products' => [
                    'title' => 'nav.business.my_products',
                    'route' => 'partner.products.index',
                    'icon' => 'fas fa-box',
                    'permission' => 'manage-own-products',
                    'description' => 'Sản phẩm của tôi',
                    'order' => 21,
                ],
            ],
            'manufacturer' => [
                'manufacturer_dashboard' => [
                    'title' => 'nav.business.manufacturer_dashboard',
                    'route' => 'manufacturer.dashboard',
                    'icon' => 'fas fa-industry',
                    'permission' => null,
                    'description' => 'Dashboard nhà sản xuất',
                    'order' => 20,
                ],
                'manufacturer_products' => [
                    'title' => 'nav.business.my_products',
                    'route' => 'manufacturer.products.index',
                    'icon' => 'fas fa-box',
                    'permission' => 'manage-own-products',
                    'description' => 'Sản phẩm của tôi',
                    'order' => 21,
                ],
                'manufacturer_cad' => [
                    'title' => 'nav.business.cad_files',
                    'route' => 'manufacturer.cad.index',
                    'icon' => 'fas fa-drafting-compass',
                    'permission' => 'manage-cad-files',
                    'description' => 'File CAD',
                    'order' => 22,
                ],
            ],
            'supplier' => [
                'supplier_dashboard' => [
                    'title' => 'nav.business.supplier_dashboard',
                    'route' => 'supplier.dashboard',
                    'icon' => 'fas fa-truck',
                    'permission' => null,
                    'description' => 'Dashboard nhà cung cấp',
                    'order' => 20,
                ],
                'supplier_products' => [
                    'title' => 'nav.business.my_products',
                    'route' => 'supplier.products.index',
                    'icon' => 'fas fa-box',
                    'permission' => 'manage-own-products',
                    'description' => 'Sản phẩm của tôi',
                    'order' => 21,
                ],
            ],
            'brand' => [
                'brand_dashboard' => [
                    'title' => 'nav.business.brand_dashboard',
                    'route' => 'brand.dashboard',
                    'icon' => 'fas fa-bullhorn',
                    'permission' => null,
                    'description' => 'Dashboard thương hiệu',
                    'order' => 20,
                ],
                'brand_insights' => [
                    'title' => 'nav.business.market_insights',
                    'route' => 'brand.insights.index',
                    'icon' => 'fas fa-lightbulb',
                    'permission' => 'view-market-insights',
                    'description' => 'Thông tin thị trường',
                    'order' => 21,
                ],
                'brand_advertising' => [
                    'title' => 'nav.business.advertising',
                    'route' => 'brand.advertising.index',
                    'icon' => 'fas fa-ad',
                    'permission' => 'manage-advertising',
                    'description' => 'Quảng cáo',
                    'order' => 22,
                ],
            ],
        ],

        // Member roles
        'member_roles' => [
            'user_dashboard' => [
                'title' => 'nav.user.dashboard',
                'route' => 'user.dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'permission' => null,
                'description' => 'Dashboard cá nhân',
                'order' => 30,
                'roles' => ['senior_member', 'member', 'guest'],
            ],
            'my_threads' => [
                'title' => 'nav.user.my_threads',
                'route' => 'user.my-threads',
                'icon' => 'fas fa-comments',
                'permission' => 'create-threads',
                'description' => 'Bài viết của tôi',
                'order' => 31,
                'roles' => ['senior_member', 'member'], // Guest không thể tạo threads
            ],
            'bookmarks' => [
                'title' => 'nav.user.bookmarks',
                'route' => 'user.bookmarks',
                'icon' => 'fas fa-bookmark',
                'permission' => null,
                'description' => 'Bookmark',
                'order' => 32,
                'roles' => ['senior_member', 'member', 'guest'],
            ],
            'following' => [
                'title' => 'nav.user.following',
                'route' => 'user.following',
                'icon' => 'fas fa-heart',
                'permission' => 'follow-users',
                'description' => 'Đang theo dõi',
                'order' => 33,
                'roles' => ['senior_member', 'member', 'guest'],
            ],
            'ratings' => [
                'title' => 'nav.user.ratings',
                'route' => 'user.ratings',
                'icon' => 'fas fa-star-half-alt',
                'permission' => 'rate-products',
                'description' => 'Đánh giá của tôi',
                'order' => 34,
                'roles' => ['senior_member', 'member'], // Guest không thể rate
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Menu Items
    |--------------------------------------------------------------------------
    |
    | Menu items cho user profile dropdown
    |
    */
    'profile_items' => [
        'profile_view' => [
            'title' => 'nav.user.profile',
            'route' => 'profile.show',
            'icon' => 'fas fa-user',
            'permission' => null,
            'description' => 'Xem hồ sơ',
            'order' => 1,
            'params' => 'username', // Sẽ được thay thế bằng username của user
        ],
        'profile_edit' => [
            'title' => 'nav.user.account_settings',
            'route' => 'profile.edit',
            'icon' => 'fas fa-cog',
            'permission' => null,
            'description' => 'Cài đặt tài khoản',
            'order' => 2,
        ],
        'business_profile' => [
            'title' => 'nav.business.business_profile',
            'route' => '{role}.profile.edit', // Dynamic route based on role
            'icon' => 'fas fa-building',
            'permission' => 'manage-business-profile',
            'description' => 'Hồ sơ doanh nghiệp',
            'order' => 3,
            'roles' => ['verified_partner', 'manufacturer', 'supplier', 'brand'],
        ],
        'admin_profile' => [
            'title' => 'nav.admin.profile',
            'route' => 'admin.profile.index',
            'icon' => 'fas fa-user-shield',
            'permission' => 'access-admin-panel',
            'description' => 'Hồ sơ admin',
            'order' => 4,
            'roles' => ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Menu Items
    |--------------------------------------------------------------------------
    |
    | Menu items cho authentication (guest users)
    |
    */
    'auth_items' => [
        'login' => [
            'title' => 'auth.login',
            'route' => 'login',
            'icon' => 'fas fa-sign-in-alt',
            'permission' => null,
            'description' => 'Đăng nhập',
            'order' => 1,
            'class' => 'btn btn-outline-primary',
        ],
        'register' => [
            'title' => 'auth.register',
            'route' => 'register',
            'icon' => 'fas fa-user-plus',
            'permission' => null,
            'description' => 'Đăng ký',
            'order' => 2,
            'class' => 'btn btn-primary',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Cấu hình các tính năng có thể bật/tắt cho từng role
    |
    */
    'features' => [
        'shopping_cart' => [
            'enabled_roles' => ['verified_partner', 'manufacturer', 'supplier'],
            'requires_verification' => true,
        ],
        'create_content' => [
            'enabled_roles' => ['senior_member', 'member', 'verified_partner', 'manufacturer', 'supplier', 'brand'],
            'disabled_roles' => ['guest'],
        ],
        'admin_access' => [
            'enabled_roles' => ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'],
        ],
        'business_features' => [
            'enabled_roles' => ['verified_partner', 'manufacturer', 'supplier', 'brand'],
            'requires_verification' => true,
        ],
        'notifications' => [
            'enabled_roles' => ['*'], // All authenticated users
        ],
        'search' => [
            'enabled_roles' => ['*'], // All users including guests
        ],
        'language_switcher' => [
            'enabled_roles' => ['*'], // All users including guests
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Validation Rules
    |--------------------------------------------------------------------------
    |
    | Rules để validate menu items
    |
    */
    'validation' => [
        'required_fields' => ['title', 'route', 'icon'],
        'optional_fields' => ['permission', 'description', 'order', 'roles', 'params', 'class'],
        'max_menu_depth' => 3,
        'max_items_per_menu' => 20,
    ],
];
