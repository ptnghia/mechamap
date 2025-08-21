<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $showcase_id
 * @property int $user_id
 * @property int $technical_quality
 * @property int $innovation
 * @property int $usefulness
 * @property int $documentation
 * @property float $overall_rating
 * @property string|null $review
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Showcase $showcase
 * @property-read \App\Models\User $user
 */
class ShowcaseRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'showcase_id',
        'user_id',
        'technical_quality',
        'innovation',
        'usefulness',
        'documentation',
        'overall_rating',
        'review',
        'has_media',
        'images',
        'like_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'technical_quality' => 'integer',
        'innovation' => 'integer',
        'usefulness' => 'integer',
        'documentation' => 'integer',
        'overall_rating' => 'decimal:2',
        'has_media' => 'boolean',
        'images' => 'array',
        'like_count' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate overall rating before saving
        static::saving(function ($rating) {
            $rating->overall_rating = ($rating->technical_quality +
                                    $rating->innovation +
                                    $rating->usefulness +
                                    $rating->documentation) / 4;

            // Auto-update has_media when images are set
            $rating->has_media = !empty($rating->images) && count($rating->images) > 0;
        });

        // Update like_count when likes are added/removed
        static::saved(function ($rating) {
            $rating->updateLikeCount();
        });
    }

    /**
     * Get the showcase that owns the rating.
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class);
    }

    /**
     * Get the user that created the rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get rating categories as array.
     */
    public function getCategoriesAttribute(): array
    {
        return [
            'technical_quality' => $this->technical_quality,
            'innovation' => $this->innovation,
            'usefulness' => $this->usefulness,
            'documentation' => $this->documentation,
        ];
    }

    /**
     * Get category names in Vietnamese.
     */
    public static function getCategoryNames(): array
    {
        return [
            'technical_quality' => 'Chất lượng kỹ thuật',
            'innovation' => 'Tính sáng tạo',
            'usefulness' => 'Tính hữu ích',
            'documentation' => 'Chất lượng tài liệu',
        ];
    }

    /**
     * Get all replies for this rating.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ShowcaseRatingReply::class, 'rating_id')
            ->with(['user', 'likes'])
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get all likes for this rating.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseRatingLike::class, 'rating_id');
    }

    /**
     * Check if this rating is liked by the given user.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Toggle like for this rating by the given user.
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
     * Get formatted review content with basic HTML support.
     */
    public function getFormattedReviewAttribute(): string
    {
        return $this->review ? nl2br(e($this->review)) : '';
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

    /**
     * Scope to get ratings with media.
     */
    public function scopeWithMedia($query)
    {
        return $query->where('has_media', true);
    }

    /**
     * Scope to get popular ratings (high like count).
     */
    public function scopePopular($query, int $minLikes = 1)
    {
        return $query->where('like_count', '>=', $minLikes)
            ->orderBy('like_count', 'desc');
    }
}
