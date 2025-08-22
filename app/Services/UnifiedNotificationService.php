<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification as CustomNotification;
use App\Models\NotificationTemplate;
use App\Models\NotificationPreference;
use App\Models\NotificationLog;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Unified Notification Service
 *
 * Provides a unified interface for both custom and Laravel built-in notification systems
 * Allows gradual migration from custom to Laravel built-in notifications
 */
class UnifiedNotificationService
{
    /**
     * Send notification using enhanced unified system
     *
     * @param User|array $users
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @param array $requestedChannels
     * @return bool
     */
    public static function send($users, string $type, string $title, string $message, array $data = [], array $requestedChannels = ['database']): bool
    {
        try {
            if (!is_array($users)) {
                $users = [$users];
            }

            $successCount = 0;
            $totalAttempts = 0;

            foreach ($users as $user) {
                if (!$user instanceof User) {
                    continue;
                }

                // Get user's enabled channels for this notification type
                $enabledChannels = NotificationPreference::getEnabledChannels($user->id, $type);

                // Intersect with requested channels
                $channelsToSend = array_intersect($requestedChannels, $enabledChannels);

                if (empty($channelsToSend)) {
                    Log::info("User {$user->id} has disabled all requested channels for notification type: {$type}");
                    continue;
                }

                $userSuccess = true;

                // Send to each enabled channel
                foreach ($channelsToSend as $channel) {
                    $totalAttempts++;

                    try {
                        switch ($channel) {
                            case 'database':
                                self::sendDatabaseNotification($user, $type, $title, $message, $data);
                                break;

                            case 'email':
                            case 'mail':
                                self::sendEmailNotification($user, $type, $title, $message, $data);
                                break;

                            case 'broadcast':
                            case 'websocket':
                                self::sendBroadcastNotification($user, $type, $title, $message, $data);
                                break;

                            default:
                                Log::warning("Unknown notification channel: {$channel}");
                                continue 2;
                        }

                        $successCount++;

                    } catch (\Exception $e) {
                        $userSuccess = false;
                        Log::error("Failed to send {$channel} notification to user {$user->id}: " . $e->getMessage());

                        // Log the failure
                        NotificationLog::logDelivery(
                            null,
                            $type,
                            $channel,
                            User::class,
                            $user->id,
                            'failed',
                            ['title' => $title, 'message' => $message],
                            $e->getMessage()
                        );
                    }
                }

                if ($userSuccess) {
                    Log::info("Successfully sent notification to user {$user->id} via channels: " . implode(', ', $channelsToSend));
                }
            }

            $successRate = $totalAttempts > 0 ? ($successCount / $totalAttempts) * 100 : 0;
            Log::info("Notification batch completed: {$successCount}/{$totalAttempts} successful ({$successRate}%)");

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Unified notification send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send database notification (primary notification system)
     */
    private static function sendDatabaseNotification(User $user, string $type, string $title, string $message, array $data): void
    {
        try {
            // Check if user wants to receive this notification type
            $enabledChannels = NotificationPreference::getEnabledChannels($user->id, $type);

            if (empty($enabledChannels)) {
                Log::info("User {$user->id} has disabled all channels for notification type: {$type}");
                return;
            }

            // Auto-determine category based on type
            $category = self::getCategoryFromType($type);

            // Auto-determine priority and urgency
            $priority = $data['priority'] ?? self::getPriorityFromType($type);
            $urgencyLevel = self::getUrgencyLevelFromType($type);



            // Try to get template for this notification type
            $template = NotificationTemplate::active()->forType($type)->first();

            // Use template if available, otherwise use provided title/message
            if ($template && $template->supportsChannel('database')) {
                $rendered = $template->render('database', $data);
                if ($rendered) {
                    $title = $rendered['title'] ?? $title;
                    $message = $rendered['message'] ?? $message;
                }
            }

            // Create the notification
            $notification = CustomNotification::create([
                'user_id' => $user->id,
                'type' => $type,
                'category' => $category,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'priority' => $priority,
                'urgency_level' => $urgencyLevel,
                'status' => 'pending', // Will be updated to delivered after successful creation
                'is_read' => false,
                'action_url' => $data['action_url'] ?? null,
                'requires_action' => $data['requires_action'] ?? false,
            ]);

            // Update notification status to delivered
            $notification->update(['status' => 'delivered']);

            // Log the delivery
            NotificationLog::logDelivery(
                $notification->id,
                $type,
                'database',
                User::class,
                $user->id,
                'sent',
                ['title' => $title, 'message' => $message]
            );

        } catch (\Exception $e) {
            Log::error('Custom notification failed: ' . $e->getMessage());

            // Log the failure
            NotificationLog::logDelivery(
                null,
                $type,
                'database',
                User::class,
                $user->id,
                'failed',
                ['title' => $title, 'message' => $message],
                $e->getMessage()
            );
        }
    }

    /**
     * Send email notification using templates
     */
    private static function sendEmailNotification(User $user, string $type, string $title, string $message, array $data): void
    {
        try {
            // Get email template for this notification type
            $template = NotificationTemplate::active()
                ->forType($type)
                ->forChannel('email')
                ->first();

            if (!$template) {
                Log::warning("No email template found for notification type: {$type}");
                // Fallback to basic email without template
                self::sendBasicEmail($user, $title, $message, $data);
                return;
            }

            // Render email template with variables
            $rendered = $template->render('email', $data);

            if (!$rendered) {
                Log::error("Failed to render email template for type: {$type}");
                return;
            }

            // Validate required email fields
            if (empty($rendered['subject']) || empty($rendered['body'])) {
                Log::error("Email template missing required fields (subject/body) for type: {$type}");
                return;
            }

            // Send email using Laravel Mail
            \Mail::to($user->email)->send(new \App\Mail\NotificationEmail(
                $rendered['subject'],
                $rendered['body'],
                $data
            ));

            // Log successful email delivery
            NotificationLog::logDelivery(
                null, // No notification_id for email-only
                $type,
                'email',
                User::class,
                $user->id,
                'sent',
                [
                    'subject' => $rendered['subject'],
                    'to' => $user->email,
                    'template_used' => true
                ]
            );

            Log::info("Email notification sent successfully", [
                'user_id' => $user->id,
                'type' => $type,
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error("Email notification failed for user {$user->id}: " . $e->getMessage());

            // Log the failure
            NotificationLog::logDelivery(
                null,
                $type,
                'email',
                User::class,
                $user->id,
                'failed',
                ['to' => $user->email],
                $e->getMessage()
            );
        }
    }

    /**
     * Send basic email without template (fallback)
     */
    private static function sendBasicEmail(User $user, string $title, string $message, array $data): void
    {
        try {
            \Mail::to($user->email)->send(new \App\Mail\BasicNotificationEmail(
                $title,
                $message,
                $data
            ));

            Log::info("Basic email notification sent", [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error("Basic email notification failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send Laravel built-in notification
     */
    private static function sendLaravelNotification(User $user, string $type, string $title, string $message, array $data): void
    {
        try {
            $notificationData = [
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'created_at' => now(),
            ];

            $user->notify(new \App\Notifications\UnifiedNotification($notificationData));
        } catch (\Exception $e) {
            Log::error('Laravel notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Send broadcast notification via WebSocket
     */
    private static function sendBroadcastNotification(User $user, string $type, string $title, string $message, array $data): void
    {
        try {
            // Get broadcast template if available
            $template = NotificationTemplate::active()
                ->forType($type)
                ->forChannel('broadcast')
                ->first();

            $broadcastData = [
                'id' => uniqid('broadcast_'),
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'user_id' => $user->id,
                'timestamp' => now()->toISOString(),
                'source' => 'unified_service'
            ];

            // Use template if available
            if ($template) {
                $rendered = $template->render('broadcast', $data);
                if ($rendered) {
                    $broadcastData['title'] = $rendered['title'] ?? $title;
                    $broadcastData['message'] = $rendered['message'] ?? $message;
                    $broadcastData['template_used'] = true;
                }
            }

            // Broadcast via Laravel Broadcasting
            broadcast(new \App\Events\NotificationSent($user, $broadcastData));

            // Log successful broadcast
            NotificationLog::logDelivery(
                null,
                $type,
                'broadcast',
                User::class,
                $user->id,
                'sent',
                [
                    'broadcast_id' => $broadcastData['id'],
                    'channel' => "user.{$user->id}",
                    'template_used' => isset($broadcastData['template_used'])
                ]
            );

            Log::info("Broadcast notification sent successfully", [
                'user_id' => $user->id,
                'type' => $type,
                'broadcast_id' => $broadcastData['id']
            ]);

        } catch (\Exception $e) {
            Log::error("Broadcast notification failed for user {$user->id}: " . $e->getMessage());

            // Log the failure
            NotificationLog::logDelivery(
                null,
                $type,
                'broadcast',
                User::class,
                $user->id,
                'failed',
                ['channel' => "user.{$user->id}"],
                $e->getMessage()
            );
        }
    }



    /**
     * Get unified notifications for user
     */
    public static function getUserNotifications(User $user, int $page = 1, int $perPage = 20): Collection
    {
        try {
            // Get custom notifications
            $customNotifications = $user->userNotifications()
                ->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->data,
                        'is_read' => $notification->is_read,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at,
                        'source' => 'custom',
                    ];
                });

            // Get Laravel notifications
            $laravelNotifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get()
                ->map(function ($notification) {
                    $data = json_decode($notification->data, true);
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $data['title'] ?? '',
                        'message' => $data['message'] ?? '',
                        'data' => $data['data'] ?? [],
                        'is_read' => !is_null($notification->read_at),
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at,
                        'source' => 'laravel',
                    ];
                });

            // Merge and sort by created_at
            return $customNotifications->concat($laravelNotifications)
                ->sortByDesc('created_at')
                ->take($perPage)
                ->values();

        } catch (\Exception $e) {
            Log::error('Get unified notifications failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get unread count from both systems
     */
    public static function getUnreadCount(User $user): int
    {
        try {
            $customCount = $user->userNotifications()->where('is_read', false)->count();
            $laravelCount = $user->unreadNotifications()->count();

            return $customCount + $laravelCount;
        } catch (\Exception $e) {
            Log::error('Get unread count failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark notification as read in both systems
     */
    public static function markAsRead(User $user, string $notificationId, string $source = 'auto'): bool
    {
        try {
            if ($source === 'custom' || $source === 'auto') {
                $customNotification = $user->userNotifications()->find($notificationId);
                if ($customNotification) {
                    $customNotification->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                    return true;
                }
            }

            if ($source === 'laravel' || $source === 'auto') {
                $laravelNotification = $user->notifications()->find($notificationId);
                if ($laravelNotification) {
                    $laravelNotification->markAsRead();
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Mark as read failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read
     */
    public static function markAllAsRead(User $user): bool
    {
        try {
            // Mark custom notifications as read
            $user->userNotifications()->where('is_read', false)->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            // Mark Laravel notifications as read
            $user->unreadNotifications()->update(['read_at' => now()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Mark all as read failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log notification for debugging
     */
    private static function logNotification(User $user, string $type, array $channels, string $status, array $data): void
    {
        try {
            foreach ($channels as $channel) {
                DB::table('notification_logs')->insert([
                    'type' => $type,
                    'channel' => $channel,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $user->id,
                    'status' => $status,
                    'data' => json_encode($data),
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Notification logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Get notification statistics
     */
    public static function getStats(User $user): array
    {
        try {
            return [
                'custom' => [
                    'total' => $user->userNotifications()->count(),
                    'unread' => $user->userNotifications()->where('is_read', false)->count(),
                ],
                'laravel' => [
                    'total' => $user->notifications()->count(),
                    'unread' => $user->unreadNotifications()->count(),
                ],
                'unified' => [
                    'total' => $user->userNotifications()->count() + $user->notifications()->count(),
                    'unread' => self::getUnreadCount($user),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Get notification stats failed: ' . $e->getMessage());
            return [
                'custom' => ['total' => 0, 'unread' => 0],
                'laravel' => ['total' => 0, 'unread' => 0],
                'unified' => ['total' => 0, 'unread' => 0],
            ];
        }
    }

    /**
     * Auto-determine category from notification type
     */
    private static function getCategoryFromType(string $type): string
    {
        $categoryMappings = [
            'forum' => [
                'forum_activity', 'thread_created', 'thread_replied', 'comment_created',
                'comment_mention', 'thread_liked', 'comment_liked'
            ],
            'marketplace' => [
                'marketplace_activity', 'product_approved', 'product_rejected',
                'order_update', 'commission_paid', 'quote_request', 'business_verified',
                'business_rejected'
            ],
            'social' => [
                'user_followed', 'user_registered', 'user_mention', 'like_received',
                'follow_received', 'message_received'
            ],
            'security' => [
                'login_from_new_device', 'password_changed', 'account_locked',
                'suspicious_activity', 'security_alert'
            ],
        ];

        foreach ($categoryMappings as $category => $types) {
            if (in_array($type, $types)) {
                return $category;
            }
        }

        return 'system'; // default
    }

    /**
     * Auto-determine priority from notification type
     */
    private static function getPriorityFromType(string $type): string
    {
        $urgentTypes = ['security_alert', 'account_locked', 'suspicious_activity'];
        $highTypes = [
            'password_changed', 'login_from_new_device', 'system_announcement',
            'business_rejected', 'product_rejected'
        ];

        if (in_array($type, $urgentTypes)) {
            return 'urgent';
        }

        if (in_array($type, $highTypes)) {
            return 'high';
        }

        return 'normal';
    }

    /**
     * Auto-determine urgency level from notification type
     */
    private static function getUrgencyLevelFromType(string $type): int
    {
        $level3Types = ['security_alert', 'account_locked', 'suspicious_activity'];
        $level2Types = [
            'password_changed', 'login_from_new_device', 'system_announcement',
            'maintenance_notice', 'business_verified', 'business_rejected',
            'product_rejected', 'order_update'
        ];

        if (in_array($type, $level3Types)) {
            return 3;
        }

        if (in_array($type, $level2Types)) {
            return 2;
        }

        return 1; // default
    }

    /**
     * Send bulk notifications to multiple users
     */
    public static function sendBulk(array $users, string $type, string $title, string $message, array $data = [], array $channels = ['database']): array
    {
        $results = [
            'total' => count($users),
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($users as $user) {
            try {
                // Ensure $user is a User instance
                if (!$user instanceof User) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'user_id' => 'unknown',
                        'error' => 'Invalid user object provided'
                    ];
                    continue;
                }

                if (self::send($user, $type, $title, $message, $data, $channels)) {
                    $results['successful']++;
                } else {
                    $results['failed']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'user_id' => $user->id ?? 'unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        Log::info("Bulk notification completed", $results);
        return $results;
    }

    /**
     * Get delivery statistics
     */
    public static function getDeliveryStats(array $filters = []): array
    {
        return NotificationLog::getDeliveryStats($filters);
    }

    /**
     * Get system health metrics
     */
    public static function getSystemHealth(): array
    {
        try {
            $stats = [
                'total_notifications' => CustomNotification::count(),
                'total_logs' => NotificationLog::count(),
                'total_templates' => NotificationTemplate::count(),
                'active_templates' => NotificationTemplate::active()->count(),
                'total_preferences' => NotificationPreference::count(),
                'enabled_preferences' => NotificationPreference::enabled()->count(),
            ];

            // Recent activity (last 24 hours)
            $recentStats = [
                'notifications_24h' => CustomNotification::where('created_at', '>', now()->subDay())->count(),
                'logs_24h' => NotificationLog::where('created_at', '>', now()->subDay())->count(),
                'success_rate_24h' => NotificationLog::getSuccessRate(),
            ];

            // Channel distribution
            $channelStats = NotificationLog::selectRaw('channel, COUNT(*) as count')
                ->groupBy('channel')
                ->pluck('count', 'channel')
                ->toArray();

            return [
                'overview' => $stats,
                'recent_activity' => $recentStats,
                'channel_distribution' => $channelStats,
                'timestamp' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get system health: ' . $e->getMessage());

            return [
                'overview' => [
                    'total_notifications' => 0,
                    'total_logs' => 0,
                    'total_templates' => 0,
                    'active_templates' => 0,
                    'total_preferences' => 0,
                    'enabled_preferences' => 0,
                ],
                'recent_activity' => [
                    'notifications_24h' => 0,
                    'logs_24h' => 0,
                    'success_rate_24h' => 0,
                ],
                'channel_distribution' => [],
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Retry failed notifications
     */
    public static function retryFailedNotifications(int $limit = 100): array
    {
        $failedLogs = NotificationLog::getFailedForRetry($limit);
        $results = [
            'total_retried' => 0,
            'successful_retries' => 0,
            'failed_retries' => 0
        ];

        foreach ($failedLogs as $log) {
            try {
                $user = User::find($log->notifiable_id);
                if (!$user) {
                    continue;
                }

                $data = $log->data ?? [];
                $title = $data['title'] ?? 'Retry Notification';
                $message = $data['message'] ?? 'Retrying failed notification';

                if (self::send($user, $log->type, $title, $message, $data, [$log->channel])) {
                    $log->markAsRetried();
                    $results['successful_retries']++;
                } else {
                    $results['failed_retries']++;
                }

                $results['total_retried']++;

            } catch (\Exception $e) {
                Log::error("Failed to retry notification log {$log->id}: " . $e->getMessage());
                $results['failed_retries']++;
            }
        }

        return $results;
    }

    /**
     * Clear old notification logs
     */
    public static function cleanupOldLogs(int $daysToKeep = 30): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        $deletedCount = NotificationLog::where('created_at', '<', $cutoffDate)
            ->where('status', '!=', 'failed') // Keep failed logs for debugging
            ->delete();

        Log::info("Cleaned up {$deletedCount} old notification logs older than {$daysToKeep} days");

        return $deletedCount;
    }

    /**
     * Get notification templates with caching
     */
    public static function getTemplatesCached(): Collection
    {
        return \Cache::remember('notification_templates', 3600, function () {
            return NotificationTemplate::active()->get();
        });
    }

    /**
     * Invalidate template cache
     */
    public static function invalidateTemplateCache(): void
    {
        \Cache::forget('notification_templates');
    }
}
