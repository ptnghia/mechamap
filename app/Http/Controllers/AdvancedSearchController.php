<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use App\Services\AutoCompleteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        // Search in products
        $products = \App\Models\MarketplaceProduct::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with(['user', 'category'])
            ->limit(10)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'id' => $product->id,
                'title' => $product->name,
                'content' => substr(strip_tags($product->description), 0, 200),
                'url' => route('marketplace.products.show', $product),
                'user' => $product->user->name,
                'price' => $product->price,
                'created_at' => $product->created_at,
                'score' => $this->calculateRelevanceScore($query, $product->name . ' ' . $product->description),
            ];
        }

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
}
