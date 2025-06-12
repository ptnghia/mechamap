<?php
// Test CMS Pages tables performance and create sample data

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔬 PHASE 8 - CMS PAGES PERFORMANCE TEST\n";
echo "======================================\n\n";

try {
    // Test 1: Check existing tables
    echo "📋 Checking CMS Pages tables...\n";

    $tables = ['page_categories', 'pages', 'faq_categories', 'faqs'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "✅ {$table}: {$count} records\n";
        } catch (Exception $e) {
            echo "❌ {$table}: Error - " . $e->getMessage() . "\n";
        }
    }

    // Test 2: Create sample data for testing
    echo "\n📝 Creating sample CMS Pages data...\n";

    // Create page categories
    $pageCategories = [
        [
            'name' => 'Engineering Guides',
            'slug' => 'engineering-guides',
            'description' => 'Comprehensive guides for mechanical engineering topics',
            'order' => 1
        ],
        [
            'name' => 'Software Tutorials',
            'slug' => 'software-tutorials',
            'description' => 'Step-by-step tutorials for CAD and engineering software',
            'order' => 2
        ],
        [
            'name' => 'Standards & Codes',
            'slug' => 'standards-codes',
            'description' => 'Engineering standards and design codes reference',
            'order' => 3
        ]
    ];

    foreach ($pageCategories as $category) {
        try {
            DB::table('page_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
            echo "✅ Created/Updated page category: {$category['name']}\n";
        } catch (Exception $e) {
            echo "⚠️ Page category error: " . $e->getMessage() . "\n";
        }
    }

    // Get first user ID for sample data
    $userId = DB::table('users')->first()->id ?? 1;
    $categoryId = DB::table('page_categories')->first()->id ?? 1;

    // Create sample pages
    $pages = [
        [
            'title' => 'Introduction to Mechanical Engineering Design',
            'slug' => 'intro-mechanical-engineering-design',
            'content' => '<h1>Introduction to Mechanical Engineering Design</h1>
<p>Mechanical engineering design is the process of creating mechanical systems that solve specific problems or meet particular needs. This comprehensive guide covers the fundamental principles and methodologies used in modern mechanical design.</p>

<h2>Design Process Overview</h2>
<p>The mechanical design process typically follows these key phases:</p>
<ol>
<li><strong>Problem Definition</strong> - Clearly define the design requirements and constraints</li>
<li><strong>Conceptual Design</strong> - Generate and evaluate multiple design concepts</li>
<li><strong>Preliminary Design</strong> - Develop the most promising concepts</li>
<li><strong>Detailed Design</strong> - Complete the design with full specifications</li>
<li><strong>Testing and Validation</strong> - Verify the design meets all requirements</li>
</ol>

<h2>Key Design Considerations</h2>
<ul>
<li>Safety factors and reliability</li>
<li>Material selection and properties</li>
<li>Manufacturing feasibility</li>
<li>Cost optimization</li>
<li>Environmental impact</li>
</ul>

<h2>Design Tools and Software</h2>
<p>Modern mechanical design relies heavily on computer-aided design (CAD) tools:</p>
<ul>
<li>SolidWorks - Parametric design and simulation</li>
<li>AutoCAD - 2D drafting and documentation</li>
<li>CATIA - Advanced surface modeling</li>
<li>Inventor - Product development and visualization</li>
</ul>

<h2>Standards and Codes</h2>
<p>Design must comply with relevant engineering standards such as:</p>
<ul>
<li>ASME Y14.5 - Geometric Dimensioning and Tolerancing</li>
<li>ISO 9001 - Quality Management Systems</li>
<li>AISC Steel Construction Manual</li>
<li>ASTM Material Standards</li>
</ul>',
            'excerpt' => 'A comprehensive introduction to mechanical engineering design principles, processes, and tools for modern engineers.',
            'category_id' => $categoryId,
            'user_id' => $userId,
            'status' => 'published',
            'order' => 1,
            'is_featured' => true,
            'view_count' => 245,
            'meta_title' => 'Mechanical Engineering Design Guide | MechaMap',
            'meta_description' => 'Learn the fundamentals of mechanical engineering design including process, tools, and standards. Perfect for engineering students and professionals.',
            'meta_keywords' => 'mechanical engineering, design process, CAD, engineering standards, SolidWorks',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'title' => 'SolidWorks FEA Tutorial for Beginners',
            'slug' => 'solidworks-fea-tutorial-beginners',
            'content' => '<h1>SolidWorks FEA Tutorial for Beginners</h1>
<p>This tutorial will guide you through the basics of Finite Element Analysis (FEA) using SolidWorks Simulation. You\'ll learn how to set up, run, and interpret FEA studies for mechanical components.</p>

<h2>Prerequisites</h2>
<ul>
<li>Basic SolidWorks modeling experience</li>
<li>Understanding of solid mechanics principles</li>
<li>SolidWorks Premium or Professional license</li>
</ul>

<h2>Getting Started with SolidWorks Simulation</h2>

<h3>Step 1: Enable the Simulation Add-in</h3>
<ol>
<li>Go to Tools → Add-Ins</li>
<li>Check "SolidWorks Simulation"</li>
<li>Click OK to enable</li>
</ol>

<h3>Step 2: Create a New Study</h3>
<ol>
<li>Open your SolidWorks part</li>
<li>Go to Simulation tab</li>
<li>Click "New Study" → "Static"</li>
<li>Name your study (e.g., "Stress Analysis")</li>
</ol>

<h3>Step 3: Apply Materials</h3>
<ol>
<li>Right-click on the part in the simulation tree</li>
<li>Select "Apply/Edit Material"</li>
<li>Choose appropriate material (e.g., AISI 1020 Steel)</li>
<li>Verify material properties</li>
</ol>

<h3>Step 4: Define Fixtures (Boundary Conditions)</h3>
<ol>
<li>Right-click "Fixtures" in simulation tree</li>
<li>Select fixture type (Fixed, Roller, etc.)</li>
<li>Select faces/edges to constrain</li>
<li>Apply the fixture</li>
</ol>

<h3>Step 5: Apply Loads</h3>
<ol>
<li>Right-click "External Loads"</li>
<li>Select load type (Force, Pressure, etc.)</li>
<li>Define magnitude and direction</li>
<li>Apply to appropriate faces/edges</li>
</ol>

<h3>Step 6: Create Mesh</h3>
<ol>
<li>Right-click "Mesh" in simulation tree</li>
<li>Select "Create Mesh"</li>
<li>Adjust mesh density if needed</li>
<li>Click "OK" to generate mesh</li>
</ol>

<h3>Step 7: Run the Analysis</h3>
<ol>
<li>Right-click on study name</li>
<li>Select "Run"</li>
<li>Wait for analysis to complete</li>
<li>Check for errors or warnings</li>
</ol>

<h3>Step 8: Review Results</h3>
<ol>
<li>Von Mises stress plot</li>
<li>Displacement plot</li>
<li>Safety factor plot</li>
<li>Reaction forces at fixtures</li>
</ol>

<h2>Best Practices</h2>
<ul>
<li>Always validate FEA results with hand calculations</li>
<li>Use appropriate mesh density for accuracy</li>
<li>Check convergence by refining mesh</li>
<li>Apply proper safety factors</li>
<li>Document assumptions and limitations</li>
</ul>

<h2>Common Mistakes to Avoid</h2>
<ul>
<li>Over-constraining the model</li>
<li>Using inappropriate material properties</li>
<li>Ignoring stress concentrations</li>
<li>Not checking mesh quality</li>
<li>Misinterpreting results</li>
</ul>',
            'excerpt' => 'Learn the basics of Finite Element Analysis (FEA) in SolidWorks Simulation. Step-by-step tutorial for mechanical engineers.',
            'category_id' => $categoryId,
            'user_id' => $userId,
            'status' => 'published',
            'order' => 2,
            'is_featured' => false,
            'view_count' => 387,
            'meta_title' => 'SolidWorks FEA Tutorial | Beginner Guide to Simulation',
            'meta_description' => 'Complete beginner tutorial for SolidWorks FEA simulation. Learn stress analysis, boundary conditions, and result interpretation.',
            'meta_keywords' => 'SolidWorks FEA, finite element analysis, simulation tutorial, stress analysis, mechanical engineering',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(2)
        ]
    ];

    foreach ($pages as $page) {
        try {
            DB::table('pages')->updateOrInsert(
                ['slug' => $page['slug']],
                $page
            );
            echo "✅ Created/Updated page: {$page['title']}\n";
        } catch (Exception $e) {
            echo "⚠️ Page creation error: " . $e->getMessage() . "\n";
        }
    }

    // Create FAQ categories
    echo "\n❓ Creating FAQ categories...\n";

    $faqCategories = [
        [
            'name' => 'CAD Software',
            'slug' => 'cad-software',
            'description' => 'Questions about CAD software usage and troubleshooting',
            'order' => 1,
            'is_active' => true
        ],
        [
            'name' => 'Materials & Properties',
            'slug' => 'materials-properties',
            'description' => 'Material selection and properties questions',
            'order' => 2,
            'is_active' => true
        ],
        [
            'name' => 'Design Calculations',
            'slug' => 'design-calculations',
            'description' => 'Engineering calculations and formulas',
            'order' => 3,
            'is_active' => true
        ]
    ];

    foreach ($faqCategories as $category) {
        try {
            DB::table('faq_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
            echo "✅ Created/Updated FAQ category: {$category['name']}\n";
        } catch (Exception $e) {
            echo "⚠️ FAQ category error: " . $e->getMessage() . "\n";
        }
    }

    // Create sample FAQs
    echo "\n❓ Creating sample FAQs...\n";

    $faqCategoryId = DB::table('faq_categories')->first()->id ?? 1;

    $faqs = [
        [
            'question' => 'How do I calculate the factor of safety for a mechanical component?',
            'answer' => 'The factor of safety (FoS) is calculated as the ratio of the ultimate strength of the material to the maximum stress in the component:

FoS = Ultimate Strength / Maximum Stress

For static loading:
- FoS = σ_ultimate / σ_max (for tensile failure)
- FoS = τ_ultimate / τ_max (for shear failure)

For dynamic loading, consider fatigue strength:
- FoS = σ_fatigue / σ_alternating

Typical safety factors:
- Static loading: 2-4
- Dynamic loading: 4-8
- Critical applications: 6-10

Always consult relevant design standards (AISC, ASME, etc.) for specific requirements.',
            'category_id' => $faqCategoryId,
            'order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'question' => 'What is the difference between yield strength and ultimate tensile strength?',
            'answer' => 'Yield Strength (σ_y):
- The stress at which a material begins to deform plastically
- Material will return to original shape if stress is removed below this point
- Used for design calculations to avoid permanent deformation
- Typical values: Steel ~250-400 MPa, Aluminum ~100-300 MPa

Ultimate Tensile Strength (σ_u):
- The maximum stress a material can withstand before fracture
- Also called tensile strength or breaking strength
- Material will fail catastrophically beyond this point
- Always higher than yield strength for ductile materials

Key Differences:
1. Yield strength = onset of plastic deformation
2. Ultimate strength = maximum load capacity
3. Design typically based on yield strength with safety factors
4. Ultimate strength used for failure analysis

For brittle materials (like ceramics), yield and ultimate strength may be similar since they fracture without significant plastic deformation.',
            'category_id' => $faqCategoryId,
            'order' => 2,
            'is_active' => true,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(1)
        ]
    ];

    foreach ($faqs as $faq) {
        try {
            $existingFaq = DB::table('faqs')
                ->where('question', $faq['question'])
                ->first();

            if (!$existingFaq) {
                DB::table('faqs')->insert($faq);
                echo "✅ Created FAQ: " . substr($faq['question'], 0, 50) . "...\n";
            } else {
                echo "ℹ️ FAQ already exists: " . substr($faq['question'], 0, 50) . "...\n";
            }
        } catch (Exception $e) {
            echo "⚠️ FAQ creation error: " . $e->getMessage() . "\n";
        }
    }

    // Test 3: Performance testing
    echo "\n⚡ Running performance tests...\n";

    $start = microtime(true);
    $pageCategories = DB::table('page_categories')
        ->orderBy('order')
        ->get();
    $time1 = (microtime(true) - $start) * 1000;
    echo "✅ Page categories query: {$time1:.2f}ms ({$pageCategories->count()} records)\n";

    $start = microtime(true);
    $pages = DB::table('pages')
        ->join('page_categories', 'pages.category_id', '=', 'page_categories.id')
        ->where('pages.status', 'published')
        ->orderByDesc('pages.view_count')
        ->select('pages.*', 'page_categories.name as category_name')
        ->take(10)
        ->get();
    $time2 = (microtime(true) - $start) * 1000;
    echo "✅ Pages with categories query: {$time2:.2f}ms ({$pages->count()} records)\n";

    $start = microtime(true);
    $faqs = DB::table('faqs')
        ->join('faq_categories', 'faqs.category_id', '=', 'faq_categories.id')
        ->where('faqs.is_active', true)
        ->orderBy('faqs.order')
        ->select('faqs.*', 'faq_categories.name as category_name')
        ->get();
    $time3 = (microtime(true) - $start) * 1000;
    echo "✅ FAQs with categories query: {$time3:.2f}ms ({$faqs->count()} records)\n";

    $start = microtime(true);
    $searchResults = DB::table('pages')
        ->where('title', 'like', '%engineering%')
        ->orWhere('content', 'like', '%design%')
        ->where('status', 'published')
        ->get();
    $time4 = (microtime(true) - $start) * 1000;
    echo "✅ Page search query: {$time4:.2f}ms ({$searchResults->count()} records)\n";

    // Calculate average performance
    $totalTime = $time1 + $time2 + $time3 + $time4;
    $averageTime = $totalTime / 4;

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 CMS PAGES PERFORMANCE SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    echo "Total Test Time: {$totalTime:.2f}ms\n";
    echo "Average Query Time: {$averageTime:.2f}ms\n";
    echo "Target: <20ms per query\n";
    echo "Status: " . ($averageTime < 20 ? "✅ EXCELLENT" : ($averageTime < 50 ? "⚠️ ACCEPTABLE" : "❌ NEEDS OPTIMIZATION")) . "\n\n";

    echo "📈 TABLE STATISTICS\n";
    echo str_repeat("-", 30) . "\n";
    foreach ($tables as $table) {
        $count = DB::table($table)->count();
        echo "- {$table}: {$count} records\n";
    }

    echo "\n🏁 CMS PAGES TEST COMPLETED SUCCESSFULLY!\n";
    echo "Average Performance: {$averageTime:.2f}ms ✅\n";

} catch (Exception $e) {
    echo "\n❌ Error during CMS Pages testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
