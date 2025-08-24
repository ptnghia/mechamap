<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Group WebSocket Service
 * 
 * Specialized service for handling WebSocket events for group conversations
 * Manages real-time features like messaging, member management, typing indicators
 */
class GroupWebSocketService
{
    private string $websocketServerUrl;
    private string $serverSecret;

    public function __construct()
    {
        $this->websocketServerUrl = config('websocket.server_url', 'http://localhost:3000');
        $this->serverSecret = config('websocket.server_secret', env('WEBSOCKET_SERVER_SECRET'));
    }

    /**
     * Broadcast group message to all members
     */
    public function broadcastGroupMessage(Message $message): void
    {
        try {
            $conversation = $message->conversation;
            if (!$conversation || $conversation->type !== 'group') {
                return;
            }

            // Get all active group members
            $members = GroupMember::where('conversation_id', $conversation->id)
                ->where('is_active', true)
                ->with('user')
                ->get();

            $messageData = [
                'id' => $message->id,
                'group_id' => $conversation->id,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'user_avatar' => $message->user->avatar_url,
                'content' => $message->content,
                'type' => $message->type,
                'created_at' => $message->created_at->toISOString(),
                'attachments' => $message->attachments ?? [],
                'is_system' => $message->is_system,
            ];

            // Broadcast to group channel
            $this->broadcastToGroupChannel($conversation->id, 'group_message', $messageData);

            // Send individual notifications to members
            foreach ($members as $member) {
                if ($member->user_id !== $message->user_id) {
                    $this->sendToUser($member->user_id, [
                        'type' => 'group_message',
                        'group_id' => $conversation->id,
                        'group_name' => $conversation->title,
                        'message' => $messageData,
                    ]);
                }
            }

            Log::info('Group message broadcasted', [
                'message_id' => $message->id,
                'group_id' => $conversation->id,
                'members_count' => $members->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast group message: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Broadcast member join event
     */
    public function broadcastMemberJoined(GroupMember $member): void
    {
        try {
            $user = $member->user;
            $conversation = $member->conversation;

            $memberData = [
                'group_id' => $conversation->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_avatar' => $user->avatar_url,
                'role' => $member->role->value,
                'joined_at' => $member->joined_at->toISOString(),
            ];

            // Broadcast to group channel
            $this->broadcastToGroupChannel($conversation->id, 'member_joined', $memberData);

            // Update member count
            $this->broadcastMemberCountUpdate($conversation->id);

            Log::info('Member join broadcasted', [
                'group_id' => $conversation->id,
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast member join: ' . $e->getMessage(), [
                'member_id' => $member->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Broadcast member leave event
     */
    public function broadcastMemberLeft(int $conversationId, int $userId, string $userName): void
    {
        try {
            $memberData = [
                'group_id' => $conversationId,
                'user_id' => $userId,
                'user_name' => $userName,
                'left_at' => now()->toISOString(),
            ];

            // Broadcast to group channel
            $this->broadcastToGroupChannel($conversationId, 'member_left', $memberData);

            // Update member count
            $this->broadcastMemberCountUpdate($conversationId);

            Log::info('Member leave broadcasted', [
                'group_id' => $conversationId,
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast member leave: ' . $e->getMessage(), [
                'group_id' => $conversationId,
                'user_id' => $userId,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Broadcast typing indicator
     */
    public function broadcastTypingIndicator(int $groupId, int $userId, string $userName, bool $isTyping): void
    {
        try {
            $typingData = [
                'group_id' => $groupId,
                'user_id' => $userId,
                'user_name' => $userName,
                'is_typing' => $isTyping,
                'timestamp' => now()->toISOString(),
            ];

            $event = $isTyping ? 'group_typing_start' : 'group_typing_stop';
            $this->broadcastToGroupChannel($groupId, $event, $typingData);

            // Cache typing status for a short time
            $cacheKey = "group_typing_{$groupId}_{$userId}";
            if ($isTyping) {
                Cache::put($cacheKey, true, 10); // 10 seconds
            } else {
                Cache::forget($cacheKey);
            }

        } catch (\Exception $e) {
            Log::error('Failed to broadcast typing indicator: ' . $e->getMessage(), [
                'group_id' => $groupId,
                'user_id' => $userId,
                'is_typing' => $isTyping
            ]);
        }
    }

    /**
     * Broadcast group update event
     */
    public function broadcastGroupUpdate(Conversation $conversation, array $changes = []): void
    {
        try {
            $updateData = [
                'group_id' => $conversation->id,
                'title' => $conversation->title,
                'description' => $conversation->description,
                'updated_at' => $conversation->updated_at->toISOString(),
                'changes' => $changes,
            ];

            $this->broadcastToGroupChannel($conversation->id, 'group_updated', $updateData);

            Log::info('Group update broadcasted', [
                'group_id' => $conversation->id,
                'changes' => $changes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast group update: ' . $e->getMessage(), [
                'group_id' => $conversation->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Broadcast member count update
     */
    public function broadcastMemberCountUpdate(int $groupId): void
    {
        try {
            $memberCount = GroupMember::where('conversation_id', $groupId)
                ->where('is_active', true)
                ->count();

            $countData = [
                'group_id' => $groupId,
                'count' => $memberCount,
                'updated_at' => now()->toISOString(),
            ];

            $this->broadcastToGroupChannel($groupId, 'member_count_updated', $countData);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast member count update: ' . $e->getMessage(), [
                'group_id' => $groupId,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get active typing users for a group
     */
    public function getActiveTypingUsers(int $groupId): array
    {
        try {
            $pattern = "group_typing_{$groupId}_*";
            $keys = Cache::getRedis()->keys($pattern);
            
            $typingUsers = [];
            foreach ($keys as $key) {
                $userId = str_replace("group_typing_{$groupId}_", '', $key);
                if (Cache::get($key)) {
                    $user = User::find($userId);
                    if ($user) {
                        $typingUsers[] = [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                        ];
                    }
                }
            }

            return $typingUsers;

        } catch (\Exception $e) {
            Log::error('Failed to get active typing users: ' . $e->getMessage(), [
                'group_id' => $groupId
            ]);
            return [];
        }
    }

    /**
     * Broadcast to group channel
     */
    private function broadcastToGroupChannel(int $groupId, string $event, array $data): void
    {
        $this->sendWebSocketMessage([
            'action' => 'broadcast_to_channel',
            'channel' => "group.{$groupId}",
            'event' => $event,
            'data' => $data,
        ]);
    }

    /**
     * Send message to specific user
     */
    private function sendToUser(int $userId, array $data): void
    {
        $this->sendWebSocketMessage([
            'action' => 'send_to_user',
            'user_id' => $userId,
            'data' => $data,
        ]);
    }

    /**
     * Send message to WebSocket server
     */
    private function sendWebSocketMessage(array $data): void
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serverSecret,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->websocketServerUrl . '/api/laravel-broadcast', $data);

            if (!$response->successful()) {
                Log::warning('WebSocket message failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'data' => $data
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WebSocket message error: ' . $e->getMessage(), [
                'data' => $data,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Join user to group channel
     */
    public function joinUserToGroup(int $userId, int $groupId): void
    {
        $this->sendWebSocketMessage([
            'action' => 'join_channel',
            'user_id' => $userId,
            'channel' => "group.{$groupId}",
        ]);
    }

    /**
     * Remove user from group channel
     */
    public function removeUserFromGroup(int $userId, int $groupId): void
    {
        $this->sendWebSocketMessage([
            'action' => 'leave_channel',
            'user_id' => $userId,
            'channel' => "group.{$groupId}",
        ]);
    }

    /**
     * Get group channel statistics
     */
    public function getGroupChannelStats(int $groupId): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serverSecret,
                ])
                ->get($this->websocketServerUrl . "/api/channel-stats/group.{$groupId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Failed to get group channel stats: ' . $e->getMessage(), [
                'group_id' => $groupId
            ]);
            return [];
        }
    }
}
