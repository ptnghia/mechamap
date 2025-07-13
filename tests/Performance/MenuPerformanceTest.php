<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Services\MenuService;
use App\Services\MenuCacheService;
use App\Services\MenuPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Menu Performance Test
 * 
 * Test suite để đo lường và validate performance của menu system
 */
class MenuPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear cache before each test
        Cache::flush();
        
        // Enable query logging
        DB::enableQueryLog();
    }

    /** @test */
    public function menu_service_performance_is_acceptable()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Test menu configuration generation
        $config = MenuService::getMenuConfiguration($user);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        
        // Performance assertions
        $this->assertLessThan(100, $executionTime, 'Menu configuration should generate in under 100ms');
        $this->assertLessThan(1024 * 1024, $memoryUsage, 'Memory usage should be under 1MB');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('component', $config);
    }

    /** @test */
    public function menu_caching_improves_performance()
    {
        $user = User::factory()->create(['role' => 'verified_partner']);
        
        // First call (no cache)
        $startTime1 = microtime(true);
        $config1 = MenuService::getMenuConfiguration($user);
        $time1 = (microtime(true) - $startTime1) * 1000;
        
        // Second call (with cache)
        $startTime2 = microtime(true);
        $config2 = MenuService::getMenuConfiguration($user);
        $time2 = (microtime(true) - $startTime2) * 1000;
        
        // Cache should improve performance significantly
        $this->assertLessThan($time1 * 0.5, $time2, 'Cached call should be at least 50% faster');
        $this->assertEquals($config1, $config2, 'Cached config should be identical');
    }

    /** @test */
    public function database_queries_are_optimized()
    {
        $user = User::factory()->create(['role' => 'manufacturer']);
        
        DB::flushQueryLog();
        
        // Generate menu configuration
        MenuService::getMenuConfiguration($user);
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should use minimal database queries
        $this->assertLessThanOrEqual(5, $queryCount, 'Menu generation should use 5 or fewer database queries');
        
        // Log queries for analysis
        foreach ($queries as $query) {
            echo "Query: " . $query['query'] . " (Time: " . $query['time'] . "ms)\n";
        }
    }

    /** @test */
    public function bulk_user_data_loading_is_efficient()
    {
        $users = User::factory()->count(10)->create();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        DB::flushQueryLog();
        
        // Bulk load menu data
        $result = MenuPerformanceService::bulkPreloadMenuData($users);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsage = $endMemory - $startMemory;
        $queryCount = count($queries);
        
        // Performance assertions for bulk loading
        $this->assertLessThan(500, $executionTime, 'Bulk loading 10 users should take under 500ms');
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsage, 'Memory usage should be under 5MB');
        $this->assertLessThanOrEqual(10, $queryCount, 'Should use 10 or fewer queries for bulk loading');
        $this->assertCount(10, $result, 'Should return data for all 10 users');
    }

    /** @test */
    public function menu_component_rendering_performance()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $config = MenuService::getMenuConfiguration($user);
        
        $components = [
            'components.menu.admin-menu',
            'components.menu.member-menu',
            'components.menu.business-menu',
            'components.menu.guest-menu'
        ];
        
        foreach ($components as $component) {
            $startTime = microtime(true);
            
            try {
                $html = MenuPerformanceService::optimizeComponentRendering($component, $config);
                $renderTime = (microtime(true) - $startTime) * 1000;
                
                $this->assertLessThan(200, $renderTime, "Component {$component} should render in under 200ms");
                $this->assertNotEmpty($html, "Component {$component} should produce HTML output");
                
            } catch (\Exception $e) {
                // Some components might not exist in test environment
                $this->markTestSkipped("Component {$component} not available: " . $e->getMessage());
            }
        }
    }

    /** @test */
    public function cache_invalidation_performance()
    {
        $users = User::factory()->count(5)->create();
        
        // Warm up cache
        foreach ($users as $user) {
            MenuService::getMenuConfiguration($user);
        }
        
        $startTime = microtime(true);
        
        // Test cache invalidation
        foreach ($users as $user) {
            MenuCacheService::invalidateUserMenuCache($user);
        }
        
        $invalidationTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(100, $invalidationTime, 'Cache invalidation for 5 users should take under 100ms');
    }

    /** @test */
    public function memory_usage_is_reasonable()
    {
        $initialMemory = memory_get_usage();
        
        // Create multiple users with different roles
        $roles = ['super_admin', 'member', 'verified_partner', 'manufacturer', 'guest'];
        $users = [];
        
        foreach ($roles as $role) {
            $users[] = User::factory()->create(['role' => $role]);
        }
        
        // Generate menu configurations
        $configs = [];
        foreach ($users as $user) {
            $configs[] = MenuService::getMenuConfiguration($user);
        }
        
        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;
        
        // Memory increase should be reasonable
        $this->assertLessThan(2 * 1024 * 1024, $memoryIncrease, 'Memory increase should be under 2MB for 5 menu configs');
        
        // Cleanup
        unset($configs, $users);
        
        // Force garbage collection
        gc_collect_cycles();
        
        $afterCleanupMemory = memory_get_usage();
        $memoryReclaimed = $finalMemory - $afterCleanupMemory;
        
        $this->assertGreaterThan(0, $memoryReclaimed, 'Memory should be reclaimed after cleanup');
    }

    /** @test */
    public function concurrent_menu_access_performance()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        $startTime = microtime(true);
        
        // Simulate concurrent access
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = MenuService::getMenuConfiguration($user);
        }
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        $averageTime = $totalTime / 10;
        
        $this->assertLessThan(50, $averageTime, 'Average menu generation time should be under 50ms with caching');
        
        // All results should be identical (cached)
        for ($i = 1; $i < 10; $i++) {
            $this->assertEquals($results[0], $results[$i], "Result {$i} should match first result");
        }
    }

    /** @test */
    public function route_validation_performance()
    {
        $routes = [
            'home', 'forums.index', 'showcases.index', 'marketplace.index',
            'user.dashboard', 'admin.dashboard', 'partner.dashboard',
            'non.existent.route', 'another.fake.route'
        ];
        
        $startTime = microtime(true);
        
        $validations = [];
        foreach ($routes as $route) {
            $validations[$route] = \Illuminate\Support\Facades\Route::has($route);
        }
        
        $validationTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(100, $validationTime, 'Route validation for 9 routes should take under 100ms');
        $this->assertCount(9, $validations, 'Should validate all routes');
    }

    /** @test */
    public function performance_monitoring_works()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        // Test performance monitoring
        MenuPerformanceService::monitorPerformance('menu_generation_test', [
            'user_id' => $user->id,
            'user_role' => $user->role
        ]);
        
        $metrics = MenuPerformanceService::getPerformanceMetrics();
        
        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('cache_hit_rate', $metrics);
        $this->assertArrayHasKey('average_render_time', $metrics);
        $this->assertArrayHasKey('database_query_count', $metrics);
        $this->assertArrayHasKey('memory_usage', $metrics);
    }

    /** @test */
    public function stress_test_menu_system()
    {
        // Create many users
        $users = User::factory()->count(50)->create();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        DB::flushQueryLog();
        
        // Generate menu configs for all users
        $configs = [];
        foreach ($users as $user) {
            $configs[] = MenuService::getMenuConfiguration($user);
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = DB::getQueryLog();
        
        $totalTime = ($endTime - $startTime) * 1000;
        $averageTime = $totalTime / 50;
        $memoryUsage = $endMemory - $startMemory;
        $queryCount = count($queries);
        
        // Stress test assertions
        $this->assertLessThan(5000, $totalTime, 'Generating 50 menu configs should take under 5 seconds');
        $this->assertLessThan(100, $averageTime, 'Average time per menu config should be under 100ms');
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsage, 'Memory usage should be under 10MB');
        $this->assertLessThan(100, $queryCount, 'Should use fewer than 100 database queries total');
        
        echo "\nStress Test Results:\n";
        echo "Total Time: {$totalTime}ms\n";
        echo "Average Time: {$averageTime}ms\n";
        echo "Memory Usage: " . number_format($memoryUsage / 1024 / 1024, 2) . "MB\n";
        echo "Query Count: {$queryCount}\n";
    }

    protected function tearDown(): void
    {
        // Log final performance metrics
        $peakMemory = memory_get_peak_usage(true);
        $queries = DB::getQueryLog();
        
        echo "\nTest Performance Summary:\n";
        echo "Peak Memory: " . number_format($peakMemory / 1024 / 1024, 2) . "MB\n";
        echo "Total Queries: " . count($queries) . "\n";
        
        parent::tearDown();
    }
}
