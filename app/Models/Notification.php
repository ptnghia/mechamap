<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification Model - Phase 3
 * Model cho hệ thống thông báo nâng cao
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'priority',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope cho notifications chưa đọc
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope cho notifications theo type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope cho notifications theo priority
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'business_verified' => 'check-circle',
            'business_rejected' => 'times-circle',
            'product_approved' => 'box',
            'product_rejected' => 'box',
            'order_update' => 'shopping-cart',
            'role_changed' => 'user-cog',
            'commission_paid' => 'dollar-sign',
            'system_announcement' => 'bullhorn',
            'quote_request' => 'file-invoice',
            'user_registered' => 'user-plus',
            'forum_activity' => 'comments',
            'marketplace_activity' => 'store',
            default => 'bell',
        };
    }

    /**
     * Get notification color based on type and priority
     */
    public function getColorAttribute(): string
    {
        if ($this->priority === 'high') {
            return 'danger';
        }

        return match ($this->type) {
            'business_verified', 'product_approved', 'commission_paid' => 'success',
            'business_rejected', 'product_rejected' => 'danger',
            'order_update' => 'info',
            'role_changed' => 'warning',
            'system_announcement' => 'primary',
            'quote_request' => 'info',
            default => 'secondary',
        };
    }



    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if notification has action URL
     */
    public function hasActionUrl(): bool
    {
        return isset($this->data['action_url']) && !empty($this->data['action_url']);
    }

    /**
     * Get action URL
     */
    public function getActionUrl(): ?string
    {
        return $this->data['action_url'] ?? null;
    }
}
