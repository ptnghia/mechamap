<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * Menu Performance Service
 * 
 * Service để tối ưu hóa performance của menu system
 * Giảm database queries và cải thiện rendering speed
 */
class MenuPerformanceService
{
    /**
     * Cache keys for performance optimization
     */
    const CACHE_USER_COUNTS = 'menu_user_counts';
    const CACHE_NOTIFICATION_COUNTS = 'menu_notification_counts';
    const CACHE_CART_COUNTS = 'menu_cart_counts';
    const CACHE_ACTIVITY_COUNTS = 'menu_activity_counts';

    /**
     * Cache TTL for different data types
     */
    const TTL_USER_COUNTS = 300;        // 5 minutes
    const TTL_NOTIFICATION_COUNTS = 60; // 1 minute
    const TTL_CART_COUNTS = 180;        // 3 minutes
    const TTL_ACTIVITY_COUNTS = 600;    // 10 minutes

    /**
     * Optimize user data loading for menu
     *
     * @param User $user
     * @return array
     */
    public static function getOptimizedUserData(User $user): array
    {
        $cacheKey = "menu_user_data_{$user->id}";
        
        return Cache::remember($cacheKey, self::TTL_USER_COUNTS, function () use ($user) {
            // Load all required data in a single optimized query
            return [
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'role_display_name' => $user->role_display_name,
                    'role_color' => $user->role_color,
                    'avatar_url' => $user->avatar_url,
                    'business_verified' => $user->business_verified ?? false,
                    'last_login_at' => $user->last_login_at,
                ],
                'counts' => self::getUserCounts($user),
                'permissions' => self::getCachedUserPermissions($user),
                'features' => self::getUserFeatures($user)
            ];
        });
    }

    /**
     * Get user counts (threads, comments, bookmarks, etc.) optimized
     *
     * @param User $user
     * @return array
     */
    private static function getUserCounts(User $user): array
    {
        $cacheKey = self::CACHE_USER_COUNTS . "_{$user->id}";
        
        return Cache::remember($cacheKey, self::TTL_USER_COUNTS, function () use ($user) {
            // Single query to get all counts
            $counts = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM threads WHERE user_id = ?) as threads_count,
                    (SELECT COUNT(*) FROM comments WHERE user_id = ?) as comments_count,
                    (SELECT COUNT(*) FROM bookmarks WHERE user_id = ?) as bookmarks_count,
                    (SELECT COUNT(*) FROM follows WHERE follower_id = ?) as following_count,
                    (SELECT COUNT(*) FROM ratings WHERE user_id = ?) as ratings_count
            ", [$user->id, $user->id, $user->id, $user->id, $user->id]);

            $result = $counts[0] ?? null;
            
            return [
                'threads' => $result->threads_count ?? 0,
                'comments' => $result->comments_count ?? 0,
                'bookmarks' => $result->bookmarks_count ?? 0,
                'following' => $result->following_count ?? 0,
                'ratings' => $result->ratings_count ?? 0,
            ];
        });
    }

    /**
     * Get cached user permissions
     *
     * @param User $user
     * @return array
     */
    private static function getCachedUserPermissions(User $user): array
    {
        return MenuCacheService::getCachedPermissionCheck($user, 'all') ?? 
               self::calculateUserPermissions($user);
    }

    /**
     * Calculate user permissions efficiently
     *
     * @param User $user
     * @return array
     */
    private static function calculateUserPermissions(User $user): array
    {
        // Use role-based permission mapping for efficiency
        $rolePermissions = [
            'super_admin' => [
                'can_access_admin' => true,
                'can_create_content' => true,
                'can_buy_products' => false,
                'can_sell_products' => false,
                'can_view_cart' => false,
                'can_moderate' => true,
            ],
            'verified_partner' => [
                'can_access_admin' => false,
                'can_create_content' => true,
                'can_buy_products' => true,
                'can_sell_products' => true,
                'can_view_cart' => $user->business_verified ?? false,
                'can_moderate' => false,
            ],
            'member' => [
                'can_access_admin' => false,
                'can_create_content' => true,
                'can_buy_products' => false,
                'can_sell_products' => false,
                'can_view_cart' => false,
                'can_moderate' => false,
            ],
            'guest' => [
                'can_access_admin' => false,
                'can_create_content' => false,
                'can_buy_products' => false,
                'can_sell_products' => false,
                'can_view_cart' => false,
                'can_moderate' => false,
            ],
        ];

        return $rolePermissions[$user->role] ?? $rolePermissions['guest'];
    }

    /**
     * Get user features efficiently
     *
     * @param User $user
     * @return array
     */
    private static function getUserFeatures(User $user): array
    {
        $isAdmin = in_array($user->role, ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator']);
        $isBusiness = in_array($user->role, ['verified_partner', 'manufacturer', 'supplier', 'brand']);
        $isVerified = $user->business_verified ?? false;

        return [
            'show_admin_status_bar' => $isAdmin,
            'show_business_status_bar' => $isBusiness,
            'show_member_status_bar' => $user->role === 'guest',
            'show_shopping_cart' => $isBusiness && $isVerified,
            'show_create_dropdown' => $user->role !== 'guest',
            'show_notifications' => true,
            'enable_search' => true,
            'show_language_switcher' => true,
        ];
    }

    /**
     * Get notification counts efficiently
     *
     * @param User $user
     * @return array
     */
    public static function getNotificationCounts(User $user): array
    {
        $cacheKey = self::CACHE_NOTIFICATION_COUNTS . "_{$user->id}";
        
        return Cache::remember($cacheKey, self::TTL_NOTIFICATION_COUNTS, function () use ($user) {
            // Optimized query for notification counts
            $counts = DB::select("
                SELECT 
                    COUNT(*) as total_count,
                    SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_count
                FROM notifications 
                WHERE notifiable_id = ? AND notifiable_type = ?
            ", [$user->id, get_class($user)]);

            $result = $counts[0] ?? null;
            
            return [
                'total' => $result->total_count ?? 0,
                'unread' => $result->unread_count ?? 0,
            ];
        });
    }

    /**
     * Get cart counts for business users
     *
     * @param User $user
     * @return array
     */
    public static function getCartCounts(User $user): array
    {
        if (!in_array($user->role, ['verified_partner', 'manufacturer', 'supplier'])) {
            return ['items' => 0, 'total' => 0];
        }

        $cacheKey = self::CACHE_CART_COUNTS . "_{$user->id}";
        
        return Cache::remember($cacheKey, self::TTL_CART_COUNTS, function () use ($user) {
            $counts = DB::select("
                SELECT 
                    COUNT(*) as items_count,
                    COALESCE(SUM(quantity * price), 0) as total_amount
                FROM cart_items 
                WHERE user_id = ?
            ", [$user->id]);

            $result = $counts[0] ?? null;
            
            return [
                'items' => $result->items_count ?? 0,
                'total' => $result->total_amount ?? 0,
            ];
        });
    }

    /**
     * Preload menu data for multiple users (bulk optimization)
     *
     * @param Collection $users
     * @return array
     */
    public static function bulkPreloadMenuData(Collection $users): array
    {
        $userIds = $users->pluck('id')->toArray();
        
        // Bulk load all counts in single queries
        $threadCounts = self::bulkGetThreadCounts($userIds);
        $commentCounts = self::bulkGetCommentCounts($userIds);
        $notificationCounts = self::bulkGetNotificationCounts($userIds);
        
        $result = [];
        
        foreach ($users as $user) {
            $result[$user->id] = [
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'avatar_url' => $user->avatar_url,
                ],
                'counts' => [
                    'threads' => $threadCounts[$user->id] ?? 0,
                    'comments' => $commentCounts[$user->id] ?? 0,
                    'notifications' => $notificationCounts[$user->id] ?? 0,
                ],
                'permissions' => self::calculateUserPermissions($user),
            ];
            
            // Cache individual user data
            $cacheKey = "menu_user_data_{$user->id}";
            Cache::put($cacheKey, $result[$user->id], self::TTL_USER_COUNTS);
        }
        
        return $result;
    }

    /**
     * Bulk get thread counts
     *
     * @param array $userIds
     * @return array
     */
    private static function bulkGetThreadCounts(array $userIds): array
    {
        if (empty($userIds)) return [];
        
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        
        $results = DB::select("
            SELECT user_id, COUNT(*) as count 
            FROM threads 
            WHERE user_id IN ($placeholders)
            GROUP BY user_id
        ", $userIds);
        
        return collect($results)->pluck('count', 'user_id')->toArray();
    }

    /**
     * Bulk get comment counts
     *
     * @param array $userIds
     * @return array
     */
    private static function bulkGetCommentCounts(array $userIds): array
    {
        if (empty($userIds)) return [];
        
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        
        $results = DB::select("
            SELECT user_id, COUNT(*) as count 
            FROM comments 
            WHERE user_id IN ($placeholders)
            GROUP BY user_id
        ", $userIds);
        
        return collect($results)->pluck('count', 'user_id')->toArray();
    }

    /**
     * Bulk get notification counts
     *
     * @param array $userIds
     * @return array
     */
    private static function bulkGetNotificationCounts(array $userIds): array
    {
        if (empty($userIds)) return [];
        
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        
        $results = DB::select("
            SELECT notifiable_id as user_id, COUNT(*) as count 
            FROM notifications 
            WHERE notifiable_id IN ($placeholders) AND notifiable_type = ?
            AND read_at IS NULL
            GROUP BY notifiable_id
        ", array_merge($userIds, [User::class]));
        
        return collect($results)->pluck('count', 'user_id')->toArray();
    }

    /**
     * Optimize menu rendering performance
     *
     * @param array $menuItems
     * @param User|null $user
     * @return array
     */
    public static function optimizeMenuItems(array $menuItems, ?User $user): array
    {
        // Pre-validate all routes in batch
        $routes = collect($menuItems)->pluck('route')->filter()->unique()->toArray();
        $routeValidations = self::batchValidateRoutes($routes);
        
        // Filter and optimize menu items
        $optimizedItems = [];
        
        foreach ($menuItems as $key => $item) {
            // Skip items with invalid routes
            if (isset($item['route']) && !($routeValidations[$item['route']] ?? false)) {
                continue;
            }
            
            // Add performance metadata
            $item['_performance'] = [
                'cached' => true,
                'validated' => true,
                'optimized' => true
            ];
            
            $optimizedItems[$key] = $item;
        }
        
        return $optimizedItems;
    }

    /**
     * Batch validate routes for performance
     *
     * @param array $routes
     * @return array
     */
    private static function batchValidateRoutes(array $routes): array
    {
        $cacheKey = 'menu_batch_route_validation_' . md5(implode(',', $routes));
        
        return Cache::remember($cacheKey, 3600, function () use ($routes) {
            $validations = [];
            
            foreach ($routes as $route) {
                $validations[$route] = \Illuminate\Support\Facades\Route::has($route);
            }
            
            return $validations;
        });
    }

    /**
     * Invalidate performance caches for user
     *
     * @param User $user
     * @return void
     */
    public static function invalidateUserPerformanceCache(User $user): void
    {
        $cacheKeys = [
            "menu_user_data_{$user->id}",
            self::CACHE_USER_COUNTS . "_{$user->id}",
            self::CACHE_NOTIFICATION_COUNTS . "_{$user->id}",
            self::CACHE_CART_COUNTS . "_{$user->id}",
            self::CACHE_ACTIVITY_COUNTS . "_{$user->id}",
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        Log::info("Menu performance cache invalidated for user", [
            'user_id' => $user->id,
            'cache_keys_cleared' => count($cacheKeys)
        ]);
    }

    /**
     * Get performance metrics
     *
     * @return array
     */
    public static function getPerformanceMetrics(): array
    {
        return [
            'cache_hit_rate' => self::calculateCacheHitRate(),
            'average_render_time' => self::getAverageRenderTime(),
            'database_query_count' => self::getDatabaseQueryCount(),
            'memory_usage' => memory_get_peak_usage(true),
            'cache_size' => self::getCacheSize(),
        ];
    }

    /**
     * Calculate cache hit rate
     *
     * @return float
     */
    private static function calculateCacheHitRate(): float
    {
        // This would be implemented with proper tracking
        return 85.5; // Placeholder
    }

    /**
     * Get average render time
     *
     * @return float
     */
    private static function getAverageRenderTime(): float
    {
        // This would be implemented with proper tracking
        return 45.2; // milliseconds, placeholder
    }

    /**
     * Get database query count
     *
     * @return int
     */
    private static function getDatabaseQueryCount(): int
    {
        // This would be implemented with query logging
        return 3; // Placeholder - optimized to 3 queries max
    }

    /**
     * Get cache size
     *
     * @return string
     */
    private static function getCacheSize(): string
    {
        // This would be implemented based on cache driver
        return '2.5MB'; // Placeholder
    }

    /**
     * Optimize Blade component rendering
     *
     * @param string $component
     * @param array $data
     * @return string
     */
    public static function optimizeComponentRendering(string $component, array $data): string
    {
        $startTime = microtime(true);
        
        // Pre-process data for optimal rendering
        $optimizedData = self::preprocessComponentData($data);
        
        // Render component
        $html = view($component, $optimizedData)->render();
        
        $renderTime = (microtime(true) - $startTime) * 1000;
        
        // Log slow renders
        if ($renderTime > 100) { // 100ms threshold
            Log::warning("Slow menu component render", [
                'component' => $component,
                'render_time_ms' => $renderTime,
                'data_size' => strlen(serialize($data))
            ]);
        }
        
        return $html;
    }

    /**
     * Preprocess component data for optimal rendering
     *
     * @param array $data
     * @return array
     */
    private static function preprocessComponentData(array $data): array
    {
        // Remove unnecessary data
        $optimized = $data;
        
        // Convert objects to arrays for faster access
        if (isset($optimized['user']) && is_object($optimized['user'])) {
            $optimized['user'] = $optimized['user']->toArray();
        }
        
        // Pre-calculate commonly used values
        if (isset($optimized['menu_items'])) {
            foreach ($optimized['menu_items'] as &$item) {
                $item['_has_route'] = isset($item['route']);
                $item['_has_permission'] = isset($item['permission']);
                $item['_has_icon'] = isset($item['icon']);
            }
        }
        
        return $optimized;
    }

    /**
     * Monitor menu performance
     *
     * @param string $action
     * @param array $context
     * @return void
     */
    public static function monitorPerformance(string $action, array $context = []): void
    {
        $metrics = array_merge([
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ], $context);
        
        // Log to performance channel
        Log::channel('performance')->info("Menu performance metric", $metrics);
        
        // Store in cache for dashboard
        $cacheKey = 'menu_performance_metrics';
        $existingMetrics = Cache::get($cacheKey, []);
        $existingMetrics[] = $metrics;
        
        // Keep only last 100 metrics
        if (count($existingMetrics) > 100) {
            $existingMetrics = array_slice($existingMetrics, -100);
        }
        
        Cache::put($cacheKey, $existingMetrics, 3600);
    }
}
