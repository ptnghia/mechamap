<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinimalDocumentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating minimal documentation data...');

        // Clear existing data first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('documentations')->delete();
        DB::table('documentation_categories')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get admin user ID
        $adminUserId = DB::table('users')->where('role', 'admin')->value('id');
        if (!$adminUserId) {
            $this->command->error('No admin user found!');
            return;
        }

        // Insert categories directly with DB::table (no Eloquent)
        $categoryId1 = DB::table('documentation_categories')->insertGetId([
            'name' => 'Hướng dẫn cơ bản',
            'slug' => 'huong-dan-co-ban',
            'description' => 'Hướng dẫn sử dụng cơ bản',
            'icon' => 'fas fa-book',
            'color_code' => '#007bff',
            'is_public' => 1,
            'is_active' => 1,
            'sort_order' => 1,
            'document_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId2 = DB::table('documentation_categories')->insertGetId([
            'name' => 'API Docs',
            'slug' => 'api-docs',
            'description' => 'API Documentation',
            'icon' => 'fas fa-code',
            'color_code' => '#28a745',
            'is_public' => 0,
            'allowed_roles' => json_encode(['admin']),
            'is_active' => 1,
            'sort_order' => 2,
            'document_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✓ Created 2 categories");

        // Insert documents directly with DB::table (no Eloquent)
        $docId1 = DB::table('documentations')->insertGetId([
            'title' => 'Bắt đầu với MechaMap',
            'slug' => 'bat-dau-voi-mechamap',
            'content' => "# Bắt đầu với MechaMap\n\nChào mừng bạn đến với MechaMap!\n\n## Bước 1: Đăng ký\nTạo tài khoản mới\n\n## Bước 2: Khám phá\nDuyệt forum và marketplace",
            'excerpt' => 'Hướng dẫn bắt đầu sử dụng MechaMap',
            'category_id' => $categoryId1,
            'author_id' => $adminUserId,
            'status' => 'published',
            'is_featured' => 1,
            'is_public' => 1,
            'content_type' => 'guide',
            'difficulty_level' => 'beginner',
            'meta_title' => 'Bắt đầu với MechaMap',
            'meta_description' => 'Hướng dẫn bắt đầu sử dụng MechaMap',
            'tags' => json_encode(['guide', 'beginner']),
            'sort_order' => 1,
            'view_count' => 0,
            'rating_count' => 0,
            'rating_average' => 0,
            'download_count' => 0,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $docId2 = DB::table('documentations')->insertGetId([
            'title' => 'API Authentication',
            'slug' => 'api-authentication',
            'content' => "# API Authentication\n\n## Login\n```\nPOST /api/v1/auth/login\n```\n\n## Response\n```json\n{\n  \"token\": \"jwt_token_here\"\n}\n```",
            'excerpt' => 'Hướng dẫn xác thực API',
            'category_id' => $categoryId2,
            'author_id' => $adminUserId,
            'status' => 'published',
            'is_featured' => 1,
            'is_public' => 0,
            'allowed_roles' => json_encode(['admin']),
            'content_type' => 'api',
            'difficulty_level' => 'intermediate',
            'meta_title' => 'API Authentication',
            'meta_description' => 'Hướng dẫn xác thực API',
            'tags' => json_encode(['api', 'auth']),
            'sort_order' => 1,
            'view_count' => 0,
            'rating_count' => 0,
            'rating_average' => 0,
            'download_count' => 0,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✓ Created 2 documents");

        // Create initial versions (minimal)
        DB::table('documentation_versions')->insert([
            [
                'documentation_id' => $docId1,
                'user_id' => $adminUserId,
                'version_number' => '1.0.0',
                'content' => "# Bắt đầu với MechaMap\n\nChào mừng bạn đến với MechaMap!",
                'change_summary' => 'Tạo tài liệu ban đầu',
                'is_major_version' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'documentation_id' => $docId2,
                'user_id' => $adminUserId,
                'version_number' => '1.0.0',
                'content' => "# API Authentication\n\n## Login\nPOST /api/v1/auth/login",
                'change_summary' => 'Tạo tài liệu ban đầu',
                'is_major_version' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("✓ Created versions");

        // Update category document counts
        DB::table('documentation_categories')->where('id', $categoryId1)->update(['document_count' => 1]);
        DB::table('documentation_categories')->where('id', $categoryId2)->update(['document_count' => 1]);

        $this->command->info('🎉 Minimal documentation seeding completed!');
        $this->command->info('Created: 2 categories, 2 documents, 2 versions');

        $this->command->newLine();
        $this->command->info('🌐 Test URLs:');
        $this->command->info('- Admin: http://localhost:8000/admin/documentation');
        $this->command->info('- Public: http://localhost:8000/docs');
    }
}
