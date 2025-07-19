#!/usr/bin/env php
<?php

/**
 * Debug WebSocket Configuration on VPS
 * Kiểm tra tất cả các vấn đề có thể gây ra lỗi "Undefined variable $configJson"
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DEBUGGING WEBSOCKET CONFIGURATION ON VPS\n";
echo "============================================\n\n";

// 1. Check environment
echo "📋 1. ENVIRONMENT CHECK:\n";
echo "------------------------\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n\n";

// 2. Check WebSocket environment variables
echo "🌐 2. WEBSOCKET ENVIRONMENT VARIABLES:\n";
echo "-------------------------------------\n";
$wsVars = [
    'WEBSOCKET_SERVER_URL',
    'WEBSOCKET_SERVER_HOST', 
    'WEBSOCKET_SERVER_PORT',
    'WEBSOCKET_SERVER_SECURE',
    'REALTIME_SERVER_URL',
    'NODEJS_BROADCAST_URL'
];

foreach ($wsVars as $var) {
    $value = env($var);
    echo "$var: " . ($value ?: 'NOT SET') . "\n";
}
echo "\n";

// 3. Check config files
echo "📁 3. CONFIG FILES CHECK:\n";
echo "-------------------------\n";
$configFiles = [
    'config/websocket.php',
    'config/broadcasting.php',
    'config/app.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file MISSING!\n";
    }
}
echo "\n";

// 4. Test config loading
echo "⚙️ 4. CONFIG LOADING TEST:\n";
echo "--------------------------\n";
try {
    $wsConfig = config('websocket');
    if ($wsConfig) {
        echo "✅ websocket config loaded successfully\n";
        echo "   Server URL: " . config('websocket.server.url', 'NOT SET') . "\n";
        echo "   Server Host: " . config('websocket.server.host', 'NOT SET') . "\n";
        echo "   Server Port: " . config('websocket.server.port', 'NOT SET') . "\n";
    } else {
        echo "❌ websocket config failed to load\n";
    }
} catch (\Exception $e) {
    echo "❌ Error loading websocket config: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Test WebSocketConfig component
echo "🔧 5. WEBSOCKETCONFIG COMPONENT TEST:\n";
echo "------------------------------------\n";
try {
    $component = new \App\View\Components\WebSocketConfig();
    echo "✅ WebSocketConfig component created successfully\n";
    echo "   Server URL: " . ($component->serverUrl ?: 'NULL') . "\n";
    echo "   Server Host: " . ($component->serverHost ?: 'NULL') . "\n";
    echo "   Server Port: " . ($component->serverPort ?: 'NULL') . "\n";
    echo "   Secure: " . ($component->secure ? 'true' : 'false') . "\n";
    echo "   Laravel URL: " . ($component->laravelUrl ?: 'NULL') . "\n";
    
    // Test configJson method
    try {
        $configJson = $component->configJson();
        echo "✅ configJson() method works\n";
        echo "   JSON length: " . strlen($configJson) . " characters\n";
        
        $decoded = json_decode($configJson, true);
        if ($decoded) {
            echo "✅ JSON is valid\n";
            if (isset($decoded['error'])) {
                echo "⚠️ JSON contains error flag\n";
            }
        } else {
            echo "❌ JSON is invalid\n";
        }
    } catch (\Exception $e) {
        echo "❌ configJson() method failed: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ WebSocketConfig component failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Test component rendering
echo "🎨 6. COMPONENT RENDERING TEST:\n";
echo "-------------------------------\n";
try {
    $component = new \App\View\Components\WebSocketConfig();
    $view = $component->render();
    echo "✅ Component render() method works\n";
    
    // Check if view data contains configJson
    $viewData = $view->getData();
    if (isset($viewData['configJson'])) {
        echo "✅ configJson is passed to view\n";
    } else {
        echo "❌ configJson is NOT passed to view\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Component rendering failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Check cache status
echo "💾 7. CACHE STATUS:\n";
echo "------------------\n";
try {
    // Check if config is cached
    if (file_exists(bootstrap_path('cache/config.php'))) {
        echo "⚠️ Config cache exists - may be outdated\n";
        echo "   Run: php artisan config:clear\n";
    } else {
        echo "✅ No config cache\n";
    }
    
    // Check view cache
    $viewCachePath = storage_path('framework/views');
    if (is_dir($viewCachePath)) {
        $files = glob($viewCachePath . '/*');
        if (count($files) > 0) {
            echo "⚠️ View cache exists (" . count($files) . " files)\n";
            echo "   Run: php artisan view:clear\n";
        } else {
            echo "✅ No view cache\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Cache check failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Recommendations
echo "💡 8. RECOMMENDATIONS:\n";
echo "----------------------\n";

$issues = [];

if (!env('WEBSOCKET_SERVER_URL')) {
    $issues[] = "Set WEBSOCKET_SERVER_URL in .env";
}

if (!config('websocket.server.url')) {
    $issues[] = "Configure websocket.server.url";
}

if (!file_exists('config/websocket.php')) {
    $issues[] = "Copy config/websocket.php from repository";
}

if (file_exists(bootstrap_path('cache/config.php'))) {
    $issues[] = "Clear config cache: php artisan config:clear";
}

if (empty($issues)) {
    echo "✅ No issues found - configuration looks good!\n";
} else {
    echo "🔧 Issues to fix:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". $issue\n";
    }
}

echo "\n🎯 QUICK FIX COMMANDS:\n";
echo "=====================\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan view:clear\n";
echo "composer dump-autoload\n";

echo "\n";
