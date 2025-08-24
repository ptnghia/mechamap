<?php

/**
 * Script phân tích coverage của SEO data cho các route dashboard
 * Chạy: php scripts/analyze_dashboard_seo_coverage.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Dashboard SEO Coverage Analysis\n";
echo "=================================\n\n";

// Lấy tất cả routes dashboard từ Laravel
$dashboardRoutes = [];
foreach (Route::getRoutes() as $route) {
    if ($route->getName() && str_starts_with($route->getName(), 'dashboard.')) {
        // Chỉ lấy routes GET
        if (in_array('GET', $route->methods())) {
            $dashboardRoutes[] = [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods()
            ];
        }
    }
}

// Sắp xếp theo tên route
usort($dashboardRoutes, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

echo "📋 Found " . count($dashboardRoutes) . " dashboard routes:\n";
foreach ($dashboardRoutes as $route) {
    echo "   - {$route['name']} → /{$route['uri']}\n";
}

// Lấy SEO data hiện tại từ DashboardSeoSeeder
$seoSeederFile = __DIR__ . '/../database/seeders/DashboardSeoSeeder.php';
$seoContent = file_get_contents($seoSeederFile);

// Extract route names từ seeder
preg_match_all("/'route_name'\s*=>\s*'([^']+)'/", $seoContent, $matches);
$seoRoutes = $matches[1];

echo "\n📊 SEO Data Coverage Analysis:\n";
echo "==============================\n";

$routeNames = array_column($dashboardRoutes, 'name');
$missingRoutes = array_diff($routeNames, $seoRoutes);
$extraRoutes = array_diff($seoRoutes, $routeNames);

echo "✅ Routes with SEO data: " . count($seoRoutes) . "\n";
echo "📝 Total dashboard routes: " . count($routeNames) . "\n";
echo "❌ Missing SEO data: " . count($missingRoutes) . "\n";
echo "⚠️ Extra SEO entries: " . count($extraRoutes) . "\n";

if (!empty($missingRoutes)) {
    echo "\n❌ Routes missing SEO data:\n";
    foreach ($missingRoutes as $route) {
        echo "   - {$route}\n";
    }
}

if (!empty($extraRoutes)) {
    echo "\n⚠️ SEO entries for non-existent routes:\n";
    foreach ($extraRoutes as $route) {
        echo "   - {$route}\n";
    }
}

// Phân tích theo nhóm
$routeGroups = [
    'Common' => [],
    'Profile' => [],
    'Activity' => [],
    'Notifications' => [],
    'Messages' => [],
    'Settings' => [],
    'Community' => [],
    'Marketplace' => [],
    'Other' => []
];

foreach ($routeNames as $route) {
    if (str_contains($route, '.profile.')) {
        $routeGroups['Profile'][] = $route;
    } elseif (str_contains($route, '.activity')) {
        $routeGroups['Activity'][] = $route;
    } elseif (str_contains($route, '.notifications.')) {
        $routeGroups['Notifications'][] = $route;
    } elseif (str_contains($route, '.messages.')) {
        $routeGroups['Messages'][] = $route;
    } elseif (str_contains($route, '.settings.')) {
        $routeGroups['Settings'][] = $route;
    } elseif (str_contains($route, '.community.')) {
        $routeGroups['Community'][] = $route;
    } elseif (str_contains($route, '.marketplace.')) {
        $routeGroups['Marketplace'][] = $route;
    } elseif ($route === 'dashboard') {
        $routeGroups['Common'][] = $route;
    } else {
        $routeGroups['Other'][] = $route;
    }
}

echo "\n📊 Routes by Category:\n";
echo "=====================\n";
foreach ($routeGroups as $group => $routes) {
    if (!empty($routes)) {
        echo "{$group}: " . count($routes) . " routes\n";
        foreach ($routes as $route) {
            $hasSeo = in_array($route, $seoRoutes) ? '✅' : '❌';
            echo "   {$hasSeo} {$route}\n";
        }
        echo "\n";
    }
}

// Tạo template cho các route thiếu SEO
if (!empty($missingRoutes)) {
    echo "🔧 Generated SEO templates for missing routes:\n";
    echo "==============================================\n\n";
    
    foreach ($missingRoutes as $route) {
        $routeParts = explode('.', $route);
        $lastPart = end($routeParts);
        
        // Tạo title và description dựa trên route name
        $title = ucfirst(str_replace(['.', '_'], [' ', ' '], $route));
        $title = str_replace('Dashboard ', '', $title);
        $title .= ' - Dashboard - MechaMap';
        
        $description = "Quản lý " . strtolower(str_replace(['.', '_'], [' ', ' '], $route));
        $keywords = str_replace(['.', '_'], [', ', ', '], $route);
        
        echo "            // {$title}\n";
        echo "            [\n";
        echo "                'route_name' => '{$route}',\n";
        echo "                'title' => '{$title}',\n";
        echo "                'description' => '{$description}',\n";
        echo "                'keywords' => '{$keywords}',\n";
        echo "                'title_i18n' => json_encode([\n";
        echo "                    'vi' => '{$title}',\n";
        echo "                    'en' => '" . str_replace(' - MechaMap', ' - Dashboard - MechaMap', $title) . "'\n";
        echo "                ]),\n";
        echo "                'description_i18n' => json_encode([\n";
        echo "                    'vi' => '{$description}',\n";
        echo "                    'en' => 'Manage " . strtolower(str_replace(['.', '_'], [' ', ' '], $route)) . "'\n";
        echo "                ]),\n";
        echo "                'og_title' => '{$title}',\n";
        echo "                'og_description' => '{$description}',\n";
        echo "                'canonical_url' => '/" . str_replace('.', '/', $route) . "',\n";
        echo "                'no_index' => true, // Private page\n";
        echo "                'is_active' => true,\n";
        echo "            ],\n\n";
    }
}

// Tạo summary report
echo "📋 Summary Report:\n";
echo "==================\n";
echo "Total Dashboard Routes: " . count($routeNames) . "\n";
echo "Routes with SEO Data: " . count($seoRoutes) . "\n";
echo "Coverage: " . round((count($seoRoutes) / count($routeNames)) * 100, 1) . "%\n";
echo "Missing Routes: " . count($missingRoutes) . "\n";
echo "Extra SEO Entries: " . count($extraRoutes) . "\n";

if (count($missingRoutes) === 0 && count($extraRoutes) === 0) {
    echo "\n🎉 Perfect! All dashboard routes have SEO data coverage!\n";
} elseif (count($missingRoutes) === 0) {
    echo "\n✅ All routes have SEO data, but there are some extra entries to clean up.\n";
} else {
    echo "\n⚠️ Some routes are missing SEO data. Please update DashboardSeoSeeder.php\n";
}

echo "\n🔧 Next Steps:\n";
echo "==============\n";
if (!empty($missingRoutes)) {
    echo "1. Add SEO data for missing routes using the templates above\n";
    echo "2. Update database/seeders/DashboardSeoSeeder.php\n";
    echo "3. Run: php artisan db:seed --class=DashboardSeoSeeder\n";
}
if (!empty($extraRoutes)) {
    echo "4. Remove SEO entries for non-existent routes\n";
}
echo "5. Test SEO meta tags on dashboard pages\n";
echo "6. Verify breadcrumb functionality\n";

echo "\n✨ Dashboard SEO analysis completed!\n";
