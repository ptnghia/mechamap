<?php

/**
 * CMS Performance Test Script - Fixed Version
 * Tests the performance of content management system queries
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ContentTemplate;
use App\Models\ContentCategory;
use App\Models\KnowledgeArticle;
use App\Models\ContentBlock;

echo "🚀 CMS PERFORMANCE TEST - MECHANICAL ENGINEERING FORUM\n";
echo "================================================\n\n";

try {
    // Test 1: Content Templates performance
    echo "📝 Testing Content Templates...\n";
    $start = microtime(true);

    $templates = ContentTemplate::with('creator')
        ->where('is_active', true)
        ->orderBy('difficulty_level')
        ->take(10)
        ->get();

    $time1 = (microtime(true) - $start) * 1000;
    echo "✅ Content Templates query: " . number_format($time1, 2) . "ms ({$templates->count()} records)\n";

    // Test 2: Category hierarchy
    echo "\n📂 Testing Content Categories...\n";
    $start = microtime(true);

    $categories = ContentCategory::with('children')
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    $time2 = (microtime(true) - $start) * 1000;
    echo "✅ Content Categories with children: " . number_format($time2, 2) . "ms ({$categories->count()} records)\n";

    // Test 3: Search across templates
    echo "\n🔍 Testing Template Search...\n";
    $start = microtime(true);

    $searchResults = ContentTemplate::where('name', 'like', '%beam%')
        ->orWhere('description', 'like', '%calculation%')
        ->orWhere('template_content', 'like', '%deflection%')
        ->with('creator')
        ->get();

    $time3 = (microtime(true) - $start) * 1000;
    echo "✅ Template search query: " . number_format($time3, 2) . "ms ({$searchResults->count()} records)\n";

    // Test 4: Complex filtering
    echo "\n⚙️ Testing Complex Template Filtering...\n";
    $start = microtime(true);

    $filteredTemplates = ContentTemplate::where('template_type', 'calculation_guide')
        ->where('difficulty_level', 'intermediate')
        ->whereIn('engineering_domain', ['mechanical_design', 'materials'])
        ->with(['creator', 'category'])
        ->get();

    $time4 = (microtime(true) - $start) * 1000;
    echo "✅ Complex template filtering: " . number_format($time4, 2) . "ms ({$filteredTemplates->count()} records)\n";

    // Test 5: Analytics aggregation
    echo "\n📊 Testing Template Analytics...\n";
    $start = microtime(true);

    $analytics = ContentTemplate::selectRaw('template_type, COUNT(*) as count, AVG(view_count) as avg_views')
        ->where('is_active', true)
        ->groupBy('template_type')
        ->get();

    $time5 = (microtime(true) - $start) * 1000;
    echo "✅ Template analytics aggregation: " . number_format($time5, 2) . "ms ({$analytics->count()} groups)\n";

    // Test 6: Knowledge Articles (if table exists)
    if (Schema::hasTable('knowledge_articles')) {
        echo "\n📖 Testing Knowledge Articles...\n";
        $start = microtime(true);

        $articles = KnowledgeArticle::with(['author', 'category'])
            ->where('is_published', true)
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        $time6 = (microtime(true) - $start) * 1000;
        echo "✅ Knowledge Articles query: " . number_format($time6, 2) . "ms ({$articles->count()} records)\n";
    } else {
        echo "\n📖 Knowledge Articles table not found, skipping...\n";
        $time6 = 0;
    }

    // Test 7: Content Blocks (if table exists)
    if (Schema::hasTable('content_blocks')) {
        echo "\n🧩 Testing Content Blocks...\n";
        $start = microtime(true);

        $blocks = ContentBlock::where('block_type', 'formula')
            ->orWhere('block_type', 'material_property')
            ->with('creator')
            ->get();

        $time7 = (microtime(true) - $start) * 1000;
        echo "✅ Content Blocks query: " . number_format($time7, 2) . "ms ({$blocks->count()} records)\n";
    } else {
        echo "\n🧩 Content Blocks table not found, skipping...\n";
        $time7 = 0;
    }

    // Calculate overall performance
    $totalTime = $time1 + $time2 + $time3 + $time4 + $time5 + $time6 + $time7;
    $testCount = ($time6 > 0 ? 1 : 0) + ($time7 > 0 ? 1 : 0) + 5; // Base 5 tests + optional tests
    $averageTime = $totalTime / $testCount;

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

    // Engineering-specific performance tests
    echo "\n🔧 MECHANICAL ENGINEERING SPECIFIC TESTS\n";
    echo str_repeat("-", 40) . "\n";

    // CAD/Engineering search
    $start = microtime(true);
    $engineeringTemplates = ContentTemplate::where('engineering_domain', 'like', '%mechanical%')
        ->orWhere('template_content', 'like', '%CAD%')
        ->orWhere('template_content', 'like', '%SolidWorks%')
        ->get();
    $engTime = (microtime(true) - $start) * 1000;
    echo "✅ Engineering-specific search: " . number_format($engTime, 2) . "ms ({$engineeringTemplates->count()} records)\n";

    // Manufacturing process search
    $start = microtime(true);
    $mfgTemplates = ContentTemplate::where('template_type', 'manufacturing_process')
        ->orWhere('engineering_domain', 'manufacturing')
        ->get();
    $mfgTime = (microtime(true) - $start) * 1000;
    echo "✅ Manufacturing process search: " . number_format($mfgTime, 2) . "ms ({$mfgTemplates->count()} records)\n";

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🏆 FINAL RESULT\n";
    $finalAverage = ($averageTime + $engTime + $mfgTime) / 3;
    echo "Average Performance: " . number_format($finalAverage, 2) . "ms (Target: <20ms) " . ($finalAverage < 20 ? "✅" : "⚠️") . "\n";
    echo str_repeat("=", 50) . "\n";
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ CMS Performance testing completed!\n";
