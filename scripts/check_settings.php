<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SEO SETTINGS ===\n";
try {
    $seoSettings = \App\Models\SeoSetting::all();
    foreach ($seoSettings as $setting) {
        echo "Key: {$setting->key}, Value: {$setting->value}, Group: {$setting->group}\n";
    }
} catch (Exception $e) {
    echo "Lỗi khi lấy SEO Settings: " . $e->getMessage() . "\n";
}

echo "\n=== GENERAL SETTINGS ===\n";
try {
    $settings = \App\Models\Setting::all();
    foreach ($settings as $setting) {
        echo "Key: {$setting->key}, Value: {$setting->value}, Group: {$setting->group}\n";
    }
} catch (Exception $e) {
    echo "Lỗi khi lấy Settings: " . $e->getMessage() . "\n";
}
