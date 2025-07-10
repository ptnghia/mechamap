<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ConnectionManagementService
{
    const CONNECTION_TIMEOUT = 300; // 5 minutes
    const HEARTBEAT_INTERVAL = 30; // 30 seconds
    const MAX_RECONNECT_ATTEMPTS = 5;
    const RECONNECT_DELAY = 2000; // 2 seconds

    /**
     * Register a new WebSocket connection
     */
    public static function registerConnection(User $user, string $socketId, array $connectionInfo = []): bool
    {
        try {
            $connectionData = [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'connected_at' => now()->toISOString(),
                'last_heartbeat' => now()->toISOString(),
                'ip_address' => $connectionInfo['ip_address'] ?? request()->ip(),
                'user_agent' => $connectionInfo['user_agent'] ?? request()->userAgent(),
                'browser' => static::detectBrowser($connectionInfo['user_agent'] ?? ''),
                'platform' => static::detectPlatform($connectionInfo['user_agent'] ?? ''),
                'connection_quality' => 'good',
                'reconnect_count' => 0,
                'status' => 'active',
            ];

            // Store connection in cache
            static::storeConnection($user, $socketId, $connectionData);

            // Update user online status
            RealTimeNotificationService::markUserOnline($user);

            // Log connection
            Log::info('WebSocket connection registered', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'ip_address' => $connectionData['ip_address'],
                'browser' => $connectionData['browser'],
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to register WebSocket connection', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Update connection heartbeat
     */
    public static function updateHeartbeat(User $user, string $socketId, array $metrics = []): bool
    {
        try {
            $connection = static::getConnection($user, $socketId);
            if (!$connection) {
                Log::warning('Heartbeat for unknown connection', [
                    'user_id' => $user->id,
                    'socket_id' => $socketId,
                ]);
                return false;
            }

            // Update heartbeat timestamp
            $connection['last_heartbeat'] = now()->toISOString();

            // Update connection quality if metrics provided
            if (!empty($metrics)) {
                $connection['connection_quality'] = static::calculateConnectionQuality($metrics);
                $connection['latency'] = $metrics['latency'] ?? null;
                $connection['packet_loss'] = $metrics['packet_loss'] ?? null;
            }

            // Store updated connection
            static::storeConnection($user, $socketId, $connection);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update connection heartbeat', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Handle connection disconnection
     */
    public static function handleDisconnection(User $user, string $socketId, string $reason = 'unknown'): void
    {
        try {
            $connection = static::getConnection($user, $socketId);
            if ($connection) {
                // Update connection status
                $connection['status'] = 'disconnected';
                $connection['disconnected_at'] = now()->toISOString();
                $connection['disconnect_reason'] = $reason;

                // Store final connection state
                static::storeConnection($user, $socketId, $connection);

                // Log disconnection
                Log::info('WebSocket connection disconnected', [
                    'user_id' => $user->id,
                    'socket_id' => $socketId,
                    'reason' => $reason,
                    'duration' => static::calculateConnectionDuration($connection),
                ]);
            }

            // Remove from active connections
            static::removeConnection($user, $socketId);

            // Check if user has other active connections
            $activeConnections = static::getActiveConnections($user);
            if (empty($activeConnections)) {
                RealTimeNotificationService::markUserOffline($user);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle connection disconnection', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle connection reconnection
     */
    public static function handleReconnection(User $user, string $oldSocketId, string $newSocketId): bool
    {
        try {
            $oldConnection = static::getConnection($user, $oldSocketId);
            if ($oldConnection) {
                // Increment reconnect count
                $reconnectCount = ($oldConnection['reconnect_count'] ?? 0) + 1;

                // Create new connection with updated info
                $newConnectionData = array_merge($oldConnection, [
                    'socket_id' => $newSocketId,
                    'reconnected_at' => now()->toISOString(),
                    'reconnect_count' => $reconnectCount,
                    'status' => 'active',
                    'last_heartbeat' => now()->toISOString(),
                ]);

                // Remove old connection
                static::removeConnection($user, $oldSocketId);

                // Store new connection
                static::storeConnection($user, $newSocketId, $newConnectionData);

                Log::info('WebSocket connection reconnected', [
                    'user_id' => $user->id,
                    'old_socket_id' => $oldSocketId,
                    'new_socket_id' => $newSocketId,
                    'reconnect_count' => $reconnectCount,
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to handle connection reconnection', [
                'user_id' => $user->id,
                'old_socket_id' => $oldSocketId,
                'new_socket_id' => $newSocketId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get connection information
     */
    public static function getConnection(User $user, string $socketId): ?array
    {
        $cacheKey = "connection_{$user->id}_{$socketId}";
        return Cache::get($cacheKey);
    }

    /**
     * Get all active connections for user
     */
    public static function getActiveConnections(User $user): array
    {
        $pattern = "connection_{$user->id}_*";
        $connections = [];

        try {
            if (static::isRedisAvailable()) {
                $keys = Redis::keys($pattern);
                foreach ($keys as $key) {
                    $connection = Cache::get($key);
                    if ($connection && $connection['status'] === 'active') {
                        $connections[] = $connection;
                    }
                }
            } else {
                // Fallback: get from user sockets cache
                $userConnections = WebSocketConnectionService::getUserActiveConnections($user);
                foreach ($userConnections as $socketId => $connectionInfo) {
                    $connection = static::getConnection($user, $socketId);
                    if ($connection && $connection['status'] === 'active') {
                        $connections[] = $connection;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get active connections', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $connections;
    }

    /**
     * Store connection data
     */
    private static function storeConnection(User $user, string $socketId, array $connectionData): void
    {
        $cacheKey = "connection_{$user->id}_{$socketId}";
        Cache::put($cacheKey, $connectionData, now()->addSeconds(static::CONNECTION_TIMEOUT));

        // Also store in Redis if available
        if (static::isRedisAvailable()) {
            try {
                Redis::setex($cacheKey, static::CONNECTION_TIMEOUT, json_encode($connectionData));
            } catch (\Exception $e) {
                Log::warning('Failed to store connection in Redis', [
                    'user_id' => $user->id,
                    'socket_id' => $socketId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Remove connection data
     */
    private static function removeConnection(User $user, string $socketId): void
    {
        $cacheKey = "connection_{$user->id}_{$socketId}";
        Cache::forget($cacheKey);

        if (static::isRedisAvailable()) {
            try {
                Redis::del($cacheKey);
            } catch (\Exception $e) {
                Log::warning('Failed to remove connection from Redis', [
                    'user_id' => $user->id,
                    'socket_id' => $socketId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Calculate connection quality based on metrics
     */
    private static function calculateConnectionQuality(array $metrics): string
    {
        $latency = $metrics['latency'] ?? 0;
        $packetLoss = $metrics['packet_loss'] ?? 0;

        if ($latency < 100 && $packetLoss < 1) {
            return 'excellent';
        } elseif ($latency < 300 && $packetLoss < 3) {
            return 'good';
        } elseif ($latency < 500 && $packetLoss < 5) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Calculate connection duration
     */
    private static function calculateConnectionDuration(array $connection): int
    {
        $connectedAt = \Carbon\Carbon::parse($connection['connected_at']);
        $disconnectedAt = isset($connection['disconnected_at'])
            ? \Carbon\Carbon::parse($connection['disconnected_at'])
            : now();

        return (int) $disconnectedAt->diffInSeconds($connectedAt);
    }

    /**
     * Detect browser from user agent
     */
    private static function detectBrowser(string $userAgent): string
    {
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';
        return 'Unknown';
    }

    /**
     * Detect platform from user agent
     */
    private static function detectPlatform(string $userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';
        return 'Unknown';
    }

    /**
     * Check if Redis is available
     */
    public static function isRedisAvailable(): bool
    {
        try {
            return class_exists('Redis') &&
                   config('database.redis.default') &&
                   Redis::ping();
        } catch (\Exception $e) {
            return false;
        }
    }
}
