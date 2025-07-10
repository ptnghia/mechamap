<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification A/B Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for A/B testing notification effectiveness in MechaMap
    |
    */

    'enabled' => env('NOTIFICATION_AB_TESTING_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Default Test Configuration
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'test_duration_days' => env('AB_TEST_DURATION_DAYS', 7),
        'min_sample_size' => env('AB_TEST_MIN_SAMPLE_SIZE', 100),
        'confidence_level' => env('AB_TEST_CONFIDENCE_LEVEL', 95),
        'statistical_significance' => env('AB_TEST_STATISTICAL_SIGNIFICANCE', 0.05),
        'auto_conclude' => env('AB_TEST_AUTO_CONCLUDE', true),
        'traffic_split' => [
            'control' => 50,
            'variant' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Test Types
    |--------------------------------------------------------------------------
    */
    'test_types' => [
        'title' => [
            'name' => 'Title Testing',
            'description' => 'Test different notification titles',
            'metrics' => ['open_rate', 'click_rate', 'engagement_score'],
        ],
        'message' => [
            'name' => 'Message Content Testing',
            'description' => 'Test different notification message content',
            'metrics' => ['open_rate', 'click_rate', 'time_to_read'],
        ],
        'timing' => [
            'name' => 'Send Timing Testing',
            'description' => 'Test different send times',
            'metrics' => ['open_rate', 'response_time', 'engagement_score'],
        ],
        'priority' => [
            'name' => 'Priority Level Testing',
            'description' => 'Test different priority levels',
            'metrics' => ['open_rate', 'click_rate', 'dismiss_rate'],
        ],
        'template' => [
            'name' => 'Template Design Testing',
            'description' => 'Test different email/UI templates',
            'metrics' => ['open_rate', 'click_rate', 'conversion_rate'],
        ],
        'frequency' => [
            'name' => 'Frequency Testing',
            'description' => 'Test different notification frequencies',
            'metrics' => ['engagement_score', 'unsubscribe_rate', 'satisfaction_score'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Configuration
    |--------------------------------------------------------------------------
    */
    'metrics' => [
        'open_rate' => [
            'name' => 'Open Rate',
            'description' => 'Percentage of notifications opened',
            'calculation' => 'opened / sent * 100',
            'higher_is_better' => true,
            'format' => 'percentage',
        ],
        'click_rate' => [
            'name' => 'Click Rate',
            'description' => 'Percentage of notifications clicked',
            'calculation' => 'clicked / opened * 100',
            'higher_is_better' => true,
            'format' => 'percentage',
        ],
        'engagement_score' => [
            'name' => 'Engagement Score',
            'description' => 'Overall engagement score (0-100)',
            'calculation' => 'weighted_engagement_actions',
            'higher_is_better' => true,
            'format' => 'score',
        ],
        'time_to_read' => [
            'name' => 'Time to Read',
            'description' => 'Average time from send to read (minutes)',
            'calculation' => 'avg(read_time - send_time)',
            'higher_is_better' => false,
            'format' => 'minutes',
        ],
        'response_time' => [
            'name' => 'Response Time',
            'description' => 'Average time from send to action (minutes)',
            'calculation' => 'avg(action_time - send_time)',
            'higher_is_better' => false,
            'format' => 'minutes',
        ],
        'dismiss_rate' => [
            'name' => 'Dismiss Rate',
            'description' => 'Percentage of notifications dismissed',
            'calculation' => 'dismissed / sent * 100',
            'higher_is_better' => false,
            'format' => 'percentage',
        ],
        'conversion_rate' => [
            'name' => 'Conversion Rate',
            'description' => 'Percentage leading to desired action',
            'calculation' => 'conversions / sent * 100',
            'higher_is_better' => true,
            'format' => 'percentage',
        ],
        'unsubscribe_rate' => [
            'name' => 'Unsubscribe Rate',
            'description' => 'Percentage leading to unsubscribe',
            'calculation' => 'unsubscribes / sent * 100',
            'higher_is_better' => false,
            'format' => 'percentage',
        ],
        'satisfaction_score' => [
            'name' => 'Satisfaction Score',
            'description' => 'User satisfaction rating (1-5)',
            'calculation' => 'avg(satisfaction_ratings)',
            'higher_is_better' => true,
            'format' => 'rating',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Segmentation
    |--------------------------------------------------------------------------
    */
    'segmentation' => [
        'enabled' => env('AB_TEST_SEGMENTATION_ENABLED', true),
        'segments' => [
            'user_role' => [
                'admin', 'moderator', 'member', 'guest',
                'supplier', 'manufacturer', 'brand'
            ],
            'activity_level' => [
                'high', 'medium', 'low', 'inactive'
            ],
            'registration_date' => [
                'new' => '< 30 days',
                'recent' => '30-90 days',
                'established' => '90-365 days',
                'veteran' => '> 365 days'
            ],
            'engagement_history' => [
                'highly_engaged', 'moderately_engaged', 
                'low_engaged', 'never_engaged'
            ],
        ],
        'exclude_segments' => [
            'test_users', 'admin_users', 'inactive_users'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statistical Analysis
    |--------------------------------------------------------------------------
    */
    'statistics' => [
        'methods' => [
            'chi_square' => [
                'name' => 'Chi-Square Test',
                'applicable_metrics' => ['open_rate', 'click_rate', 'conversion_rate'],
                'min_sample_size' => 30,
            ],
            't_test' => [
                'name' => 'T-Test',
                'applicable_metrics' => ['engagement_score', 'time_to_read', 'satisfaction_score'],
                'min_sample_size' => 30,
            ],
            'mann_whitney' => [
                'name' => 'Mann-Whitney U Test',
                'applicable_metrics' => ['response_time', 'engagement_score'],
                'min_sample_size' => 20,
            ],
        ],
        'effect_size' => [
            'small' => 0.2,
            'medium' => 0.5,
            'large' => 0.8,
        ],
        'power_analysis' => [
            'desired_power' => 0.8,
            'alpha' => 0.05,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Configuration
    |--------------------------------------------------------------------------
    */
    'reporting' => [
        'daily_reports' => env('AB_TEST_DAILY_REPORTS', true),
        'weekly_summaries' => env('AB_TEST_WEEKLY_SUMMARIES', true),
        'real_time_monitoring' => env('AB_TEST_REAL_TIME_MONITORING', true),
        'export_formats' => ['csv', 'json', 'pdf'],
        'dashboard_refresh_interval' => env('AB_TEST_DASHBOARD_REFRESH', 300), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Safety & Ethics
    |--------------------------------------------------------------------------
    */
    'safety' => [
        'max_concurrent_tests' => env('AB_TEST_MAX_CONCURRENT', 3),
        'cooldown_period_days' => env('AB_TEST_COOLDOWN_DAYS', 1),
        'user_consent_required' => env('AB_TEST_USER_CONSENT', false),
        'data_retention_days' => env('AB_TEST_DATA_RETENTION', 90),
        'exclude_critical_notifications' => true,
        'emergency_stop_enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'analytics_service' => env('AB_TEST_ANALYTICS_INTEGRATION', true),
        'engagement_tracking' => env('AB_TEST_ENGAGEMENT_INTEGRATION', true),
        'email_service' => env('AB_TEST_EMAIL_INTEGRATION', true),
        'webhook_notifications' => env('AB_TEST_WEBHOOK_NOTIFICATIONS', false),
        'slack_alerts' => env('AB_TEST_SLACK_ALERTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('AB_TEST_CACHE_ENABLED', true),
        'ttl' => env('AB_TEST_CACHE_TTL', 3600), // seconds
        'prefix' => env('AB_TEST_CACHE_PREFIX', 'ab_test_'),
        'store' => env('AB_TEST_CACHE_STORE', 'redis'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Types for Testing
    |--------------------------------------------------------------------------
    */
    'testable_notification_types' => [
        'thread_created',
        'thread_replied',
        'comment_mention',
        'product_out_of_stock',
        'order_status_changed',
        'review_received',
        'wishlist_available',
        'seller_message',
        // Exclude critical security notifications
        // 'login_from_new_device',
        // 'password_changed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sample Test Configurations
    |--------------------------------------------------------------------------
    */
    'sample_tests' => [
        'thread_notification_title' => [
            'name' => 'Thread Notification Title Test',
            'type' => 'title',
            'notification_type' => 'thread_created',
            'variants' => [
                'control' => 'New thread in {{forum_name}}',
                'variant_a' => '🔥 Hot discussion started in {{forum_name}}',
                'variant_b' => 'Join the conversation: {{thread_title}}',
            ],
            'target_metrics' => ['open_rate', 'click_rate'],
            'duration_days' => 7,
            'traffic_split' => ['control' => 34, 'variant_a' => 33, 'variant_b' => 33],
        ],
        'product_alert_timing' => [
            'name' => 'Product Alert Timing Test',
            'type' => 'timing',
            'notification_type' => 'product_out_of_stock',
            'variants' => [
                'control' => 'immediate',
                'variant_a' => 'delay_1_hour',
                'variant_b' => 'delay_4_hours',
            ],
            'target_metrics' => ['open_rate', 'response_time'],
            'duration_days' => 14,
        ],
    ],
];
