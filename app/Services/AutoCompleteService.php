<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Thread;
use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Models\Category;

/**
 * AutoComplete Service
 * Provides intelligent auto-complete suggestions for search
 */
class AutoCompleteService
{
    protected $searchService;
    protected $cachePrefix = 'autocomplete';
    protected $cacheTtl = 3600; // 1 hour

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Get auto-complete suggestions
     */
    public function getSuggestions(string $query, string $type = 'all', int $limit = 10): array
    {
        $query = trim(strtolower($query));
        
        if (strlen($query) < 2) {
            return [];
        }

        $cacheKey = $this->getCacheKey($query, $type, $limit);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $type, $limit) {
            return $this->generateSuggestions($query, $type, $limit);
        });
    }

    /**
     * Generate suggestions based on query and type
     */
    private function generateSuggestions(string $query, string $type, int $limit): array
    {
        $suggestions = [];

        try {
            // Try Elasticsearch first if available
            if ($this->searchService->isAvailable()) {
                $suggestions = $this->getElasticsearchSuggestions($query, $type, $limit);
            }
            
            // Fallback to database if Elasticsearch not available or no results
            if (empty($suggestions)) {
                $suggestions = $this->getDatabaseSuggestions($query, $type, $limit);
            }

            // Add popular searches and trending terms
            $suggestions = array_merge(
                $suggestions,
                $this->getPopularSuggestions($query, $limit - count($suggestions))
            );

            // Remove duplicates and limit results
            $suggestions = $this->deduplicateAndLimit($suggestions, $limit);

            // Add metadata and scoring
            $suggestions = $this->enhanceSuggestions($suggestions, $query);

        } catch (\Exception $e) {
            Log::error('AutoComplete error: ' . $e->getMessage());
            $suggestions = $this->getDatabaseSuggestions($query, $type, $limit);
        }

        return $suggestions;
    }

    /**
     * Get suggestions from Elasticsearch
     */
    private function getElasticsearchSuggestions(string $query, string $type, int $limit): array
    {
        // This would use Elasticsearch completion suggester
        // For now, return empty array as fallback
        return [];
    }

    /**
     * Get suggestions from database
     */
    private function getDatabaseSuggestions(string $query, string $type, int $limit): array
    {
        $suggestions = [];

        if ($type === 'all' || $type === 'threads') {
            $suggestions = array_merge($suggestions, $this->getThreadSuggestions($query, $limit));
        }

        if ($type === 'all' || $type === 'products') {
            $suggestions = array_merge($suggestions, $this->getProductSuggestions($query, $limit));
        }

        if ($type === 'all' || $type === 'users') {
            $suggestions = array_merge($suggestions, $this->getUserSuggestions($query, $limit));
        }

        if ($type === 'all' || $type === 'categories') {
            $suggestions = array_merge($suggestions, $this->getCategorySuggestions($query, $limit));
        }

        return $suggestions;
    }

    /**
     * Get thread title suggestions
     */
    private function getThreadSuggestions(string $query, int $limit): array
    {
        $threads = Thread::where('title', 'LIKE', "{$query}%")
            ->orWhere('title', 'LIKE', "%{$query}%")
            ->select('title', 'view_count', 'created_at')
            ->orderByRaw("
                CASE 
                    WHEN title LIKE '{$query}%' THEN 1 
                    ELSE 2 
                END, view_count DESC
            ")
            ->limit($limit)
            ->get();

        return $threads->map(function ($thread) {
            return [
                'text' => $thread->title,
                'type' => 'thread',
                'category' => 'Thảo luận',
                'icon' => 'fas fa-comments',
                'popularity' => $thread->view_count,
                'date' => $thread->created_at,
            ];
        })->toArray();
    }

    /**
     * Get product name suggestions
     */
    private function getProductSuggestions(string $query, int $limit): array
    {
        $products = MarketplaceProduct::where('name', 'LIKE', "{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->select('name', 'price', 'view_count', 'created_at')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE '{$query}%' THEN 1 
                    ELSE 2 
                END, view_count DESC
            ")
            ->limit($limit)
            ->get();

        return $products->map(function ($product) {
            return [
                'text' => $product->name,
                'type' => 'product',
                'category' => 'Sản phẩm',
                'icon' => 'fas fa-shopping-cart',
                'popularity' => $product->view_count,
                'price' => $product->price,
                'date' => $product->created_at,
            ];
        })->toArray();
    }

    /**
     * Get user suggestions
     */
    private function getUserSuggestions(string $query, int $limit): array
    {
        $users = User::where('name', 'LIKE', "{$query}%")
            ->orWhere('username', 'LIKE', "{$query}%")
            ->select('name', 'username', 'avatar', 'created_at')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE '{$query}%' THEN 1 
                    WHEN username LIKE '{$query}%' THEN 2
                    ELSE 3 
                END
            ")
            ->limit($limit)
            ->get();

        return $users->map(function ($user) {
            return [
                'text' => $user->name,
                'type' => 'user',
                'category' => 'Người dùng',
                'icon' => 'fas fa-user',
                'username' => $user->username,
                'avatar' => $user->avatar,
                'date' => $user->created_at,
            ];
        })->toArray();
    }

    /**
     * Get category suggestions
     */
    private function getCategorySuggestions(string $query, int $limit): array
    {
        $categories = Category::where('name', 'LIKE', "{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->select('name', 'description', 'threads_count')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE '{$query}%' THEN 1 
                    ELSE 2 
                END, threads_count DESC
            ")
            ->limit($limit)
            ->get();

        return $categories->map(function ($category) {
            return [
                'text' => $category->name,
                'type' => 'category',
                'category' => 'Danh mục',
                'icon' => 'fas fa-folder',
                'description' => $category->description,
                'count' => $category->threads_count,
            ];
        })->toArray();
    }

    /**
     * Get popular search suggestions
     */
    private function getPopularSuggestions(string $query, int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $popularTerms = Cache::remember('popular_search_terms', 3600, function () {
            return [
                'máy tiện CNC',
                'động cơ servo',
                'bearing SKF',
                'thép không gỉ',
                'máy phay',
                'sensor áp suất',
                'motor giảm tốc',
                'van điện từ',
                'máy hàn',
                'dao phay',
                'ổ bi',
                'dầu thủy lực',
                'máy cắt laser',
                'robot công nghiệp',
                'PLC Siemens',
            ];
        });

        $suggestions = [];
        foreach ($popularTerms as $term) {
            if (stripos($term, $query) !== false) {
                $suggestions[] = [
                    'text' => $term,
                    'type' => 'popular',
                    'category' => 'Phổ biến',
                    'icon' => 'fas fa-fire',
                    'popularity' => 100,
                ];
            }
        }

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * Get trending suggestions
     */
    public function getTrendingSuggestions(int $limit = 10): array
    {
        return Cache::remember('trending_suggestions', 1800, function () use ($limit) {
            // Get trending terms from search logs
            $trending = DB::table('search_logs')
                ->select('query', DB::raw('COUNT(*) as search_count'))
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('query')
                ->orderBy('search_count', 'desc')
                ->limit($limit)
                ->get();

            return $trending->map(function ($item) {
                return [
                    'text' => $item->query,
                    'type' => 'trending',
                    'category' => 'Xu hướng',
                    'icon' => 'fas fa-trending-up',
                    'search_count' => $item->search_count,
                ];
            })->toArray();
        });
    }

    /**
     * Get recent user searches
     */
    public function getRecentSearches(int $userId, int $limit = 5): array
    {
        return Cache::remember("recent_searches_{$userId}", 1800, function () use ($userId, $limit) {
            $recent = DB::table('user_search_history')
                ->where('user_id', $userId)
                ->orderBy('last_searched_at', 'desc')
                ->limit($limit)
                ->pluck('query')
                ->toArray();

            return array_map(function ($query) {
                return [
                    'text' => $query,
                    'type' => 'recent',
                    'category' => 'Tìm kiếm gần đây',
                    'icon' => 'fas fa-history',
                ];
            }, $recent);
        });
    }

    /**
     * Get smart suggestions based on context
     */
    public function getSmartSuggestions(string $query, array $context = []): array
    {
        $suggestions = [];

        // Context-aware suggestions
        if (isset($context['category'])) {
            $suggestions = array_merge(
                $suggestions,
                $this->getCategorySpecificSuggestions($query, $context['category'])
            );
        }

        if (isset($context['user_role'])) {
            $suggestions = array_merge(
                $suggestions,
                $this->getRoleSpecificSuggestions($query, $context['user_role'])
            );
        }

        // Semantic suggestions (related terms)
        $suggestions = array_merge(
            $suggestions,
            $this->getSemanticSuggestions($query)
        );

        return $this->deduplicateAndLimit($suggestions, 10);
    }

    /**
     * Get category-specific suggestions
     */
    private function getCategorySpecificSuggestions(string $query, string $category): array
    {
        $categoryTerms = [
            'mechanical' => ['máy', 'động cơ', 'bearing', 'gear', 'shaft'],
            'electrical' => ['motor', 'sensor', 'PLC', 'inverter', 'cable'],
            'hydraulic' => ['pump', 'valve', 'cylinder', 'filter', 'oil'],
            'pneumatic' => ['air', 'compressor', 'actuator', 'regulator'],
        ];

        $terms = $categoryTerms[$category] ?? [];
        $suggestions = [];

        foreach ($terms as $term) {
            if (stripos($term, $query) !== false || stripos($query, $term) !== false) {
                $suggestions[] = [
                    'text' => $term,
                    'type' => 'category_specific',
                    'category' => 'Liên quan',
                    'icon' => 'fas fa-lightbulb',
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Get role-specific suggestions
     */
    private function getRoleSpecificSuggestions(string $query, string $role): array
    {
        $roleTerms = [
            'supplier' => ['wholesale', 'bulk', 'distributor', 'manufacturer'],
            'manufacturer' => ['production', 'assembly', 'quality', 'specification'],
            'engineer' => ['calculation', 'design', 'analysis', 'simulation'],
        ];

        $terms = $roleTerms[$role] ?? [];
        $suggestions = [];

        foreach ($terms as $term) {
            if (stripos($term, $query) !== false) {
                $suggestions[] = [
                    'text' => $term,
                    'type' => 'role_specific',
                    'category' => 'Chuyên ngành',
                    'icon' => 'fas fa-user-tie',
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Get semantic suggestions (related terms)
     */
    private function getSemanticSuggestions(string $query): array
    {
        $semanticMap = [
            'máy tiện' => ['CNC', 'lathe', 'turning', 'chuck', 'tool holder'],
            'động cơ' => ['motor', 'servo', 'stepper', 'AC', 'DC'],
            'bearing' => ['ổ bi', 'ball bearing', 'roller', 'thrust', 'pillow block'],
            'sensor' => ['cảm biến', 'proximity', 'temperature', 'pressure', 'flow'],
        ];

        $suggestions = [];
        foreach ($semanticMap as $key => $related) {
            if (stripos($key, $query) !== false || stripos($query, $key) !== false) {
                foreach ($related as $term) {
                    $suggestions[] = [
                        'text' => $term,
                        'type' => 'semantic',
                        'category' => 'Liên quan',
                        'icon' => 'fas fa-link',
                    ];
                }
                break;
            }
        }

        return $suggestions;
    }

    /**
     * Remove duplicates and limit results
     */
    private function deduplicateAndLimit(array $suggestions, int $limit): array
    {
        // Remove duplicates based on text
        $unique = [];
        $seen = [];

        foreach ($suggestions as $suggestion) {
            $key = strtolower($suggestion['text']);
            if (!isset($seen[$key])) {
                $unique[] = $suggestion;
                $seen[$key] = true;
            }
        }

        return array_slice($unique, 0, $limit);
    }

    /**
     * Enhance suggestions with additional metadata
     */
    private function enhanceSuggestions(array $suggestions, string $query): array
    {
        return array_map(function ($suggestion) use ($query) {
            // Calculate relevance score
            $suggestion['score'] = $this->calculateRelevanceScore($suggestion['text'], $query);
            
            // Add search URL
            $suggestion['url'] = route('search.advanced', ['q' => $suggestion['text']]);
            
            // Add formatted display text
            $suggestion['display'] = $this->highlightMatch($suggestion['text'], $query);
            
            return $suggestion;
        }, $suggestions);
    }

    /**
     * Calculate relevance score
     */
    private function calculateRelevanceScore(string $text, string $query): float
    {
        $text = strtolower($text);
        $query = strtolower($query);
        
        // Exact match gets highest score
        if ($text === $query) {
            return 1.0;
        }
        
        // Starts with query gets high score
        if (strpos($text, $query) === 0) {
            return 0.9;
        }
        
        // Contains query gets medium score
        if (strpos($text, $query) !== false) {
            return 0.7;
        }
        
        // Fuzzy match gets lower score
        $similarity = 0;
        similar_text($text, $query, $similarity);
        return $similarity / 100;
    }

    /**
     * Highlight matching text
     */
    private function highlightMatch(string $text, string $query): string
    {
        return preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark>$1</mark>',
            $text
        );
    }

    /**
     * Get cache key
     */
    private function getCacheKey(string $query, string $type, int $limit): string
    {
        return $this->cachePrefix . ':' . md5($query . $type . $limit);
    }

    /**
     * Clear autocomplete cache
     */
    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix])->flush();
    }

    /**
     * Warm up cache with popular terms
     */
    public function warmUpCache(): void
    {
        $popularTerms = [
            'máy', 'động', 'motor', 'bearing', 'sensor', 'valve', 'pump', 'gear'
        ];

        foreach ($popularTerms as $term) {
            $this->getSuggestions($term, 'all', 10);
        }
    }
}
