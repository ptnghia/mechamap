<?php

namespace App\Console\Commands;

use App\Services\RedisClusterManager;
use App\Services\EnhancedNotificationCacheService;
use Illuminate\Console\Command;

class RedisClusterMonitor extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'redis-cluster:monitor 
                            {--health : Check cluster health}
                            {--stats : Show cluster statistics}
                            {--warmup : Warm up cache}
                            {--cleanup : Clean up expired cache}
                            {--cluster= : Specific cluster to monitor}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor and manage Redis cluster for notifications';

    private RedisClusterManager $clusterManager;
    private EnhancedNotificationCacheService $cacheService;

    public function __construct(RedisClusterManager $clusterManager, EnhancedNotificationCacheService $cacheService)
    {
        parent::__construct();
        $this->clusterManager = $clusterManager;
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 Redis Cluster Monitor for MechaMap Notifications');
        $this->newLine();

        if ($this->option('health')) {
            return $this->checkHealth();
        }

        if ($this->option('stats')) {
            return $this->showStatistics();
        }

        if ($this->option('warmup')) {
            return $this->warmUpCache();
        }

        if ($this->option('cleanup')) {
            return $this->cleanupCache();
        }

        // Default: show overview
        return $this->showOverview();
    }

    /**
     * Check cluster health
     */
    private function checkHealth(): int
    {
        try {
            $cluster = $this->option('cluster');
            
            if ($cluster) {
                $health = $this->clusterManager->checkClusterHealth($cluster);
                $this->displayClusterHealth($cluster, $health);
            } else {
                $allHealth = $this->clusterManager->getAllClustersHealth();
                
                foreach ($allHealth as $clusterName => $health) {
                    $this->displayClusterHealth($clusterName, $health);
                    $this->newLine();
                }
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Health check failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display cluster health information
     */
    private function displayClusterHealth(string $clusterName, array $health): void
    {
        if ($this->option('json')) {
            $this->line(json_encode($health, JSON_PRETTY_PRINT));
            return;
        }

        $statusColor = match($health['status']) {
            'healthy' => 'green',
            'degraded' => 'yellow',
            'critical' => 'red',
            default => 'gray'
        };

        $this->line("📊 <fg=blue>Cluster:</> <fg=white>{$clusterName}</>");
        $this->line("🔍 <fg=blue>Status:</> <fg={$statusColor}>{$health['status']}</>");
        $this->line("🖥️  <fg=blue>Total Nodes:</> {$health['total_nodes']}");
        $this->line("✅ <fg=blue>Healthy Nodes:</> {$health['healthy_nodes']}");
        
        if (isset($health['master_nodes'])) {
            $this->line("👑 <fg=blue>Master Nodes:</> {$health['master_nodes']}");
            $this->line("🔄 <fg=blue>Slave Nodes:</> {$health['slave_nodes']}");
        }

        if (!empty($health['nodes'])) {
            $this->newLine();
            $this->line("📋 <fg=blue>Node Details:</>");
            
            $headers = ['Index', 'Host:Port', 'Status', 'Role', 'Memory (MB)', 'Response (ms)'];
            $rows = [];
            
            foreach ($health['nodes'] as $node) {
                $memoryMB = isset($node['memory_usage']) ? round($node['memory_usage'] / 1024 / 1024, 2) : 0;
                
                $rows[] = [
                    $node['index'],
                    $node['host'] . ':' . $node['port'],
                    $node['status'],
                    $node['role'] ?? 'unknown',
                    $memoryMB,
                    $node['response_time'] ?? 0,
                ];
            }
            
            $this->table($headers, $rows);
        }
    }

    /**
     * Show cluster statistics
     */
    private function showStatistics(): int
    {
        try {
            $cluster = $this->option('cluster');
            
            if ($cluster) {
                $stats = $this->clusterManager->getClusterStatistics($cluster);
                $this->displayClusterStatistics($cluster, $stats);
            } else {
                $clusters = ['default', 'notifications', 'analytics', 'sessions'];
                
                foreach ($clusters as $clusterName) {
                    try {
                        $stats = $this->clusterManager->getClusterStatistics($clusterName);
                        $this->displayClusterStatistics($clusterName, $stats);
                        $this->newLine();
                    } catch (\Exception $e) {
                        $this->warn("Could not get statistics for cluster: {$clusterName}");
                    }
                }
            }

            // Show cache statistics
            $this->showCacheStatistics();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Statistics failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display cluster statistics
     */
    private function displayClusterStatistics(string $clusterName, array $stats): void
    {
        if ($this->option('json')) {
            $this->line(json_encode($stats, JSON_PRETTY_PRINT));
            return;
        }

        $this->line("📈 <fg=blue>Cluster Statistics:</> <fg=white>{$clusterName}</>");
        $this->line("🔧 <fg=blue>Enabled:</> " . ($stats['enabled'] ? 'Yes' : 'No'));
        $this->line("⚡ <fg=blue>Total Operations:</> " . number_format($stats['total_operations']));
        $this->line("✅ <fg=blue>Cache Hits:</> " . number_format($stats['cache_hits']));
        $this->line("❌ <fg=blue>Cache Misses:</> " . number_format($stats['cache_misses']));
        
        if (isset($stats['hit_rate'])) {
            $hitRateColor = $stats['hit_rate'] >= 80 ? 'green' : ($stats['hit_rate'] >= 60 ? 'yellow' : 'red');
            $this->line("🎯 <fg=blue>Hit Rate:</> <fg={$hitRateColor}>{$stats['hit_rate']}%</>");
        }

        if (!empty($stats['memory_usage'])) {
            $totalMemory = array_sum($stats['memory_usage']);
            $memoryMB = round($totalMemory / 1024 / 1024, 2);
            $this->line("💾 <fg=blue>Total Memory:</> {$memoryMB} MB");
        }
    }

    /**
     * Show cache statistics
     */
    private function showCacheStatistics(): void
    {
        try {
            $cacheStats = $this->cacheService->getCacheStatistics();
            
            $this->line("🗄️  <fg=blue>Cache Overview:</>");
            $this->line("💾 <fg=blue>Total Memory:</> " . round($cacheStats['total_memory_usage'] / 1024 / 1024, 2) . " MB");
            
            if (!empty($cacheStats['clusters'])) {
                foreach ($cacheStats['clusters'] as $cluster => $stats) {
                    if (isset($stats['hit_rate'])) {
                        $hitRateColor = $stats['hit_rate'] >= 80 ? 'green' : ($stats['hit_rate'] >= 60 ? 'yellow' : 'red');
                        $this->line("  📊 <fg=blue>{$cluster}:</> <fg={$hitRateColor}>{$stats['hit_rate']}% hit rate</>");
                    }
                }
            }

        } catch (\Exception $e) {
            $this->warn('Could not retrieve cache statistics: ' . $e->getMessage());
        }
    }

    /**
     * Warm up cache
     */
    private function warmUpCache(): int
    {
        try {
            $this->info('🔥 Starting cache warm-up...');
            
            $success = $this->cacheService->warmUpCache();
            
            if ($success) {
                $this->info('✅ Cache warm-up completed successfully');
                return Command::SUCCESS;
            } else {
                $this->error('❌ Cache warm-up failed');
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Cache warm-up failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Clean up cache
     */
    private function cleanupCache(): int
    {
        try {
            $this->info('🧹 Starting cache cleanup...');
            
            $cleanedUp = $this->cacheService->cleanupExpiredCache();
            
            $this->info("✅ Cache cleanup completed. Cleaned up {$cleanedUp} entries");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Cache cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Show overview
     */
    private function showOverview(): int
    {
        $this->info('📊 Redis Cluster Overview');
        $this->newLine();

        // Check if cluster is enabled
        $clusterEnabled = $this->clusterManager->isClusterEnabled();
        $this->line("🔧 <fg=blue>Cluster Mode:</> " . ($clusterEnabled ? '<fg=green>Enabled</>' : '<fg=yellow>Disabled (Fallback Mode)</>'));
        
        if ($clusterEnabled) {
            $this->line("🎯 <fg=blue>Available Commands:</>");
            $this->line("  • <fg=cyan>--health</> : Check cluster health");
            $this->line("  • <fg=cyan>--stats</> : Show detailed statistics");
            $this->line("  • <fg=cyan>--warmup</> : Warm up cache with frequent data");
            $this->line("  • <fg=cyan>--cleanup</> : Clean up expired cache entries");
            $this->line("  • <fg=cyan>--cluster=name</> : Target specific cluster");
            $this->line("  • <fg=cyan>--json</> : Output in JSON format");
        } else {
            $this->warn('Running in single-node fallback mode. Consider enabling Redis Cluster for high availability.');
        }

        $this->newLine();
        $this->line("💡 <fg=blue>Example usage:</>");
        $this->line("  <fg=gray>php artisan redis-cluster:monitor --health</>");
        $this->line("  <fg=gray>php artisan redis-cluster:monitor --stats --cluster=notifications</>");
        $this->line("  <fg=gray>php artisan redis-cluster:monitor --warmup</>");

        return Command::SUCCESS;
    }
}
