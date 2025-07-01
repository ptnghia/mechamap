<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeCategory;

class KnowledgeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cơ Khí Chế Tạo',
                'slug' => 'co-khi-che-tao',
                'description' => 'Kiến thức về thiết kế, chế tạo và gia công cơ khí',
                'icon' => 'fas fa-cog',
                'color' => '#007bff',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Thiết Kế Cơ Khí',
                        'slug' => 'thiet-ke-co-khi',
                        'description' => 'Nguyên lý thiết kế máy và cơ cấu',
                        'icon' => 'fas fa-drafting-compass',
                        'color' => '#0056b3',
                    ],
                    [
                        'name' => 'Gia Công Cơ Khí',
                        'slug' => 'gia-cong-co-khi',
                        'description' => 'Các phương pháp gia công và chế tạo',
                        'icon' => 'fas fa-tools',
                        'color' => '#004085',
                    ]
                ]
            ],
            [
                'name' => 'CAD/CAM',
                'slug' => 'cad-cam',
                'description' => 'Thiết kế hỗ trợ máy tính và sản xuất',
                'icon' => 'fas fa-cube',
                'color' => '#28a745',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'AutoCAD',
                        'slug' => 'autocad',
                        'description' => 'Hướng dẫn sử dụng AutoCAD',
                        'icon' => 'fas fa-vector-square',
                        'color' => '#1e7e34',
                    ],
                    [
                        'name' => 'SolidWorks',
                        'slug' => 'solidworks',
                        'description' => 'Thiết kế 3D với SolidWorks',
                        'icon' => 'fas fa-cube',
                        'color' => '#155724',
                    ]
                ]
            ],
            [
                'name' => 'Tự Động Hóa',
                'slug' => 'tu-dong-hoa',
                'description' => 'Hệ thống điều khiển và tự động hóa',
                'icon' => 'fas fa-robot',
                'color' => '#ffc107',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'PLC',
                        'slug' => 'plc',
                        'description' => 'Lập trình điều khiển logic',
                        'icon' => 'fas fa-microchip',
                        'color' => '#e0a800',
                    ],
                    [
                        'name' => 'HMI',
                        'slug' => 'hmi',
                        'description' => 'Giao diện người máy',
                        'icon' => 'fas fa-desktop',
                        'color' => '#d39e00',
                    ]
                ]
            ],
            [
                'name' => 'Tính Toán Kỹ Thuật',
                'slug' => 'tinh-toan-ky-thuat',
                'description' => 'Phương pháp tính toán trong cơ khí',
                'icon' => 'fas fa-calculator',
                'color' => '#17a2b8',
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Sức Bền Vật Liệu',
                        'slug' => 'suc-ben-vat-lieu',
                        'description' => 'Tính toán độ bền và biến dạng',
                        'icon' => 'fas fa-weight-hanging',
                        'color' => '#138496',
                    ],
                    [
                        'name' => 'Động Học Máy',
                        'slug' => 'dong-hoc-may',
                        'description' => 'Phân tích chuyển động cơ cấu',
                        'icon' => 'fas fa-sync-alt',
                        'color' => '#117a8b',
                    ]
                ]
            ],
            [
                'name' => 'Vật Liệu Kỹ Thuật',
                'slug' => 'vat-lieu-ky-thuat',
                'description' => 'Tính chất và ứng dụng vật liệu',
                'icon' => 'fas fa-layer-group',
                'color' => '#6f42c1',
                'sort_order' => 5,
            ],
            [
                'name' => 'Bảo Trì & Sửa Chữa',
                'slug' => 'bao-tri-sua-chua',
                'description' => 'Bảo dưỡng và sửa chữa thiết bị',
                'icon' => 'fas fa-wrench',
                'color' => '#fd7e14',
                'sort_order' => 6,
            ]
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = KnowledgeCategory::create($categoryData);

            // Create children categories
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                $childData['sort_order'] = 0;
                KnowledgeCategory::create($childData);
            }
        }
    }
}
