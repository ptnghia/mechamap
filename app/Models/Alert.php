<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property string|null $alertable_type
 * @property int|null $alertable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent|null $alertable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert read()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereUserId($value)
 * @mixin \Eloquent
 */
class Alert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'read_at',
        'alertable_id',
        'alertable_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent alertable model.
     */
    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the comment if the alert is related to a comment.
     */
    public function comment()
    {
        if ($this->alertable_type === Comment::class) {
            return $this->belongsTo(Comment::class, 'alertable_id');
        }
        return null;
    }

    /**
     * Get the conversation if the alert is related to a conversation.
     */
    public function conversation()
    {
        if ($this->alertable_type === Conversation::class) {
            return $this->belongsTo(Conversation::class, 'alertable_id');
        }
        return null;
    }

    /**
     * Scope a query to only include unread alerts.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read alerts.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Check if the alert is read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark the alert as read.
     */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }
}
