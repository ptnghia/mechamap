<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShowcaseCategory;

class ShowcaseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thiết kế Cơ khí',
                'slug' => 'thiet-ke-co-khi',
                'description' => 'Các dự án thiết kế cơ khí, máy móc và thiết bị công nghiệp',
                'is_active' => true,
            ],
            [
                'name' => 'Phân tích FEA/CFD',
                'slug' => 'phan-tich-fea-cfd',
                'description' => 'Các dự án phân tích phần tử hữu hạn và động lực học chất lỏng',
                'is_active' => true,
            ],
            [
                'name' => 'CAD/CAM',
                'slug' => 'cad-cam',
                'description' => 'Các dự án thiết kế hỗ trợ máy tính và sản xuất hỗ trợ máy tính',
                'is_active' => true,
            ],
            [
                'name' => 'Sản xuất & Gia công',
                'slug' => 'san-xuat-gia-cong',
                'description' => 'Các dự án về quy trình sản xuất, gia công cơ khí',
                'is_active' => true,
            ],
            [
                'name' => 'Tự động hóa',
                'slug' => 'tu-dong-hoa',
                'description' => 'Các dự án tự động hóa trong sản xuất và điều khiển',
                'is_active' => true,
            ],
            [
                'name' => 'In 3D & Additive Manufacturing',
                'slug' => 'in-3d-additive-manufacturing',
                'description' => 'Các dự án in 3D và sản xuất cộng gộp',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ShowcaseCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
