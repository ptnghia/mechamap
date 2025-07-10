<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ConnectionHealthMonitor
{
    const HEALTH_CHECK_INTERVAL = 60; // 1 minute
    const STALE_CONNECTION_THRESHOLD = 300; // 5 minutes
    const POOR_QUALITY_THRESHOLD = 3; // 3 consecutive poor quality checks

    /**
     * Monitor all active connections
     */
    public static function monitorAllConnections(): array
    {
        $results = [
            'total_connections' => 0,
            'healthy_connections' => 0,
            'stale_connections' => 0,
            'poor_quality_connections' => 0,
            'cleaned_up_connections' => 0,
            'users_affected' => [],
        ];

        try {
            $allConnections = static::getAllActiveConnections();
            $results['total_connections'] = count($allConnections);

            foreach ($allConnections as $connection) {
                $healthStatus = static::checkConnectionHealth($connection);
                
                switch ($healthStatus['status']) {
                    case 'healthy':
                        $results['healthy_connections']++;
                        break;
                    case 'stale':
                        $results['stale_connections']++;
                        static::handleStaleConnection($connection);
                        $results['cleaned_up_connections']++;
                        $results['users_affected'][] = $connection['user_id'];
                        break;
                    case 'poor_quality':
                        $results['poor_quality_connections']++;
                        static::handlePoorQualityConnection($connection);
                        break;
                }
            }

            // Remove duplicates from users_affected
            $results['users_affected'] = array_unique($results['users_affected']);

            Log::info('Connection health monitoring completed', $results);

        } catch (\Exception $e) {
            Log::error('Failed to monitor connections', [
                'error' => $e->getMessage(),
            ]);
        }

        return $results;
    }

    /**
     * Check health of a specific connection
     */
    public static function checkConnectionHealth(array $connection): array
    {
        $lastHeartbeat = \Carbon\Carbon::parse($connection['last_heartbeat']);
        $timeSinceHeartbeat = now()->diffInSeconds($lastHeartbeat);
        
        $healthStatus = [
            'status' => 'healthy',
            'issues' => [],
            'metrics' => [
                'time_since_heartbeat' => $timeSinceHeartbeat,
                'connection_quality' => $connection['connection_quality'] ?? 'unknown',
                'reconnect_count' => $connection['reconnect_count'] ?? 0,
            ],
        ];

        // Check for stale connection
        if ($timeSinceHeartbeat > static::STALE_CONNECTION_THRESHOLD) {
            $healthStatus['status'] = 'stale';
            $healthStatus['issues'][] = 'No heartbeat for ' . $timeSinceHeartbeat . ' seconds';
        }

        // Check for poor connection quality
        if (isset($connection['connection_quality']) && $connection['connection_quality'] === 'poor') {
            $poorQualityCount = static::getPoorQualityCount($connection);
            if ($poorQualityCount >= static::POOR_QUALITY_THRESHOLD) {
                $healthStatus['status'] = 'poor_quality';
                $healthStatus['issues'][] = 'Poor connection quality for ' . $poorQualityCount . ' consecutive checks';
            }
        }

        // Check for excessive reconnections
        if (($connection['reconnect_count'] ?? 0) > 10) {
            $healthStatus['issues'][] = 'Excessive reconnections: ' . $connection['reconnect_count'];
        }

        // Check for high latency
        if (isset($connection['latency']) && $connection['latency'] > 1000) {
            $healthStatus['issues'][] = 'High latency: ' . $connection['latency'] . 'ms';
        }

        return $healthStatus;
    }

    /**
     * Handle stale connection
     */
    private static function handleStaleConnection(array $connection): void
    {
        try {
            $user = User::find($connection['user_id']);
            if ($user) {
                // Force disconnect the stale connection
                ConnectionManagementService::handleDisconnection(
                    $user, 
                    $connection['socket_id'], 
                    'stale_connection_cleanup'
                );

                Log::info('Cleaned up stale connection', [
                    'user_id' => $user->id,
                    'socket_id' => $connection['socket_id'],
                    'time_since_heartbeat' => now()->diffInSeconds(\Carbon\Carbon::parse($connection['last_heartbeat'])),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle stale connection', [
                'connection' => $connection,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle poor quality connection
     */
    private static function handlePoorQualityConnection(array $connection): void
    {
        try {
            // Increment poor quality counter
            static::incrementPoorQualityCount($connection);

            // Send connection quality warning to user
            $user = User::find($connection['user_id']);
            if ($user) {
                static::sendConnectionQualityWarning($user, $connection);
            }

            Log::warning('Poor quality connection detected', [
                'user_id' => $connection['user_id'],
                'socket_id' => $connection['socket_id'],
                'connection_quality' => $connection['connection_quality'],
                'latency' => $connection['latency'] ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle poor quality connection', [
                'connection' => $connection,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get all active connections
     */
    private static function getAllActiveConnections(): array
    {
        $connections = [];

        try {
            // Get all connection cache keys
            $pattern = 'connection_*';
            
            if (ConnectionManagementService::isRedisAvailable()) {
                $keys = \Redis::keys($pattern);
                foreach ($keys as $key) {
                    $connection = Cache::get($key);
                    if ($connection && $connection['status'] === 'active') {
                        $connections[] = $connection;
                    }
                }
            } else {
                // Fallback: scan cache for connection keys
                // This is less efficient but works without Redis
                $users = User::whereNotNull('id')->get();
                foreach ($users as $user) {
                    $userConnections = ConnectionManagementService::getActiveConnections($user);
                    $connections = array_merge($connections, $userConnections);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to get all active connections', [
                'error' => $e->getMessage(),
            ]);
        }

        return $connections;
    }

    /**
     * Get poor quality count for connection
     */
    private static function getPoorQualityCount(array $connection): int
    {
        $cacheKey = "poor_quality_count_{$connection['user_id']}_{$connection['socket_id']}";
        return Cache::get($cacheKey, 0);
    }

    /**
     * Increment poor quality count
     */
    private static function incrementPoorQualityCount(array $connection): void
    {
        $cacheKey = "poor_quality_count_{$connection['user_id']}_{$connection['socket_id']}";
        $count = Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $count, now()->addHours(1));
    }

    /**
     * Send connection quality warning to user
     */
    private static function sendConnectionQualityWarning(User $user, array $connection): void
    {
        try {
            // Check if we've already sent a warning recently
            $warningCacheKey = "connection_warning_{$user->id}";
            if (Cache::has($warningCacheKey)) {
                return; // Don't spam warnings
            }

            // Send notification about poor connection
            $notificationData = [
                'type' => 'connection_quality_warning',
                'title' => 'Chất lượng kết nối kém',
                'message' => 'Kết nối của bạn đang gặp vấn đề. Một số thông báo có thể bị trễ.',
                'data' => [
                    'connection_quality' => $connection['connection_quality'],
                    'latency' => $connection['latency'] ?? null,
                    'suggestions' => [
                        'Kiểm tra kết nối internet',
                        'Thử refresh trang',
                        'Đóng các tab không cần thiết',
                    ],
                ],
                'priority' => 'normal',
            ];

            RealTimeNotificationService::sendToUser($user, $notificationData);

            // Cache warning to prevent spam
            Cache::put($warningCacheKey, true, now()->addMinutes(30));

        } catch (\Exception $e) {
            Log::error('Failed to send connection quality warning', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get connection statistics
     */
    public static function getConnectionStatistics(): array
    {
        $stats = [
            'total_active_connections' => 0,
            'connections_by_quality' => [
                'excellent' => 0,
                'good' => 0,
                'fair' => 0,
                'poor' => 0,
                'unknown' => 0,
            ],
            'connections_by_browser' => [],
            'connections_by_platform' => [],
            'average_connection_duration' => 0,
            'total_reconnections' => 0,
        ];

        try {
            $connections = static::getAllActiveConnections();
            $stats['total_active_connections'] = count($connections);

            $totalDuration = 0;
            $totalReconnections = 0;

            foreach ($connections as $connection) {
                // Quality stats
                $quality = $connection['connection_quality'] ?? 'unknown';
                $stats['connections_by_quality'][$quality]++;

                // Browser stats
                $browser = $connection['browser'] ?? 'Unknown';
                $stats['connections_by_browser'][$browser] = ($stats['connections_by_browser'][$browser] ?? 0) + 1;

                // Platform stats
                $platform = $connection['platform'] ?? 'Unknown';
                $stats['connections_by_platform'][$platform] = ($stats['connections_by_platform'][$platform] ?? 0) + 1;

                // Duration calculation
                $connectedAt = \Carbon\Carbon::parse($connection['connected_at']);
                $duration = now()->diffInSeconds($connectedAt);
                $totalDuration += $duration;

                // Reconnection stats
                $totalReconnections += $connection['reconnect_count'] ?? 0;
            }

            if (count($connections) > 0) {
                $stats['average_connection_duration'] = round($totalDuration / count($connections));
            }
            $stats['total_reconnections'] = $totalReconnections;

        } catch (\Exception $e) {
            Log::error('Failed to get connection statistics', [
                'error' => $e->getMessage(),
            ]);
        }

        return $stats;
    }

    /**
     * Clean up old connection data
     */
    public static function cleanupOldConnections(): int
    {
        $cleanedUp = 0;

        try {
            $connections = static::getAllActiveConnections();
            $cutoffTime = now()->subSeconds(static::STALE_CONNECTION_THRESHOLD * 2);

            foreach ($connections as $connection) {
                $lastHeartbeat = \Carbon\Carbon::parse($connection['last_heartbeat']);
                
                if ($lastHeartbeat->isBefore($cutoffTime)) {
                    $user = User::find($connection['user_id']);
                    if ($user) {
                        ConnectionManagementService::handleDisconnection(
                            $user,
                            $connection['socket_id'],
                            'cleanup_old_connection'
                        );
                        $cleanedUp++;
                    }
                }
            }

            Log::info('Cleaned up old connections', [
                'cleaned_up_count' => $cleanedUp,
                'cutoff_time' => $cutoffTime->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old connections', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleanedUp;
    }
}
