<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $template_content
 * @property string $template_type
 * @property array<array-key, mixed>|null $template_variables
 * @property array<array-key, mixed>|null $required_skills
 * @property string $difficulty_level
 * @property string|null $industry_sector
 * @property int $created_by
 * @property int|null $updated_by
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $usage_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read mixed $difficulty_display
 * @property-read mixed $type_display
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate byDifficulty($level)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereDifficultyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereIndustrySector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereRequiredSkills($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereTemplateContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereTemplateVariables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTemplate whereUsageCount($value)
 * @mixin \Eloquent
 */
class ContentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'template_content',
        'template_type',
        'template_variables',
        'required_skills',
        'difficulty_level',
        'industry_sector',
        'created_by',
        'updated_by',
        'is_active',
        'is_featured',
        'usage_count'
    ];

    protected $casts = [
        'template_variables' => 'array',
        'required_skills' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'usage_count' => 'integer'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    // Mutators
    public function incrementUsageCount()
    {
        $this->increment('usage_count');
    }

    // Accessors
    public function getTypeDisplayAttribute()
    {
        return match ($this->template_type) {
            'calculation_guide' => 'Hướng dẫn Tính toán',
            'cad_tutorial' => 'Tutorial CAD',
            'fea_procedure' => 'Quy trình FEA',
            'manufacturing_process' => 'Quy trình Sản xuất',
            'safety_protocol' => 'Quy định An toàn',
            'design_standard' => 'Tiêu chuẩn Thiết kế',
            'material_spec' => 'Thông số Vật liệu',
            'troubleshooting' => 'Khắc phục Sự cố',
            default => ucfirst(str_replace('_', ' ', $this->template_type))
        };
    }

    public function getDifficultyDisplayAttribute()
    {
        return match ($this->difficulty_level) {
            'beginner' => 'Cơ bản',
            'intermediate' => 'Trung cấp',
            'advanced' => 'Nâng cao',
            'expert' => 'Chuyên gia',
            default => $this->difficulty_level
        };
    }
}
