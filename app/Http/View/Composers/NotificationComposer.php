<?php

namespace App\Http\View\Composers;

use App\Services\NotificationPreloadService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    protected NotificationPreloadService $notificationPreloadService;

    public function __construct(NotificationPreloadService $notificationPreloadService)
    {
        $this->notificationPreloadService = $notificationPreloadService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Only add notification data for authenticated users
        if (Auth::check()) {
            $notificationData = $this->notificationPreloadService->getPreloadData();
            
            $view->with([
                'preloadedNotifications' => $notificationData['notifications'],
                'preloadedUnreadCount' => $notificationData['unread_count'],
                'hasPreloadedNotifications' => $notificationData['has_notifications']
            ]);
        } else {
            $view->with([
                'preloadedNotifications' => [],
                'preloadedUnreadCount' => 0,
                'hasPreloadedNotifications' => false
            ]);
        }
    }
}
