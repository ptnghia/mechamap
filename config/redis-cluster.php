<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis Cluster Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Redis Cluster setup for high-availability caching
    | in MechaMap notification system.
    |
    */

    'enabled' => env('REDIS_CLUSTER_ENABLED', false),

    'clusters' => [
        'default' => [
            'nodes' => [
                [
                    'host' => env('REDIS_CLUSTER_NODE1_HOST', '127.0.0.1'),
                    'port' => env('REDIS_CLUSTER_NODE1_PORT', 7000),
                    'password' => env('REDIS_CLUSTER_NODE1_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_CLUSTER_NODE2_HOST', '127.0.0.1'),
                    'port' => env('REDIS_CLUSTER_NODE2_PORT', 7001),
                    'password' => env('REDIS_CLUSTER_NODE2_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_CLUSTER_NODE3_HOST', '127.0.0.1'),
                    'port' => env('REDIS_CLUSTER_NODE3_PORT', 7002),
                    'password' => env('REDIS_CLUSTER_NODE3_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_CLUSTER_NODE4_HOST', '127.0.0.1'),
                    'port' => env('REDIS_CLUSTER_NODE4_PORT', 7003),
                    'password' => env('REDIS_CLUSTER_NODE4_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_CLUSTER_NODE5_HOST', '127.0.0.1'),
                    'port' => env('REDIS_CLUSTER_NODE5_PORT', 7004),
                    'password' => env('REDIS_CLUSTER_NODE5_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_CLUSTER_NODE6_HOST', '127.0.0.1'),
                    'port' => env('REDIS_CLUSTER_NODE6_PORT', 7005),
                    'password' => env('REDIS_CLUSTER_NODE6_PASSWORD', null),
                ],
            ],
            'options' => [
                'cluster' => 'redis',
                'prefix' => env('REDIS_PREFIX', 'mechamap_'),
                'serializer' => 'igbinary',
                'compression' => 'lz4',
                'read_timeout' => 60,
                'timeout' => 5,
                'retry_interval' => 100,
                'persistent' => true,
            ],
        ],

        'notifications' => [
            'nodes' => [
                [
                    'host' => env('REDIS_NOTIFICATIONS_NODE1_HOST', '127.0.0.1'),
                    'port' => env('REDIS_NOTIFICATIONS_NODE1_PORT', 7010),
                    'password' => env('REDIS_NOTIFICATIONS_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_NOTIFICATIONS_NODE2_HOST', '127.0.0.1'),
                    'port' => env('REDIS_NOTIFICATIONS_NODE2_PORT', 7011),
                    'password' => env('REDIS_NOTIFICATIONS_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_NOTIFICATIONS_NODE3_HOST', '127.0.0.1'),
                    'port' => env('REDIS_NOTIFICATIONS_NODE3_PORT', 7012),
                    'password' => env('REDIS_NOTIFICATIONS_PASSWORD', null),
                ],
            ],
            'options' => [
                'cluster' => 'redis',
                'prefix' => env('REDIS_PREFIX', 'mechamap_') . 'notifications_',
                'serializer' => 'igbinary',
                'compression' => 'lz4',
                'read_timeout' => 30,
                'timeout' => 3,
                'retry_interval' => 50,
                'persistent' => true,
            ],
        ],

        'analytics' => [
            'nodes' => [
                [
                    'host' => env('REDIS_ANALYTICS_NODE1_HOST', '127.0.0.1'),
                    'port' => env('REDIS_ANALYTICS_NODE1_PORT', 7020),
                    'password' => env('REDIS_ANALYTICS_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_ANALYTICS_NODE2_HOST', '127.0.0.1'),
                    'port' => env('REDIS_ANALYTICS_NODE2_PORT', 7021),
                    'password' => env('REDIS_ANALYTICS_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_ANALYTICS_NODE3_HOST', '127.0.0.1'),
                    'port' => env('REDIS_ANALYTICS_NODE3_PORT', 7022),
                    'password' => env('REDIS_ANALYTICS_PASSWORD', null),
                ],
            ],
            'options' => [
                'cluster' => 'redis',
                'prefix' => env('REDIS_PREFIX', 'mechamap_') . 'analytics_',
                'serializer' => 'igbinary',
                'compression' => 'lz4',
                'read_timeout' => 60,
                'timeout' => 5,
                'retry_interval' => 100,
                'persistent' => true,
            ],
        ],

        'sessions' => [
            'nodes' => [
                [
                    'host' => env('REDIS_SESSIONS_NODE1_HOST', '127.0.0.1'),
                    'port' => env('REDIS_SESSIONS_NODE1_PORT', 7030),
                    'password' => env('REDIS_SESSIONS_PASSWORD', null),
                ],
                [
                    'host' => env('REDIS_SESSIONS_NODE2_HOST', '127.0.0.1'),
                    'port' => env('REDIS_SESSIONS_NODE2_PORT', 7031),
                    'password' => env('REDIS_SESSIONS_PASSWORD', null),
                ],
            ],
            'options' => [
                'cluster' => 'redis',
                'prefix' => env('REDIS_PREFIX', 'mechamap_') . 'sessions_',
                'serializer' => 'php',
                'read_timeout' => 30,
                'timeout' => 3,
                'retry_interval' => 50,
                'persistent' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failover Configuration
    |--------------------------------------------------------------------------
    */
    'failover' => [
        'enabled' => env('REDIS_FAILOVER_ENABLED', true),
        'fallback_to_single' => env('REDIS_FALLBACK_TO_SINGLE', true),
        'fallback_config' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', 6379),
            'password' => env('REDIS_PASSWORD', null),
            'database' => env('REDIS_DB', 0),
        ],
        'health_check_interval' => 30, // seconds
        'max_retry_attempts' => 3,
        'retry_delay' => 1000, // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'connection_pool_size' => env('REDIS_CONNECTION_POOL_SIZE', 10),
        'max_connections_per_node' => env('REDIS_MAX_CONNECTIONS_PER_NODE', 5),
        'pipeline_enabled' => env('REDIS_PIPELINE_ENABLED', true),
        'pipeline_batch_size' => env('REDIS_PIPELINE_BATCH_SIZE', 100),
        'compression_threshold' => env('REDIS_COMPRESSION_THRESHOLD', 1024), // bytes
        'ttl_optimization' => env('REDIS_TTL_OPTIMIZATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('REDIS_MONITORING_ENABLED', true),
        'metrics_collection' => env('REDIS_METRICS_COLLECTION', true),
        'slow_log_enabled' => env('REDIS_SLOW_LOG_ENABLED', true),
        'slow_log_threshold' => env('REDIS_SLOW_LOG_THRESHOLD', 10000), // microseconds
        'memory_usage_alerts' => env('REDIS_MEMORY_ALERTS', true),
        'memory_threshold' => env('REDIS_MEMORY_THRESHOLD', 80), // percentage
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Distribution Strategy
    |--------------------------------------------------------------------------
    */
    'distribution' => [
        'strategy' => env('REDIS_DISTRIBUTION_STRATEGY', 'consistent_hashing'),
        'hash_slots' => 16384,
        'replication_factor' => env('REDIS_REPLICATION_FACTOR', 1),
        'auto_failover' => env('REDIS_AUTO_FAILOVER', true),
        'read_from_slaves' => env('REDIS_READ_FROM_SLAVES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'auth_enabled' => env('REDIS_AUTH_ENABLED', true),
        'tls_enabled' => env('REDIS_TLS_ENABLED', false),
        'tls_cert_path' => env('REDIS_TLS_CERT_PATH', null),
        'tls_key_path' => env('REDIS_TLS_KEY_PATH', null),
        'tls_ca_path' => env('REDIS_TLS_CA_PATH', null),
        'acl_enabled' => env('REDIS_ACL_ENABLED', false),
        'acl_users' => [
            'notifications' => [
                'password' => env('REDIS_NOTIFICATIONS_ACL_PASSWORD', null),
                'commands' => ['get', 'set', 'del', 'exists', 'expire', 'ttl'],
                'keys' => ['notifications:*', 'engagement:*'],
            ],
            'analytics' => [
                'password' => env('REDIS_ANALYTICS_ACL_PASSWORD', null),
                'commands' => ['get', 'set', 'del', 'exists', 'expire', 'ttl', 'incr', 'decr'],
                'keys' => ['analytics:*', 'metrics:*'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup and Recovery
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('REDIS_BACKUP_ENABLED', true),
        'schedule' => env('REDIS_BACKUP_SCHEDULE', '0 2 * * *'), // Daily at 2 AM
        'retention_days' => env('REDIS_BACKUP_RETENTION', 7),
        'backup_path' => env('REDIS_BACKUP_PATH', storage_path('backups/redis')),
        'compression' => env('REDIS_BACKUP_COMPRESSION', true),
    ],
];
