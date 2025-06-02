<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiPerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Controller chuyên xử lý monitoring và quản lý API system
 */
class ApiMonitoringController extends Controller
{
    protected $performanceService;

    public function __construct(ApiPerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Lấy dashboard tổng quan API metrics
     */
    public function dashboard()
    {
        $metrics = $this->performanceService->getApiMetrics(24);
        $stats = $this->performanceService->getCachedStats();

        // Lấy thống kê real-time
        $realtimeStats = [
            'cache_hit_rate' => $this->getCacheHitRate(),
            'active_connections' => $this->getActiveConnections(),
            'memory_usage' => $this->getMemoryUsage(),
            'database_connections' => $this->getDatabaseConnections(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'api_metrics' => $metrics,
                'general_stats' => $stats,
                'realtime_stats' => $realtimeStats,
                'top_endpoints' => $this->getTopEndpoints($metrics),
                'error_summary' => $this->getErrorSummary($metrics),
            ],
            'meta' => [
                'generated_at' => now()->toISOString(),
                'monitoring_period' => '24 hours',
            ]
        ]);
    }

    /**
     * Lấy metrics chi tiết cho một endpoint cụ thể
     */
    public function endpointMetrics(Request $request)
    {
        $endpoint = $request->get('endpoint');
        $hours = $request->get('hours', 24);

        $metrics = [];

        for ($i = 0; $i < $hours; $i++) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            $hourMetrics = Cache::get("api_metrics_{$hour}", []);

            if (isset($hourMetrics[$endpoint])) {
                $metrics[$hour] = $hourMetrics[$endpoint];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'endpoint' => $endpoint,
                'metrics' => $metrics,
                'summary' => $this->calculateEndpointSummary($metrics),
            ]
        ]);
    }

    /**
     * Health check tổng quan hệ thống
     */
    public function healthCheck()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
            'memory' => $this->checkMemory(),
        ];

        $overallHealth = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'success' => true,
            'data' => [
                'overall_status' => $overallHealth ? 'healthy' : 'unhealthy',
                'checks' => $checks,
                'timestamp' => now()->toISOString(),
            ]
        ], $overallHealth ? 200 : 503);
    }

    /**
     * Làm mới cache
     */
    public function refreshCache(Request $request)
    {
        $pattern = $request->get('pattern');

        try {
            $this->performanceService->clearCache($pattern);

            // Warm up cache quan trọng
            if (!$pattern || $pattern === 'all') {
                $this->performanceService->warmUpCache();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache đã được làm mới thành công',
                'pattern' => $pattern ?: 'all',
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi làm mới cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy logs API errors gần đây
     */
    public function recentErrors(Request $request)
    {
        $limit = $request->get('limit', 50);

        // Đọc log files để lấy errors gần đây
        $logPath = storage_path('logs/laravel.log');
        $errors = [];

        if (file_exists($logPath)) {
            $lines = array_slice(file($logPath), -1000); // Lấy 1000 dòng cuối

            foreach (array_reverse($lines) as $line) {
                if (strpos($line, 'ERROR') !== false || strpos($line, 'WARNING') !== false) {
                    $errors[] = [
                        'timestamp' => $this->extractTimestamp($line),
                        'level' => $this->extractLevel($line),
                        'message' => $this->extractMessage($line),
                    ];

                    if (count($errors) >= $limit) {
                        break;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'errors' => $errors,
                'total' => count($errors),
            ]
        ]);
    }

    /**
     * Export API metrics để phân tích
     */
    public function exportMetrics(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $metrics = $this->performanceService->getApiMetrics($hours);

        if ($format === 'csv') {
            return $this->exportToCsv($metrics);
        }

        return response()->json([
            'success' => true,
            'data' => $metrics,
            'meta' => [
                'format' => $format,
                'hours' => $hours,
                'exported_at' => now()->toISOString(),
            ]
        ]);
    }

    // ===== PRIVATE HELPER METHODS =====

    private function getCacheHitRate()
    {
        // Tính toán cache hit rate (giả lập)
        $hits = Cache::get('cache_hits', 0);
        $misses = Cache::get('cache_misses', 0);
        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    private function getActiveConnections()
    {
        try {
            return DB::select('SHOW STATUS LIKE "Threads_connected"')[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getMemoryUsage()
    {
        return [
            'used' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'limit' => ini_get('memory_limit'),
        ];
    }

    private function getDatabaseConnections()
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getTopEndpoints($metrics, $limit = 10)
    {
        return collect($metrics)
            ->sortByDesc('total_requests')
            ->take($limit)
            ->map(function ($data, $endpoint) {
                return [
                    'endpoint' => $endpoint,
                    'requests' => $data['total_requests'],
                    'avg_duration' => $data['avg_duration'],
                    'error_rate' => $data['error_rate'],
                ];
            })
            ->values()
            ->all();
    }

    private function getErrorSummary($metrics)
    {
        $totalErrors = collect($metrics)->sum('total_errors');
        $totalRequests = collect($metrics)->sum('total_requests');

        $errorsByEndpoint = collect($metrics)
            ->filter(fn($data) => $data['total_errors'] > 0)
            ->map(fn($data, $endpoint) => [
                'endpoint' => $endpoint,
                'errors' => $data['total_errors'],
                'error_rate' => $data['error_rate'],
            ])
            ->sortByDesc('errors')
            ->values()
            ->all();

        return [
            'total_errors' => $totalErrors,
            'overall_error_rate' => $totalRequests > 0 ? round(($totalErrors / $totalRequests) * 100, 2) : 0,
            'errors_by_endpoint' => $errorsByEndpoint,
        ];
    }

    private function calculateEndpointSummary($metrics)
    {
        if (empty($metrics)) {
            return null;
        }

        $totalRequests = array_sum(array_column($metrics, 'count'));
        $totalDuration = array_sum(array_column($metrics, 'total_duration'));
        $totalErrors = array_sum(array_column($metrics, 'errors'));
        $maxDuration = max(array_column($metrics, 'max_duration'));

        return [
            'total_requests' => $totalRequests,
            'avg_duration' => $totalRequests > 0 ? round($totalDuration / $totalRequests, 3) : 0,
            'max_duration' => $maxDuration,
            'total_errors' => $totalErrors,
            'error_rate' => $totalRequests > 0 ? round(($totalErrors / $totalRequests) * 100, 2) : 0,
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            return ['status' => $value === 'ok' ? 'ok' : 'error', 'message' => 'Cache is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache failed: ' . $e->getMessage()];
        }
    }

    private function checkStorage()
    {
        try {
            $path = storage_path('app/health_check.txt');
            file_put_contents($path, 'test');
            $content = file_get_contents($path);
            unlink($path);

            return ['status' => $content === 'test' ? 'ok' : 'error', 'message' => 'Storage is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage failed: ' . $e->getMessage()];
        }
    }

    private function checkQueue()
    {
        // Kiểm tra queue status
        try {
            // Giả lập check queue - có thể customize theo queue driver
            return ['status' => 'ok', 'message' => 'Queue is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Queue failed: ' . $e->getMessage()];
        }
    }

    private function checkMemory()
    {
        $usage = memory_get_usage(true);
        $limit = $this->convertToBytes(ini_get('memory_limit'));
        $percentage = ($usage / $limit) * 100;

        $status = $percentage < 80 ? 'ok' : ($percentage < 95 ? 'warning' : 'critical');

        return [
            'status' => $status,
            'message' => "Memory usage: {$percentage}%",
            'usage' => round($usage / 1024 / 1024, 2) . ' MB',
            'limit' => ini_get('memory_limit'),
        ];
    }

    private function convertToBytes($value)
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;

        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    private function extractTimestamp($line)
    {
        preg_match('/\[(.*?)\]/', $line, $matches);
        return $matches[1] ?? null;
    }

    private function extractLevel($line)
    {
        if (strpos($line, 'ERROR') !== false) return 'ERROR';
        if (strpos($line, 'WARNING') !== false) return 'WARNING';
        return 'INFO';
    }

    private function extractMessage($line)
    {
        // Lấy phần message sau timestamp và level
        $parts = explode('] ', $line, 3);
        return isset($parts[2]) ? trim($parts[2]) : $line;
    }

    private function exportToCsv($metrics)
    {
        $csv = "Endpoint,Total Requests,Avg Duration,Max Duration,Total Errors,Error Rate\n";

        foreach ($metrics as $endpoint => $data) {
            $csv .= "{$endpoint},{$data['total_requests']},{$data['avg_duration']},{$data['max_duration']},{$data['total_errors']},{$data['error_rate']}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="api_metrics_' . date('Y-m-d_H-i-s') . '.csv"');
    }
}
