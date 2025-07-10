<?php

namespace App\Console\Commands;

use App\Services\ConnectionHealthMonitor;
use App\Services\WebSocketConnectionService;
use Illuminate\Console\Command;

class MonitorWebSocketConnections extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'websocket:monitor 
                            {--cleanup : Clean up stale connections}
                            {--stats : Show connection statistics}
                            {--health : Run health check on all connections}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor WebSocket connections health and performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 WebSocket Connection Monitor');
        $this->newLine();

        try {
            if ($this->option('cleanup')) {
                $this->runCleanup();
            } elseif ($this->option('stats')) {
                $this->showStatistics();
            } elseif ($this->option('health')) {
                $this->runHealthCheck();
            } else {
                $this->runFullMonitoring();
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Monitoring failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Run full monitoring
     */
    private function runFullMonitoring(): void
    {
        $this->info('Running full WebSocket connection monitoring...');
        $this->newLine();

        // Health check
        $this->line('📊 Health Check Results:');
        $healthResults = ConnectionHealthMonitor::monitorAllConnections();
        $this->displayHealthResults($healthResults);
        $this->newLine();

        // Statistics
        $this->line('📈 Connection Statistics:');
        $stats = ConnectionHealthMonitor::getConnectionStatistics();
        $this->displayStatistics($stats);
        $this->newLine();

        // Cleanup
        $this->line('🧹 Cleanup Results:');
        $cleanedUp = ConnectionHealthMonitor::cleanupOldConnections();
        $this->info("Cleaned up {$cleanedUp} old connections");
    }

    /**
     * Run cleanup only
     */
    private function runCleanup(): void
    {
        $this->info('🧹 Cleaning up stale connections...');
        
        $cleanedUp = ConnectionHealthMonitor::cleanupOldConnections();
        $inactiveCleanedUp = WebSocketConnectionService::cleanupInactiveConnections();
        
        $this->info("✅ Cleaned up {$cleanedUp} stale connections");
        $this->info("✅ Cleaned up {$inactiveCleanedUp} inactive connections");
        $this->info("Total cleaned: " . ($cleanedUp + $inactiveCleanedUp));
    }

    /**
     * Show statistics only
     */
    private function showStatistics(): void
    {
        $this->info('📈 WebSocket Connection Statistics');
        $this->newLine();

        $stats = ConnectionHealthMonitor::getConnectionStatistics();
        $this->displayStatistics($stats);

        // Additional online user stats
        $this->newLine();
        $this->line('👥 Online Users:');
        $onlineCount = WebSocketConnectionService::getOnlineUsersCount();
        $this->info("Total online users: {$onlineCount}");

        $roles = ['Admin', 'Moderator', 'Supplier', 'Manufacturer'];
        foreach ($roles as $role) {
            $onlineByRole = WebSocketConnectionService::getOnlineUsersByRole($role);
            $this->line("  {$role}: " . count($onlineByRole));
        }
    }

    /**
     * Run health check only
     */
    private function runHealthCheck(): void
    {
        $this->info('🏥 Running connection health check...');
        $this->newLine();

        $results = ConnectionHealthMonitor::monitorAllConnections();
        $this->displayHealthResults($results);

        if ($results['stale_connections'] > 0 || $results['poor_quality_connections'] > 0) {
            $this->warn('⚠️  Issues detected! Consider running cleanup.');
        } else {
            $this->info('✅ All connections are healthy!');
        }
    }

    /**
     * Display health check results
     */
    private function displayHealthResults(array $results): void
    {
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Connections', $results['total_connections']],
                ['Healthy Connections', $results['healthy_connections']],
                ['Stale Connections', $results['stale_connections']],
                ['Poor Quality Connections', $results['poor_quality_connections']],
                ['Cleaned Up', $results['cleaned_up_connections']],
                ['Users Affected', count($results['users_affected'])],
            ]
        );

        // Health percentage
        if ($results['total_connections'] > 0) {
            $healthPercentage = round(($results['healthy_connections'] / $results['total_connections']) * 100, 1);
            $this->line("Health Score: {$healthPercentage}%");
        }
    }

    /**
     * Display connection statistics
     */
    private function displayStatistics(array $stats): void
    {
        // Basic stats
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Active Connections', $stats['total_active_connections']],
                ['Average Connection Duration', $this->formatDuration($stats['average_connection_duration'])],
                ['Total Reconnections', $stats['total_reconnections']],
            ]
        );

        // Quality distribution
        $this->newLine();
        $this->line('Connection Quality Distribution:');
        foreach ($stats['connections_by_quality'] as $quality => $count) {
            $percentage = $stats['total_active_connections'] > 0 
                ? round(($count / $stats['total_active_connections']) * 100, 1) 
                : 0;
            $this->line("  {$quality}: {$count} ({$percentage}%)");
        }

        // Browser distribution
        if (!empty($stats['connections_by_browser'])) {
            $this->newLine();
            $this->line('Browser Distribution:');
            arsort($stats['connections_by_browser']);
            foreach ($stats['connections_by_browser'] as $browser => $count) {
                $percentage = $stats['total_active_connections'] > 0 
                    ? round(($count / $stats['total_active_connections']) * 100, 1) 
                    : 0;
                $this->line("  {$browser}: {$count} ({$percentage}%)");
            }
        }

        // Platform distribution
        if (!empty($stats['connections_by_platform'])) {
            $this->newLine();
            $this->line('Platform Distribution:');
            arsort($stats['connections_by_platform']);
            foreach ($stats['connections_by_platform'] as $platform => $count) {
                $percentage = $stats['total_active_connections'] > 0 
                    ? round(($count / $stats['total_active_connections']) * 100, 1) 
                    : 0;
                $this->line("  {$platform}: {$count} ({$percentage}%)");
            }
        }
    }

    /**
     * Format duration in seconds to human readable
     */
    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds}s";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return "{$minutes}m {$remainingSeconds}s";
        } else {
            $hours = floor($seconds / 3600);
            $remainingMinutes = floor(($seconds % 3600) / 60);
            return "{$hours}h {$remainingMinutes}m";
        }
    }
}
