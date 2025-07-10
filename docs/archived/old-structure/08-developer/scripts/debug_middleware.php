<?php
/**
 * Debug Middleware Configuration
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "🔍 MIDDLEWARE CONFIGURATION DEBUG\n";
echo "=====================================\n\n";

// Get route collection
$router = $app->make('router');
$routes = $router->getRoutes();

echo "📋 Routes with middleware information:\n";
echo "=====================================\n";

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'api/v1/orders') !== false) {
        echo "\n🎯 Route: " . $route->methods()[0] . " /" . $uri . "\n";
        echo "   Controller: " . $route->getActionName() . "\n";
        echo "   Middleware: " . implode(', ', $route->middleware()) . "\n";
    }
}

echo "\n📊 Middleware Groups:\n";
echo "====================\n";

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$reflection = new ReflectionClass($kernel);

if ($reflection->hasProperty('middlewareGroups')) {
    $property = $reflection->getProperty('middlewareGroups');
    $property->setAccessible(true);
    $middlewareGroups = $property->getValue($kernel);

    foreach ($middlewareGroups as $group => $middleware) {
        echo "\n[$group]:\n";
        foreach ($middleware as $m) {
            echo "  - $m\n";
        }
    }
}

echo "\n🔧 Current Database Config:\n";
echo "===========================\n";
echo "Default connection: " . config('database.default') . "\n";
echo "Environment: " . app()->environment() . "\n";

echo "\n✅ Debug completed!\n";
