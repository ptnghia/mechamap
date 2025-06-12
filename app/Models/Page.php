<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $excerpt
 * @property string $page_type
 * @property string|null $technical_specs
 * @property string|null $prerequisites
 * @property string|null $difficulty_level
 * @property int|null $estimated_read_time
 * @property string|null $featured_image
 * @property string|null $related_software
 * @property string|null $engineering_standards
 * @property int $category_id
 * @property int $user_id
 * @property string $status
 * @property string|null $published_at
 * @property string|null $reviewed_at
 * @property int $order
 * @property bool $is_featured
 * @property int $view_count
 * @property string $rating_average
 * @property int $rating_count
 * @property int $requires_login
 * @property int $is_premium
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $author_id
 * @property int|null $reviewer_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\PageCategory $category
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDifficultyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereEngineeringStandards($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereEstimatedReadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereFeaturedImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePrerequisites($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereRatingAverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereRatingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereRelatedSoftware($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereRequiresLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTechnicalSpecs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereViewCount($value)
 * @mixin \Eloquent
 */
class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category_id',
        'user_id',
        'status',
        'order',
        'is_featured',
        'view_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /**
     * Get the user that created the page.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the page.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PageCategory::class, 'category_id');
    }

    /**
     * Get all media attached to this page.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured pages.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
