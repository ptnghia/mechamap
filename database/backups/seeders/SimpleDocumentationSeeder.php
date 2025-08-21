<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentationCategory;
use App\Models\Documentation;
use App\Models\User;

class SimpleDocumentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating simple documentation data...');

        // Clear existing data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \DB::table('documentations')->delete();
        \DB::table('documentation_categories')->delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get admin user
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $this->command->error('No admin user found!');
            return;
        }

        // Create main categories
        $categories = [
            [
                'name' => 'Hướng dẫn hệ thống',
                'slug' => 'huong-dan-he-thong',
                'description' => 'Hướng dẫn sử dụng hệ thống MechaMap',
                'icon' => 'fas fa-book',
                'color_code' => '#007bff',
                'is_public' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'API Documentation',
                'slug' => 'api-docs',
                'description' => 'Tài liệu API cho developers',
                'icon' => 'fas fa-code',
                'color_code' => '#28a745',
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator'],
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $category = DocumentationCategory::create($categoryData);
            $createdCategories[$category->slug] = $category;
            $this->command->info("✓ Created category: {$category->name}");
        }

        // Create sample documents
        $documents = [
            [
                'title' => 'Hướng dẫn bắt đầu',
                'slug' => 'huong-dan-bat-dau',
                'content' => $this->getStarterGuideContent(),
                'excerpt' => 'Hướng dẫn cơ bản để bắt đầu sử dụng MechaMap',
                'category_id' => $createdCategories['huong-dan-he-thong']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => true,
                'content_type' => 'guide',
                'difficulty_level' => 'beginner',
                'meta_title' => 'Hướng dẫn bắt đầu với MechaMap',
                'meta_description' => 'Hướng dẫn cơ bản để bắt đầu sử dụng MechaMap',
                'tags' => ['guide', 'beginner'],
                'sort_order' => 1,
                'published_at' => now(),
            ],
            [
                'title' => 'API Authentication',
                'slug' => 'api-authentication',
                'content' => $this->getApiAuthContent(),
                'excerpt' => 'Hướng dẫn xác thực API',
                'category_id' => $createdCategories['api-docs']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator'],
                'content_type' => 'api',
                'difficulty_level' => 'intermediate',
                'meta_title' => 'API Authentication Guide',
                'meta_description' => 'Hướng dẫn xác thực API cho MechaMap',
                'tags' => ['api', 'auth'],
                'sort_order' => 1,
                'published_at' => now(),
            ],
        ];

        foreach ($documents as $docData) {
            $documentation = Documentation::create($docData);
            
            // Create initial version
            $documentation->createVersion($adminUser, 'Tạo tài liệu ban đầu');
            
            $this->command->info("✓ Created document: {$documentation->title}");
        }

        $this->command->info('🎉 Simple documentation seeding completed!');
        $this->command->info("Created {$createdCategories->count()} categories and " . count($documents) . " documents");
    }

    private function getStarterGuideContent(): string
    {
        return <<<'EOD'
# Hướng dẫn bắt đầu với MechaMap

## Chào mừng đến với MechaMap!

MechaMap là nền tảng cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam.

## Các bước đầu tiên

### 1. Đăng ký tài khoản
- Truy cập trang chủ
- Click "Đăng ký"
- Điền thông tin cá nhân

### 2. Khám phá forum
- Duyệt các danh mục
- Đọc bài viết
- Tham gia thảo luận

### 3. Sử dụng marketplace
- Tìm kiếm sản phẩm
- Liên hệ nhà cung cấp
- Mua sắm an toàn

## Cần hỗ trợ?
Liên hệ team support qua email: support@mechamap.com
EOD;
    }

    private function getApiAuthContent(): string
    {
        return <<<'EOD'
# API Authentication

## Overview
MechaMap API sử dụng JWT tokens cho authentication.

## Login
```http
POST /api/v1/auth/login
{
    "email": "user@example.com",
    "password": "password"
}
```

## Response
```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "role": "member"
        }
    }
}
```

## Using Token
```http
Authorization: Bearer {token}
```

## Rate Limits
- Public: 60 requests/minute
- Authenticated: 100 requests/minute
EOD;
    }
}
