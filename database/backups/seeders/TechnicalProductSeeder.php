<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TechnicalProduct;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;

class TechnicalProductSeeder extends Seeder
{
    /**
     * Convert Products to TechnicalProducts for marketplace compatibility
     */
    public function run(): void
    {
        $this->command->info('🔧 Bắt đầu tạo TechnicalProducts từ Products...');

        $products = Product::all();
        $businessUsers = User::whereIn('role', ['supplier', 'manufacturer', 'brand'])->get();
        $categories = ProductCategory::all();

        if ($products->isEmpty()) {
            $this->command->error('❌ Không có Products để convert!');
            return;
        }

        $technicalProducts = [];

        foreach ($products as $product) {
            $seller = $businessUsers->random();
            $category = $categories->random();

            $technicalProduct = TechnicalProduct::create([
                'title' => $product->name,
                'slug' => $product->slug . '-tech',
                'description' => $product->description,
                'short_description' => $product->short_description,
                'seller_id' => $seller->id,
                'category_id' => $category->id,
                'price' => $product->price,
                'discount_percentage' => $product->is_on_sale ? $product->getDiscountPercentage() : 0,
                'currency' => 'VND',
                'file_formats' => $product->file_formats,
                'software_compatibility' => $product->software_compatibility,
                'complexity_level' => ['beginner', 'intermediate', 'advanced'][array_rand(['beginner', 'intermediate', 'advanced'])],
                'keywords' => implode(',', $product->keywords ?? []),
                'status' => 'approved', // Make sure they're approved
                'is_featured' => $product->is_featured,
                'is_bestseller' => rand(0, 1),
                'rating_average' => $product->average_rating,
                'rating_count' => $product->review_count,
                'view_count' => $product->view_count,
                'download_count' => 0,
                'sales_count' => $product->sales_count,
                'total_revenue' => $product->sales_count * $product->price,
                'published_at' => $product->created_at,
                'created_at' => $product->created_at,
                'updated_at' => now(),
            ]);

            $technicalProducts[] = $technicalProduct;
        }

        $this->command->info('✅ Đã tạo ' . count($technicalProducts) . ' TechnicalProducts');

        // Create some additional technical products
        $this->createAdditionalTechnicalProducts($businessUsers, $categories);

        $this->command->info('🎉 Hoàn thành tạo TechnicalProducts!');
    }

    private function createAdditionalTechnicalProducts($businessUsers, $categories): void
    {
        $additionalProducts = [
            [
                'title' => 'CAD Library - Mechanical Components',
                'description' => 'Thư viện CAD hoàn chỉnh với hơn 1000 component cơ khí thông dụng. Bao gồm bearings, gears, fasteners, và mechanical parts khác.',
                'price' => 1500000,
                'seller_type' => 'manufacturer',
                'file_formats' => ['DWG', 'STEP', 'IGES'],
                'software_compatibility' => 'AutoCAD, SolidWorks, Inventor',
                'file_size_mb' => 250.5,
            ],
            [
                'title' => 'FEA Analysis Templates - Structural',
                'description' => 'Bộ template phân tích FEA cho các cấu trúc thông dụng. Bao gồm beam, truss, frame analysis với ANSYS và Abaqus.',
                'price' => 800000,
                'seller_type' => 'manufacturer',
                'file_formats' => ['INP', 'CDB', 'MAC'],
                'software_compatibility' => 'ANSYS, Abaqus, LS-DYNA',
                'file_size_mb' => 45.2,
            ],
            [
                'title' => 'CNC Programming Guide - Advanced',
                'description' => 'Hướng dẫn lập trình CNC nâng cao cho máy 5-axis. Bao gồm strategies, toolpath optimization và post-processing.',
                'price' => 1200000,
                'seller_type' => 'supplier',
                'file_formats' => ['PDF', 'NC', 'MPF'],
                'software_compatibility' => 'Mastercam, PowerMill, NX CAM',
                'file_size_mb' => 89.7,
            ],
            [
                'title' => 'Material Database - Engineering Alloys',
                'description' => 'Database vật liệu kỹ thuật với properties đầy đủ cho hơn 500 loại hợp kim. Tương thích với các phần mềm FEA.',
                'price' => 600000,
                'seller_type' => 'brand',
                'file_formats' => ['XML', 'MAT', 'CSV'],
                'software_compatibility' => 'ANSYS, SolidWorks Simulation, Inventor',
                'file_size_mb' => 12.3,
            ],
            [
                'title' => 'Hydraulic System Design Package',
                'description' => 'Gói thiết kế hệ thống thủy lực hoàn chỉnh. Bao gồm schematics, component selection và calculation sheets.',
                'price' => 2000000,
                'seller_type' => 'manufacturer',
                'file_formats' => ['DWG', 'PDF', 'XLSX'],
                'software_compatibility' => 'AutoCAD, FluidSIM, Automation Studio',
                'file_size_mb' => 156.8,
            ]
        ];

        foreach ($additionalProducts as $productData) {
            $seller = $businessUsers->where('role', $productData['seller_type'])->random();
            $category = $categories->random();

            TechnicalProduct::create([
                'title' => $productData['title'],
                'slug' => \Illuminate\Support\Str::slug($productData['title']) . '-' . time(),
                'description' => $productData['description'],
                'short_description' => \Illuminate\Support\Str::limit($productData['description'], 100),
                'seller_id' => $seller->id,
                'category_id' => $category->id,
                'price' => $productData['price'],
                'discount_percentage' => 15,
                'currency' => 'VND',
                'file_formats' => $productData['file_formats'],
                'software_compatibility' => $productData['software_compatibility'],
                'complexity_level' => ['intermediate', 'advanced'][array_rand(['intermediate', 'advanced'])],
                'keywords' => implode(',', ['CAD', 'engineering', 'mechanical', 'design']),
                'status' => 'approved',
                'is_featured' => rand(0, 1),
                'is_bestseller' => rand(0, 1),
                'rating_average' => rand(40, 50) / 10, // 4.0-5.0
                'rating_count' => rand(10, 50),
                'view_count' => rand(100, 1000),
                'download_count' => rand(20, 200),
                'sales_count' => rand(5, 50),
                'total_revenue' => rand(5, 50) * $productData['price'],
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('✅ Đã tạo thêm ' . count($additionalProducts) . ' TechnicalProducts');
    }
}
