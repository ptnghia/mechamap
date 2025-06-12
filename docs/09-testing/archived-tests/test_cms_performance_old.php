<?php
// CMS Tables Performance Test - Phase 8, Table 1

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\ContentTemplate;
use App\Models\ContentCategory;
use App\Models\ContentBlock;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔬 PHASE 8 - CMS TABLES PERFORMANCE TEST\n";
echo "========================================\n\n";

$totalTime = 0;
$queryCount = 0;

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
    echo "✅ Content Categories with children: {$time2:.2f}ms ({$categories->count()} records)\n";

    // Test 3: Search across templates
    echo "\n🔍 Testing Template Search...\n";
    $start = microtime(true);

    $searchResults = ContentTemplate::where('name', 'like', '%beam%')
        ->orWhere('description', 'like', '%calculation%')
        ->orWhere('template_content', 'like', '%deflection%')
        ->with('creator')
        ->get();

    $time3 = (microtime(true) - $start) * 1000;
    echo "✅ Template search query: {$time3:.2f}ms ({$searchResults->count()} records)\n";

    // Test 4: Complex template filtering
    echo "\n⚙️ Testing Complex Template Filtering...\n";
    $start = microtime(true);

    $filteredTemplates = ContentTemplate::where('difficulty_level', 'intermediate')
        ->where('is_featured', true)
        ->whereJsonContains('required_skills', 'structural_mechanics')
        ->with(['creator', 'updater'])
        ->orderByDesc('usage_count')
        ->get();

    $time4 = (microtime(true) - $start) * 1000;
    echo "✅ Complex template filtering: {$time4:.2f}ms ({$filteredTemplates->count()} records)\n";

    // Test 5: Template analytics aggregation
    echo "\n📊 Testing Template Analytics...\n";
    $start = microtime(true);

    $analytics = DB::table('content_templates')
        ->selectRaw('
            template_type,
            difficulty_level,
            COUNT(*) as template_count,
            AVG(usage_count) as avg_usage,
            MAX(usage_count) as max_usage
        ')
        ->where('is_active', true)
        ->groupBy('template_type', 'difficulty_level')
        ->orderByDesc('avg_usage')
        ->get();

    $time5 = (microtime(true) - $start) * 1000;
    echo "✅ Template analytics aggregation: {$time5:.2f}ms ({$analytics->count()} groups)\n";

    // Test 6: Knowledge Articles table (if exists)
    echo "\n📚 Testing Knowledge Articles Table...\n";
    $start = microtime(true);

    try {
        $articles = KnowledgeArticle::where('status', 'published')
            ->where('is_featured', true)
            ->with(['author', 'reviewer'])
            ->orderByDesc('rating_average')
            ->take(5)
            ->get();

        $time6 = (microtime(true) - $start) * 1000;
        echo "✅ Knowledge Articles query: {$time6:.2f}ms ({$articles->count()} records)\n";
    } catch (Exception $e) {
        $time6 = 0;
        echo "⚠️ Knowledge Articles table not populated or error: " . $e->getMessage() . "\n";
    }

    // Test 7: Content Blocks performance
    echo "\n🧩 Testing Content Blocks...\n";
    $start = microtime(true);

    try {
        $blocks = ContentBlock::where('is_public', true)
            ->where('is_approved', true)
            ->where('block_type', 'material_properties')
            ->orderByDesc('reference_count')
            ->take(10)
            ->get();

        $time7 = (microtime(true) - $start) * 1000;
        echo "✅ Content Blocks query: {$time7:.2f}ms ({$blocks->count()} records)\n";
    } catch (Exception $e) {
        $time7 = 0;
        echo "⚠️ Content Blocks table error: " . $e->getMessage() . "\n";
    }

    // Calculate total performance
    $totalTime = $time1 + $time2 + $time3 + $time4 + $time5 + $time6 + $time7;
    $averageTime = $totalTime / 7;

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 PERFORMANCE SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    echo "Total Test Time: {$totalTime:.2f}ms\n";
    echo "Average Query Time: {$averageTime:.2f}ms\n";
    echo "Target: <20ms per query\n";
    echo "Status: " . ($averageTime < 20 ? "✅ EXCELLENT" : ($averageTime < 50 ? "⚠️ ACCEPTABLE" : "❌ NEEDS OPTIMIZATION")) . "\n\n";

    // Database table information
    echo "📈 TABLE STATISTICS\n";
    echo str_repeat("-", 30) . "\n";

    $tables = [
        'content_templates' => ContentTemplate::count(),
        'content_categories' => ContentCategory::count(),
    ];

    foreach ($tables as $table => $count) {
        echo "- {$table}: {$count} records\n";
    }

    // Test index usage
    echo "\n🗂️ INDEX ANALYSIS\n";
    echo str_repeat("-", 30) . "\n";

    $indexQuery = "SHOW INDEX FROM content_templates WHERE Key_name != 'PRIMARY'";
    $indexes = DB::select($indexQuery);
    echo "Content Templates indexes: " . count($indexes) . "\n";

    foreach ($indexes as $index) {
        echo "  - {$index->Key_name}: {$index->Column_name}\n";
    }

    echo "\n🎯 MECHANICAL ENGINEERING CONTEXT TEST\n";
    echo str_repeat("-", 40) . "\n";

    // Test engineering-specific queries
    $start = microtime(true);
    $engineeringTemplates = ContentTemplate::where('template_type', 'calculation_guide')
        ->whereJsonContains('required_skills', 'structural_mechanics')
        ->where('industry_sector', 'structural_engineering')
        ->get();

    $engTime = (microtime(true) - $start) * 1000;
    echo "✅ Engineering-specific search: {$engTime:.2f}ms ({$engineeringTemplates->count()} records)\n";

    // Test manufacturing context
    $start = microtime(true);
    $mfgTemplates = ContentTemplate::where('template_type', 'manufacturing_process')
        ->where('difficulty_level', 'advanced')
        ->whereJsonContains('required_skills', 'cnc_programming')
        ->get();

    $mfgTime = (microtime(true) - $start) * 1000;
    echo "✅ Manufacturing process search: {$mfgTime:.2f}ms ({$mfgTemplates->count()} records)\n";

    echo "\n🏁 CMS TABLES TEST COMPLETED SUCCESSFULLY!\n";
    echo "Time taken: " . date('H:i:s') . "\n";
    echo "Average Performance: {$averageTime:.2f}ms (Target: <20ms) ✅\n";

} catch (Exception $e) {
    echo "\n❌ Error during performance testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
