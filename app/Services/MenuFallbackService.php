<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\RouteValidationHelper;
use App\Helpers\MenuPermissionHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Menu Fallback Service
 * 
 * Service để xử lý fallback khi routes không tồn tại hoặc user không có quyền
 * Đảm bảo menu luôn hiển thị được ngay cả khi có lỗi
 */
class MenuFallbackService
{
    /**
     * Cache key prefix
     */
    const CACHE_PREFIX = 'menu_fallback_';
    
    /**
     * Cache TTL - 1 hour
     */
    const CACHE_TTL = 3600;

    /**
     * Xử lý fallback cho menu configuration
     *
     * @param array $menuConfig
     * @param User|null $user
     * @return array
     */
    public static function processFallbackMenu(array $menuConfig, ?User $user = null): array
    {
        $processedConfig = $menuConfig;
        
        if (isset($menuConfig['menu_items'])) {
            $processedConfig['menu_items'] = self::processMenuItems($menuConfig['menu_items'], $user);
        }
        
        // Đảm bảo luôn có ít nhất home menu
        if (empty($processedConfig['menu_items'])) {
            $processedConfig['menu_items'] = self::getEmergencyMenu($user);
        }
        
        return $processedConfig;
    }

    /**
     * Xử lý từng menu item với fallback
     *
     * @param array $menuItems
     * @param User|null $user
     * @return array
     */
    private static function processMenuItems(array $menuItems, ?User $user): array
    {
        $processedItems = [];
        
        foreach ($menuItems as $key => $menuItem) {
            $processedItem = self::processMenuItem($menuItem, $user, $key);
            
            if ($processedItem) {
                $processedItems[$key] = $processedItem;
            }
        }
        
        return $processedItems;
    }

    /**
     * Xử lý một menu item với fallback
     *
     * @param array $menuItem
     * @param User|null $user
     * @param string $menuKey
     * @return array|null
     */
    private static function processMenuItem(array $menuItem, ?User $user, string $menuKey): ?array
    {
        // Kiểm tra permission trước
        if (!MenuPermissionHelper::canAccessMenuItem($user, $menuItem)) {
            self::logFallbackAction('permission_denied', $menuKey, $menuItem, $user);
            return null; // Không hiển thị menu item
        }

        // Kiểm tra route tồn tại
        if (!isset($menuItem['route'])) {
            self::logFallbackAction('missing_route_config', $menuKey, $menuItem, $user);
            return null;
        }

        if (!RouteValidationHelper::routeExists($menuItem['route'])) {
            // Tìm fallback route
            $fallbackRoute = self::findFallbackRoute($menuItem['route'], $user);
            
            if ($fallbackRoute) {
                $menuItem['route'] = $fallbackRoute;
                $menuItem['_fallback'] = true;
                $menuItem['_original_route'] = $menuItem['route'];
                
                self::logFallbackAction('route_fallback', $menuKey, $menuItem, $user);
            } else {
                self::logFallbackAction('no_fallback_found', $menuKey, $menuItem, $user);
                return null;
            }
        }

        // Kiểm tra route accessibility
        if (!RouteValidationHelper::isRouteAccessible($menuItem['route'], $user)) {
            $fallbackRoute = self::findAccessibleFallbackRoute($menuItem['route'], $user);
            
            if ($fallbackRoute) {
                $menuItem['route'] = $fallbackRoute;
                $menuItem['_fallback'] = true;
                $menuItem['_accessibility_fallback'] = true;
                
                self::logFallbackAction('accessibility_fallback', $menuKey, $menuItem, $user);
            } else {
                self::logFallbackAction('no_accessible_fallback', $menuKey, $menuItem, $user);
                return null;
            }
        }

        return $menuItem;
    }

    /**
     * Tìm fallback route cho route không tồn tại
     *
     * @param string $originalRoute
     * @param User|null $user
     * @return string|null
     */
    private static function findFallbackRoute(string $originalRoute, ?User $user): ?string
    {
        $cacheKey = self::CACHE_PREFIX . 'route_' . md5($originalRoute . ($user?->role ?? 'guest'));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($originalRoute, $user) {
            // Sử dụng RouteValidationHelper để generate fallback
            $fallback = RouteValidationHelper::generateFallbackRoute($originalRoute);
            
            // Kiểm tra fallback có accessible không
            if (RouteValidationHelper::isRouteAccessible($fallback, $user)) {
                return $fallback;
            }
            
            // Thử các fallback khác
            $alternativeFallbacks = self::getAlternativeFallbacks($originalRoute, $user);
            
            foreach ($alternativeFallbacks as $alternative) {
                if (RouteValidationHelper::routeExists($alternative) && 
                    RouteValidationHelper::isRouteAccessible($alternative, $user)) {
                    return $alternative;
                }
            }
            
            return null;
        });
    }

    /**
     * Tìm fallback route cho route không accessible
     *
     * @param string $originalRoute
     * @param User|null $user
     * @return string|null
     */
    private static function findAccessibleFallbackRoute(string $originalRoute, ?User $user): ?string
    {
        $fallbacks = self::getAccessibilityFallbacks($originalRoute, $user);
        
        foreach ($fallbacks as $fallback) {
            if (RouteValidationHelper::routeExists($fallback) && 
                RouteValidationHelper::isRouteAccessible($fallback, $user)) {
                return $fallback;
            }
        }
        
        return null;
    }

    /**
     * Lấy alternative fallbacks cho route
     *
     * @param string $originalRoute
     * @param User|null $user
     * @return array
     */
    private static function getAlternativeFallbacks(string $originalRoute, ?User $user): array
    {
        $userRole = $user?->role ?? 'guest';
        
        // Role-based fallbacks
        $roleFallbacks = [
            'admin' => ['admin.dashboard', 'home'],
            'business' => ['user.dashboard', 'home'],
            'member' => ['user.dashboard', 'forums.index', 'home'],
            'guest' => ['forums.index', 'showcases.index', 'home']
        ];
        
        // Determine user category
        $userCategory = 'guest';
        if ($user) {
            if (in_array($userRole, ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'])) {
                $userCategory = 'admin';
            } elseif (in_array($userRole, ['verified_partner', 'manufacturer', 'supplier', 'brand'])) {
                $userCategory = 'business';
            } elseif (in_array($userRole, ['senior_member', 'member', 'guest'])) {
                $userCategory = 'member';
            }
        }
        
        return $roleFallbacks[$userCategory] ?? $roleFallbacks['guest'];
    }

    /**
     * Lấy accessibility fallbacks
     *
     * @param string $originalRoute
     * @param User|null $user
     * @return array
     */
    private static function getAccessibilityFallbacks(string $originalRoute, ?User $user): array
    {
        // Common accessible routes for different user types
        if (!$user) {
            return ['home', 'forums.index', 'showcases.index'];
        }
        
        $fallbacks = ['home'];
        
        // Add role-appropriate fallbacks
        if ($user->role !== 'guest') {
            $fallbacks[] = 'user.dashboard';
            $fallbacks[] = 'forums.index';
            $fallbacks[] = 'showcases.index';
        }
        
        if (in_array($user->role, ['verified_partner', 'manufacturer', 'supplier', 'brand'])) {
            $fallbacks[] = 'marketplace.index';
        }
        
        return $fallbacks;
    }

    /**
     * Lấy emergency menu khi tất cả menu items đều fail
     *
     * @param User|null $user
     * @return array
     */
    private static function getEmergencyMenu(?User $user): array
    {
        $emergencyItems = [
            'home' => [
                'title' => 'Trang chủ',
                'route' => 'home',
                'icon' => 'fas fa-home',
                '_emergency' => true
            ]
        ];
        
        if (!$user) {
            $emergencyItems['login'] = [
                'title' => 'Đăng nhập',
                'route' => 'login',
                'icon' => 'fas fa-sign-in-alt',
                '_emergency' => true
            ];
        } else {
            // Add safe routes for authenticated users
            if (RouteValidationHelper::routeExists('forums.index')) {
                $emergencyItems['forums'] = [
                    'title' => 'Diễn đàn',
                    'route' => 'forums.index',
                    'icon' => 'fas fa-comments',
                    '_emergency' => true
                ];
            }
            
            if (RouteValidationHelper::routeExists('showcases.index')) {
                $emergencyItems['showcases'] = [
                    'title' => 'Showcases',
                    'route' => 'showcases.index',
                    'icon' => 'fas fa-star',
                    '_emergency' => true
                ];
            }
        }
        
        self::logFallbackAction('emergency_menu_used', 'system', [], $user);
        
        return $emergencyItems;
    }

    /**
     * Tạo fallback menu cho specific role
     *
     * @param string $role
     * @return array
     */
    public static function createRoleFallbackMenu(string $role): array
    {
        $fallbackMenus = [
            'super_admin' => [
                'home' => ['title' => 'Trang chủ', 'route' => 'home', 'icon' => 'fas fa-home'],
                'admin' => ['title' => 'Admin Panel', 'route' => 'admin.dashboard', 'icon' => 'fas fa-shield-alt']
            ],
            'verified_partner' => [
                'home' => ['title' => 'Trang chủ', 'route' => 'home', 'icon' => 'fas fa-home'],
                'dashboard' => ['title' => 'Dashboard', 'route' => 'user.dashboard', 'icon' => 'fas fa-tachometer-alt'],
                'marketplace' => ['title' => 'Marketplace', 'route' => 'marketplace.index', 'icon' => 'fas fa-store']
            ],
            'member' => [
                'home' => ['title' => 'Trang chủ', 'route' => 'home', 'icon' => 'fas fa-home'],
                'forums' => ['title' => 'Diễn đàn', 'route' => 'forums.index', 'icon' => 'fas fa-comments'],
                'showcases' => ['title' => 'Showcases', 'route' => 'showcases.index', 'icon' => 'fas fa-star']
            ],
            'guest' => [
                'home' => ['title' => 'Trang chủ', 'route' => 'home', 'icon' => 'fas fa-home'],
                'login' => ['title' => 'Đăng nhập', 'route' => 'login', 'icon' => 'fas fa-sign-in-alt']
            ]
        ];
        
        return $fallbackMenus[$role] ?? $fallbackMenus['guest'];
    }

    /**
     * Kiểm tra menu có cần fallback không
     *
     * @param array $menuConfig
     * @param User|null $user
     * @return bool
     */
    public static function needsFallback(array $menuConfig, ?User $user = null): bool
    {
        if (!isset($menuConfig['menu_items']) || empty($menuConfig['menu_items'])) {
            return true;
        }
        
        $validItems = 0;
        
        foreach ($menuConfig['menu_items'] as $menuItem) {
            if (isset($menuItem['route']) && 
                RouteValidationHelper::routeExists($menuItem['route']) &&
                MenuPermissionHelper::canAccessMenuItem($user, $menuItem)) {
                $validItems++;
            }
        }
        
        return $validItems === 0;
    }

    /**
     * Log fallback actions
     *
     * @param string $action
     * @param string $menuKey
     * @param array $menuItem
     * @param User|null $user
     * @return void
     */
    private static function logFallbackAction(string $action, string $menuKey, array $menuItem, ?User $user): void
    {
        Log::info("Menu fallback action", [
            'action' => $action,
            'menu_key' => $menuKey,
            'original_route' => $menuItem['route'] ?? null,
            'fallback_route' => $menuItem['_fallback'] ?? null,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'timestamp' => now()
        ]);
    }

    /**
     * Get fallback statistics
     *
     * @return array
     */
    public static function getFallbackStats(): array
    {
        // Placeholder for statistics
        // Trong production có thể implement với Redis counters
        return [
            'total_fallbacks' => 0,
            'route_fallbacks' => 0,
            'permission_fallbacks' => 0,
            'emergency_menu_usage' => 0,
            'last_fallback' => null
        ];
    }

    /**
     * Clear fallback cache
     *
     * @return void
     */
    public static function clearFallbackCache(): void
    {
        // Clear fallback cache
        // Note: Trong production nên sử dụng Redis với pattern matching
        Log::info("Menu fallback cache cleared");
    }

    /**
     * Validate fallback configuration
     *
     * @param array $config
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateFallbackConfiguration(array $config): array
    {
        $errors = [];
        $valid = true;
        
        // Kiểm tra emergency routes tồn tại
        $emergencyRoutes = ['home', 'login'];
        foreach ($emergencyRoutes as $route) {
            if (!RouteValidationHelper::routeExists($route)) {
                $errors[] = "Emergency route '{$route}' does not exist";
                $valid = false;
            }
        }
        
        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }
}
