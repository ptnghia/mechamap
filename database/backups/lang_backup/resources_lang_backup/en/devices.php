<?php

/**
 * Devices Translation File - English
 * Translation keys for device management functionality
 * Auto-generated: <?= date('Y-m-d H:i:s') ?>

 */

return [
    // Page titles and headers
    'title' => 'Device Management',
    'subtitle' => 'View and manage devices that have logged into your account',
    'clean_old_devices' => 'Clean Old Devices',
    
    // Action buttons and tooltips
    'actions' => [
        'trust' => 'Mark as trusted',
        'untrust' => 'Remove trust',
        'remove' => 'Remove device',
    ],
    
    // Status labels
    'status' => [
        'trusted' => 'Trusted',
        'untrusted' => 'Untrusted',
    ],
    
    // Confirmation messages
    'confirmations' => [
        'untrust_device' => 'Remove trust from this device?',
        'clean_old_devices' => 'Delete all untrusted and inactive devices from the past 90 days?',
    ],
    
    // Error messages
    'errors' => [
        'load_failed' => 'Unable to load device information',
    ],
];
