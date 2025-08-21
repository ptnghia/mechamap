<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class PageSeoSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        PageSeo::query()->delete();

        $pages = [
            [
                'route_name' => 'home',
                'url_pattern' => '/',
                'title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                'description' => 'Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam. Thảo luận CAD/CAM, chia sẻ kinh nghiệm thiết kế, công nghệ chế tạo và giải pháp kỹ thuật.',
                'keywords' => 'mechanical engineering vietnam, CAD CAM, thiết kế cơ khí, forum kỹ thuật, cộng đồng kỹ sư',
                'og_title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                'og_description' => 'Tham gia cộng đồng kỹ sư cơ khí lớn nhất Việt Nam. Thảo luận về CAD/CAM, thiết kế máy móc, công nghệ chế tạo.',
                'twitter_title' => 'MechaMap - Vietnam Mechanical Engineering Community',
                'twitter_description' => 'Join Vietnam\'s largest mechanical engineering community. Discuss CAD/CAM, machine design, manufacturing technology.',
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
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'category.show',
                'url_pattern' => '/forum/categories/*',
                'title' => '{category_name} - Thảo luận Kỹ thuật | MechaMap',
                'description' => 'Các chủ đề thảo luận trong danh mục {category_name}. Tham gia cộng đồng kỹ sư cơ khí để học hỏi và chia sẻ kinh nghiệm.',
                'keywords' => '{category_name}, forum cơ khí, thảo luận kỹ thuật, cộng đồng kỹ sư',
                'no_index' => false,
                'is_active' => true,
            ],
            [
                'route_name' => 'forums.index',
                'url_pattern' => '/forums',
                'title' => 'Diễn đàn Kỹ thuật Cơ khí - Cộng đồng MechaMap',
                'description' => 'Tham gia diễn đàn kỹ thuật cơ khí MechaMap với hàng nghìn chủ đề thảo luận về CAD/CAM, thiết kế máy móc, công nghệ chế tạo, vật liệu kỹ thuật và tự động hóa. Kết nối với cộng đồng kỹ sư chuyên nghiệp.',
                'keywords' => 'diễn đàn cơ khí, forum kỹ thuật, thảo luận CAD/CAM, thiết kế máy móc, công nghệ chế tạo, cộng đồng kỹ sư, mechanical engineering forum',
                'og_title' => 'Diễn đàn Kỹ thuật Cơ khí - MechaMap',
                'og_description' => 'Khám phá diễn đàn kỹ thuật cơ khí hàng đầu Việt Nam với hàng nghìn chủ đề về CAD/CAM, thiết kế, chế tạo và automation.',
                'og_image' => '/images/seo/mechamap-forums-og.jpg',
                'twitter_title' => 'Mechanical Engineering Forum - MechaMap',
                'twitter_description' => 'Join Vietnam\'s leading mechanical engineering forum. Discuss CAD/CAM, machine design, manufacturing technology.',
                'twitter_image' => '/images/seo/mechamap-forums-twitter.jpg',
                'canonical_url' => '/forums',
                'is_active' => true,
            ],
            [
                'route_name' => 'user.profile',
                'url_pattern' => '/users/*',
                'title' => 'Hồ sơ {user_name} | MechaMap',
                'description' => 'Xem hồ sơ và hoạt động của {user_name} trong cộng đồng MechaMap.',
                'no_index' => true,
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            PageSeo::create($page);
        }

        $this->command->info('✅ PageSeo Seeder completed! Created ' . count($pages) . ' page configurations.');
    }
}
