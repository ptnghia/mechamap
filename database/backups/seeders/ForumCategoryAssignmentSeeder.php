<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Support\Facades\DB;

class ForumCategoryAssignmentSeeder extends Seeder
{
    /**
     * Phân chia forums vào categories dựa trên logic nghiệp vụ
     * Categories → Forums → Threads structure
     */
    public function run(): void
    {
        // Tạo categories chính cho mechanical engineering forum
        $categories = [
            [
                'id' => 1,
                'name' => 'Thiết kế & CAD/CAM',
                'slug' => 'thiet-ke-cad-cam',
                'description' => 'Thiết kế cơ khí, phần mềm CAD/CAM, mô phỏng và phân tích',
                'parent_id' => null,
                'order' => 1
            ],
            [
                'id' => 2,
                'name' => 'Sản xuất & Gia công',
                'slug' => 'san-xuat-gia-cong',
                'description' => 'Công nghệ chế tạo, gia công CNC, công nghệ sản xuất',
                'parent_id' => null,
                'order' => 2
            ],
            [
                'id' => 3,
                'name' => 'Tự động hóa & Robotics',
                'slug' => 'tu-dong-hoa-robotics',
                'description' => 'PLC, HMI, robot công nghiệp, hệ thống tự động',
                'parent_id' => null,
                'order' => 3
            ],
            [
                'id' => 4,
                'name' => 'Vật liệu & Thử nghiệm',
                'slug' => 'vat-lieu-thu-nghiem',
                'description' => 'Vật liệu kỹ thuật, thử nghiệm cơ tính, chất lượng',
                'parent_id' => null,
                'order' => 4
            ],
            [
                'id' => 5,
                'name' => 'Thảo luận chung',
                'slug' => 'thao-luan-chung',
                'description' => 'Các chủ đề chung, tin tức ngành, học tập',
                'parent_id' => null,
                'order' => 5
            ]
        ];

        // Insert/Update categories
        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['id' => $categoryData['id']],
                $categoryData
            );
        }

        // Phân chia forums vào categories
        $forumAssignments = [
            // Category: Thiết kế & CAD/CAM
            1 => [
                [
                    'name' => 'SolidWorks & AutoCAD',
                    'slug' => 'solidworks-autocad',
                    'description' => 'Thảo luận về SolidWorks, AutoCAD và các phần mềm CAD khác',
                    'order' => 1
                ],
                [
                    'name' => 'ANSYS & Simulation',
                    'slug' => 'ansys-simulation',
                    'description' => 'Phân tích FEA, CFD, mô phỏng và tính toán kỹ thuật',
                    'order' => 2
                ],
                [
                    'name' => 'Thiết kế sản phẩm',
                    'slug' => 'thiet-ke-san-pham',
                    'description' => 'Thiết kế máy móc, thiết bị, sản phẩm cơ khí',
                    'order' => 3
                ]
            ],

            // Category: Sản xuất & Gia công
            2 => [
                [
                    'name' => 'CNC & Gia công chính xác',
                    'slug' => 'cnc-gia-cong-chinh-xac',
                    'description' => 'CNC programming, gia công chính xác, công nghệ cắt',
                    'order' => 1
                ],
                [
                    'name' => 'Gia công truyền thống',
                    'slug' => 'gia-cong-truyen-thong',
                    'description' => 'Tiện, phay, bào, mài và các phương pháp gia công cơ bản',
                    'order' => 2
                ],
                [
                    'name' => 'In 3D & Additive Manufacturing',
                    'slug' => 'in-3d-additive',
                    'description' => 'Công nghệ in 3D, additive manufacturing, rapid prototyping',
                    'order' => 3
                ]
            ],

            // Category: Tự động hóa & Robotics
            3 => [
                [
                    'name' => 'PLC & HMI Programming',
                    'slug' => 'plc-hmi-programming',
                    'description' => 'Lập trình PLC, HMI, SCADA và hệ thống điều khiển',
                    'order' => 1
                ],
                [
                    'name' => 'Robot công nghiệp',
                    'slug' => 'robot-cong-nghiep',
                    'description' => 'Robot ABB, KUKA, Fanuc, programming và ứng dụng',
                    'order' => 2
                ],
                [
                    'name' => 'IoT & Industry 4.0',
                    'slug' => 'iot-industry-4-0',
                    'description' => 'Internet of Things, Industry 4.0, smart manufacturing',
                    'order' => 3
                ]
            ],

            // Category: Vật liệu & Thử nghiệm
            4 => [
                [
                    'name' => 'Vật liệu kỹ thuật',
                    'slug' => 'vat-lieu-ky-thuat',
                    'description' => 'Thép, hợp kim, polymer, composite và tính chất vật liệu',
                    'order' => 1
                ],
                [
                    'name' => 'Thử nghiệm & Kiểm định',
                    'slug' => 'thu-nghiem-kiem-dinh',
                    'description' => 'Thử nghiệm cơ học, kiểm định chất lượng, đo lường',
                    'order' => 2
                ],
                [
                    'name' => 'Heat Treatment & Surface',
                    'slug' => 'heat-treatment-surface',
                    'description' => 'Nhiệt luyện, xử lý bề mặt, coating và plating',
                    'order' => 3
                ]
            ],

            // Category: Thảo luận chung
            5 => [
                [
                    'name' => 'Thảo luận chung',
                    'slug' => 'thao-luan-chung',
                    'description' => 'Thảo luận chung về các chủ đề cơ khí và tự động hóa',
                    'order' => 1
                ],
                [
                    'name' => 'Tin tức công nghệ',
                    'slug' => 'tin-tuc-cong-nghe',
                    'description' => 'Tin tức mới nhất về công nghệ cơ khí và tự động hóa',
                    'order' => 2
                ],
                [
                    'name' => 'Học tập & Nghề nghiệp',
                    'slug' => 'hoc-tap-nghe-nghiep',
                    'description' => 'Tài liệu học tập, tư vấn nghề nghiệp, certification',
                    'order' => 3
                ]
            ]
        ];

        // Create forums and assign to categories
        foreach ($forumAssignments as $categoryId => $forums) {
            foreach ($forums as $forumData) {
                $forumData['category_id'] = $categoryId;
                $forumData['is_private'] = false;
                $forumData['thread_count'] = 0;
                $forumData['post_count'] = 0;
                $forumData['requires_approval'] = false;
                $forumData['allowed_thread_types'] = json_encode([
                    'discussion', 'question', 'tutorial', 'showcase'
                ]);

                Forum::updateOrCreate(
                    ['slug' => $forumData['slug']],
                    $forumData
                );
            }
        }

        // Update existing forums from backup data nếu có
        $this->updateExistingForums();
    }

    /**
     * Cập nhật forums từ dữ liệu backup nếu có
     */
    private function updateExistingForums()
    {
        $existingForumsMapping = [
            'thao-luan-chung' => 5, // Category: Thảo luận chung
            'cnc-gia-cong-chinh-xac' => 2, // Category: Sản xuất & Gia công
            // Thêm mapping khác từ backup data nếu cần
        ];

        foreach ($existingForumsMapping as $forumSlug => $categoryId) {
            Forum::where('slug', $forumSlug)->update([
                'category_id' => $categoryId
            ]);
        }
    }
}
