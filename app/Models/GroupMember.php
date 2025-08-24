<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\GroupRole;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'joined_at',
        'invited_by',
        'invitation_accepted_at',
        'is_active',
        'left_at',
    ];

    protected $casts = [
        'role' => GroupRole::class,
        'joined_at' => 'datetime',
        'invitation_accepted_at' => 'datetime',
        'left_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the conversation this member belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user for this member
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who invited this member
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Check if member is creator
     */
    public function isCreator(): bool
    {
        return $this->role === GroupRole::CREATOR;
    }

    /**
     * Check if member is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === GroupRole::ADMIN;
    }

    /**
     * Check if member is moderator
     */
    public function isModerator(): bool
    {
        return $this->role === GroupRole::MODERATOR;
    }

    /**
     * Check if member has management privileges
     */
    public function hasManagementPrivileges(): bool
    {
        return in_array($this->role, [GroupRole::CREATOR, GroupRole::ADMIN]);
    }

    /**
     * Check if member can moderate content
     */
    public function canModerateContent(): bool
    {
        return in_array($this->role, [
            GroupRole::CREATOR, 
            GroupRole::ADMIN, 
            GroupRole::MODERATOR
        ]);
    }

    /**
     * Get role power level
     */
    public function getRolePowerLevel(): int
    {
        return $this->role->getPowerLevel();
    }

    /**
     * Check if this member can promote another member to a role
     */
    public function canPromoteToRole(GroupRole $targetRole): bool
    {
        // Only creator can promote to admin
        if ($targetRole === GroupRole::ADMIN && $this->role !== GroupRole::CREATOR) {
            return false;
        }

        // Cannot promote to creator
        if ($targetRole === GroupRole::CREATOR) {
            return false;
        }

        // Must have higher power level
        return $this->getRolePowerLevel() > $targetRole->getPowerLevel();
    }

    /**
     * Scope for active members
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for members by role
     */
    public function scopeByRole($query, GroupRole $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for creators
     */
    public function scopeCreators($query)
    {
        return $query->where('role', GroupRole::CREATOR);
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', GroupRole::ADMIN);
    }

    /**
     * Scope for moderators
     */
    public function scopeModerators($query)
    {
        return $query->where('role', GroupRole::MODERATOR);
    }

    /**
     * Scope for regular members
     */
    public function scopeRegularMembers($query)
    {
        return $query->where('role', GroupRole::MEMBER);
    }

    /**
     * Scope for management roles (creator + admin)
     */
    public function scopeManagement($query)
    {
        return $query->whereIn('role', [GroupRole::CREATOR, GroupRole::ADMIN]);
    }
}
