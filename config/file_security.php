<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for file upload security features
    | including virus scanning, file validation, and threat detection.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | File Upload Limits
    |--------------------------------------------------------------------------
    */
    'upload_limits' => [
        'max_files_per_hour' => env('FILE_UPLOAD_MAX_PER_HOUR', 20),
        'max_total_size_per_hour' => env('FILE_UPLOAD_MAX_SIZE_PER_HOUR', 500 * 1024 * 1024), // 500MB
        'max_file_size' => env('FILE_UPLOAD_MAX_SIZE', 100 * 1024 * 1024), // 100MB
        'max_files_per_request' => env('FILE_UPLOAD_MAX_PER_REQUEST', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    */
    'allowed_types' => [
        'images' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'max_size' => 10 * 1024 * 1024, // 10MB
        ],
        'documents' => [
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            'mime_types' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ],
            'max_size' => 50 * 1024 * 1024, // 50MB
        ],
        'cad_files' => [
            'extensions' => ['dwg', 'step', 'stp', 'iges', 'igs', 'stl', 'obj', 'sldprt', 'ipt', 'f3d'],
            'mime_types' => [
                'application/acad',
                'application/x-dwg',
                'image/vnd.dwg',
                'application/step',
                'application/iges',
                'application/sla',
                'model/obj',
            ],
            'max_size' => 100 * 1024 * 1024, // 100MB
        ],
        'archives' => [
            'extensions' => ['zip', 'rar', '7z'],
            'mime_types' => [
                'application/zip',
                'application/x-zip-compressed',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
            ],
            'max_size' => 50 * 1024 * 1024, // 50MB
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked File Types
    |--------------------------------------------------------------------------
    */
    'blocked_types' => [
        'extensions' => [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
            'php', 'php3', 'php4', 'php5', 'phtml', 'asp', 'aspx', 'jsp',
            'sh', 'bash', 'zsh', 'fish', 'ps1', 'psm1', 'psd1',
            'msi', 'deb', 'rpm', 'dmg', 'pkg', 'app',
            'htaccess', 'htpasswd', 'ini', 'conf', 'config',
            'sql', 'db', 'sqlite', 'sqlite3',
        ],
        'mime_types' => [
            'application/x-executable',
            'application/x-msdownload',
            'application/x-msdos-program',
            'application/x-winexe',
            'application/x-php',
            'text/x-php',
            'application/x-httpd-php',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Virus Scanning Configuration
    |--------------------------------------------------------------------------
    */
    'virus_scanning' => [
        'enabled' => env('VIRUS_SCANNING_ENABLED', true),
        'quarantine_enabled' => env('VIRUS_QUARANTINE_ENABLED', true),
        'quarantine_path' => env('VIRUS_QUARANTINE_PATH', 'quarantine'),
        'scan_timeout' => env('VIRUS_SCAN_TIMEOUT', 30), // seconds
        'threat_levels' => [
            'low' => 1,
            'medium' => 5,
            'high' => 7,
            'critical' => 9,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Validation
    |--------------------------------------------------------------------------
    */
    'content_validation' => [
        'enabled' => env('CONTENT_VALIDATION_ENABLED', true),
        'max_entropy' => env('CONTENT_MAX_ENTROPY', 7.5),
        'scan_depth' => env('CONTENT_SCAN_DEPTH', 8192), // bytes
        'metadata_scanning' => env('METADATA_SCANNING_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => env('RATE_LIMITING_ENABLED', true),
        'form_submissions_per_minute' => env('RATE_LIMIT_FORMS', 30),
        'file_uploads_per_minute' => env('RATE_LIMIT_UPLOADS', 10),
        'csrf_failures_before_block' => env('CSRF_FAILURES_LIMIT', 5),
        'block_duration' => env('RATE_LIMIT_BLOCK_DURATION', 3600), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Monitoring
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('SECURITY_MONITORING_ENABLED', true),
        'log_all_uploads' => env('LOG_ALL_UPLOADS', true),
        'log_failed_uploads' => env('LOG_FAILED_UPLOADS', true),
        'alert_on_threats' => env('ALERT_ON_THREATS', true),
        'alert_threshold' => env('ALERT_THRESHOLD', 'medium'),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage Security
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'use_private_disk' => env('USE_PRIVATE_DISK', true),
        'encrypt_files' => env('ENCRYPT_FILES', false),
        'generate_secure_names' => env('SECURE_FILENAMES', true),
        'preserve_original_names' => env('PRESERVE_ORIGINAL_NAMES', true),
        'backup_uploads' => env('BACKUP_UPLOADS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | CSRF Protection
    |--------------------------------------------------------------------------
    */
    'csrf' => [
        'enhanced_validation' => env('ENHANCED_CSRF_VALIDATION', true),
        'token_lifetime' => env('CSRF_TOKEN_LIFETIME', 7200), // 2 hours
        'validate_referrer' => env('CSRF_VALIDATE_REFERRER', true),
        'validate_user_agent' => env('CSRF_VALIDATE_USER_AGENT', true),
        'allowed_domains' => [
            'mechamap.test',
            'mechamap.com',
            'localhost',
            '127.0.0.1',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Malware Detection
    |--------------------------------------------------------------------------
    */
    'malware_detection' => [
        'enabled' => env('MALWARE_DETECTION_ENABLED', true),
        'signature_scanning' => env('SIGNATURE_SCANNING_ENABLED', true),
        'pattern_scanning' => env('PATTERN_SCANNING_ENABLED', true),
        'entropy_analysis' => env('ENTROPY_ANALYSIS_ENABLED', true),
        'structure_validation' => env('STRUCTURE_VALIDATION_ENABLED', true),
        'cache_scan_results' => env('CACHE_SCAN_RESULTS', true),
        'cache_duration' => env('SCAN_CACHE_DURATION', 86400), // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | File Reputation System
    |--------------------------------------------------------------------------
    */
    'reputation' => [
        'enabled' => env('FILE_REPUTATION_ENABLED', true),
        'check_known_hashes' => env('CHECK_KNOWN_HASHES', true),
        'update_reputation_db' => env('UPDATE_REPUTATION_DB', true),
        'reputation_sources' => [
            // Add external reputation services here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'notify_admins_on_threats' => env('NOTIFY_ADMINS_ON_THREATS', true),
        'notify_users_on_quarantine' => env('NOTIFY_USERS_ON_QUARANTINE', true),
        'email_security_reports' => env('EMAIL_SECURITY_REPORTS', false),
        'slack_webhook' => env('SECURITY_SLACK_WEBHOOK', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    */
    'development' => [
        'bypass_security_in_dev' => env('BYPASS_SECURITY_IN_DEV', false),
        'log_debug_info' => env('LOG_SECURITY_DEBUG', false),
        'test_mode' => env('SECURITY_TEST_MODE', false),
    ],
];
