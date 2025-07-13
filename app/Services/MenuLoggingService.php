<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Menu Logging Service
 * 
 * Service để log và track các vấn đề liên quan đến menu:
 * - Missing routes
 * - Permission issues
 * - Fallback usage
 * - Performance metrics
 */
class MenuLoggingService
{
    /**
     * Log channels
     */
    const CHANNEL_MENU = 'menu';
    const CHANNEL_SECURITY = 'security';
    const CHANNEL_PERFORMANCE = 'performance';

    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';

    /**
     * Cache keys
     */
    const CACHE_STATS = 'menu_logging_stats';
    const CACHE_MISSING_ROUTES = 'menu_missing_routes';
    const CACHE_PERMISSION_ISSUES = 'menu_permission_issues';

    /**
     * Log missing route
     *
     * @param string $routeName
     * @param array $context
     * @param User|null $user
     * @return void
     */
    public static function logMissingRoute(string $routeName, array $context = [], ?User $user = null): void
    {
        $logData = [
            'type' => 'missing_route',
            'route' => $routeName,
            'context' => $context,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId()
        ];

        Log::channel(self::CHANNEL_MENU)->warning("Missing route detected", $logData);

        // Update statistics
        self::updateMissingRouteStats($routeName, $user);

        // Check if this is a critical missing route
        if (self::isCriticalRoute($routeName)) {
            self::logCriticalMissingRoute($routeName, $logData);
        }
    }

    /**
     * Log permission denied
     *
     * @param string $permission
     * @param array $menuItem
     * @param User|null $user
     * @return void
     */
    public static function logPermissionDenied(string $permission, array $menuItem, ?User $user = null): void
    {
        $logData = [
            'type' => 'permission_denied',
            'permission' => $permission,
            'menu_item' => $menuItem,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId()
        ];

        Log::channel(self::CHANNEL_SECURITY)->info("Menu permission denied", $logData);

        // Update permission statistics
        self::updatePermissionStats($permission, $user);

        // Check for suspicious activity
        self::checkSuspiciousActivity($user, $permission);
    }

    /**
     * Log menu access attempt
     *
     * @param string $menuKey
     * @param array $menuItem
     * @param User|null $user
     * @param bool $success
     * @return void
     */
    public static function logMenuAccess(string $menuKey, array $menuItem, ?User $user, bool $success): void
    {
        $logData = [
            'type' => 'menu_access',
            'menu_key' => $menuKey,
            'menu_route' => $menuItem['route'] ?? 'unknown',
            'success' => $success,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString()
        ];

        $level = $success ? self::LEVEL_DEBUG : self::LEVEL_WARNING;
        Log::channel(self::CHANNEL_MENU)->log($level, "Menu access attempt", $logData);

        // Update access statistics
        self::updateAccessStats($menuKey, $success, $user);
    }

    /**
     * Log fallback usage
     *
     * @param string $fallbackType
     * @param string $originalRoute
     * @param string $fallbackRoute
     * @param User|null $user
     * @return void
     */
    public static function logFallbackUsage(string $fallbackType, string $originalRoute, string $fallbackRoute, ?User $user = null): void
    {
        $logData = [
            'type' => 'fallback_usage',
            'fallback_type' => $fallbackType,
            'original_route' => $originalRoute,
            'fallback_route' => $fallbackRoute,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString()
        ];

        Log::channel(self::CHANNEL_MENU)->info("Menu fallback used", $logData);

        // Update fallback statistics
        self::updateFallbackStats($fallbackType, $user);
    }

    /**
     * Log menu performance metrics
     *
     * @param array $metrics
     * @param User|null $user
     * @return void
     */
    public static function logPerformanceMetrics(array $metrics, ?User $user = null): void
    {
        $logData = [
            'type' => 'performance_metrics',
            'metrics' => $metrics,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'timestamp' => now()->toISOString()
        ];

        Log::channel(self::CHANNEL_PERFORMANCE)->info("Menu performance metrics", $logData);

        // Check for performance issues
        if (isset($metrics['load_time']) && $metrics['load_time'] > 1000) {
            self::logSlowMenuLoad($metrics, $user);
        }
    }

    /**
     * Log menu configuration error
     *
     * @param string $error
     * @param array $context
     * @return void
     */
    public static function logConfigurationError(string $error, array $context = []): void
    {
        $logData = [
            'type' => 'configuration_error',
            'error' => $error,
            'context' => $context,
            'timestamp' => now()->toISOString()
        ];

        Log::channel(self::CHANNEL_MENU)->error("Menu configuration error", $logData);
    }

    /**
     * Update missing route statistics
     *
     * @param string $routeName
     * @param User|null $user
     * @return void
     */
    private static function updateMissingRouteStats(string $routeName, ?User $user): void
    {
        $cacheKey = self::CACHE_MISSING_ROUTES;
        $stats = Cache::get($cacheKey, []);

        $stats['total'] = ($stats['total'] ?? 0) + 1;
        $stats['routes'][$routeName] = ($stats['routes'][$routeName] ?? 0) + 1;
        $stats['by_role'][$user?->role ?? 'guest'] = ($stats['by_role'][$user?->role ?? 'guest'] ?? 0) + 1;
        $stats['last_updated'] = now()->toISOString();

        Cache::put($cacheKey, $stats, 86400); // 24 hours
    }

    /**
     * Update permission statistics
     *
     * @param string $permission
     * @param User|null $user
     * @return void
     */
    private static function updatePermissionStats(string $permission, ?User $user): void
    {
        $cacheKey = self::CACHE_PERMISSION_ISSUES;
        $stats = Cache::get($cacheKey, []);

        $stats['total'] = ($stats['total'] ?? 0) + 1;
        $stats['permissions'][$permission] = ($stats['permissions'][$permission] ?? 0) + 1;
        $stats['by_role'][$user?->role ?? 'guest'] = ($stats['by_role'][$user?->role ?? 'guest'] ?? 0) + 1;
        $stats['last_updated'] = now()->toISOString();

        Cache::put($cacheKey, $stats, 86400); // 24 hours
    }

    /**
     * Update access statistics
     *
     * @param string $menuKey
     * @param bool $success
     * @param User|null $user
     * @return void
     */
    private static function updateAccessStats(string $menuKey, bool $success, ?User $user): void
    {
        $cacheKey = self::CACHE_STATS;
        $stats = Cache::get($cacheKey, []);

        $stats['total_accesses'] = ($stats['total_accesses'] ?? 0) + 1;
        $stats['successful_accesses'] = ($stats['successful_accesses'] ?? 0) + ($success ? 1 : 0);
        $stats['failed_accesses'] = ($stats['failed_accesses'] ?? 0) + ($success ? 0 : 1);
        $stats['menu_items'][$menuKey] = ($stats['menu_items'][$menuKey] ?? 0) + 1;
        $stats['by_role'][$user?->role ?? 'guest'] = ($stats['by_role'][$user?->role ?? 'guest'] ?? 0) + 1;
        $stats['last_updated'] = now()->toISOString();

        Cache::put($cacheKey, $stats, 86400); // 24 hours
    }

    /**
     * Update fallback statistics
     *
     * @param string $fallbackType
     * @param User|null $user
     * @return void
     */
    private static function updateFallbackStats(string $fallbackType, ?User $user): void
    {
        $cacheKey = 'menu_fallback_stats';
        $stats = Cache::get($cacheKey, []);

        $stats['total'] = ($stats['total'] ?? 0) + 1;
        $stats['types'][$fallbackType] = ($stats['types'][$fallbackType] ?? 0) + 1;
        $stats['by_role'][$user?->role ?? 'guest'] = ($stats['by_role'][$user?->role ?? 'guest'] ?? 0) + 1;
        $stats['last_updated'] = now()->toISOString();

        Cache::put($cacheKey, $stats, 86400); // 24 hours
    }

    /**
     * Check if route is critical
     *
     * @param string $routeName
     * @return bool
     */
    private static function isCriticalRoute(string $routeName): bool
    {
        $criticalRoutes = [
            'home', 'login', 'logout', 'admin.dashboard',
            'user.dashboard', 'forums.index', 'showcases.index'
        ];

        return in_array($routeName, $criticalRoutes);
    }

    /**
     * Log critical missing route
     *
     * @param string $routeName
     * @param array $logData
     * @return void
     */
    private static function logCriticalMissingRoute(string $routeName, array $logData): void
    {
        Log::channel(self::CHANNEL_MENU)->critical("Critical route missing", array_merge($logData, [
            'critical' => true,
            'alert_required' => true
        ]));

        // Có thể gửi alert đến admin hoặc monitoring system
        // self::sendCriticalAlert($routeName, $logData);
    }

    /**
     * Check for suspicious activity
     *
     * @param User|null $user
     * @param string $permission
     * @return void
     */
    private static function checkSuspiciousActivity(?User $user, string $permission): void
    {
        if (!$user) return;

        // Check for repeated permission denials
        $cacheKey = "permission_denials_{$user->id}";
        $denials = Cache::get($cacheKey, 0);
        $denials++;

        Cache::put($cacheKey, $denials, 3600); // 1 hour

        if ($denials > 10) {
            Log::channel(self::CHANNEL_SECURITY)->warning("Suspicious permission activity", [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'permission' => $permission,
                'denial_count' => $denials,
                'timestamp' => now()->toISOString()
            ]);
        }
    }

    /**
     * Log slow menu load
     *
     * @param array $metrics
     * @param User|null $user
     * @return void
     */
    private static function logSlowMenuLoad(array $metrics, ?User $user): void
    {
        Log::channel(self::CHANNEL_PERFORMANCE)->warning("Slow menu load detected", [
            'type' => 'slow_menu_load',
            'metrics' => $metrics,
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get logging statistics
     *
     * @return array
     */
    public static function getLoggingStats(): array
    {
        return [
            'general_stats' => Cache::get(self::CACHE_STATS, []),
            'missing_routes' => Cache::get(self::CACHE_MISSING_ROUTES, []),
            'permission_issues' => Cache::get(self::CACHE_PERMISSION_ISSUES, []),
            'fallback_stats' => Cache::get('menu_fallback_stats', [])
        ];
    }

    /**
     * Get most problematic routes
     *
     * @param int $limit
     * @return array
     */
    public static function getMostProblematicRoutes(int $limit = 10): array
    {
        $missingRoutes = Cache::get(self::CACHE_MISSING_ROUTES, []);
        
        if (!isset($missingRoutes['routes'])) {
            return [];
        }

        arsort($missingRoutes['routes']);
        
        return array_slice($missingRoutes['routes'], 0, $limit, true);
    }

    /**
     * Get permission denial trends
     *
     * @return array
     */
    public static function getPermissionDenialTrends(): array
    {
        $permissionIssues = Cache::get(self::CACHE_PERMISSION_ISSUES, []);
        
        return [
            'total_denials' => $permissionIssues['total'] ?? 0,
            'by_permission' => $permissionIssues['permissions'] ?? [],
            'by_role' => $permissionIssues['by_role'] ?? [],
            'last_updated' => $permissionIssues['last_updated'] ?? null
        ];
    }

    /**
     * Clear logging statistics
     *
     * @return void
     */
    public static function clearLoggingStats(): void
    {
        Cache::forget(self::CACHE_STATS);
        Cache::forget(self::CACHE_MISSING_ROUTES);
        Cache::forget(self::CACHE_PERMISSION_ISSUES);
        Cache::forget('menu_fallback_stats');

        Log::channel(self::CHANNEL_MENU)->info("Menu logging statistics cleared");
    }

    /**
     * Export logs for analysis
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function exportLogs(string $startDate, string $endDate): array
    {
        // Placeholder for log export functionality
        // Trong production có thể implement với log aggregation
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_entries' => 0,
            'export_url' => null
        ];
    }

    /**
     * Generate menu health report
     *
     * @return array
     */
    public static function generateHealthReport(): array
    {
        $stats = self::getLoggingStats();
        
        return [
            'overall_health' => self::calculateOverallHealth($stats),
            'missing_routes_count' => $stats['missing_routes']['total'] ?? 0,
            'permission_issues_count' => $stats['permission_issues']['total'] ?? 0,
            'fallback_usage_count' => $stats['fallback_stats']['total'] ?? 0,
            'success_rate' => self::calculateSuccessRate($stats['general_stats'] ?? []),
            'recommendations' => self::generateRecommendations($stats),
            'generated_at' => now()->toISOString()
        ];
    }

    /**
     * Calculate overall health score
     *
     * @param array $stats
     * @return int
     */
    private static function calculateOverallHealth(array $stats): int
    {
        $score = 100;
        
        // Deduct points for issues
        $missingRoutes = $stats['missing_routes']['total'] ?? 0;
        $permissionIssues = $stats['permission_issues']['total'] ?? 0;
        $fallbackUsage = $stats['fallback_stats']['total'] ?? 0;
        
        $score -= min(30, $missingRoutes * 2);
        $score -= min(20, $permissionIssues);
        $score -= min(15, $fallbackUsage);
        
        return max(0, $score);
    }

    /**
     * Calculate success rate
     *
     * @param array $generalStats
     * @return float
     */
    private static function calculateSuccessRate(array $generalStats): float
    {
        $total = $generalStats['total_accesses'] ?? 0;
        $successful = $generalStats['successful_accesses'] ?? 0;
        
        return $total > 0 ? round(($successful / $total) * 100, 2) : 100.0;
    }

    /**
     * Generate recommendations
     *
     * @param array $stats
     * @return array
     */
    private static function generateRecommendations(array $stats): array
    {
        $recommendations = [];
        
        if (($stats['missing_routes']['total'] ?? 0) > 0) {
            $recommendations[] = 'Fix missing routes to improve menu reliability';
        }
        
        if (($stats['permission_issues']['total'] ?? 0) > 10) {
            $recommendations[] = 'Review permission configuration for menu items';
        }
        
        if (($stats['fallback_stats']['total'] ?? 0) > 5) {
            $recommendations[] = 'Investigate frequent fallback usage';
        }
        
        return $recommendations;
    }
}
