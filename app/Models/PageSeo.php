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
        'title_i18n',
        'description_i18n',
        'keywords_i18n',
        'focus_keyword',
        'focus_keyword_i18n',
        'og_title',
        'og_description',
        'og_image',
        'og_title_i18n',
        'og_description_i18n',
        'og_type',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_title_i18n',
        'twitter_description_i18n',
        'twitter_card_type',
        'canonical_url',
        'no_index',
        'extra_meta',
        'structured_data',
        'article_type',
        'breadcrumb_title',
        'breadcrumb_title_i18n',
        'meta_author',
        'priority',
        'sitemap_include',
        'sitemap_priority',
        'sitemap_changefreq',
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
        'sitemap_include' => 'boolean',
        'title_i18n' => 'array',
        'description_i18n' => 'array',
        'keywords_i18n' => 'array',
        'focus_keyword_i18n' => 'array',
        'og_title_i18n' => 'array',
        'og_description_i18n' => 'array',
        'twitter_title_i18n' => 'array',
        'twitter_description_i18n' => 'array',
        'structured_data' => 'array',
        'breadcrumb_title_i18n' => 'array',
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

    /**
     * Get localized title
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocalizedTitle(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        // Try to get from i18n JSON first
        if ($this->title_i18n && isset($this->title_i18n[$locale])) {
            return $this->title_i18n[$locale];
        }

        // Fallback to default title
        return $this->title;
    }

    /**
     * Get localized description
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocalizedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if ($this->description_i18n && isset($this->description_i18n[$locale])) {
            return $this->description_i18n[$locale];
        }

        return $this->description;
    }

    /**
     * Get localized keywords
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocalizedKeywords(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if ($this->keywords_i18n && isset($this->keywords_i18n[$locale])) {
            return $this->keywords_i18n[$locale];
        }

        return $this->keywords;
    }

    /**
     * Get localized OG title
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocalizedOgTitle(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if ($this->og_title_i18n && isset($this->og_title_i18n[$locale])) {
            return $this->og_title_i18n[$locale];
        }

        return $this->og_title ?: $this->getLocalizedTitle($locale);
    }

    /**
     * Get localized OG description
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocalizedOgDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if ($this->og_description_i18n && isset($this->og_description_i18n[$locale])) {
            return $this->og_description_i18n[$locale];
        }

        return $this->og_description ?: $this->getLocalizedDescription($locale);
    }

    /**
     * Set multilingual data
     *
     * @param array $data
     * @return void
     */
    public function setMultilingualData(array $data): void
    {
        $multilingualFields = [
            'title' => 'title_i18n',
            'description' => 'description_i18n',
            'keywords' => 'keywords_i18n',
            'og_title' => 'og_title_i18n',
            'og_description' => 'og_description_i18n',
            'twitter_title' => 'twitter_title_i18n',
            'twitter_description' => 'twitter_description_i18n',
        ];

        foreach ($multilingualFields as $field => $i18nField) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $this->{$i18nField} = $data[$field];
            }
        }
    }

    /**
     * Get all localized data for current locale
     *
     * @param string|null $locale
     * @return array
     */
    public function getLocalizedData(?string $locale = null): array
    {
        return [
            'title' => $this->getLocalizedTitle($locale),
            'description' => $this->getLocalizedDescription($locale),
            'keywords' => $this->getLocalizedKeywords($locale),
            'og_title' => $this->getLocalizedOgTitle($locale),
            'og_description' => $this->getLocalizedOgDescription($locale),
        ];
    }
}
