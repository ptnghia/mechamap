<?php

namespace App\Http\View\Composers;

use App\Models\Notification;
use App\Services\UnifiedNotificationManager;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Only add notification data for authenticated users
        if (Auth::check()) {
            $user = Auth::user();

            // Get notifications for header dropdown (similar to getHeaderNotifications)
            $notifications = $user->userNotifications()
                ->orderByRaw("CASE
                    WHEN priority = 'urgent' THEN 1
                    WHEN priority = 'high' THEN 2
                    WHEN priority = 'normal' THEN 3
                    WHEN priority = 'low' THEN 4
                    ELSE 5 END")
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at,
                        'time_ago' => $notification->created_at->diffForHumans(),
                        'icon' => 'fas fa-' . $notification->icon,
                        'color' => $notification->color,
                        'action_url' => $notification->action_url,
                    ];
                });

            // Get unread count using UnifiedNotificationManager
            $unreadCount = UnifiedNotificationManager::getUnreadCount($user);

            $view->with([
                'headerNotifications' => $notifications,
                'headerUnreadCount' => $unreadCount
            ]);
        } else {
            $view->with([
                'headerNotifications' => collect([]),
                'headerUnreadCount' => 0
            ]);
        }
    }
}
