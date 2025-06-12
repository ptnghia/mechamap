<?php

namespace Database\Seeders;

use App\Models\PageCategory;
use Illuminate\Database\Seeder;

class PageCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hướng Dẫn Kỹ Thuật',
                'slug' => 'huong-dan-ky-thuat',
                'description' => 'Các hướng dẫn kỹ thuật về cơ khí và tự động hóa',
                'order' => 1,
            ],
            [
                'name' => 'PLC & Automation',
                'slug' => 'plc-automation',
                'description' => 'Kiến thức về PLC và hệ thống tự động hóa',
                'order' => 2,
            ],
            [
                'name' => 'Robot & CNC',
                'slug' => 'robot-cnc',
                'description' => 'Thông tin về robot công nghiệp và máy CNC',
                'order' => 3,
            ],
            [
                'name' => 'Industry 4.0',
                'slug' => 'industry-4-0',
                'description' => 'Công nghệ Industry 4.0 và IoT',
                'order' => 4,
            ],
            [
                'name' => 'An Toàn Lao Động',
                'slug' => 'an-toan-lao-dong',
                'description' => 'Quy định và hướng dẫn an toàn trong công nghiệp',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            PageCategory::create($category);
        }
    }
}
