<?php

namespace App\Http\View\Composers;

use App\Http\Controllers\NotificationController;
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
            $notificationController = new NotificationController();
            $notificationData = $notificationController->getHeaderNotifications();

            $view->with([
                'headerNotifications' => $notificationData['notifications'],
                'headerUnreadCount' => $notificationData['unread_count']
            ]);
        } else {
            $view->with([
                'headerNotifications' => collect([]),
                'headerUnreadCount' => 0
            ]);
        }
    }
}
