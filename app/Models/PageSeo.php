<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string|null $route_name
 * @property string|null $url_pattern
 * @property string|null $title
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $twitter_title
 * @property string|null $twitter_description
 * @property string|null $twitter_image
 * @property string|null $canonical_url
 * @property bool $no_index
 * @property string|null $extra_meta
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereCanonicalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereExtraMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereNoIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereOgDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereOgImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereOgTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereRouteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereTwitterDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereTwitterImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereTwitterTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageSeo whereUrlPattern($value)
 * @mixin \Eloquent
 */
class PageSeo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'route_name',
        'url_pattern',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'no_index',
        'extra_meta',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'no_index' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Find SEO settings for a specific route
     *
     * @param string $routeName
     * @return PageSeo|null
     */
    public static function findByRoute(string $routeName)
    {
        return self::where('route_name', $routeName)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Find SEO settings for a specific URL
     *
     * @param string $url
     * @return PageSeo|null
     */
    public static function findByUrl(string $url)
    {
        $pages = self::where('is_active', true)
            ->whereNotNull('url_pattern')
            ->get();

        foreach ($pages as $page) {
            if (preg_match('#' . $page->url_pattern . '#i', $url)) {
                return $page;
            }
        }

        return null;
    }
}
