<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class RedisClusterManager
{
    private array $config;
    private array $connections = [];
    private array $healthStatus = [];
    private bool $clusterEnabled;

    public function __construct()
    {
        $this->config = config('redis-cluster');
        $this->clusterEnabled = $this->config['enabled'] ?? false;
    }

    /**
     * Get Redis connection for specific cluster
     */
    public function getConnection(string $cluster = 'default'): mixed
    {
        if (!$this->clusterEnabled) {
            return $this->getFallbackConnection();
        }

        if (!isset($this->connections[$cluster])) {
            $this->connections[$cluster] = $this->createClusterConnection($cluster);
        }

        return $this->connections[$cluster];
    }

    /**
     * Create cluster connection
     */
    private function createClusterConnection(string $cluster): mixed
    {
        try {
            $clusterConfig = $this->config['clusters'][$cluster] ?? null;

            if (!$clusterConfig) {
                throw new Exception("Cluster configuration not found: {$cluster}");
            }

            $nodes = [];
            foreach ($clusterConfig['nodes'] as $node) {
                $nodes[] = $node['host'] . ':' . $node['port'];
            }

            $options = $clusterConfig['options'] ?? [];

            // Create Redis cluster connection
            $redis = new \RedisCluster(null, $nodes, 1.5, 1.5, true, $options['password'] ?? null);

            // Configure options
            if (isset($options['serializer'])) {
                $redis->setOption(\Redis::OPT_SERIALIZER, $options['serializer']);
            }

            if (isset($options['compression'])) {
                $redis->setOption(\Redis::OPT_COMPRESSION, $options['compression']);
            }

            if (isset($options['prefix'])) {
                $redis->setOption(\Redis::OPT_PREFIX, $options['prefix']);
            }

            Log::info("Redis cluster connection established", ['cluster' => $cluster]);

            return $redis;

        } catch (Exception $e) {
            Log::error("Failed to create Redis cluster connection", [
                'cluster' => $cluster,
                'error' => $e->getMessage(),
            ]);

            if ($this->config['failover']['fallback_to_single'] ?? false) {
                return $this->getFallbackConnection();
            }

            throw $e;
        }
    }

    /**
     * Get fallback single Redis connection
     */
    private function getFallbackConnection(): mixed
    {
        try {
            // Check if Redis extension is available
            if (!extension_loaded('redis')) {
                Log::warning("Redis extension not available, using Laravel Cache facade");
                return new \stdClass(); // Placeholder for cache facade
            }

            $fallbackConfig = $this->config['failover']['fallback_config'];

            $redis = new \Redis();
            $redis->connect(
                $fallbackConfig['host'],
                $fallbackConfig['port'],
                3 // timeout
            );

            if ($fallbackConfig['password']) {
                $redis->auth($fallbackConfig['password']);
            }

            if (isset($fallbackConfig['database'])) {
                $redis->select($fallbackConfig['database']);
            }

            Log::warning("Using fallback Redis connection");

            return $redis;

        } catch (Exception $e) {
            Log::warning("Redis not available, using cache facade fallback", [
                'error' => $e->getMessage(),
            ]);
            return new \stdClass(); // Placeholder for cache facade
        }
    }

    /**
     * Check cluster health
     */
    public function checkClusterHealth(string $cluster = 'default'): array
    {
        $health = [
            'cluster' => $cluster,
            'status' => 'unknown',
            'nodes' => [],
            'total_nodes' => 0,
            'healthy_nodes' => 0,
            'master_nodes' => 0,
            'slave_nodes' => 0,
            'memory_usage' => [],
            'last_check' => now()->toISOString(),
        ];

        try {
            if (!$this->clusterEnabled) {
                return $this->checkSingleNodeHealth();
            }

            $connection = $this->getConnection($cluster);
            $clusterConfig = $this->config['clusters'][$cluster];

            foreach ($clusterConfig['nodes'] as $index => $node) {
                $nodeHealth = $this->checkNodeHealth($node, $index);
                $health['nodes'][] = $nodeHealth;
                $health['total_nodes']++;

                if ($nodeHealth['status'] === 'healthy') {
                    $health['healthy_nodes']++;
                }

                if ($nodeHealth['role'] === 'master') {
                    $health['master_nodes']++;
                } elseif ($nodeHealth['role'] === 'slave') {
                    $health['slave_nodes']++;
                }

                $health['memory_usage'][] = $nodeHealth['memory_usage'];
            }

            // Determine overall cluster status
            $healthyPercentage = ($health['healthy_nodes'] / $health['total_nodes']) * 100;

            if ($healthyPercentage >= 80) {
                $health['status'] = 'healthy';
            } elseif ($healthyPercentage >= 50) {
                $health['status'] = 'degraded';
            } else {
                $health['status'] = 'critical';
            }

            $this->healthStatus[$cluster] = $health;

        } catch (Exception $e) {
            $health['status'] = 'error';
            $health['error'] = $e->getMessage();

            Log::error("Cluster health check failed", [
                'cluster' => $cluster,
                'error' => $e->getMessage(),
            ]);
        }

        return $health;
    }

    /**
     * Check individual node health
     */
    private function checkNodeHealth(array $nodeConfig, int $index): array
    {
        $nodeHealth = [
            'index' => $index,
            'host' => $nodeConfig['host'] ?? '127.0.0.1',
            'port' => $nodeConfig['port'] ?? 6379,
            'status' => 'unknown',
            'role' => 'unknown',
            'memory_usage' => 0,
            'connected_clients' => 0,
            'uptime' => 0,
            'response_time' => 0,
        ];

        try {
            // Check if Redis extension is available
            if (!extension_loaded('redis')) {
                $nodeHealth['status'] = 'simulated';
                $nodeHealth['role'] = 'master';
                $nodeHealth['memory_usage'] = 1024 * 1024; // 1MB simulated
                $nodeHealth['connected_clients'] = 1;
                $nodeHealth['uptime'] = 3600; // 1 hour simulated
                $nodeHealth['response_time'] = 1.5;
                return $nodeHealth;
            }

            $startTime = microtime(true);

            $redis = new \Redis();
            $redis->connect($nodeHealth['host'], $nodeHealth['port'], 3);

            if (isset($nodeConfig['password'])) {
                $redis->auth($nodeConfig['password']);
            }

            // Test connection with ping
            $pong = $redis->ping();
            $nodeHealth['response_time'] = round((microtime(true) - $startTime) * 1000, 2);

            if ($pong === '+PONG' || $pong === 'PONG') {
                $nodeHealth['status'] = 'healthy';

                // Get node info
                $info = $redis->info();

                $nodeHealth['role'] = $info['role'] ?? 'unknown';
                $nodeHealth['memory_usage'] = $info['used_memory'] ?? 0;
                $nodeHealth['connected_clients'] = $info['connected_clients'] ?? 0;
                $nodeHealth['uptime'] = $info['uptime_in_seconds'] ?? 0;
            } else {
                $nodeHealth['status'] = 'unhealthy';
            }

            $redis->close();

        } catch (Exception $e) {
            $nodeHealth['status'] = 'error';
            $nodeHealth['error'] = $e->getMessage();
        }

        return $nodeHealth;
    }

    /**
     * Check single node health (fallback mode)
     */
    private function checkSingleNodeHealth(): array
    {
        $fallbackConfig = $this->config['failover']['fallback_config'];

        return [
            'cluster' => 'fallback',
            'status' => 'healthy',
            'mode' => 'single_node',
            'nodes' => [
                $this->checkNodeHealth($fallbackConfig, 0)
            ],
            'total_nodes' => 1,
            'healthy_nodes' => 1,
            'last_check' => now()->toISOString(),
        ];
    }

    /**
     * Get cluster statistics
     */
    public function getClusterStatistics(string $cluster = 'default'): array
    {
        $stats = [
            'cluster' => $cluster,
            'enabled' => $this->clusterEnabled,
            'total_operations' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
            'memory_usage' => [],
            'performance_metrics' => [],
        ];

        try {
            if (!$this->clusterEnabled) {
                return $this->getSingleNodeStatistics();
            }

            $connection = $this->getConnection($cluster);

            // Get cluster-wide statistics
            $clusterConfig = $this->config['clusters'][$cluster];

            foreach ($clusterConfig['nodes'] as $index => $node) {
                $nodeStats = $this->getNodeStatistics($node, $index);

                $stats['total_operations'] += $nodeStats['total_operations'];
                $stats['cache_hits'] += $nodeStats['cache_hits'];
                $stats['cache_misses'] += $nodeStats['cache_misses'];
                $stats['memory_usage'][] = $nodeStats['memory_usage'];
                $stats['performance_metrics'][] = $nodeStats['performance_metrics'];
            }

            // Calculate hit rate
            $totalRequests = $stats['cache_hits'] + $stats['cache_misses'];
            $stats['hit_rate'] = $totalRequests > 0 ?
                round(($stats['cache_hits'] / $totalRequests) * 100, 2) : 0;

        } catch (Exception $e) {
            Log::error("Failed to get cluster statistics", [
                'cluster' => $cluster,
                'error' => $e->getMessage(),
            ]);
        }

        return $stats;
    }

    /**
     * Get node statistics
     */
    private function getNodeStatistics(array $nodeConfig, int $index): array
    {
        $stats = [
            'index' => $index,
            'host' => $nodeConfig['host'],
            'port' => $nodeConfig['port'],
            'total_operations' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
            'memory_usage' => 0,
            'performance_metrics' => [],
        ];

        try {
            $redis = new \Redis();
            $redis->connect($nodeConfig['host'], $nodeConfig['port'], 3);

            if (isset($nodeConfig['password'])) {
                $redis->auth($nodeConfig['password']);
            }

            $info = $redis->info();

            $stats['total_operations'] = $info['total_commands_processed'] ?? 0;
            $stats['cache_hits'] = $info['keyspace_hits'] ?? 0;
            $stats['cache_misses'] = $info['keyspace_misses'] ?? 0;
            $stats['memory_usage'] = $info['used_memory'] ?? 0;

            $stats['performance_metrics'] = [
                'ops_per_sec' => $info['instantaneous_ops_per_sec'] ?? 0,
                'input_kbps' => $info['instantaneous_input_kbps'] ?? 0,
                'output_kbps' => $info['instantaneous_output_kbps'] ?? 0,
                'connected_clients' => $info['connected_clients'] ?? 0,
            ];

            $redis->close();

        } catch (Exception $e) {
            Log::error("Failed to get node statistics", [
                'node' => $nodeConfig['host'] . ':' . $nodeConfig['port'],
                'error' => $e->getMessage(),
            ]);
        }

        return $stats;
    }

    /**
     * Get single node statistics (fallback mode)
     */
    private function getSingleNodeStatistics(): array
    {
        $fallbackConfig = $this->config['failover']['fallback_config'];
        $nodeStats = $this->getNodeStatistics($fallbackConfig, 0);

        return [
            'cluster' => 'fallback',
            'enabled' => false,
            'mode' => 'single_node',
            'total_operations' => $nodeStats['total_operations'],
            'cache_hits' => $nodeStats['cache_hits'],
            'cache_misses' => $nodeStats['cache_misses'],
            'hit_rate' => $nodeStats['cache_hits'] + $nodeStats['cache_misses'] > 0 ?
                round(($nodeStats['cache_hits'] / ($nodeStats['cache_hits'] + $nodeStats['cache_misses'])) * 100, 2) : 0,
            'memory_usage' => [$nodeStats['memory_usage']],
            'performance_metrics' => [$nodeStats['performance_metrics']],
        ];
    }

    /**
     * Perform cluster failover
     */
    public function performFailover(string $cluster, string $fromNode, string $toNode): bool
    {
        try {
            Log::info("Performing cluster failover", [
                'cluster' => $cluster,
                'from_node' => $fromNode,
                'to_node' => $toNode,
            ]);

            // Implementation would depend on Redis Cluster setup
            // This is a placeholder for actual failover logic

            return true;

        } catch (Exception $e) {
            Log::error("Cluster failover failed", [
                'cluster' => $cluster,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get all cluster health status
     */
    public function getAllClustersHealth(): array
    {
        $allHealth = [];

        foreach (array_keys($this->config['clusters'] ?? []) as $cluster) {
            $allHealth[$cluster] = $this->checkClusterHealth($cluster);
        }

        return $allHealth;
    }

    /**
     * Clear cluster connections
     */
    public function clearConnections(): void
    {
        foreach ($this->connections as $connection) {
            try {
                if (method_exists($connection, 'close')) {
                    $connection->close();
                }
            } catch (Exception $e) {
                Log::warning("Failed to close Redis connection", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->connections = [];
        $this->healthStatus = [];
    }

    /**
     * Check if cluster is enabled
     */
    public function isClusterEnabled(): bool
    {
        return $this->clusterEnabled;
    }

    /**
     * Get cluster configuration
     */
    public function getClusterConfig(string $cluster = null): array
    {
        if ($cluster) {
            return $this->config['clusters'][$cluster] ?? [];
        }

        return $this->config;
    }
}
