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
     * @return string
     */
    function seo_title(?string $locale = null): string
    {
        $seoService = app(\App\Services\MultilingualSeoService::class);
        $seoData = $seoService->getSeoData(request(), $locale);

        return $seoData['title'] ?? 'MechaMap';
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
        $seoService = app(\App\Services\MultilingualSeoService::class);
        $seoData = $seoService->getSeoData(request(), $locale);

        return $seoData['description'] ?? '';
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
