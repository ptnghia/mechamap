<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Events\RealTimeNotification;
use App\Events\UserActivityUpdate;
use App\Events\DashboardMetricsUpdate;
use App\Events\ChatMessageSent;
use App\Models\User;
use App\Models\Notification;

/**
 * WebSocket Service
 * Handles real-time communication and WebSocket management
 */
class WebSocketService
{
    protected $connections;
    protected $channels;
    protected $userSessions;

    public function __construct()
    {
        $this->connections = collect();
        $this->channels = collect();
        $this->userSessions = collect();
    }

    /**
     * Broadcast real-time notification to user
     */
    public function broadcastNotification(User $user, Notification $notification): void
    {
        try {
            // Broadcast to user's private channel
            broadcast(new RealTimeNotification($user, $notification))
                ->toOthers();

            // Update notification count in cache
            $this->updateNotificationCount($user->id);

            // Log the broadcast
            Log::info('Real-time notification broadcasted', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'type' => $notification->type,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
            ]);
        }
    }

    /**
     * Broadcast user activity update
     */
    public function broadcastUserActivity(int $userId, array $activity): void
    {
        try {
            broadcast(new UserActivityUpdate($userId, $activity));

            // Update user's last activity
            $this->updateUserLastActivity($userId);

            Log::info('User activity broadcasted', [
                'user_id' => $userId,
                'activity' => $activity['type'] ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast user activity: ' . $e->getMessage(), [
                'user_id' => $userId,
                'activity' => $activity,
            ]);
        }
    }

    /**
     * Broadcast dashboard metrics update
     */
    public function broadcastDashboardMetrics(array $metrics): void
    {
        try {
            broadcast(new DashboardMetricsUpdate($metrics));

            // Cache the latest metrics
            Cache::put('dashboard_metrics_realtime', $metrics, 300); // 5 minutes

            Log::info('Dashboard metrics broadcasted', [
                'metrics_count' => count($metrics),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast dashboard metrics: ' . $e->getMessage());
        }
    }

    /**
     * Broadcast chat message
     */
    public function broadcastChatMessage(int $senderId, int $receiverId, array $message): void
    {
        try {
            broadcast(new ChatMessageSent($senderId, $receiverId, $message))
                ->toOthers();

            // Update conversation last activity
            $this->updateConversationActivity($senderId, $receiverId);

            Log::info('Chat message broadcasted', [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message_id' => $message['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast chat message: ' . $e->getMessage(), [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
            ]);
        }
    }

    /**
     * Get online users count
     */
    public function getOnlineUsersCount(): int
    {
        try {
            return Cache::remember('online_users_count', 60, function () {
                // Get from Redis or WebSocket connections
                return $this->userSessions->count();
            });
        } catch (\Exception $e) {
            Log::error('Failed to get online users count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get user's online status
     */
    public function getUserOnlineStatus(int $userId): bool
    {
        try {
            return Cache::get("user_online_{$userId}", false);
        } catch (\Exception $e) {
            Log::error('Failed to get user online status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set user online status
     */
    public function setUserOnlineStatus(int $userId, bool $online = true): void
    {
        try {
            if ($online) {
                Cache::put("user_online_{$userId}", true, 300); // 5 minutes
                $this->userSessions->put($userId, now());
            } else {
                Cache::forget("user_online_{$userId}");
                $this->userSessions->forget($userId);
            }

            // Broadcast user status change
            $this->broadcastUserActivity($userId, [
                'type' => $online ? 'user_online' : 'user_offline',
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to set user online status: ' . $e->getMessage());
        }
    }

    /**
     * Join user to channel
     */
    public function joinChannel(int $userId, string $channel): void
    {
        try {
            if (!$this->channels->has($channel)) {
                $this->channels->put($channel, collect());
            }

            $this->channels->get($channel)->put($userId, now());

            Log::info('User joined channel', [
                'user_id' => $userId,
                'channel' => $channel,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to join channel: ' . $e->getMessage());
        }
    }

    /**
     * Leave user from channel
     */
    public function leaveChannel(int $userId, string $channel): void
    {
        try {
            if ($this->channels->has($channel)) {
                $this->channels->get($channel)->forget($userId);
            }

            Log::info('User left channel', [
                'user_id' => $userId,
                'channel' => $channel,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to leave channel: ' . $e->getMessage());
        }
    }

    /**
     * Get channel users
     */
    public function getChannelUsers(string $channel): array
    {
        try {
            if (!$this->channels->has($channel)) {
                return [];
            }

            return $this->channels->get($channel)->keys()->toArray();

        } catch (\Exception $e) {
            Log::error('Failed to get channel users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Broadcast to channel
     */
    public function broadcastToChannel(string $channel, array $data): void
    {
        try {
            $users = $this->getChannelUsers($channel);
            
            foreach ($users as $userId) {
                broadcast(new RealTimeNotification(
                    User::find($userId),
                    (object) $data
                ))->toOthers();
            }

            Log::info('Broadcasted to channel', [
                'channel' => $channel,
                'users_count' => count($users),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast to channel: ' . $e->getMessage());
        }
    }

    /**
     * Get real-time statistics
     */
    public function getRealtimeStats(): array
    {
        try {
            return [
                'online_users' => $this->getOnlineUsersCount(),
                'active_channels' => $this->channels->count(),
                'total_connections' => $this->connections->count(),
                'server_uptime' => $this->getServerUptime(),
                'memory_usage' => $this->getMemoryUsage(),
                'last_updated' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get realtime stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(int $userId, int $receiverId, bool $typing = true): void
    {
        try {
            broadcast(new UserActivityUpdate($receiverId, [
                'type' => 'typing_indicator',
                'user_id' => $userId,
                'typing' => $typing,
                'timestamp' => now()->toISOString(),
            ]));

        } catch (\Exception $e) {
            Log::error('Failed to send typing indicator: ' . $e->getMessage());
        }
    }

    /**
     * Broadcast system announcement
     */
    public function broadcastSystemAnnouncement(array $announcement): void
    {
        try {
            broadcast(new RealTimeNotification(null, (object) [
                'type' => 'system_announcement',
                'title' => $announcement['title'],
                'message' => $announcement['message'],
                'level' => $announcement['level'] ?? 'info',
                'timestamp' => now()->toISOString(),
            ]));

            Log::info('System announcement broadcasted', [
                'title' => $announcement['title'],
                'level' => $announcement['level'] ?? 'info',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast system announcement: ' . $e->getMessage());
        }
    }

    // Private helper methods

    private function updateNotificationCount(int $userId): void
    {
        try {
            $count = Notification::where('user_id', $userId)
                ->where('read_at', null)
                ->count();

            Cache::put("user_notification_count_{$userId}", $count, 3600);

        } catch (\Exception $e) {
            Log::error('Failed to update notification count: ' . $e->getMessage());
        }
    }

    private function updateUserLastActivity(int $userId): void
    {
        try {
            Cache::put("user_last_activity_{$userId}", now(), 3600);
            
            // Update database every 5 minutes to avoid too many writes
            $lastUpdate = Cache::get("user_last_db_update_{$userId}");
            if (!$lastUpdate || now()->diffInMinutes($lastUpdate) >= 5) {
                User::where('id', $userId)->update(['last_activity_at' => now()]);
                Cache::put("user_last_db_update_{$userId}", now(), 300);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update user last activity: ' . $e->getMessage());
        }
    }

    private function updateConversationActivity(int $senderId, int $receiverId): void
    {
        try {
            $conversationKey = "conversation_activity_{$senderId}_{$receiverId}";
            Cache::put($conversationKey, now(), 3600);

        } catch (\Exception $e) {
            Log::error('Failed to update conversation activity: ' . $e->getMessage());
        }
    }

    private function getServerUptime(): int
    {
        try {
            $startTime = Cache::get('websocket_server_start_time', now());
            return now()->diffInSeconds($startTime);

        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getMemoryUsage(): array
    {
        try {
            return [
                'used' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit'),
            ];

        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Health check for WebSocket service
     */
    public function healthCheck(): array
    {
        try {
            return [
                'status' => 'healthy',
                'websocket_server' => $this->checkWebSocketServer(),
                'redis_connection' => $this->checkRedisConnection(),
                'cache_system' => $this->checkCacheSystem(),
                'online_users' => $this->getOnlineUsersCount(),
                'active_channels' => $this->channels->count(),
                'timestamp' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    private function checkWebSocketServer(): bool
    {
        try {
            // Check if WebSocket server is running
            return true; // Placeholder - implement actual check
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkRedisConnection(): bool
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheSystem(): bool
    {
        try {
            Cache::put('health_check', 'ok', 10);
            return Cache::get('health_check') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }
}
