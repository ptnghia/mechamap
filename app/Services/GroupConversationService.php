<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationType;
use App\Models\GroupMember;
use App\Models\GroupPermission;
use App\Models\Message;
use App\Models\User;
use App\Enums\GroupRole;
use App\Enums\GroupRequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class GroupConversationService
{
    /**
     * Create a new group conversation from approved request
     */
    public function createGroupFromRequest($groupRequest): Conversation
    {
        return DB::transaction(function () use ($groupRequest) {
            // Create group conversation
            $conversation = Conversation::create([
                'title' => $groupRequest->title,
                'conversation_type_id' => $groupRequest->conversation_type_id,
                'is_group' => true,
                'group_request_id' => $groupRequest->id,
                'max_members' => $groupRequest->conversationType->max_members,
                'is_public' => false,
                'group_description' => $groupRequest->description,
                'group_rules' => $this->getDefaultGroupRules(),
            ]);

            // Add creator as group creator
            $this->addMember($conversation, $groupRequest->creator_id, GroupRole::CREATOR);

            // Create system message for group creation
            Message::createSystemMessage(
                $conversation->id,
                'group_created',
                "🎉 Nhóm '{$conversation->title}' đã được tạo thành công!",
                [
                    'group_request_id' => $groupRequest->id,
                    'creator_id' => $groupRequest->creator_id,
                    'created_at' => now()->toISOString(),
                ],
                $groupRequest->creator_id
            );

            return $conversation;
        });
    }

    /**
     * Add member to group
     */
    public function addMember(Conversation $conversation, int $userId, GroupRole $role = GroupRole::MEMBER, ?int $invitedBy = null): GroupMember
    {
        // Check if conversation is group
        if (!$conversation->is_group) {
            throw new Exception('Chỉ có thể thêm thành viên vào group conversation');
        }

        // Check if user is already a member
        if ($conversation->hasMember($userId)) {
            throw new Exception('User đã là thành viên của group này');
        }

        // Check member limit
        if ($conversation->activeMembers()->count() >= $conversation->max_members) {
            throw new Exception('Group đã đạt giới hạn số thành viên');
        }

        return DB::transaction(function () use ($conversation, $userId, $role, $invitedBy) {
            // Create group member
            $member = GroupMember::create([
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'role' => $role,
                'joined_at' => now(),
                'invited_by' => $invitedBy,
                'invitation_accepted_at' => now(),
                'is_active' => true,
            ]);

            // Create default permissions for the member
            $this->createDefaultPermissions($conversation, $userId, $role);

            // Create system message
            $user = User::find($userId);
            Message::createSystemMessage(
                $conversation->id,
                'member_joined',
                "{$user->name} đã tham gia nhóm",
                [
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'role' => $role->value,
                    'invited_by' => $invitedBy,
                ],
                $userId
            );

            return $member;
        });
    }

    /**
     * Remove member from group
     */
    public function removeMember(Conversation $conversation, int $userId, ?int $removedBy = null): bool
    {
        $member = $conversation->getMember($userId);
        
        if (!$member) {
            throw new Exception('User không phải là thành viên của group này');
        }

        // Cannot remove creator
        if ($member->role === GroupRole::CREATOR) {
            throw new Exception('Không thể xóa người tạo group');
        }

        return DB::transaction(function () use ($conversation, $member, $removedBy) {
            // Deactivate member
            $member->update([
                'is_active' => false,
                'left_at' => now(),
            ]);

            // Remove permissions
            GroupPermission::where('conversation_id', $conversation->id)
                          ->where('user_id', $member->user_id)
                          ->delete();

            // Create system message
            $user = $member->user;
            Message::createSystemMessage(
                $conversation->id,
                'member_left',
                "{$user->name} đã rời khỏi nhóm",
                [
                    'user_id' => $member->user_id,
                    'user_name' => $user->name,
                    'removed_by' => $removedBy,
                    'was_removed' => $removedBy !== null,
                ]
            );

            return true;
        });
    }

    /**
     * Change member role
     */
    public function changeMemberRole(Conversation $conversation, int $userId, GroupRole $newRole, int $changedBy): bool
    {
        $member = $conversation->getMember($userId);
        
        if (!$member) {
            throw new Exception('User không phải là thành viên của group này');
        }

        // Cannot change creator role
        if ($member->role === GroupRole::CREATOR || $newRole === GroupRole::CREATOR) {
            throw new Exception('Không thể thay đổi role của creator');
        }

        $oldRole = $member->role;

        return DB::transaction(function () use ($conversation, $member, $newRole, $oldRole, $changedBy) {
            // Update member role
            $member->update(['role' => $newRole]);

            // Update permissions
            $this->updateMemberPermissions($conversation, $member->user_id, $newRole);

            // Create system message
            $user = $member->user;
            $changer = User::find($changedBy);
            Message::createSystemMessage(
                $conversation->id,
                'role_changed',
                "{$user->name} đã được thay đổi vai trò từ {$oldRole->getDisplayName()} thành {$newRole->getDisplayName()}",
                [
                    'user_id' => $member->user_id,
                    'user_name' => $user->name,
                    'old_role' => $oldRole->value,
                    'new_role' => $newRole->value,
                    'changed_by' => $changedBy,
                    'changed_by_name' => $changer->name,
                ]
            );

            return true;
        });
    }

    /**
     * Update group settings
     */
    public function updateGroupSettings(Conversation $conversation, array $settings): bool
    {
        $allowedFields = ['title', 'group_description', 'group_rules', 'is_public'];
        $updateData = array_intersect_key($settings, array_flip($allowedFields));

        if (empty($updateData)) {
            return false;
        }

        return $conversation->update($updateData);
    }

    /**
     * Transfer group ownership
     */
    public function transferOwnership(Conversation $conversation, int $newOwnerId, int $currentOwnerId): bool
    {
        $currentOwner = $conversation->getMember($currentOwnerId);
        $newOwner = $conversation->getMember($newOwnerId);

        if (!$currentOwner || $currentOwner->role !== GroupRole::CREATOR) {
            throw new Exception('Chỉ creator mới có thể transfer ownership');
        }

        if (!$newOwner) {
            throw new Exception('User mới không phải là thành viên của group');
        }

        return DB::transaction(function () use ($conversation, $currentOwner, $newOwner) {
            // Change current owner to admin
            $currentOwner->update(['role' => GroupRole::ADMIN]);
            $this->updateMemberPermissions($conversation, $currentOwner->user_id, GroupRole::ADMIN);

            // Change new owner to creator
            $newOwner->update(['role' => GroupRole::CREATOR]);
            $this->updateMemberPermissions($conversation, $newOwner->user_id, GroupRole::CREATOR);

            // Create system message
            Message::createSystemMessage(
                $conversation->id,
                'ownership_transferred',
                "Quyền sở hữu nhóm đã được chuyển từ {$currentOwner->user->name} sang {$newOwner->user->name}",
                [
                    'old_owner_id' => $currentOwner->user_id,
                    'old_owner_name' => $currentOwner->user->name,
                    'new_owner_id' => $newOwner->user_id,
                    'new_owner_name' => $newOwner->user->name,
                ]
            );

            return true;
        });
    }

    /**
     * Create default permissions for member based on role
     */
    private function createDefaultPermissions(Conversation $conversation, int $userId, GroupRole $role): void
    {
        $permissions = match($role) {
            GroupRole::CREATOR => [
                'can_invite_members' => true,
                'can_remove_members' => true,
                'can_moderate_content' => true,
                'can_change_settings' => true,
                'can_manage_roles' => true,
                'can_delete_group' => true,
            ],
            GroupRole::ADMIN => [
                'can_invite_members' => true,
                'can_remove_members' => true,
                'can_moderate_content' => true,
                'can_change_settings' => true,
                'can_manage_roles' => true,
                'can_delete_group' => false,
            ],
            GroupRole::MODERATOR => [
                'can_invite_members' => false,
                'can_remove_members' => false,
                'can_moderate_content' => true,
                'can_change_settings' => false,
                'can_manage_roles' => false,
                'can_delete_group' => false,
            ],
            GroupRole::MEMBER => [
                'can_invite_members' => false,
                'can_remove_members' => false,
                'can_moderate_content' => false,
                'can_change_settings' => false,
                'can_manage_roles' => false,
                'can_delete_group' => false,
            ],
        };

        GroupPermission::create(array_merge([
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
        ], $permissions));
    }

    /**
     * Update member permissions based on new role
     */
    private function updateMemberPermissions(Conversation $conversation, int $userId, GroupRole $role): void
    {
        $permission = GroupPermission::where('conversation_id', $conversation->id)
                                   ->where('user_id', $userId)
                                   ->first();

        if ($permission) {
            match($role) {
                GroupRole::CREATOR => $permission->grantAllPermissions(),
                GroupRole::ADMIN => $permission->grantAdminPermissions(),
                GroupRole::MODERATOR => $permission->grantModeratorPermissions(),
                GroupRole::MEMBER => $permission->grantBasicPermissions(),
            };
        }
    }

    /**
     * Get default group rules
     */
    private function getDefaultGroupRules(): string
    {
        return 'Quy tắc nhóm:
1. Tôn trọng ý kiến của các thành viên khác
2. Chia sẻ kiến thức một cách tích cực
3. Không spam hoặc quảng cáo không liên quan
4. Sử dụng ngôn ngữ lịch sự và chuyên nghiệp
5. Tập trung vào chủ đề chính của nhóm';
    }
}
