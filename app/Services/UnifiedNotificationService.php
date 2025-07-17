<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification as CustomNotification;
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
     * Send notification using both systems
     *
     * @param User|array $users
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @param array $channels
     * @return bool
     */
    public static function send($users, string $type, string $title, string $message, array $data = [], array $channels = ['database']): bool
    {
        try {
            if (!is_array($users)) {
                $users = [$users];
            }

            foreach ($users as $user) {
                if (!$user instanceof User) {
                    continue;
                }

                // Send via custom notification system (for backward compatibility)
                self::sendCustomNotification($user, $type, $title, $message, $data);

                // Send via Laravel built-in notification system
                if (in_array('database', $channels)) {
                    self::sendLaravelNotification($user, $type, $title, $message, $data);
                }

                // Send via email if requested
                if (in_array('mail', $channels) && $user->email_notifications_enabled) {
                    self::sendEmailNotification($user, $type, $title, $message, $data);
                }

                // Log the notification
                self::logNotification($user, $type, $channels, 'sent', $data);

                // Broadcast real-time notification if database channel is used
                if (in_array('database', $channels)) {
                    try {
                        $notification = $user->userNotifications()->latest()->first();
                        broadcast(new \App\Events\NotificationSent($user, [
                            'id' => $notification->id,
                            'type' => $type,
                            'title' => $title,
                            'message' => $message,
                            'data' => $data,
                            'created_at' => $notification->created_at,
                            'source' => 'custom'
                        ]));
                    } catch (\Exception $e) {
                        \Log::error('Failed to broadcast notification: ' . $e->getMessage());
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Unified notification send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send custom notification (legacy system)
     */
    private static function sendCustomNotification(User $user, string $type, string $title, string $message, array $data): void
    {
        try {
            CustomNotification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'priority' => $data['priority'] ?? 'normal',
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Custom notification failed: ' . $e->getMessage());
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
     * Send email notification
     */
    private static function sendEmailNotification(User $user, string $type, string $title, string $message, array $data): void
    {
        try {
            // Use existing email notification logic from NotificationService
            \App\Services\NotificationService::sendEmail($user, (object)[
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Email notification failed: ' . $e->getMessage());
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
}
