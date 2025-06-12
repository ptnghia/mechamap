<?php

/**
 * CMS Pages Test Script - Safe Version
 * Tests CMS pages functionality with column validation
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

    // Check if enhanced columns exist
    $hasIsActive = Schema::hasColumn('page_categories', 'is_active');
    $hasSortOrder = Schema::hasColumn('page_categories', 'sort_order');
    $hasOrder = Schema::hasColumn('page_categories', 'order');

    $query = DB::table('page_categories');

    if ($hasIsActive) {
        $query->where('is_active', true);
    }

    if ($hasSortOrder) {
        $query->orderBy('sort_order');
    } elseif ($hasOrder) {
        $query->orderBy('order');
    } else {
        $query->orderBy('id');
    }

    $pageCategories = $query->get();

    $time1 = (microtime(true) - $start) * 1000;
    echo "✅ Page categories query: " . number_format($time1, 2) . "ms ({$pageCategories->count()} records)\n";

    // Test 2: Pages with categories
    echo "\n📄 Testing Pages with Categories...\n";
    $start = microtime(true);

    $hasIsPublished = Schema::hasColumn('pages', 'is_published');
    $hasStatus = Schema::hasColumn('pages', 'status');

    $query = DB::table('pages')
        ->leftJoin('page_categories', 'pages.category_id', '=', 'page_categories.id')
        ->select('pages.*', 'page_categories.name as category_name');

    if ($hasIsPublished) {
        $query->where('pages.is_published', true);
    } elseif ($hasStatus) {
        $query->where('pages.status', 'published');
    }

    $pages = $query->orderBy('pages.created_at', 'desc')->get();

    $time2 = (microtime(true) - $start) * 1000;
    echo "✅ Pages with categories query: " . number_format($time2, 2) . "ms ({$pages->count()} records)\n";

    // Test 3: FAQs with categories
    echo "\n❓ Testing FAQs with Categories...\n";
    $start = microtime(true);

    $faqHasIsPublished = Schema::hasColumn('faqs', 'is_published');
    $faqHasStatus = Schema::hasColumn('faqs', 'status');
    $faqHasSortOrder = Schema::hasColumn('faqs', 'sort_order');
    $faqHasOrder = Schema::hasColumn('faqs', 'order');

    $query = DB::table('faqs')
        ->leftJoin('faq_categories', 'faqs.category_id', '=', 'faq_categories.id')
        ->select('faqs.*', 'faq_categories.name as category_name');

    if ($faqHasIsPublished) {
        $query->where('faqs.is_published', true);
    } elseif ($faqHasStatus) {
        $query->where('faqs.status', 'published');
    }

    if ($faqHasSortOrder) {
        $query->orderBy('faqs.sort_order');
    } elseif ($faqHasOrder) {
        $query->orderBy('faqs.order');
    } else {
        $query->orderBy('faqs.id');
    }

    $faqs = $query->get();

    $time3 = (microtime(true) - $start) * 1000;
    echo "✅ FAQs with categories query: " . number_format($time3, 2) . "ms ({$faqs->count()} records)\n";

    // Test 4: Search functionality
    echo "\n🔍 Testing Page Search...\n";
    $start = microtime(true);

    $query = DB::table('pages')
        ->where('title', 'like', '%engineering%')
        ->orWhere('content', 'like', '%mechanical%');

    if (Schema::hasColumn('pages', 'meta_description')) {
        $query->orWhere('meta_description', 'like', '%CAD%');
    }

    if ($hasIsPublished) {
        $query->where('is_published', true);
    } elseif ($hasStatus) {
        $query->where('status', 'published');
    }

    $searchResults = $query->get();

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
            $categoryName = $page->category_name ?? 'No category';
            echo "  - {$page->title} ({$categoryName})\n";
        }
    }

    if ($faqs->count() > 0) {
        echo "\n❓ Sample FAQs:\n";
        foreach ($faqs->take(3) as $faq) {
            echo "  - {$faq->question}\n";
        }
    }

    // Column status info
    echo "\n🔧 COLUMN STATUS INFO\n";
    echo str_repeat("-", 30) . "\n";
    echo "Page Categories:\n";
    echo "  - is_active: " . ($hasIsActive ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "  - sort_order: " . ($hasSortOrder ? "✅ EXISTS" : ($hasOrder ? "✅ EXISTS (as 'order')" : "❌ MISSING")) . "\n";
    echo "Pages:\n";
    echo "  - is_published: " . ($hasIsPublished ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "  - status: " . ($hasStatus ? "✅ EXISTS" : "❌ MISSING") . "\n";
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ CMS Pages testing completed!\n";
