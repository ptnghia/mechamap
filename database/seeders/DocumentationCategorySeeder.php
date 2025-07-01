<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentationCategory;

class DocumentationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Documentation Categories...');

        // Create main documentation categories
        $categories = [
            [
                'name' => 'Giới thiệu hệ thống',
                'slug' => 'gioi-thieu-he-thong',
                'description' => 'Tổng quan về hệ thống MechaMap, kiến trúc và tính năng chính',
                'icon' => 'fas fa-info-circle',
                'color_code' => '#007bff',
                'is_public' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Hướng dẫn sử dụng',
                'slug' => 'huong-dan-su-dung',
                'description' => 'Hướng dẫn chi tiết cho từng user role và tính năng',
                'icon' => 'fas fa-book-open',
                'color_code' => '#28a745',
                'is_public' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Quản lý tính năng',
                'slug' => 'quan-ly-tinh-nang',
                'description' => 'Documentation cho từng module admin và tính năng quản lý',
                'icon' => 'fas fa-cogs',
                'color_code' => '#ffc107',
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'API Documentation',
                'slug' => 'api-documentation',
                'description' => 'REST API endpoints, authentication và examples',
                'icon' => 'fas fa-code',
                'color_code' => '#6f42c1',
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator', 'supplier', 'manufacturer'],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Hướng dẫn kỹ thuật',
                'slug' => 'huong-dan-ky-thuat',
                'description' => 'Deployment, configuration, troubleshooting',
                'icon' => 'fas fa-tools',
                'color_code' => '#dc3545',
                'is_public' => false,
                'allowed_roles' => ['admin'],
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $category = DocumentationCategory::create($categoryData);
            $createdCategories[$category->slug] = $category;
            $this->command->info("✓ Created category: {$category->name}");
        }

        // Create subcategories
        $subcategories = [
            // Subcategories for "Hướng dẫn sử dụng"
            [
                'name' => 'Hướng dẫn Admin',
                'slug' => 'huong-dan-admin',
                'description' => 'Hướng dẫn dành cho Administrator',
                'parent_id' => $createdCategories['huong-dan-su-dung']->id,
                'icon' => 'fas fa-user-shield',
                'color_code' => '#28a745',
                'is_public' => false,
                'allowed_roles' => ['admin'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Hướng dẫn Moderator',
                'slug' => 'huong-dan-moderator',
                'description' => 'Hướng dẫn dành cho Moderator',
                'parent_id' => $createdCategories['huong-dan-su-dung']->id,
                'icon' => 'fas fa-user-edit',
                'color_code' => '#28a745',
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hướng dẫn Business Partners',
                'slug' => 'huong-dan-business-partners',
                'description' => 'Hướng dẫn dành cho Supplier, Manufacturer, Brand',
                'parent_id' => $createdCategories['huong-dan-su-dung']->id,
                'icon' => 'fas fa-handshake',
                'color_code' => '#28a745',
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator', 'supplier', 'manufacturer', 'brand'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Hướng dẫn Members',
                'slug' => 'huong-dan-members',
                'description' => 'Hướng dẫn dành cho thành viên cộng đồng',
                'parent_id' => $createdCategories['huong-dan-su-dung']->id,
                'icon' => 'fas fa-users',
                'color_code' => '#28a745',
                'is_public' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($subcategories as $subcategoryData) {
            $subcategory = DocumentationCategory::create($subcategoryData);
            $this->command->info("✓ Created subcategory: {$subcategory->name}");
        }

        $this->command->info('Documentation Categories created successfully!');
    }
}
