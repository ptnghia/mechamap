<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $showcaseable_type
 * @property int $showcaseable_id
 * @property string $title Tiêu đề dự án kỹ thuật
 * @property string $slug URL-friendly identifier cho project
 * @property string|null $description Mô tả chi tiết dự án, phương pháp, kết quả
 * @property string|null $project_type Loại dự án kỹ thuật
 * @property string|null $software_used Phần mềm sử dụng: SolidWorks, AutoCAD, ANSYS, CATIA, Fusion360
 * @property string|null $materials Vật liệu sử dụng: Steel, Aluminum, Composite, Plastic, etc.
 * @property string|null $manufacturing_process Quy trình sản xuất: CNC, 3D Printing, Casting, Welding, Machining
 * @property string|null $technical_specs Thông số kỹ thuật: {"dimensions":"100x50x20mm","tolerance":"±0.01","weight":"2.5kg"}
 * @property string $category Danh mục dự án
 * @property string $complexity_level Mức độ phức tạp kỹ thuật
 * @property string|null $industry_application Ứng dụng ngành công nghiệp
 * @property int $has_tutorial Project có kèm hướng dẫn step-by-step không
 * @property int $has_calculations Project có kèm tính toán kỹ thuật không
 * @property int $has_cad_files Project có file CAD đính kèm không
 * @property string|null $learning_objectives Mục tiêu học tập: ["FEA analysis","Design optimization","Manufacturing process"]
 * @property string|null $cover_image Ảnh đại diện chính của project
 * @property string|null $image_gallery Gallery ảnh process và kết quả
 * @property string|null $file_attachments Files đính kèm: CAD, drawings, calculations, reports
 * @property string $status Trạng thái review và publication
 * @property int $is_public Project có public access không
 * @property int $allow_downloads Cho phép download files không
 * @property int $allow_comments Cho phép comment và discussion không
 * @property int $view_count Số lượt xem project
 * @property int $like_count Số lượt like từ community
 * @property int $download_count Số lượt download files
 * @property int $share_count Số lượt chia sẻ project
 * @property string $rating_average Đánh giá trung bình (0.00 - 5.00)
 * @property int $rating_count Số lượng đánh giá
 * @property string $technical_quality_score Điểm chất lượng kỹ thuật do expert đánh giá
 * @property int $display_order Thứ tự hiển thị trong category
 * @property string|null $featured_at Thời gian được featured
 * @property string|null $approved_at Thời gian được approve
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShowcaseComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShowcaseFollow> $follows
 * @property-read int|null $follows_count
 * @property-read string|null $featured_image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShowcaseLike> $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read Model|\Eloquent $showcaseable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereAllowDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereComplexityLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereDownloadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereFeaturedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereFileAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereHasCadFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereHasCalculations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereHasTutorial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereImageGallery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereIndustryApplication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereLearningObjectives($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereManufacturingProcess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereMaterials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereProjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereRatingAverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereRatingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereShareCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereShowcaseableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereShowcaseableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereSoftwareUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereTechnicalQualityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereTechnicalSpecs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Showcase whereViewCount($value)
 * @mixin \Eloquent
 */
class Showcase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'showcaseable_id',
        'showcaseable_type',
        'title',
        'slug',
        'description',
        'location',
        'usage',
        'floors',
        'cover_image',
        'status',
        'category',
        'order',
    ];

    /**
     * Get the user that owns the showcase item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent showcaseable model.
     */
    public function showcaseable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Lấy tất cả comments của showcase này.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ShowcaseComment::class);
    }

    /**
     * Lấy tất cả media liên quan đến showcase này.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Lấy featured image của showcase.
     */
    public function getFeaturedImageAttribute(): ?string
    {
        $featuredMedia = $this->media()
            ->where('file_name', 'like', '%[Featured]%')
            ->first();

        return $featuredMedia ? $featuredMedia->url : null;
    }

    /**
     * Lấy tất cả likes của showcase này.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseLike::class);
    }

    /**
     * Lấy tất cả follows của user sở hữu showcase này.
     */
    public function follows(): HasMany
    {
        return $this->hasMany(ShowcaseFollow::class, 'following_id', 'user_id');
    }

    /**
     * Kiểm tra user hiện tại đã like showcase này chưa.
     */
    public function isLikedBy($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Kiểm tra user hiện tại đã follow showcase owner này chưa.
     */
    public function isFollowedBy($userId): bool
    {
        return ShowcaseFollow::where('follower_id', $userId)
            ->where('following_id', $this->user_id)
            ->exists();
    }

    /**
     * Đếm số lượng likes.
     */
    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    /**
     * Đếm số lượng người theo dõi showcase owner.
     */
    public function followsCount(): int
    {
        return ShowcaseFollow::where('following_id', $this->user_id)->count();
    }

    /**
     * Đếm số lượng comments.
     */
    public function commentsCount(): int
    {
        return $this->comments()->count();
    }

    /**
     * Get cover image URL
     */
    public function getCoverImageUrl(): string
    {
        if ($this->cover_image) {
            if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
                return $this->cover_image;
            }
            return asset('storage/' . str_replace('public/', '', $this->cover_image));
        }

        return asset('images/placeholder.svg');
    }

    /**
     * Get showcase URL based on slug or ID
     */
    public function getShowcaseUrl(): string
    {
        if ($this->slug) {
            return route('showcase.show', $this->slug);
        }
        return route('showcase.show', $this->id);
    }

    /**
     * Check if showcase is independent (not linked to thread/post)
     */
    public function isIndependent(): bool
    {
        return is_null($this->showcaseable_id) && is_null($this->showcaseable_type);
    }

    /**
     * Get showcase type display name
     */
    public function getTypeDisplayName(): string
    {
        if ($this->isIndependent()) {
            return 'Original Showcase';
        }

        if ($this->showcaseable_type === 'App\\Models\\Thread') {
            return 'Thread Showcase';
        }

        if ($this->showcaseable_type === 'App\\Models\\Post') {
            return 'Post Showcase';
        }

        return 'Showcase';
    }
}
