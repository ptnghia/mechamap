#!/usr/bin/env php
<?php

/**
 * Test Asset URLs Configuration
 * Kiểm tra xem assets có đang sử dụng CDN hay local URLs
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 TESTING ASSET URLS CONFIGURATION\n";
echo "===================================\n\n";

// Test environment variables
echo "📋 Environment Variables:\n";
echo "-------------------------\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "CDN_URL: " . (env('CDN_URL') ?: 'Not set') . "\n";
echo "CDN_ENABLED: " . (env('CDN_ENABLED', false) ? 'true' : 'false') . "\n\n";

// Test config values
echo "⚙️ Configuration Values:\n";
echo "------------------------\n";
echo "app.url: " . config('app.url') . "\n";
echo "app.asset_url: " . (config('app.asset_url') ?: 'Not set') . "\n";
echo "production.domain.cdn: " . (config('production.domain.cdn') ?: 'Not set') . "\n\n";

// Test asset helper functions
echo "🔗 Asset Helper Functions:\n";
echo "--------------------------\n";

$testAssets = [
    'css/main.css',
    'js/app.js',
    'images/logo.png',
    'images/favicon.ico'
];

foreach ($testAssets as $assetPath) {
    $url = asset($assetPath);
    echo "asset('$assetPath'): $url\n";
    
    // Check if URL contains CDN
    if (strpos($url, 'cdn.mechamap.com') !== false) {
        echo "  ❌ WARNING: Using CDN URL!\n";
    } else {
        echo "  ✅ Using local URL\n";
    }
}

echo "\n";

// Test versioned assets if available
if (function_exists('asset_versioned')) {
    echo "📦 Versioned Asset Helper:\n";
    echo "-------------------------\n";
    
    foreach ($testAssets as $assetPath) {
        $url = asset_versioned($assetPath);
        echo "asset_versioned('$assetPath'): $url\n";
        
        // Check if URL contains CDN
        if (strpos($url, 'cdn.mechamap.com') !== false) {
            echo "  ❌ WARNING: Using CDN URL!\n";
        } else {
            echo "  ✅ Using local URL\n";
        }
    }
    echo "\n";
}

// Test production domain detection
echo "🌐 Production Domain Detection:\n";
echo "------------------------------\n";
$appUrl = config('app.url');
if (str_contains($appUrl, 'mechamap.com')) {
    echo "✅ Production domain detected: $appUrl\n";
} else {
    echo "ℹ️ Non-production domain: $appUrl\n";
}

// Test CDN configuration logic
echo "\n🔧 CDN Configuration Logic:\n";
echo "---------------------------\n";
$cdnEnabled = env('CDN_ENABLED', false);
$cdnUrl = config('production.domain.cdn');

if ($cdnEnabled && $cdnUrl) {
    echo "❌ CDN would be enabled with URL: $cdnUrl\n";
    echo "   This would cause assets to load from CDN!\n";
} elseif (!$cdnEnabled) {
    echo "✅ CDN is disabled (CDN_ENABLED=false)\n";
} elseif (!$cdnUrl) {
    echo "✅ CDN URL is not configured\n";
} else {
    echo "✅ CDN is properly disabled\n";
}

echo "\n📝 SUMMARY:\n";
echo "===========\n";

$hasIssues = false;

// Check for CDN usage
foreach ($testAssets as $assetPath) {
    $url = asset($assetPath);
    if (strpos($url, 'cdn.mechamap.com') !== false) {
        echo "❌ Assets are using CDN URLs\n";
        $hasIssues = true;
        break;
    }
}

if (!$hasIssues) {
    echo "✅ All assets are using local URLs\n";
}

// Check configuration
if (env('CDN_ENABLED', false)) {
    echo "⚠️ CDN_ENABLED is set to true\n";
    $hasIssues = true;
}

if (config('production.domain.cdn') && env('CDN_ENABLED', false)) {
    echo "⚠️ CDN URL is configured and enabled\n";
    $hasIssues = true;
}

if (!$hasIssues) {
    echo "✅ Configuration is correct for local asset serving\n";
    echo "\n🎉 SUCCESS: Assets will be served from your VPS, not CDN!\n";
} else {
    echo "\n🔧 FIXES NEEDED:\n";
    echo "================\n";
    echo "1. Add to your .env file:\n";
    echo "   CDN_URL=\n";
    echo "   CDN_ENABLED=false\n\n";
    echo "2. Clear config cache:\n";
    echo "   php artisan config:clear\n\n";
    echo "3. Restart your web server\n";
}

echo "\n";
