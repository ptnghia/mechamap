<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
