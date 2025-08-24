<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PageSeo;

$routes = [
    // Basic routes for breadcrumb hierarchy
    ['route_name' => 'threads', 'title' => 'Chủ đề', 'breadcrumb_title' => 'Chủ đề', 'vi' => 'Chủ đề', 'en' => 'Threads'],
    ['route_name' => 'marketplace.products', 'title' => 'Sản phẩm', 'breadcrumb_title' => 'Sản phẩm', 'vi' => 'Sản phẩm', 'en' => 'Products'],
    ['route_name' => 'marketplace.sellers', 'title' => 'Nhà cung cấp', 'breadcrumb_title' => 'Nhà cung cấp', 'vi' => 'Nhà cung cấp', 'en' => 'Sellers'],
    ['route_name' => 'dashboard', 'title' => 'Dashboard', 'breadcrumb_title' => 'Dashboard', 'vi' => 'Dashboard', 'en' => 'Dashboard'],
    ['route_name' => 'dashboard.profile', 'title' => 'Hồ sơ', 'breadcrumb_title' => 'Hồ sơ', 'vi' => 'Hồ sơ', 'en' => 'Profile'],
    ['route_name' => 'dashboard.notifications', 'title' => 'Thông báo', 'breadcrumb_title' => 'Thông báo', 'vi' => 'Thông báo', 'en' => 'Notifications'],
    ['route_name' => 'dashboard.messages', 'title' => 'Tin nhắn', 'breadcrumb_title' => 'Tin nhắn', 'vi' => 'Tin nhắn', 'en' => 'Messages'],
    ['route_name' => 'dashboard.settings', 'title' => 'Cài đặt', 'breadcrumb_title' => 'Cài đặt', 'vi' => 'Cài đặt', 'en' => 'Settings'],
    ['route_name' => 'members', 'title' => 'Thành viên', 'breadcrumb_title' => 'Thành viên', 'vi' => 'Thành viên', 'en' => 'Members'],
    ['route_name' => 'about', 'title' => 'Giới thiệu', 'breadcrumb_title' => 'Giới thiệu', 'vi' => 'Giới thiệu', 'en' => 'About'],
    ['route_name' => 'contact', 'title' => 'Liên hệ', 'breadcrumb_title' => 'Liên hệ', 'vi' => 'Liên hệ', 'en' => 'Contact'],
    ['route_name' => 'search', 'title' => 'Tìm kiếm', 'breadcrumb_title' => 'Tìm kiếm', 'vi' => 'Tìm kiếm', 'en' => 'Search'],
];

$created = 0;
$updated = 0;

foreach ($routes as $route) {
    $existing = PageSeo::where('route_name', $route['route_name'])->first();
    
    if (!$existing) {
        $seo = new PageSeo();
        $seo->route_name = $route['route_name'];
        $seo->title = $route['title'];
        $seo->breadcrumb_title = $route['breadcrumb_title'];
        $seo->breadcrumb_title_i18n = json_encode(['vi' => $route['vi'], 'en' => $route['en']]);
        $seo->is_active = true;
        $seo->save();
        $created++;
        echo "Created: {$route['route_name']}\n";
    } else {
        // Update breadcrumb_title_i18n if missing
        if (empty($existing->breadcrumb_title_i18n)) {
            $existing->breadcrumb_title_i18n = json_encode(['vi' => $route['vi'], 'en' => $route['en']]);
            $existing->save();
            $updated++;
            echo "Updated: {$route['route_name']}\n";
        } else {
            echo "Exists: {$route['route_name']}\n";
        }
    }
}

echo "\nSummary:\n";
echo "Created: {$created} new SEO entries\n";
echo "Updated: {$updated} existing SEO entries\n";
echo "Total processed: " . count($routes) . " routes\n";
