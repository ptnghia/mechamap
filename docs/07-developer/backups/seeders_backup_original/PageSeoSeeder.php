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
        $pages = [
            [
                'route_name' => 'home',
                'url_pattern' => '^$',
                'title' => 'MechaMap - Diễn đàn cộng đồng',
                'description' => 'MechaMap là diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, trang chủ',
                'og_title' => 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức',
                'og_description' => 'Tham gia MechaMap để chia sẻ và học hỏi kiến thức từ cộng đồng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
                'og_image' => '/images/og-image-home.jpg',
                'twitter_title' => 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức',
                'twitter_description' => 'Tham gia MechaMap để chia sẻ và học hỏi kiến thức từ cộng đồng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
                'twitter_image' => '/images/twitter-image-home.jpg',
                'canonical_url' => '',
                'no_index' => false,
                'extra_meta' => '',
                'is_active' => true,
            ],
            [
                'route_name' => 'forums.index',
                'url_pattern' => '^forums$',
                'title' => 'Danh sách diễn đàn - MechaMap',
                'description' => 'Khám phá các diễn đàn của MechaMap với nhiều chủ đề đa dạng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, danh sách diễn đàn',
                'og_title' => 'Danh sách diễn đàn - MechaMap',
                'og_description' => 'Khám phá các diễn đàn của MechaMap với nhiều chủ đề đa dạng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
                'og_image' => '/images/og-image-forums.jpg',
                'twitter_title' => 'Danh sách diễn đàn - MechaMap',
                'twitter_description' => 'Khám phá các diễn đàn của MechaMap với nhiều chủ đề đa dạng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
                'twitter_image' => '/images/twitter-image-forums.jpg',
                'canonical_url' => '',
                'no_index' => false,
                'extra_meta' => '',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new',
                'url_pattern' => '^whats-new$',
                'title' => 'Có gì mới - MechaMap',
                'description' => 'Cập nhật những bài viết, thảo luận mới nhất trên diễn đàn MechaMap.',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, có gì mới, bài viết mới',
                'og_title' => 'Có gì mới - MechaMap',
                'og_description' => 'Cập nhật những bài viết, thảo luận mới nhất trên diễn đàn MechaMap.',
                'og_image' => '/images/og-image-whats-new.jpg',
                'twitter_title' => 'Có gì mới - MechaMap',
                'twitter_description' => 'Cập nhật những bài viết, thảo luận mới nhất trên diễn đàn MechaMap.',
                'twitter_image' => '/images/twitter-image-whats-new.jpg',
                'canonical_url' => '',
                'no_index' => false,
                'extra_meta' => '',
                'is_active' => true,
            ],
            [
                'route_name' => 'threads.show',
                'url_pattern' => '^threads/[0-9]+',
                'title' => 'Chủ đề: %thread_title% - MechaMap',
                'description' => '%thread_description%',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, %thread_keywords%',
                'og_title' => '%thread_title% - MechaMap',
                'og_description' => '%thread_description%',
                'og_image' => '',
                'twitter_title' => '%thread_title% - MechaMap',
                'twitter_description' => '%thread_description%',
                'twitter_image' => '',
                'canonical_url' => '',
                'no_index' => false,
                'extra_meta' => '',
                'is_active' => true,
            ],
            [
                'route_name' => 'categories.show',
                'url_pattern' => '^categories/[a-z0-9-]+',
                'title' => 'Chuyên mục: %category_name% - MechaMap',
                'description' => 'Khám phá các bài viết trong chuyên mục %category_name% trên diễn đàn MechaMap.',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, %category_name%, chuyên mục',
                'og_title' => 'Chuyên mục: %category_name% - MechaMap',
                'og_description' => 'Khám phá các bài viết trong chuyên mục %category_name% trên diễn đàn MechaMap.',
                'og_image' => '',
                'twitter_title' => 'Chuyên mục: %category_name% - MechaMap',
                'twitter_description' => 'Khám phá các bài viết trong chuyên mục %category_name% trên diễn đàn MechaMap.',
                'twitter_image' => '',
                'canonical_url' => '',
                'no_index' => false,
                'extra_meta' => '',
                'is_active' => true,
            ],
            [
                'route_name' => 'profile.show',
                'url_pattern' => '^users/[a-zA-Z0-9_-]+',
                'title' => 'Hồ sơ của %user_name% - MechaMap',
                'description' => 'Xem hồ sơ và hoạt động của %user_name% trên diễn đàn MechaMap.',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, %user_name%, hồ sơ, thành viên',
                'og_title' => 'Hồ sơ của %user_name% - MechaMap',
                'og_description' => 'Xem hồ sơ và hoạt động của %user_name% trên diễn đàn MechaMap.',
                'og_image' => '',
                'twitter_title' => 'Hồ sơ của %user_name% - MechaMap',
                'twitter_description' => 'Xem hồ sơ và hoạt động của %user_name% trên diễn đàn MechaMap.',
                'twitter_image' => '',
                'canonical_url' => '',
                'no_index' => true,
                'extra_meta' => '',
                'is_active' => true,
            ],
            [
                'route_name' => 'search.index',
                'url_pattern' => '^search',
                'title' => 'Tìm kiếm - MechaMap',
                'description' => 'Tìm kiếm bài viết, chủ đề và thành viên trên diễn đàn MechaMap.',
                'keywords' => 'mechamap, diễn đàn, cộng đồng, forum, tìm kiếm',
                'og_title' => 'Tìm kiếm - MechaMap',
                'og_description' => 'Tìm kiếm bài viết, chủ đề và thành viên trên diễn đàn MechaMap.',
                'og_image' => '',
                'twitter_title' => 'Tìm kiếm - MechaMap',
                'twitter_description' => 'Tìm kiếm bài viết, chủ đề và thành viên trên diễn đàn MechaMap.',
                'twitter_image' => '',
                'canonical_url' => '',
                'no_index' => true,
                'extra_meta' => '',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            PageSeo::create($page);
        }
    }
}
