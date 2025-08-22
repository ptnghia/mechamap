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

    // ============================================================================
    // WEBSOCKET SERVER API METHODS (API Key protected)
    // ============================================================================

    /**
     * Verify user for WebSocket server (API Key protected)
     */
    public function verifyUser(Request $request)
    {
        try {
            $token = $request->input('token');

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token required'
                ], 400);
            }

            $user = null;

            // Check if it's a Sanctum token (contains |)
            if (str_contains($token, '|')) {
                // Verify Sanctum token
                $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
            } else {
                // Try to verify as JWT token
                try {
                    $jwtSecret = env('JWT_SECRET', config('app.key'));
                    $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($jwtSecret, 'HS256'));
                    $payload = (array) $decoded;

                    // Check expiration
                    if (isset($payload['exp']) && $payload['exp'] < time()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Token expired'
                        ], 401);
                    }

                    // Get user ID from payload
                    $userId = $payload['userId'] ?? $payload['sub'] ?? $payload['user_id'] ?? null;

                    if ($userId) {
                        $user = \App\Models\User::find($userId);
                    }

                } catch (\Firebase\JWT\ExpiredException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token expired'
                    ], 401);
                } catch (\Firebase\JWT\SignatureInvalidException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid token signature'
                    ], 401);
                } catch (\Exception $e) {
                    // JWT verification failed, token might be invalid
                    Log::warning('JWT verification failed', [
                        'error' => $e->getMessage(),
                        'token_prefix' => substr($token, 0, 10) . '...'
                    ]);
                }
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Check if user is active
            if (isset($user->status) && $user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'User account is not active'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'is_online' => $user->is_online ?? false,
                        'last_activity' => $user->last_activity,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('WebSocket user verification failed', [
                'error' => $e->getMessage(),
                'token_prefix' => substr($request->input('token', ''), 0, 10) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User verification failed'
            ], 500);
        }
    }

    /**
     * Get user by ID for WebSocket server (API Key protected)
     */
    public function getUserById(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'is_online' => $user->is_online ?? false,
                        'last_activity' => $user->last_activity,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get user by ID', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user'
            ], 500);
        }
    }

    /**
     * Handle broadcasting from WebSocket server to Laravel (API Key protected)
     */
    public function broadcastFromWebSocket(Request $request)
    {
        try {
            $event = $request->input('event');
            $data = $request->input('data', []);
            $channels = $request->input('channels', []);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event type required'
                ], 400);
            }

            // Log the broadcast request
            Log::info('WebSocket server broadcast request', [
                'event' => $event,
                'channels' => $channels,
                'data_keys' => array_keys($data)
            ]);

            // Process the broadcast based on event type
            switch ($event) {
                case 'user.activity':
                    $this->handleUserActivity($data);
                    break;
                case 'notification.delivered':
                    $this->handleNotificationDelivered($data);
                    break;
                default:
                    Log::warning('Unknown WebSocket broadcast event', ['event' => $event]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Broadcast processed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('WebSocket broadcast processing failed', [
                'error' => $e->getMessage(),
                'event' => $request->input('event')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Broadcast processing failed'
            ], 500);
        }
    }

    /**
     * Get user permissions for WebSocket server (API Key protected)
     */
    public function getUserPermissions(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $permissions = [
                'can_send_messages' => true,
                'can_receive_notifications' => true,
                'can_join_channels' => true,
                'max_connections' => 5,
                'rate_limit' => [
                    'messages_per_minute' => 60,
                    'connections_per_hour' => 10
                ]
            ];

            // Role-based permissions
            switch ($user->role) {
                case 'admin':
                case 'moderator':
                    $permissions['can_broadcast'] = true;
                    $permissions['can_moderate'] = true;
                    $permissions['max_connections'] = 20;
                    break;
                case 'verified_partner':
                case 'manufacturer':
                case 'supplier':
                case 'brand':
                    $permissions['can_send_business_messages'] = true;
                    $permissions['max_connections'] = 10;
                    break;
                case 'guest':
                    $permissions['can_send_messages'] = false;
                    $permissions['max_connections'] = 2;
                    $permissions['rate_limit']['messages_per_minute'] = 10;
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'permissions' => $permissions
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get user permissions', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user permissions'
            ], 500);
        }
    }

    /**
     * Handle user activity updates from WebSocket
     */
    private function handleUserActivity($data)
    {
        if (isset($data['user_id'])) {
            User::where('id', $data['user_id'])->update([
                'last_activity' => now(),
                'is_online' => $data['is_online'] ?? true
            ]);
        }
    }

    /**
     * Handle notification delivery confirmation from WebSocket
     */
    private function handleNotificationDelivered($data)
    {
        if (isset($data['notification_id'])) {
            UserNotification::where('id', $data['notification_id'])->update([
                'delivered_at' => now()
            ]);
        }
    }
}
