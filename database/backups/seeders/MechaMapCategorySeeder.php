<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class MechaMapCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed mechanical engineering categories for MechaMap forum
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thiết kế Cơ khí',
                'slug' => 'thiet-ke-co-khi',
                'description' => 'Thảo luận về thiết kế sản phẩm cơ khí, nguyên lý hoạt động, và phương pháp tính toán thiết kế',
                'icon' => 'https://api.iconify.design/material-symbols:engineering.svg',
                'color_code' => '#1976D2',
                'meta_description' => 'Cộng đồng thiết kế cơ khí - Chia sẻ kinh nghiệm thiết kế, tính toán và phân tích kỹ thuật',
                'meta_keywords' => 'thiết kế cơ khí, mechanical design, CAD, kỹ thuật cơ khí, tính toán thiết kế',
                'is_technical' => true,
                'expertise_level' => 'intermediate',
                'requires_verification' => false,
                'allowed_file_types' => json_encode(['dwg', 'step', 'iges', 'pdf', 'doc', 'jpg', 'png']),
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'CAD/CAM Software',
                        'slug' => 'cad-cam-software',
                        'description' => 'Thảo luận về AutoCAD, SolidWorks, CATIA, Fusion 360, và các phần mềm CAD/CAM khác',
                        'color_code' => '#2196F3',
                        'expertise_level' => 'beginner',
                        'allowed_file_types' => json_encode(['dwg', 'step', 'iges', 'ipt', 'sldprt', 'f3d']),
                    ],
                    [
                        'name' => 'Phân tích FEA/CFD',
                        'slug' => 'phan-tich-fea-cfd',
                        'description' => 'Finite Element Analysis, Computational Fluid Dynamics với ANSYS, Abaqus, SolidWorks Simulation',
                        'color_code' => '#3F51B5',
                        'expertise_level' => 'advanced',
                        'requires_verification' => true,
                        'allowed_file_types' => json_encode(['anf', 'inp', 'cae', 'pdf', 'xlsx']),
                    ],
                    [
                        'name' => 'Thiết kế máy móc',
                        'slug' => 'thiet-ke-may-moc',
                        'description' => 'Thiết kế hệ thống cơ khí, máy móc công nghiệp, thiết bị tự động',
                        'color_code' => '#4CAF50',
                        'expertise_level' => 'intermediate',
                    ]
                ]
            ],
            [
                'name' => 'Công nghệ Chế tạo',
                'slug' => 'cong-nghe-che-tao',
                'description' => 'Các phương pháp gia công, công nghệ sản xuất, quy trình chế tạo trong công nghiệp',
                'icon' => 'https://api.iconify.design/material-symbols:precision-manufacturing.svg',
                'color_code' => '#FF5722',
                'meta_description' => 'Cộng đồng công nghệ chế tạo - Chia sẻ kinh nghiệm gia công, CNC, công nghệ sản xuất',
                'meta_keywords' => 'công nghệ chế tạo, CNC machining, gia công cơ khí, manufacturing technology',
                'is_technical' => true,
                'expertise_level' => 'intermediate',
                'requires_verification' => false,
                'allowed_file_types' => json_encode(['nc', 'tap', 'pdf', 'doc', 'jpg', 'mp4']),
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'CNC Machining',
                        'slug' => 'cnc-machining',
                        'description' => 'Gia công CNC, lập trình G-code, CAM, setup máy và tối ưu thông số gia công',
                        'color_code' => '#FF7043',
                        'expertise_level' => 'intermediate',
                        'allowed_file_types' => json_encode(['nc', 'tap', 'mpf', 'cnc', 'pdf']),
                    ],
                    [
                        'name' => 'Gia công truyền thống',
                        'slug' => 'gia-cong-truyen-thong',
                        'description' => 'Gia công trên máy tiện, máy phay, máy bào, máy mài và các phương pháp gia công thủ công',
                        'color_code' => '#FF8A65',
                        'expertise_level' => 'beginner',
                    ],
                    [
                        'name' => 'In 3D & Additive Manufacturing',
                        'slug' => 'in-3d-additive',
                        'description' => 'Công nghệ in 3D, SLA, SLS, FDM, và các phương pháp sản xuất cộng dồn',
                        'color_code' => '#FF9800',
                        'expertise_level' => 'intermediate',
                        'allowed_file_types' => json_encode(['stl', 'obj', '3mf', 'gcode', 'pdf']),
                    ]
                ]
            ],
            [
                'name' => 'Vật liệu Kỹ thuật',
                'slug' => 'vat-lieu-ky-thuat',
                'description' => 'Thảo luận về tính chất, ứng dụng và lựa chọn các loại vật liệu kỹ thuật',
                'icon' => 'https://api.iconify.design/material-symbols:science.svg',
                'color_code' => '#9C27B0',
                'meta_description' => 'Cộng đồng vật liệu kỹ thuật - Nghiên cứu tính chất vật liệu, lựa chọn vật liệu cho thiết kế',
                'meta_keywords' => 'vật liệu kỹ thuật, engineering materials, kim loại, composite, polymer',
                'is_technical' => true,
                'expertise_level' => 'advanced',
                'requires_verification' => true,
                'allowed_file_types' => json_encode(['pdf', 'xlsx', 'doc', 'jpg', 'png']),
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Kim loại & Hợp kim',
                        'slug' => 'kim-loai-hop-kim',
                        'description' => 'Thép, nhôm, đồng, titan và các hợp kim kỹ thuật, xử lý nhiệt',
                        'color_code' => '#673AB7',
                        'expertise_level' => 'intermediate',
                    ],
                    [
                        'name' => 'Polymer & Composite',
                        'slug' => 'polymer-composite',
                        'description' => 'Vật liệu polyme, composite, sợi carbon, sợi thủy tinh',
                        'color_code' => '#9C27B0',
                        'expertise_level' => 'advanced',
                    ],
                    [
                        'name' => 'Vật liệu Smart',
                        'slug' => 'vat-lieu-smart',
                        'description' => 'Vật liệu thông minh, shape memory alloy, piezoelectric materials',
                        'color_code' => '#E91E63',
                        'expertise_level' => 'expert',
                        'requires_verification' => true,
                    ]
                ]
            ],
            [
                'name' => 'Tự động hóa & Robotics',
                'slug' => 'tu-dong-hoa-robotics',
                'description' => 'Hệ thống tự động hóa, robot công nghiệp, IoT và điều khiển thông minh',
                'icon' => 'https://api.iconify.design/material-symbols:smart-toy-outline.svg',
                'color_code' => '#607D8B',
                'meta_description' => 'Cộng đồng tự động hóa & robotics - PLC, HMI, robot công nghiệp, IoT',
                'meta_keywords' => 'tự động hóa, robotics, PLC, HMI, industrial automation, IoT',
                'is_technical' => true,
                'expertise_level' => 'advanced',
                'requires_verification' => false,
                'allowed_file_types' => json_encode(['pdf', 'doc', 'zip', 'jpg', 'mp4']),
                'is_active' => true,
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'PLC & HMI',
                        'slug' => 'plc-hmi',
                        'description' => 'Lập trình PLC, thiết kế HMI, SCADA, Siemens, Allen-Bradley, Mitsubishi',
                        'color_code' => '#546E7A',
                        'expertise_level' => 'intermediate',
                    ],
                    [
                        'name' => 'Robot công nghiệp',
                        'slug' => 'robot-cong-nghiep',
                        'description' => 'Robot 6 trục, lập trình robot, ứng dụng robot trong sản xuất',
                        'color_code' => '#78909C',
                        'expertise_level' => 'advanced',
                    ],
                    [
                        'name' => 'Sensors & Actuators',
                        'slug' => 'sensors-actuators',
                        'description' => 'Cảm biến công nghiệp, actuator, servo motor, stepper motor',
                        'color_code' => '#90A4AE',
                        'expertise_level' => 'intermediate',
                    ]
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            // Create parent category
            $parent = Category::create($categoryData);

            // Create child categories
            foreach ($children as $childData) {
                Category::create([
                    'name' => $childData['name'],
                    'slug' => $childData['slug'],
                    'description' => $childData['description'],
                    'parent_id' => $parent->id,
                    'icon' => 'https://api.iconify.design/material-symbols:topic.svg',
                    'color_code' => $childData['color_code'] ?? $parent->color_code,
                    'meta_description' => "Thảo luận về {$childData['name']} - {$childData['description']}",
                    'meta_keywords' => $childData['name'] . ', ' . $parent->meta_keywords,
                    'is_technical' => true,
                    'expertise_level' => $childData['expertise_level'] ?? 'intermediate',
                    'requires_verification' => $childData['requires_verification'] ?? false,
                    'allowed_file_types' => $childData['allowed_file_types'] ?? $parent->allowed_file_types,
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
            }
        }

        $this->command->info('✅ Đã seed ' . count($categories) . ' main categories với các subcategories');
        $this->command->info('🔧 Categories đã được tối ưu cho forum cơ khí MechaMap');
    }
}
