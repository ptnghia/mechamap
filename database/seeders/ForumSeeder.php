<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Forum;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ForumSeeder extends Seeder
{
    /**
     * Seed forum structure cho MechaMap
     * Tạo forums theo categories đã có với nội dung chuyên ngành cơ khí
     */
    public function run(): void
    {
        $this->command->info('🏛️ Bắt đầu seed forum structure...');

        // Lấy categories đã có
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->error('❌ Không có categories! Chạy MechaMapCategorySeeder trước.');
            return;
        }

        // Lấy admin user để làm creator
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Tạo forums cho từng category
        foreach ($categories as $category) {
            $this->createForumsForCategory($category, $adminUser);
        }

        $this->command->info('✅ Hoàn thành seed forum structure!');
    }

    private function createForumsForCategory(Category $category, User $adminUser): void
    {
        // Định nghĩa forums cho từng category cơ khí
        $forumData = $this->getForumDataForCategory($category);

        foreach ($forumData as $forumInfo) {
            Forum::create([
                'name' => $forumInfo['name'],
                'slug' => Str::slug($forumInfo['name']) . '-' . $category->id,
                'description' => $forumInfo['description'],
                'category_id' => $category->id,
                'parent_id' => null,
                'order' => $forumInfo['order'] ?? 0,
                'is_private' => false,
                'thread_count' => 0,
                'post_count' => 0,
                'last_thread_id' => null,
                'last_activity_at' => null,
                'last_post_user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->line("   📂 Tạo forums cho category: {$category->name}");
    }

    private function getForumDataForCategory(Category $category): array
    {
        // Dựa vào tên category để tạo forums phù hợp
        $categoryName = strtolower($category->name);

        if (str_contains($categoryName, 'thiết kế cơ khí')) {
            return [
                [
                    'name' => 'CAD/CAM Software',
                    'description' => 'Thảo luận về phần mềm thiết kế CAD/CAM: SolidWorks, AutoCAD, Inventor, Fusion 360, Mastercam',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'Phân tích FEA/CFD',
                    'description' => 'Finite Element Analysis và Computational Fluid Dynamics: ANSYS, Abaqus, COMSOL',
                    'featured' => true,
                    'order' => 2
                ],
                [
                    'name' => 'Thiết kế máy móc',
                    'description' => 'Thiết kế và tính toán máy móc công nghiệp, cơ cấu, truyền động',
                    'featured' => false,
                    'order' => 3
                ],
                [
                    'name' => 'Bản vẽ kỹ thuật',
                    'description' => 'Quy chuẩn bản vẽ, ký hiệu, dung sai, tiêu chuẩn ISO/TCVN',
                    'featured' => false,
                    'order' => 4
                ]
            ];
        }

        if (str_contains($categoryName, 'cad/cam software')) {
            return [
                [
                    'name' => 'SolidWorks',
                    'description' => 'Thảo luận về SolidWorks: modeling, assembly, simulation, tips & tricks',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'AutoCAD',
                    'description' => 'AutoCAD 2D/3D, AutoCAD Mechanical, bản vẽ kỹ thuật',
                    'featured' => true,
                    'order' => 2
                ],
                [
                    'name' => 'Inventor',
                    'description' => 'Autodesk Inventor, parametric modeling, iLogic',
                    'featured' => false,
                    'order' => 3
                ],
                [
                    'name' => 'Fusion 360',
                    'description' => 'Fusion 360 CAD/CAM, cloud-based design, generative design',
                    'featured' => false,
                    'order' => 4
                ]
            ];
        }

        if (str_contains($categoryName, 'cnc machining')) {
            return [
                [
                    'name' => 'Mastercam',
                    'description' => 'Lập trình CNC với Mastercam: 2D, 3D, 4-5 axis, post processor',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'G-Code Programming',
                    'description' => 'Lập trình G-code thủ công, macro, custom cycles',
                    'featured' => false,
                    'order' => 2
                ],
                [
                    'name' => 'CNC Setup & Operation',
                    'description' => 'Setup máy CNC, work holding, tool management',
                    'featured' => false,
                    'order' => 3
                ]
            ];
        }

        if (str_contains($categoryName, 'công nghệ chế tạo')) {
            return [
                [
                    'name' => 'CNC Machining',
                    'description' => 'Gia công CNC: lập trình, vận hành máy phay, máy tiện CNC',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'Gia công truyền thống',
                    'description' => 'Tiện, phay, bào, mài và các phương pháp gia công cơ khí truyền thống',
                    'featured' => false,
                    'order' => 2
                ],
                [
                    'name' => 'In 3D & Additive Manufacturing',
                    'description' => 'Công nghệ in 3D, SLA, SLS, FDM và ứng dụng trong sản xuất',
                    'featured' => true,
                    'order' => 3
                ],
                [
                    'name' => 'Đồ gá & Fixture',
                    'description' => 'Thiết kế và chế tạo đồ gá, fixture, jig cho gia công',
                    'featured' => false,
                    'order' => 4
                ]
            ];
        }

        if (str_contains($categoryName, 'phân tích fea/cfd')) {
            return [
                [
                    'name' => 'ANSYS',
                    'description' => 'ANSYS Workbench, Mechanical, Fluent, CFX simulation',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'ABAQUS',
                    'description' => 'ABAQUS/CAE, nonlinear analysis, advanced materials',
                    'featured' => false,
                    'order' => 2
                ],
                [
                    'name' => 'COMSOL',
                    'description' => 'COMSOL Multiphysics, coupled physics simulation',
                    'featured' => false,
                    'order' => 3
                ]
            ];
        }

        if (str_contains($categoryName, 'kim loại') || str_contains($categoryName, 'vật liệu')) {
            return [
                [
                    'name' => 'Kim loại & Hợp kim',
                    'description' => 'Thép, nhôm, đồng, titan và các hợp kim công nghiệp',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'Polymer & Composite',
                    'description' => 'Nhựa kỹ thuật, composite, vật liệu tổng hợp',
                    'featured' => false,
                    'order' => 2
                ],
                [
                    'name' => 'Xử lý nhiệt',
                    'description' => 'Nhiệt luyện, tôi, ram, ủ và các phương pháp xử lý nhiệt',
                    'featured' => true,
                    'order' => 3
                ],
                [
                    'name' => 'Vật liệu Smart',
                    'description' => 'Vật liệu thông minh, hợp kim nhớ hình, vật liệu nano',
                    'featured' => false,
                    'order' => 4
                ]
            ];
        }

        if (str_contains($categoryName, 'plc') || str_contains($categoryName, 'hmi')) {
            return [
                [
                    'name' => 'Siemens PLC',
                    'description' => 'Siemens S7-1200, S7-1500, TIA Portal, WinCC',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'Allen-Bradley',
                    'description' => 'ControlLogix, CompactLogix, RSLogix, FactoryTalk',
                    'featured' => false,
                    'order' => 2
                ],
                [
                    'name' => 'Mitsubishi PLC',
                    'description' => 'FX series, Q series, GX Works, GOT HMI',
                    'featured' => false,
                    'order' => 3
                ]
            ];
        }

        if (str_contains($categoryName, 'robot công nghiệp')) {
            return [
                [
                    'name' => 'ABB Robotics',
                    'description' => 'ABB robot programming, RobotStudio, RAPID language',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'KUKA Robotics',
                    'description' => 'KUKA robot, KRL programming, WorkVisual',
                    'featured' => false,
                    'order' => 2
                ],
                [
                    'name' => 'Fanuc Robotics',
                    'description' => 'Fanuc robot, KAREL, Roboguide simulation',
                    'featured' => false,
                    'order' => 3
                ]
            ];
        }

        if (str_contains($categoryName, 'tự động hóa')) {
            return [
                [
                    'name' => 'PLC & HMI',
                    'description' => 'Lập trình PLC: Siemens, Allen-Bradley, Mitsubishi, Schneider',
                    'featured' => true,
                    'order' => 1
                ],
                [
                    'name' => 'Robot công nghiệp',
                    'description' => 'Robot ABB, KUKA, Fanuc, Yaskawa và ứng dụng trong sản xuất',
                    'featured' => true,
                    'order' => 2
                ],
                [
                    'name' => 'Sensors & Actuators',
                    'description' => 'Cảm biến, động cơ servo, stepper, van điện từ',
                    'featured' => false,
                    'order' => 3
                ],
                [
                    'name' => 'Industry 4.0 & IoT',
                    'description' => 'Công nghiệp 4.0, Internet of Things, Smart Factory',
                    'featured' => true,
                    'order' => 4
                ]
            ];
        }

        // Default forums cho categories khác
        return [
            [
                'name' => "Thảo luận chung - {$category->name}",
                'description' => "Thảo luận chung về {$category->name}",
                'featured' => true,
                'order' => 1
            ],
            [
                'name' => "Hỏi đáp - {$category->name}",
                'description' => "Hỏi đáp và giải đáp thắc mắc về {$category->name}",
                'featured' => false,
                'order' => 2
            ],
            [
                'name' => "Kinh nghiệm - {$category->name}",
                'description' => "Chia sẻ kinh nghiệm và best practices về {$category->name}",
                'featured' => false,
                'order' => 3
            ]
        ];
    }
}
