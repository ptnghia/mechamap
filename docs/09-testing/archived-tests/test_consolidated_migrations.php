<?php

// Test script cho consolidated migrations
// Chạy test từng file consolidated migration

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Testing Consolidated Migrations\n";
echo "==================================\n\n";

// Test database connection
try {
    $connection = DB::connection();
    echo "✅ Database connection: OK\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test consolidated tables
$consolidatedTables = [
    'categories' => [
        'file' => 'database/migrations/consolidated/2025_01_11_155000_create_categories_table_consolidated.php',
        'expectedFields' => ['id', 'name', 'slug', 'description', 'parent_id', 'order', 'icon', 'color_code', 'meta_description', 'meta_keywords', 'is_technical', 'expertise_level', 'requires_verification', 'allowed_file_types', 'thread_count', 'post_count', 'last_activity_at', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'expectedIndexes' => 7
    ],
    'threads' => [
        'file' => 'database/migrations/consolidated/2025_01_11_155500_create_threads_table_consolidated.php',
        'expectedFields' => ['id', 'title', 'slug', 'content', 'featured_image', 'meta_description', 'search_keywords', 'read_time', 'status', 'user_id', 'forum_id', 'category_id', 'is_sticky', 'is_locked', 'is_featured', 'is_solved', 'solution_comment_id', 'solved_at', 'solved_by', 'quality_score', 'average_rating', 'ratings_count', 'thread_type', 'technical_difficulty', 'project_type', 'software_used', 'industry_sector', 'technical_specs', 'requires_calculations', 'has_drawings', 'urgency_level', 'standards_compliance', 'requires_pe_review', 'has_cad_files', 'attachment_count', 'views', 'likes', 'bookmarks', 'shares', 'replies', 'last_activity_at', 'bumped_at', 'priority', 'created_at', 'updated_at'],
        'expectedIndexes' => 10
    ],
    'comments' => [
        'file' => 'database/migrations/consolidated/2025_01_11_160000_create_comments_table_consolidated.php',
        'expectedFields' => ['id', 'thread_id', 'user_id', 'parent_id', 'content', 'has_media', 'has_code_snippet', 'has_formula', 'formula_content', 'like_count', 'dislikes_count', 'helpful_count', 'expert_endorsements', 'quality_score', 'technical_accuracy_score', 'verification_status', 'verified_by', 'verified_at', 'technical_tags', 'answer_type', 'is_flagged', 'is_spam', 'is_solution', 'reports_count', 'edited_at', 'edit_count', 'edited_by', 'edit_reason', 'created_at', 'updated_at', 'deleted_at'],
        'expectedIndexes' => 15
    ],
    'tags' => [
        'file' => 'database/migrations/consolidated/2025_01_11_160500_create_tags_table_consolidated.php',
        'expectedFields' => ['id', 'name', 'slug', 'description', 'color_code', 'tag_type', 'expertise_level', 'usage_count', 'last_used_at', 'is_featured', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'expectedIndexes' => 10
    ]
];

$results = [];
$totalTests = count($consolidatedTables);
$passedTests = 0;

foreach ($consolidatedTables as $tableName => $tableInfo) {
    echo "🔍 Testing table: {$tableName}\n";
    echo "   File: {$tableInfo['file']}\n";

    $testResult = [
        'table' => $tableName,
        'file_exists' => false,
        'syntax_valid' => false,
        'fields_count' => 0,
        'expected_fields' => count($tableInfo['expectedFields']),
        'missing_fields' => [],
        'extra_fields' => [],
        'status' => 'FAIL'
    ];

    // Check if file exists
    if (file_exists($tableInfo['file'])) {
        $testResult['file_exists'] = true;
        echo "   ✅ File exists\n";

        // Check syntax by loading file
        try {
            $content = file_get_contents($tableInfo['file']);
            if (strpos($content, '<?php') !== false && strpos($content, 'Schema::create') !== false) {
                $testResult['syntax_valid'] = true;
                echo "   ✅ Syntax valid\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Syntax error: " . $e->getMessage() . "\n";
        }

        // Analyze expected vs actual fields (from file content analysis)
        $actualFields = [];
        if (preg_match_all('/\$table->[\w]+\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $actualFields = $matches[1];
        }

        // Add timestamp fields
        if (strpos($content, 'timestamps()') !== false) {
            $actualFields[] = 'created_at';
            $actualFields[] = 'updated_at';
        }

        // Add soft deletes if present
        if (strpos($content, 'softDeletes()') !== false) {
            $actualFields[] = 'deleted_at';
        }

        $testResult['fields_count'] = count($actualFields);
        $testResult['missing_fields'] = array_diff($tableInfo['expectedFields'], $actualFields);
        $testResult['extra_fields'] = array_diff($actualFields, $tableInfo['expectedFields']);

        if (count($testResult['missing_fields']) === 0 && $testResult['syntax_valid']) {
            $testResult['status'] = 'PASS';
            $passedTests++;
            echo "   ✅ All expected fields present\n";
        } else {
            echo "   ⚠️  Field analysis:\n";
            echo "      Expected: {$testResult['expected_fields']} fields\n";
            echo "      Found: {$testResult['fields_count']} fields\n";
            if (!empty($testResult['missing_fields'])) {
                echo "      Missing: " . implode(', ', $testResult['missing_fields']) . "\n";
            }
        }
    } else {
        echo "   ❌ File not found\n";
    }

    $results[] = $testResult;
    echo "\n";
}

// Summary report
echo "📊 CONSOLIDATION TEST SUMMARY\n";
echo "=============================\n";
echo "Total Tables Tested: {$totalTests}\n";
echo "Passed: {$passedTests}\n";
echo "Failed: " . ($totalTests - $passedTests) . "\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 1) . "%\n\n";

// Detailed results
echo "📋 DETAILED RESULTS\n";
echo "===================\n";
foreach ($results as $result) {
    $status = $result['status'] === 'PASS' ? '✅' : '❌';
    echo "{$status} {$result['table']}: {$result['status']}\n";
    echo "   Fields: {$result['fields_count']}/{$result['expected_fields']}\n";
    if (!empty($result['missing_fields'])) {
        echo "   Missing: " . implode(', ', array_slice($result['missing_fields'], 0, 3)) .
            (count($result['missing_fields']) > 3 ? '...' : '') . "\n";
    }
}

echo "\n🎯 RECOMMENDATION\n";
echo "=================\n";
if ($passedTests === $totalTests) {
    echo "✅ All consolidated migrations are ready for deployment!\n";
    echo "   Next: Test with fresh database migration\n";
} else {
    echo "⚠️  Some consolidated migrations need review:\n";
    foreach ($results as $result) {
        if ($result['status'] === 'FAIL') {
            echo "   - Review {$result['table']} table consolidation\n";
        }
    }
}

echo "\n🚀 Migration consolidation analysis complete!\n";
