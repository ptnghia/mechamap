<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các danh mục chính
        $categories = [
            [
                'name' => 'Tin tức',
                'description' => 'Tin tức mới nhất về các dự án xây dựng',
            ],
            [
                'name' => 'Dự án',
                'description' => 'Thảo luận về các dự án đang triển khai',
            ],
            [
                'name' => 'Kiến trúc',
                'description' => 'Thảo luận về kiến trúc và thiết kế',
            ],
            [
                'name' => 'Quy hoạch đô thị',
                'description' => 'Thảo luận về quy hoạch và phát triển đô thị',
            ],
            [
                'name' => 'Hỏi đáp',
                'description' => 'Đặt câu hỏi và nhận câu trả lời từ cộng đồng',
            ],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'order' => $index,
            ]);
        }

        // Tạo các danh mục con cho danh mục "Dự án"
        $projectCategory = Category::where('slug', 'du-an')->first();
        
        $subCategories = [
            [
                'name' => 'Dự án phía Bắc',
                'description' => 'Các dự án ở khu vực phía Bắc',
            ],
            [
                'name' => 'Dự án phía Nam',
                'description' => 'Các dự án ở khu vực phía Nam',
            ],
            [
                'name' => 'Dự án phía Trung',
                'description' => 'Các dự án ở khu vực miền Trung',
            ],
            [
                'name' => 'Dự án quốc tế',
                'description' => 'Các dự án ở nước ngoài',
            ],
        ];

        foreach ($subCategories as $index => $subCategory) {
            Category::create([
                'name' => $subCategory['name'],
                'slug' => Str::slug($subCategory['name']),
                'description' => $subCategory['description'],
                'parent_id' => $projectCategory->id,
                'order' => $index,
            ]);
        }
    }
}
