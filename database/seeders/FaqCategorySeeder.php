<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lập trình PLC',
                'slug' => 'plc-programming',
                'description' => 'Câu hỏi về lập trình PLC và tự động hóa',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Robot & CNC',
                'slug' => 'robot-cnc',
                'description' => 'Các câu hỏi về robot công nghiệp và máy CNC',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'HMI & SCADA',
                'slug' => 'hmi-scada',
                'description' => 'Câu hỏi về giao diện người máy và hệ thống SCADA',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Cảm biến & Cơ cấu chấp hành',
                'slug' => 'sensors-actuators',
                'description' => 'Thông tin về cảm biến và cơ cấu chấp hành',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Bảo trì & Sửa chữa',
                'slug' => 'maintenance',
                'description' => 'Bảo trì và xử lý sự cố hệ thống',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            FaqCategory::create($category);
        }
    }
}
