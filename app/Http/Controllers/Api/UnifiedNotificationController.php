<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UnifiedNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Unified Notification API Controller
 * 
 * Provides API endpoints for the unified notification system
 */
class UnifiedNotificationController extends Controller
{
    /**
     * Get user's notifications from both systems
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);

            $notifications = UnifiedNotificationService::getUserNotifications($user, $page, $perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'meta' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $notifications->count(),
                ],
                'message' => 'Notifications retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function count(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = UnifiedNotificationService::getUnreadCount($user);

            return response()->json([
                'success' => true,
                'count' => $count,
                'unread_count' => $count, // For compatibility
                'message' => 'Unread count retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Failed to get unread count',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'notification_id' => 'required|string',
                'source' => 'sometimes|in:custom,laravel,auto'
            ]);

            $user = Auth::user();
            $notificationId = $request->notification_id;
            $source = $request->get('source', 'auto');

            $success = UnifiedNotificationService::markAsRead($user, $notificationId, $source);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $success = UnifiedNotificationService::markAllAsRead($user);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'All notifications marked as read successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark all notifications as read'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send test notification
     */
    public function sendTest(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'title' => 'required|string',
                'message' => 'required|string',
                'channels' => 'sometimes|array',
                'channels.*' => 'in:database,mail'
            ]);

            $user = Auth::user();
            $channels = $request->get('channels', ['database']);

            $success = UnifiedNotificationService::send(
                $user,
                $request->type,
                $request->title,
                $request->message,
                ['test' => true],
                $channels
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test notification'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $user = Auth::user();
            $stats = UnifiedNotificationService::getStats($user);

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Notification statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get recent notifications for header/dropdown
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 5);

            $notifications = UnifiedNotificationService::getUserNotifications($user, 1, $limit);
            $totalUnread = UnifiedNotificationService::getUnreadCount($user);

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total_unread' => $totalUnread,
                'message' => 'Recent notifications retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent notifications',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
