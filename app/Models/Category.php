<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $parent_id
 * @property int $order
 * @property string|null $icon URL hoặc class name của icon cho danh mục (material-symbols, ionicons, etc.)
 * @property string|null $color_code Mã màu hex cho danh mục (#FF5722 cho Manufacturing, #2196F3 cho CAD/CAM)
 * @property string|null $meta_description Mô tả SEO cho danh mục
 * @property string|null $meta_keywords Keywords SEO cho danh mục kỹ thuật
 * @property bool $is_technical Danh mục kỹ thuật yêu cầu expertise hay thảo luận chung
 * @property string|null $expertise_level Cấp độ chuyên môn được khuyến nghị cho danh mục
 * @property bool $requires_verification Yêu cầu verification từ expert để post trong danh mục này
 * @property array<array-key, mixed>|null $allowed_file_types Các loại file được phép upload: ["dwg","step","iges","pdf","doc","jpg"]
 * @property int $thread_count Số lượng thread trong danh mục (cached)
 * @property int $post_count Tổng số bài post trong danh mục (cached)
 * @property \Illuminate\Support\Carbon|null $last_activity_at Thời gian hoạt động cuối cùng trong danh mục
 * @property bool $is_active Danh mục có đang hoạt động không
 * @property int $sort_order Thứ tự sắp xếp danh mục (thay thế cho order)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read mixed $representative_image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thread> $threads
 * @property-read int|null $threads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereAllowedFileTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereExpertiseLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsTechnical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category wherePostCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereRequiresVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereThreadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        // MechaMap mechanical engineering fields
        'icon',
        'color_code',
        'meta_description',
        'meta_keywords',
        'is_technical',
        'expertise_level',
        'requires_verification',
        'allowed_file_types',
        'thread_count',
        'post_count',
        'last_activity_at',
        'is_active',
        'sort_order',
        // Avatar and media support
        'avatar_url',
        'avatar_media_id',
        'banner_url',
        'banner_media_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_technical' => 'boolean',
        'requires_verification' => 'boolean',
        'allowed_file_types' => 'array',
        'thread_count' => 'integer',
        'post_count' => 'integer',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the forums in this category.
     */
    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class);
    }

    /**
     * Get the threads in this category.
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Get all media attached to this category (polymorphic).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the avatar media for this category.
     */
    public function avatarMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'avatar_media_id');
    }

    /**
     * Get the banner media for this category.
     */
    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    /**
     * Get the representative image for this category.
     */
    public function getRepresentativeImageAttribute()
    {
        return $this->avatarMedia ?: $this->media()->first();
    }

    /**
     * Get the avatar URL (prioritize media over direct URL).
     */
    public function getAvatarUrlAttribute($value)
    {
        if ($this->avatarMedia) {
            // Loại bỏ slash đầu để tránh double slash
            $cleanPath = ltrim($this->avatarMedia->file_path, '/');
            return asset('storage/' . $cleanPath);
        }
        return $value;
    }

    /**
     * Get the banner URL (prioritize media over direct URL).
     */
    public function getBannerUrlAttribute($value)
    {
        if ($this->bannerMedia) {
            // Loại bỏ slash đầu để tránh double slash
            $cleanPath = ltrim($this->bannerMedia->file_path, '/');
            return asset('storage/' . $cleanPath);
        }
        return $value;
    }
}
