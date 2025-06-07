<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Đảm bảo chạy theo thứ tự đúng
        $this->call([
            // Core seeders - Chạy đầu tiên
            RolesAndPermissionsSeeder::class, // Phân quyền trước
            AdminUserSeeder::class, // Admin users

            // Structure seeders
            CategorySeeder::class, // Forum categories
            ForumSeeder::class, // Forums
            TagSeeder::class, // Tags for threads
            PageCategorySeeder::class, // Page categories
            FaqCategorySeeder::class, // FAQ categories

            // User seeders
            UserSeeder::class, // Create diverse users với mechanical expertise

            // Content seeders - Depends on users and structure
            ThreadSeeder::class, // Forum threads
            PostSeeder::class, // Thread replies
            CommentSeeder::class, // Comments on threads/posts/showcases

            // Showcase seeders
            ShowcaseSeeder::class, // CAD designs and projects
            ShowcaseCommentSeeder::class, // Showcase comments
            ShowcaseLikeSeeder::class, // Showcase likes
            ShowcaseFollowSeeder::class, // Showcase follows

            // Interaction seeders
            BookmarkSeeder::class, // User bookmarks
            ReactionSeeder::class, // Likes, loves, etc.
            PollSeeder::class, // Polls in threads
            AlertSeeder::class, // User notifications
            ReportSeeder::class, // Content reports

            // Enhanced Thread States - New quality features
            ThreadRatingSeeder::class, // Individual thread ratings (1-5 stars)
            ThreadBookmarkSeeder::class, // Advanced bookmark với folders
            EnhancedThreadStatesSeeder::class, // Update existing threads với enhanced states

            // Media seeders
            MediaSeeder::class, // Images and files
            ForumCategoryImageSeeder::class, // Professional images for forums and categories

            // Page content seeders
            PageSeeder::class, // Static pages with mechanical content
            FaqSeeder::class, // FAQ about automation

            // Settings - Keep existing (giữ nguyên theo yêu cầu)
            SeoSettingSeeder::class, // SEO settings (giữ nguyên)
            PageSeoSeeder::class, // Page SEO (giữ nguyên)
            SettingSeeder::class, // Site settings (giữ nguyên)
        ]);
    }
}
