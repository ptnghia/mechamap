<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon_url
 * @property string|null $color_code
 * @property int|null $parent_id
 * @property int $sort_order
 * @property string $category_type
 * @property array<array-key, mixed>|null $metadata
 * @property bool $is_active
 * @property bool $show_in_menu
 * @property int $content_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ContentCategory> $children
 * @property-read int|null $children_count
 * @property-read mixed $type_display
 * @property-read ContentCategory|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory inMenu()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory rootCategories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereContentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereShowInMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_url',
        'color_code',
        'parent_id',
        'sort_order',
        'category_type',
        'metadata',
        'is_active',
        'show_in_menu',
        'content_count'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'content_count' => 'integer'
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ContentCategory::class, 'parent_id')->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('category_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Accessors
    public function getTypeDisplayAttribute()
    {
        return match ($this->category_type) {
            'engineering_discipline' => 'Ngành Kỹ thuật',
            'content_type' => 'Loại Nội dung',
            'skill_level' => 'Trình độ Kỹ năng',
            'industry_sector' => 'Lĩnh vực Công nghiệp',
            'software_category' => 'Danh mục Phần mềm',
            default => ucfirst(str_replace('_', ' ', $this->category_type))
        };
    }

    // Methods
    public function incrementContentCount()
    {
        $this->increment('content_count');

        // Also increment parent category if exists
        if ($this->parent) {
            $this->parent->incrementContentCount();
        }
    }

    public function decrementContentCount()
    {
        if ($this->content_count > 0) {
            $this->decrement('content_count');

            // Also decrement parent category if exists
            if ($this->parent && $this->parent->content_count > 0) {
                $this->parent->decrementContentCount();
            }
        }
    }

    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getDepthLevel()
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }
}
