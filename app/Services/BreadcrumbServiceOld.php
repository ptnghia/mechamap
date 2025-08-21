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

        // Always start with Home - get from SEO data
        $homeSeo = PageSeo::findByRoute('home');
        $breadcrumbs[] = [
            'title' => $homeSeo ? $this->extractBreadcrumbTitle($homeSeo->title) : 'Trang chủ',
            'url' => route('home'),
            'active' => false
        ];

        // Get page SEO data for current page
        $pageSeo = $this->getPageSeoData($routeName, $currentUrl);

        // Generate breadcrumbs based on route hierarchy and SEO data
        $breadcrumbs = array_merge($breadcrumbs, $this->generateSeoBasedBreadcrumbs($routeName, $request, $pageSeo));

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

        // Build breadcrumb hierarchy based on route structure
        $breadcrumbs = [];

        // Parse route to understand hierarchy
        $routeParts = explode('.', $routeName);
        $currentPath = '';

        foreach ($routeParts as $index => $part) {
            $currentPath .= ($index > 0 ? '.' : '') . $part;

            // Skip the last part as it will be handled separately
            if ($index === count($routeParts) - 1) {
                break;
            }

            // Get SEO data for this route level
            $levelSeo = PageSeo::findByRoute($currentPath);
            if ($levelSeo) {
                $breadcrumbs[] = [
                    'title' => $this->extractBreadcrumbTitle($levelSeo->title),
                    'url' => $this->generateUrlFromRoute($currentPath, $request),
                    'active' => false
                ];
            } else {
                // Fallback to route-based breadcrumb
                $fallbackBreadcrumb = $this->generateFallbackBreadcrumbForRoute($currentPath, $request);
                if ($fallbackBreadcrumb) {
                    $breadcrumbs[] = $fallbackBreadcrumb;
                }
            }
        }

        // Add current page breadcrumb
        if ($pageSeo) {
            $breadcrumbs[] = [
                'title' => $this->extractBreadcrumbTitle($pageSeo->title, $request),
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

        return $breadcrumbs;
    }

    /**
     * Extract breadcrumb title from SEO title
     * Removes site name and cleans up the title for breadcrumb display
     */
    private function generateCategoryBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        if ($routeName === 'categories.show') {
            $category = $request->route('category');

            $breadcrumbs[] = [
                'title' => __('ui.navigation.forums'),
                'url' => route('forums.index'),
                'active' => false
            ];

            if ($category) {
                $breadcrumbs[] = [
                    'title' => $category->name,
                    'url' => route('categories.show', $category->slug),
                    'active' => false
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Generate thread-related breadcrumbs
     */
    private function generateThreadBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        switch ($routeName) {
            case 'threads.index':
                $breadcrumbs[] = [
                    'title' => __('breadcrumb.threads'),
                    'url' => route('threads.index'),
                    'active' => false
                ];
                break;

            case 'threads.show':
                $thread = $request->route('thread');

                $breadcrumbs[] = [
                    'title' => __('breadcrumb.forums'),
                    'url' => route('forums.index'),
                    'active' => false
                ];

                if ($thread && $thread->forum) {
                    if ($thread->forum->category) {
                        $breadcrumbs[] = [
                            'title' => $thread->forum->category->name,
                            'url' => route('categories.show', $thread->forum->category->slug),
                            'active' => false
                        ];
                    }

                    $breadcrumbs[] = [
                        'title' => $thread->forum->name,
                        'url' => route('forums.show', $thread->forum->slug),
                        'active' => false
                    ];
                }

                if ($thread) {
                    $breadcrumbs[] = [
                        'title' => Str::limit($thread->title, 50),
                        'url' => route('threads.show', $thread->slug),
                        'active' => false
                    ];
                }
                break;

            case 'threads.create':
                $breadcrumbs[] = [
                    'title' => __('breadcrumb.forums'),
                    'url' => route('forums.index'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'title' => __('breadcrumb.thread.create'),
                    'url' => route('threads.create'),
                    'active' => false
                ];
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Generate marketplace-related breadcrumbs
     */
    private function generateMarketplaceBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'title' => __('ui.navigation.marketplace'),
            'url' => route('marketplace.index'),
            'active' => false
        ];

        switch ($routeName) {
            case 'marketplace.products.index':
                $breadcrumbs[] = [
                    'title' => __('ui.marketplace.products'),
                    'url' => route('marketplace.products.index'),
                    'active' => false
                ];
                break;

            case 'marketplace.products.show':
                $product = $request->route('slug');
                $breadcrumbs[] = [
                    'title' => __('ui.marketplace.products'),
                    'url' => route('marketplace.products.index'),
                    'active' => false
                ];

                if ($product) {
                    $breadcrumbs[] = [
                        'title' => Str::limit($product, 50),
                        'url' => route('marketplace.products.show', $product),
                        'active' => false
                    ];
                }
                break;

            case 'marketplace.cart.index':
                $breadcrumbs[] = [
                    'title' => __('ui.marketplace.cart'),
                    'url' => route('marketplace.cart.index'),
                    'active' => false
                ];
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Generate user-related breadcrumbs
     */
    private function generateUserBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        switch ($routeName) {
            case 'users.index':
                $breadcrumbs[] = [
                    'title' => __('ui.navigation.users'),
                    'url' => route('users.index'),
                    'active' => false
                ];
                break;

            case 'profile.show':
                $user = $request->route('user');
                $breadcrumbs[] = [
                    'title' => __('ui.navigation.users'),
                    'url' => route('users.index'),
                    'active' => false
                ];

                if ($user) {
                    $breadcrumbs[] = [
                        'title' => $user->name,
                        'url' => route('profile.show', $user->username),
                        'active' => false
                    ];
                }
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Generate member-related breadcrumbs
     */
    private function generateMemberBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'title' => __('ui.navigation.members'),
            'url' => route('members.index'),
            'active' => false
        ];

        switch ($routeName) {
            case 'members.online':
                $breadcrumbs[] = [
                    'title' => __('ui.members.online'),
                    'url' => route('members.online'),
                    'active' => false
                ];
                break;

            case 'members.staff':
                $breadcrumbs[] = [
                    'title' => __('ui.members.staff'),
                    'url' => route('members.staff'),
                    'active' => false
                ];
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Generate showcase-related breadcrumbs
     */
    private function generateShowcaseBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'title' => __('ui.navigation.showcase'),
            'url' => route('showcase.index'),
            'active' => false
        ];

        switch ($routeName) {
            case 'showcase.show':
                $showcase = $request->route('showcase');
                if ($showcase) {
                    $breadcrumbs[] = [
                        'title' => Str::limit($showcase->title, 50),
                        'url' => route('showcase.show', $showcase->slug),
                        'active' => false
                    ];
                }
                break;

            case 'showcase.create':
                $breadcrumbs[] = [
                    'title' => __('ui.showcase.create'),
                    'url' => route('showcase.create'),
                    'active' => false
                ];
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Generate tools-related breadcrumbs
     */
    private function generateToolsBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'title' => __('ui.navigation.tools'),
            'url' => route('tools.index'),
            'active' => false
        ];

        switch ($routeName) {
            case 'tools.calculators':
                $breadcrumbs[] = [
                    'title' => __('ui.tools.calculators'),
                    'url' => route('tools.calculators'),
                    'active' => false
                ];
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Generate pages-related breadcrumbs
     */
    private function generatePagesBreadcrumbs(string $routeName, Request $request): array
    {
        $breadcrumbs = [];

        if ($routeName === 'pages.show') {
            $page = $request->route('slug');
            if ($page) {
                $breadcrumbs[] = [
                    'title' => Str::limit($page, 50),
                    'url' => route('pages.show', $page),
                    'active' => false
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Generate generic breadcrumbs for other routes
     */
    private function generateGenericBreadcrumbs(string $routeName, Request $request, ?PageSeo $pageSeo): array
    {
        $breadcrumbs = [];

        // Use SEO title if available, otherwise generate from route name
        $title = $pageSeo ? $pageSeo->title : $this->generateTitleFromRoute($routeName);

        if ($title) {
            $breadcrumbs[] = [
                'title' => $title,
                'url' => $request->url(),
                'active' => false
            ];
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

        // Replace common placeholders
        foreach ($routeParams as $key => $value) {
            if (is_object($value)) {
                // Handle model objects
                if (method_exists($value, 'name')) {
                    $title = str_replace('{' . $key . '_name}', $value->name, $title);
                }
                if (method_exists($value, 'title')) {
                    $title = str_replace('{' . $key . '_title}', $value->title, $title);
                }
                if (method_exists($value, 'username')) {
                    $title = str_replace('{user_name}', $value->username, $title);
                }
            } else {
                // Handle string parameters
                $title = str_replace('{' . $key . '}', $value, $title);
            }
        }

        return $title;
    }

    /**
     * Generate URL from route name
     */
    private function generateUrlFromRoute(string $routeName, Request $request): string
    {
        try {
            // Try to generate route URL
            if (Route::has($routeName)) {
                return route($routeName);
            }
        } catch (\Exception $e) {
            // Fallback to current URL structure
        }

        return $request->url();
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

    /**
     * Generate title from route name (legacy fallback)
     */
    private function generateTitleFromRoute(string $routeName): string
    {
        // Convert route name to readable title
        $parts = explode('.', $routeName);
        $lastPart = end($parts);

        return Str::title(str_replace(['-', '_'], ' ', $lastPart));
    }
}
