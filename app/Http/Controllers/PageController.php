<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class PageController extends Controller
{
    /**
     * Display a specific page by slug
     */
    public function show(string $slug): View|Response
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'user'])
            ->firstOrFail();

        // Increment view count
        $page->increment('view_count');

        // Set SEO meta tags
        $seoData = [
            'title' => $page->meta_title ?: $page->title,
            'description' => $page->meta_description ?: $page->excerpt,
            'keywords' => $page->meta_keywords,
            'og_title' => $page->meta_title ?: $page->title,
            'og_description' => $page->meta_description ?: $page->excerpt,
            'canonical' => url()->current(),
        ];

        return view('pages.dynamic', compact('page', 'seoData'));
    }

    /**
     * Display page by predefined route names
     */
    public function showByRoute(string $routeName): View|Response
    {
        // Map route names to slugs
        $routeSlugMap = [
            'terms' => 'dieu-khoan-su-dung',
            'privacy' => 'chinh-sach-bao-mat',
            'about' => 've-chung-toi',
            'contact' => 'lien-he',
            'rules' => 'quy-dinh-cong-dong',
            'help' => 'tro-giup',
            'faq' => 'cau-hoi-thuong-gap',
        ];

        $slug = $routeSlugMap[$routeName] ?? $routeName;
        
        return $this->show($slug);
    }

    /**
     * Display all pages in a category
     */
    public function category(string $categorySlug): View
    {
        $category = PageCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $pages = Page::where('category_id', $category->id)
            ->where('status', 'published')
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('pages.category', compact('category', 'pages'));
    }

    /**
     * Display all page categories
     */
    public function categories(): View
    {
        $categories = PageCategory::where('is_active', true)
            ->withCount(['pages' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('order')
            ->get();

        return view('pages.categories', compact('categories'));
    }

    /**
     * Search pages
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category');

        $pagesQuery = Page::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            });

        if ($categoryId) {
            $pagesQuery->where('category_id', $categoryId);
        }

        $pages = $pagesQuery->with(['category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = PageCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.search', compact('pages', 'categories', 'query', 'categoryId'));
    }

    /**
     * Get page content for AJAX requests
     */
    public function getContent(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->select('id', 'title', 'content', 'excerpt', 'updated_at')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'content' => $page->content,
                'excerpt' => $page->excerpt,
                'last_updated' => $page->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Get sitemap data for pages
     */
    public function getSitemapData()
    {
        $pages = Page::where('status', 'published')
            ->select('slug', 'updated_at', 'created_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $pages->map(function ($page) {
            return [
                'url' => route('pages.show', $page->slug),
                'lastmod' => $page->updated_at->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        });
    }

    /**
     * Handle legacy routes and redirects
     */
    public function handleLegacyRoute(string $path)
    {
        // Map old paths to new slugs
        $legacyMap = [
            'terms-of-service' => 'dieu-khoan-su-dung',
            'privacy-policy' => 'chinh-sach-bao-mat',
            'about-us' => 've-chung-toi',
            'contact-us' => 'lien-he',
            'community-rules' => 'quy-dinh-cong-dong',
            'help-center' => 'tro-giup',
        ];

        if (isset($legacyMap[$path])) {
            return redirect()->route('pages.show', $legacyMap[$path], 301);
        }

        // Try to find page by old path
        $page = Page::where('slug', $path)
            ->orWhere('slug', 'like', "%{$path}%")
            ->where('status', 'published')
            ->first();

        if ($page) {
            return redirect()->route('pages.show', $page->slug, 301);
        }

        abort(404);
    }

    /**
     * Get related pages
     */
    protected function getRelatedPages(Page $page, int $limit = 3)
    {
        return Page::where('category_id', $page->category_id)
            ->where('id', '!=', $page->id)
            ->where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular pages
     */
    public function popular(): View
    {
        $pages = Page::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->with(['category'])
            ->get();

        return view('pages.popular', compact('pages'));
    }

    /**
     * Get recent pages
     */
    public function recent(): View
    {
        $pages = Page::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->with(['category'])
            ->get();

        return view('pages.recent', compact('pages'));
    }
}
