<?php

/**
 * English Translation Messages
 * 
 * ⚠️  IMPORTANT: MechaMap uses Database-Based Translations
 * 
 * MechaMap project has migrated to database-based translation system
 * instead of traditional PHP files. All translation keys are stored in 'translations' table.
 * 
 * Usage:
 * - Use helper functions: t_core(), t_ui(), t_content(), t_feature(), t_user()
 * - Or use __() function with key format: 'category.section.key'
 * 
 * Translation management:
 * - Access: https://mechamap.test/translations
 * - Command: php artisan translations:add-dashboard
 * 
 * Key structure:
 * - core.*     : Core functionality (auth, validation, errors)
 * - ui.*       : User interface (buttons, labels, navigation)
 * - content.*  : Page content (titles, descriptions, messages)
 * - feature.*  : Specific features (forum, marketplace, showcase)
 * - user.*     : User management (profile, settings, dashboard)
 * 
 * This file only contains basic keys for fallback purposes.
 */

return [
    // Fallback messages - only used when database is unavailable
    'fallback' => [
        'database_unavailable' => 'Translation system temporarily unavailable',
        'loading' => 'Loading...',
        'error' => 'An error occurred',
        'success' => 'Success',
        'warning' => 'Warning',
        'info' => 'Information',
    ],
    
    // Basic navigation - for emergency cases
    'nav' => [
        'home' => 'Home',
        'dashboard' => 'Dashboard',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
    ],
    
    // Basic actions
    'actions' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'view' => 'View',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'submit' => 'Submit',
        'confirm' => 'Confirm',
    ],
    
    // Basic status
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'draft' => 'Draft',
        'published' => 'Published',
    ],
    
    // Database translation info
    'database_info' => [
        'enabled' => true,
        'table' => 'translations',
        'cache_enabled' => true,
        'fallback_to_file' => true,
        'management_url' => '/translations',
    ],
];
