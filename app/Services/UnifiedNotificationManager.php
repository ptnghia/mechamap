<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Events\NotificationBroadcastEvent;

/**
 * Unified Notification Manager
 * Dual-table architecture for user and system notifications
 * - custom_notifications: User notifications (public)
 * - notifications: System/Admin notifications (restricted)
 */
class UnifiedNotificationManager
{
    /**
     * Notification categories
     */
    const CATEGORIES = [
        'system' => 'Hệ thống',
        'forum' => 'Diễn đàn',
        'marketplace' => 'Thương mại',
        'social' => 'Xã hội',
        'security' => 'Bảo mật'
    ];

    /**
     * Priority levels
     */
    const PRIORITIES = [
        'low' => 1,
        'normal' => 2,
        'high' => 3,
        'urgent' => 4
    ];

    /**
     * Delivery channels
     */
    const CHANNELS = [
        'database' => 'Database',
        'websocket' => 'Real-time',
        'email' => 'Email',
        'push' => 'Push Notification'
    ];

    /**
     * System notification types (stored in Laravel notifications table)
     * Only accessible by super admin
     */
    const SYSTEM_NOTIFICATION_TYPES = [
        'email_verification',
        'password_reset',
        'system_maintenance',
        'security_incident',
        'admin_alert',
        'backup_completed',
        'backup_failed',
        'system_error',
        'performance_alert',
        'database_maintenance',
        'server_status',
        'application_deployment',
        'critical_system_event'
    ];

    /**
     * Send unified notification
     * Automatically routes to appropriate table based on notification type
     *
     * @param User|array $users Target users
     * @param string $type Notification type
     * @param string $title Notification title
     * @param string $message Notification message
     * @param array $options Additional options
     * @return bool Success status
     */
    public static function send(
        $users,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): bool {
        try {
            if (!is_array($users)) {
                $users = [$users];
            }

            // Check if this is a system notification
            $isSystemNotification = in_array($type, self::SYSTEM_NOTIFICATION_TYPES);

            $defaultOptions = [
                'category' => self::inferCategoryFromType($type),
                'priority' => 'normal',
                'urgency_level' => 2,
                'data' => [],
                'metadata' => [],
                'channels' => ['database', 'websocket'],
                'action_url' => null,
                'action_text' => null,
                'requires_action' => false,
                'scheduled_at' => null,
                'expires_at' => null,
                'batch_id' => null,
                'notifiable_type' => null,
                'notifiable_id' => null,
                'use_laravel_notifications' => $isSystemNotification,
            ];

            $options = array_merge($defaultOptions, $options);
            $batchId = $options['batch_id'] ?? uniqid('batch_');

            foreach ($users as $user) {
                if (!$user instanceof User) {
                    continue;
                }

                if ($options['use_laravel_notifications']) {
                    // Send via Laravel built-in notifications (system notifications)
                    self::sendSystemNotification($user, $type, $title, $message, $options);
                } else {
                    // Send via custom notifications (user notifications)
                    $notification = self::createNotification($user, $type, $title, $message, $options, $batchId);

                    if ($notification) {
                        self::processDeliveryChannels($user, $notification, $options['channels']);
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Unified notification send failed', [
                'error' => $e->getMessage(),
                'type' => $type,
                'users_count' => count($users)
            ]);
            return false;
        }
    }

    /**
     * Create notification record
     */
    private static function createNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        array $options,
        string $batchId
    ): ?Notification {
        try {
            return Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'category' => $options['category'],
                'title' => $title,
                'message' => $message,
                'data' => $options['data'],
                'metadata' => array_merge($options['metadata'], [
                    'created_by' => 'unified_manager',
                    'version' => '2.0',
                    'source' => request()->ip() ?? 'system'
                ]),
                'priority' => $options['priority'],
                'urgency_level' => self::mapPriorityToUrgencyLevel($options['priority']),
                'status' => $options['scheduled_at'] ? 'pending' : 'sent',
                'notifiable_type' => $options['notifiable_type'],
                'notifiable_id' => $options['notifiable_id'],
                'action_url' => $options['action_url'],
                'action_text' => $options['action_text'],
                'requires_action' => $options['requires_action'],
                'delivery_channels' => $options['channels'],
                'scheduled_at' => $options['scheduled_at'],
                'expires_at' => $options['expires_at'],
                'batch_id' => $batchId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Process delivery channels
     */
    private static function processDeliveryChannels(User $user, Notification $notification, array $channels): void
    {
        $sentVia = [];

        foreach ($channels as $channel) {
            try {
                switch ($channel) {
                    case 'websocket':
                        if (self::sendWebSocket($user, $notification)) {
                            $sentVia[] = 'websocket';
                        }
                        break;

                    case 'email':
                        if (self::sendEmail($user, $notification)) {
                            $sentVia[] = 'email';
                        }
                        break;

                    case 'push':
                        if (self::sendPushNotification($user, $notification)) {
                            $sentVia[] = 'push';
                        }
                        break;

                    case 'database':
                        $sentVia[] = 'database'; // Already saved
                        break;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to send via {$channel}", [
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Update sent_via field
        $notification->update(['sent_via' => $sentVia]);

        // Update cache
        self::updateUserNotificationCache($user);
    }

    /**
     * Send WebSocket notification
     */
    private static function sendWebSocket(User $user, Notification $notification): bool
    {
        try {
            $websocketUrl = config('websocket.server.url');
            $apiKey = config('websocket.api_key');

            if (!$websocketUrl || !$apiKey) {
                return false;
            }

            $payload = [
                'event' => 'notification',
                'user_id' => $user->id,
                'channel' => 'user.' . $user->id,
                'priority' => $notification->priority,
                'data' => [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'category' => $notification->category,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'requires_action' => $notification->requires_action,
                    'created_at' => $notification->created_at->toISOString(),
                ],
                'metadata' => [
                    'unread_count' => self::getUnreadCount($user),
                    'source' => 'unified_manager'
                ]
            ];

            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($websocketUrl . '/api/broadcast', $payload);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('WebSocket notification failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email notification
     */
    private static function sendEmail(User $user, Notification $notification): bool
    {
        try {
            // Check if user has email notifications enabled
            if (!$user->email_notifications_enabled) {
                return false;
            }

            // Queue email job based on priority
            $queue = $notification->priority === 'urgent' ? 'emails-urgent' : 'emails-normal';

            // Dispatch email job (implementation depends on your email system)
            // dispatch(new SendNotificationEmail($user, $notification))->onQueue($queue);

            return true;

        } catch (\Exception $e) {
            Log::error('Email notification failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send push notification
     */
    private static function sendPushNotification(User $user, Notification $notification): bool
    {
        try {
            // Implementation for push notifications
            // This would integrate with FCM, APNs, or other push services
            return true;

        } catch (\Exception $e) {
            Log::error('Push notification failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user's unread notification count
     */
    public static function getUnreadCount(User $user): int
    {
        $cacheKey = "user_notifications_unread_count_{$user->id}";

        return Cache::remember($cacheKey, 300, function () use ($user) {
            return $user->userNotifications()->where('is_read', false)->count();
        });
    }

    /**
     * Update user notification cache
     */
    private static function updateUserNotificationCache(User $user): void
    {
        $cacheKey = "user_notifications_unread_count_{$user->id}";
        Cache::forget($cacheKey);

        // Refresh cache
        self::getUnreadCount($user);
    }

    /**
     * Infer category from notification type
     */
    private static function inferCategoryFromType(string $type): string
    {
        return match(true) {
            str_contains($type, 'thread_') || str_contains($type, 'comment_') => 'forum',
            str_contains($type, 'order_') || str_contains($type, 'product_') || str_contains($type, 'payment_') => 'marketplace',
            str_contains($type, 'message_') || str_contains($type, 'user_') || str_contains($type, 'follow') => 'social',
            str_contains($type, 'security_') || str_contains($type, 'login_') || str_contains($type, 'password_') => 'security',
            default => 'system'
        };
    }

    /**
     * Send system notification via Laravel built-in notifications
     */
    private static function sendSystemNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        array $options
    ): void {
        try {
            // Create Laravel notification
            $user->notify(new \App\Notifications\SystemNotification([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $options['data'],
                'priority' => $options['priority'],
                'action_url' => $options['action_url'],
                'metadata' => array_merge($options['metadata'], [
                    'created_by' => 'unified_manager',
                    'system_notification' => true,
                    'restricted_access' => true
                ])
            ]));

            Log::info('System notification sent', [
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send system notification', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user notifications (custom_notifications only)
     * System notifications are excluded for regular users
     */
    public static function getUserNotifications(User $user, int $page = 1, int $perPage = 15)
    {
        return $user->userNotifications()
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get system notifications (Laravel notifications only)
     * Only accessible by super admin
     */
    public static function getSystemNotifications(User $user, int $page = 1, int $perPage = 15)
    {
        // Check if user is super admin
        if (!self::isSuperAdmin($user)) {
            throw new \Exception('Access denied. Only super admin can view system notifications.');
        }

        return $user->notifications()
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get all notifications for super admin (both tables)
     */
    public static function getAllNotifications(User $user, int $page = 1, int $perPage = 15)
    {
        if (!self::isSuperAdmin($user)) {
            // Regular users only get custom notifications
            return self::getUserNotifications($user, $page, $perPage);
        }

        // Super admin gets both custom and system notifications
        // This would require a more complex query or separate handling
        return [
            'user_notifications' => self::getUserNotifications($user, $page, $perPage),
            'system_notifications' => self::getSystemNotifications($user, $page, $perPage)
        ];
    }

    /**
     * Check if user is super admin
     */
    private static function isSuperAdmin(User $user): bool
    {
        return $user->role === 'super_admin' ||
               (method_exists($user, 'hasRole') && $user->hasRole('super_admin'));
    }

    /**
     * Map priority to urgency level
     */
    private static function mapPriorityToUrgencyLevel(string $priority): int
    {
        return match($priority) {
            'urgent' => 5,
            'high' => 4,
            'normal' => 3,
            'low' => 2,
            default => 1
        };
    }
}
