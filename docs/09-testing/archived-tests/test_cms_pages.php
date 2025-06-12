<?php

/**
 * CMS Pages Test Script - Fixed Version
 * Tests the CMS pages functionality
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🚀 CMS PAGES TEST - MECHANICAL ENGINEERING FORUM\n";
echo "===============================================\n\n";

try {
    // Test 1: Page Categories
    echo "📂 Testing Page Categories...\n";
    $start = microtime(true);

    $pageCategories = DB::table('page_categories')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    $time1 = (microtime(true) - $start) * 1000;
    echo "✅ Page categories query: " . number_format($time1, 2) . "ms ({$pageCategories->count()} records)\n";

    // Test 2: Pages with categories
    echo "\n📄 Testing Pages with Categories...\n";
    $start = microtime(true);

    $pages = DB::table('pages')
        ->leftJoin('page_categories', 'pages.category_id', '=', 'page_categories.id')
        ->select('pages.*', 'page_categories.name as category_name')
        ->where('pages.is_published', true)
        ->orderBy('pages.created_at', 'desc')
        ->get();

    $time2 = (microtime(true) - $start) * 1000;
    echo "✅ Pages with categories query: " . number_format($time2, 2) . "ms ({$pages->count()} records)\n";

    // Test 3: FAQs with categories
    echo "\n❓ Testing FAQs with Categories...\n";
    $start = microtime(true);

    $faqs = DB::table('faqs')
        ->leftJoin('faq_categories', 'faqs.category_id', '=', 'faq_categories.id')
        ->select('faqs.*', 'faq_categories.name as category_name')
        ->where('faqs.is_published', true)
        ->orderBy('faqs.sort_order')
        ->get();

    $time3 = (microtime(true) - $start) * 1000;
    echo "✅ FAQs with categories query: " . number_format($time3, 2) . "ms ({$faqs->count()} records)\n";

    // Test 4: Search functionality
    echo "\n🔍 Testing Page Search...\n";
    $start = microtime(true);

    $searchResults = DB::table('pages')
        ->where('title', 'like', '%engineering%')
        ->orWhere('content', 'like', '%mechanical%')
        ->orWhere('meta_description', 'like', '%CAD%')
        ->where('is_published', true)
        ->get();

    $time4 = (microtime(true) - $start) * 1000;
    echo "✅ Page search query: " . number_format($time4, 2) . "ms ({$searchResults->count()} records)\n";

    // Calculate performance metrics
    $totalTime = $time1 + $time2 + $time3 + $time4;
    $averageTime = $totalTime / 4;

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

    echo "Average Performance: " . number_format($averageTime, 2) . "ms ✅\n";
    echo str_repeat("=", 50) . "\n";

    // Display some sample data
    echo "\n📋 SAMPLE DATA OVERVIEW\n";
    echo str_repeat("-", 30) . "\n";

    if ($pageCategories->count() > 0) {
        echo "📂 Page Categories:\n";
        foreach ($pageCategories->take(3) as $category) {
            echo "  - {$category->name}\n";
        }
    }

    if ($pages->count() > 0) {
        echo "\n📄 Recent Pages:\n";
        foreach ($pages->take(3) as $page) {
            echo "  - {$page->title} ({$page->category_name})\n";
        }
    }

    if ($faqs->count() > 0) {
        echo "\n❓ Sample FAQs:\n";
        foreach ($faqs->take(3) as $faq) {
            echo "  - {$faq->question}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ CMS Pages testing completed!\n";
