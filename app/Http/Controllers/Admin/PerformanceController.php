<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\DatabaseOptimizationService;
use App\Services\PerformanceMonitoringService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    protected $cacheService;
    protected $dbOptimizationService;
    protected $performanceService;
    protected $auditService;

    public function __construct(
        CacheService $cacheService,
        DatabaseOptimizationService $dbOptimizationService,
        PerformanceMonitoringService $performanceService,
        AuditLogService $auditService
    ) {
        $this->cacheService = $cacheService;
        $this->dbOptimizationService = $dbOptimizationService;
        $this->performanceService = $performanceService;
        $this->auditService = $auditService;
    }

    /**
     * Performance Dashboard
     */
    public function index()
    {
        // Get performance metrics
        $metrics = $this->performanceService->getPerformanceMetrics();

        // Get database performance
        $dbMetrics = $this->dbOptimizationService->getPerformanceMetrics();

        // Get cache statistics
        $cacheStats = $this->getCacheStatistics();

        // Get system health
        $systemHealth = $metrics['application_health'] ?? [];

        $this->auditService->logAdminAction('view', 'performance_dashboard');

        return view('admin.performance.index', compact(
            'metrics',
            'dbMetrics',
            'cacheStats',
            'systemHealth'
        ));
    }

    /**
     * Cache Management
     */
    public function cache()
    {
        $cacheStats = $this->getCacheStatistics();

        return view('admin.performance.cache', compact('cacheStats'));
    }

    /**
     * Database Optimization
     */
    public function database()
    {
        $dbMetrics = $this->dbOptimizationService->getPerformanceMetrics();

        return view('admin.performance.database', compact('dbMetrics'));
    }

    /**
     * Security Monitoring
     */
    public function security()
    {
        $securityAnalytics = $this->auditService->getSecurityAnalytics(30);

        return view('admin.performance.security', compact('securityAnalytics'));
    }

    /**
     * Clear all cache
     */
    public function clearCache(Request $request)
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');

            // Clear config cache
            Artisan::call('config:clear');

            // Clear route cache
            Artisan::call('route:clear');

            // Clear view cache
            Artisan::call('view:clear');

            // Clear analytics cache
            $this->cacheService->clearAnalyticsCache();

            $this->auditService->logAdminAction('clear_cache', 'system', null, [
                'cache_types' => ['application', 'config', 'route', 'view', 'analytics']
            ], 'medium');

            return response()->json([
                'success' => true,
                'message' => 'Tất cả cache đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Warm up cache
     */
    public function warmUpCache(Request $request)
    {
        try {
            $this->cacheService->warmUpCache();

            $this->auditService->logAdminAction('warm_up_cache', 'system');

            return response()->json([
                'success' => true,
                'message' => 'Cache đã được làm nóng thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi làm nóng cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize database
     */
    public function optimizeDatabase(Request $request)
    {
        try {
            // Create optimized indexes
            $this->dbOptimizationService->createOptimizedIndexes();

            // Create composite indexes
            $this->dbOptimizationService->createCompositeIndexes();

            // Clean up tables
            $this->dbOptimizationService->cleanupTables();

            $this->auditService->logAdminAction('optimize_database', 'system', null, [
                'optimization_types' => ['indexes', 'composite_indexes', 'table_cleanup']
            ], 'high');

            return response()->json([
                'success' => true,
                'message' => 'Cơ sở dữ liệu đã được tối ưu hóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tối ưu hóa cơ sở dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time performance metrics
     */
    public function realtimeMetrics(Request $request)
    {
        $metrics = $this->performanceService->getPerformanceMetrics();

        return response()->json($metrics);
    }

    /**
     * Export performance report
     */
    public function exportReport(Request $request)
    {
        $period = $request->get('period', 30);
        $format = $request->get('format', 'json');

        try {
            $report = [
                'generated_at' => now()->toISOString(),
                'period_days' => $period,
                'performance_metrics' => $this->performanceService->getPerformanceMetrics(),
                'database_metrics' => $this->dbOptimizationService->getPerformanceMetrics(),
                'security_analytics' => $this->auditService->getSecurityAnalytics($period),
                'cache_statistics' => $this->getCacheStatistics(),
            ];

            $this->auditService->logAdminAction('export', 'performance_report', null, [
                'period' => $period,
                'format' => $format
            ]);

            if ($format === 'json') {
                return response()->json($report);
            }

            // For other formats, you could implement CSV, PDF export here
            return response()->json([
                'success' => false,
                'message' => 'Định dạng không được hỗ trợ'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * System maintenance mode
     */
    public function toggleMaintenanceMode(Request $request)
    {
        try {
            $isDown = app()->isDownForMaintenance();

            if ($isDown) {
                Artisan::call('up');
                $action = 'disable_maintenance';
                $message = 'Chế độ bảo trì đã được tắt';
            } else {
                Artisan::call('down', [
                    '--secret' => 'mechamap-admin-secret',
                    '--render' => 'errors::503'
                ]);
                $action = 'enable_maintenance';
                $message = 'Chế độ bảo trì đã được bật';
            }

            $this->auditService->logAdminAction($action, 'system', null, [], 'high');

            return response()->json([
                'success' => true,
                'message' => $message,
                'maintenance_mode' => !$isDown
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thay đổi chế độ bảo trì: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get audit logs
     */
    public function auditLogs(Request $request)
    {
        $filters = $request->only(['user_id', 'action', 'resource', 'risk_level', 'date_from', 'date_to']);
        $limit = $request->get('limit', 50);
        $offset = $request->get('offset', 0);

        $logs = $this->auditService->getAuditLogs($filters, $limit, $offset);

        return response()->json([
            'logs' => $logs,
            'filters' => $filters,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
            ]
        ]);
    }

    /**
     * Private helper methods
     */
    private function getCacheStatistics()
    {
        try {
            return [
                'status' => 'active',
                'driver' => config('cache.default'),
                'hit_rate' => 85.5, // Mock data - implement based on your cache driver
                'miss_rate' => 14.5,
                'total_keys' => 1250,
                'memory_usage' => '45.2 MB',
                'evictions' => 12,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
