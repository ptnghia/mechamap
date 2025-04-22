<?php

namespace App\Http\Middleware;

use App\Models\PageSeo;
use App\Models\SeoSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApplySeoSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get general SEO settings
        $generalSettings = SeoSetting::getGroup('general');
        $socialSettings = SeoSetting::getGroup('social');
        $advancedSettings = SeoSetting::getGroup('advanced');

        // Default SEO settings
        $seoSettings = [
            'site_title' => $generalSettings['site_title'] ?? config('app.name'),
            'site_description' => $generalSettings['site_description'] ?? '',
            'site_keywords' => $generalSettings['site_keywords'] ?? '',
            'allow_indexing' => ($generalSettings['allow_indexing'] ?? '1') === '1',
            'google_analytics_id' => $generalSettings['google_analytics_id'] ?? '',
            'google_search_console_id' => $generalSettings['google_search_console_id'] ?? '',
            'facebook_app_id' => $generalSettings['facebook_app_id'] ?? '',
            'twitter_username' => $generalSettings['twitter_username'] ?? '',

            'og_title' => $socialSettings['og_title'] ?? ($generalSettings['site_title'] ?? config('app.name')),
            'og_description' => $socialSettings['og_description'] ?? ($generalSettings['site_description'] ?? ''),
            'og_image' => $socialSettings['og_image'] ?? '',
            'twitter_card' => $socialSettings['twitter_card'] ?? 'summary',
            'twitter_title' => $socialSettings['twitter_title'] ?? ($generalSettings['site_title'] ?? config('app.name')),
            'twitter_description' => $socialSettings['twitter_description'] ?? ($generalSettings['site_description'] ?? ''),
            'twitter_image' => $socialSettings['twitter_image'] ?? '',

            'header_scripts' => $advancedSettings['header_scripts'] ?? '',
            'footer_scripts' => $advancedSettings['footer_scripts'] ?? '',
            'custom_css' => $advancedSettings['custom_css'] ?? '',
            'canonical_url' => $advancedSettings['canonical_url'] ?? '',
            'extra_meta' => '',
        ];

        // Check for page-specific SEO settings
        $pageSeo = null;

        // Try to find by route name
        $routeName = Route::currentRouteName();
        if ($routeName) {
            $pageSeo = PageSeo::findByRoute($routeName);
        }

        // If not found by route name, try to find by URL pattern
        if (!$pageSeo) {
            $currentUrl = $request->path();
            $pageSeo = PageSeo::findByUrl($currentUrl);
        }

        // If page-specific SEO settings found, override default settings
        if ($pageSeo) {
            // Override basic SEO settings
            if ($pageSeo->title) {
                $seoSettings['site_title'] = $pageSeo->title;
            }

            if ($pageSeo->description) {
                $seoSettings['site_description'] = $pageSeo->description;
            }

            if ($pageSeo->keywords) {
                $seoSettings['site_keywords'] = $pageSeo->keywords;
            }

            if ($pageSeo->no_index) {
                $seoSettings['allow_indexing'] = false;
            }

            if ($pageSeo->canonical_url) {
                $seoSettings['canonical_url'] = $pageSeo->canonical_url;
            }

            // Override Open Graph settings
            if ($pageSeo->og_title) {
                $seoSettings['og_title'] = $pageSeo->og_title;
            } elseif ($pageSeo->title) {
                $seoSettings['og_title'] = $pageSeo->title;
            }

            if ($pageSeo->og_description) {
                $seoSettings['og_description'] = $pageSeo->og_description;
            } elseif ($pageSeo->description) {
                $seoSettings['og_description'] = $pageSeo->description;
            }

            if ($pageSeo->og_image) {
                $seoSettings['og_image'] = $pageSeo->og_image;
            }

            // Override Twitter Card settings
            if ($pageSeo->twitter_title) {
                $seoSettings['twitter_title'] = $pageSeo->twitter_title;
            } elseif ($pageSeo->title) {
                $seoSettings['twitter_title'] = $pageSeo->title;
            }

            if ($pageSeo->twitter_description) {
                $seoSettings['twitter_description'] = $pageSeo->twitter_description;
            } elseif ($pageSeo->description) {
                $seoSettings['twitter_description'] = $pageSeo->description;
            }

            if ($pageSeo->twitter_image) {
                $seoSettings['twitter_image'] = $pageSeo->twitter_image;
            }

            // Add extra meta tags
            if ($pageSeo->extra_meta) {
                $seoSettings['extra_meta'] = $pageSeo->extra_meta;
            }
        }

        // Share settings with all views
        View::share('seo', $seoSettings);

        return $next($request);
    }
}
