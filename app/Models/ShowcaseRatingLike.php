<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ShowcaseRatingLike Model
 * 
 * Quản lý likes cho đánh giá showcase
 * 
 * @property int $id
 * @property int $rating_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShowcaseRating $rating
 * @property-read \App\Models\User $user
 */
class ShowcaseRatingLike extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'showcase_rating_likes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'rating_id',
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

        // Update rating like_count when like is created
        static::created(function ($like) {
            $like->rating->updateLikeCount();
        });

        // Update rating like_count when like is deleted
        static::deleted(function ($like) {
            $like->rating->updateLikeCount();
        });
    }

    /**
     * Get the rating that owns this like.
     */
    public function rating(): BelongsTo
    {
        return $this->belongsTo(ShowcaseRating::class, 'rating_id');
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
     * Scope to get likes for a specific rating.
     */
    public function scopeForRating($query, ShowcaseRating $rating)
    {
        return $query->where('rating_id', $rating->id);
    }

    /**
     * Scope to get recent likes.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
