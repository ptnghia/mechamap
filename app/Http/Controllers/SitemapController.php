<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\User;
use App\Models\TechnicalProduct;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index(): Response
    {
        $sitemaps = [
            [
                'loc' => route('sitemap.pages'),
                'lastmod' => $this->getLastModified('pages'),
            ],
            [
                'loc' => route('sitemap.forums'),
                'lastmod' => $this->getLastModified('forums'),
            ],
            [
                'loc' => route('sitemap.threads'),
                'lastmod' => $this->getLastModified('threads'),
            ],
            [
                'loc' => route('sitemap.users'),
                'lastmod' => $this->getLastModified('users'),
            ],
            [
                'loc' => route('sitemap.products'),
                'lastmod' => $this->getLastModified('products'),
            ],
        ];

        $xml = $this->generateSitemapIndex($sitemaps);

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Generate pages sitemap
     */
    public function pages(): Response
    {
        $cacheKey = 'sitemap_pages';
        
        $xml = Cache::remember($cacheKey, 3600, function () {
            $pages = Page::where('status', 'published')
                ->select('slug', 'updated_at', 'created_at')
                ->orderBy('updated_at', 'desc')
                ->get();

            $urls = [];
            
            // Add static routes
            $urls[] = [
                'loc' => route('home'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            // Add dynamic pages
            foreach ($pages as $page) {
                $urls[] = [
                    'loc' => route('pages.show', $page->slug),
                    'lastmod' => $page->updated_at->toISOString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }

            return $this->generateSitemap($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Generate forums sitemap
     */
    public function forums(): Response
    {
        $cacheKey = 'sitemap_forums';
        
        $xml = Cache::remember($cacheKey, 3600, function () {
            $forums = Forum::where('is_active', true)
                ->select('slug', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->get();

            $urls = [];
            
            // Add forums index
            $urls[] = [
                'loc' => route('forums.index'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];

            // Add individual forums
            foreach ($forums as $forum) {
                $urls[] = [
                    'loc' => route('forums.show', $forum->slug),
                    'lastmod' => $forum->updated_at->toISOString(),
                    'changefreq' => 'daily',
                    'priority' => '0.8',
                ];
            }

            return $this->generateSitemap($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Generate threads sitemap
     */
    public function threads(): Response
    {
        $cacheKey = 'sitemap_threads';
        
        $xml = Cache::remember($cacheKey, 1800, function () {
            $threads = Thread::where('status', 'published')
                ->select('slug', 'updated_at', 'created_at')
                ->orderBy('updated_at', 'desc')
                ->limit(10000) // Limit for performance
                ->get();

            $urls = [];
            
            foreach ($threads as $thread) {
                $urls[] = [
                    'loc' => route('threads.show', $thread->slug),
                    'lastmod' => $thread->updated_at->toISOString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            return $this->generateSitemap($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }

    /**
     * Generate users sitemap
     */
    public function users(): Response
    {
        $cacheKey = 'sitemap_users';
        
        $xml = Cache::remember($cacheKey, 3600, function () {
            $users = User::where('is_active', true)
                ->whereNotNull('username')
                ->select('username', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->limit(5000) // Limit for performance
                ->get();

            $urls = [];
            
            // Add members index
            $urls[] = [
                'loc' => route('members.index'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '0.6',
            ];

            // Add individual user profiles
            foreach ($users as $user) {
                $urls[] = [
                    'loc' => route('members.show', $user->username),
                    'lastmod' => $user->updated_at->toISOString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

            return $this->generateSitemap($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Generate products sitemap
     */
    public function products(): Response
    {
        $cacheKey = 'sitemap_products';
        
        $xml = Cache::remember($cacheKey, 1800, function () {
            $products = TechnicalProduct::where('status', 'active')
                ->select('slug', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->limit(10000)
                ->get();

            $urls = [];
            
            // Add marketplace index
            $urls[] = [
                'loc' => route('marketplace.index'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];

            // Add individual products
            foreach ($products as $product) {
                $urls[] = [
                    'loc' => route('marketplace.products.show', $product->slug),
                    'lastmod' => $product->updated_at->toISOString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            return $this->generateSitemap($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }

    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /register\n";
        $content .= "Disallow: /password/\n";
        $content .= "Disallow: /search?*\n";
        $content .= "Disallow: /*?page=*\n";
        $content .= "Disallow: /*?sort=*\n";
        $content .= "\n";
        $content .= "Sitemap: " . route('sitemap.index') . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Generate sitemap index XML
     */
    private function generateSitemapIndex(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars($sitemap['loc']) . "</loc>\n";
            $xml .= "    <lastmod>" . $sitemap['lastmod'] . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Generate sitemap XML
     */
    private function generateSitemap(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get last modified date for a content type
     */
    private function getLastModified(string $type): string
    {
        return match($type) {
            'pages' => Page::max('updated_at')?->toISOString() ?? now()->toISOString(),
            'forums' => Forum::max('updated_at')?->toISOString() ?? now()->toISOString(),
            'threads' => Thread::max('updated_at')?->toISOString() ?? now()->toISOString(),
            'users' => User::max('updated_at')?->toISOString() ?? now()->toISOString(),
            'products' => TechnicalProduct::max('updated_at')?->toISOString() ?? now()->toISOString(),
            default => now()->toISOString(),
        };
    }
}
