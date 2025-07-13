<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * MechaMap Menu Service
 * 
 * Quản lý việc chọn menu component phù hợp dựa vào user role
 * và cung cấp menu configuration cho từng loại user
 */
class MenuService
{
    /**
     * Cache key prefix cho menu configurations
     */
    const CACHE_PREFIX = 'menu_config_';
    
    /**
     * Cache TTL (Time To Live) - 1 hour
     */
    const CACHE_TTL = 3600;

    /**
     * Lấy menu component phù hợp cho user
     *
     * @param User|null $user
     * @return string
     */
    public static function getMenuComponent(?User $user): string
    {
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

    /**
     * Lấy menu configuration cho user với caching
     *
     * @param User|null $user
     * @return array
     */
    public static function getMenuConfiguration(?User $user): array
    {
        if (!$user) {
            return self::getGuestMenuConfiguration();
        }

        $cacheKey = self::CACHE_PREFIX . $user->role . '_' . $user->id;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            return self::buildMenuConfiguration($user);
        });
    }

    /**
     * Xây dựng menu configuration cho user
     *
     * @param User $user
     * @return array
     */
    private static function buildMenuConfiguration(User $user): array
    {
        $config = [
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

        return $config;
    }

    /**
     * Lấy menu configuration cho guest (chưa đăng nhập)
     *
     * @return array
     */
    private static function getGuestMenuConfiguration(): array
    {
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

    /**
     * Lấy permissions của user
     *
     * @param User $user
     * @return array
     */
    private static function getUserPermissions(User $user): array
    {
        return [
            'can_access_admin' => $user->canAccessAdmin(),
            'can_create_content' => $user->role !== 'guest',
            'can_buy_products' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']),
            'can_sell_products' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']),
            'can_view_cart' => in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']) && ($user->business_verified ?? false),
            'can_moderate' => in_array($user->role, ['content_moderator', 'marketplace_moderator', 'community_moderator']),
        ];
    }

    /**
     * Lấy menu items cho role cụ thể
     *
     * @param string $role
     * @return array
     */
    private static function getMenuItemsForRole(string $role): array
    {
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

        return self::validateMenuRoutes($baseItems);
    }

    /**
     * Lấy menu items cho guest
     *
     * @return array
     */
    private static function getGuestMenuItems(): array
    {
        $items = [
            'home' => ['route' => 'home', 'icon' => 'fas fa-home'],
            'forums' => ['route' => 'forums.index', 'icon' => 'fas fa-comments'],
            'showcases' => ['route' => 'showcases.index', 'icon' => 'fas fa-star'],
            'marketplace' => ['route' => 'marketplace.index', 'icon' => 'fas fa-store'],
            'login' => ['route' => 'login', 'icon' => 'fas fa-sign-in-alt'],
            'register' => ['route' => 'register', 'icon' => 'fas fa-user-plus'],
        ];

        return self::validateMenuRoutes($items);
    }

    /**
     * Validate menu routes và loại bỏ routes không tồn tại
     *
     * @param array $items
     * @return array
     */
    private static function validateMenuRoutes(array $items): array
    {
        $validatedItems = [];
        
        foreach ($items as $key => $item) {
            if (Route::has($item['route'])) {
                $validatedItems[$key] = $item;
            } else {
                // Log missing route for debugging
                Log::warning("Menu route not found: {$item['route']}", [
                    'menu_key' => $key,
                    'route' => $item['route']
                ]);
            }
        }
        
        return $validatedItems;
    }

    /**
     * Lấy feature flags cho user
     *
     * @param User $user
     * @return array
     */
    private static function getFeatureFlags(User $user): array
    {
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

    /**
     * Invalidate menu cache cho user
     *
     * @param User $user
     * @return void
     */
    public static function invalidateUserMenuCache(User $user): void
    {
        $cacheKey = self::CACHE_PREFIX . $user->role . '_' . $user->id;
        Cache::forget($cacheKey);
        
        Log::info("Menu cache invalidated for user", [
            'user_id' => $user->id,
            'role' => $user->role,
            'cache_key' => $cacheKey
        ]);
    }

    /**
     * Invalidate tất cả menu cache cho role
     *
     * @param string $role
     * @return void
     */
    public static function invalidateRoleMenuCache(string $role): void
    {
        $pattern = self::CACHE_PREFIX . $role . '_*';
        
        // Note: Trong production nên sử dụng Redis với pattern matching
        // Hiện tại chỉ log để tracking
        Log::info("Menu cache invalidation requested for role", [
            'role' => $role,
            'pattern' => $pattern
        ]);
    }

    /**
     * Kiểm tra user có quyền truy cập menu item không
     *
     * @param User|null $user
     * @param string $route
     * @param string|null $permission
     * @return bool
     */
    public static function canAccessMenuItem(?User $user, string $route, ?string $permission = null): bool
    {
        // Kiểm tra route tồn tại
        if (!Route::has($route)) {
            return false;
        }

        // Nếu không có user và không cần permission -> cho phép (public route)
        if (!$user && !$permission) {
            return true;
        }

        // Nếu có user nhưng không cần permission -> cho phép
        if ($user && !$permission) {
            return true;
        }

        // Nếu cần permission -> kiểm tra permission
        if ($permission && $user) {
            return $user->hasPermission($permission);
        }

        return false;
    }
}
