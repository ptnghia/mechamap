<?php

namespace App\Services;

use App\Models\PageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Dynamic Breadcrumb Service
 *
 * Generates breadcrumbs based on SEO data from page_seos table
 * Falls back to route-based generation when SEO data is not available
 */
class BreadcrumbService
{
    /**
     * Generate dynamic breadcrumb based on current route and SEO data
     *
     * @param Request $request
     * @return array
     */
    public function generate(Request $request): array
    {
        $breadcrumbs = [];
        $routeName = Route::currentRouteName();
        $currentUrl = $request->path();

        // Don't show breadcrumb for home page
        if ($routeName === 'home') {
            return []; // Return empty array for home page
        }

        // Check if we have SEO data for current page
        $pageSeo = $this->getPageSeoData($routeName, $currentUrl);
        if (!$pageSeo) {
            return []; // Return empty array if no SEO data
        }

        // Always start with Home - get from SEO data with localization
        $homeSeo = PageSeo::findByRoute('home');
        if (!$homeSeo) {
            return []; // Return empty array if no home SEO data
        }

        $homeTitle = $this->extractBreadcrumbTitle($homeSeo->getLocalizedTitle(), $request);

        $breadcrumbs[] = [
            'title' => $homeTitle,
            'url' => route('home'),
            'active' => false
        ];

        // Generate breadcrumbs based on route hierarchy and SEO data
        $additionalBreadcrumbs = $this->generateSeoBasedBreadcrumbs($routeName, $request, $pageSeo);
        $breadcrumbs = array_merge($breadcrumbs, $additionalBreadcrumbs);

        // Mark the last item as active
        if (!empty($breadcrumbs)) {
            $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
        }

        return $breadcrumbs;
    }

    /**
     * Get page SEO data for breadcrumb context
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
     * Generate breadcrumbs based on SEO data and route hierarchy
     *
     * @param string|null $routeName
     * @param Request $request
     * @param PageSeo|null $pageSeo
     * @return array
     */
    private function generateSeoBasedBreadcrumbs(?string $routeName, Request $request, ?PageSeo $pageSeo): array
    {
        if (!$routeName) {
            return $this->generateFallbackBreadcrumb($request, $pageSeo);
        }

        $breadcrumbs = [];

        // Handle specific route patterns with hierarchy
        if ($this->isHierarchicalRoute($routeName)) {
            $breadcrumbs = $this->generateHierarchicalBreadcrumbs($routeName, $request);

            // Add current page breadcrumb for hierarchical routes if not already included
            if ($pageSeo && !$this->isCurrentPageInBreadcrumbs($breadcrumbs, $request->url())) {
                $breadcrumbs[] = [
                    'title' => $this->extractBreadcrumbTitle($pageSeo->getLocalizedTitle(), $request),
                    'url' => $request->url(),
                    'active' => false
                ];
            }
        } else {
            // Add current page breadcrumb for non-hierarchical routes
            if ($pageSeo) {
                $breadcrumbs[] = [
                    'title' => $this->extractBreadcrumbTitle($pageSeo->getLocalizedTitle(), $request),
                    'url' => $request->url(),
                    'active' => false
                ];
            } else {
                // Fallback for current page
                $fallbackBreadcrumb = $this->generateFallbackBreadcrumbForRoute($routeName, $request);
                if ($fallbackBreadcrumb) {
                    $breadcrumbs[] = $fallbackBreadcrumb;
                }
            }
        }

        return $breadcrumbs;
    }

    /**
     * Check if current page URL is already in breadcrumbs
     */
    private function isCurrentPageInBreadcrumbs(array $breadcrumbs, string $currentUrl): bool
    {
        foreach ($breadcrumbs as $breadcrumb) {
            if (isset($breadcrumb['url']) && $breadcrumb['url'] === $currentUrl) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if route requires hierarchical breadcrumbs
     */
    private function isHierarchicalRoute(string $routeName): bool
    {
        $hierarchicalPatterns = [
            'forums.show',
            'categories.show',
            'threads.show',
            'threads.create',
            'marketplace.products.show',
            'showcase.show',
            'users.show',
            'profile.show'
        ];

        foreach ($hierarchicalPatterns as $pattern) {
            if (str_starts_with($routeName, str_replace('.show', '', $pattern)) || $routeName === $pattern) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate hierarchical breadcrumbs for complex routes
     */
    private function generateHierarchicalBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        // Forum hierarchy: Forums > Category > Forum > Thread
        if (str_starts_with($routeName, 'forums.') || str_starts_with($routeName, 'threads.') || str_starts_with($routeName, 'categories.')) {
            // Only add forums.index breadcrumb if we're NOT on the forums.index page itself
            if ($routeName !== 'forums.index') {
                $forumsSeo = PageSeo::findByRoute('forums.index');
                if ($forumsSeo) {
                    $breadcrumbs[] = [
                        'title' => $this->extractBreadcrumbTitle($forumsSeo->getLocalizedTitle(), $request),
                        'url' => route('forums.index'),
                        'active' => false
                    ];
                }
            }

            // Add category if available
            $routeParams = $request->route() ? $request->route()->parameters() : [];
            if (isset($routeParams['forum']) && $routeParams['forum']->category) {
                $breadcrumbs[] = [
                    'title' => $routeParams['forum']->category->name,
                    'url' => route('categories.show', $routeParams['forum']->category->slug),
                    'active' => false
                ];
            }

            // Add forum if available
            if (isset($routeParams['forum'])) {
                $breadcrumbs[] = [
                    'title' => $routeParams['forum']->name,
                    'url' => route('forums.show', $routeParams['forum']->slug),
                    'active' => false
                ];
            }
        }

        // Marketplace hierarchy: Marketplace > Products > Product
        elseif (str_starts_with($routeName, 'marketplace.')) {
            $marketplaceSeo = PageSeo::findByRoute('marketplace.index');
            if ($marketplaceSeo) {
                $breadcrumbs[] = [
                    'title' => $this->extractBreadcrumbTitle($marketplaceSeo->getLocalizedTitle(), $request),
                    'url' => route('marketplace.index'),
                    'active' => false
                ];
            }

            if (str_contains($routeName, 'products')) {
                $productsSeo = PageSeo::findByRoute('marketplace.products.index');
                if ($productsSeo) {
                    $breadcrumbs[] = [
                        'title' => $this->extractBreadcrumbTitle($productsSeo->getLocalizedTitle(), $request),
                        'url' => route('marketplace.products.index'),
                        'active' => false
                    ];
                }
            }
        }

        // User hierarchy: Users > User Profile
        elseif (str_starts_with($routeName, 'users.') || str_starts_with($routeName, 'profile.')) {
            $usersSeo = PageSeo::findByRoute('users.index');
            if ($usersSeo) {
                $breadcrumbs[] = [
                    'title' => $this->extractBreadcrumbTitle($usersSeo->getLocalizedTitle(), $request),
                    'url' => route('users.index'),
                    'active' => false
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Extract breadcrumb title from SEO title
     * Removes site name and cleans up the title for breadcrumb display
     */
    private function extractBreadcrumbTitle(string $seoTitle, ?Request $request = null): string
    {
        // Remove common site suffixes
        $title = preg_replace('/\s*\|\s*MechaMap.*$/i', '', $seoTitle);
        $title = preg_replace('/\s*-\s*MechaMap.*$/i', '', $title);

        // Handle dynamic placeholders if request is provided
        if ($request) {
            $title = $this->replaceDynamicPlaceholders($title, $request);
        }

        // Shorten title by removing content after first dash for breadcrumb display
        // Example: "Diễn đàn Kỹ thuật Cơ khí - Thảo luận CAD/CAM" → "Diễn đàn Kỹ thuật Cơ khí"
        $title = preg_replace('/\s*-\s*.*$/', '', $title);

        // Clean up and return
        return trim($title) ?: $seoTitle;
    }

    /**
     * Replace dynamic placeholders in title with actual values
     */
    private function replaceDynamicPlaceholders(string $title, Request $request): string
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
                    $title = str_replace('{forum_name}', $value->name, $title);
                }
                if ($key === 'thread' && isset($value->title)) {
                    $title = str_replace('{thread_title}', $value->title, $title);
                }
                if ($key === 'category' && isset($value->name)) {
                    $title = str_replace('{category_name}', $value->name, $title);
                }
                if ($key === 'product' && isset($value->name)) {
                    $title = str_replace('{product_name}', $value->name, $title);
                }

                // Handle model objects (generic patterns)
                if (isset($value->name)) {
                    $title = str_replace('{' . $key . '_name}', $value->name, $title);
                    $title = str_replace('{' . $key . '}', $value->name, $title);
                }
                if (isset($value->title)) {
                    $title = str_replace('{' . $key . '_title}', $value->title, $title);
                    if (!isset($value->name)) {
                        $title = str_replace('{' . $key . '}', $value->title, $title);
                    }
                }
                if (isset($value->username)) {
                    $title = str_replace('{user_name}', $value->username, $title);
                }
                if ($key === 'category' && method_exists($value, 'name')) {
                    $title = str_replace('{category_name}', $value->name, $title);
                }
            } else {
                // Handle string parameters
                $title = str_replace('{' . $key . '}', $value, $title);
            }
        }

        return $title;
    }

    /**
     * Generate fallback breadcrumb when no SEO data available
     */
    private function generateFallbackBreadcrumb(Request $request, ?PageSeo $pageSeo): array
    {
        if ($pageSeo) {
            return [[
                'title' => $this->extractBreadcrumbTitle($pageSeo->title, $request),
                'url' => $request->url(),
                'active' => false
            ]];
        }

        return [[
            'title' => $this->generateTitleFromUrl($request->path()),
            'url' => $request->url(),
            'active' => false
        ]];
    }

    /**
     * Generate fallback breadcrumb for specific route
     */
    private function generateFallbackBreadcrumbForRoute(string $routeName, Request $request): ?array
    {
        // Map common route patterns to readable names
        $routeMap = [
            'forums' => 'Diễn đàn',
            'forums.index' => 'Diễn đàn',
            'threads' => 'Bài viết',
            'threads.index' => 'Bài viết',
            'users' => 'Thành viên',
            'users.index' => 'Thành viên',
            'marketplace' => 'Marketplace',
            'marketplace.index' => 'Marketplace',
            'showcase' => 'Showcase',
            'showcase.index' => 'Showcase',
            'tools' => 'Công cụ',
            'tools.index' => 'Công cụ',
            'members' => 'Cộng đồng',
            'members.index' => 'Cộng đồng',
        ];

        if (isset($routeMap[$routeName])) {
            try {
                return [
                    'title' => $routeMap[$routeName],
                    'url' => Route::has($routeName) ? route($routeName) : $request->url(),
                    'active' => false
                ];
            } catch (\Exception $e) {
                // Continue to fallback
            }
        }

        return null;
    }

    /**
     * Generate title from URL path
     */
    private function generateTitleFromUrl(string $path): string
    {
        $segments = array_filter(explode('/', $path));
        $lastSegment = end($segments);

        return Str::title(str_replace(['-', '_'], ' ', $lastSegment)) ?: 'Trang';
    }
}
