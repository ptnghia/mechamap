<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\GroupMember;
use App\Models\GroupPermission;
use App\Models\User;
use App\Enums\GroupRole;

class GroupPermissionService
{
    /**
     * Check if user has permission in group
     */
    public function hasPermission(User $user, Conversation $group, string $permission): bool
    {
        if (!$group->is_group) {
            return false;
        }

        $member = $group->getMember($user->id);
        if (!$member || !$member->is_active) {
            return false;
        }

        $role = $member->role;

        return match($permission) {
            'invite_members' => $this->canInviteMembers($role, $group, $user),
            'remove_members' => $this->canRemoveMembers($role),
            'change_roles' => $this->canChangeRoles($role),
            'moderate_content' => $this->canModerateContent($role),
            'change_settings' => $this->canChangeSettings($role),
            'manage_group' => $this->canManageGroup($role),
            'delete_group' => $this->canDeleteGroup($role),
            'send_messages' => $this->canSendMessages($role),
            'view_members' => $this->canViewMembers($role),
            'leave_group' => $this->canLeaveGroup($role),
            default => false
        };
    }

    /**
     * Check if user can invite members
     */
    private function canInviteMembers(GroupRole $role, Conversation $group, User $user): bool
    {
        if (in_array($role, [GroupRole::CREATOR, GroupRole::ADMIN])) {
            return true;
        }

        // Check custom permissions for members
        $permissions = $group->permissions()->where('user_id', $user->id)->first();
        return $permissions?->can_invite_members ?? false;
    }

    /**
     * Check if user can remove members
     */
    private function canRemoveMembers(GroupRole $role): bool
    {
        return in_array($role, [GroupRole::CREATOR, GroupRole::ADMIN]);
    }

    /**
     * Check if user can change roles
     */
    private function canChangeRoles(GroupRole $role): bool
    {
        return in_array($role, [GroupRole::CREATOR, GroupRole::ADMIN]);
    }

    /**
     * Check if user can moderate content
     */
    private function canModerateContent(GroupRole $role): bool
    {
        return in_array($role, [GroupRole::CREATOR, GroupRole::ADMIN, GroupRole::MODERATOR]);
    }

    /**
     * Check if user can change settings
     */
    private function canChangeSettings(GroupRole $role): bool
    {
        return in_array($role, [GroupRole::CREATOR, GroupRole::ADMIN]);
    }

    /**
     * Check if user can manage group
     */
    private function canManageGroup(GroupRole $role): bool
    {
        return $role === GroupRole::CREATOR;
    }

    /**
     * Check if user can delete group
     */
    private function canDeleteGroup(GroupRole $role): bool
    {
        return $role === GroupRole::CREATOR;
    }

    /**
     * Check if user can send messages
     */
    private function canSendMessages(GroupRole $role): bool
    {
        return true; // All members can send messages
    }

    /**
     * Check if user can view members
     */
    private function canViewMembers(GroupRole $role): bool
    {
        return true; // All members can view member list
    }

    /**
     * Check if user can leave group
     */
    private function canLeaveGroup(GroupRole $role): bool
    {
        return $role !== GroupRole::CREATOR; // Creator cannot leave, must transfer ownership first
    }

    /**
     * Check if user can promote another user to specific role
     */
    public function canPromoteToRole(User $promoter, GroupRole $targetRole, Conversation $group): bool
    {
        $promoterMember = $group->getMember($promoter->id);
        if (!$promoterMember) {
            return false;
        }

        $promoterRole = $promoterMember->role;

        // Only creator can promote to admin
        if ($targetRole === GroupRole::ADMIN && $promoterRole !== GroupRole::CREATOR) {
            return false;
        }

        // Cannot promote to creator
        if ($targetRole === GroupRole::CREATOR) {
            return false;
        }

        // Must have higher power level
        return $promoterRole->getPowerLevel() > $targetRole->getPowerLevel();
    }

    /**
     * Check if user can demote another user
     */
    public function canDemote(User $demoter, User $target, Conversation $group): bool
    {
        $demoterMember = $group->getMember($demoter->id);
        $targetMember = $group->getMember($target->id);

        if (!$demoterMember || !$targetMember) {
            return false;
        }

        // Cannot demote creator
        if ($targetMember->role === GroupRole::CREATOR) {
            return false;
        }

        // Must have higher power level
        return $demoterMember->role->getPowerLevel() > $targetMember->role->getPowerLevel();
    }

    /**
     * Check if user can remove specific member
     */
    public function canRemoveMember(User $remover, User $target, Conversation $group): bool
    {
        if (!$this->hasPermission($remover, $group, 'remove_members')) {
            return false;
        }

        $targetMember = $group->getMember($target->id);
        if (!$targetMember) {
            return false;
        }

        // Cannot remove creator
        if ($targetMember->role === GroupRole::CREATOR) {
            return false;
        }

        $removerMember = $group->getMember($remover->id);
        
        // Must have higher or equal power level (admins can remove other admins)
        return $removerMember->role->getPowerLevel() >= $targetMember->role->getPowerLevel();
    }

    /**
     * Get user's effective permissions in group
     */
    public function getUserPermissions(User $user, Conversation $group): array
    {
        if (!$group->is_group) {
            return [];
        }

        $member = $group->getMember($user->id);
        if (!$member || !$member->is_active) {
            return [];
        }

        $permissions = [
            'send_messages' => $this->hasPermission($user, $group, 'send_messages'),
            'view_members' => $this->hasPermission($user, $group, 'view_members'),
            'invite_members' => $this->hasPermission($user, $group, 'invite_members'),
            'remove_members' => $this->hasPermission($user, $group, 'remove_members'),
            'change_roles' => $this->hasPermission($user, $group, 'change_roles'),
            'moderate_content' => $this->hasPermission($user, $group, 'moderate_content'),
            'change_settings' => $this->hasPermission($user, $group, 'change_settings'),
            'manage_group' => $this->hasPermission($user, $group, 'manage_group'),
            'delete_group' => $this->hasPermission($user, $group, 'delete_group'),
            'leave_group' => $this->hasPermission($user, $group, 'leave_group'),
        ];

        return $permissions;
    }

    /**
     * Get available roles that user can assign
     */
    public function getAvailableRoles(User $user, Conversation $group): array
    {
        $member = $group->getMember($user->id);
        if (!$member) {
            return [];
        }

        $userRole = $member->role;
        $availableRoles = [];

        foreach (GroupRole::cases() as $role) {
            if ($role === GroupRole::CREATOR) {
                continue; // Creator role cannot be assigned
            }

            if ($this->canPromoteToRole($user, $role, $group)) {
                $availableRoles[] = [
                    'value' => $role->value,
                    'label' => $role->getDisplayName(),
                    'color' => $role->getColor(),
                    'icon' => $role->getIcon(),
                ];
            }
        }

        return $availableRoles;
    }

    /**
     * Check if group has reached member limit
     */
    public function hasReachedMemberLimit(Conversation $group): bool
    {
        return $group->activeMembers()->count() >= $group->max_members;
    }

    /**
     * Get member statistics for group
     */
    public function getMemberStatistics(Conversation $group): array
    {
        $members = $group->activeMembers()->with('user')->get();

        $stats = [
            'total_members' => $members->count(),
            'max_members' => $group->max_members,
            'available_slots' => $group->max_members - $members->count(),
            'roles' => [
                'creator' => $members->where('role', GroupRole::CREATOR)->count(),
                'admin' => $members->where('role', GroupRole::ADMIN)->count(),
                'moderator' => $members->where('role', GroupRole::MODERATOR)->count(),
                'member' => $members->where('role', GroupRole::MEMBER)->count(),
            ],
            'recent_joins' => $members->where('joined_at', '>=', now()->subDays(7))->count(),
        ];

        return $stats;
    }
}
