<?php

/**
 * FIX BUSINESS SERVICES HARDCODED STRINGS
 * Thay thế các hardcoded strings còn lại trong business/services.blade.php
 */

echo "=== FIXING BUSINESS SERVICES HARDCODED STRINGS ===\n\n";

$businessFile = __DIR__ . "/resources/views/business/services.blade.php";

if (!file_exists($businessFile)) {
    echo "❌ File not found: $businessFile\n";
    exit(1);
}

$content = file_get_contents($businessFile);

// Replace hardcoded strings with translation keys
$replacements = [
    "{{ __('Premium listings appear at the top of search results and category pages, giving your business maximum visibility. They also include enhanced visual elements to make your listing stand out from the competition.') }}" => "{{ __('business.premium_listings_description') }}",
    "{{ __('Yes, you can cancel your subscription at any time. Your services will continue until the end of your current billing period.') }}" => "{{ __('business.cancel_subscription_description') }}",
    "{{ __('Yes, we offer custom enterprise packages for larger businesses with specific needs. Please contact our sales team to discuss your requirements and get a tailored solution.') }}" => "{{ __('business.custom_packages_description') }}",
];

$changedCount = 0;
foreach ($replacements as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedCount++;
        echo "✅ Replaced hardcoded string with translation key\n";
    }
}

if ($changedCount > 0) {
    if (file_put_contents($businessFile, $content)) {
        echo "✅ Updated $businessFile with $changedCount replacements\n";
    } else {
        echo "❌ Failed to write $businessFile\n";
    }
} else {
    echo "ℹ️  No hardcoded strings found to replace\n";
}

echo "\n✅ Business services hardcoded strings fix completed at " . date('Y-m-d H:i:s') . "\n";
?>
