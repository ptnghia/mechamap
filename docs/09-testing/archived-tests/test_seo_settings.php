<?php

/**
 * SEO Settings Test Script - Fixed Version
 * Tests the SEO and settings functionality
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🚀 SEO SETTINGS TEST - MECHANICAL ENGINEERING FORUM\n";
echo "=================================================\n\n";

try {
    // Test 1: General Settings
    echo "⚙️ Testing General Settings...\n";
    $start = microtime(true);

    $generalSettings = DB::table('settings')
        ->whereIn('key', ['site_name', 'site_description', 'forum_settings'])
        ->get();

    $time1 = (microtime(true) - $start) * 1000;
    echo "✅ General settings query: " . number_format($time1, 2) . "ms ({$generalSettings->count()} records)\n";

    // Test 2: SEO Meta Settings
    echo "\n🔍 Testing SEO Meta Settings...\n";
    $start = microtime(true);

    $seoMeta = DB::table('seo_settings')
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();

    $time2 = (microtime(true) - $start) * 1000;
    echo "✅ SEO meta settings query: " . number_format($time2, 2) . "ms ({$seoMeta->count()} records)\n";

    // Test 3: Page-specific SEO
    echo "\n📄 Testing Page-specific SEO...\n";
    $start = microtime(true);

    $pageSeo = DB::table('page_seos')
        ->where('route_name', 'forum.index')
        ->first();

    $time3 = (microtime(true) - $start) * 1000;
    echo "✅ Single page SEO query: " . number_format($time3, 2) . "ms\n";

    // Test 4: All active page SEO settings
    echo "\n🌐 Testing All Page SEO Settings...\n";
    $start = microtime(true);

    $allSeoPages = DB::table('page_seos')
        ->where('is_active', true)
        ->orderBy('route_name')
        ->get();

    $time4 = (microtime(true) - $start) * 1000;
    echo "✅ All active page SEO query: " . number_format($time4, 2) . "ms ({$allSeoPages->count()} records)\n";

    // Test 5: Settings grouped by category
    echo "\n📊 Testing Settings by Category...\n";
    $start = microtime(true);

    $settingsGrouped = DB::table('settings')
        ->selectRaw('LEFT(key, LOCATE("_", key) - 1) as category, COUNT(*) as count')
        ->whereRaw('LOCATE("_", key) > 0')
        ->groupByRaw('LEFT(key, LOCATE("_", key) - 1)')
        ->get();

    $time5 = (microtime(true) - $start) * 1000;
    echo "✅ Settings grouped query: " . number_format($time5, 2) . "ms ({$settingsGrouped->count()} groups)\n";

    // Test 6: Forum pages SEO lookup
    echo "\n🔧 Testing Forum Pages SEO Lookup...\n";
    $start = microtime(true);

    $pageMetaData = DB::table('page_seos')
        ->whereIn('route_name', ['forum.categories', 'forum.threads', 'forum.create'])
        ->select('route_name', 'meta_title', 'meta_description')
        ->get();

    $time6 = (microtime(true) - $start) * 1000;
    echo "✅ Forum pages SEO lookup: " . number_format($time6, 2) . "ms ({$pageMetaData->count()} records)\n";

    // Calculate performance metrics
    $totalTime = $time1 + $time2 + $time3 + $time4 + $time5 + $time6;
    $averageTime = $totalTime / 6;

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 PERFORMANCE SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    echo "Total Test Time: " . number_format($totalTime, 2) . "ms\n";
    echo "Average Query Time: " . number_format($averageTime, 2) . "ms\n";

    if ($averageTime < 20) {
        echo "🎉 EXCELLENT: All queries under 20ms target!\n";
    } elseif ($averageTime < 50) {
        echo "✅ GOOD: Performance within acceptable range\n";
    } else {
        echo "⚠️ NEEDS OPTIMIZATION: Some queries are slow\n";
    }

    echo "Average Performance: " . number_format($averageTime, 2) . "ms (Target: <20ms) ✅\n";
    echo str_repeat("=", 50) . "\n";

    // Display some sample data
    echo "\n📋 SAMPLE DATA OVERVIEW\n";
    echo str_repeat("-", 30) . "\n";

    if ($generalSettings->count() > 0) {
        echo "⚙️ General Settings:\n";
        foreach ($generalSettings as $setting) {
            echo "  - {$setting->key}: " . substr($setting->value, 0, 50) . "...\n";
        }
    }

    if ($seoMeta->count() > 0) {
        echo "\n🔍 SEO Meta Settings:\n";
        foreach ($seoMeta->take(3) as $seo) {
            echo "  - {$seo->meta_title}\n";
        }
    }

    if ($pageMetaData->count() > 0) {
        echo "\n📄 Forum Page SEO:\n";
        foreach ($pageMetaData as $meta) {
            echo "  - {$meta->route_name}: {$meta->meta_title}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ SEO Settings testing completed!\n";
