<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Route Validation Helper
 * 
 * Helper class để kiểm tra route tồn tại và validate menu items
 * trước khi render trong menu components
 */
class RouteValidationHelper
{
    /**
     * Cache key prefix cho route validation
     */
    const CACHE_PREFIX = 'route_validation_';
    
    /**
     * Cache TTL - 1 hour
     */
    const CACHE_TTL = 3600;

    /**
     * Kiểm tra route có tồn tại không
     *
     * @param string $routeName
     * @return bool
     */
    public static function routeExists(string $routeName): bool
    {
        $cacheKey = self::CACHE_PREFIX . md5($routeName);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($routeName) {
            return Route::has($routeName);
        });
    }

    /**
     * Kiểm tra multiple routes cùng lúc
     *
     * @param array $routeNames
     * @return array ['route_name' => bool]
     */
    public static function validateMultipleRoutes(array $routeNames): array
    {
        $results = [];
        
        foreach ($routeNames as $routeName) {
            $results[$routeName] = self::routeExists($routeName);
        }
        
        return $results;
    }

    /**
     * Validate menu item với route và parameters
     *
     * @param array $menuItem
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateMenuItem(array $menuItem): array
    {
        $errors = [];
        $valid = true;

        // Kiểm tra required fields
        if (!isset($menuItem['route'])) {
            $errors[] = 'Menu item missing route';
            $valid = false;
        }

        if (!isset($menuItem['title'])) {
            $errors[] = 'Menu item missing title';
            $valid = false;
        }

        // Kiểm tra route tồn tại
        if (isset($menuItem['route']) && !self::routeExists($menuItem['route'])) {
            $errors[] = "Route '{$menuItem['route']}' does not exist";
            $valid = false;
            
            // Log missing route
            self::logMissingRoute($menuItem['route'], $menuItem);
        }

        // Kiểm tra route parameters nếu có
        if (isset($menuItem['params']) && isset($menuItem['route'])) {
            $validationResult = self::validateRouteParameters($menuItem['route'], $menuItem['params']);
            if (!$validationResult['valid']) {
                $errors = array_merge($errors, $validationResult['errors']);
                $valid = false;
            }
        }

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }

    /**
     * Validate route parameters
     *
     * @param string $routeName
     * @param mixed $params
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateRouteParameters(string $routeName, $params): array
    {
        $errors = [];
        $valid = true;

        try {
            if (!self::routeExists($routeName)) {
                return [
                    'valid' => false,
                    'errors' => ["Route '{$routeName}' does not exist"]
                ];
            }

            $route = Route::getRoutes()->getByName($routeName);
            if (!$route) {
                return [
                    'valid' => false,
                    'errors' => ["Cannot get route instance for '{$routeName}'"]
                ];
            }

            // Lấy required parameters từ route
            $requiredParams = $route->parameterNames();
            
            if (empty($requiredParams)) {
                // Route không cần parameters
                if (!empty($params)) {
                    $errors[] = "Route '{$routeName}' does not accept parameters but parameters provided";
                    $valid = false;
                }
            } else {
                // Route cần parameters
                if (empty($params)) {
                    $errors[] = "Route '{$routeName}' requires parameters: " . implode(', ', $requiredParams);
                    $valid = false;
                } else {
                    // Kiểm tra số lượng parameters
                    $paramCount = is_array($params) ? count($params) : 1;
                    $requiredCount = count($requiredParams);
                    
                    if ($paramCount < $requiredCount) {
                        $errors[] = "Route '{$routeName}' requires {$requiredCount} parameters, {$paramCount} provided";
                        $valid = false;
                    }
                }
            }

        } catch (\Exception $e) {
            $errors[] = "Error validating route parameters: " . $e->getMessage();
            $valid = false;
        }

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }

    /**
     * Validate toàn bộ menu configuration
     *
     * @param array $menuConfig
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public static function validateMenuConfiguration(array $menuConfig): array
    {
        $errors = [];
        $warnings = [];
        $valid = true;

        if (!isset($menuConfig['menu_items']) || !is_array($menuConfig['menu_items'])) {
            return [
                'valid' => false,
                'errors' => ['Menu configuration missing menu_items array'],
                'warnings' => []
            ];
        }

        foreach ($menuConfig['menu_items'] as $key => $menuItem) {
            $itemValidation = self::validateMenuItem($menuItem);
            
            if (!$itemValidation['valid']) {
                $valid = false;
                foreach ($itemValidation['errors'] as $error) {
                    $errors[] = "Menu item '{$key}': {$error}";
                }
            }

            // Kiểm tra warnings
            if (isset($menuItem['icon']) && !str_starts_with($menuItem['icon'], 'fas ') && !str_starts_with($menuItem['icon'], 'far ')) {
                $warnings[] = "Menu item '{$key}': Icon '{$menuItem['icon']}' may not be a valid FontAwesome class";
            }

            if (isset($menuItem['permission']) && empty($menuItem['permission'])) {
                $warnings[] = "Menu item '{$key}': Empty permission string";
            }
        }

        return [
            'valid' => $valid,
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Lấy danh sách routes bị missing từ menu configuration
     *
     * @param array $menuConfig
     * @return array
     */
    public static function getMissingRoutes(array $menuConfig): array
    {
        $missingRoutes = [];

        if (!isset($menuConfig['menu_items'])) {
            return $missingRoutes;
        }

        foreach ($menuConfig['menu_items'] as $key => $menuItem) {
            if (isset($menuItem['route']) && !self::routeExists($menuItem['route'])) {
                $missingRoutes[] = [
                    'menu_key' => $key,
                    'route' => $menuItem['route'],
                    'title' => $menuItem['title'] ?? 'Unknown'
                ];
            }
        }

        return $missingRoutes;
    }

    /**
     * Generate fallback route cho missing routes
     *
     * @param string $originalRoute
     * @return string
     */
    public static function generateFallbackRoute(string $originalRoute): string
    {
        // Fallback routes mapping
        $fallbackMap = [
            // Admin routes
            'admin.dashboard' => 'home',
            'admin.users.index' => 'admin.dashboard',
            'admin.content.index' => 'admin.dashboard',
            'admin.marketplace.index' => 'admin.dashboard',
            'admin.settings.index' => 'admin.dashboard',
            
            // Business routes
            'partner.dashboard' => 'user.dashboard',
            'manufacturer.dashboard' => 'user.dashboard',
            'supplier.dashboard' => 'user.dashboard',
            'brand.dashboard' => 'user.dashboard',
            
            // User routes
            'user.dashboard' => 'home',
            'user.my-threads' => 'forums.index',
            'user.bookmarks' => 'home',
            'user.following' => 'home',
            'user.ratings' => 'home',
            
            // Marketplace routes
            'marketplace.index' => 'home',
            'marketplace.cart.index' => 'marketplace.index',
            
            // Default fallbacks
            'forums.index' => 'home',
            'showcases.index' => 'home',
            'docs.index' => 'home',
        ];

        // Kiểm tra exact match
        if (isset($fallbackMap[$originalRoute])) {
            $fallback = $fallbackMap[$originalRoute];
            if (self::routeExists($fallback)) {
                return $fallback;
            }
        }

        // Kiểm tra pattern match
        foreach ($fallbackMap as $pattern => $fallback) {
            if (str_contains($originalRoute, explode('.', $pattern)[0])) {
                if (self::routeExists($fallback)) {
                    return $fallback;
                }
            }
        }

        // Ultimate fallback
        return 'home';
    }

    /**
     * Log missing route để debug
     *
     * @param string $routeName
     * @param array $context
     * @return void
     */
    public static function logMissingRoute(string $routeName, array $context = []): void
    {
        Log::warning("Missing route detected in menu", [
            'route' => $routeName,
            'context' => $context,
            'timestamp' => now(),
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip()
        ]);
    }

    /**
     * Clear route validation cache
     *
     * @param string|null $routeName Specific route hoặc null để clear all
     * @return void
     */
    public static function clearValidationCache(?string $routeName = null): void
    {
        if ($routeName) {
            $cacheKey = self::CACHE_PREFIX . md5($routeName);
            Cache::forget($cacheKey);
        } else {
            // Clear all route validation cache
            // Note: Trong production nên sử dụng Redis với pattern matching
            Log::info("Route validation cache clear requested");
        }
    }

    /**
     * Get validation statistics
     *
     * @return array
     */
    public static function getValidationStats(): array
    {
        // Đây sẽ là placeholder cho statistics
        // Trong production có thể implement với Redis counters
        return [
            'total_validations' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
            'missing_routes_count' => 0,
            'last_validation' => null
        ];
    }

    /**
     * Kiểm tra route có accessible với user hiện tại không
     *
     * @param string $routeName
     * @param mixed $user
     * @return bool
     */
    public static function isRouteAccessible(string $routeName, $user = null): bool
    {
        if (!self::routeExists($routeName)) {
            return false;
        }

        try {
            $route = Route::getRoutes()->getByName($routeName);
            if (!$route) {
                return false;
            }

            // Kiểm tra middleware của route
            $middleware = $route->middleware();
            
            // Nếu có auth middleware nhưng user null
            if (in_array('auth', $middleware) && !$user) {
                return false;
            }

            // Kiểm tra admin middleware
            if (in_array('admin', $middleware) && $user && !$user->canAccessAdmin()) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Error checking route accessibility", [
                'route' => $routeName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
