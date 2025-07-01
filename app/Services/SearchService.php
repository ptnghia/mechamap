<?php

namespace App\Services;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Models\Thread;
use App\Models\MarketplaceProduct;
use App\Models\User;
use App\Models\TechnicalDrawing;

/**
 * Search Service
 * Handles Elasticsearch integration and search functionality
 */
class SearchService
{
    protected $client;
    protected $indexPrefix;
    protected $isElasticsearchAvailable;

    public function __construct()
    {
        $this->indexPrefix = config('elasticsearch.index_prefix', 'mechamap');
        $this->initializeElasticsearch();
    }

    /**
     * Initialize Elasticsearch client
     */
    private function initializeElasticsearch(): void
    {
        try {
            // Temporarily disable Elasticsearch for testing
            $this->client = null;
            return;

            $hosts = config('elasticsearch.hosts', ['localhost:9200']);

            $this->client = ClientBuilder::create()
                ->setHosts($hosts)
                ->setRetries(2)
                ->build();

            // Test connection
            $this->client->ping();
            $this->isElasticsearchAvailable = true;

            Log::info('Elasticsearch connection established');

        } catch (\Exception $e) {
            $this->isElasticsearchAvailable = false;
            Log::warning('Elasticsearch not available: ' . $e->getMessage());
        }
    }

    /**
     * Perform advanced search
     */
    public function search(string $query, array $filters = [], array $options = []): array
    {
        if (!$this->isElasticsearchAvailable) {
            throw new \Exception('Elasticsearch service not available');
        }

        $startTime = microtime(true);

        try {
            $searchParams = $this->buildSearchParams($query, $filters, $options);
            $response = $this->client->search($searchParams);

            $results = $this->formatSearchResults($response, $options);
            $results['meta']['search_time'] = round((microtime(true) - $startTime) * 1000, 2);

            return $results;

        } catch (\Exception $e) {
            Log::error('Elasticsearch search error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build Elasticsearch search parameters
     */
    private function buildSearchParams(string $query, array $filters, array $options): array
    {
        $page = $options['page'] ?? 1;
        $perPage = $options['per_page'] ?? 20;
        $sort = $options['sort'] ?? 'relevance';

        $searchParams = [
            'index' => $this->getSearchIndices($filters),
            'body' => [
                'query' => $this->buildQuery($query, $filters),
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'sort' => $this->buildSort($sort),
                'highlight' => $this->buildHighlight(),
            ]
        ];

        // Add aggregations for facets
        if ($options['include_facets'] ?? false) {
            $searchParams['body']['aggs'] = $this->buildAggregations();
        }

        // Add suggestions
        if ($options['include_suggestions'] ?? false) {
            $searchParams['body']['suggest'] = $this->buildSuggestions($query);
        }

        return $searchParams;
    }

    /**
     * Build Elasticsearch query
     */
    private function buildQuery(string $query, array $filters): array
    {
        $must = [];
        $filter = [];

        // Main search query
        if (!empty($query)) {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => [
                        'title^3',
                        'content^2',
                        'description^2',
                        'tags^1.5',
                        'category^1.5',
                        'author^1',
                    ],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                    'operator' => 'and',
                ]
            ];
        } else {
            $must[] = ['match_all' => new \stdClass()];
        }

        // Apply filters
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $filter[] = ['terms' => [$field => $value]];
            } else {
                $filter[] = ['term' => [$field => $value]];
            }
        }

        // Date range filters
        if (isset($filters['date_from']) || isset($filters['date_to'])) {
            $dateRange = [];
            if (isset($filters['date_from'])) {
                $dateRange['gte'] = $filters['date_from'];
            }
            if (isset($filters['date_to'])) {
                $dateRange['lte'] = $filters['date_to'];
            }
            $filter[] = ['range' => ['created_at' => $dateRange]];
        }

        // Price range filters
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $priceRange = [];
            if (isset($filters['price_min'])) {
                $priceRange['gte'] = $filters['price_min'];
            }
            if (isset($filters['price_max'])) {
                $priceRange['lte'] = $filters['price_max'];
            }
            $filter[] = ['range' => ['price' => $priceRange]];
        }

        $queryBody = [
            'bool' => [
                'must' => $must,
            ]
        ];

        if (!empty($filter)) {
            $queryBody['bool']['filter'] = $filter;
        }

        return $queryBody;
    }

    /**
     * Build sort parameters
     */
    private function buildSort(string $sort): array
    {
        switch ($sort) {
            case 'date':
                return [['created_at' => ['order' => 'desc']]];
            case 'popularity':
                return [['view_count' => ['order' => 'desc']], ['_score' => ['order' => 'desc']]];
            case 'price_asc':
                return [['price' => ['order' => 'asc']]];
            case 'price_desc':
                return [['price' => ['order' => 'desc']]];
            case 'relevance':
            default:
                return [['_score' => ['order' => 'desc']]];
        }
    }

    /**
     * Build highlight configuration
     */
    private function buildHighlight(): array
    {
        return [
            'fields' => [
                'title' => [
                    'fragment_size' => 150,
                    'number_of_fragments' => 1,
                ],
                'content' => [
                    'fragment_size' => 200,
                    'number_of_fragments' => 2,
                ],
                'description' => [
                    'fragment_size' => 150,
                    'number_of_fragments' => 1,
                ],
            ],
            'pre_tags' => ['<mark>'],
            'post_tags' => ['</mark>'],
        ];
    }

    /**
     * Build aggregations for faceted search
     */
    private function buildAggregations(): array
    {
        return [
            'categories' => [
                'terms' => [
                    'field' => 'category.keyword',
                    'size' => 20,
                ]
            ],
            'content_types' => [
                'terms' => [
                    'field' => 'content_type.keyword',
                    'size' => 10,
                ]
            ],
            'authors' => [
                'terms' => [
                    'field' => 'author.keyword',
                    'size' => 15,
                ]
            ],
            'tags' => [
                'terms' => [
                    'field' => 'tags.keyword',
                    'size' => 30,
                ]
            ],
            'price_ranges' => [
                'range' => [
                    'field' => 'price',
                    'ranges' => [
                        ['to' => 100000],
                        ['from' => 100000, 'to' => 500000],
                        ['from' => 500000, 'to' => 1000000],
                        ['from' => 1000000, 'to' => 5000000],
                        ['from' => 5000000],
                    ]
                ]
            ],
            'date_ranges' => [
                'date_range' => [
                    'field' => 'created_at',
                    'ranges' => [
                        ['from' => 'now-1d', 'to' => 'now', 'key' => 'last_day'],
                        ['from' => 'now-7d', 'to' => 'now', 'key' => 'last_week'],
                        ['from' => 'now-30d', 'to' => 'now', 'key' => 'last_month'],
                        ['from' => 'now-1y', 'to' => 'now', 'key' => 'last_year'],
                    ]
                ]
            ],
        ];
    }

    /**
     * Build suggestions configuration
     */
    private function buildSuggestions(string $query): array
    {
        return [
            'text' => $query,
            'title_suggest' => [
                'term' => [
                    'field' => 'title',
                    'size' => 5,
                ]
            ],
            'content_suggest' => [
                'phrase' => [
                    'field' => 'content',
                    'size' => 3,
                    'gram_size' => 2,
                    'direct_generator' => [
                        [
                            'field' => 'content',
                            'suggest_mode' => 'missing',
                        ]
                    ],
                ]
            ],
        ];
    }

    /**
     * Format search results
     */
    private function formatSearchResults(array $response, array $options): array
    {
        $results = [];
        $total = $response['hits']['total']['value'] ?? 0;

        foreach ($response['hits']['hits'] as $hit) {
            $source = $hit['_source'];
            $highlight = $hit['highlight'] ?? [];

            $result = [
                'id' => $source['id'],
                'type' => $source['content_type'],
                'title' => $source['title'],
                'content' => $source['content'] ?? $source['description'] ?? '',
                'url' => $this->generateUrl($source),
                'author' => $source['author'] ?? '',
                'category' => $source['category'] ?? '',
                'tags' => $source['tags'] ?? [],
                'created_at' => $source['created_at'],
                'score' => $hit['_score'],
            ];

            // Add type-specific fields
            if ($source['content_type'] === 'product') {
                $result['price'] = $source['price'] ?? 0;
                $result['currency'] = $source['currency'] ?? 'VND';
                $result['image'] = $source['image'] ?? null;
            }

            // Add highlights
            if (!empty($highlight)) {
                $result['highlights'] = $highlight;
            }

            $results[] = $result;
        }

        $formatted = [
            'results' => $results,
            'total' => $total,
            'meta' => [
                'page' => ($options['page'] ?? 1),
                'per_page' => ($options['per_page'] ?? 20),
                'total_pages' => ceil($total / ($options['per_page'] ?? 20)),
            ],
        ];

        // Add facets
        if (isset($response['aggregations'])) {
            $formatted['facets'] = $this->formatFacets($response['aggregations']);
        }

        // Add suggestions
        if (isset($response['suggest'])) {
            $formatted['suggestions'] = $this->formatSuggestions($response['suggest']);
        }

        return $formatted;
    }

    /**
     * Format facets from aggregations
     */
    private function formatFacets(array $aggregations): array
    {
        $facets = [];

        foreach ($aggregations as $name => $agg) {
            if (isset($agg['buckets'])) {
                $facets[$name] = [
                    'name' => $this->getFacetDisplayName($name),
                    'buckets' => array_map(function ($bucket) {
                        return [
                            'key' => $bucket['key'],
                            'count' => $bucket['doc_count'],
                        ];
                    }, $agg['buckets'])
                ];
            }
        }

        return $facets;
    }

    /**
     * Format suggestions
     */
    private function formatSuggestions(array $suggest): array
    {
        $suggestions = [];

        foreach ($suggest as $name => $suggestionGroup) {
            foreach ($suggestionGroup as $suggestion) {
                if (!empty($suggestion['options'])) {
                    foreach ($suggestion['options'] as $option) {
                        $suggestions[] = [
                            'text' => $option['text'],
                            'score' => $option['score'],
                            'type' => $name,
                        ];
                    }
                }
            }
        }

        // Sort by score and remove duplicates
        usort($suggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice(array_unique($suggestions, SORT_REGULAR), 0, 10);
    }

    /**
     * Get search indices based on filters
     */
    private function getSearchIndices(array $filters): string
    {
        $indices = [];

        if (empty($filters['content_type']) || in_array('thread', $filters['content_type'])) {
            $indices[] = $this->indexPrefix . '_threads';
        }
        if (empty($filters['content_type']) || in_array('product', $filters['content_type'])) {
            $indices[] = $this->indexPrefix . '_products';
        }
        if (empty($filters['content_type']) || in_array('user', $filters['content_type'])) {
            $indices[] = $this->indexPrefix . '_users';
        }
        if (empty($filters['content_type']) || in_array('document', $filters['content_type'])) {
            $indices[] = $this->indexPrefix . '_documents';
        }

        return implode(',', $indices ?: [$this->indexPrefix . '_*']);
    }

    /**
     * Generate URL for search result
     */
    private function generateUrl(array $source): string
    {
        switch ($source['content_type']) {
            case 'thread':
                return route('threads.show', $source['id']);
            case 'product':
                return route('marketplace.products.show', $source['id']);
            case 'user':
                return route('users.show', $source['id']);
            case 'document':
                return route('documents.show', $source['id']);
            default:
                return '#';
        }
    }

    /**
     * Get facet display name
     */
    private function getFacetDisplayName(string $facetName): string
    {
        $names = [
            'categories' => 'Danh mục',
            'content_types' => 'Loại nội dung',
            'authors' => 'Tác giả',
            'tags' => 'Thẻ',
            'price_ranges' => 'Khoảng giá',
            'date_ranges' => 'Thời gian',
        ];

        return $names[$facetName] ?? ucfirst(str_replace('_', ' ', $facetName));
    }

    /**
     * Get spelling suggestions
     */
    public function getSpellingSuggestions(string $query): array
    {
        if (!$this->isElasticsearchAvailable) {
            return [];
        }

        try {
            $params = [
                'index' => $this->indexPrefix . '_*',
                'body' => [
                    'suggest' => [
                        'text' => $query,
                        'spell_check' => [
                            'term' => [
                                'field' => 'title',
                                'size' => 5,
                                'sort' => 'frequency',
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->client->search($params);
            return $this->formatSuggestions($response['suggest'] ?? []);

        } catch (\Exception $e) {
            Log::error('Spelling suggestions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get search analytics
     */
    public function getSearchAnalytics(string $period): array
    {
        // This would typically query your search logs
        // For now, return mock data
        return [
            'total_searches' => 12543,
            'unique_queries' => 8921,
            'avg_results_per_search' => 15.7,
            'top_queries' => [
                'máy tiện CNC' => 234,
                'động cơ servo' => 189,
                'bearing SKF' => 156,
                'thép không gỉ' => 134,
                'máy phay' => 123,
            ],
            'no_results_queries' => [
                'máy laser cắt kim loại' => 23,
                'robot hàn tự động' => 18,
                'sensor nhiệt độ công nghiệp' => 15,
            ],
            'search_trends' => [
                // Time series data
            ],
        ];
    }

    /**
     * Get facets for current search context
     */
    public function getFacets(string $query, array $filters): array
    {
        if (!$this->isElasticsearchAvailable) {
            return [];
        }

        try {
            $params = [
                'index' => $this->getSearchIndices($filters),
                'body' => [
                    'query' => $this->buildQuery($query, $filters),
                    'size' => 0, // We only want aggregations
                    'aggs' => $this->buildAggregations(),
                ]
            ];

            $response = $this->client->search($params);
            return $this->formatFacets($response['aggregations'] ?? []);

        } catch (\Exception $e) {
            Log::error('Facets error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if Elasticsearch is available
     */
    public function isAvailable(): bool
    {
        return $this->isElasticsearchAvailable;
    }
}
