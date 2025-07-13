<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use App\Services\AutoCompleteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\SearchLog;

/**
 * Advanced Search Controller
 * Handles advanced search functionality with Elasticsearch integration
 */
class AdvancedSearchController extends Controller
{
    protected $searchService;
    protected $autoCompleteService;

    public function __construct(
        SearchService $searchService,
        AutoCompleteService $autoCompleteService
    ) {
        $this->searchService = $searchService;
        $this->autoCompleteService = $autoCompleteService;
    }

    /**
     * Display advanced search interface
     */
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $filters = $request->get('filters', []);
        $results = [];
        $facets = [];
        $suggestions = [];

        if ($query) {
            try {
                $searchResults = $this->searchService->search($query, $filters, [
                    'page' => $request->get('page', 1),
                    'per_page' => $request->get('per_page', 20),
                    'sort' => $request->get('sort', 'relevance'),
                    'include_facets' => true,
                    'include_suggestions' => true,
                ]);

                $results = $searchResults['results'];
                $facets = $searchResults['facets'] ?? [];
                $suggestions = $searchResults['suggestions'] ?? [];

            } catch (\Exception $e) {
                Log::error('Advanced search error: ' . $e->getMessage());
                $results = $this->fallbackSearch($query, $filters);
            }
        }

        $popularSearches = $this->getPopularSearches();
        $recentSearches = $this->getRecentSearches($request);
        $searchCategories = $this->getSearchCategories();

        return view('search.advanced', compact(
            'query',
            'filters',
            'results',
            'facets',
            'suggestions',
            'popularSearches',
            'recentSearches',
            'searchCategories'
        ));
    }

    /**
     * Perform AJAX search
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'filters' => 'nullable|array',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|string|in:relevance,date,popularity,price_asc,price_desc',
        ]);

        try {
            $results = $this->searchService->search(
                $request->get('q'),
                $request->get('filters', []),
                [
                    'page' => $request->get('page', 1),
                    'per_page' => $request->get('per_page', 20),
                    'sort' => $request->get('sort', 'relevance'),
                    'include_facets' => $request->boolean('include_facets'),
                    'include_suggestions' => $request->boolean('include_suggestions'),
                    'include_highlights' => true,
                ]
            );

            // Track search query
            $this->trackSearch($request->get('q'), $request->ip());

            return response()->json([
                'success' => true,
                'data' => $results,
                'query' => $request->get('q'),
                'total_time' => $results['meta']['search_time'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('Search API error: ' . $e->getMessage());

            // Fallback to database search
            $fallbackResults = $this->fallbackSearch(
                $request->get('q'),
                $request->get('filters', [])
            );

            return response()->json([
                'success' => true,
                'data' => $fallbackResults,
                'query' => $request->get('q'),
                'fallback' => true,
                'message' => 'Using fallback search due to service unavailability'
            ]);
        }
    }

    /**
     * Get auto-complete suggestions
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
            'type' => 'nullable|string|in:all,threads,products,users,categories',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 10);

        try {
            $suggestions = $this->autoCompleteService->getSuggestions($query, $type, $limit);

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'query' => $query,
            ]);

        } catch (\Exception $e) {
            Log::error('Autocomplete error: ' . $e->getMessage());

            // Fallback to simple database suggestions
            $fallbackSuggestions = $this->getFallbackSuggestions($query, $type, $limit);

            return response()->json([
                'success' => true,
                'data' => $fallbackSuggestions,
                'query' => $query,
                'fallback' => true,
            ]);
        }
    }

    /**
     * Get search suggestions for typos and similar queries
     */
    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        try {
            $suggestions = $this->searchService->getSpellingSuggestions($request->get('q'));

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'query' => $request->get('q'),
            ]);

        } catch (\Exception $e) {
            Log::error('Suggestions error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to get suggestions',
                'data' => [],
            ]);
        }
    }

    /**
     * Get faceted search filters
     */
    public function facets(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'filters' => 'nullable|array',
        ]);

        try {
            $facets = $this->searchService->getFacets(
                $request->get('q', ''),
                $request->get('filters', [])
            );

            return response()->json([
                'success' => true,
                'data' => $facets,
            ]);

        } catch (\Exception $e) {
            Log::error('Facets error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to get facets',
                'data' => [],
            ]);
        }
    }

    /**
     * Save search query for user
     */
    public function saveSearch(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'filters' => 'nullable|array',
            'name' => 'nullable|string|max:100',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required to save searches',
            ], 401);
        }

        try {
            $savedSearch = auth()->user()->savedSearches()->create([
                'name' => $request->get('name') ?: $request->get('query'),
                'query' => $request->get('query'),
                'filters' => $request->get('filters', []),
                'search_count' => 1,
                'last_used_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $savedSearch,
                'message' => 'Search saved successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Save search error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to save search',
            ], 500);
        }
    }

    /**
     * Get user's saved searches
     */
    public function savedSearches(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $savedSearches = auth()->user()->savedSearches()
            ->orderBy('last_used_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $savedSearches,
        ]);
    }

    /**
     * Get search analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        $period = $request->get('period', '7d'); // 1d, 7d, 30d, 90d

        try {
            $analytics = $this->searchService->getSearchAnalytics($period);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'period' => $period,
            ]);

        } catch (\Exception $e) {
            Log::error('Search analytics error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to get search analytics',
            ], 500);
        }
    }

    // Private helper methods

    private function fallbackSearch(string $query, array $filters = []): array
    {
        // Implement database fallback search
        $results = [];

        // Search in threads
        $threads = \App\Models\Thread::where('title', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->with(['user', 'category'])
            ->limit(10)
            ->get();

        foreach ($threads as $thread) {
            $results[] = [
                'type' => 'thread',
                'id' => $thread->id,
                'title' => $thread->title,
                'content' => substr(strip_tags($thread->content), 0, 200),
                'url' => route('threads.show', $thread),
                'user' => $thread->user->name,
                'category' => $thread->category->name,
                'created_at' => $thread->created_at,
                'score' => $this->calculateRelevanceScore($query, $thread->title . ' ' . $thread->content),
            ];
        }

        // TODO: Marketplace search - disabled for forum focus
        // Will be enabled later when marketplace is priority
        /*
        $products = \App\Models\MarketplaceProduct::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with(['seller', 'category'])
            ->limit(10)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'id' => $product->id,
                'title' => $product->name,
                'content' => substr(strip_tags($product->description), 0, 200),
                'url' => route('marketplace.products.show', $product),
                'user' => $product->seller ? $product->seller->business_name : 'Unknown Seller',
                'price' => $product->price,
                'created_at' => $product->created_at,
                'score' => $this->calculateRelevanceScore($query, $product->name . ' ' . $product->description),
            ];
        }
        */

        // Sort by relevance score
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return [
            'results' => array_slice($results, 0, 20),
            'total' => count($results),
            'meta' => [
                'search_time' => 0,
                'fallback' => true,
            ],
        ];
    }

    private function calculateRelevanceScore(string $query, string $content): float
    {
        $query = strtolower($query);
        $content = strtolower($content);

        // Simple relevance scoring
        $score = 0;

        // Exact match in title gets highest score
        if (strpos($content, $query) !== false) {
            $score += 10;
        }

        // Word matches
        $queryWords = explode(' ', $query);
        foreach ($queryWords as $word) {
            if (strlen($word) > 2 && strpos($content, $word) !== false) {
                $score += 2;
            }
        }

        return $score;
    }

    private function getFallbackSuggestions(string $query, string $type, int $limit): array
    {
        $suggestions = [];
        $query = strtolower($query);

        if ($type === 'all' || $type === 'threads') {
            $threads = \App\Models\Thread::where('title', 'LIKE', "{$query}%")
                ->limit($limit)
                ->pluck('title')
                ->toArray();

            foreach ($threads as $title) {
                $suggestions[] = [
                    'text' => $title,
                    'type' => 'thread',
                    'category' => 'Threads',
                ];
            }
        }

        if ($type === 'all' || $type === 'products') {
            $products = \App\Models\MarketplaceProduct::where('name', 'LIKE', "{$query}%")
                ->limit($limit)
                ->pluck('name')
                ->toArray();

            foreach ($products as $name) {
                $suggestions[] = [
                    'text' => $name,
                    'type' => 'product',
                    'category' => 'Products',
                ];
            }
        }

        return array_slice($suggestions, 0, $limit);
    }

    private function getPopularSearches(): array
    {
        return Cache::remember('popular_searches', 3600, function () {
            // Get from search analytics or predefined list
            return [
                'máy tiện CNC',
                'động cơ servo',
                'bearing SKF',
                'thép không gỉ',
                'máy phay',
                'sensor áp suất',
                'motor giảm tốc',
                'van điện từ',
            ];
        });
    }

    private function getRecentSearches(Request $request): array
    {
        if (auth()->check()) {
            return auth()->user()->searchHistory()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->pluck('query')
                ->toArray();
        }

        // Get from session for guests
        return session()->get('recent_searches', []);
    }

    private function getSearchCategories(): array
    {
        return [
            'all' => 'Tất cả',
            'threads' => 'Thảo luận',
            'products' => 'Sản phẩm',
            'users' => 'Người dùng',
            'documents' => 'Tài liệu',
            'cad_files' => 'File CAD',
        ];
    }

    private function trackSearch(string $query, string $ip): void
    {
        try {
            // Track search for analytics
            \App\Models\SearchLog::create([
                'query' => $query,
                'user_id' => auth()->id(),
                'ip_address' => $ip,
                'user_agent' => request()->userAgent(),
                'results_count' => 0, // Will be updated by search service
                'search_time_ms' => 0, // Will be updated by search service
            ]);

            // Update user's search history
            if (auth()->check()) {
                auth()->user()->searchHistory()->updateOrCreate(
                    ['query' => $query],
                    ['search_count' => \DB::raw('search_count + 1'), 'last_searched_at' => now()]
                );
            }

            // Update session recent searches for guests
            if (!auth()->check()) {
                $recentSearches = session()->get('recent_searches', []);
                $recentSearches = array_unique(array_merge([$query], $recentSearches));
                session()->put('recent_searches', array_slice($recentSearches, 0, 10));
            }

        } catch (\Exception $e) {
            Log::error('Search tracking error: ' . $e->getMessage());
        }
    }

    /**
     * Basic search interface (merged from SearchController)
     */
    public function basic(Request $request): View
    {
        $startTime = microtime(true);
        $query = $request->input('query');
        $type = $request->input('type', 'all');

        $threads = collect();
        $posts = collect();
        $users = collect();
        $totalResults = 0;

        if ($query) {
            // Try Elasticsearch first, fallback to database
            try {
                if ($this->searchService->isAvailable()) {
                    $searchResults = $this->searchService->search($query, ['type' => $type], [
                        'per_page' => 30,
                        'include_highlights' => true
                    ]);

                    // Convert Elasticsearch results to basic format
                    $threads = collect($searchResults['results']['threads'] ?? []);
                    $posts = collect($searchResults['results']['posts'] ?? []);
                    $users = collect($searchResults['results']['users'] ?? []);
                    $totalResults = $searchResults['meta']['total'] ?? 0;
                } else {
                    throw new \Exception('Elasticsearch not available');
                }
            } catch (\Exception $e) {
                // Fallback to database search
                $results = $this->fallbackBasicSearch($query, $type);
                $threads = $results['threads'];
                $posts = $results['posts'];
                $users = $results['users'];
                $totalResults = $results['total'];
            }

            // Log search activity
            $this->logSearch($query, $request, $totalResults, $startTime, [
                'search_type' => $type,
                'content_type' => 'basic',
                'method' => 'basic_search'
            ]);
        }

        return view('search.basic', compact('query', 'type', 'threads', 'posts', 'users', 'totalResults'));
    }

    /**
     * AJAX search (merged from SearchController)
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $query = $request->input('query');
        $scope = $request->input('scope', 'global');
        $threadId = $request->input('thread_id');
        $forumId = $request->input('forum_id');

        $results = [];
        $totalResults = 0;

        if ($query && strlen($query) >= 2) {
            try {
                if ($this->searchService->isAvailable()) {
                    // Use Elasticsearch for AJAX search
                    $filters = [];
                    if ($scope === 'thread' && $threadId) {
                        $filters['thread_id'] = $threadId;
                    } elseif ($scope === 'forum' && $forumId) {
                        $filters['forum_id'] = $forumId;
                    }

                    $searchResults = $this->searchService->search($query, $filters, [
                        'per_page' => 10,
                        'include_highlights' => true
                    ]);

                    $results = $this->formatAjaxResults($searchResults['results']);
                    $totalResults = $searchResults['meta']['total'] ?? 0;
                } else {
                    throw new \Exception('Elasticsearch not available');
                }
            } catch (\Exception $e) {
                // Fallback to database AJAX search
                $results = $this->fallbackAjaxSearch($query, $scope, $threadId, $forumId);
                $totalResults = collect($results)->flatten(1)->count();
            }

            // Log AJAX search
            $this->logSearch($query, $request, $totalResults, $startTime, [
                'search_type' => 'ajax',
                'content_type' => 'ajax',
                'scope' => $scope,
                'thread_id' => $threadId,
                'forum_id' => $forumId
            ]);
        }

        return response()->json([
            'results' => $results,
            'total' => $totalResults,
            'advanced_search_url' => route('search.advanced')
        ]);
    }

    /**
     * Fallback basic search using database
     */
    private function fallbackBasicSearch(string $query, string $type): array
    {
        $threads = collect();
        $posts = collect();
        $users = collect();

        // Search threads
        if ($type == 'all' || $type == 'threads') {
            $threads = Thread::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->with(['user', 'forum'])
                ->latest()
                ->take(10)
                ->get();
        }

        // Search posts
        if ($type == 'all' || $type == 'posts') {
            $posts = Post::where('content', 'like', "%{$query}%")
                ->with(['user', 'thread'])
                ->latest()
                ->take(10)
                ->get();
        }

        // Search users
        if ($type == 'all' || $type == 'users') {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->latest()
                ->take(10)
                ->get();
        }

        return [
            'threads' => $threads,
            'posts' => $posts,
            'users' => $users,
            'total' => $threads->count() + $posts->count() + $users->count()
        ];
    }

    /**
     * Fallback AJAX search using database
     */
    private function fallbackAjaxSearch(string $query, string $scope, $threadId = null, $forumId = null): array
    {
        $results = [];

        if ($scope === 'thread' && $threadId) {
            // Search within specific thread
            $posts = Post::where('thread_id', $threadId)
                ->where('content', 'like', "%{$query}%")
                ->with(['user'])
                ->take(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'content' => Str::limit(strip_tags($post->content), 100),
                        'user' => [
                            'name' => $post->user->name,
                            'username' => $post->user->username
                        ],
                        'url' => route('threads.show', $post->thread_id) . '#post-' . $post->id
                    ];
                });
            $results['posts'] = $posts;

        } elseif ($scope === 'forum' && $forumId) {
            // Search within specific forum
            $threads = Thread::where('forum_id', $forumId)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                })
                ->with(['user'])
                ->take(5)
                ->get()
                ->map(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'content' => Str::limit(strip_tags($thread->content), 100),
                        'user' => [
                            'name' => $thread->user->name,
                            'username' => $thread->user->username
                        ],
                        'url' => route('threads.show', $thread)
                    ];
                });
            $results['threads'] = $threads;

        } else {
            // Global search
            $threads = Thread::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->with(['user', 'forum'])
                ->take(5)
                ->get()
                ->map(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'content' => Str::limit(strip_tags($thread->content), 100),
                        'user' => [
                            'name' => $thread->user->name,
                            'username' => $thread->user->username
                        ],
                        'forum' => [
                            'name' => $thread->forum->name,
                            'url' => route('forums.show', $thread->forum)
                        ],
                        'url' => route('threads.show', $thread)
                    ];
                });

            $posts = Post::where('content', 'like', "%{$query}%")
                ->with(['user', 'thread'])
                ->take(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'content' => Str::limit(strip_tags($post->content), 100),
                        'user' => [
                            'name' => $post->user->name,
                            'username' => $post->user->username
                        ],
                        'thread' => [
                            'title' => $post->thread->title,
                            'url' => route('threads.show', $post->thread)
                        ],
                        'url' => route('threads.show', $post->thread_id) . '#post-' . $post->id
                    ];
                });

            $results['threads'] = $threads;
            $results['posts'] = $posts;
        }

        return $results;
    }

    /**
     * Format Elasticsearch results for AJAX response
     */
    private function formatAjaxResults(array $results): array
    {
        $formatted = [];

        if (isset($results['threads'])) {
            $formatted['threads'] = collect($results['threads'])->map(function ($thread) {
                return [
                    'id' => $thread['id'],
                    'title' => $thread['title'],
                    'content' => Str::limit(strip_tags($thread['content'] ?? ''), 100),
                    'user' => [
                        'name' => $thread['user_name'],
                        'username' => $thread['user_name'] // Assuming username same as name for now
                    ],
                    'forum' => [
                        'name' => $thread['forum_name'],
                        'url' => '#' // Will be populated by frontend
                    ],
                    'url' => route('threads.show', $thread['id'])
                ];
            });
        }

        if (isset($results['posts'])) {
            $formatted['posts'] = collect($results['posts'])->map(function ($post) {
                return [
                    'id' => $post['id'],
                    'content' => Str::limit(strip_tags($post['content'] ?? ''), 100),
                    'user' => [
                        'name' => $post['user_name'],
                        'username' => $post['user_name']
                    ],
                    'thread' => [
                        'title' => $post['thread_title'] ?? '',
                        'url' => route('threads.show', $post['thread_id'] ?? 0)
                    ],
                    'url' => route('threads.show', $post['thread_id'] ?? 0) . '#post-' . $post['id']
                ];
            });
        }

        return $formatted;
    }

    /**
     * Enhanced search logging (merged from SearchController)
     */
    private function logSearch(
        string $query,
        Request $request,
        int $resultsCount,
        float $startTime,
        array $filters = []
    ): void {
        try {
            $responseTime = round((microtime(true) - $startTime) * 1000);

            SearchLog::create([
                'query' => $query,
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'results_count' => $resultsCount,
                'response_time_ms' => $responseTime,
                'filters' => $filters,
                'content_type' => $filters['content_type'] ?? 'advanced',
                'search_method' => $filters['method'] ?? 'elasticsearch',
                'created_at' => now()
            ]);

            // Track search for analytics
            $this->trackSearch($query, $request);

        } catch (\Exception $e) {
            Log::error('Failed to log search activity: ' . $e->getMessage(), [
                'query' => $query,
                'user_id' => Auth::check() ? Auth::id() : null,
                'results_count' => $resultsCount
            ]);
        }
    }
}
