<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\GroupMember;
use App\Models\Message;
use App\Events\GroupTypingIndicator;
use App\Services\GroupWebSocketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Group WebSocket Controller
 * 
 * Handles WebSocket-related API endpoints for group conversations
 */
class GroupWebSocketController extends Controller
{
    private GroupWebSocketService $groupWebSocketService;

    public function __construct(GroupWebSocketService $groupWebSocketService)
    {
        $this->groupWebSocketService = $groupWebSocketService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Join a group channel
     */
    public function joinGroup(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required|integer|exists:conversations,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid group ID',
                    'errors' => $validator->errors()
                ], 400);
            }

            $groupId = $request->input('group_id');
            $user = Auth::user();

            // Check if user is a member of the group
            $member = GroupMember::where('conversation_id', $groupId)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

            // Join user to group WebSocket channel
            $this->groupWebSocketService->joinUserToGroup($user->id, $groupId);

            Log::info('User joined group WebSocket channel', [
                'user_id' => $user->id,
                'group_id' => $groupId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined group channel',
                'data' => [
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to join group channel: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'group_id' => $request->input('group_id'),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to join group channel'
            ], 500);
        }
    }

    /**
     * Leave a group channel
     */
    public function leaveGroup(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required|integer|exists:conversations,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid group ID',
                    'errors' => $validator->errors()
                ], 400);
            }

            $groupId = $request->input('group_id');
            $user = Auth::user();

            // Remove user from group WebSocket channel
            $this->groupWebSocketService->removeUserFromGroup($user->id, $groupId);

            Log::info('User left group WebSocket channel', [
                'user_id' => $user->id,
                'group_id' => $groupId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully left group channel',
                'data' => [
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to leave group channel: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'group_id' => $request->input('group_id'),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group channel'
            ], 500);
        }
    }

    /**
     * Send typing indicator
     */
    public function sendTyping(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required|integer|exists:conversations,id',
                'is_typing' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request data',
                    'errors' => $validator->errors()
                ], 400);
            }

            $groupId = $request->input('group_id');
            $isTyping = $request->input('is_typing');
            $user = Auth::user();

            // Check if user is a member of the group
            $member = GroupMember::where('conversation_id', $groupId)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

            $conversation = Conversation::find($groupId);

            // Broadcast typing indicator
            event(new GroupTypingIndicator($user, $conversation, $isTyping));

            // Use GroupWebSocketService for enhanced features
            $this->groupWebSocketService->broadcastTypingIndicator(
                $groupId,
                $user->id,
                $user->name,
                $isTyping
            );

            return response()->json([
                'success' => true,
                'message' => 'Typing indicator sent',
                'data' => [
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                    'is_typing' => $isTyping,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send typing indicator: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'group_id' => $request->input('group_id'),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send typing indicator'
            ], 500);
        }
    }

    /**
     * Get group channel statistics
     */
    public function getChannelStats(Request $request, int $groupId): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check if user is a member of the group
            $member = GroupMember::where('conversation_id', $groupId)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

            // Get channel statistics
            $stats = $this->groupWebSocketService->getGroupChannelStats($groupId);

            // Get active typing users
            $typingUsers = $this->groupWebSocketService->getActiveTypingUsers($groupId);

            return response()->json([
                'success' => true,
                'data' => [
                    'group_id' => $groupId,
                    'channel_stats' => $stats,
                    'typing_users' => $typingUsers,
                    'member_count' => GroupMember::where('conversation_id', $groupId)
                        ->where('is_active', true)
                        ->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get channel stats: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'group_id' => $groupId,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get channel statistics'
            ], 500);
        }
    }

    /**
     * Get active typing users
     */
    public function getTypingUsers(Request $request, int $groupId): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check if user is a member of the group
            $member = GroupMember::where('conversation_id', $groupId)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

            // Get active typing users
            $typingUsers = $this->groupWebSocketService->getActiveTypingUsers($groupId);

            return response()->json([
                'success' => true,
                'data' => [
                    'group_id' => $groupId,
                    'typing_users' => $typingUsers,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get typing users: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'group_id' => $groupId,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get typing users'
            ], 500);
        }
    }
}
