<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        PageSeo::query()->delete();        $pages = [
            [
                'route_name' => 'home',
                'url_pattern' => '/',
                'title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                'description' => 'Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam. Thảo luận CAD/CAM, chia sẻ kinh nghiệm thiết kế, công nghệ chế tạo và giải pháp kỹ thuật.',
                'keywords' => 'mechanical engineering vietnam, CAD CAM, thiết kế cơ khí, forum kỹ thuật, cộng đồng kỹ sư',
                'og_title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                'og_description' => 'Tham gia cộng đồng kỹ sư cơ khí lớn nhất Việt Nam. Thảo luận về CAD/CAM, thiết kế máy móc, công nghệ chế tạo.',
                'og_image' => '/images/seo/mechamap-home-og.jpg',
                'twitter_title' => 'MechaMap - Vietnam Mechanical Engineering Community',
                'twitter_description' => 'Join Vietnam\'s largest mechanical engineering community. Discuss CAD/CAM, machine design, manufacturing technology.',
                'twitter_image' => '/images/seo/mechamap-home-twitter.jpg',
                'canonical_url' => '/',
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'forum.index',
                'url_pattern' => '/forum',
                'title' => 'Forum Kỹ thuật - Thảo luận Cơ khí | MechaMap',
                'description' => 'Diễn đàn thảo luận kỹ thuật cơ khí. Trao đổi về thiết kế, vật liệu, gia công, tự động hóa và các chủ đề chuyên môn khác.',
                'keywords' => 'forum cơ khí, thảo luận kỹ thuật, thiết kế máy móc, vật liệu kỹ thuật, gia công CNC',
                'og_title' => 'Forum Kỹ thuật Cơ khí',
                'og_description' => 'Diễn đàn chuyên về kỹ thuật cơ khí với hàng nghìn chủ đề thảo luận từ các chuyên gia.',
                'og_image' => '/images/seo/mechamap-forum-og.jpg',
                'canonical_url' => '/forum',
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'thread.show',
                'url_pattern' => '/forum/threads/*',
                'title' => '{thread_title} | Forum MechaMap',
                'description' => '{thread_excerpt}',
                'keywords' => '{thread_tags}, forum cơ khí, thảo luận kỹ thuật',
                'og_title' => '{thread_title}',
                'og_description' => '{thread_excerpt}',
                'og_image' => '{thread_image}',
                'canonical_url' => '/forum/threads/{thread_id}',
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'category.show',
                'url_pattern' => '/forum/categories/*',
                'title' => '{category_name} - Thảo luận Kỹ thuật | MechaMap',
                'description' => 'Các chủ đề thảo luận trong danh mục {category_name}. Tham gia cộng đồng kỹ sư cơ khí để học hỏi và chia sẻ kinh nghiệm.',
                'keywords' => '{category_name}, forum cơ khí, thảo luận kỹ thuật, cộng đồng kỹ sư',
                'og_title' => '{category_name} - Forum MechaMap',
                'og_description' => 'Thảo luận về {category_name} trong cộng đồng kỹ sư cơ khí Việt Nam.',
                'canonical_url' => '/forum/categories/{category_slug}',
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'user.profile',
                'url_pattern' => '/users/*',
                'title' => 'Hồ sơ {user_name} | MechaMap',
                'description' => 'Xem hồ sơ và hoạt động của {user_name} trong cộng đồng MechaMap.',
                'keywords' => 'hồ sơ người dùng, {user_name}, cộng đồng kỹ sư',
                'canonical_url' => '/users/{user_id}',
                'no_index' => true, // Privacy: không index profile người dùng
                'is_active' => true,
            ],
            [
                'route_name' => 'search.results',
                'url_pattern' => '/search*',
                'title' => 'Kết quả tìm kiếm | MechaMap',
                'description' => 'Tìm kiếm thông tin kỹ thuật, bài viết và thảo luận trong cộng đồng MechaMap.',
                'keywords' => 'tìm kiếm, forum cơ khí, kỹ thuật',
                'no_index' => true, // SEO best practice: không index search results
                'is_active' => true,
            ],
            [
                'route_name' => 'showcase.index',
                'url_pattern' => '/showcase',
                'title' => 'Showcase Dự án - Triển lãm Kỹ thuật | MechaMap',
                'description' => 'Khám phá những dự án kỹ thuật cơ khí ấn tượng từ cộng đồng. Thiết kế máy móc, sản phẩm và giải pháp công nghệ.',
                'keywords' => 'showcase dự án, triển lãm kỹ thuật, thiết kế cơ khí, sản phẩm công nghệ',
                'og_title' => 'Showcase Dự án Kỹ thuật - MechaMap',
                'og_description' => 'Khám phá và chia sẻ những dự án kỹ thuật cơ khí xuất sắc.',
                'canonical_url' => '/showcase',
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new',
                'url_pattern' => '/whats-new',
                'title' => 'Có gì mới - Tin tức Kỹ thuật | MechaMap',
                'description' => 'Cập nhật tin tức mới nhất về công nghệ cơ khí, sản phẩm và xu hướng trong ngành.',
                'keywords' => 'tin tức kỹ thuật, công nghệ mới, xu hướng cơ khí, innovation',
                'canonical_url' => '/whats-new',
                'no_index' => false,
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            PageSeo::create($page);
        }

        $this->command->info('✅ PageSeo Seeder completed! Created ' . count($pages) . ' page configurations.');
    }
}
