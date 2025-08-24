<?php

/**
 * Script kiểm tra breadcrumb tự động cho dashboard routes
 * Chạy: php scripts/test_dashboard_breadcrumbs.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\BreadcrumbService;
use App\Services\DashboardBreadcrumbService;
use App\Models\PageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "🍞 Dashboard Breadcrumb Testing\n";
echo "==============================\n\n";

// Lấy tất cả dashboard routes
$dashboardRoutes = [];
foreach (Route::getRoutes() as $route) {
    if ($route->getName() && str_starts_with($route->getName(), 'dashboard.') && in_array('GET', $route->methods())) {
        $dashboardRoutes[] = [
            'name' => $route->getName(),
            'uri' => $route->uri(),
        ];
    }
}

// Sắp xếp theo tên route
usort($dashboardRoutes, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

echo "📋 Found " . count($dashboardRoutes) . " dashboard routes to test\n\n";

// Test breadcrumb cho từng route
$breadcrumbService = new BreadcrumbService();
$dashboardBreadcrumbService = new DashboardBreadcrumbService();

$successCount = 0;
$errorCount = 0;
$errors = [];

echo "🔍 Testing breadcrumbs for each route:\n";
echo "=====================================\n\n";

foreach ($dashboardRoutes as $routeInfo) {
    $routeName = $routeInfo['name'];
    $uri = $routeInfo['uri'];
    
    echo "Testing: {$routeName}\n";
    echo "   URI: /{$uri}\n";
    
    try {
        // Tạo mock request
        $request = Request::create('/' . $uri, 'GET');
        
        // Set route name cho request
        $route = new \Illuminate\Routing\Route(['GET'], $uri, []);
        $route->name($routeName);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
        
        // Test BreadcrumbService
        echo "   🔍 BreadcrumbService: ";
        $breadcrumbs1 = $breadcrumbService->generate($request);
        echo "Generated " . count($breadcrumbs1) . " items\n";
        
        // Hiển thị breadcrumb path
        $path1 = array_map(function($item) {
            return $item['title'];
        }, $breadcrumbs1);
        echo "      Path: " . implode(' > ', $path1) . "\n";
        
        // Test DashboardBreadcrumbService
        echo "   🔍 DashboardBreadcrumbService: ";
        
        // Set current route name để DashboardBreadcrumbService có thể đọc
        Route::shouldReceive('currentRouteName')->andReturn($routeName);
        
        $breadcrumbs2 = DashboardBreadcrumbService::generate();
        echo "Generated " . count($breadcrumbs2) . " items\n";
        
        // Hiển thị breadcrumb path
        $path2 = array_map(function($item) {
            return $item['title'];
        }, $breadcrumbs2);
        echo "      Path: " . implode(' > ', $path2) . "\n";
        
        // Kiểm tra SEO data
        $seoData = PageSeo::findByRoute($routeName);
        if ($seoData) {
            echo "   ✅ SEO Data: Found\n";
            echo "      Title: " . $seoData->getLocalizedTitle() . "\n";
        } else {
            echo "   ❌ SEO Data: Not found\n";
        }
        
        $successCount++;
        echo "   ✅ Success\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
        $errors[] = [
            'route' => $routeName,
            'error' => $e->getMessage()
        ];
        $errorCount++;
    }
}

echo "📊 Test Summary:\n";
echo "================\n";
echo "✅ Successful tests: {$successCount}\n";
echo "❌ Failed tests: {$errorCount}\n";
echo "📋 Total routes tested: " . count($dashboardRoutes) . "\n";

if (!empty($errors)) {
    echo "\n❌ Errors encountered:\n";
    foreach ($errors as $error) {
        echo "   - {$error['route']}: {$error['error']}\n";
    }
}

// Test specific dashboard routes với SEO data
echo "\n🎯 Testing specific routes with SEO data:\n";
echo "=========================================\n";

$testRoutes = [
    'dashboard.profile.edit',
    'dashboard.community.showcases.index',
    'dashboard.community.bookmarks.index',
    'dashboard.marketplace.orders.index',
    'dashboard.notifications.index',
    'dashboard.messages.index',
    'dashboard.settings.index'
];

foreach ($testRoutes as $routeName) {
    echo "\nTesting: {$routeName}\n";
    
    // Kiểm tra SEO data
    $seoData = PageSeo::findByRoute($routeName);
    if ($seoData) {
        echo "   ✅ SEO Data found\n";
        echo "      VI Title: " . $seoData->getLocalizedTitle('vi') . "\n";
        echo "      EN Title: " . $seoData->getLocalizedTitle('en') . "\n";
        echo "      Description: " . substr($seoData->getLocalizedDescription('vi'), 0, 50) . "...\n";
        echo "      Canonical: " . $seoData->canonical_url . "\n";
    } else {
        echo "   ❌ No SEO data found\n";
    }
    
    // Test breadcrumb generation
    try {
        $uri = str_replace('.', '/', $routeName);
        $request = Request::create('/' . $uri, 'GET');
        
        $route = new \Illuminate\Routing\Route(['GET'], $uri, []);
        $route->name($routeName);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
        
        $breadcrumbs = $breadcrumbService->generate($request);
        echo "   🍞 Breadcrumb: " . implode(' > ', array_map(function($item) {
            return $item['title'];
        }, $breadcrumbs)) . "\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Breadcrumb error: " . $e->getMessage() . "\n";
    }
}

// Kiểm tra breadcrumb hierarchy cho dashboard
echo "\n🏗️ Dashboard Breadcrumb Hierarchy Analysis:\n";
echo "==========================================\n";

$hierarchyTests = [
    'dashboard.community.showcases.create' => ['Home', 'Dashboard', 'Community', 'Showcases', 'Create'],
    'dashboard.marketplace.seller.analytics.index' => ['Home', 'Dashboard', 'Marketplace', 'Seller', 'Analytics'],
    'dashboard.profile.edit' => ['Home', 'Dashboard', 'Profile', 'Edit'],
    'dashboard.notifications.archive' => ['Home', 'Dashboard', 'Notifications', 'Archive'],
];

foreach ($hierarchyTests as $routeName => $expectedHierarchy) {
    echo "\nTesting hierarchy for: {$routeName}\n";
    echo "   Expected: " . implode(' > ', $expectedHierarchy) . "\n";
    
    try {
        $uri = str_replace('.', '/', $routeName);
        $request = Request::create('/' . $uri, 'GET');
        
        $route = new \Illuminate\Routing\Route(['GET'], $uri, []);
        $route->name($routeName);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
        
        $breadcrumbs = $breadcrumbService->generate($request);
        $actualHierarchy = array_map(function($item) {
            return $item['title'];
        }, $breadcrumbs);
        
        echo "   Actual:   " . implode(' > ', $actualHierarchy) . "\n";
        
        // So sánh
        $matches = count(array_intersect($expectedHierarchy, $actualHierarchy));
        $total = max(count($expectedHierarchy), count($actualHierarchy));
        $similarity = round(($matches / $total) * 100, 1);
        
        echo "   Similarity: {$similarity}%\n";
        
        if ($similarity >= 80) {
            echo "   ✅ Good hierarchy\n";
        } elseif ($similarity >= 60) {
            echo "   ⚠️ Acceptable hierarchy\n";
        } else {
            echo "   ❌ Poor hierarchy\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n🔧 Recommendations:\n";
echo "===================\n";
echo "1. Ensure all dashboard routes have SEO data in page_seos table\n";
echo "2. Check BreadcrumbService handles dashboard routes correctly\n";
echo "3. Verify DashboardBreadcrumbService is being used in dashboard views\n";
echo "4. Test breadcrumb display in actual dashboard pages\n";
echo "5. Ensure breadcrumb middleware is applied to dashboard routes\n";

echo "\n✨ Dashboard breadcrumb testing completed!\n";
