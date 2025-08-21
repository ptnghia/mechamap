<?php

namespace App\Services;

use App\Models\PageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Multilingual SEO Service
 *
 * Handles SEO data with multilingual support
 */
class MultilingualSeoService
{
    /**
     * Get SEO data for current page with localization
     *
     * @param Request $request
     * @param string|null $locale
     * @return array
     */
    public function getSeoData(Request $request, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $routeName = Route::currentRouteName();
        $currentUrl = $request->path();

        // Get page SEO data
        $pageSeo = $this->getPageSeoData($routeName, $currentUrl);

        if (!$pageSeo) {
            return $this->getDefaultSeoData($locale);
        }

        // Get localized data
        $seoData = [
            'title' => $this->processTitle($pageSeo->getLocalizedTitle($locale), $request),
            'description' => $this->processDescription($pageSeo->getLocalizedDescription($locale), $request),
            'keywords' => $pageSeo->getLocalizedKeywords($locale),
            'og_title' => $this->processTitle($pageSeo->getLocalizedOgTitle($locale), $request),
            'og_description' => $this->processDescription($pageSeo->getLocalizedOgDescription($locale), $request),
            'og_image' => $pageSeo->og_image,
            'twitter_title' => $this->processTitle($this->getLocalizedTwitterTitle($pageSeo, $locale), $request),
            'twitter_description' => $this->processDescription($this->getLocalizedTwitterDescription($pageSeo, $locale), $request),
            'twitter_image' => $pageSeo->twitter_image,
            'canonical_url' => $this->getCanonicalUrl($pageSeo->canonical_url, $request),
            'no_index' => $pageSeo->no_index,
            'extra_meta' => $pageSeo->extra_meta,
        ];

        return array_filter($seoData, fn($value) => !is_null($value));
    }

    /**
     * Get page SEO data
     *
     * @param string|null $routeName
     * @param string $currentUrl
     * @return PageSeo|null
     */
    private function getPageSeoData(?string $routeName, string $currentUrl): ?PageSeo
    {
        if ($routeName) {
            $pageSeo = PageSeo::findByRoute($routeName);
            if ($pageSeo) {
                return $pageSeo;
            }
        }

        return PageSeo::findByUrl($currentUrl);
    }

    /**
     * Process title with dynamic placeholders
     *
     * @param string|null $title
     * @param Request $request
     * @return string|null
     */
    private function processTitle(?string $title, Request $request): ?string
    {
        if (!$title) {
            return null;
        }

        return $this->replaceDynamicPlaceholders($title, $request);
    }

    /**
     * Process description with dynamic placeholders
     *
     * @param string|null $description
     * @param Request $request
     * @return string|null
     */
    private function processDescription(?string $description, Request $request): ?string
    {
        if (!$description) {
            return null;
        }

        return $this->replaceDynamicPlaceholders($description, $request);
    }

    /**
     * Replace dynamic placeholders in text
     *
     * @param string $text
     * @param Request $request
     * @return string
     */
    private function replaceDynamicPlaceholders(string $text, Request $request): string
    {
        // Get route parameters
        $routeParams = $request->route() ? $request->route()->parameters() : [];

        // Special handling for marketplace products - find product by slug
        if ($request->route() && $request->route()->getName() === 'marketplace.products.show') {
            $slug = $routeParams['slug'] ?? null;
            if ($slug) {
                try {
                    $product = \App\Models\MarketplaceProduct::where('slug', $slug)
                        ->where('status', 'approved')
                        ->where('is_active', true)
                        ->first();
                    if ($product) {
                        $routeParams['product'] = $product;
                    }
                } catch (\Exception $e) {
                    // Ignore errors and continue without product
                }
            }
        }



        // Replace common placeholders
        foreach ($routeParams as $key => $value) {


            if (is_object($value)) {
                // Special handling for specific models FIRST (before generic patterns)
                if ($key === 'forum' && isset($value->name)) {
                    $text = str_replace('{forum_name}', $value->name, $text);
                }
                if ($key === 'thread' && isset($value->title)) {
                    $text = str_replace('{thread_title}', $value->title, $text);
                }
                if ($key === 'category' && isset($value->name)) {
                    $text = str_replace('{category_name}', $value->name, $text);
                }
                if ($key === 'product' && isset($value->name)) {
                    $text = str_replace('{product_name}', $value->name, $text);
                }

                // Handle model objects (generic patterns)
                if (isset($value->name)) {
                    $text = str_replace('{' . $key . '_name}', $value->name, $text);
                    $text = str_replace('{' . $key . '}', $value->name, $text);
                }
                if (isset($value->title)) {
                    $text = str_replace('{' . $key . '_title}', $value->title, $text);
                    if (!isset($value->name)) {
                        $text = str_replace('{' . $key . '}', $value->title, $text);
                    }
                }
                if (isset($value->username)) {
                    $text = str_replace('{user_name}', $value->username, $text);
                }
                if (isset($value->description)) {
                    $text = str_replace('{' . $key . '_description}', $value->description, $text);
                }
            } else {
                // Handle string parameters
                $text = str_replace('{' . $key . '}', $value, $text);
            }
        }

        return $text;
    }

    /**
     * Get canonical URL
     *
     * @param string|null $canonicalPattern
     * @param Request $request
     * @return string|null
     */
    private function getCanonicalUrl(?string $canonicalPattern, Request $request): ?string
    {
        if (!$canonicalPattern) {
            return $request->url();
        }

        // Process dynamic placeholders in canonical URL
        $canonicalUrl = $this->replaceDynamicPlaceholders($canonicalPattern, $request);

        // Ensure it's a full URL
        if (!str_starts_with($canonicalUrl, 'http')) {
            $canonicalUrl = url($canonicalUrl);
        }

        return $canonicalUrl;
    }

    /**
     * Get default SEO data when no specific data found
     *
     * @param string $locale
     * @return array
     */
    private function getDefaultSeoData(string $locale): array
    {
        $defaultTitles = [
            'vi' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
        ];

        $defaultDescriptions = [
            'vi' => 'Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam.',
            'en' => 'Leading forum platform for Vietnam\'s mechanical engineering community.'
        ];

        return [
            'title' => $defaultTitles[$locale] ?? $defaultTitles['vi'],
            'description' => $defaultDescriptions[$locale] ?? $defaultDescriptions['vi'],
            'keywords' => 'mechanical engineering, vietnam, forum, community',
            'canonical_url' => url()->current(),
        ];
    }

    /**
     * Create or update multilingual SEO data
     *
     * @param string $routeName
     * @param array $data
     * @return PageSeo
     */
    public function createOrUpdateSeoData(string $routeName, array $data): PageSeo
    {
        $pageSeo = PageSeo::where('route_name', $routeName)->first();

        if ($pageSeo) {
            $pageSeo->update($data);
        } else {
            $data['route_name'] = $routeName;
            $pageSeo = PageSeo::create($data);
        }

        return $pageSeo;
    }

    /**
     * Get available locales for SEO data
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        return ['vi', 'en'];
    }

    /**
     * Validate multilingual SEO data
     *
     * @param array $data
     * @return array
     */
    public function validateMultilingualData(array $data): array
    {
        $errors = [];
        $requiredFields = ['title_i18n', 'description_i18n'];
        $availableLocales = $this->getAvailableLocales();

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || !is_array($data[$field])) {
                $errors[] = "Field {$field} must be an array";
                continue;
            }

            foreach ($availableLocales as $locale) {
                if (empty($data[$field][$locale])) {
                    $errors[] = "Field {$field} is required for locale {$locale}";
                }
            }
        }

        return $errors;
    }

    /**
     * Export SEO data for translation
     *
     * @param string|null $routeName
     * @return array
     */
    public function exportForTranslation(?string $routeName = null): array
    {
        $query = PageSeo::where('is_active', true);

        if ($routeName) {
            $query->where('route_name', $routeName);
        }

        $pages = $query->get();
        $exportData = [];

        foreach ($pages as $page) {
            $exportData[$page->route_name] = [
                'title' => [
                    'vi' => $page->title_i18n['vi'] ?? $page->title,
                    'en' => $page->title_i18n['en'] ?? '',
                ],
                'description' => [
                    'vi' => $page->description_i18n['vi'] ?? $page->description,
                    'en' => $page->description_i18n['en'] ?? '',
                ],
                'keywords' => [
                    'vi' => $page->keywords_i18n['vi'] ?? $page->keywords,
                    'en' => $page->keywords_i18n['en'] ?? '',
                ],
            ];
        }

        return $exportData;
    }

    /**
     * Get localized Twitter title
     *
     * @param PageSeo $pageSeo
     * @param string $locale
     * @return string|null
     */
    private function getLocalizedTwitterTitle(PageSeo $pageSeo, string $locale): ?string
    {
        if ($pageSeo->twitter_title_i18n && isset($pageSeo->twitter_title_i18n[$locale])) {
            return $pageSeo->twitter_title_i18n[$locale];
        }

        return $pageSeo->twitter_title ?: $pageSeo->getLocalizedTitle($locale);
    }

    /**
     * Get localized Twitter description
     *
     * @param PageSeo $pageSeo
     * @param string $locale
     * @return string|null
     */
    private function getLocalizedTwitterDescription(PageSeo $pageSeo, string $locale): ?string
    {
        if ($pageSeo->twitter_description_i18n && isset($pageSeo->twitter_description_i18n[$locale])) {
            return $pageSeo->twitter_description_i18n[$locale];
        }

        return $pageSeo->twitter_description ?: $pageSeo->getLocalizedDescription($locale);
    }
}
