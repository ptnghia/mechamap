<?php

namespace App\Http\Controllers;

use App\Services\RealTimeNotificationService;
use App\Services\WebSocketConnectionService;
use App\Services\ConnectionManagementService;
use App\Services\ConnectionHealthMonitor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebSocketController extends Controller
{
    /**
     * Handle WebSocket connection
     */
    public function connect(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $socketId = $request->input('socket_id');
            if (!$socketId) {
                return response()->json(['error' => 'Socket ID required'], 400);
            }

            // Register connection with management system
            $connectionInfo = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $registered = ConnectionManagementService::registerConnection($user, $socketId, $connectionInfo);

            if (!$registered) {
                return response()->json(['error' => 'Failed to register connection'], 500);
            }

            // Handle user connection (legacy support)
            WebSocketConnectionService::handleUserConnect($user, $socketId);

            return response()->json([
                'success' => true,
                'message' => 'Connected successfully',
                'user_id' => $user->id,
                'socket_id' => $socketId,
                'unread_count' => RealTimeNotificationService::getUnreadCount($user),
                'connection_registered' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('WebSocket connection failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Connection failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle WebSocket disconnection
     */
    public function disconnect(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $socketId = $request->input('socket_id');
            if (!$socketId) {
                return response()->json(['error' => 'Socket ID required'], 400);
            }

            // Handle disconnection with management system
            $reason = $request->input('reason', 'user_initiated');
            ConnectionManagementService::handleDisconnection($user, $socketId, $reason);

            // Handle user disconnection (legacy support)
            WebSocketConnectionService::handleUserDisconnect($user, $socketId);

            return response()->json([
                'success' => true,
                'message' => 'Disconnected successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('WebSocket disconnection failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Disconnection failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update socket activity (heartbeat)
     */
    public function heartbeat(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $socketId = $request->input('socket_id');
            if (!$socketId) {
                return response()->json(['error' => 'Socket ID required'], 400);
            }

            // Update heartbeat with connection metrics
            $metrics = [
                'latency' => $request->input('latency'),
                'packet_loss' => $request->input('packet_loss'),
            ];

            $updated = ConnectionManagementService::updateHeartbeat($user, $socketId, $metrics);

            if (!$updated) {
                return response()->json(['error' => 'Connection not found'], 404);
            }

            // Update socket activity (legacy support)
            WebSocketConnectionService::updateSocketActivity($user, $socketId);

            return response()->json([
                'success' => true,
                'timestamp' => now()->toISOString(),
                'unread_count' => RealTimeNotificationService::getUnreadCount($user),
                'heartbeat_updated' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('WebSocket heartbeat failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Heartbeat failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's online status
     */
    public function getOnlineStatus(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $isOnline = RealTimeNotificationService::isUserOnline($user);
            $activeConnections = WebSocketConnectionService::getUserActiveConnections($user);

            return response()->json([
                'user_id' => $user->id,
                'is_online' => $isOnline,
                'active_connections' => count($activeConnections),
                'connections' => $activeConnections,
                'unread_count' => RealTimeNotificationService::getUnreadCount($user),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get online status', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Failed to get status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test notification
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $notificationData = [
                'type' => 'test_notification',
                'title' => 'Test Notification',
                'message' => 'This is a test real-time notification',
                'data' => [
                    'test' => true,
                    'sent_at' => now()->toISOString(),
                ],
                'priority' => 'normal',
            ];

            $success = RealTimeNotificationService::sendToUser($user, $notificationData);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Test notification sent' : 'Failed to send notification',
                'notification_data' => $notificationData,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send test notification', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Failed to send test notification',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle connection reconnection
     */
    public function reconnect(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $oldSocketId = $request->input('old_socket_id');
            $newSocketId = $request->input('new_socket_id');

            if (!$oldSocketId || !$newSocketId) {
                return response()->json(['error' => 'Both old and new socket IDs required'], 400);
            }

            $reconnected = ConnectionManagementService::handleReconnection($user, $oldSocketId, $newSocketId);

            return response()->json([
                'success' => $reconnected,
                'message' => $reconnected ? 'Reconnected successfully' : 'Reconnection failed',
                'user_id' => $user->id,
                'old_socket_id' => $oldSocketId,
                'new_socket_id' => $newSocketId,
            ]);

        } catch (\Exception $e) {
            Log::error('WebSocket reconnection failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Reconnection failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get connection health status
     */
    public function getConnectionHealth(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $socketId = $request->input('socket_id');
            if (!$socketId) {
                return response()->json(['error' => 'Socket ID required'], 400);
            }

            $connection = ConnectionManagementService::getConnection($user, $socketId);
            if (!$connection) {
                return response()->json(['error' => 'Connection not found'], 404);
            }

            $healthStatus = ConnectionHealthMonitor::checkConnectionHealth($connection);

            return response()->json([
                'connection' => $connection,
                'health_status' => $healthStatus,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get connection health', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Failed to get connection health',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get WebSocket statistics (admin only)
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole(['Admin', 'Moderator'])) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get comprehensive statistics
            $connectionStats = ConnectionHealthMonitor::getConnectionStatistics();
            $onlineUsersCount = WebSocketConnectionService::getOnlineUsersCount();
            $onlineAdmins = WebSocketConnectionService::getOnlineUsersByRole('Admin');
            $onlineModerators = WebSocketConnectionService::getOnlineUsersByRole('Moderator');
            $onlineSuppliers = WebSocketConnectionService::getOnlineUsersByRole('Supplier');

            return response()->json([
                'connection_statistics' => $connectionStats,
                'online_users_total' => $onlineUsersCount,
                'online_by_role' => [
                    'admins' => count($onlineAdmins),
                    'moderators' => count($onlineModerators),
                    'suppliers' => count($onlineSuppliers),
                ],
                'online_users_details' => [
                    'admins' => $onlineAdmins,
                    'moderators' => $onlineModerators,
                    'suppliers' => $onlineSuppliers,
                ],
                'statistics_generated_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get WebSocket statistics', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Failed to get statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
