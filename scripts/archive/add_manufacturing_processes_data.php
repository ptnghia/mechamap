<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Adding Manufacturing Processes Data...\n";

// Manufacturing processes data
$processes = [
    [
        'uuid' => \Illuminate\Support\Str::uuid(),
        'name' => 'CNC Milling',
        'code' => 'CNC-MILL-001',
        'description' => 'Computer Numerical Control milling process for precision machining of complex parts with high accuracy and repeatability.',
        'category' => 'Machining',
        'subcategory' => 'CNC Machining',
        'process_type' => 'Subtractive',
        'alternative_names' => json_encode(['Computer Numerical Control Milling', 'CNC Machining']),
        'materials_compatible' => json_encode(['Steel', 'Aluminum', 'Titanium', 'Plastic', 'Brass', 'Copper']),
        'material_limitations' => json_encode(['Very hard materials may require special tooling', 'Some plastics may melt due to heat']),
        'dimensional_capabilities' => json_encode(['Length: 0.1mm - 2000mm', 'Width: 0.1mm - 1500mm', 'Height: 0.1mm - 800mm']),
        'surface_finish_range' => json_encode(['Ra 0.8-3.2 μm', 'Can achieve mirror finish with proper tooling']),
        'tolerance_capabilities' => json_encode(['Standard: ±0.05mm', 'Precision: ±0.01mm', 'Ultra-precision: ±0.005mm']),
        'required_equipment' => json_encode(['CNC Milling Machine', 'Cutting tools', 'Workholding fixtures', 'CAM software']),
        'tooling_requirements' => json_encode(['End mills', 'Face mills', 'Drill bits', 'Reamers', 'Taps']),
        'setup_requirements' => json_encode(['CAD/CAM programming', 'Tool setup', 'Workpiece fixturing', 'Machine calibration']),
        'operating_parameters' => json_encode(['Spindle speed: 100-20000 RPM', 'Feed rate: 10-5000 mm/min', 'Depth of cut: 0.1-10mm']),
        'parameter_ranges' => json_encode(['Speed varies by material and tool', 'Feed rate depends on surface finish requirements']),
        'optimization_guidelines' => json_encode(['Use proper speeds and feeds', 'Maintain sharp tools', 'Adequate coolant flow']),
        'geometric_capabilities' => json_encode(['Complex 3D surfaces', 'Pockets and cavities', 'Holes and threads', 'Contoured surfaces']),
        'geometric_limitations' => json_encode(['Internal corners have radius limitation', 'Deep narrow slots challenging']),
        'min_feature_size' => 0.1000,
        'max_part_size' => 2000.00,
        'complexity_rating' => json_encode(['Simple: 1-3', 'Moderate: 4-6', 'Complex: 7-10']),
        'quality_standards' => json_encode(['ISO 9001', 'AS9100', 'ISO 13485']),
        'inspection_methods' => json_encode(['CMM measurement', 'Surface roughness testing', 'Dimensional inspection']),
        'typical_defects' => json_encode(['Tool marks', 'Dimensional deviation', 'Surface roughness', 'Burrs']),
        'prevention_methods' => json_encode(['Proper tool selection', 'Optimal cutting parameters', 'Regular tool maintenance']),
        'setup_cost' => 150.00,
        'unit_cost_factor' => 0.75,
        'minimum_quantity' => 1,
        'cost_drivers' => json_encode(['Material cost', 'Machining time', 'Tool wear', 'Setup complexity']),
        'setup_time_hours' => 2.5,
        'cycle_time_factor' => 1.2,
        'production_rate_factors' => json_encode(['Material removal rate', 'Part complexity', 'Surface finish requirements']),
        'lead_time_days' => 5,
        'environmental_impact' => json_encode(['Metal chips recyclable', 'Coolant disposal required', 'Energy consumption moderate']),
        'safety_requirements' => json_encode(['Eye protection', 'Hearing protection', 'Machine guarding', 'Proper ventilation']),
        'waste_products' => json_encode(['Metal chips', 'Used coolant', 'Worn tools']),
        'requires_special_handling' => false,
        'typical_applications' => json_encode(['Aerospace components', 'Automotive parts', 'Medical devices', 'Precision tooling']),
        'industries' => json_encode(['Aerospace', 'Automotive', 'Medical', 'Defense', 'Electronics']),
        'part_types' => json_encode(['Housings', 'Brackets', 'Gears', 'Fixtures', 'Prototypes']),
        'prerequisite_processes' => json_encode(['Material preparation', 'CAD design', 'CAM programming']),
        'subsequent_processes' => json_encode(['Deburring', 'Surface treatment', 'Quality inspection', 'Assembly']),
        'alternative_processes' => json_encode(['Manual milling', 'EDM', '3D printing', 'Casting + machining']),
        'tags' => json_encode(['CNC', 'Milling', 'Precision', 'Machining', 'Subtractive']),
        'keywords' => json_encode(['CNC milling', 'precision machining', 'computer numerical control', 'metal cutting']),
        'status' => 'approved',
        'is_active' => true,
        'is_featured' => true,
        'usage_count' => 0,
        'view_count' => 0,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'CNC Turning',
        'description' => 'Precision turning process using computer-controlled lathes for cylindrical parts with excellent dimensional accuracy.',
        'category' => 'Machining',
        'skill_level' => 'Intermediate',
        'cost_per_hour' => 65.00,
        'production_rate' => 'High',
        'material_compatibility' => json_encode(['Steel', 'Aluminum', 'Brass', 'Stainless Steel']),
        'advantages' => json_encode([
            'High production rates',
            'Excellent concentricity',
            'Good surface finish',
            'Cost-effective for cylindrical parts'
        ]),
        'disadvantages' => json_encode([
            'Limited to rotational parts',
            'Setup time for complex parts',
            'Tool wear considerations'
        ]),
        'applications' => json_encode([
            'Shafts and rods',
            'Bushings and sleeves',
            'Threaded components',
            'Precision pins'
        ]),
        'equipment_required' => json_encode([
            'CNC Lathe',
            'Turning tools',
            'Chuck and tailstock',
            'Measuring instruments'
        ]),
        'typical_tolerances' => '±0.02mm',
        'surface_finish' => 'Ra 1.6-6.3 μm',
        'lead_time' => '2-5 days',
        'minimum_quantity' => 1,
        'maximum_quantity' => 5000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => '3D Printing (FDM)',
        'description' => 'Fused Deposition Modeling additive manufacturing process for rapid prototyping and low-volume production.',
        'category' => 'Additive Manufacturing',
        'skill_level' => 'Basic',
        'cost_per_hour' => 25.00,
        'production_rate' => 'Low',
        'material_compatibility' => json_encode(['PLA', 'ABS', 'PETG', 'TPU']),
        'advantages' => json_encode([
            'Low cost and accessible',
            'Complex geometries possible',
            'No tooling required',
            'Rapid prototyping capability'
        ]),
        'disadvantages' => json_encode([
            'Layer lines visible',
            'Limited material options',
            'Slower production',
            'Support structures needed'
        ]),
        'applications' => json_encode([
            'Prototypes and models',
            'Custom fixtures',
            'Educational models',
            'Low-stress components'
        ]),
        'equipment_required' => json_encode([
            '3D Printer (FDM)',
            'Filament materials',
            'Slicing software',
            'Post-processing tools'
        ]),
        'typical_tolerances' => '±0.2mm',
        'surface_finish' => 'Ra 6.3-25 μm',
        'lead_time' => '1-3 days',
        'minimum_quantity' => 1,
        'maximum_quantity' => 100,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Sheet Metal Forming',
        'description' => 'Metal forming process using press brakes and forming tools to create bent and shaped sheet metal components.',
        'category' => 'Forming',
        'skill_level' => 'Intermediate',
        'cost_per_hour' => 55.00,
        'production_rate' => 'High',
        'material_compatibility' => json_encode(['Steel', 'Aluminum', 'Stainless Steel', 'Copper']),
        'advantages' => json_encode([
            'High production rates',
            'Cost-effective for thin parts',
            'Good strength-to-weight ratio',
            'Minimal material waste'
        ]),
        'disadvantages' => json_encode([
            'Limited to thin materials',
            'Spring-back considerations',
            'Tooling costs for complex shapes'
        ]),
        'applications' => json_encode([
            'Enclosures and housings',
            'Brackets and supports',
            'Automotive body panels',
            'HVAC components'
        ]),
        'equipment_required' => json_encode([
            'Press Brake',
            'Forming dies',
            'Sheet metal shears',
            'Measuring tools'
        ]),
        'typical_tolerances' => '±0.1mm',
        'surface_finish' => 'As formed',
        'lead_time' => '2-4 days',
        'minimum_quantity' => 10,
        'maximum_quantity' => 10000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Investment Casting',
        'description' => 'Precision casting process using wax patterns and ceramic molds for complex, near-net-shape components.',
        'category' => 'Casting',
        'skill_level' => 'Expert',
        'cost_per_hour' => 95.00,
        'production_rate' => 'Medium',
        'material_compatibility' => json_encode(['Steel', 'Stainless Steel', 'Titanium', 'Superalloys']),
        'advantages' => json_encode([
            'Complex geometries possible',
            'Excellent surface finish',
            'Near-net-shape capability',
            'Wide material selection'
        ]),
        'disadvantages' => json_encode([
            'High tooling costs',
            'Long lead times',
            'Size limitations',
            'Expensive for low volumes'
        ]),
        'applications' => json_encode([
            'Aerospace components',
            'Gas turbine parts',
            'Medical implants',
            'Jewelry and art'
        ]),
        'equipment_required' => json_encode([
            'Wax injection equipment',
            'Ceramic shell system',
            'Furnaces',
            'Finishing equipment'
        ]),
        'typical_tolerances' => '±0.05mm',
        'surface_finish' => 'Ra 1.6-6.3 μm',
        'lead_time' => '4-8 weeks',
        'minimum_quantity' => 50,
        'maximum_quantity' => 50000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Laser Cutting',
        'description' => 'High-precision cutting process using focused laser beam for clean cuts in various materials with minimal heat-affected zone.',
        'category' => 'Cutting',
        'skill_level' => 'Intermediate',
        'cost_per_hour' => 85.00,
        'production_rate' => 'High',
        'material_compatibility' => json_encode(['Steel', 'Aluminum', 'Stainless Steel', 'Acrylic']),
        'advantages' => json_encode([
            'High precision cutting',
            'Minimal material waste',
            'No tool wear',
            'Complex shapes possible'
        ]),
        'disadvantages' => json_encode([
            'High equipment cost',
            'Thickness limitations',
            'Heat-affected zone',
            'Safety considerations'
        ]),
        'applications' => json_encode([
            'Sheet metal fabrication',
            'Signage and displays',
            'Automotive components',
            'Electronics enclosures'
        ]),
        'equipment_required' => json_encode([
            'Laser cutting machine',
            'Assist gas system',
            'Material handling',
            'Fume extraction'
        ]),
        'typical_tolerances' => '±0.05mm',
        'surface_finish' => 'Ra 3.2-12.5 μm',
        'lead_time' => '1-3 days',
        'minimum_quantity' => 1,
        'maximum_quantity' => 1000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Injection Molding',
        'description' => 'High-volume plastic manufacturing process using heated plastic injected into precision molds for consistent parts.',
        'category' => 'Molding',
        'skill_level' => 'Advanced',
        'cost_per_hour' => 45.00,
        'production_rate' => 'Very High',
        'material_compatibility' => json_encode(['ABS', 'Polypropylene', 'Nylon', 'Polycarbonate']),
        'advantages' => json_encode([
            'Very high production rates',
            'Excellent repeatability',
            'Complex geometries',
            'Low per-part cost'
        ]),
        'disadvantages' => json_encode([
            'High tooling costs',
            'Long lead times for tooling',
            'Design constraints',
            'High minimum quantities'
        ]),
        'applications' => json_encode([
            'Consumer products',
            'Automotive components',
            'Electronic housings',
            'Medical devices'
        ]),
        'equipment_required' => json_encode([
            'Injection molding machine',
            'Precision molds',
            'Material handling',
            'Quality control equipment'
        ]),
        'typical_tolerances' => '±0.1mm',
        'surface_finish' => 'Ra 0.4-1.6 μm',
        'lead_time' => '6-12 weeks',
        'minimum_quantity' => 1000,
        'maximum_quantity' => 1000000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Welding (TIG)',
        'description' => 'Tungsten Inert Gas welding process for high-quality joints in various metals with excellent control and precision.',
        'category' => 'Joining',
        'skill_level' => 'Advanced',
        'cost_per_hour' => 70.00,
        'production_rate' => 'Low',
        'material_compatibility' => json_encode(['Steel', 'Stainless Steel', 'Aluminum', 'Titanium']),
        'advantages' => json_encode([
            'High-quality welds',
            'Excellent control',
            'Clean process',
            'Wide material compatibility'
        ]),
        'disadvantages' => json_encode([
            'Slow process',
            'Requires skilled operators',
            'Equipment cost',
            'Shielding gas required'
        ]),
        'applications' => json_encode([
            'Aerospace structures',
            'Pressure vessels',
            'Food processing equipment',
            'Architectural metalwork'
        ]),
        'equipment_required' => json_encode([
            'TIG welding machine',
            'Tungsten electrodes',
            'Shielding gas',
            'Safety equipment'
        ]),
        'typical_tolerances' => '±0.5mm',
        'surface_finish' => 'As welded',
        'lead_time' => '3-7 days',
        'minimum_quantity' => 1,
        'maximum_quantity' => 500,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Grinding',
        'description' => 'Precision surface finishing process using abrasive wheels to achieve tight tolerances and excellent surface quality.',
        'category' => 'Finishing',
        'skill_level' => 'Intermediate',
        'cost_per_hour' => 60.00,
        'production_rate' => 'Medium',
        'material_compatibility' => json_encode(['Steel', 'Stainless Steel', 'Tool Steel', 'Ceramics']),
        'advantages' => json_encode([
            'Excellent surface finish',
            'Tight tolerances',
            'Hard material capability',
            'Precise dimensional control'
        ]),
        'disadvantages' => json_encode([
            'Slow material removal',
            'Heat generation',
            'Wheel wear and dressing',
            'Limited to simple geometries'
        ]),
        'applications' => json_encode([
            'Precision tooling',
            'Bearing surfaces',
            'Gauge blocks',
            'Machine components'
        ]),
        'equipment_required' => json_encode([
            'Grinding machine',
            'Grinding wheels',
            'Coolant system',
            'Precision measuring tools'
        ]),
        'typical_tolerances' => '±0.005mm',
        'surface_finish' => 'Ra 0.1-0.8 μm',
        'lead_time' => '2-5 days',
        'minimum_quantity' => 1,
        'maximum_quantity' => 1000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Electroplating',
        'description' => 'Electrochemical process for applying thin metal coatings to improve corrosion resistance, appearance, and functionality.',
        'category' => 'Surface Treatment',
        'skill_level' => 'Intermediate',
        'cost_per_hour' => 35.00,
        'production_rate' => 'High',
        'material_compatibility' => json_encode(['Steel', 'Copper', 'Aluminum', 'Plastic (conductive)']),
        'advantages' => json_encode([
            'Uniform coating thickness',
            'Wide range of coating materials',
            'Good adhesion',
            'Cost-effective for large batches'
        ]),
        'disadvantages' => json_encode([
            'Environmental considerations',
            'Complex part geometries challenging',
            'Chemical handling requirements',
            'Thickness limitations'
        ]),
        'applications' => json_encode([
            'Decorative finishes',
            'Corrosion protection',
            'Electronic components',
            'Automotive trim'
        ]),
        'equipment_required' => json_encode([
            'Electroplating tanks',
            'Power supplies',
            'Chemical handling',
            'Waste treatment system'
        ]),
        'typical_tolerances' => '±0.01mm',
        'surface_finish' => 'Ra 0.2-1.6 μm',
        'lead_time' => '3-7 days',
        'minimum_quantity' => 10,
        'maximum_quantity' => 100000,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
];

try {
    // Check if manufacturing_processes table exists
    if (!DB::getSchemaBuilder()->hasTable('manufacturing_processes')) {
        echo "❌ Table 'manufacturing_processes' does not exist. Creating table...\n";

        // Create the table
        DB::statement("
            CREATE TABLE manufacturing_processes (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(100),
                skill_level ENUM('Basic', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Basic',
                cost_per_hour DECIMAL(8,2),
                production_rate ENUM('Very Low', 'Low', 'Medium', 'High', 'Very High') DEFAULT 'Medium',
                material_compatibility JSON,
                advantages JSON,
                disadvantages JSON,
                applications JSON,
                equipment_required JSON,
                typical_tolerances VARCHAR(50),
                surface_finish VARCHAR(50),
                lead_time VARCHAR(50),
                minimum_quantity INT DEFAULT 1,
                maximum_quantity INT DEFAULT 1000,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                INDEX idx_category (category),
                INDEX idx_skill_level (skill_level),
                INDEX idx_production_rate (production_rate)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        echo "✅ Table 'manufacturing_processes' created successfully.\n";
    }

    // Clear existing data
    DB::table('manufacturing_processes')->truncate();
    echo "🗑️ Cleared existing manufacturing processes data.\n";

    // Insert new data
    foreach ($processes as $process) {
        DB::table('manufacturing_processes')->insert($process);
        echo "✅ Added: {$process['name']}\n";
    }

    echo "\n🎉 Successfully added " . count($processes) . " manufacturing processes!\n";
    echo "📊 Categories: Machining, Additive Manufacturing, Forming, Casting, Cutting, Molding, Joining, Finishing, Surface Treatment\n";
    echo "🎯 Skill Levels: Basic, Intermediate, Advanced, Expert\n";
    echo "⚡ Production Rates: Low, Medium, High, Very High\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Manufacturing Processes Database populated successfully!\n";
