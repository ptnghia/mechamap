<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class WebSocketConnectionService
{
    /**
     * Handle user connection
     */
    public static function handleUserConnect(User $user, string $socketId): void
    {
        try {
            // Mark user as online
            RealTimeNotificationService::markUserOnline($user);

            // Store socket connection
            static::storeSocketConnection($user, $socketId);

            // Deliver offline notifications
            $deliveredCount = RealTimeNotificationService::deliverOfflineNotifications($user);

            // Log connection
            Log::info('User connected to WebSocket', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'offline_notifications_delivered' => $deliveredCount,
            ]);

            // Broadcast user online status to relevant channels
            static::broadcastUserOnlineStatus($user, true);

        } catch (\Exception $e) {
            Log::error('Failed to handle user WebSocket connection', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle user disconnection
     */
    public static function handleUserDisconnect(User $user, string $socketId): void
    {
        try {
            // Remove socket connection
            static::removeSocketConnection($user, $socketId);

            // Check if user has other active connections
            $activeConnections = static::getUserActiveConnections($user);

            if (empty($activeConnections)) {
                // Mark user as offline if no active connections
                RealTimeNotificationService::markUserOffline($user);

                // Broadcast user offline status
                static::broadcastUserOnlineStatus($user, false);
            }

            Log::info('User disconnected from WebSocket', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'remaining_connections' => count($activeConnections),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle user WebSocket disconnection', [
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store socket connection
     */
    private static function storeSocketConnection(User $user, string $socketId): void
    {
        $cacheKey = "user_sockets_{$user->id}";
        $connections = Cache::get($cacheKey, []);

        $connections[$socketId] = [
            'socket_id' => $socketId,
            'connected_at' => now()->toISOString(),
            'last_activity' => now()->toISOString(),
        ];

        Cache::put($cacheKey, $connections, now()->addHours(1));

        // Store in Redis for WebSocket server (if Redis is available)
        if (static::isRedisAvailable()) {
            try {
                Redis::hset("user_sockets_{$user->id}", $socketId, json_encode($connections[$socketId]));
                Redis::expire("user_sockets_{$user->id}", 3600);
            } catch (\Exception $e) {
                Log::warning('Failed to store socket connection in Redis', [
                    'user_id' => $user->id,
                    'socket_id' => $socketId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Remove socket connection
     */
    private static function removeSocketConnection(User $user, string $socketId): void
    {
        $cacheKey = "user_sockets_{$user->id}";
        $connections = Cache::get($cacheKey, []);

        unset($connections[$socketId]);

        if (empty($connections)) {
            Cache::forget($cacheKey);
        } else {
            Cache::put($cacheKey, $connections, now()->addHours(1));
        }

        // Remove from Redis (if Redis is available)
        if (static::isRedisAvailable()) {
            try {
                Redis::hdel("user_sockets_{$user->id}", $socketId);

                // Remove hash if empty
                if (Redis::hlen("user_sockets_{$user->id}") === 0) {
                    Redis::del("user_sockets_{$user->id}");
                }
            } catch (\Exception $e) {
                Log::warning('Failed to remove socket connection from Redis', [
                    'user_id' => $user->id,
                    'socket_id' => $socketId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get user's active socket connections
     */
    public static function getUserActiveConnections(User $user): array
    {
        $cacheKey = "user_sockets_{$user->id}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Update socket activity
     */
    public static function updateSocketActivity(User $user, string $socketId): void
    {
        $cacheKey = "user_sockets_{$user->id}";
        $connections = Cache::get($cacheKey, []);

        if (isset($connections[$socketId])) {
            $connections[$socketId]['last_activity'] = now()->toISOString();
            Cache::put($cacheKey, $connections, now()->addHours(1));

            // Update in Redis (if Redis is available)
            if (static::isRedisAvailable()) {
                try {
                    Redis::hset("user_sockets_{$user->id}", $socketId, json_encode($connections[$socketId]));
                } catch (\Exception $e) {
                    Log::warning('Failed to update socket activity in Redis', [
                        'user_id' => $user->id,
                        'socket_id' => $socketId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Broadcast user online status
     */
    private static function broadcastUserOnlineStatus(User $user, bool $isOnline): void
    {
        try {
            // Broadcast to admin dashboard
            broadcast(new \App\Events\UserOnlineStatusChanged($user, $isOnline))
                ->toOthers();

            // Broadcast to user's contacts/followers if applicable
            static::broadcastToUserContacts($user, $isOnline);

        } catch (\Exception $e) {
            Log::warning('Failed to broadcast user online status', [
                'user_id' => $user->id,
                'is_online' => $isOnline,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast to user's contacts
     */
    private static function broadcastToUserContacts(User $user, bool $isOnline): void
    {
        // This would depend on your social features implementation
        // For now, we'll just log it
        Log::debug('User online status changed', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_online' => $isOnline,
        ]);
    }

    /**
     * Clean up inactive connections
     */
    public static function cleanupInactiveConnections(): int
    {
        $cleanedUp = 0;
        $cutoffTime = now()->subMinutes(30);

        try {
            // Get all user socket keys from cache
            $pattern = 'user_sockets_*';
            $keys = static::isRedisAvailable() ? Redis::keys($pattern) : [];

            foreach ($keys as $key) {
                $connections = Cache::get($key, []);
                $activeConnections = [];

                foreach ($connections as $socketId => $connection) {
                    $lastActivity = \Carbon\Carbon::parse($connection['last_activity']);

                    if ($lastActivity->isAfter($cutoffTime)) {
                        $activeConnections[$socketId] = $connection;
                    } else {
                        $cleanedUp++;
                    }
                }

                if (empty($activeConnections)) {
                    Cache::forget($key);

                    // Extract user ID and mark as offline
                    $userId = str_replace('user_sockets_', '', $key);
                    if (is_numeric($userId)) {
                        $user = User::find($userId);
                        if ($user) {
                            RealTimeNotificationService::markUserOffline($user);
                        }
                    }
                } else {
                    Cache::put($key, $activeConnections, now()->addHours(1));
                }
            }

            Log::info('Cleaned up inactive WebSocket connections', [
                'cleaned_up_count' => $cleanedUp,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup inactive connections', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleanedUp;
    }

    /**
     * Get online users count
     */
    public static function getOnlineUsersCount(): int
    {
        try {
            $pattern = 'user_online_*';
            $keys = static::isRedisAvailable() ? Redis::keys($pattern) : [];
            return count($keys);
        } catch (\Exception $e) {
            Log::warning('Failed to get online users count', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get online users by role
     */
    public static function getOnlineUsersByRole(string $role): array
    {
        $onlineUsers = [];

        try {
            $users = User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })->get();

            foreach ($users as $user) {
                if (RealTimeNotificationService::isUserOnline($user)) {
                    $onlineUsers[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'connections' => count(static::getUserActiveConnections($user)),
                    ];
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to get online users by role', [
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }

        return $onlineUsers;
    }

    /**
     * Check if Redis is available
     */
    private static function isRedisAvailable(): bool
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
