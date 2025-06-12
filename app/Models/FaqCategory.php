<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon_url
 * @property string $engineering_domain
 * @property int $faq_count
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Faq> $faqs
 * @property-read int|null $faqs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereEngineeringDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereFaqCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaqCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FaqCategory extends Model
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
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the FAQs in this category.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
