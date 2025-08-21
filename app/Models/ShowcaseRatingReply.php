<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ShowcaseRatingReply Model
 * 
 * Quản lý replies cho đánh giá showcase
 * Hỗ trợ nested replies và media attachments
 * 
 * @property int $id
 * @property int $rating_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $content
 * @property bool $has_media
 * @property array|null $images
 * @property int $like_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShowcaseRating $rating
 * @property-read \App\Models\User $user
 * @property-read ShowcaseRatingReply|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ShowcaseRatingReply> $replies
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShowcaseRatingReplyLike> $likes
 */
class ShowcaseRatingReply extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'showcase_rating_replies';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'rating_id',
        'user_id',
        'parent_id',
        'content',
        'has_media',
        'images',
        'like_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'has_media' => 'boolean',
        'images' => 'array',
        'like_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update has_media when images are set
        static::saving(function ($reply) {
            $reply->has_media = !empty($reply->images) && count($reply->images) > 0;
        });

        // Update like_count when likes are added/removed
        static::saved(function ($reply) {
            $reply->updateLikeCount();
        });
    }

    /**
     * Get the rating that owns this reply.
     */
    public function rating(): BelongsTo
    {
        return $this->belongsTo(ShowcaseRating::class, 'rating_id');
    }

    /**
     * Get the user who created this reply.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent reply (for nested replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ShowcaseRatingReply::class, 'parent_id');
    }

    /**
     * Get the child replies (nested replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ShowcaseRatingReply::class, 'parent_id')
            ->with(['user', 'likes'])
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get all likes for this reply.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseRatingReplyLike::class, 'reply_id');
    }

    /**
     * Check if this reply is liked by the given user.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Toggle like for this reply by the given user.
     */
    public function toggleLike(User $user): bool
    {
        $existingLike = $this->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $this->updateLikeCount();
            return false; // Unliked
        } else {
            $this->likes()->create(['user_id' => $user->id]);
            $this->updateLikeCount();
            return true; // Liked
        }
    }

    /**
     * Update the like_count based on actual likes.
     */
    public function updateLikeCount(): void
    {
        $this->like_count = $this->likes()->count();
        $this->saveQuietly(); // Save without triggering events
    }

    /**
     * Get the depth level of this reply (for nested display).
     */
    public function getDepthLevel(): int
    {
        $depth = 0;
        $current = $this;

        while ($current->parent_id) {
            $depth++;
            $current = $current->parent;
            
            // Prevent infinite loop
            if ($depth > 10) break;
        }

        return $depth;
    }

    /**
     * Get all replies in a thread (including nested).
     */
    public function getThreadReplies()
    {
        return static::where('rating_id', $this->rating_id)
            ->where(function ($query) {
                $query->where('parent_id', $this->id)
                    ->orWhere('id', $this->id);
            })
            ->with(['user', 'likes'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Scope to get only top-level replies (no parent).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get replies with media.
     */
    public function scopeWithMedia($query)
    {
        return $query->where('has_media', true);
    }

    /**
     * Scope to get popular replies (high like count).
     */
    public function scopePopular($query, int $minLikes = 1)
    {
        return $query->where('like_count', '>=', $minLikes)
            ->orderBy('like_count', 'desc');
    }

    /**
     * Get formatted content with basic HTML support.
     */
    public function getFormattedContentAttribute(): string
    {
        return nl2br(e($this->content));
    }

    /**
     * Get image URLs if images exist.
     */
    public function getImageUrlsAttribute(): array
    {
        if (!$this->has_media || empty($this->images)) {
            return [];
        }

        return collect($this->images)->map(function ($image) {
            // Assuming images are stored as relative paths
            return asset('storage/' . $image);
        })->toArray();
    }
}
