<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'can_invite_members',
        'can_remove_members',
        'can_moderate_content',
        'can_change_settings',
        'can_manage_roles',
        'can_delete_group',
    ];

    protected $casts = [
        'can_invite_members' => 'boolean',
        'can_remove_members' => 'boolean',
        'can_moderate_content' => 'boolean',
        'can_change_settings' => 'boolean',
        'can_manage_roles' => 'boolean',
        'can_delete_group' => 'boolean',
    ];

    /**
     * Get the conversation for this permission
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user for this permission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user has any management permissions
     */
    public function hasAnyManagementPermission(): bool
    {
        return $this->can_remove_members ||
               $this->can_change_settings ||
               $this->can_manage_roles ||
               $this->can_delete_group;
    }

    /**
     * Check if user has any moderation permissions
     */
    public function hasAnyModerationPermission(): bool
    {
        return $this->can_moderate_content ||
               $this->can_remove_members;
    }

    /**
     * Get all permissions as array
     */
    public function getPermissionsArray(): array
    {
        return [
            'can_invite_members' => $this->can_invite_members,
            'can_remove_members' => $this->can_remove_members,
            'can_moderate_content' => $this->can_moderate_content,
            'can_change_settings' => $this->can_change_settings,
            'can_manage_roles' => $this->can_manage_roles,
            'can_delete_group' => $this->can_delete_group,
        ];
    }

    /**
     * Set permissions from array
     */
    public function setPermissionsFromArray(array $permissions): void
    {
        $this->fill($permissions);
    }

    /**
     * Grant all permissions
     */
    public function grantAllPermissions(): void
    {
        $this->update([
            'can_invite_members' => true,
            'can_remove_members' => true,
            'can_moderate_content' => true,
            'can_change_settings' => true,
            'can_manage_roles' => true,
            'can_delete_group' => true,
        ]);
    }

    /**
     * Revoke all permissions
     */
    public function revokeAllPermissions(): void
    {
        $this->update([
            'can_invite_members' => false,
            'can_remove_members' => false,
            'can_moderate_content' => false,
            'can_change_settings' => false,
            'can_manage_roles' => false,
            'can_delete_group' => false,
        ]);
    }

    /**
     * Grant basic member permissions
     */
    public function grantBasicPermissions(): void
    {
        $this->update([
            'can_invite_members' => false,
            'can_remove_members' => false,
            'can_moderate_content' => false,
            'can_change_settings' => false,
            'can_manage_roles' => false,
            'can_delete_group' => false,
        ]);
    }

    /**
     * Grant moderator permissions
     */
    public function grantModeratorPermissions(): void
    {
        $this->update([
            'can_invite_members' => false,
            'can_remove_members' => false,
            'can_moderate_content' => true,
            'can_change_settings' => false,
            'can_manage_roles' => false,
            'can_delete_group' => false,
        ]);
    }

    /**
     * Grant admin permissions
     */
    public function grantAdminPermissions(): void
    {
        $this->update([
            'can_invite_members' => true,
            'can_remove_members' => true,
            'can_moderate_content' => true,
            'can_change_settings' => true,
            'can_manage_roles' => true,
            'can_delete_group' => false,
        ]);
    }
}
