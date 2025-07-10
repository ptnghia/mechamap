<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\NotificationEngagementService;
use App\Models\Notification;

class TrackNotificationEngagement
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track for authenticated users
        if (!auth()->check()) {
            return $response;
        }

        $user = auth()->user();

        // Track notification view if notification_id is present
        if ($request->has('notification_id')) {
            $this->trackNotificationView($request, $user);
        }

        // Track notification click if coming from notification link
        if ($request->has('from_notification')) {
            $this->trackNotificationClick($request, $user);
        }

        return $response;
    }

    /**
     * Track notification view
     */
    private function trackNotificationView(Request $request, $user): void
    {
        try {
            $notificationId = $request->input('notification_id');
            $notification = Notification::find($notificationId);

            if ($notification && $notification->user_id === $user->id) {
                $context = [
                    'source' => 'middleware',
                    'route' => $request->route()?->getName(),
                    'method' => $request->method(),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ];

                NotificationEngagementService::trackView($notification, $user, $context);
            }
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Failed to track notification view in middleware', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'notification_id' => $request->input('notification_id'),
            ]);
        }
    }

    /**
     * Track notification click
     */
    private function trackNotificationClick(Request $request, $user): void
    {
        try {
            $notificationId = $request->input('from_notification');
            $notification = Notification::find($notificationId);

            if ($notification && $notification->user_id === $user->id) {
                $context = [
                    'source' => 'middleware',
                    'route' => $request->route()?->getName(),
                    'method' => $request->method(),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'referrer' => $request->header('referer'),
                ];

                NotificationEngagementService::trackClick($notification, $user, $context);
            }
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Failed to track notification click in middleware', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'notification_id' => $request->input('from_notification'),
            ]);
        }
    }
}
