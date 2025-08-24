<?php

namespace App\Observers;

use App\Models\GroupMember;
use App\Events\GroupMemberJoined;
use App\Events\GroupMemberLeft;
use App\Services\GroupWebSocketService;
use Illuminate\Support\Facades\Log;

/**
 * Group Member Observer
 * 
 * Handles events when group members join or leave
 */
class GroupMemberObserver
{
    /**
     * Handle the GroupMember "created" event.
     */
    public function created(GroupMember $member): void
    {
        try {
            Log::info('Group member created', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id,
                'role' => $member->role->value
            ]);

            // Broadcast member joined event
            event(new GroupMemberJoined($member));

            // Use GroupWebSocketService for enhanced features
            $groupWebSocketService = app(GroupWebSocketService::class);
            $groupWebSocketService->broadcastMemberJoined($member);

            // Join user to group WebSocket channel
            $groupWebSocketService->joinUserToGroup($member->user_id, $member->conversation_id);

            Log::info('Group member join events broadcasted', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle group member creation', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the GroupMember "updated" event.
     */
    public function updated(GroupMember $member): void
    {
        try {
            // Check if member was deactivated (left the group)
            if ($member->wasChanged('is_active') && !$member->is_active) {
                $this->handleMemberLeft($member);
                return;
            }

            // Check if member role was changed
            if ($member->wasChanged('role')) {
                $this->handleMemberRoleChanged($member);
            }

            Log::info('Group member updated', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id,
                'changes' => $member->getChanges()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle group member update', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the GroupMember "deleted" event.
     */
    public function deleted(GroupMember $member): void
    {
        try {
            $this->handleMemberLeft($member);

            Log::info('Group member deleted', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle group member deletion', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle member leaving the group
     */
    private function handleMemberLeft(GroupMember $member): void
    {
        try {
            $user = $member->user;
            $conversation = $member->conversation;

            if (!$user || !$conversation) {
                Log::warning('User or conversation not found for member left event', [
                    'member_id' => $member->id,
                    'user_id' => $member->user_id,
                    'conversation_id' => $member->conversation_id
                ]);
                return;
            }

            // Broadcast member left event
            event(new GroupMemberLeft($user, $conversation, 'left'));

            // Use GroupWebSocketService for enhanced features
            $groupWebSocketService = app(GroupWebSocketService::class);
            $groupWebSocketService->broadcastMemberLeft(
                $conversation->id,
                $user->id,
                $user->name
            );

            // Remove user from group WebSocket channel
            $groupWebSocketService->removeUserFromGroup($user->id, $conversation->id);

            Log::info('Group member left events broadcasted', [
                'member_id' => $member->id,
                'group_id' => $conversation->id,
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle member left', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle member role change
     */
    private function handleMemberRoleChanged(GroupMember $member): void
    {
        try {
            $groupWebSocketService = app(GroupWebSocketService::class);
            
            // Broadcast member update with role change
            $groupWebSocketService->broadcastToGroupChannel(
                $member->conversation_id,
                'member_role_updated',
                [
                    'group_id' => $member->conversation_id,
                    'user_id' => $member->user_id,
                    'user_name' => $member->user->name,
                    'old_role' => $member->getOriginal('role'),
                    'new_role' => $member->role->value,
                    'updated_at' => now()->toISOString(),
                ]
            );

            Log::info('Group member role change broadcasted', [
                'member_id' => $member->id,
                'group_id' => $member->conversation_id,
                'user_id' => $member->user_id,
                'old_role' => $member->getOriginal('role'),
                'new_role' => $member->role->value
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle member role change', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
