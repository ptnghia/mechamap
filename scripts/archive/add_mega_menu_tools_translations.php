<?php
/**
 * Add Mega Menu Tools Translation Keys
 * Script để thêm nhanh các translation keys cho mega menu footer tools
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🛠️ Adding Mega Menu Tools Translation Keys...\n";
echo "===============================================\n";

// Translation keys cần thêm cho mega menu footer
$translationKeys = [
    // Tools section
    'navigation.tools.title' => [
        'vi' => 'Công cụ Chuyên ngành',
        'en' => 'Professional Tools',
        'group' => 'navigation'
    ],
    'navigation.tools.calculators' => [
        'vi' => 'Máy tính',
        'en' => 'Calculators',
        'group' => 'navigation'
    ],
    'navigation.tools.databases' => [
        'vi' => 'Cơ sở dữ liệu',
        'en' => 'Databases',
        'group' => 'navigation'
    ],
    'navigation.tools.libraries' => [
        'vi' => 'Thư viện',
        'en' => 'Libraries',
        'group' => 'navigation'
    ],

    // Calculator tools
    'navigation.tools.material_calculator' => [
        'vi' => 'Tính toán vật liệu',
        'en' => 'Material Calculator',
        'group' => 'navigation'
    ],
    'navigation.tools.process_calculator' => [
        'vi' => 'Tính toán quy trình',
        'en' => 'Process Calculator',
        'group' => 'navigation'
    ],

    // Database tools
    'navigation.tools.materials_db' => [
        'vi' => 'Database vật liệu',
        'en' => 'Materials Database',
        'group' => 'navigation'
    ],
    'navigation.tools.standards_db' => [
        'vi' => 'Database tiêu chuẩn',
        'en' => 'Standards Database',
        'group' => 'navigation'
    ],
    'navigation.tools.processes_db' => [
        'vi' => 'Database quy trình',
        'en' => 'Processes Database',
        'group' => 'navigation'
    ],

    // Library tools
    'navigation.tools.cad_library' => [
        'vi' => 'Thư viện CAD',
        'en' => 'CAD Library',
        'group' => 'navigation'
    ],
    'navigation.tools.technical_resources' => [
        'vi' => 'Tài nguyên kỹ thuật',
        'en' => 'Technical Resources',
        'group' => 'navigation'
    ],
    'navigation.tools.documentation' => [
        'vi' => 'Tài liệu',
        'en' => 'Documentation',
        'group' => 'navigation'
    ]
];

$totalAdded = 0;
$totalSkipped = 0;

foreach ($translationKeys as $key => $data) {
    echo "\n📝 Processing key: {$key}\n";

    // Check if key already exists
    $existingVi = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'vi')
        ->first();

    $existingEn = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'en')
        ->first();

    if ($existingVi && $existingEn) {
        echo "   ⏭️ Skipped: Key already exists\n";
        $totalSkipped++;
        continue;
    }

    // Add Vietnamese translation
    if (!$existingVi) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $data['vi'],
            'locale' => 'vi',
            'group_name' => $data['group'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added VI: {$data['vi']}\n";
    }

    // Add English translation
    if (!$existingEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $data['en'],
            'locale' => 'en',
            'group_name' => $data['group'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added EN: {$data['en']}\n";
    }

    $totalAdded++;
}

echo "\n🎉 SUMMARY:\n";
echo "===========\n";
echo "✅ Keys processed: " . count($translationKeys) . "\n";
echo "✅ Keys added: {$totalAdded}\n";
echo "⏭️ Keys skipped: {$totalSkipped}\n";
echo "\n🚀 Mega menu tools translation keys have been added successfully!\n";
echo "Now you can use these keys in your mega menu footer.\n";
