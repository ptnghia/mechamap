<?php

/**
 * Demo script để test MenuService
 * Chạy: php demo_menu_service.php
 */

// Mock User class để test
class MockUser {
    public $role;
    public $id;
    public $name;
    public $role_display_name;
    public $role_color;
    public $avatar_url;
    public $business_verified;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function canAccessAdmin() {
        return in_array($this->role, [
            'super_admin', 'system_admin', 'content_admin', 
            'content_moderator', 'marketplace_moderator', 'community_moderator'
        ]);
    }

    public function hasPermission($permission) {
        // Mock permission check
        return true;
    }
}

// Mock MenuService class
class MenuService {
    public static function getMenuComponent($user): string {
        if (!$user) {
            return 'menu.guest-menu';
        }

        return match($user->role) {
            // System Management Group
            'super_admin', 'system_admin', 'content_admin' => 'menu.admin-menu',
            
            // Community Management Group  
            'content_moderator', 'marketplace_moderator', 'community_moderator' => 'menu.admin-menu',
            
            // Business Partners Group
            'verified_partner', 'manufacturer', 'supplier', 'brand' => 'menu.business-menu',
            
            // Community Members Group
            'senior_member', 'member', 'guest' => 'menu.member-menu',
            
            // Default fallback
            default => 'menu.guest-menu'
        };
    }

    public static function getMenuConfiguration($user): array {
        if (!$user) {
            return [
                'component' => 'menu.guest-menu',
                'user_info' => null,
                'permissions' => [],
                'menu_items' => self::getGuestMenuItems(),
                'features' => [
                    'can_register' => true,
                    'can_login' => true,
                    'show_guest_notice' => true,
                ],
            ];
        }

        return [
            'component' => self::getMenuComponent($user),
            'user_info' => [
                'name' => $user->name,
                'role' => $user->role,
                'role_display_name' => $user->role_display_name,
                'role_color' => $user->role_color,
                'avatar_url' => $user->avatar_url,
                'is_verified' => $user->business_verified ?? false,
            ],
            'permissions' => self::getUserPermissions($user),
            'menu_items' => self::getMenuItemsForRole($user->role),
            'features' => self::getFeatureFlags($user),
        ];
    }

    private static function getUserPermissions($user): array {
        return [
            'can_access_admin' => $user->canAccessAdmin(),
            'can_create_content' => $user->role !== 'guest',
            'can_buy_products' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']),
            'can_sell_products' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']),
            'can_view_cart' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']) && ($user->business_verified ?? false),
            'can_moderate' => in_array($user->role, ['content_moderator', 'marketplace_moderator', 'community_moderator']),
        ];
    }

    private static function getMenuItemsForRole($role): array {
        $baseItems = [
            'home' => ['route' => 'home', 'icon' => 'fas fa-home'],
            'forums' => ['route' => 'forums.index', 'icon' => 'fas fa-comments'],
            'showcases' => ['route' => 'showcases.index', 'icon' => 'fas fa-star'],
            'marketplace' => ['route' => 'marketplace.index', 'icon' => 'fas fa-store'],
        ];

        // Add role-specific items
        switch($role) {
            case 'super_admin':
            case 'system_admin':
            case 'content_admin':
                $baseItems['admin'] = ['route' => 'admin.dashboard', 'icon' => 'fas fa-shield-alt'];
                break;
                
            case 'verified_partner':
                $baseItems['partner_dashboard'] = ['route' => 'partner.dashboard', 'icon' => 'fas fa-briefcase'];
                break;
                
            case 'manufacturer':
                $baseItems['manufacturer_dashboard'] = ['route' => 'manufacturer.dashboard', 'icon' => 'fas fa-industry'];
                break;
                
            case 'supplier':
                $baseItems['supplier_dashboard'] = ['route' => 'supplier.dashboard', 'icon' => 'fas fa-truck'];
                break;
                
            case 'brand':
                $baseItems['brand_dashboard'] = ['route' => 'brand.dashboard', 'icon' => 'fas fa-bullhorn'];
                break;
                
            case 'senior_member':
            case 'member':
                $baseItems['user_dashboard'] = ['route' => 'user.dashboard', 'icon' => 'fas fa-tachometer-alt'];
                $baseItems['docs'] = ['route' => 'docs.index', 'icon' => 'fas fa-book'];
                break;
        }

        return $baseItems;
    }

    private static function getGuestMenuItems(): array {
        return [
            'home' => ['route' => 'home', 'icon' => 'fas fa-home'],
            'forums' => ['route' => 'forums.index', 'icon' => 'fas fa-comments'],
            'showcases' => ['route' => 'showcases.index', 'icon' => 'fas fa-star'],
            'marketplace' => ['route' => 'marketplace.index', 'icon' => 'fas fa-store'],
            'login' => ['route' => 'login', 'icon' => 'fas fa-sign-in-alt'],
            'register' => ['route' => 'register', 'icon' => 'fas fa-user-plus'],
        ];
    }

    private static function getFeatureFlags($user): array {
        return [
            'show_admin_status_bar' => $user->canAccessAdmin(),
            'show_business_status_bar' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier', 'brand']),
            'show_member_status_bar' => $user->role === 'guest',
            'show_shopping_cart' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']) && ($user->business_verified ?? false),
            'show_create_dropdown' => $user->role !== 'guest',
            'show_notifications' => true,
            'enable_search' => true,
            'show_language_switcher' => true,
        ];
    }
}

// Test cases
echo "=== DEMO MENU SERVICE ===\n\n";

// Test 1: Guest user (null)
echo "1. Test Guest User (null):\n";
$guestComponent = MenuService::getMenuComponent(null);
$guestConfig = MenuService::getMenuConfiguration(null);
echo "   Component: {$guestComponent}\n";
echo "   Features: " . json_encode($guestConfig['features'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Admin roles
echo "2. Test Admin Roles:\n";
$adminRoles = ['super_admin', 'system_admin', 'content_admin', 'content_moderator'];
foreach ($adminRoles as $role) {
    $user = new MockUser([
        'role' => $role,
        'id' => 1,
        'name' => ucfirst(str_replace('_', ' ', $role)),
        'role_display_name' => ucfirst(str_replace('_', ' ', $role)),
        'role_color' => 'danger',
        'avatar_url' => '/images/admin.jpg',
        'business_verified' => false
    ]);
    
    $component = MenuService::getMenuComponent($user);
    $config = MenuService::getMenuConfiguration($user);
    
    echo "   {$role}: {$component}\n";
    echo "     - Can access admin: " . ($config['permissions']['can_access_admin'] ? 'Yes' : 'No') . "\n";
    echo "     - Can create content: " . ($config['permissions']['can_create_content'] ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Test 3: Business roles
echo "3. Test Business Roles:\n";
$businessRoles = ['verified_partner', 'manufacturer', 'supplier', 'brand'];
foreach ($businessRoles as $role) {
    $user = new MockUser([
        'role' => $role,
        'id' => 2,
        'name' => ucfirst(str_replace('_', ' ', $role)),
        'role_display_name' => ucfirst(str_replace('_', ' ', $role)),
        'role_color' => 'success',
        'avatar_url' => '/images/business.jpg',
        'business_verified' => true
    ]);
    
    $component = MenuService::getMenuComponent($user);
    $config = MenuService::getMenuConfiguration($user);
    
    echo "   {$role}: {$component}\n";
    echo "     - Can buy products: " . ($config['permissions']['can_buy_products'] ? 'Yes' : 'No') . "\n";
    echo "     - Can sell products: " . ($config['permissions']['can_sell_products'] ? 'Yes' : 'No') . "\n";
    echo "     - Can view cart: " . ($config['permissions']['can_view_cart'] ? 'Yes' : 'No') . "\n";
    echo "     - Show business status bar: " . ($config['features']['show_business_status_bar'] ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Test 4: Member roles
echo "4. Test Member Roles:\n";
$memberRoles = ['senior_member', 'member', 'guest'];
foreach ($memberRoles as $role) {
    $user = new MockUser([
        'role' => $role,
        'id' => 3,
        'name' => ucfirst(str_replace('_', ' ', $role)),
        'role_display_name' => ucfirst(str_replace('_', ' ', $role)),
        'role_color' => 'primary',
        'avatar_url' => '/images/member.jpg',
        'business_verified' => false
    ]);
    
    $component = MenuService::getMenuComponent($user);
    $config = MenuService::getMenuConfiguration($user);
    
    echo "   {$role}: {$component}\n";
    echo "     - Can create content: " . ($config['permissions']['can_create_content'] ? 'Yes' : 'No') . "\n";
    echo "     - Show create dropdown: " . ($config['features']['show_create_dropdown'] ? 'Yes' : 'No') . "\n";
    echo "     - Show member status bar: " . ($config['features']['show_member_status_bar'] ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Test 5: Business verification status
echo "5. Test Business Verification Status:\n";
$unverifiedUser = new MockUser([
    'role' => 'manufacturer',
    'id' => 4,
    'name' => 'Unverified Manufacturer',
    'role_display_name' => 'Manufacturer',
    'role_color' => 'warning',
    'avatar_url' => '/images/unverified.jpg',
    'business_verified' => false
]);

$verifiedUser = new MockUser([
    'role' => 'manufacturer',
    'id' => 5,
    'name' => 'Verified Manufacturer',
    'role_display_name' => 'Manufacturer',
    'role_color' => 'success',
    'avatar_url' => '/images/verified.jpg',
    'business_verified' => true
]);

$unverifiedConfig = MenuService::getMenuConfiguration($unverifiedUser);
$verifiedConfig = MenuService::getMenuConfiguration($verifiedUser);

echo "   Unverified Manufacturer:\n";
echo "     - Can view cart: " . ($unverifiedConfig['permissions']['can_view_cart'] ? 'Yes' : 'No') . "\n";
echo "     - Is verified: " . ($unverifiedConfig['user_info']['is_verified'] ? 'Yes' : 'No') . "\n";

echo "   Verified Manufacturer:\n";
echo "     - Can view cart: " . ($verifiedConfig['permissions']['can_view_cart'] ? 'Yes' : 'No') . "\n";
echo "     - Is verified: " . ($verifiedConfig['user_info']['is_verified'] ? 'Yes' : 'No') . "\n";

echo "\n=== DEMO COMPLETED ===\n";
