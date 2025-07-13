<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Elasticsearch integration in MechaMap
    |
    */

    'enabled' => env('ELASTICSEARCH_ENABLED', false),

    'hosts' => [
        env('ELASTICSEARCH_HOST', 'localhost:9200'),
    ],

    'retries' => env('ELASTICSEARCH_RETRIES', 2),

    'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', 'mechamap'),

    'indices' => [
        'threads' => [
            'name' => env('ELASTICSEARCH_INDEX_PREFIX', 'mechamap') . '_threads',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'vietnamese_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'asciifolding',
                                'stop',
                                'snowball'
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                    'content' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'user_id' => ['type' => 'integer'],
                    'user_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'category_id' => ['type' => 'integer'],
                    'category_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'forum_id' => ['type' => 'integer'],
                    'forum_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'tags' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'is_pinned' => ['type' => 'boolean'],
                    'is_locked' => ['type' => 'boolean'],
                    'is_featured' => ['type' => 'boolean'],
                    'views_count' => ['type' => 'integer'],
                    'replies_count' => ['type' => 'integer'],
                    'likes_count' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ]
            ]
        ],

        'showcases' => [
            'name' => env('ELASTICSEARCH_INDEX_PREFIX', 'mechamap') . '_showcases',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'vietnamese_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'asciifolding',
                                'stop',
                                'snowball'
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'content' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'user_id' => ['type' => 'integer'],
                    'user_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'category' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'complexity_level' => ['type' => 'keyword'],
                    'software_used' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'tags' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'is_featured' => ['type' => 'boolean'],
                    'is_public' => ['type' => 'boolean'],
                    'views_count' => ['type' => 'integer'],
                    'likes_count' => ['type' => 'integer'],
                    'rating_average' => ['type' => 'float'],
                    'rating_count' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ]
            ]
        ],

        'users' => [
            'name' => env('ELASTICSEARCH_INDEX_PREFIX', 'mechamap') . '_users',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'vietnamese_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'asciifolding',
                                'stop'
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                    'email' => ['type' => 'keyword'],
                    'username' => ['type' => 'keyword'],
                    'bio' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'company' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'location' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'skills' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'role' => ['type' => 'keyword'],
                    'is_verified' => ['type' => 'boolean'],
                    'is_active' => ['type' => 'boolean'],
                    'threads_count' => ['type' => 'integer'],
                    'showcases_count' => ['type' => 'integer'],
                    'reputation_score' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'last_active_at' => ['type' => 'date'],
                ]
            ]
        ],

        'products' => [
            'name' => env('ELASTICSEARCH_INDEX_PREFIX', 'mechamap') . '_products',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'vietnamese_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'asciifolding',
                                'stop'
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'category' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'type' => ['type' => 'keyword'],
                    'price' => ['type' => 'float'],
                    'currency' => ['type' => 'keyword'],
                    'seller_id' => ['type' => 'integer'],
                    'seller_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'tags' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer'
                    ],
                    'is_active' => ['type' => 'boolean'],
                    'is_featured' => ['type' => 'boolean'],
                    'views_count' => ['type' => 'integer'],
                    'sales_count' => ['type' => 'integer'],
                    'rating_average' => ['type' => 'float'],
                    'rating_count' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ]
            ]
        ]
    ],

    'search' => [
        'default_size' => 20,
        'max_size' => 100,
        'highlight' => [
            'pre_tags' => ['<mark>'],
            'post_tags' => ['</mark>'],
            'fields' => [
                'title' => ['fragment_size' => 150],
                'content' => ['fragment_size' => 200],
                'description' => ['fragment_size' => 200],
            ]
        ],
        'aggregations' => [
            'categories' => ['terms' => ['field' => 'category_name.keyword', 'size' => 20]],
            'users' => ['terms' => ['field' => 'user_name.keyword', 'size' => 10]],
            'complexity' => ['terms' => ['field' => 'complexity_level', 'size' => 5]],
            'price_ranges' => [
                'range' => [
                    'field' => 'price',
                    'ranges' => [
                        ['to' => 100000],
                        ['from' => 100000, 'to' => 500000],
                        ['from' => 500000, 'to' => 1000000],
                        ['from' => 1000000]
                    ]
                ]
            ]
        ]
    ]
];
