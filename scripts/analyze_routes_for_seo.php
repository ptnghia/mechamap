<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel application
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Minimal middleware for route analysis
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 PHÂN TÍCH ROUTES CẦN SEO DATA - MECHAMAP\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Get all routes
$routes = Route::getRoutes();
$publicRoutes = [];
$adminRoutes = [];
$apiRoutes = [];
$authRoutes = [];
$dashboardRoutes = [];
$otherRoutes = [];

// Routes that typically don't need SEO (exclude from public SEO)
$excludeFromSeo = [
    // Authentication & verification
    'login', 'register', 'logout', 'password.', 'verification.', 'verify-email',
    
    // API routes
    'api.', 'sanctum.',
    
    // AJAX/API endpoints
    '.api', '.ajax', '.data', '.search-users', '.unread-count',
    
    // Actions (POST/PUT/DELETE)
    '.store', '.update', '.destroy', '.delete', '.toggle', '.vote', '.follow', '.like',
    '.bookmark', '.save', '.upload', '.download', '.export', '.import',
    
    // Webhooks & callbacks
    'webhook', 'callback', 'sepay-webhook',
    
    // Test routes
    'test.', 'test-',
    
    // System routes
    'robots', 'sitemap', 'up', 'health',
    
    // Redirects
    'Illuminate\Routing › RedirectController',
];

foreach ($routes as $route) {
    $name = $route->getName();
    $uri = $route->uri();
    $methods = implode('|', $route->methods());
    $action = $route->getActionName();
    
    // Skip routes without names
    if (!$name) {
        continue;
    }
    
    // Categorize routes
    if (str_starts_with($name, 'admin.')) {
        $adminRoutes[] = ['name' => $name, 'uri' => $uri, 'methods' => $methods];
    } elseif (str_starts_with($name, 'api.') || str_starts_with($uri, 'api/')) {
        $apiRoutes[] = ['name' => $name, 'uri' => $uri, 'methods' => $methods];
    } elseif (str_starts_with($name, 'dashboard.') || str_starts_with($uri, 'dashboard/')) {
        $dashboardRoutes[] = ['name' => $name, 'uri' => $uri, 'methods' => $methods];
    } elseif (in_array($name, ['login', 'register', 'logout']) || 
              str_contains($name, 'password.') || 
              str_contains($name, 'verification.')) {
        $authRoutes[] = ['name' => $name, 'uri' => $uri, 'methods' => $methods];
    } else {
        // Check if route should be excluded from SEO
        $shouldExclude = false;
        foreach ($excludeFromSeo as $pattern) {
            if (str_contains($name, $pattern) || str_contains($action, $pattern)) {
                $shouldExclude = true;
                break;
            }
        }
        
        if (!$shouldExclude && str_contains($methods, 'GET')) {
            $publicRoutes[] = ['name' => $name, 'uri' => $uri, 'methods' => $methods];
        } else {
            $otherRoutes[] = ['name' => $name, 'uri' => $uri, 'methods' => $methods];
        }
    }
}

// Display results
echo "📊 TỔNG QUAN ROUTES:\n";
echo "- Tổng số routes: " . count($routes) . "\n";
echo "- Public routes (cần SEO): " . count($publicRoutes) . "\n";
echo "- Admin routes: " . count($adminRoutes) . "\n";
echo "- API routes: " . count($apiRoutes) . "\n";
echo "- Auth routes: " . count($authRoutes) . "\n";
echo "- Dashboard routes: " . count($dashboardRoutes) . "\n";
echo "- Other routes: " . count($otherRoutes) . "\n\n";

echo "🎯 PUBLIC ROUTES CẦN SEO DATA (" . count($publicRoutes) . " routes):\n";
echo str_repeat("-", 80) . "\n";

// Group public routes by category
$routeCategories = [
    'Home & Main Pages' => [],
    'Forums & Threads' => [],
    'Marketplace' => [],
    'Showcase' => [],
    'Users & Profiles' => [],
    'Tools & Resources' => [],
    'Content Pages' => [],
    'Search & Discovery' => [],
    'Other Public' => [],
];

foreach ($publicRoutes as $route) {
    $name = $route['name'];
    
    if (in_array($name, ['home', 'welcome', 'about.index', 'accessibility', 'privacy.index', 'terms.index', 'rules'])) {
        $routeCategories['Home & Main Pages'][] = $route;
    } elseif (str_contains($name, 'forum') || str_contains($name, 'thread') || str_contains($name, 'comment')) {
        $routeCategories['Forums & Threads'][] = $route;
    } elseif (str_contains($name, 'marketplace') || str_contains($name, 'product') || str_contains($name, 'seller') || str_contains($name, 'supplier')) {
        $routeCategories['Marketplace'][] = $route;
    } elseif (str_contains($name, 'showcase')) {
        $routeCategories['Showcase'][] = $route;
    } elseif (str_contains($name, 'user') || str_contains($name, 'profile') || str_contains($name, 'member')) {
        $routeCategories['Users & Profiles'][] = $route;
    } elseif (str_contains($name, 'tool') || str_contains($name, 'calculator') || str_contains($name, 'material') || str_contains($name, 'documentation')) {
        $routeCategories['Tools & Resources'][] = $route;
    } elseif (str_contains($name, 'page') || str_contains($name, 'news') || str_contains($name, 'tutorial') || str_contains($name, 'whats-new')) {
        $routeCategories['Content Pages'][] = $route;
    } elseif (str_contains($name, 'search')) {
        $routeCategories['Search & Discovery'][] = $route;
    } else {
        $routeCategories['Other Public'][] = $route;
    }
}

foreach ($routeCategories as $category => $routes) {
    if (empty($routes)) continue;
    
    echo "\n📁 {$category} (" . count($routes) . " routes):\n";
    foreach ($routes as $route) {
        echo sprintf("   %-40s %-30s %s\n", $route['name'], $route['uri'], $route['methods']);
    }
}

echo "\n\n🔍 PHÂN TÍCH CHI TIẾT:\n";
echo str_repeat("-", 80) . "\n";

// Priority analysis
$highPriorityRoutes = [
    'home', 'about.index', 'forums.index', 'threads.index', 'threads.show',
    'marketplace.index', 'marketplace.products.index', 'marketplace.products.show',
    'showcase.index', 'showcase.show', 'users.index', 'profile.show',
    'tools.index', 'search.index'
];

$mediumPriorityRoutes = [
    'whats-new', 'news.industry.index', 'tutorials.index', 'members.index',
    'pages.show', 'categories.index', 'tags.index'
];

echo "\n🚨 HIGH PRIORITY (Core pages - cần SEO ngay): " . count(array_intersect($highPriorityRoutes, array_column($publicRoutes, 'name'))) . " routes\n";
echo "🔶 MEDIUM PRIORITY (Important pages): " . count(array_intersect($mediumPriorityRoutes, array_column($publicRoutes, 'name'))) . " routes\n";
echo "🔹 LOW PRIORITY (Other public pages): " . (count($publicRoutes) - count(array_intersect($highPriorityRoutes, array_column($publicRoutes, 'name'))) - count(array_intersect($mediumPriorityRoutes, array_column($publicRoutes, 'name')))) . " routes\n";

echo "\n✅ KẾT LUẬN:\n";
echo "- Cần tạo SEO data cho " . count($publicRoutes) . " public routes\n";
echo "- Ưu tiên cao: " . count(array_intersect($highPriorityRoutes, array_column($publicRoutes, 'name'))) . " routes\n";
echo "- Ưu tiên trung bình: " . count(array_intersect($mediumPriorityRoutes, array_column($publicRoutes, 'name'))) . " routes\n";
echo "- Admin routes không cần SEO public\n";
echo "- API routes không cần SEO\n\n";

echo "📝 KHUYẾN NGHỊ:\n";
echo "1. Bắt đầu với High Priority routes\n";
echo "2. Sử dụng dynamic placeholders cho routes có parameters\n";
echo "3. Tạo template SEO cho từng category\n";
echo "4. Implement multilingual SEO cho tất cả routes\n";
echo "5. Sử dụng URL patterns cho dynamic routes\n\n";
