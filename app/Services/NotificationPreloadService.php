<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationPreloadService
{
    /**
     * Get preload data for notifications
     * This will be embedded in the page to avoid initial API calls
     */
    public function getPreloadData(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'notifications' => [],
                'unread_count' => 0,
                'has_notifications' => false
            ];
        }

        // Cache key for user's notification data
        $cacheKey = "notifications_preload_user_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) { // 5 minutes cache
            // Get unread count
            $unreadCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            // Get recent notifications for dropdown (limit 10 for performance)
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at->toISOString(),
                        'formatted_time' => $notification->created_at->diffForHumans(),
                        'icon' => $this->getNotificationIcon($notification->type),
                        'url' => $notification->url,
                        'data' => $notification->data
                    ];
                });

            return [
                'notifications' => $notifications->toArray(),
                'unread_count' => $unreadCount,
                'has_notifications' => $notifications->isNotEmpty()
            ];
        });
    }

    /**
     * Get icon for notification type
     */
    private function getNotificationIcon(string $type): string
    {
        $icons = [
            'message' => 'fas fa-envelope',
            'comment' => 'fas fa-comment',
            'like' => 'fas fa-heart',
            'follow' => 'fas fa-user-plus',
            'system' => 'fas fa-cog',
            'order' => 'fas fa-shopping-cart',
            'payment' => 'fas fa-credit-card',
            'security' => 'fas fa-shield-alt',
            'update' => 'fas fa-sync-alt',
            'warning' => 'fas fa-exclamation-triangle',
            'success' => 'fas fa-check-circle',
            'info' => 'fas fa-info-circle',
            'default' => 'fas fa-bell'
        ];

        return $icons[$type] ?? $icons['default'];
    }

    /**
     * Clear cache for user
     */
    public function clearCache(int $userId): void
    {
        $cacheKey = "notifications_preload_user_{$userId}";
        Cache::forget($cacheKey);
    }

    /**
     * Clear cache for current user
     */
    public function clearCurrentUserCache(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->clearCache($user->id);
        }
    }
}
