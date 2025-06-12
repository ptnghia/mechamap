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
 * @property string|null $color_code
 * @property string $category_type
 * @property int $is_active
 * @property int $show_in_menu
 * @property int $page_count
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory wherePageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereShowInMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PageCategory extends Model
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
    ];

    /**
     * Get the pages in this category.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'category_id');
    }
}
