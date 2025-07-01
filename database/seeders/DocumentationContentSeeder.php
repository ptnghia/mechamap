<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Documentation;
use App\Models\DocumentationCategory;
use App\Models\User;

class DocumentationContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Documentation Content...');

        // Get admin user
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $this->command->error('No admin user found! Please run user seeders first.');
            return;
        }

        // Get categories
        $categories = DocumentationCategory::all()->keyBy('slug');

        if ($categories->isEmpty()) {
            $this->command->error('No categories found! Please run DocumentationCategorySeeder first.');
            return;
        }

        // Create sample documentations with shorter content
        $documentations = [
            // System Introduction
            [
                'title' => 'Tổng quan về MechaMap',
                'slug' => 'tong-quan-ve-mechamap',
                'content' => $this->getSystemOverviewContent(),
                'excerpt' => 'MechaMap là nền tảng cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam.',
                'category_id' => $categories['gioi-thieu-he-thong']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => true,
                'content_type' => 'guide',
                'difficulty_level' => 'beginner',
                'meta_title' => 'Tổng quan về MechaMap',
                'meta_description' => 'Tìm hiểu về MechaMap, nền tảng kết nối cộng đồng kỹ sư cơ khí.',
                'tags' => ['mechamap', 'overview', 'introduction'],
                'sort_order' => 1,
                'published_at' => now(),
            ],
            [
                'title' => 'Kiến trúc hệ thống MechaMap',
                'slug' => 'kien-truc-he-thong-mechamap',
                'content' => $this->getSystemArchitectureContent(),
                'excerpt' => 'Tìm hiểu về kiến trúc kỹ thuật và các module chính của MechaMap.',
                'category_id' => $categories['gioi-thieu-he-thong']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => true,
                'content_type' => 'reference',
                'difficulty_level' => 'intermediate',
                'meta_title' => 'Kiến trúc hệ thống MechaMap',
                'meta_description' => 'Kiến trúc kỹ thuật của MechaMap: Laravel backend, database design.',
                'tags' => ['architecture', 'technical', 'laravel'],
                'sort_order' => 2,
                'published_at' => now(),
            ],
            [
                'title' => 'Hướng dẫn quản trị hệ thống',
                'slug' => 'huong-dan-quan-tri-he-thong',
                'content' => $this->getAdminGuideContent(),
                'excerpt' => 'Hướng dẫn chi tiết cho Administrator về cách quản lý hệ thống.',
                'category_id' => $categories['huong-dan-admin']->id ?? $categories['huong-dan-su-dung']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => false,
                'allowed_roles' => ['admin'],
                'content_type' => 'guide',
                'difficulty_level' => 'advanced',
                'meta_title' => 'Hướng dẫn quản trị hệ thống MechaMap',
                'meta_description' => 'Hướng dẫn chi tiết cho Administrator về dashboard và user management.',
                'tags' => ['admin', 'management', 'dashboard'],
                'sort_order' => 1,
                'published_at' => now(),
            ],
            [
                'title' => 'API Authentication Guide',
                'slug' => 'api-authentication-guide',
                'content' => $this->getApiAuthContent(),
                'excerpt' => 'Hướng dẫn xác thực và phân quyền API, sử dụng tokens.',
                'category_id' => $categories['api-documentation']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => false,
                'allowed_roles' => ['admin', 'moderator', 'supplier', 'manufacturer'],
                'content_type' => 'api',
                'difficulty_level' => 'intermediate',
                'meta_title' => 'MechaMap API Authentication Guide',
                'meta_description' => 'Complete guide to MechaMap API authentication and authorization.',
                'tags' => ['api', 'authentication', 'security'],
                'sort_order' => 1,
                'published_at' => now(),
            ],
            [
                'title' => 'Hướng dẫn sử dụng cho thành viên',
                'slug' => 'huong-dan-su-dung-cho-thanh-vien',
                'content' => $this->getMemberGuideContent(),
                'excerpt' => 'Hướng dẫn chi tiết cho thành viên về forum và marketplace.',
                'category_id' => $categories['huong-dan-members']->id ?? $categories['huong-dan-su-dung']->id,
                'author_id' => $adminUser->id,
                'status' => 'published',
                'is_featured' => true,
                'is_public' => true,
                'content_type' => 'tutorial',
                'difficulty_level' => 'beginner',
                'meta_title' => 'Hướng dẫn sử dụng MechaMap cho thành viên',
                'meta_description' => 'Hướng dẫn chi tiết về cách tham gia forum và sử dụng marketplace.',
                'tags' => ['member', 'forum', 'marketplace'],
                'sort_order' => 1,
                'published_at' => now(),
            ],
        ];

        foreach ($documentations as $docData) {
            $documentation = Documentation::create($docData);
            
            // Create initial version
            $documentation->createVersion($adminUser, 'Tạo tài liệu ban đầu');
            
            $this->command->info("✓ Created document: {$documentation->title}");
        }

        $this->command->info('Documentation Content created successfully!');
    }

    private function getSystemOverviewContent(): string
    {
        return <<<'EOD'
# Tổng quan về MechaMap

## Giới thiệu
MechaMap là nền tảng cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam, kết nối kỹ sư, nhà sản xuất và doanh nghiệp.

## Tính năng chính
- **Forum cộng đồng**: Thảo luận kỹ thuật và chia sẻ kiến thức
- **Marketplace B2B2C**: Mua bán thiết bị và tài liệu kỹ thuật
- **Quản lý CAD**: Upload và chia sẻ bản vẽ kỹ thuật
- **Hệ thống 8 cấp phân quyền**: Từ Guest đến Admin

## Lợi ích
- Kết nối với cộng đồng kỹ sư
- Tiếp cận kiến thức chuyên môn
- Tìm kiếm cơ hội kinh doanh
- Chia sẻ và học hỏi kinh nghiệm
EOD;
    }

    private function getSystemArchitectureContent(): string
    {
        return <<<'EOD'
# Kiến trúc hệ thống MechaMap

## Tổng quan
MechaMap sử dụng kiến trúc MVC với Laravel framework.

## Cấu trúc Database
- **Core Tables**: users, categories, forums, threads
- **Marketplace**: products, orders, payments
- **Technical**: cad_files, materials, standards

## API Structure
- Authentication: JWT token-based
- Endpoints: `/api/v1/*`
- Rate limiting: Per user/IP

## Security
- Multi-factor authentication
- Role-based permissions
- Data encryption
- XSS/CSRF protection
EOD;
    }

    private function getAdminGuideContent(): string
    {
        return <<<'EOD'
# Hướng dẫn quản trị hệ thống

## Dashboard
Admin dashboard cung cấp tổng quan về:
- Thống kê người dùng
- Hoạt động gần đây
- Báo cáo doanh thu

## User Management
1. Xem danh sách: `/admin/users`
2. Tạo user mới
3. Phân quyền
4. Khóa/mở khóa tài khoản

## Content Management
- Quản lý forum và categories
- Kiểm duyệt bài viết
- Xử lý báo cáo vi phạm

## System Settings
- Cấu hình chung
- Security settings
- Email và payment gateways
EOD;
    }

    private function getApiAuthContent(): string
    {
        return <<<'EOD'
# API Authentication Guide

## Overview
MechaMap API sử dụng JWT cho authentication.

## Login
```http
POST /api/v1/auth/login
{
    "email": "user@example.com",
    "password": "password"
}
```

## Using Token
```http
Authorization: Bearer {token}
```

## Rate Limiting
- Public: 60 requests/minute
- Authenticated: 100 requests/minute
- Admin: 200 requests/minute

## Error Handling
- 401: Unauthorized
- 403: Forbidden
- 429: Too Many Requests
EOD;
    }

    private function getMemberGuideContent(): string
    {
        return <<<'EOD'
# Hướng dẫn sử dụng cho thành viên

## Đăng ký tài khoản
1. Truy cập trang chủ
2. Click "Đăng ký"
3. Điền thông tin
4. Xác nhận email

## Sử dụng Forum
- Chọn forum phù hợp
- Tạo bài viết mới
- Trả lời và thảo luận
- Sử dụng tags

## Marketplace
- Tìm kiếm sản phẩm
- Xem chi tiết và đánh giá
- Liên hệ seller
- Đặt hàng và thanh toán

## Tips
- Xây dựng reputation
- Tham gia tích cực
- Tuân thủ quy định
EOD;
    }
}
