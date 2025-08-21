<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class ComprehensivePageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder này tạo dữ liệu SEO cơ bản cho tất cả routes frontend của MechaMap
     * để hỗ trợ hệ thống breadcrumb động và SEO optimization.
     */
    public function run(): void
    {
        // Clear existing data
        PageSeo::query()->delete();

        $this->command->info('🔄 Creating comprehensive SEO data for all frontend routes...');

        // ====================================================================
        // HOME & MAIN PAGES
        // ====================================================================
        $homePages = [
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
                'is_active' => true,
            ],
            [
                'route_name' => 'welcome',
                'url_pattern' => '/welcome',
                'title' => 'Chào mừng đến với MechaMap',
                'description' => 'Khám phá cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam với hàng nghìn chuyên gia và sinh viên.',
                'keywords' => 'welcome, chào mừng, mechanical engineering, cộng đồng kỹ thuật',
                'canonical_url' => '/welcome',
                'is_active' => true,
            ],
        ];

        foreach ($homePages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // FORUM SYSTEM PAGES
        // ====================================================================
        $forumPages = [
            [
                'route_name' => 'forums.index',
                'url_pattern' => '/forums',
                'title' => 'Diễn đàn Kỹ thuật Cơ khí | MechaMap',
                'description' => 'Khám phá các diễn đàn chuyên ngành về CAD/CAM, thiết kế máy, chế tạo, automation và robotics. Tham gia thảo luận với cộng đồng kỹ sư.',
                'keywords' => 'diễn đàn cơ khí, mechanical engineering forum, CAD CAM, thiết kế máy, chế tạo máy',
                'og_title' => 'Diễn đàn Kỹ thuật Cơ khí - MechaMap',
                'og_description' => 'Tham gia thảo luận về kỹ thuật cơ khí với hàng nghìn chuyên gia trong ngành.',
                'og_image' => '/images/seo/mechamap-forums-og.jpg',
                'twitter_title' => 'Mechanical Engineering Forum - MechaMap',
                'twitter_description' => 'Join Vietnam\'s leading mechanical engineering forum. Discuss CAD/CAM, machine design, manufacturing technology.',
                'twitter_image' => '/images/seo/mechamap-forums-twitter.jpg',
                'canonical_url' => '/forums',
                'is_active' => true,
            ],
            [
                'route_name' => 'forums.show',
                'url_pattern' => '/forums/*',
                'title' => '{forum_name} | Diễn đàn MechaMap',
                'description' => 'Thảo luận về {forum_name} trong cộng đồng kỹ sư cơ khí MechaMap. Chia sẻ kinh nghiệm và học hỏi từ các chuyên gia.',
                'keywords' => '{forum_name}, diễn đàn, mechanical engineering, thảo luận kỹ thuật',
                'canonical_url' => '/forums/{forum_slug}',
                'is_active' => true,
            ],
            [
                'route_name' => 'categories.show',
                'url_pattern' => '/categories/*',
                'title' => '{category_name} | Danh mục MechaMap',
                'description' => 'Khám phá tất cả diễn đàn trong danh mục {category_name}. Tìm hiểu và thảo luận về các chủ đề kỹ thuật chuyên sâu.',
                'keywords' => '{category_name}, danh mục, mechanical engineering, kỹ thuật cơ khí',
                'canonical_url' => '/categories/{category_slug}',
                'is_active' => true,
            ],
        ];

        foreach ($forumPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // THREAD SYSTEM PAGES
        // ====================================================================
        $threadPages = [
            [
                'route_name' => 'threads.index',
                'url_pattern' => '/threads',
                'title' => 'Tất cả Bài viết | MechaMap',
                'description' => 'Duyệt qua tất cả bài viết về kỹ thuật cơ khí, từ thiết kế CAD đến automation. Tìm kiếm giải pháp cho các vấn đề kỹ thuật.',
                'keywords' => 'bài viết kỹ thuật, mechanical engineering posts, CAD tutorials, automation guides',
                'canonical_url' => '/threads',
                'is_active' => true,
            ],
            [
                'route_name' => 'threads.show',
                'url_pattern' => '/threads/*',
                'title' => '{thread_title} | MechaMap',
                'description' => '{thread_excerpt}',
                'keywords' => '{thread_tags}, mechanical engineering, kỹ thuật cơ khí',
                'canonical_url' => '/threads/{thread_slug}',
                'is_active' => true,
            ],
            [
                'route_name' => 'threads.create',
                'url_pattern' => '/threads/create',
                'title' => 'Tạo Bài viết Mới | MechaMap',
                'description' => 'Chia sẻ kiến thức, đặt câu hỏi hoặc thảo luận về các vấn đề kỹ thuật cơ khí với cộng đồng.',
                'keywords' => 'tạo bài viết, chia sẻ kiến thức, mechanical engineering discussion',
                'canonical_url' => '/threads/create',
                'no_index' => true,
                'is_active' => true,
            ],
        ];

        foreach ($threadPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // USER SYSTEM PAGES
        // ====================================================================
        $userPages = [
            [
                'route_name' => 'users.index',
                'url_pattern' => '/users',
                'title' => 'Thành viên Cộng đồng | MechaMap',
                'description' => 'Khám phá cộng đồng kỹ sư cơ khí MechaMap. Kết nối với các chuyên gia, sinh viên và những người đam mê kỹ thuật.',
                'keywords' => 'thành viên, cộng đồng kỹ sư, mechanical engineers, networking',
                'canonical_url' => '/users',
                'is_active' => true,
            ],
            [
                'route_name' => 'profile.show',
                'url_pattern' => '/users/*',
                'title' => 'Hồ sơ {user_name} | MechaMap',
                'description' => 'Xem hồ sơ và hoạt động của {user_name} trong cộng đồng MechaMap.',
                'keywords' => '{user_name}, hồ sơ thành viên, mechanical engineer profile',
                'canonical_url' => '/users/{username}',
                'no_index' => true,
                'is_active' => true,
            ],
        ];

        foreach ($userPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // MARKETPLACE PAGES
        // ====================================================================
        $marketplacePages = [
            [
                'route_name' => 'marketplace.index',
                'url_pattern' => '/marketplace',
                'title' => 'Marketplace Kỹ thuật Cơ khí | MechaMap',
                'description' => 'Mua bán sản phẩm, phần mềm CAD, tài liệu kỹ thuật và thiết bị cơ khí. Marketplace chuyên nghiệp cho cộng đồng kỹ sư.',
                'keywords' => 'marketplace cơ khí, mua bán CAD, phần mềm kỹ thuật, thiết bị cơ khí',
                'canonical_url' => '/marketplace',
                'is_active' => true,
            ],
            [
                'route_name' => 'marketplace.products.index',
                'url_pattern' => '/marketplace/products',
                'title' => 'Sản phẩm Kỹ thuật | Marketplace MechaMap',
                'description' => 'Khám phá hàng nghìn sản phẩm kỹ thuật cơ khí: phần mềm CAD, tài liệu thiết kế, thiết bị và công cụ chuyên nghiệp.',
                'keywords' => 'sản phẩm kỹ thuật, CAD software, mechanical products, engineering tools',
                'canonical_url' => '/marketplace/products',
                'is_active' => true,
            ],
            [
                'route_name' => 'marketplace.products.show',
                'url_pattern' => '/marketplace/products/*',
                'title' => '{product_name} | Marketplace MechaMap',
                'description' => '{product_description}',
                'keywords' => '{product_tags}, marketplace, mechanical engineering product',
                'canonical_url' => '/marketplace/products/{product_slug}',
                'is_active' => true,
            ],
        ];

        foreach ($marketplacePages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // SHOWCASE PAGES
        // ====================================================================
        $showcasePages = [
            [
                'route_name' => 'showcase.index',
                'url_pattern' => '/showcase',
                'title' => 'Showcase Dự án Kỹ thuật | MechaMap',
                'description' => 'Khám phá các dự án kỹ thuật cơ khí xuất sắc từ cộng đồng. Thiết kế CAD, automation, robotics và các giải pháp sáng tạo.',
                'keywords' => 'showcase kỹ thuật, dự án cơ khí, CAD showcase, engineering projects, mechanical design',
                'canonical_url' => '/showcase',
                'is_active' => true,
            ],
            [
                'route_name' => 'showcase.show',
                'url_pattern' => '/showcase/*',
                'title' => '{showcase_title} | Showcase MechaMap',
                'description' => '{showcase_description}',
                'keywords' => '{showcase_tags}, showcase, mechanical engineering project',
                'canonical_url' => '/showcase/{showcase_slug}',
                'is_active' => true,
            ],
        ];

        foreach ($showcasePages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // TOOLS & RESOURCES PAGES
        // ====================================================================
        $toolPages = [
            [
                'route_name' => 'tools.index',
                'url_pattern' => '/tools',
                'title' => 'Công cụ Kỹ thuật | MechaMap',
                'description' => 'Bộ sưu tập công cụ và tài nguyên hữu ích cho kỹ sư cơ khí: calculators, converters, reference materials.',
                'keywords' => 'công cụ kỹ thuật, engineering tools, calculators, converters, mechanical engineering resources',
                'canonical_url' => '/tools',
                'is_active' => true,
            ],
            [
                'route_name' => 'tools.calculators',
                'url_pattern' => '/tools/calculators',
                'title' => 'Máy tính Kỹ thuật | MechaMap Tools',
                'description' => 'Bộ máy tính chuyên dụng cho kỹ sư cơ khí: tính toán độ bền, thiết kế trục, bánh răng và nhiều ứng dụng khác.',
                'keywords' => 'máy tính kỹ thuật, engineering calculators, strength calculations, gear design',
                'canonical_url' => '/tools/calculators',
                'is_active' => true,
            ],
        ];

        foreach ($toolPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // MEMBER & COMMUNITY PAGES
        // ====================================================================
        $memberPages = [
            [
                'route_name' => 'members.index',
                'url_pattern' => '/members',
                'title' => 'Danh sách Thành viên | MechaMap',
                'description' => 'Danh sách tất cả thành viên trong cộng đồng kỹ thuật cơ khí MechaMap. Tìm kiếm và kết nối với các chuyên gia.',
                'keywords' => 'danh sách thành viên, cộng đồng kỹ sư, mechanical engineers directory',
                'canonical_url' => '/members',
                'is_active' => true,
            ],
            [
                'route_name' => 'members.online',
                'url_pattern' => '/members/online',
                'title' => 'Thành viên Đang Online | MechaMap',
                'description' => 'Xem danh sách thành viên đang hoạt động trực tuyến trong cộng đồng MechaMap.',
                'keywords' => 'thành viên online, active members, real-time community',
                'canonical_url' => '/members/online',
                'is_active' => true,
            ],
            [
                'route_name' => 'faq.index',
                'url_pattern' => '/faq',
                'title' => 'Câu hỏi Thường gặp | MechaMap',
                'description' => 'Tìm câu trả lời cho các câu hỏi thường gặp về MechaMap, cách sử dụng forum và các tính năng của cộng đồng.',
                'keywords' => 'FAQ, câu hỏi thường gặp, hướng dẫn sử dụng, MechaMap help',
                'canonical_url' => '/faq',
                'is_active' => true,
            ],
        ];

        foreach ($memberPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // WHAT'S NEW SYSTEM PAGES
        // ====================================================================
        $whatsNewPages = [
            [
                'route_name' => 'whats-new',
                'url_pattern' => '/whats-new',
                'title' => 'Có gì Mới | MechaMap',
                'description' => 'Khám phá nội dung mới nhất trong cộng đồng kỹ thuật cơ khí: bài viết, thảo luận, showcase và hoạt động của thành viên.',
                'keywords' => 'có gì mới, whats new, nội dung mới, latest content, mechanical engineering updates',
                'canonical_url' => '/whats-new',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new.popular',
                'url_pattern' => '/whats-new/popular',
                'title' => 'Nội dung Phổ biến | MechaMap',
                'description' => 'Khám phá những nội dung phổ biến nhất trong cộng đồng kỹ thuật cơ khí MechaMap.',
                'keywords' => 'nội dung phổ biến, popular content, trending mechanical engineering',
                'canonical_url' => '/whats-new/popular',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new.threads',
                'url_pattern' => '/whats-new/threads',
                'title' => 'Bài viết Mới | MechaMap',
                'description' => 'Theo dõi những bài viết mới nhất về kỹ thuật cơ khí, CAD/CAM, automation và robotics.',
                'keywords' => 'bài viết mới, new threads, latest discussions, mechanical engineering posts',
                'canonical_url' => '/whats-new/threads',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new.showcases',
                'url_pattern' => '/whats-new/showcases',
                'title' => 'Showcase Mới | MechaMap',
                'description' => 'Khám phá những dự án showcase mới nhất từ cộng đồng kỹ sư cơ khí.',
                'keywords' => 'showcase mới, new showcases, latest projects, engineering projects',
                'canonical_url' => '/whats-new/showcases',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new.hot-topics',
                'url_pattern' => '/whats-new/hot-topics',
                'title' => 'Chủ đề Hot | MechaMap',
                'description' => 'Theo dõi những chủ đề hot nhất trong cộng đồng kỹ thuật cơ khí.',
                'keywords' => 'chủ đề hot, hot topics, trending discussions, popular topics',
                'canonical_url' => '/whats-new/hot-topics',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new.media',
                'url_pattern' => '/whats-new/media',
                'title' => 'Media Mới | MechaMap',
                'description' => 'Khám phá hình ảnh, video và media mới nhất được chia sẻ trong cộng đồng.',
                'keywords' => 'media mới, new media, images, videos, engineering media',
                'canonical_url' => '/whats-new/media',
                'is_active' => true,
            ],
            [
                'route_name' => 'whats-new.replies',
                'url_pattern' => '/whats-new/replies',
                'title' => 'Bài viết cần Trả lời | MechaMap',
                'description' => 'Tìm những bài viết đang cần sự trợ giúp và tham gia thảo luận.',
                'keywords' => 'cần trả lời, need replies, help needed, unanswered questions',
                'canonical_url' => '/whats-new/replies',
                'is_active' => true,
            ],
        ];

        foreach ($whatsNewPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // SEARCH SYSTEM PAGES
        // ====================================================================
        $searchPages = [
            [
                'route_name' => 'search.index',
                'url_pattern' => '/search',
                'title' => 'Tìm kiếm | MechaMap',
                'description' => 'Tìm kiếm nội dung trong cộng đồng kỹ thuật cơ khí: bài viết, showcase, sản phẩm và thành viên.',
                'keywords' => 'tìm kiếm, search, mechanical engineering search, forum search',
                'canonical_url' => '/search',
                'is_active' => true,
            ],
            [
                'route_name' => 'search.basic',
                'url_pattern' => '/search/basic',
                'title' => 'Tìm kiếm Cơ bản | MechaMap',
                'description' => 'Giao diện tìm kiếm cơ bản cho nội dung trong cộng đồng MechaMap.',
                'keywords' => 'tìm kiếm cơ bản, basic search, simple search',
                'canonical_url' => '/search/basic',
                'is_active' => true,
            ],
            [
                'route_name' => 'search.advanced',
                'url_pattern' => '/search/advanced',
                'title' => 'Tìm kiếm Nâng cao | MechaMap',
                'description' => 'Tìm kiếm nâng cao với nhiều bộ lọc và tùy chọn chi tiết.',
                'keywords' => 'tìm kiếm nâng cao, advanced search, detailed search, filters',
                'canonical_url' => '/search/advanced',
                'is_active' => true,
            ],
        ];

        foreach ($searchPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // BROWSE SYSTEM PAGES
        // ====================================================================
        $browsePages = [
            [
                'route_name' => 'browse.threads.index',
                'url_pattern' => '/browse/threads',
                'title' => 'Duyệt Bài viết | MechaMap',
                'description' => 'Duyệt qua tất cả bài viết trong cộng đồng kỹ thuật cơ khí theo nhiều tiêu chí khác nhau.',
                'keywords' => 'duyệt bài viết, browse threads, mechanical engineering discussions',
                'canonical_url' => '/browse/threads',
                'is_active' => true,
            ],
            [
                'route_name' => 'browse.threads.top-rated',
                'url_pattern' => '/browse/threads/top-rated',
                'title' => 'Bài viết Đánh giá Cao | MechaMap',
                'description' => 'Khám phá những bài viết được đánh giá cao nhất trong cộng đồng.',
                'keywords' => 'bài viết đánh giá cao, top rated threads, best discussions',
                'canonical_url' => '/browse/threads/top-rated',
                'is_active' => true,
            ],
            [
                'route_name' => 'browse.threads.trending',
                'url_pattern' => '/browse/threads/trending',
                'title' => 'Bài viết Thịnh hành | MechaMap',
                'description' => 'Theo dõi những bài viết đang thịnh hành trong cộng đồng kỹ thuật cơ khí.',
                'keywords' => 'bài viết thịnh hành, trending threads, popular discussions',
                'canonical_url' => '/browse/threads/trending',
                'is_active' => true,
            ],
            [
                'route_name' => 'browse.threads.by-tag',
                'url_pattern' => '/browse/threads/by-tag/*',
                'title' => 'Bài viết theo Tag: {tag} | MechaMap',
                'description' => 'Duyệt bài viết được gắn tag {tag} trong cộng đồng kỹ thuật cơ khí.',
                'keywords' => '{tag}, bài viết theo tag, tagged discussions',
                'canonical_url' => '/browse/threads/by-tag/{tag}',
                'is_active' => true,
            ],
            [
                'route_name' => 'browse.threads.by-forum',
                'url_pattern' => '/browse/threads/by-forum/*',
                'title' => 'Bài viết trong {forum} | MechaMap',
                'description' => 'Duyệt tất cả bài viết trong diễn đàn {forum}.',
                'keywords' => '{forum}, bài viết diễn đàn, forum discussions',
                'canonical_url' => '/browse/threads/by-forum/{forum}',
                'is_active' => true,
            ],
        ];

        foreach ($browsePages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // PAGES & CONTENT MANAGEMENT
        // ====================================================================
        $contentPages = [
            [
                'route_name' => 'pages.show',
                'url_pattern' => '/pages/*',
                'title' => '{page_title} | MechaMap',
                'description' => '{page_description}',
                'keywords' => '{page_keywords}, MechaMap content',
                'canonical_url' => '/pages/{page_slug}',
                'is_active' => true,
            ],
            [
                'route_name' => 'pages.categories',
                'url_pattern' => '/pages/categories',
                'title' => 'Danh mục Trang | MechaMap',
                'description' => 'Duyệt qua các danh mục trang nội dung trong MechaMap.',
                'keywords' => 'danh mục trang, page categories, content categories',
                'canonical_url' => '/pages/categories',
                'is_active' => true,
            ],
            [
                'route_name' => 'pages.category',
                'url_pattern' => '/pages/category/*',
                'title' => 'Danh mục: {category} | MechaMap',
                'description' => 'Xem tất cả trang trong danh mục {category}.',
                'keywords' => '{category}, page category, content category',
                'canonical_url' => '/pages/category/{category}',
                'is_active' => true,
            ],
            [
                'route_name' => 'pages.popular',
                'url_pattern' => '/pages/popular',
                'title' => 'Trang Phổ biến | MechaMap',
                'description' => 'Khám phá những trang nội dung phổ biến nhất trong MechaMap.',
                'keywords' => 'trang phổ biến, popular pages, trending content',
                'canonical_url' => '/pages/popular',
                'is_active' => true,
            ],
            [
                'route_name' => 'pages.recent',
                'url_pattern' => '/pages/recent',
                'title' => 'Trang Mới nhất | MechaMap',
                'description' => 'Xem những trang nội dung mới nhất được thêm vào MechaMap.',
                'keywords' => 'trang mới nhất, recent pages, latest content',
                'canonical_url' => '/pages/recent',
                'is_active' => true,
            ],
            [
                'route_name' => 'new',
                'url_pattern' => '/new',
                'title' => 'Nội dung Mới | MechaMap',
                'description' => 'Khám phá nội dung mới nhất trong cộng đồng: bài viết, showcase, sản phẩm và hoạt động của thành viên.',
                'keywords' => 'nội dung mới, latest content, new posts, recent activities',
                'canonical_url' => '/new',
                'is_active' => true,
            ],
        ];

        foreach ($contentPages as $page) {
            PageSeo::create($page);
        }

        // ====================================================================
        // SUMMARY OUTPUT
        // ====================================================================
        $totalPages = count($homePages) + count($forumPages) + count($threadPages) +
                     count($userPages) + count($marketplacePages) + count($showcasePages) +
                     count($toolPages) + count($memberPages) + count($whatsNewPages) +
                     count($searchPages) + count($browsePages) + count($contentPages);

        $this->command->info('✅ Created ' . count($homePages) . ' home page SEO configurations');
        $this->command->info('✅ Created ' . count($forumPages) . ' forum page SEO configurations');
        $this->command->info('✅ Created ' . count($threadPages) . ' thread page SEO configurations');
        $this->command->info('✅ Created ' . count($userPages) . ' user page SEO configurations');
        $this->command->info('✅ Created ' . count($marketplacePages) . ' marketplace page SEO configurations');
        $this->command->info('✅ Created ' . count($showcasePages) . ' showcase page SEO configurations');
        $this->command->info('✅ Created ' . count($toolPages) . ' tools page SEO configurations');
        $this->command->info('✅ Created ' . count($memberPages) . ' member page SEO configurations');
        $this->command->info('✅ Created ' . count($whatsNewPages) . ' what\'s new page SEO configurations');
        $this->command->info('✅ Created ' . count($searchPages) . ' search page SEO configurations');
        $this->command->info('✅ Created ' . count($browsePages) . ' browse page SEO configurations');
        $this->command->info('✅ Created ' . count($contentPages) . ' content page SEO configurations');
        $this->command->newLine();
        $this->command->info('🎯 Total SEO configurations created: ' . $totalPages);
        $this->command->info('🚀 Comprehensive PageSeo seeding completed successfully!');
    }
}
