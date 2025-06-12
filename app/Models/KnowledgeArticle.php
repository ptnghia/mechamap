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
 * @property string|null $excerpt
 * @property string $content
 * @property string $article_type
 * @property string|null $engineering_field
 * @property array<array-key, mixed>|null $prerequisites
 * @property array<array-key, mixed>|null $learning_outcomes
 * @property string $difficulty_level
 * @property int|null $estimated_read_time
 * @property string|null $featured_image
 * @property array<array-key, mixed>|null $technical_specs
 * @property array<array-key, mixed>|null $software_requirements
 * @property int $author_id
 * @property int|null $reviewer_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property int $view_count
 * @property numeric $rating_average
 * @property int $rating_count
 * @property bool $is_featured
 * @property bool $requires_pe_license
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read mixed $field_display
 * @property-read mixed $read_time_display
 * @property-read mixed $status_display
 * @property-read mixed $type_display
 * @property-read \App\Models\User|null $reviewer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContentRevision> $revisions
 * @property-read int|null $revisions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle byDifficulty($level)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle byField($field)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle peRequired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereArticleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereDifficultyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereEngineeringField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereEstimatedReadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereFeaturedImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereLearningOutcomes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle wherePrerequisites($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereRatingAverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereRatingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereRequiresPeLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereSoftwareRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereTechnicalSpecs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereViewCount($value)
 * @mixin \Eloquent
 */
class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'article_type',
        'engineering_field',
        'prerequisites',
        'learning_outcomes',
        'difficulty_level',
        'estimated_read_time',
        'featured_image',
        'technical_specs',
        'software_requirements',
        'author_id',
        'reviewer_id',
        'status',
        'published_at',
        'reviewed_at',
        'view_count',
        'rating_average',
        'rating_count',
        'is_featured',
        'requires_pe_license'
    ];

    protected $casts = [
        'prerequisites' => 'array',
        'learning_outcomes' => 'array',
        'technical_specs' => 'array',
        'software_requirements' => 'array',
        'published_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'rating_average' => 'decimal:2',
        'is_featured' => 'boolean',
        'requires_pe_license' => 'boolean'
    ];

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function revisions(): MorphMany
    {
        return $this->morphMany(ContentRevision::class, 'revisionable');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('article_type', $type);
    }

    public function scopeByField($query, $field)
    {
        return $query->where('engineering_field', $field);
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopePeRequired($query)
    {
        return $query->where('requires_pe_license', true);
    }

    // Mutators
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function addRating($rating)
    {
        $newCount = $this->rating_count + 1;
        $newAverage = (($this->rating_average * $this->rating_count) + $rating) / $newCount;

        $this->update([
            'rating_count' => $newCount,
            'rating_average' => round($newAverage, 2)
        ]);
    }

    // Accessors
    public function getTypeDisplayAttribute()
    {
        return match ($this->article_type) {
            'tutorial' => 'Hướng dẫn',
            'best_practice' => 'Thực hành Tốt nhất',
            'case_study' => 'Nghiên cứu Tình huống',
            'troubleshooting' => 'Khắc phục Sự cố',
            'standard_procedure' => 'Quy trình Chuẩn',
            'design_guide' => 'Hướng dẫn Thiết kế',
            'calculation_method' => 'Phương pháp Tính toán',
            'software_guide' => 'Hướng dẫn Phần mềm',
            default => ucfirst(str_replace('_', ' ', $this->article_type))
        };
    }

    public function getFieldDisplayAttribute()
    {
        return match ($this->engineering_field) {
            'mechanical_design' => 'Thiết kế Cơ khí',
            'manufacturing_engineering' => 'Kỹ thuật Sản xuất',
            'materials_engineering' => 'Kỹ thuật Vật liệu',
            'automotive_engineering' => 'Kỹ thuật Ô tô',
            'aerospace_engineering' => 'Kỹ thuật Hàng không',
            'industrial_engineering' => 'Kỹ thuật Công nghiệp',
            'quality_engineering' => 'Kỹ thuật Chất lượng',
            'maintenance_engineering' => 'Kỹ thuật Bảo trì',
            default => ucfirst(str_replace('_', ' ', $this->engineering_field ?? ''))
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            'draft' => 'Bản nháp',
            'review' => 'Đang duyệt',
            'published' => 'Đã xuất bản',
            'archived' => 'Lưu trữ',
            default => $this->status
        };
    }

    public function getReadTimeDisplayAttribute()
    {
        if (!$this->estimated_read_time) return null;
        return $this->estimated_read_time . ' phút';
    }
}
