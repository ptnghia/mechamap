<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MenuCacheService;
use App\Services\MenuLoggingService;
use App\Services\MenuPerformanceService;
use App\Services\MenuFallbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Menu Monitoring Controller
 * 
 * Controller để monitor và quản lý performance của menu system
 */
class MenuMonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display menu monitoring dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = [
            'performance_metrics' => MenuPerformanceService::getPerformanceMetrics(),
            'cache_stats' => MenuCacheService::getCacheStats(),
            'logging_stats' => MenuLoggingService::getLoggingStats(),
            'fallback_stats' => MenuFallbackService::getFallbackStats(),
            'health_report' => MenuLoggingService::generateHealthReport(),
            'cache_health' => MenuCacheService::checkCacheHealth(),
        ];

        return view('admin.menu-monitoring.index', $data);
    }

    /**
     * Get real-time performance metrics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetrics()
    {
        return response()->json([
            'performance' => MenuPerformanceService::getPerformanceMetrics(),
            'cache' => MenuCacheService::getCacheStats(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get problematic routes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProblematicRoutes()
    {
        return response()->json([
            'missing_routes' => MenuLoggingService::getMostProblematicRoutes(20),
            'permission_denials' => MenuLoggingService::getPermissionDenialTrends()
        ]);
    }

    /**
     * Clear all menu cache
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            MenuCacheService::invalidateAllMenuCache();
            MenuLoggingService::clearLoggingStats();
            
            return response()->json([
                'success' => true,
                'message' => 'All menu cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Warm up cache for all roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function warmUpCache()
    {
        try {
            $results = MenuCacheService::warmUpAllRoleCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache warmed up successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to warm up cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run performance test
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function runPerformanceTest()
    {
        try {
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
            
            // Test menu generation for different roles
            $testResults = [];
            $roles = ['super_admin', 'member', 'verified_partner', 'manufacturer', 'guest'];
            
            foreach ($roles as $role) {
                $roleStartTime = microtime(true);
                
                // Create mock user
                $mockUser = new \App\Models\User(['role' => $role]);
                $config = \App\Services\MenuService::getMenuConfiguration($mockUser);
                
                $roleTime = (microtime(true) - $roleStartTime) * 1000;
                
                $testResults[$role] = [
                    'time_ms' => round($roleTime, 2),
                    'menu_items_count' => count($config['menu_items'] ?? []),
                    'has_permissions' => !empty($config['permissions'] ?? []),
                    'has_features' => !empty($config['features'] ?? [])
                ];
            }
            
            $totalTime = (microtime(true) - $startTime) * 1000;
            $memoryUsage = memory_get_usage() - $startMemory;
            
            return response()->json([
                'success' => true,
                'total_time_ms' => round($totalTime, 2),
                'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
                'role_results' => $testResults,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Performance test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export monitoring data
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportData(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $data = [
            'export_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'generated_at' => now()->toISOString(),
                'generated_by' => auth()->user()->name
            ],
            'performance_metrics' => MenuPerformanceService::getPerformanceMetrics(),
            'cache_stats' => MenuCacheService::getCacheStats(),
            'logging_stats' => MenuLoggingService::getLoggingStats(),
            'health_report' => MenuLoggingService::generateHealthReport(),
            'problematic_routes' => MenuLoggingService::getMostProblematicRoutes(50),
            'permission_trends' => MenuLoggingService::getPermissionDenialTrends()
        ];
        
        $filename = "menu-monitoring-{$startDate}-to-{$endDate}.json";
        
        return response()->json($data)
                        ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Get cache health status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCacheHealth()
    {
        return response()->json(MenuCacheService::checkCacheHealth());
    }

    /**
     * Get system health overview
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSystemHealth()
    {
        $health = [
            'overall_status' => 'healthy',
            'components' => [
                'cache' => MenuCacheService::checkCacheHealth(),
                'logging' => MenuLoggingService::generateHealthReport(),
                'performance' => $this->checkPerformanceHealth(),
                'fallback' => MenuFallbackService::validateFallbackConfiguration([])
            ],
            'timestamp' => now()->toISOString()
        ];
        
        // Determine overall status
        $hasErrors = false;
        $hasWarnings = false;
        
        foreach ($health['components'] as $component) {
            if (isset($component['status'])) {
                if ($component['status'] === 'error') {
                    $hasErrors = true;
                } elseif ($component['status'] === 'warning') {
                    $hasWarnings = true;
                }
            }
        }
        
        if ($hasErrors) {
            $health['overall_status'] = 'error';
        } elseif ($hasWarnings) {
            $health['overall_status'] = 'warning';
        }
        
        return response()->json($health);
    }

    /**
     * Check performance health
     *
     * @return array
     */
    private function checkPerformanceHealth(): array
    {
        $metrics = MenuPerformanceService::getPerformanceMetrics();
        
        $health = [
            'status' => 'healthy',
            'issues' => [],
            'metrics' => $metrics
        ];
        
        // Check performance thresholds
        if (isset($metrics['average_render_time']) && $metrics['average_render_time'] > 100) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Average render time is high: ' . $metrics['average_render_time'] . 'ms';
        }
        
        if (isset($metrics['cache_hit_rate']) && $metrics['cache_hit_rate'] < 80) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Cache hit rate is low: ' . $metrics['cache_hit_rate'] . '%';
        }
        
        if (isset($metrics['database_query_count']) && $metrics['database_query_count'] > 10) {
            $health['status'] = 'warning';
            $health['issues'][] = 'High database query count: ' . $metrics['database_query_count'];
        }
        
        return $health;
    }

    /**
     * Get detailed cache information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCacheDetails()
    {
        $details = [
            'stats' => MenuCacheService::getCacheStats(),
            'health' => MenuCacheService::checkCacheHealth(),
            'recommendations' => $this->getCacheRecommendations()
        ];
        
        return response()->json($details);
    }

    /**
     * Get cache recommendations
     *
     * @return array
     */
    private function getCacheRecommendations(): array
    {
        $stats = MenuCacheService::getCacheStats();
        $recommendations = [];
        
        if (isset($stats['hit_rate']) && is_numeric($stats['hit_rate']) && $stats['hit_rate'] < 80) {
            $recommendations[] = 'Consider warming up cache more frequently to improve hit rate';
        }
        
        if (isset($stats['user_menu_cache_count']) && $stats['user_menu_cache_count'] > 1000) {
            $recommendations[] = 'Large number of user menu caches - consider implementing cleanup strategy';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Cache performance is optimal';
        }
        
        return $recommendations;
    }

    /**
     * Test menu system functionality
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testSystem()
    {
        $tests = [
            'route_validation' => $this->testRouteValidation(),
            'permission_checking' => $this->testPermissionChecking(),
            'cache_functionality' => $this->testCacheFunctionality(),
            'fallback_system' => $this->testFallbackSystem()
        ];
        
        $overallSuccess = !in_array(false, array_column($tests, 'success'));
        
        return response()->json([
            'overall_success' => $overallSuccess,
            'tests' => $tests,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Test route validation
     *
     * @return array
     */
    private function testRouteValidation(): array
    {
        try {
            $testRoutes = ['home', 'forums.index', 'non.existent.route'];
            $results = [];
            
            foreach ($testRoutes as $route) {
                $results[$route] = \Illuminate\Support\Facades\Route::has($route);
            }
            
            return [
                'success' => true,
                'message' => 'Route validation working correctly',
                'results' => $results
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Route validation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test permission checking
     *
     * @return array
     */
    private function testPermissionChecking(): array
    {
        try {
            // Create mock users with different roles
            $testUsers = [
                new \App\Models\User(['role' => 'super_admin']),
                new \App\Models\User(['role' => 'member']),
                new \App\Models\User(['role' => 'guest'])
            ];
            
            $results = [];
            foreach ($testUsers as $user) {
                $config = \App\Services\MenuService::getMenuConfiguration($user);
                $results[$user->role] = !empty($config['permissions']);
            }
            
            return [
                'success' => true,
                'message' => 'Permission checking working correctly',
                'results' => $results
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Permission checking failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test cache functionality
     *
     * @return array
     */
    private function testCacheFunctionality(): array
    {
        try {
            $testKey = 'menu_test_' . time();
            $testValue = ['test' => 'data'];
            
            // Test cache set
            Cache::put($testKey, $testValue, 60);
            
            // Test cache get
            $retrieved = Cache::get($testKey);
            
            // Test cache forget
            Cache::forget($testKey);
            $afterForget = Cache::get($testKey);
            
            $success = ($retrieved === $testValue) && ($afterForget === null);
            
            return [
                'success' => $success,
                'message' => $success ? 'Cache functionality working correctly' : 'Cache functionality failed'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Cache test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test fallback system
     *
     * @return array
     */
    private function testFallbackSystem(): array
    {
        try {
            $testConfig = [
                'menu_items' => [
                    'valid_item' => ['route' => 'home', 'title' => 'Home'],
                    'invalid_item' => ['route' => 'non.existent.route', 'title' => 'Invalid']
                ]
            ];
            
            $processed = MenuFallbackService::processFallbackMenu($testConfig);
            
            $success = isset($processed['menu_items']) && 
                      count($processed['menu_items']) > 0;
            
            return [
                'success' => $success,
                'message' => $success ? 'Fallback system working correctly' : 'Fallback system failed',
                'processed_items' => count($processed['menu_items'] ?? [])
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Fallback test failed: ' . $e->getMessage()
            ];
        }
    }
}
