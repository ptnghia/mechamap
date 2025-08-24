<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'max_members',
        'requires_approval',
        'created_by_roles',
        'can_join_roles',
        'is_active',
    ];

    protected $casts = [
        'created_by_roles' => 'array',
        'can_join_roles' => 'array',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the conversations for this type
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get the group requests for this type
     */
    public function groupRequests(): HasMany
    {
        return $this->hasMany(GroupRequest::class);
    }

    /**
     * Check if a user role can create this conversation type
     */
    public function canBeCreatedBy(string $userRole): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return in_array($userRole, $this->created_by_roles ?? []);
    }

    /**
     * Check if a user role can join this conversation type
     */
    public function canBeJoinedBy(string $userRole): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return in_array($userRole, $this->can_join_roles ?? []);
    }

    /**
     * Get active conversation types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get conversation types that require approval
     */
    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    /**
     * Get conversation types available for a specific role
     */
    public function scopeAvailableForRole($query, string $role)
    {
        return $query->active()
                    ->whereJsonContains('created_by_roles', $role);
    }
}
