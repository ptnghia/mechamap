<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showcase;
use App\Models\Thread;
use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ShowcaseSeeder extends Seeder
{
    /**
     * Seed showcases với project thực tế trong ngành cơ khí
     * Tạo portfolio projects cho users
     */
    public function run(): void
    {
        $this->command->info('🏆 Bắt đầu seed showcases với projects cơ khí...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();
        $showcaseImages = Media::where('file_path', 'like', '%/showcase/%')->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ Cần có users trước khi seed showcases!');
            return;
        }

        // Tạo showcases cho users
        $this->createShowcases($users, $threads, $showcaseImages);

        $this->command->info('✅ Hoàn thành seed showcases!');
    }

    private function createShowcases($users, $threads, $showcaseImages): void
    {
        $showcaseData = $this->getShowcaseProjects();

        foreach ($showcaseData as $index => $projectData) {
            // Random user và image
            $author = $users->random();
            $image = $showcaseImages->isNotEmpty() ? $showcaseImages->random() : null;

            // Random thread để link (polymorphic relationship)
            $linkedThread = $threads->isNotEmpty() ? $threads->random() : null;

            $showcase = Showcase::create([
                'user_id' => $author->id,
                'showcaseable_type' => $linkedThread ? 'App\\Models\\Thread' : null,
                'showcaseable_id' => $linkedThread ? $linkedThread->id : null,
                'title' => $projectData['title'],
                'slug' => Str::slug($projectData['title']) . '-' . $author->id . '-' . rand(100, 999),
                'description' => $projectData['description'],
                'project_type' => $projectData['project_type'],
                'software_used' => json_encode($projectData['software_used']),
                'materials' => json_encode($projectData['materials']),
                'manufacturing_process' => json_encode($projectData['manufacturing_process']),
                'technical_specs' => json_encode($projectData['technical_specs']),
                'category' => $projectData['category'],
                'complexity_level' => $projectData['complexity_level'],
                'industry_application' => $projectData['industry_application'],
                'has_tutorial' => $projectData['has_tutorial'],
                'has_calculations' => $projectData['has_calculations'],
                'has_cad_files' => $projectData['has_cad_files'],
                'learning_objectives' => json_encode($projectData['learning_objectives']),
                'cover_image' => $image ? $image->file_path : null,
                'image_gallery' => $image ? json_encode([$image->file_path]) : null,
                'file_attachments' => json_encode($projectData['file_attachments']),
                'status' => $this->getRandomStatus(),
                'is_public' => true,
                'allow_downloads' => $projectData['allow_downloads'],
                'allow_comments' => true,
                'view_count' => rand(50, 1000),
                'like_count' => 0, // Sẽ update sau
                'download_count' => rand(0, 100),
                'share_count' => rand(0, 50),
                'rating_average' => rand(350, 500) / 100, // 3.5 - 5.0
                'rating_count' => rand(5, 30),
                'technical_quality_score' => rand(400, 500) / 100, // 4.0 - 5.0
                'display_order' => $index,
                'featured_at' => $projectData['featured'] ? now()->subDays(rand(1, 30)) : null,
                'approved_at' => now()->subDays(rand(1, 10)),
                'approved_by' => $users->where('role', 'admin')->first()?->id,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);

            $this->command->line("   🏆 Tạo showcase: {$showcase->title}");
        }
    }

    private function getShowcaseProjects(): array
    {
        return [
            [
                'title' => 'Thiết kế và Phân tích Cầu Trục 5 Tấn',
                'description' => "Dự án thiết kế hoàn chỉnh cầu trục 5 tấn cho nhà máy sản xuất.\n\n**Scope công việc:**\n- Thiết kế 3D với SolidWorks\n- Phân tích FEA với ANSYS\n- Tính toán kết cấu thép\n- Bản vẽ chế tạo chi tiết\n\n**Kết quả đạt được:**\n- Giảm 15% trọng lượng so với thiết kế cũ\n- Tăng 20% độ an toàn\n- Tiết kiệm 30% chi phí vật liệu",
                'project_type' => 'design',
                'software_used' => ['SolidWorks', 'ANSYS Mechanical', 'AutoCAD'],
                'materials' => ['Steel S355', 'Steel S235', 'Bearing SKF'],
                'manufacturing_process' => ['Welding', 'Machining', 'Assembly'],
                'technical_specs' => [
                    'capacity' => '5000 kg',
                    'span' => '12 m',
                    'lifting_height' => '8 m',
                    'safety_factor' => '2.5'
                ],
                'category' => 'design',
                'complexity_level' => 'advanced',
                'industry_application' => 'manufacturing',
                'has_tutorial' => true,
                'has_calculations' => true,
                'has_cad_files' => true,
                'learning_objectives' => ['Structural Analysis', 'Steel Design', 'FEA Simulation'],
                'file_attachments' => ['crane_assembly.SLDASM', 'stress_analysis.pdf', 'calculations.xlsx'],
                'allow_downloads' => true,
                'featured' => true
            ],
            [
                'title' => 'Tối ưu hóa Toolpath CNC cho Aluminum Aerospace',
                'description' => "Nghiên cứu tối ưu hóa toolpath CNC cho gia công chi tiết aluminum hàng không.\n\n**Phương pháp:**\n- Sử dụng Mastercam 2023\n- Adaptive milling strategies\n- High-speed machining parameters\n- Surface finish optimization\n\n**Kết quả:**\n- Giảm 40% thời gian gia công\n- Cải thiện Ra từ 1.6 xuống 0.8 μm\n- Tăng tool life 60%",
                'project_type' => 'manufacturing',
                'software_used' => ['Mastercam', 'Vericut', 'SolidWorks'],
                'materials' => ['Aluminum 7075-T6', 'Aluminum 6061-T6'],
                'manufacturing_process' => ['CNC Milling', 'High-Speed Machining'],
                'technical_specs' => [
                    'surface_finish' => 'Ra 0.8 μm',
                    'tolerance' => '±0.02 mm',
                    'material_removal_rate' => '120 cm³/min'
                ],
                'category' => 'manufacturing',
                'complexity_level' => 'intermediate',
                'industry_application' => 'aerospace',
                'has_tutorial' => true,
                'has_calculations' => true,
                'has_cad_files' => true,
                'learning_objectives' => ['CNC Programming', 'Toolpath Optimization', 'Surface Finish'],
                'file_attachments' => ['toolpath.mcx', 'speeds_feeds.pdf', 'results.xlsx'],
                'allow_downloads' => false,
                'featured' => true
            ],
            [
                'title' => 'Robot ABB Welding Cell - Automotive Frame',
                'description' => "Thiết kế và lập trình robot cell hàn khung xe ô tô.\n\n**Hệ thống:**\n- Robot ABB IRB 1600\n- Welding gun Fronius\n- Vision system Cognex\n- Safety PLC Siemens\n\n**Programming:**\n- RAPID programming\n- Path optimization\n- Quality control integration\n- Cycle time: 45 seconds/part",
                'project_type' => 'automation',
                'software_used' => ['RobotStudio', 'TIA Portal', 'SolidWorks'],
                'materials' => ['Steel SPCC', 'Welding Wire ER70S-6'],
                'manufacturing_process' => ['Robot Welding', 'Assembly'],
                'technical_specs' => [
                    'cycle_time' => '45 seconds',
                    'weld_quality' => 'ISO 3834-2',
                    'repeatability' => '±0.1 mm'
                ],
                'category' => 'automation',
                'complexity_level' => 'advanced',
                'industry_application' => 'automotive',
                'has_tutorial' => true,
                'has_calculations' => false,
                'has_cad_files' => true,
                'learning_objectives' => ['Robot Programming', 'Welding Automation', 'Quality Control'],
                'file_attachments' => ['robot_program.prg', 'layout.dwg', 'cycle_study.pdf'],
                'allow_downloads' => false,
                'featured' => false
            ],
            [
                'title' => 'Phân tích CFD Cooling System Động cơ',
                'description' => "Mô phỏng CFD hệ thống làm mát động cơ ô tô.\n\n**Mô hình:**\n- 3D CAD từ SolidWorks\n- Mesh với ANSYS Fluent\n- Turbulence model k-ε\n- Heat transfer analysis\n\n**Kết quả:**\n- Nhiệt độ max giảm 15°C\n- Pressure drop giảm 20%\n- Flow uniformity cải thiện 25%",
                'project_type' => 'analysis',
                'software_used' => ['ANSYS Fluent', 'SolidWorks', 'ANSYS Meshing'],
                'materials' => ['Coolant 50/50', 'Aluminum Radiator'],
                'manufacturing_process' => ['CFD Analysis'],
                'technical_specs' => [
                    'max_temperature' => '85°C',
                    'flow_rate' => '120 L/min',
                    'pressure_drop' => '15 kPa'
                ],
                'category' => 'analysis',
                'complexity_level' => 'advanced',
                'industry_application' => 'automotive',
                'has_tutorial' => true,
                'has_calculations' => true,
                'has_cad_files' => true,
                'learning_objectives' => ['CFD Analysis', 'Heat Transfer', 'Flow Optimization'],
                'file_attachments' => ['cfd_model.wbpj', 'results.pdf', 'mesh_study.xlsx'],
                'allow_downloads' => true,
                'featured' => true
            ],
            [
                'title' => 'Thiết kế Jig & Fixture cho Gia công CNC',
                'description' => "Thiết kế đồ gá chuyên dụng cho gia công batch production.\n\n**Yêu cầu:**\n- Clamping force 5000N\n- Repeatability ±0.005mm\n- Quick change < 30 seconds\n- Cost effective solution\n\n**Giải pháp:**\n- Modular design\n- Pneumatic clamping\n- Hardened locating pins\n- Error-proof loading",
                'project_type' => 'design',
                'software_used' => ['SolidWorks', 'AutoCAD'],
                'materials' => ['Steel 4140', 'Hardened Pins', 'Pneumatic Cylinders'],
                'manufacturing_process' => ['CNC Machining', 'Heat Treatment', 'Assembly'],
                'technical_specs' => [
                    'clamping_force' => '5000 N',
                    'repeatability' => '±0.005 mm',
                    'setup_time' => '< 30 seconds'
                ],
                'category' => 'design',
                'complexity_level' => 'intermediate',
                'industry_application' => 'manufacturing',
                'has_tutorial' => true,
                'has_calculations' => true,
                'has_cad_files' => true,
                'learning_objectives' => ['Fixture Design', 'Clamping Systems', 'Manufacturing'],
                'file_attachments' => ['fixture.SLDASM', 'drawings.dwg', 'force_calc.pdf'],
                'allow_downloads' => true,
                'featured' => false
            ]
        ];
    }

    private function getRandomStatus(): string
    {
        $statuses = ['approved', 'featured', 'approved', 'approved']; // 75% approved, 25% featured
        return $statuses[array_rand($statuses)];
    }
}
