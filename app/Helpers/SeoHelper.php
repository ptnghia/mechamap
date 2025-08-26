<?php

if (!function_exists('seo_meta')) {
    /**
     * Generate SEO meta tags for current page
     *
     * @param string|null $locale
     * @return string
     */
    function seo_meta(?string $locale = null): string
    {
        $seoService = app(\App\Services\MultilingualSeoService::class);
        $seoData = $seoService->getSeoData(request(), $locale);

        $meta = [];

        // Basic meta tags
        if (!empty($seoData['title'])) {
            $meta[] = '<title>' . e($seoData['title']) . '</title>';
        }

        if (!empty($seoData['description'])) {
            $meta[] = '<meta name="description" content="' . e($seoData['description']) . '">';
        }

        if (!empty($seoData['keywords'])) {
            $meta[] = '<meta name="keywords" content="' . e($seoData['keywords']) . '">';
        }

        // Canonical URL
        if (!empty($seoData['canonical_url'])) {
            $meta[] = '<link rel="canonical" href="' . e($seoData['canonical_url']) . '">';
        }

        // Robots meta
        if (!empty($seoData['no_index'])) {
            $meta[] = '<meta name="robots" content="noindex, nofollow">';
        } else {
            $meta[] = '<meta name="robots" content="index, follow">';
        }

        // Open Graph tags
        if (!empty($seoData['og_title'])) {
            $meta[] = '<meta property="og:title" content="' . e($seoData['og_title']) . '">';
        }

        if (!empty($seoData['og_description'])) {
            $meta[] = '<meta property="og:description" content="' . e($seoData['og_description']) . '">';
        }

        if (!empty($seoData['og_image'])) {
            $meta[] = '<meta property="og:image" content="' . e($seoData['og_image']) . '">';
        }

        $meta[] = '<meta property="og:type" content="website">';
        $meta[] = '<meta property="og:url" content="' . e(request()->url()) . '">';

        // Twitter Card tags
        $meta[] = '<meta name="twitter:card" content="summary_large_image">';

        if (!empty($seoData['twitter_title'])) {
            $meta[] = '<meta name="twitter:title" content="' . e($seoData['twitter_title']) . '">';
        }

        if (!empty($seoData['twitter_description'])) {
            $meta[] = '<meta name="twitter:description" content="' . e($seoData['twitter_description']) . '">';
        }

        if (!empty($seoData['twitter_image'])) {
            $meta[] = '<meta name="twitter:image" content="' . e($seoData['twitter_image']) . '">';
        }

        // Extra meta tags
        if (!empty($seoData['extra_meta'])) {
            $meta[] = $seoData['extra_meta'];
        }

        return implode("\n    ", $meta);
    }
}

if (!function_exists('seo_title')) {
    /**
     * Get SEO title for current page
     *
     * @param string|null $locale
     * @param bool $shortTitle Nếu true, chỉ lấy phần đầu tiên trước ký tự "|"
     * @return string
     */
    function seo_title(?string $locale = null, bool $shortTitle = false): string
    {
        try {
            $seoService = app(\App\Services\MultilingualSeoService::class);
            $seoData = $seoService->getSeoData(request(), $locale);

            $title = $seoData['title'] ?? seo_default_title($locale);

            if ($shortTitle) {
                $titleParts = explode('|', $title);
                return trim($titleParts[0]) ?: seo_default_short_title($locale);
            }

            return $title;
        } catch (\Exception $e) {
            \Log::warning('SEO title helper failed: ' . $e->getMessage());
            return $shortTitle ? seo_default_short_title($locale) : seo_default_title($locale);
        }
    }
}

if (!function_exists('seo_title_short')) {
    /**
     * Get short SEO title (phần đầu tiên trước ký tự "|")
     *
     * @param string|null $text Custom fallback text khi không có dữ liệu
     * @param string|null $locale
     * @return string
     */
    function seo_title_short(?string $text = null, ?string $locale = null): string
    {
        try {
            $seoService = app(\App\Services\MultilingualSeoService::class);
            $seoData = $seoService->getSeoData(request(), $locale);

            $title = $seoData['title'] ?? null;

            // Nếu có dữ liệu từ database, xử lý title ngắn
            if ($title) {
                $titleParts = explode('-', $title);
                $shortTitle = trim($titleParts[0]);
                if ($shortTitle) {
                    return $shortTitle;
                }
            }

            // Fallback: sử dụng $text nếu có, nếu không thì dùng default
            return $text ?: seo_default_short_title($locale);

        } catch (\Exception $e) {
            \Log::warning('SEO title short helper failed: ' . $e->getMessage());
            return $text ?: seo_default_short_title($locale);
        }
    }
}

if (!function_exists('seo_description')) {
    /**
     * Get SEO description for current page
     *
     * @param string|null $locale
     * @return string
     */
    function seo_description(?string $locale = null): string
    {
        try {
            $seoService = app(\App\Services\MultilingualSeoService::class);
            $seoData = $seoService->getSeoData(request(), $locale);

            return $seoData['description'] ?? seo_default_description($locale);
        } catch (\Exception $e) {
            \Log::warning('SEO description helper failed: ' . $e->getMessage());
            return seo_default_description($locale);
        }
    }
}

if (!function_exists('breadcrumb_title')) {
    /**
     * Get breadcrumb title from SEO data
     *
     * @param string|null $locale
     * @return string
     */
    function breadcrumb_title(?string $locale = null): string
    {
        $title = seo_title($locale);

        // Remove site name from title for breadcrumb
        $title = preg_replace('/\s*\|\s*MechaMap.*$/i', '', $title);
        $title = preg_replace('/\s*-\s*MechaMap.*$/i', '', $title);

        return trim($title) ?: $title;
    }
}

if (!function_exists('hreflang_tags')) {
    /**
     * Generate hreflang tags for multilingual SEO
     *
     * @return string
     */
    function hreflang_tags(): string
    {
        $currentUrl = request()->url();
        $availableLocales = ['vi', 'en'];
        $currentLocale = app()->getLocale();

        $tags = [];

        foreach ($availableLocales as $locale) {
            if ($locale === $currentLocale) {
                // Current page
                $tags[] = '<link rel="alternate" hreflang="' . $locale . '" href="' . $currentUrl . '">';
            } else {
                // Generate URL for other locale
                $alternateUrl = $currentUrl; // In a real implementation, you'd generate the proper URL for the other locale
                $tags[] = '<link rel="alternate" hreflang="' . $locale . '" href="' . $alternateUrl . '">';
            }
        }

        // Add x-default
        $tags[] = '<link rel="alternate" hreflang="x-default" href="' . $currentUrl . '">';

        return implode("\n    ", $tags);
    }
}

if (!function_exists('structured_data')) {
    /**
     * Generate structured data (JSON-LD) for SEO
     *
     * @param array $customData
     * @return string
     */
    function structured_data(array $customData = []): string
    {
        $seoService = app(\App\Services\MultilingualSeoService::class);
        $seoData = $seoService->getSeoData(request());

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'MechaMap',
            'description' => $seoData['description'] ?? '',
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ]
        ];

        // Merge with custom data
        $structuredData = array_merge($structuredData, $customData);

        return '<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}

if (!function_exists('page_seo_data')) {
    /**
     * Get all SEO data for current page
     *
     * @param string|null $locale
     * @return array
     */
    function page_seo_data(?string $locale = null): array
    {
        $seoService = app(\App\Services\MultilingualSeoService::class);
        return $seoService->getSeoData(request(), $locale);
    }
}

if (!function_exists('get_seo_data')) {
    /**
     * Get SEO data - alias for page_seo_data for easier access
     *
     * @param string|null $locale
     * @return array
     */
    function get_seo_data(?string $locale = null): array
    {
        return page_seo_data($locale);
    }
}

if (!function_exists('seo_value')) {
    /**
     * Get specific SEO value by key
     *
     * @param string $key
     * @param mixed $default
     * @param string|null $locale
     * @return mixed
     */
    function seo_value(string $key, $default = null, ?string $locale = null)
    {
        try {
            $seoData = page_seo_data($locale);
            return $seoData[$key] ?? $default;
        } catch (\Exception $e) {
            \Log::warning('SEO value helper failed: ' . $e->getMessage());
            return $default;
        }
    }
}

// ========================================
// DEFAULT VALUES HELPERS
// ========================================

if (!function_exists('seo_default_title')) {
    /**
     * Get default title based on locale
     *
     * @param string|null $locale
     * @return string
     */
    function seo_default_title(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $defaultTitles = [
            'vi' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
        ];

        return $defaultTitles[$locale] ?? $defaultTitles['vi'];
    }
}

if (!function_exists('seo_default_short_title')) {
    /**
     * Get default short title (without site name)
     *
     * @param string|null $locale
     * @return string
     */
    function seo_default_short_title(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $defaultShortTitles = [
            'vi' => 'MechaMap',
            'en' => 'MechaMap'
        ];

        return $defaultShortTitles[$locale] ?? $defaultShortTitles['vi'];
    }
}

if (!function_exists('seo_default_description')) {
    /**
     * Get default description based on locale
     *
     * @param string|null $locale
     * @return string
     */
    function seo_default_description(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $defaultDescriptions = [
            'vi' => 'Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam. Thảo luận CAD/CAM, thiết kế máy móc, công nghệ chế tạo.',
            'en' => 'Leading forum platform for Vietnam\'s mechanical engineering community. Discuss CAD/CAM, machine design, manufacturing technology.'
        ];

        return $defaultDescriptions[$locale] ?? $defaultDescriptions['vi'];
    }
}

if (!function_exists('seo_default_keywords')) {
    /**
     * Get default keywords based on locale
     *
     * @param string|null $locale
     * @return string
     */
    function seo_default_keywords(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $defaultKeywords = [
            'vi' => 'cơ khí, kỹ thuật, CAD, CAM, thiết kế máy móc, forum, cộng đồng, việt nam',
            'en' => 'mechanical engineering, CAD, CAM, machine design, forum, community, vietnam'
        ];

        return $defaultKeywords[$locale] ?? $defaultKeywords['vi'];
    }
}
