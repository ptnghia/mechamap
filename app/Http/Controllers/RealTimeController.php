<?php

namespace App\Http\Controllers;

use App\Services\WebSocketService;
use App\Events\RealTimeNotification;
use App\Events\UserActivityUpdate;
use App\Events\DashboardMetricsUpdate;
use App\Events\ChatMessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * Real-Time Controller
 * Handles WebSocket connections and real-time features
 */
class RealTimeController extends Controller
{
    protected $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        $this->webSocketService = $webSocketService;
    }

    /**
     * Get real-time connection status
     */
    public function status(): JsonResponse
    {
        try {
            $stats = $this->webSocketService->getRealtimeStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Real-time status error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to get real-time status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle user connection
     */
    public function connect(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'channels' => 'nullable|array',
        ]);

        try {
            $userId = $request->get('user_id');
            $channels = $request->get('channels', []);

            // Set user online status
            $this->webSocketService->setUserOnlineStatus($userId, true);

            // Join requested channels
            foreach ($channels as $channel) {
                $this->webSocketService->joinChannel($userId, $channel);
            }

            // Broadcast user online activity
            $this->webSocketService->broadcastUserActivity($userId, [
                'type' => 'user_online',
                'timestamp' => now()->toISOString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Connected successfully',
                'data' => [
                    'user_id' => $userId,
                    'channels' => $channels,
                    'connection_id' => uniqid('conn_'),
                    'server_time' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Real-time connect error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle user disconnection
     */
    public function disconnect(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'channels' => 'nullable|array',
        ]);

        try {
            $userId = $request->get('user_id');
            $channels = $request->get('channels', []);

            // Set user offline status
            $this->webSocketService->setUserOnlineStatus($userId, false);

            // Leave channels
            foreach ($channels as $channel) {
                $this->webSocketService->leaveChannel($userId, $channel);
            }

            // Broadcast user offline activity
            $this->webSocketService->broadcastUserActivity($userId, [
                'type' => 'user_offline',
                'timestamp' => now()->toISOString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Disconnected successfully',
                'data' => [
                    'user_id' => $userId,
                    'disconnected_at' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Real-time disconnect error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Disconnection failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send real-time notification
     */
    public function sendNotification(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'nullable|string|max:50',
            'level' => 'nullable|string|in:info,success,warning,error,critical',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string|max:50',
        ]);

        try {
            $user = \App\Models\User::findOrFail($request->get('user_id'));
            
            // Create notification object
            $notification = (object) [
                'type' => $request->get('type', 'general'),
                'title' => $request->get('title'),
                'message' => $request->get('message'),
                'level' => $request->get('level', 'info'),
                'action_url' => $request->get('action_url'),
                'action_text' => $request->get('action_text'),
                'timestamp' => now()->toISOString(),
            ];

            // Broadcast notification
            $this->webSocketService->broadcastNotification($user, $notification);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'data' => [
                    'notification_id' => uniqid('notif_'),
                    'user_id' => $user->id,
                    'sent_at' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Send notification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:5000',
            'type' => 'nullable|string|in:text,image,file,audio,video',
            'attachments' => 'nullable|array',
            'reply_to' => 'nullable|integer',
            'priority' => 'nullable|string|in:normal,urgent',
        ]);

        try {
            $senderId = Auth::id();
            $receiverId = $request->get('receiver_id');

            // Check if users can chat
            if (!$this->canUsersChat($senderId, $receiverId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot send messages to this user',
                ], 403);
            }

            // Create message data
            $messageData = [
                'id' => uniqid('msg_'),
                'content' => $request->get('content'),
                'type' => $request->get('type', 'text'),
                'attachments' => $request->get('attachments', []),
                'reply_to' => $request->get('reply_to'),
                'priority' => $request->get('priority', 'normal'),
                'timestamp' => now()->toISOString(),
            ];

            // Broadcast message
            $this->webSocketService->broadcastChatMessage($senderId, $receiverId, $messageData);

            // Store message in database
            $message = $this->storeChatMessage($senderId, $receiverId, $messageData);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'message_id' => $message->id,
                    'conversation_id' => $this->getConversationId($senderId, $receiverId),
                    'sent_at' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Send chat message error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'typing' => 'required|boolean',
        ]);

        try {
            $senderId = Auth::id();
            $receiverId = $request->get('receiver_id');
            $typing = $request->boolean('typing');

            $this->webSocketService->sendTypingIndicator($senderId, $receiverId, $typing);

            return response()->json([
                'success' => true,
                'message' => 'Typing indicator sent',
                'data' => [
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'typing' => $typing,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Send typing indicator error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send typing indicator',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get online users
     */
    public function getOnlineUsers(): JsonResponse
    {
        try {
            $onlineCount = $this->webSocketService->getOnlineUsersCount();
            
            // Get list of online users (if user has permission)
            $onlineUsers = [];
            if (Auth::user() && Auth::user()->can('view_online_users')) {
                $onlineUsers = $this->getOnlineUsersList();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'online_count' => $onlineCount,
                    'online_users' => $onlineUsers,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Get online users error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get online users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Broadcast system announcement
     */
    public function broadcastAnnouncement(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'level' => 'nullable|string|in:info,success,warning,error,critical',
            'target_roles' => 'nullable|array',
        ]);

        try {
            // Check if user has permission to broadcast announcements
            if (!Auth::user() || !Auth::user()->can('broadcast_announcements')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions',
                ], 403);
            }

            $announcement = [
                'title' => $request->get('title'),
                'message' => $request->get('message'),
                'level' => $request->get('level', 'info'),
                'target_roles' => $request->get('target_roles', []),
                'sender' => Auth::user()->name,
                'timestamp' => now()->toISOString(),
            ];

            $this->webSocketService->broadcastSystemAnnouncement($announcement);

            return response()->json([
                'success' => true,
                'message' => 'Announcement broadcasted successfully',
                'data' => [
                    'announcement_id' => uniqid('ann_'),
                    'broadcasted_at' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Broadcast announcement error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to broadcast announcement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time health check
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $health = $this->webSocketService->healthCheck();
            
            return response()->json([
                'success' => true,
                'data' => $health,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Real-time health check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Health check failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Private helper methods

    private function canUsersChat(int $senderId, int $receiverId): bool
    {
        // Check if receiver has blocked sender
        if (Cache::get("user_blocked_{$receiverId}_{$senderId}", false)) {
            return false;
        }

        // Check if sender is rate limited
        $messageCount = Cache::get("user_message_count_{$senderId}", 0);
        if ($messageCount > 60) { // Max 60 messages per hour
            return false;
        }

        // Increment message count
        Cache::put("user_message_count_{$senderId}", $messageCount + 1, 3600);

        return true;
    }

    private function storeChatMessage(int $senderId, int $receiverId, array $messageData): object
    {
        // This would typically store in database
        // For now, return a mock object
        return (object) [
            'id' => uniqid('db_msg_'),
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $messageData['content'],
            'type' => $messageData['type'],
            'created_at' => now(),
        ];
    }

    private function getConversationId(int $userId1, int $userId2): string
    {
        $ids = [$userId1, $userId2];
        sort($ids);
        return implode('_', $ids);
    }

    private function getOnlineUsersList(): array
    {
        // This would typically query active sessions or cache
        // For now, return mock data
        return [
            ['id' => 1, 'name' => 'John Doe', 'avatar' => null, 'last_activity' => now()],
            ['id' => 2, 'name' => 'Jane Smith', 'avatar' => null, 'last_activity' => now()],
        ];
    }
}
