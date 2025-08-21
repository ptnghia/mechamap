<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CADFile;
use App\Models\Material;
use App\Models\TechnicalDrawing;
use App\Models\EngineeringStandard;
use App\Models\ManufacturingProcess;
use App\Models\User;
use App\Models\MarketplaceSeller;
use Carbon\Carbon;

class CADMaterialSeeder extends Seeder
{
    public function run()
    {
        echo "🔧 Bắt đầu seed CAD files và materials...\n";

        // Get users and sellers
        $users = User::take(20)->get();
        $sellers = MarketplaceSeller::take(10)->get();

        if ($users->isEmpty()) {
            echo "❌ Không có users để tạo CAD files\n";
            return;
        }

        // Create Materials
        $materials = [
            ['name' => 'Thép Carbon', 'code' => 'ST37', 'density' => 7.85, 'tensile_strength' => 370, 'yield_strength' => 235],
            ['name' => 'Thép Không Gỉ 304', 'code' => 'SS304', 'density' => 8.0, 'tensile_strength' => 515, 'yield_strength' => 205],
            ['name' => 'Nhôm 6061', 'code' => 'AL6061', 'density' => 2.7, 'tensile_strength' => 310, 'yield_strength' => 276],
            ['name' => 'Đồng Thau', 'code' => 'BRASS', 'density' => 8.5, 'tensile_strength' => 300, 'yield_strength' => 200],
            ['name' => 'Gang Xám', 'code' => 'GG20', 'density' => 7.2, 'tensile_strength' => 200, 'yield_strength' => 150],
            ['name' => 'Thép Hợp Kim', 'code' => 'SCM440', 'density' => 7.85, 'tensile_strength' => 985, 'yield_strength' => 835],
            ['name' => 'Nhựa ABS', 'code' => 'ABS', 'density' => 1.05, 'tensile_strength' => 40, 'yield_strength' => 35],
            ['name' => 'Nhựa POM', 'code' => 'POM', 'density' => 1.41, 'tensile_strength' => 70, 'yield_strength' => 65],
            ['name' => 'Titan Grade 2', 'code' => 'TI-G2', 'density' => 4.51, 'tensile_strength' => 345, 'yield_strength' => 275],
            ['name' => 'Thép Dụng Cụ', 'code' => 'SKD11', 'density' => 7.7, 'tensile_strength' => 2000, 'yield_strength' => 1800]
        ];

        $materialCount = 0;
        foreach ($materials as $materialData) {
            Material::create([
                'name' => $materialData['name'],
                'code' => $materialData['code'],
                'description' => 'Vật liệu ' . $materialData['name'] . ' được sử dụng rộng rãi trong ngành cơ khí',
                'category' => collect(['metal', 'plastic', 'composite'])->random(),
                'subcategory' => collect(['steel', 'aluminum', 'copper', 'polymer'])->random(),
                'material_type' => collect(['structural', 'tool', 'bearing', 'electrical'])->random(),
                'grade' => $materialData['code'],
                'density' => $materialData['density'],
                'melting_point' => rand(200, 1500),
                'thermal_conductivity' => rand(10, 400) / 100,
                'youngs_modulus' => rand(70000, 210000),
                'yield_strength' => $materialData['yield_strength'],
                'tensile_strength' => $materialData['tensile_strength'],
                'hardness_hb' => rand(100, 400),
                'elongation' => rand(5, 30),
                'chemical_composition' => json_encode([
                    'C' => rand(1, 50) / 100,
                    'Si' => rand(1, 30) / 100,
                    'Mn' => rand(1, 20) / 100
                ]),
                'machinability' => json_encode([
                    'rating' => collect(['excellent', 'good', 'fair', 'difficult'])->random(),
                    'cutting_speed' => rand(50, 300) . ' m/min',
                    'tool_life' => collect(['high', 'medium', 'low'])->random()
                ]),
                'weldability' => json_encode([
                    'rating' => collect(['excellent', 'good', 'fair', 'poor'])->random(),
                    'preheating_required' => collect([true, false])->random(),
                    'post_weld_treatment' => collect(['required', 'optional', 'not_required'])->random()
                ]),
                'typical_applications' => json_encode([
                    'Chế tạo máy',
                    'Kết cấu thép',
                    'Gia công cơ khí',
                    'Sản xuất linh kiện'
                ]),
                'industries' => json_encode([
                    'Automotive',
                    'Aerospace',
                    'Construction',
                    'Manufacturing'
                ]),
                'cost_per_kg' => rand(5, 100),
                'availability' => collect(['high', 'medium', 'low'])->random(),
                'status' => 'approved',
                'is_active' => true,
                'usage_count' => rand(0, 100),
                'view_count' => rand(0, 500),
                'created_at' => Carbon::now()->subDays(rand(1, 100)),
                'updated_at' => Carbon::now()->subDays(rand(0, 10))
            ]);
            $materialCount++;
        }

        // Create Technical Drawings
        $drawingCount = 0;
        foreach ($users->take(15) as $user) {
            $drawing = TechnicalDrawing::create([
                'title' => 'Bản vẽ kỹ thuật ' . collect(['Trục', 'Bánh răng', 'Piston', 'Cam', 'Ổ bi', 'Khớp nối'])->random(),
                'drawing_number' => 'DWG-' . strtoupper(uniqid()),
                'description' => 'Bản vẽ kỹ thuật chi tiết cho sản xuất linh kiện cơ khí',
                'revision' => 'Rev-' . rand(1, 10),
                'created_by' => $user->id,
                'company_id' => $sellers->isNotEmpty() ? $sellers->random()->id : null,
                'file_path' => 'drawings/' . date('Y/m/d') . '/',
                'file_name' => 'drawing_' . strtolower(uniqid()) . '.dwg',
                'file_type' => collect(['dwg', 'pdf', 'dxf'])->random(),
                'file_size' => rand(1024, 5242880), // 1KB to 5MB
                'mime_type' => 'application/octet-stream',
                'drawing_type' => collect(['assembly', 'detail', 'schematic', 'layout'])->random(),
                'scale' => collect(['1:1', '1:2', '1:5', '1:10', '2:1'])->random(),
                'units' => collect(['mm', 'inch', 'cm'])->random(),
                'project_name' => 'Project ' . rand(1000, 9999),
                'part_number' => 'PN-' . strtoupper(uniqid()),
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
                'updated_at' => Carbon::now()->subDays(rand(0, 10))
            ]);
            $drawingCount++;
        }

        // Create CAD Files
        $cadCount = 0;
        foreach ($users->take(20) as $user) {
            $drawing = TechnicalDrawing::inRandomOrder()->first();

            CADFile::create([
                'filename' => 'cad_file_' . strtolower(uniqid()) . '.dwg',
                'original_filename' => collect(['bearing', 'gear', 'shaft', 'housing', 'bracket'])->random() . '_design.dwg',
                'file_path' => 'cad_files/' . date('Y/m/d') . '/',
                'file_size' => rand(1024, 10485760), // 1KB to 10MB
                'file_type' => collect(['dwg', 'step', 'iges', 'stl', 'obj'])->random(),
                'mime_type' => 'application/octet-stream',
                'title' => 'CAD File - ' . collect(['Mechanical Part', 'Assembly', 'Component', 'Tool Design'])->random(),
                'description' => 'File CAD chi tiết cho sản xuất và gia công cơ khí',
                'version' => 'v' . rand(1, 5) . '.' . rand(0, 9),
                'software_used' => collect(['AutoCAD', 'SolidWorks', 'Inventor', 'Fusion 360', 'CATIA'])->random(),
                'software_version' => '2023.' . rand(1, 12),
                'created_by' => $user->id,
                'company_id' => $sellers->isNotEmpty() ? $sellers->random()->id : null,
                'technical_drawing_id' => $drawing ? $drawing->id : null,
                'category' => collect(['mechanical', 'structural', 'electrical', 'piping'])->random(),
                'complexity_level' => collect(['beginner', 'intermediate', 'advanced', 'expert'])->random(),
                'download_count' => rand(0, 100),
                'view_count' => rand(0, 500),
                'rating' => rand(30, 50) / 10, // 3.0 to 5.0
                'tags' => json_encode(collect(['cơ khí', 'thiết kế', 'sản xuất', 'gia công', '3D', 'kỹ thuật'])->random(3)->toArray()),
                'metadata' => json_encode([
                    'units' => collect(['mm', 'inch', 'cm'])->random(),
                    'precision' => '0.01mm',
                    'coordinate_system' => 'Cartesian',
                    'materials' => Material::inRandomOrder()->take(rand(1, 2))->pluck('name')->toArray()
                ]),
                'is_public' => collect([true, false])->random(),
                'is_downloadable' => collect([true, false])->random(),
                'requires_approval' => collect([true, false])->random(),
                'approval_status' => collect(['pending', 'approved', 'rejected'])->random(),
                'approved_by' => collect([null, $users->random()->id])->random(),
                'approved_at' => collect([null, Carbon::now()->subDays(rand(1, 20))])->random(),
                'created_at' => Carbon::now()->subDays(rand(1, 90)),
                'updated_at' => Carbon::now()->subDays(rand(0, 15))
            ]);
            $cadCount++;
        }

        // Create Engineering Standards
        $standards = [
            ['code' => 'ISO 9001', 'title' => 'Quality Management Systems', 'category' => 'quality'],
            ['code' => 'ISO 14001', 'title' => 'Environmental Management', 'category' => 'environment'],
            ['code' => 'ASME BPVC', 'title' => 'Boiler and Pressure Vessel Code', 'category' => 'pressure_vessel'],
            ['code' => 'DIN 912', 'title' => 'Socket Head Cap Screws', 'category' => 'fasteners'],
            ['code' => 'JIS B 1180', 'title' => 'Hexagon Socket Head Cap Screws', 'category' => 'fasteners'],
            ['code' => 'ANSI B18.3', 'title' => 'Socket Screws', 'category' => 'fasteners'],
            ['code' => 'ISO 2768', 'title' => 'General Tolerances', 'category' => 'tolerances'],
            ['code' => 'ASME Y14.5', 'title' => 'Dimensioning and Tolerancing', 'category' => 'tolerances']
        ];

        $standardCount = 0;
        foreach ($standards as $standardData) {
            EngineeringStandard::create([
                'code' => $standardData['code'],
                'title' => $standardData['title'],
                'category' => $standardData['category'],
                'organization' => collect(['ISO', 'ASME', 'DIN', 'JIS', 'ANSI'])->random(),
                'version' => '2023',
                'status' => collect(['active', 'superseded', 'withdrawn'])->random(),
                'description' => 'Tiêu chuẩn kỹ thuật ' . $standardData['title'],
                'scope' => 'Áp dụng cho thiết kế và sản xuất trong ngành cơ khí',
                'applications' => json_encode([
                    'Thiết kế cơ khí',
                    'Sản xuất',
                    'Kiểm tra chất lượng',
                    'Đảm bảo an toàn'
                ]),
                'related_standards' => json_encode(['ISO 9001', 'ASME BPVC']),
                'effective_date' => Carbon::now()->subYears(rand(1, 5)),
                'review_date' => Carbon::now()->addYears(rand(1, 3)),
                'created_at' => Carbon::now()->subDays(rand(1, 200)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30))
            ]);
            $standardCount++;
        }

        // Create Manufacturing Processes
        $processes = [
            'Tiện', 'Phay', 'Khoan', 'Doa', 'Hàn', 'Cắt Plasma', 'Uốn', 'Dập', 'Đúc', 'Rèn'
        ];

        $processCount = 0;
        foreach ($processes as $processName) {
            ManufacturingProcess::create([
                'name' => $processName,
                'category' => collect(['machining', 'forming', 'joining', 'casting', 'additive'])->random(),
                'description' => 'Quy trình ' . $processName . ' trong sản xuất cơ khí',
                'equipment_required' => json_encode([
                    'Máy ' . strtolower($processName),
                    'Dụng cụ cắt',
                    'Đồ gá',
                    'Thiết bị đo'
                ]),
                'materials_compatible' => json_encode(Material::inRandomOrder()->take(rand(3, 6))->pluck('name')->toArray()),
                'typical_tolerances' => json_encode([
                    'dimensional' => '±0.1mm',
                    'surface_finish' => 'Ra 3.2',
                    'geometric' => '±0.05mm'
                ]),
                'production_rate' => rand(10, 1000) . ' parts/hour',
                'setup_time' => rand(15, 120) . ' minutes',
                'cycle_time' => rand(1, 60) . ' minutes',
                'cost_per_hour' => rand(50, 500),
                'skill_level_required' => collect(['basic', 'intermediate', 'advanced', 'expert'])->random(),
                'safety_requirements' => json_encode([
                    'Kính bảo hộ',
                    'Găng tay an toàn',
                    'Giày bảo hộ',
                    'Quần áo bảo hộ'
                ]),
                'quality_control_points' => json_encode([
                    'Kiểm tra kích thước',
                    'Kiểm tra bề mặt',
                    'Kiểm tra độ cứng',
                    'Kiểm tra hình học'
                ]),
                'advantages' => json_encode([
                    'Độ chính xác cao',
                    'Tốc độ sản xuất nhanh',
                    'Chi phí thấp'
                ]),
                'limitations' => json_encode([
                    'Giới hạn kích thước',
                    'Yêu cầu kỹ năng cao',
                    'Chi phí thiết bị'
                ]),
                'created_at' => Carbon::now()->subDays(rand(1, 150)),
                'updated_at' => Carbon::now()->subDays(rand(0, 20))
            ]);
            $processCount++;
        }

        echo "✅ Đã tạo $materialCount materials\n";
        echo "✅ Đã tạo $drawingCount technical drawings\n";
        echo "✅ Đã tạo $cadCount CAD files\n";
        echo "✅ Đã tạo $standardCount engineering standards\n";
        echo "✅ Đã tạo $processCount manufacturing processes\n";
        echo "🔧 Hoàn thành seed CAD files và materials!\n";
    }
}
