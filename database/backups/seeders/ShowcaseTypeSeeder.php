<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShowcaseType;

class ShowcaseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Dự án Thiết kế',
                'slug' => 'du-an-thiet-ke',
                'description' => 'Các dự án thiết kế sản phẩm, máy móc từ ý tưởng đến hoàn thiện',
                'is_active' => true,
            ],
            [
                'name' => 'Nghiên cứu & Phát triển',
                'slug' => 'nghien-cuu-phat-trien',
                'description' => 'Các dự án nghiên cứu, phát triển công nghệ mới',
                'is_active' => true,
            ],
            [
                'name' => 'Tối ưu hóa',
                'slug' => 'toi-uu-hoa',
                'description' => 'Các dự án tối ưu hóa thiết kế, quy trình sản xuất',
                'is_active' => true,
            ],
            [
                'name' => 'Phân tích & Mô phỏng',
                'slug' => 'phan-tich-mo-phong',
                'description' => 'Các dự án phân tích kỹ thuật, mô phỏng số',
                'is_active' => true,
            ],
            [
                'name' => 'Prototype & Testing',
                'slug' => 'prototype-testing',
                'description' => 'Các dự án tạo mẫu thử nghiệm và kiểm tra',
                'is_active' => true,
            ],
            [
                'name' => 'Cải tiến quy trình',
                'slug' => 'cai-tien-quy-trinh',
                'description' => 'Các dự án cải tiến quy trình sản xuất, gia công',
                'is_active' => true,
            ],
            [
                'name' => 'Học tập & Thực hành',
                'slug' => 'hoc-tap-thuc-hanh',
                'description' => 'Các dự án học tập, thực hành kỹ năng kỹ thuật',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            ShowcaseType::firstOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
