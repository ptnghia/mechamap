<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Menu Permission Helper
 * 
 * Helper class để kiểm tra permissions cho menu items
 * và xác định user có quyền truy cập menu item không
 */
class MenuPermissionHelper
{
    /**
     * Cache key prefix cho permission checking
     */
    const CACHE_PREFIX = 'menu_permission_';
    
    /**
     * Cache TTL - 30 minutes
     */
    const CACHE_TTL = 1800;

    /**
     * Kiểm tra user có quyền truy cập menu item không
     *
     * @param User|null $user
     * @param array $menuItem
     * @return bool
     */
    public static function canAccessMenuItem(?User $user, array $menuItem): bool
    {
        // Nếu không có user và menu item không cần permission -> cho phép
        if (!$user && !isset($menuItem['permission'])) {
            return true;
        }

        // Nếu không có user nhưng cần permission -> từ chối
        if (!$user && isset($menuItem['permission'])) {
            return false;
        }

        // Nếu có user nhưng không cần permission -> cho phép
        if ($user && !isset($menuItem['permission'])) {
            return true;
        }

        // Kiểm tra role restrictions
        if (isset($menuItem['roles'])) {
            if (!self::hasRequiredRole($user, $menuItem['roles'])) {
                return false;
            }
        }

        // Kiểm tra permission cụ thể
        if (isset($menuItem['permission'])) {
            return self::hasPermission($user, $menuItem['permission']);
        }

        return true;
    }

    /**
     * Kiểm tra user có role phù hợp không
     *
     * @param User $user
     * @param array|string $requiredRoles
     * @return bool
     */
    public static function hasRequiredRole(User $user, $requiredRoles): bool
    {
        if (is_string($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }

        // Nếu có '*' trong required roles -> cho phép tất cả authenticated users
        if (in_array('*', $requiredRoles)) {
            return true;
        }

        return in_array($user->role, $requiredRoles);
    }

    /**
     * Kiểm tra user có permission cụ thể không
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(User $user, string $permission): bool
    {
        $cacheKey = self::CACHE_PREFIX . $user->id . '_' . md5($permission);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $permission) {
            return self::checkUserPermission($user, $permission);
        });
    }

    /**
     * Thực hiện kiểm tra permission (không cache)
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    private static function checkUserPermission(User $user, string $permission): bool
    {
        // Permission mapping dựa vào role
        $rolePermissions = [
            'super_admin' => [
                'access-admin-panel', 'manage-system', 'manage-users', 'manage-content', 
                'manage-marketplace', 'view-users', 'create-admin-accounts', 'manage-roles',
                'view-logs', 'manage-backups', 'manage-security'
            ],
            'system_admin' => [
                'access-admin-panel', 'manage-users', 'manage-content', 'manage-marketplace',
                'view-users', 'manage-roles'
            ],
            'content_admin' => [
                'access-admin-panel', 'manage-content', 'view-content', 'moderate-content'
            ],
            'content_moderator' => [
                'access-admin-panel', 'moderate-content', 'view-content', 'approve-content'
            ],
            'marketplace_moderator' => [
                'access-admin-panel', 'moderate-marketplace', 'view-marketplace', 
                'approve-products', 'manage-orders'
            ],
            'community_moderator' => [
                'access-admin-panel', 'moderate-community', 'view-community', 
                'manage-reports', 'ban-users'
            ],
            'verified_partner' => [
                'manage-own-products', 'buy-products', 'sell-products', 'view-marketplace',
                'create-threads', 'comment-threads', 'rate-products', 'follow-users',
                'manage-business-profile'
            ],
            'manufacturer' => [
                'manage-own-products', 'buy-products', 'sell-products', 'view-marketplace',
                'create-threads', 'comment-threads', 'rate-products', 'follow-users',
                'manage-business-profile', 'manage-cad-files', 'manage-technical-files'
            ],
            'supplier' => [
                'manage-own-products', 'buy-products', 'sell-products', 'view-marketplace',
                'create-threads', 'comment-threads', 'rate-products', 'follow-users',
                'manage-business-profile'
            ],
            'brand' => [
                'view-marketplace', 'view-market-insights', 'manage-advertising',
                'create-threads', 'comment-threads', 'follow-users', 'manage-business-profile'
            ],
            'senior_member' => [
                'create-threads', 'comment-threads', 'rate-products', 'follow-users',
                'view-content', 'bookmark-content', 'advanced-search'
            ],
            'member' => [
                'create-threads', 'comment-threads', 'follow-users', 'view-content',
                'bookmark-content'
            ],
            'guest' => [
                'view-content', 'follow-users', 'bookmark-content'
            ]
        ];

        // Kiểm tra permission theo role
        $userPermissions = $rolePermissions[$user->role] ?? [];
        
        if (in_array($permission, $userPermissions)) {
            return true;
        }

        // Kiểm tra special permissions
        return self::checkSpecialPermissions($user, $permission);
    }

    /**
     * Kiểm tra special permissions (business verification, etc.)
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    private static function checkSpecialPermissions(User $user, string $permission): bool
    {
        switch ($permission) {
            case 'access-verified-features':
                return in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']) 
                       && ($user->business_verified ?? false);
                       
            case 'view-cart':
                return in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']) 
                       && ($user->business_verified ?? false);
                       
            case 'create-business-content':
                return in_array($user->role, ['verified_partner', 'manufacturer', 'supplier', 'brand']);
                
            case 'moderate-any-content':
                return in_array($user->role, ['super_admin', 'system_admin', 'content_admin', 'content_moderator']);
                
            case 'access-business-dashboard':
                return in_array($user->role, ['verified_partner', 'manufacturer', 'supplier', 'brand']);
                
            case 'manage-own-content':
                return $user->role !== 'guest';
                
            default:
                return false;
        }
    }

    /**
     * Filter menu items dựa vào permissions của user
     *
     * @param User|null $user
     * @param array $menuItems
     * @return array
     */
    public static function filterMenuItems(?User $user, array $menuItems): array
    {
        $filteredItems = [];

        foreach ($menuItems as $key => $menuItem) {
            if (self::canAccessMenuItem($user, $menuItem)) {
                $filteredItems[$key] = $menuItem;
            } else {
                // Log denied access for debugging
                self::logAccessDenied($user, $menuItem, $key);
            }
        }

        return $filteredItems;
    }

    /**
     * Kiểm tra user có thể truy cập business features không
     *
     * @param User $user
     * @return bool
     */
    public static function canAccessBusinessFeatures(User $user): bool
    {
        return in_array($user->role, ['verified_partner', 'manufacturer', 'supplier', 'brand'])
               && ($user->business_verified ?? false);
    }

    /**
     * Kiểm tra user có thể truy cập admin panel không
     *
     * @param User $user
     * @return bool
     */
    public static function canAccessAdminPanel(User $user): bool
    {
        return self::hasPermission($user, 'access-admin-panel');
    }

    /**
     * Lấy danh sách permissions của user
     *
     * @param User $user
     * @return array
     */
    public static function getUserPermissions(User $user): array
    {
        $cacheKey = self::CACHE_PREFIX . 'user_permissions_' . $user->id;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            $permissions = [];
            
            // Common permissions
            $allPermissions = [
                'access-admin-panel', 'manage-system', 'manage-users', 'manage-content',
                'manage-marketplace', 'view-users', 'create-admin-accounts', 'manage-roles',
                'view-logs', 'manage-backups', 'manage-security', 'moderate-content',
                'view-content', 'approve-content', 'moderate-marketplace', 'view-marketplace',
                'approve-products', 'manage-orders', 'moderate-community', 'view-community',
                'manage-reports', 'ban-users', 'manage-own-products', 'buy-products',
                'sell-products', 'create-threads', 'comment-threads', 'rate-products',
                'follow-users', 'manage-business-profile', 'manage-cad-files',
                'manage-technical-files', 'view-market-insights', 'manage-advertising',
                'bookmark-content', 'advanced-search', 'access-verified-features',
                'view-cart', 'create-business-content', 'moderate-any-content',
                'access-business-dashboard', 'manage-own-content'
            ];
            
            foreach ($allPermissions as $permission) {
                if (self::checkUserPermission($user, $permission)) {
                    $permissions[] = $permission;
                }
            }
            
            return $permissions;
        });
    }

    /**
     * Invalidate permission cache cho user
     *
     * @param User $user
     * @return void
     */
    public static function invalidateUserPermissionCache(User $user): void
    {
        // Clear specific user permission cache
        $userCacheKey = self::CACHE_PREFIX . 'user_permissions_' . $user->id;
        Cache::forget($userCacheKey);
        
        // Clear individual permission caches
        // Note: Trong production nên sử dụng Redis với pattern matching
        Log::info("Permission cache invalidated for user", [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
    }

    /**
     * Log access denied cho debugging
     *
     * @param User|null $user
     * @param array $menuItem
     * @param string $menuKey
     * @return void
     */
    private static function logAccessDenied(?User $user, array $menuItem, string $menuKey): void
    {
        Log::debug("Menu access denied", [
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'menu_key' => $menuKey,
            'menu_route' => $menuItem['route'] ?? 'unknown',
            'required_permission' => $menuItem['permission'] ?? null,
            'required_roles' => $menuItem['roles'] ?? null,
            'timestamp' => now()
        ]);
    }

    /**
     * Kiểm tra menu item có cần business verification không
     *
     * @param array $menuItem
     * @return bool
     */
    public static function requiresBusinessVerification(array $menuItem): bool
    {
        $verificationRequiredPermissions = [
            'buy-products', 'sell-products', 'manage-own-products', 
            'view-cart', 'access-verified-features'
        ];
        
        if (isset($menuItem['permission'])) {
            return in_array($menuItem['permission'], $verificationRequiredPermissions);
        }
        
        return false;
    }

    /**
     * Get permission requirements cho menu item
     *
     * @param array $menuItem
     * @return array
     */
    public static function getPermissionRequirements(array $menuItem): array
    {
        return [
            'permission' => $menuItem['permission'] ?? null,
            'roles' => $menuItem['roles'] ?? null,
            'requires_auth' => isset($menuItem['permission']) || isset($menuItem['roles']),
            'requires_verification' => self::requiresBusinessVerification($menuItem),
            'public_access' => !isset($menuItem['permission']) && !isset($menuItem['roles'])
        ];
    }

    /**
     * Validate permission configuration
     *
     * @param array $menuConfig
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePermissionConfiguration(array $menuConfig): array
    {
        $errors = [];
        $valid = true;

        if (!isset($menuConfig['menu_items'])) {
            return [
                'valid' => false,
                'errors' => ['Menu configuration missing menu_items']
            ];
        }

        foreach ($menuConfig['menu_items'] as $key => $menuItem) {
            // Kiểm tra permission format
            if (isset($menuItem['permission']) && !is_string($menuItem['permission'])) {
                $errors[] = "Menu item '{$key}': Permission must be string";
                $valid = false;
            }

            // Kiểm tra roles format
            if (isset($menuItem['roles']) && !is_array($menuItem['roles']) && !is_string($menuItem['roles'])) {
                $errors[] = "Menu item '{$key}': Roles must be array or string";
                $valid = false;
            }

            // Kiểm tra conflicting permissions
            if (isset($menuItem['permission']) && isset($menuItem['roles'])) {
                if ($menuItem['permission'] === 'access-admin-panel' && 
                    !empty(array_intersect($menuItem['roles'], ['member', 'guest', 'verified_partner', 'manufacturer', 'supplier', 'brand']))) {
                    $errors[] = "Menu item '{$key}': Admin permission with non-admin roles";
                    $valid = false;
                }
            }
        }

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }
}
