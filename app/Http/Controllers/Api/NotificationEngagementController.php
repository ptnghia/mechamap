<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationEngagementService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class NotificationEngagementController extends Controller
{
    /**
     * Track notification view
     */
    public function trackView(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
            'context' => 'array',
        ]);

        try {
            $notification = Notification::findOrFail($request->notification_id);
            $user = auth()->user();

            // Verify notification belongs to user
            if ($notification->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $context = array_merge($request->input('context', []), [
                'source' => 'api',
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toISOString(),
            ]);

            NotificationEngagementService::trackView($notification, $user, $context);

            return response()->json([
                'success' => true,
                'message' => 'View tracked successfully',
                'notification_id' => $notification->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to track view',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track notification click
     */
    public function trackClick(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
            'context' => 'array',
        ]);

        try {
            $notification = Notification::findOrFail($request->notification_id);
            $user = auth()->user();

            // Verify notification belongs to user
            if ($notification->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $context = array_merge($request->input('context', []), [
                'source' => 'api',
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toISOString(),
            ]);

            NotificationEngagementService::trackClick($notification, $user, $context);

            return response()->json([
                'success' => true,
                'message' => 'Click tracked successfully',
                'notification_id' => $notification->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to track click',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track notification dismiss
     */
    public function trackDismiss(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
            'context' => 'array',
        ]);

        try {
            $notification = Notification::findOrFail($request->notification_id);
            $user = auth()->user();

            // Verify notification belongs to user
            if ($notification->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $context = array_merge($request->input('context', []), [
                'source' => 'api',
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toISOString(),
            ]);

            NotificationEngagementService::trackDismiss($notification, $user, $context);

            return response()->json([
                'success' => true,
                'message' => 'Dismiss tracked successfully',
                'notification_id' => $notification->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to track dismiss',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track notification action
     */
    public function trackAction(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
            'action' => 'required|string|max:50',
            'context' => 'array',
        ]);

        try {
            $notification = Notification::findOrFail($request->notification_id);
            $user = auth()->user();

            // Verify notification belongs to user
            if ($notification->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $action = $request->input('action');
            $context = array_merge($request->input('context', []), [
                'source' => 'api',
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toISOString(),
            ]);

            NotificationEngagementService::trackAction($notification, $user, $action, $context);

            return response()->json([
                'success' => true,
                'message' => 'Action tracked successfully',
                'notification_id' => $notification->id,
                'action' => $action,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to track action',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user engagement metrics
     */
    public function getUserMetrics(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $metrics = NotificationEngagementService::getUserEngagementMetrics($user);

            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'metrics' => $metrics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get user metrics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get notification type engagement metrics
     */
    public function getTypeMetrics(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
        ]);

        try {
            $notificationType = $request->input('type');
            $metrics = NotificationEngagementService::getNotificationTypeEngagement($notificationType);

            return response()->json([
                'success' => true,
                'notification_type' => $notificationType,
                'metrics' => $metrics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get type metrics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get engagement summary for date range
     */
    public function getEngagementSummary(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            // Limit to 30 days max
            if ($startDate->diffInDays($endDate) > 30) {
                return response()->json([
                    'error' => 'Date range cannot exceed 30 days',
                ], 400);
            }

            $summary = NotificationEngagementService::getEngagementSummary($startDate, $endDate);

            return response()->json([
                'success' => true,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'summary' => $summary,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get engagement summary',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top performing notification types
     */
    public function getTopPerforming(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50',
        ]);

        try {
            $limit = $request->input('limit', 10);
            $topPerforming = NotificationEngagementService::getTopPerformingNotificationTypes($limit);

            return response()->json([
                'success' => true,
                'limit' => $limit,
                'top_performing' => $topPerforming,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get top performing types',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user engagement leaderboard
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
        ]);

        try {
            $limit = $request->input('limit', 20);
            $leaderboard = NotificationEngagementService::getUserEngagementLeaderboard($limit);

            return response()->json([
                'success' => true,
                'limit' => $limit,
                'leaderboard' => $leaderboard,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get engagement leaderboard',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk track multiple engagement events
     */
    public function bulkTrack(Request $request): JsonResponse
    {
        $request->validate([
            'events' => 'required|array|max:50',
            'events.*.notification_id' => 'required|exists:notifications,id',
            'events.*.event_type' => 'required|in:view,click,dismiss,action',
            'events.*.action' => 'required_if:events.*.event_type,action|string|max:50',
            'events.*.context' => 'array',
        ]);

        try {
            $user = auth()->user();
            $results = [];
            $errors = [];

            foreach ($request->events as $index => $eventData) {
                try {
                    $notification = Notification::findOrFail($eventData['notification_id']);

                    // Verify notification belongs to user
                    if ($notification->user_id !== $user->id) {
                        $errors[] = "Event {$index}: Unauthorized access to notification {$notification->id}";
                        continue;
                    }

                    $context = array_merge($eventData['context'] ?? [], [
                        'source' => 'bulk_api',
                        'user_agent' => $request->userAgent(),
                        'ip_address' => $request->ip(),
                        'timestamp' => now()->toISOString(),
                    ]);

                    switch ($eventData['event_type']) {
                        case 'view':
                            NotificationEngagementService::trackView($notification, $user, $context);
                            break;
                        case 'click':
                            NotificationEngagementService::trackClick($notification, $user, $context);
                            break;
                        case 'dismiss':
                            NotificationEngagementService::trackDismiss($notification, $user, $context);
                            break;
                        case 'action':
                            NotificationEngagementService::trackAction($notification, $user, $eventData['action'], $context);
                            break;
                    }

                    $results[] = [
                        'notification_id' => $notification->id,
                        'event_type' => $eventData['event_type'],
                        'status' => 'success',
                    ];

                } catch (\Exception $e) {
                    $errors[] = "Event {$index}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => empty($errors),
                'processed' => count($results),
                'results' => $results,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to process bulk tracking',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
