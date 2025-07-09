<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 *
 *
 * @property int $id
 * @property int $showcase_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $comment
 * @property int $like_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ShowcaseComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ShowcaseComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\Showcase $showcase
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereShowcaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseComment whereUserId($value)
 * @mixin \Eloquent
 */
class ShowcaseComment extends Model
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
        'parent_id',
        'comment',
        'has_media',
        'like_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_media' => 'boolean',
        'like_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Lấy showcase sở hữu comment này.
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class);
    }

    /**
     * Lấy user tạo comment này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy comment cha (parent comment).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ShowcaseComment::class, 'parent_id');
    }

    /**
     * Lấy các comment con (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ShowcaseComment::class, 'parent_id');
    }

    /**
     * Get all media attached to this comment (polymorphic).
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the likes for the comment.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseCommentLike::class);
    }

    /**
     * Check if the comment is liked by the given user.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
