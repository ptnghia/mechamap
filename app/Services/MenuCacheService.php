<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Menu Cache Service
 * 
 * Service để quản lý caching cho menu system
 * Tối ưu hóa performance bằng cách cache menu configurations
 */
class MenuCacheService
{
    /**
     * Cache prefixes
     */
    const PREFIX_USER_MENU = 'menu_user_';
    const PREFIX_ROLE_MENU = 'menu_role_';
    const PREFIX_GLOBAL_MENU = 'menu_global_';
    const PREFIX_ROUTE_VALIDATION = 'menu_route_';
    const PREFIX_PERMISSION = 'menu_permission_';

    /**
     * Cache TTL values (seconds)
     */
    const TTL_USER_MENU = 3600;      // 1 hour
    const TTL_ROLE_MENU = 7200;      // 2 hours
    const TTL_GLOBAL_MENU = 14400;   // 4 hours
    const TTL_ROUTE_VALIDATION = 3600; // 1 hour
    const TTL_PERMISSION = 1800;     // 30 minutes

    /**
     * Cache tags for invalidation
     */
    const TAG_MENU = 'menu';
    const TAG_USER = 'user';
    const TAG_ROLE = 'role';
    const TAG_PERMISSION = 'permission';

    /**
     * Get cached menu configuration for user
     *
     * @param User $user
     * @return array|null
     */
    public static function getUserMenuCache(User $user): ?array
    {
        $cacheKey = self::PREFIX_USER_MENU . $user->id;
        
        return Cache::tags([self::TAG_MENU, self::TAG_USER])
                   ->remember($cacheKey, self::TTL_USER_MENU, function () use ($user) {
                       return null; // Will be populated by MenuService
                   });
    }

    /**
     * Set cached menu configuration for user
     *
     * @param User $user
     * @param array $menuConfig
     * @return bool
     */
    public static function setUserMenuCache(User $user, array $menuConfig): bool
    {
        $cacheKey = self::PREFIX_USER_MENU . $user->id;
        
        // Add cache metadata
        $menuConfig['_cache'] = [
            'cached_at' => now()->toISOString(),
            'user_id' => $user->id,
            'user_role' => $user->role,
            'ttl' => self::TTL_USER_MENU
        ];
        
        return Cache::tags([self::TAG_MENU, self::TAG_USER])
                   ->put($cacheKey, $menuConfig, self::TTL_USER_MENU);
    }

    /**
     * Get cached menu configuration for role
     *
     * @param string $role
     * @return array|null
     */
    public static function getRoleMenuCache(string $role): ?array
    {
        $cacheKey = self::PREFIX_ROLE_MENU . $role;
        
        return Cache::tags([self::TAG_MENU, self::TAG_ROLE])
                   ->get($cacheKey);
    }

    /**
     * Set cached menu configuration for role
     *
     * @param string $role
     * @param array $menuConfig
     * @return bool
     */
    public static function setRoleMenuCache(string $role, array $menuConfig): bool
    {
        $cacheKey = self::PREFIX_ROLE_MENU . $role;
        
        // Add cache metadata
        $menuConfig['_cache'] = [
            'cached_at' => now()->toISOString(),
            'role' => $role,
            'ttl' => self::TTL_ROLE_MENU
        ];
        
        return Cache::tags([self::TAG_MENU, self::TAG_ROLE])
                   ->put($cacheKey, $menuConfig, self::TTL_ROLE_MENU);
    }

    /**
     * Get cached global menu configuration
     *
     * @return array|null
     */
    public static function getGlobalMenuCache(): ?array
    {
        $cacheKey = self::PREFIX_GLOBAL_MENU . 'config';
        
        return Cache::tags([self::TAG_MENU])
                   ->get($cacheKey);
    }

    /**
     * Set cached global menu configuration
     *
     * @param array $menuConfig
     * @return bool
     */
    public static function setGlobalMenuCache(array $menuConfig): bool
    {
        $cacheKey = self::PREFIX_GLOBAL_MENU . 'config';
        
        // Add cache metadata
        $menuConfig['_cache'] = [
            'cached_at' => now()->toISOString(),
            'type' => 'global',
            'ttl' => self::TTL_GLOBAL_MENU
        ];
        
        return Cache::tags([self::TAG_MENU])
                   ->put($cacheKey, $menuConfig, self::TTL_GLOBAL_MENU);
    }

    /**
     * Cache route validation result
     *
     * @param string $routeName
     * @param bool $exists
     * @return bool
     */
    public static function cacheRouteValidation(string $routeName, bool $exists): bool
    {
        $cacheKey = self::PREFIX_ROUTE_VALIDATION . md5($routeName);
        
        return Cache::put($cacheKey, $exists, self::TTL_ROUTE_VALIDATION);
    }

    /**
     * Get cached route validation result
     *
     * @param string $routeName
     * @return bool|null
     */
    public static function getCachedRouteValidation(string $routeName): ?bool
    {
        $cacheKey = self::PREFIX_ROUTE_VALIDATION . md5($routeName);
        
        return Cache::get($cacheKey);
    }

    /**
     * Cache permission check result
     *
     * @param User $user
     * @param string $permission
     * @param bool $hasPermission
     * @return bool
     */
    public static function cachePermissionCheck(User $user, string $permission, bool $hasPermission): bool
    {
        $cacheKey = self::PREFIX_PERMISSION . $user->id . '_' . md5($permission);
        
        return Cache::tags([self::TAG_PERMISSION, self::TAG_USER])
                   ->put($cacheKey, $hasPermission, self::TTL_PERMISSION);
    }

    /**
     * Get cached permission check result
     *
     * @param User $user
     * @param string $permission
     * @return bool|null
     */
    public static function getCachedPermissionCheck(User $user, string $permission): ?bool
    {
        $cacheKey = self::PREFIX_PERMISSION . $user->id . '_' . md5($permission);
        
        return Cache::tags([self::TAG_PERMISSION, self::TAG_USER])
                   ->get($cacheKey);
    }

    /**
     * Invalidate user menu cache
     *
     * @param User $user
     * @return bool
     */
    public static function invalidateUserMenuCache(User $user): bool
    {
        $cacheKey = self::PREFIX_USER_MENU . $user->id;
        
        $result = Cache::tags([self::TAG_MENU, self::TAG_USER])
                      ->forget($cacheKey);
        
        self::logCacheInvalidation('user_menu', $user->id, $user->role);
        
        return $result;
    }

    /**
     * Invalidate role menu cache
     *
     * @param string $role
     * @return bool
     */
    public static function invalidateRoleMenuCache(string $role): bool
    {
        $cacheKey = self::PREFIX_ROLE_MENU . $role;
        
        $result = Cache::tags([self::TAG_MENU, self::TAG_ROLE])
                      ->forget($cacheKey);
        
        self::logCacheInvalidation('role_menu', null, $role);
        
        return $result;
    }

    /**
     * Invalidate all menu cache
     *
     * @return bool
     */
    public static function invalidateAllMenuCache(): bool
    {
        $result = Cache::tags([self::TAG_MENU])->flush();
        
        self::logCacheInvalidation('all_menu', null, null);
        
        return $result;
    }

    /**
     * Invalidate permission cache for user
     *
     * @param User $user
     * @return bool
     */
    public static function invalidateUserPermissionCache(User $user): bool
    {
        // Invalidate all permission cache for this user
        $result = Cache::tags([self::TAG_PERMISSION, self::TAG_USER])
                      ->flush();
        
        self::logCacheInvalidation('user_permission', $user->id, $user->role);
        
        return $result;
    }

    /**
     * Warm up cache for specific user
     *
     * @param User $user
     * @return bool
     */
    public static function warmUpUserCache(User $user): bool
    {
        try {
            // Pre-load menu configuration
            $menuConfig = MenuService::getMenuConfiguration($user);
            self::setUserMenuCache($user, $menuConfig);
            
            // Pre-load common permissions
            $commonPermissions = self::getCommonPermissions();
            foreach ($commonPermissions as $permission) {
                $hasPermission = $user->hasPermission($permission);
                self::cachePermissionCheck($user, $permission, $hasPermission);
            }
            
            self::logCacheWarmup('user', $user->id, $user->role);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to warm up user cache", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Warm up cache for specific role
     *
     * @param string $role
     * @return bool
     */
    public static function warmUpRoleCache(string $role): bool
    {
        try {
            // Create mock user for role
            $mockUser = new User(['role' => $role]);
            $menuConfig = MenuService::getMenuConfiguration($mockUser);
            
            self::setRoleMenuCache($role, $menuConfig);
            
            self::logCacheWarmup('role', null, $role);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to warm up role cache", [
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Warm up cache for all roles
     *
     * @return array
     */
    public static function warmUpAllRoleCache(): array
    {
        $roles = [
            'super_admin', 'system_admin', 'content_admin',
            'content_moderator', 'marketplace_moderator', 'community_moderator',
            'verified_partner', 'manufacturer', 'supplier', 'brand',
            'senior_member', 'member', 'guest'
        ];
        
        $results = [];
        
        foreach ($roles as $role) {
            $results[$role] = self::warmUpRoleCache($role);
        }
        
        return $results;
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public static function getCacheStats(): array
    {
        $stats = [
            'user_menu_cache_count' => 0,
            'role_menu_cache_count' => 0,
            'route_validation_cache_count' => 0,
            'permission_cache_count' => 0,
            'total_cache_size' => 0,
            'hit_rate' => 0,
            'miss_rate' => 0
        ];
        
        // Note: Actual implementation would depend on cache driver
        // Redis implementation would be different from file cache
        
        try {
            if (config('cache.default') === 'redis') {
                $stats = self::getRedisStats();
            } else {
                $stats = self::getGenericStats();
            }
        } catch (\Exception $e) {
            Log::error("Failed to get cache stats", ['error' => $e->getMessage()]);
        }
        
        return $stats;
    }

    /**
     * Get Redis-specific cache statistics
     *
     * @return array
     */
    private static function getRedisStats(): array
    {
        try {
            $redis = Redis::connection();
            
            $userMenuKeys = $redis->keys(self::PREFIX_USER_MENU . '*');
            $roleMenuKeys = $redis->keys(self::PREFIX_ROLE_MENU . '*');
            $routeKeys = $redis->keys(self::PREFIX_ROUTE_VALIDATION . '*');
            $permissionKeys = $redis->keys(self::PREFIX_PERMISSION . '*');
            
            return [
                'user_menu_cache_count' => count($userMenuKeys),
                'role_menu_cache_count' => count($roleMenuKeys),
                'route_validation_cache_count' => count($routeKeys),
                'permission_cache_count' => count($permissionKeys),
                'total_cache_size' => $redis->dbsize(),
                'memory_usage' => $redis->info('memory')['used_memory_human'] ?? 'unknown',
                'hit_rate' => 0, // Would need to track this separately
                'miss_rate' => 0
            ];
        } catch (\Exception $e) {
            return self::getGenericStats();
        }
    }

    /**
     * Get generic cache statistics
     *
     * @return array
     */
    private static function getGenericStats(): array
    {
        return [
            'user_menu_cache_count' => 'unknown',
            'role_menu_cache_count' => 'unknown',
            'route_validation_cache_count' => 'unknown',
            'permission_cache_count' => 'unknown',
            'total_cache_size' => 'unknown',
            'hit_rate' => 'unknown',
            'miss_rate' => 'unknown'
        ];
    }

    /**
     * Clear expired cache entries
     *
     * @return int Number of cleared entries
     */
    public static function clearExpiredCache(): int
    {
        $cleared = 0;
        
        try {
            // This would be implemented differently based on cache driver
            // For Redis, we could use TTL to find expired keys
            // For file cache, we could check file modification times
            
            self::logCacheCleanup($cleared);
        } catch (\Exception $e) {
            Log::error("Failed to clear expired cache", ['error' => $e->getMessage()]);
        }
        
        return $cleared;
    }

    /**
     * Get common permissions for cache warming
     *
     * @return array
     */
    private static function getCommonPermissions(): array
    {
        return [
            'access-admin-panel',
            'manage-content',
            'view-content',
            'create-threads',
            'comment-threads',
            'buy-products',
            'sell-products',
            'view-marketplace',
            'manage-own-products',
            'follow-users',
            'rate-products'
        ];
    }

    /**
     * Log cache invalidation
     *
     * @param string $type
     * @param int|null $userId
     * @param string|null $role
     * @return void
     */
    private static function logCacheInvalidation(string $type, ?int $userId, ?string $role): void
    {
        Log::info("Menu cache invalidated", [
            'type' => $type,
            'user_id' => $userId,
            'role' => $role,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log cache warmup
     *
     * @param string $type
     * @param int|null $userId
     * @param string|null $role
     * @return void
     */
    private static function logCacheWarmup(string $type, ?int $userId, ?string $role): void
    {
        Log::info("Menu cache warmed up", [
            'type' => $type,
            'user_id' => $userId,
            'role' => $role,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log cache cleanup
     *
     * @param int $clearedCount
     * @return void
     */
    private static function logCacheCleanup(int $clearedCount): void
    {
        Log::info("Menu cache cleanup completed", [
            'cleared_entries' => $clearedCount,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Check cache health
     *
     * @return array
     */
    public static function checkCacheHealth(): array
    {
        $health = [
            'status' => 'healthy',
            'issues' => [],
            'recommendations' => []
        ];
        
        try {
            $stats = self::getCacheStats();
            
            // Check for potential issues
            if (isset($stats['hit_rate']) && is_numeric($stats['hit_rate']) && $stats['hit_rate'] < 70) {
                $health['issues'][] = 'Low cache hit rate: ' . $stats['hit_rate'] . '%';
                $health['recommendations'][] = 'Consider warming up cache more frequently';
            }
            
            if (isset($stats['total_cache_size']) && is_numeric($stats['total_cache_size']) && $stats['total_cache_size'] > 1000000) {
                $health['issues'][] = 'Large cache size detected';
                $health['recommendations'][] = 'Consider implementing cache cleanup strategy';
            }
            
            if (!empty($health['issues'])) {
                $health['status'] = 'warning';
            }
            
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['issues'][] = 'Failed to check cache health: ' . $e->getMessage();
        }
        
        return $health;
    }
}
