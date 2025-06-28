<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringService
{
    private $startTime;
    private $memoryStart;
    
    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->memoryStart = memory_get_usage(true);
    }

    /**
     * Monitor application performance metrics
     */
    public function getPerformanceMetrics()
    {
        return [
            'response_time' => $this->getResponseTime(),
            'memory_usage' => $this->getMemoryUsage(),
            'database_performance' => $this->getDatabasePerformance(),
            'cache_performance' => $this->getCachePerformance(),
            'system_resources' => $this->getSystemResources(),
            'application_health' => $this->getApplicationHealth(),
        ];
    }

    /**
     * Get response time metrics
     */
    public function getResponseTime()
    {
        $currentTime = microtime(true);
        $executionTime = ($currentTime - $this->startTime) * 1000; // Convert to milliseconds
        
        return [
            'current_request' => round($executionTime, 2),
            'average_response_time' => $this->getAverageResponseTime(),
            'slow_requests_count' => $this->getSlowRequestsCount(),
        ];
    }

    /**
     * Get memory usage metrics
     */
    public function getMemoryUsage()
    {
        $currentMemory = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        $memoryLimit = $this->getMemoryLimit();
        
        return [
            'current_usage' => $this->formatBytes($currentMemory),
            'current_usage_bytes' => $currentMemory,
            'peak_usage' => $this->formatBytes($peakMemory),
            'peak_usage_bytes' => $peakMemory,
            'memory_limit' => $this->formatBytes($memoryLimit),
            'memory_limit_bytes' => $memoryLimit,
            'usage_percentage' => round(($currentMemory / $memoryLimit) * 100, 2),
            'request_memory_delta' => $this->formatBytes($currentMemory - $this->memoryStart),
        ];
    }

    /**
     * Get database performance metrics
     */
    public function getDatabasePerformance()
    {
        try {
            $queryCount = DB::getQueryLog() ? count(DB::getQueryLog()) : 0;
            
            // Get database status
            $dbStatus = $this->getDatabaseStatus();
            
            return [
                'query_count' => $queryCount,
                'slow_queries' => $dbStatus['slow_queries'] ?? 0,
                'connections' => $dbStatus['connections'] ?? 0,
                'buffer_pool_hit_ratio' => $dbStatus['buffer_pool_hit_ratio'] ?? 0,
                'innodb_buffer_pool_size' => $dbStatus['innodb_buffer_pool_size'] ?? 0,
                'query_cache_hit_rate' => $dbStatus['query_cache_hit_rate'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::warning('Database performance monitoring failed: ' . $e->getMessage());
            return ['error' => 'Database metrics unavailable'];
        }
    }

    /**
     * Get cache performance metrics
     */
    public function getCachePerformance()
    {
        try {
            $cacheStats = $this->getCacheStats();
            
            return [
                'hit_rate' => $cacheStats['hit_rate'] ?? 0,
                'miss_rate' => $cacheStats['miss_rate'] ?? 0,
                'total_keys' => $cacheStats['total_keys'] ?? 0,
                'memory_usage' => $cacheStats['memory_usage'] ?? 0,
                'evictions' => $cacheStats['evictions'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::warning('Cache performance monitoring failed: ' . $e->getMessage());
            return ['error' => 'Cache metrics unavailable'];
        }
    }

    /**
     * Get system resource metrics
     */
    public function getSystemResources()
    {
        return [
            'cpu_usage' => $this->getCpuUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'load_average' => $this->getLoadAverage(),
            'uptime' => $this->getSystemUptime(),
        ];
    }

    /**
     * Get application health status
     */
    public function getApplicationHealth()
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
            'score' => 100,
        ];

        // Database connectivity check
        $health['checks']['database'] = $this->checkDatabaseHealth();
        
        // Cache connectivity check
        $health['checks']['cache'] = $this->checkCacheHealth();
        
        // Storage check
        $health['checks']['storage'] = $this->checkStorageHealth();
        
        // Memory check
        $health['checks']['memory'] = $this->checkMemoryHealth();
        
        // Calculate overall health score
        $health['score'] = $this->calculateHealthScore($health['checks']);
        
        if ($health['score'] < 70) {
            $health['status'] = 'unhealthy';
        } elseif ($health['score'] < 85) {
            $health['status'] = 'warning';
        }

        return $health;
    }

    /**
     * Log performance metrics
     */
    public function logPerformanceMetrics()
    {
        $metrics = $this->getPerformanceMetrics();
        
        // Log slow requests
        if ($metrics['response_time']['current_request'] > 1000) { // > 1 second
            Log::warning('Slow request detected', [
                'response_time' => $metrics['response_time']['current_request'],
                'memory_usage' => $metrics['memory_usage']['current_usage'],
                'url' => request()->fullUrl(),
                'method' => request()->method(),
            ]);
        }

        // Log high memory usage
        if ($metrics['memory_usage']['usage_percentage'] > 80) {
            Log::warning('High memory usage detected', [
                'memory_usage' => $metrics['memory_usage']['current_usage'],
                'usage_percentage' => $metrics['memory_usage']['usage_percentage'],
            ]);
        }

        // Store metrics in cache for monitoring dashboard
        Cache::put('performance_metrics_' . date('Y-m-d-H-i'), $metrics, 3600);
    }

    /**
     * Private helper methods
     */
    private function getAverageResponseTime()
    {
        // Get cached average response time
        return Cache::get('avg_response_time', 0);
    }

    private function getSlowRequestsCount()
    {
        // Get count of slow requests from cache
        return Cache::get('slow_requests_count', 0);
    }

    private function getMemoryLimit()
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit == -1) {
            return PHP_INT_MAX;
        }
        
        return $this->convertToBytes($memoryLimit);
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

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function getDatabaseStatus()
    {
        try {
            $status = [];
            
            // Get slow queries count
            $slowQueries = DB::select("SHOW STATUS LIKE 'Slow_queries'");
            $status['slow_queries'] = $slowQueries[0]->Value ?? 0;
            
            // Get connections count
            $connections = DB::select("SHOW STATUS LIKE 'Connections'");
            $status['connections'] = $connections[0]->Value ?? 0;
            
            // Get buffer pool stats
            $bufferReads = DB::select("SHOW STATUS LIKE 'Innodb_buffer_pool_reads'");
            $bufferRequests = DB::select("SHOW STATUS LIKE 'Innodb_buffer_pool_read_requests'");
            
            if (isset($bufferRequests[0]) && $bufferRequests[0]->Value > 0) {
                $status['buffer_pool_hit_ratio'] = round(
                    (1 - ($bufferReads[0]->Value ?? 0) / $bufferRequests[0]->Value) * 100, 
                    2
                );
            }
            
            return $status;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getCacheStats()
    {
        // This would depend on your cache driver (Redis, Memcached, etc.)
        // For now, return mock data
        return [
            'hit_rate' => 85.5,
            'miss_rate' => 14.5,
            'total_keys' => 1250,
            'memory_usage' => '45.2 MB',
            'evictions' => 12,
        ];
    }

    private function getCpuUsage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return round($load[0] * 100, 2);
        }
        
        return 0;
    }

    private function getDiskUsage()
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'percentage' => round(($usedSpace / $totalSpace) * 100, 2),
        ];
    }

    private function getLoadAverage()
    {
        if (function_exists('sys_getloadavg')) {
            return sys_getloadavg();
        }
        
        return [0, 0, 0];
    }

    private function getSystemUptime()
    {
        if (file_exists('/proc/uptime')) {
            $uptime = file_get_contents('/proc/uptime');
            $uptime = explode(' ', $uptime)[0];
            return round($uptime / 3600, 2) . ' hours';
        }
        
        return 'Unknown';
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::select('SELECT 1');
            return ['status' => 'healthy', 'message' => 'Database connection OK'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed'];
        }
    }

    private function checkCacheHealth()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $value = Cache::get('health_check');
            
            if ($value === 'ok') {
                return ['status' => 'healthy', 'message' => 'Cache working properly'];
            } else {
                return ['status' => 'unhealthy', 'message' => 'Cache not working'];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Cache connection failed'];
        }
    }

    private function checkStorageHealth()
    {
        $diskUsage = $this->getDiskUsage();
        
        if ($diskUsage['percentage'] > 90) {
            return ['status' => 'unhealthy', 'message' => 'Disk usage critical: ' . $diskUsage['percentage'] . '%'];
        } elseif ($diskUsage['percentage'] > 80) {
            return ['status' => 'warning', 'message' => 'Disk usage high: ' . $diskUsage['percentage'] . '%'];
        } else {
            return ['status' => 'healthy', 'message' => 'Disk usage normal: ' . $diskUsage['percentage'] . '%'];
        }
    }

    private function checkMemoryHealth()
    {
        $memory = $this->getMemoryUsage();
        
        if ($memory['usage_percentage'] > 90) {
            return ['status' => 'unhealthy', 'message' => 'Memory usage critical: ' . $memory['usage_percentage'] . '%'];
        } elseif ($memory['usage_percentage'] > 80) {
            return ['status' => 'warning', 'message' => 'Memory usage high: ' . $memory['usage_percentage'] . '%'];
        } else {
            return ['status' => 'healthy', 'message' => 'Memory usage normal: ' . $memory['usage_percentage'] . '%'];
        }
    }

    private function calculateHealthScore($checks)
    {
        $totalChecks = count($checks);
        $healthyChecks = 0;
        $warningChecks = 0;
        
        foreach ($checks as $check) {
            if ($check['status'] === 'healthy') {
                $healthyChecks++;
            } elseif ($check['status'] === 'warning') {
                $warningChecks++;
            }
        }
        
        // Healthy = 100%, Warning = 75%, Unhealthy = 0%
        return round((($healthyChecks * 100) + ($warningChecks * 75)) / $totalChecks);
    }
}
