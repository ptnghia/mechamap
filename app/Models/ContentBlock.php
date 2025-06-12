<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $block_type
 * @property array<array-key, mixed>|null $metadata
 * @property string $content_format
 * @property array<array-key, mixed>|null $tags
 * @property string|null $engineering_domain
 * @property int $created_by
 * @property bool $is_public
 * @property bool $is_approved
 * @property int $reference_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read mixed $domain_display
 * @property-read mixed $type_display
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock byDomain($domain)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereBlockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereContentFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereEngineeringDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereReferenceCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentBlock whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContentBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'block_type',
        'metadata',
        'content_format',
        'tags',
        'engineering_domain',
        'created_by',
        'is_public',
        'is_approved',
        'reference_count'
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_approved' => 'boolean',
        'reference_count' => 'integer'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('block_type', $type);
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('engineering_domain', $domain);
    }

    // Mutators
    public function incrementReferenceCount()
    {
        $this->increment('reference_count');
    }

    // Accessors
    public function getTypeDisplayAttribute()
    {
        return match ($this->block_type) {
            'engineering_formula' => 'Công thức Kỹ thuật',
            'material_properties' => 'Thuộc tính Vật liệu',
            'standard_table' => 'Bảng Tiêu chuẩn',
            'calculation_example' => 'Ví dụ Tính toán',
            'cad_snippet' => 'Đoạn CAD',
            'code_block' => 'Khối Code',
            'diagram_embed' => 'Sơ đồ Nhúng',
            'reference_link' => 'Liên kết Tham khảo',
            default => ucfirst(str_replace('_', ' ', $this->block_type))
        };
    }

    public function getDomainDisplayAttribute()
    {
        return match ($this->engineering_domain) {
            'mechanical_design' => 'Thiết kế Cơ khí',
            'manufacturing' => 'Sản xuất',
            'materials' => 'Vật liệu',
            'thermodynamics' => 'Nhiệt động học',
            'fluid_mechanics' => 'Cơ học Chất lỏng',
            'controls' => 'Điều khiển',
            'fea_analysis' => 'Phân tích FEA',
            'cad_cam' => 'CAD/CAM',
            default => ucfirst(str_replace('_', ' ', $this->engineering_domain ?? ''))
        };
    }
}
