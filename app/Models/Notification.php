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

    /**
     * The table associated with the model.
     */
    protected $table = 'custom_notifications';

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
            // New forum notification types
            'thread_created' => 'plus-circle',
            'thread_replied' => 'reply',
            'comment_mention' => 'at',
            // Security notification types
            'login_from_new_device' => 'shield-alt',
            'password_changed' => 'key',
            default => 'bell',
        };
    }

    /**
     * Get notification category based on type
     */
    public function getCategoryAttribute(): string
    {
        return match ($this->type) {
            'comment', 'like', 'follow', 'mention', 'reply' => 'social',
            'thread_created', 'thread_replied', 'comment_mention', 'showcase' => 'content',
            'business_verified', 'business_rejected', 'product_approved', 'product_rejected', 'order_update', 'commission_paid', 'quote_request' => 'marketplace',
            'login_from_new_device', 'password_changed' => 'security',
            'system_announcement', 'role_changed', 'system' => 'system',
            default => 'system',
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
            // New forum notification colors
            'thread_created' => 'success',
            'thread_replied' => 'info',
            'comment_mention' => 'warning',
            // Security notification colors
            'login_from_new_device' => 'warning',
            'password_changed' => 'danger',
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

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get notifications with optimized queries
     */
    public static function getOptimizedNotifications(User $user, int $limit = 20)
    {
        return static::select(['id', 'user_id', 'type', 'title', 'message', 'data', 'priority', 'is_read', 'read_at', 'created_at'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count efficiently
     */
    public static function getUnreadCount(User $user): int
    {
        return static::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark multiple notifications as read efficiently
     */
    public static function markAsReadBulk(User $user, array $notificationIds = []): int
    {
        $query = static::where('user_id', $user->id)
            ->where('is_read', false);

        if (!empty($notificationIds)) {
            $query->whereIn('id', $notificationIds);
        }

        return $query->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Get notifications by type with pagination
     */
    public static function getByTypeOptimized(User $user, string $type, int $perPage = 15)
    {
        return static::where('user_id', $user->id)
            ->where('type', $type)
            ->with(['user:id,name,avatar'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Clean old notifications (older than specified days)
     */
    public static function cleanOldNotifications(int $days = 90): int
    {
        return static::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * Get notification type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'thread_created' => 'Thread mới',
            'thread_replied' => 'Reply thread',
            'comment_mention' => 'Được nhắc đến',
            'login_from_new_device' => 'Đăng nhập thiết bị mới',
            'password_changed' => 'Đổi mật khẩu',
            'product_out_of_stock' => 'Hết hàng',
            'price_drop_alert' => 'Giảm giá',
            'wishlist_available' => 'Wishlist có hàng',
            'review_received' => 'Nhận đánh giá',
            'seller_message' => 'Tin nhắn seller',
            'user_followed' => 'Được theo dõi',
            'achievement_unlocked' => 'Thành tựu mới',
            'weekly_digest' => 'Tổng hợp tuần',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

}
