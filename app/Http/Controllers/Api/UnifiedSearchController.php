<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Showcase;
use App\Models\MarketplaceProduct;
use App\Models\TechnicalProduct;
use App\Models\User;
use App\Models\Forum;
use App\Services\UnifiedImageDisplayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Unified Search Controller for Header AJAX Search
 * Supports multi-content search: Threads, Showcases, Products, Users
 */
class UnifiedSearchController extends Controller
{
    protected $imageService;

    public function __construct(UnifiedImageDisplayService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Unified AJAX search for header input
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:20'
        ]);

        $query = $request->input('q');
        $limit = $request->input('limit', 12); // Total results limit
        $perCategory = 3; // Results per category

        $results = [
            'threads' => [],
            'showcases' => [],
            'products' => [],
            'users' => [],
            'meta' => [
                'query' => $query,
                'total' => 0,
                'categories' => []
            ]
        ];

        try {
            // 1. Search Threads (Forum discussions)
            try {
                $threads = $this->searchThreads($query, $perCategory);
                $results['threads'] = $threads;
                $results['meta']['categories']['threads'] = count($threads);
            } catch (\Exception $e) {
                \Log::error('Thread search error: ' . $e->getMessage());
                $results['meta']['categories']['threads'] = 0;
            }

            // 2. Search Showcases (Project showcases)
            try {
                $showcases = $this->searchShowcases($query, $perCategory);
                $results['showcases'] = $showcases;
                $results['meta']['categories']['showcases'] = count($showcases);
            } catch (\Exception $e) {
                \Log::error('Showcase search error: ' . $e->getMessage());
                $results['meta']['categories']['showcases'] = 0;
            }

            // 3. Search Products (Marketplace & Technical products)
            try {
                $products = $this->searchProducts($query, $perCategory);
                $results['products'] = $products;
                $results['meta']['categories']['products'] = count($products);
            } catch (\Exception $e) {
                \Log::error('Product search error: ' . $e->getMessage());
                $results['meta']['categories']['products'] = 0;
            }

            // 4. Search Users (if query looks like username/name)
            if ($this->shouldSearchUsers($query)) {
                try {
                    $users = $this->searchUsers($query, 2);
                    $results['users'] = $users;
                    $results['meta']['categories']['users'] = count($users);
                } catch (\Exception $e) {
                    \Log::error('User search error: ' . $e->getMessage());
                    $results['meta']['categories']['users'] = 0;
                }
            }

            $results['meta']['total'] = array_sum($results['meta']['categories']);

            return response()->json([
                'success' => true,
                'results' => $results,
                'advanced_search_url' => route('forums.search.advanced', ['q' => $query])
            ]);

        } catch (\Exception $e) {
            \Log::error('Unified search error: ' . $e->getMessage());

            // Still return partial results if available
            $results['meta']['total'] = array_sum($results['meta']['categories']);

            return response()->json([
                'success' => $results['meta']['total'] > 0, // Success if we have any results
                'message' => $results['meta']['total'] > 0 ? 'Partial results available' : 'Search temporarily unavailable',
                'results' => $results,
                'advanced_search_url' => route('forums.search.advanced', ['q' => $query])
            ], $results['meta']['total'] > 0 ? 200 : 500);
        }
    }

    /**
     * Search threads with relevance scoring
     */
    private function searchThreads(string $query, int $limit): array
    {
        $threads = Thread::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->where('moderation_status', 'approved')
            ->where('is_spam', false)
            ->whereNull('hidden_at')
            ->with(['user', 'forum', 'category', 'media'])
            ->withCount('allComments as comments_count')
            ->orderByRaw("
                CASE
                    WHEN title LIKE ? THEN 1
                    WHEN title LIKE ? THEN 2
                    WHEN content LIKE ? THEN 3
                    ELSE 4
                END, created_at DESC
            ", ["{$query}%", "%{$query}%", "%{$query}%"])
            ->limit($limit)
            ->get();

        return $threads->map(function ($thread) {
            return [
                'id' => $thread->id,
                'type' => 'thread',
                'title' => $thread->title,
                'excerpt' => Str::limit(strip_tags($thread->content), 80),
                'url' => route('threads.show', $thread),
                'author' => [
                    'name' => $thread->user->name,
                    'avatar' => $thread->user->getAvatarUrl()
                ],
                'forum' => [
                    'name' => $thread->forum->name,
                    'url' => route('forums.show', $thread->forum)
                ],
                'image' => $this->imageService->getThreadDisplayImage($thread),
                'stats' => [
                    'comments' => $thread->comments_count,
                    'views' => $thread->view_count ?? 0
                ],
                'created_at' => $thread->created_at->diffForHumans()
            ];
        })->toArray();
    }

    /**
     * Search showcases
     */
    private function searchShowcases(string $query, int $limit): array
    {
        try {
            $showcases = Showcase::where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('project_type', 'like', "%{$query}%")
                      ->orWhere('materials', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->where('is_public', true)
                ->with(['user'])
                ->orderByRaw("
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN project_type LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END, featured_at DESC, created_at DESC
                ", ["{$query}%", "%{$query}%", "%{$query}%"])
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Showcase search error: ' . $e->getMessage());
            return [];
        }

        return $showcases->map(function ($showcase) {
            return [
                'id' => $showcase->id,
                'type' => 'showcase',
                'title' => $showcase->title,
                'excerpt' => Str::limit(strip_tags($showcase->description), 80),
                'url' => route('showcase.show', $showcase),
                'author' => [
                    'name' => $showcase->user->name,
                    'avatar' => $showcase->user->getAvatarUrl()
                ],
                'project_type' => $showcase->project_type,
                'complexity' => $showcase->complexity_level,
                'image' => $showcase->cover_image ? asset(ltrim($showcase->cover_image, '/')) : null,
                'stats' => [
                    'views' => $showcase->view_count ?? 0,
                    'likes' => $showcase->like_count ?? 0,
                    'rating' => round($showcase->rating_average ?? 0, 1)
                ],
                'created_at' => $showcase->created_at->diffForHumans()
            ];
        })->toArray();
    }

    /**
     * Search products (both marketplace and technical)
     */
    private function searchProducts(string $query, int $limit): array
    {
        $products = collect();

        try {
            // Search Marketplace Products
            $marketplaceProducts = MarketplaceProduct::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->where('is_active', true)
                ->with(['seller.user'])
                ->limit($limit)
                ->get()
                ->map(function ($product) {
                try {
                    return [
                        'id' => $product->id,
                        'type' => 'marketplace_product',
                        'title' => $product->name,
                        'excerpt' => Str::limit(strip_tags($product->description), 80),
                        'url' => route('marketplace.products.show', $product),
                        'seller' => [
                            'name' => $product->seller->business_name ?? $product->seller->user->name,
                            'avatar' => $product->seller->user->getAvatarUrl()
                        ],
                        'price' => [
                            'amount' => $product->price,
                            'currency' => 'USD',
                            'formatted' => '$' . number_format($product->price, 2)
                        ],
                        'image' => $product->featured_image ? asset($product->featured_image) : null,
                        'stats' => [
                            'views' => $product->view_count ?? 0,
                            'purchases' => $product->purchase_count ?? 0
                        ],
                        'created_at' => $product->created_at->diffForHumans()
                    ];
                } catch (\Exception $e) {
                    \Log::error('Marketplace product mapping error for ID ' . $product->id . ': ' . $e->getMessage());
                    return null;
                }
            })->filter();

            // Search Technical Products
            $technicalProducts = TechnicalProduct::where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('keywords', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->with(['seller'])
                ->limit($limit)
                ->get()
                ->map(function ($product) {
                try {
                    return [
                        'id' => $product->id,
                        'type' => 'technical_product',
                        'title' => $product->title,
                        'excerpt' => Str::limit(strip_tags($product->description), 80),
                        'url' => '#', // Temporary placeholder since route doesn't exist
                        'seller' => [
                            'name' => $product->seller->business_name ?? $product->seller->name,
                            'avatar' => $product->seller->getAvatarUrl()
                        ],
                        'price' => [
                            'amount' => $product->price,
                            'currency' => $product->currency,
                            'formatted' => '$' . number_format($product->price, 2)
                        ],
                        'complexity' => $product->complexity_level,
                        'stats' => [
                            'views' => $product->view_count ?? 0,
                            'downloads' => $product->download_count ?? 0,
                            'rating' => round($product->rating_average ?? 0, 1)
                        ],
                        'created_at' => $product->created_at->diffForHumans()
                    ];
                } catch (\Exception $e) {
                    \Log::error('Technical product mapping error for ID ' . $product->id . ': ' . $e->getMessage());
                    return null;
                }
            })->filter();

            // Merge and sort by relevance
            $allProducts = $products->concat($marketplaceProducts)->concat($technicalProducts);

            return $allProducts->take($limit)->values()->toArray();
        } catch (\Exception $e) {
            \Log::error('Product search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search users
     */
    private function searchUsers(string $query, int $limit): array
    {
        // Clean query - remove @ prefix if present
        $cleanQuery = str_starts_with($query, '@') ? substr($query, 1) : $query;

        // Escape special characters for LIKE query
        $cleanQuery = str_replace(['%', '_'], ['\%', '\_'], $cleanQuery);

        if (empty($cleanQuery)) {
            return [];
        }

        $users = User::where(function ($q) use ($cleanQuery) {
                $q->where('name', 'like', "%{$cleanQuery}%")
                  ->orWhere('username', 'like', "%{$cleanQuery}%")
                  ->orWhere('company_name', 'like', "%{$cleanQuery}%");
            })
            ->where('status', 'active')
            ->whereNull('banned_at')
            ->orderByRaw("
                CASE
                    WHEN username LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN company_name LIKE ? THEN 3
                    ELSE 4
                END
            ", ["{$cleanQuery}%", "{$cleanQuery}%", "{$cleanQuery}%"])
            ->limit($limit)
            ->get();

        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'type' => 'user',
                'name' => $user->name,
                'username' => $user->username,
                'company_name' => $user->company_name,
                'role' => $user->role,
                'avatar' => $user->getAvatarUrl(),
                'url' => route('profile.show', $user),
                'stats' => [
                    'threads' => $user->threads_count ?? 0,
                    'posts' => $user->posts_count ?? 0
                ]
            ];
        })->toArray();
    }

    /**
     * Determine if we should search users based on query pattern
     */
    private function shouldSearchUsers(string $query): bool
    {
        // Search users if query starts with @
        if (str_starts_with($query, '@')) {
            return true;
        }

        // Search users if query looks like a username (alphanumeric + underscore only)
        // and is reasonably short (3-15 characters)
        if (preg_match('/^[a-zA-Z0-9_]+$/', $query) && strlen($query) >= 3 && strlen($query) <= 15) {
            return true;
        }

        return false;
    }
}
