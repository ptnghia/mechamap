<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ShowcaseRatingReplyLike Model
 * 
 * Quản lý likes cho replies của đánh giá showcase
 * 
 * @property int $id
 * @property int $reply_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShowcaseRatingReply $reply
 * @property-read \App\Models\User $user
 */
class ShowcaseRatingReplyLike extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'showcase_rating_reply_likes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reply_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update reply like_count when like is created
        static::created(function ($like) {
            $like->reply->updateLikeCount();
        });

        // Update reply like_count when like is deleted
        static::deleted(function ($like) {
            $like->reply->updateLikeCount();
        });
    }

    /**
     * Get the reply that owns this like.
     */
    public function reply(): BelongsTo
    {
        return $this->belongsTo(ShowcaseRatingReply::class, 'reply_id');
    }

    /**
     * Get the user who created this like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get likes by a specific user.
     */
    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to get likes for a specific reply.
     */
    public function scopeForReply($query, ShowcaseRatingReply $reply)
    {
        return $query->where('reply_id', $reply->id);
    }

    /**
     * Scope to get recent likes.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
