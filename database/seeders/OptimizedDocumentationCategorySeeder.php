<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptimizedDocumentationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating Documentation Categories (Optimized)...');

        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('documentations')->delete();
        DB::table('documentation_categories')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create main categories using DB::table for performance
        $categories = [
            [
                'name' => 'Giới thiệu hệ thống',
                'slug' => 'gioi-thieu-he-thong',
                'description' => 'Tổng quan về hệ thống MechaMap, kiến trúc và tính năng chính',
                'icon' => 'fas fa-info-circle',
                'color_code' => '#007bff',
                'is_public' => 1,
                'is_active' => 1,
                'sort_order' => 1,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hướng dẫn sử dụng',
                'slug' => 'huong-dan-su-dung',
                'description' => 'Hướng dẫn chi tiết cho từng user role và tính năng',
                'icon' => 'fas fa-book-open',
                'color_code' => '#28a745',
                'is_public' => 1,
                'is_active' => 1,
                'sort_order' => 2,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quản lý tính năng',
                'slug' => 'quan-ly-tinh-nang',
                'description' => 'Documentation cho từng module admin và tính năng quản lý',
                'icon' => 'fas fa-cogs',
                'color_code' => '#ffc107',
                'is_public' => 0,
                'allowed_roles' => json_encode(['admin', 'moderator']),
                'is_active' => 1,
                'sort_order' => 3,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'API Documentation',
                'slug' => 'api-documentation',
                'description' => 'REST API endpoints, authentication và examples',
                'icon' => 'fas fa-code',
                'color_code' => '#6f42c1',
                'is_public' => 0,
                'allowed_roles' => json_encode(['admin', 'moderator', 'supplier', 'manufacturer']),
                'is_active' => 1,
                'sort_order' => 4,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hướng dẫn kỹ thuật',
                'slug' => 'huong-dan-ky-thuat',
                'description' => 'Deployment, configuration, troubleshooting',
                'icon' => 'fas fa-tools',
                'color_code' => '#dc3545',
                'is_public' => 0,
                'allowed_roles' => json_encode(['admin']),
                'is_active' => 1,
                'sort_order' => 5,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $categoryIds = [];
        foreach ($categories as $categoryData) {
            $id = DB::table('documentation_categories')->insertGetId($categoryData);
            $categoryIds[$categoryData['slug']] = $id;
            $this->command->info("✓ Created category: {$categoryData['name']}");
        }

        // Create subcategories
        $subcategories = [
            [
                'name' => 'Hướng dẫn Admin',
                'slug' => 'huong-dan-admin',
                'description' => 'Hướng dẫn dành cho Administrator',
                'parent_id' => $categoryIds['huong-dan-su-dung'],
                'icon' => 'fas fa-user-shield',
                'color_code' => '#28a745',
                'is_public' => 0,
                'allowed_roles' => json_encode(['admin']),
                'is_active' => 1,
                'sort_order' => 1,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hướng dẫn Moderator',
                'slug' => 'huong-dan-moderator',
                'description' => 'Hướng dẫn dành cho Moderator',
                'parent_id' => $categoryIds['huong-dan-su-dung'],
                'icon' => 'fas fa-user-edit',
                'color_code' => '#28a745',
                'is_public' => 0,
                'allowed_roles' => json_encode(['admin', 'moderator']),
                'is_active' => 1,
                'sort_order' => 2,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hướng dẫn Business Partners',
                'slug' => 'huong-dan-business-partners',
                'description' => 'Hướng dẫn dành cho Supplier, Manufacturer, Brand',
                'parent_id' => $categoryIds['huong-dan-su-dung'],
                'icon' => 'fas fa-handshake',
                'color_code' => '#28a745',
                'is_public' => 0,
                'allowed_roles' => json_encode(['admin', 'moderator', 'supplier', 'manufacturer', 'brand']),
                'is_active' => 1,
                'sort_order' => 3,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hướng dẫn Members',
                'slug' => 'huong-dan-members',
                'description' => 'Hướng dẫn dành cho thành viên cộng đồng',
                'parent_id' => $categoryIds['huong-dan-su-dung'],
                'icon' => 'fas fa-users',
                'color_code' => '#28a745',
                'is_public' => 1,
                'is_active' => 1,
                'sort_order' => 4,
                'document_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($subcategories as $subcategoryData) {
            $id = DB::table('documentation_categories')->insertGetId($subcategoryData);
            $this->command->info("✓ Created subcategory: {$subcategoryData['name']}");
        }

        $this->command->info('🎉 Documentation Categories created successfully!');
        $this->command->info('Created: 5 main categories + 4 subcategories = 9 total');
    }
}
