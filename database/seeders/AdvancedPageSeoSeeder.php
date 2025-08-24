<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdvancedPageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data đầy đủ cho frontend user với nội dung chất lượng cao
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Advanced SEO data cho MechaMap...');

        // Xóa dữ liệu cũ để tránh trùng lặp
        PageSeo::query()->delete();

        $seoData = $this->getSeoData();

        foreach ($seoData as $data) {
            PageSeo::create($data);
            $this->command->info("✅ Tạo SEO cho: {$data['route_name']}");
        }

        $this->command->info("🎉 Hoàn thành! Đã tạo " . count($seoData) . " SEO records.");
    }

    /**
     * Get comprehensive SEO data for all public routes
     */
    private function getSeoData(): array
    {
        return [
            // ===== HIGH PRIORITY ROUTES =====

            // 1. Trang chủ - Core landing page
            [
                'route_name' => 'home',
                'url_pattern' => '/',
                'title' => 'MechaMap - Cộng đồng Kỹ sư Cơ khí Việt Nam',
                'title_i18n' => [
                    'vi' => 'MechaMap - Cộng đồng Kỹ sư Cơ khí Việt Nam',
                    'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
                ],
                'description' => 'Tham gia cộng đồng kỹ sư cơ khí lớn nhất Việt Nam. Thảo luận CAD/CAM, chia sẻ kinh nghiệm thiết kế, mua bán thiết bị và kết nối chuyên gia.',
                'description_i18n' => [
                    'vi' => 'Tham gia cộng đồng kỹ sư cơ khí lớn nhất Việt Nam. Thảo luận CAD/CAM, chia sẻ kinh nghiệm thiết kế, mua bán thiết bị và kết nối chuyên gia.',
                    'en' => 'Join Vietnam\'s largest mechanical engineering community. Discuss CAD/CAM, share design experience, trade equipment and connect with experts.'
                ],
                'keywords' => 'cộng đồng kỹ sư cơ khí, CAD CAM Việt Nam, thiết kế cơ khí, forum kỹ thuật, marketplace thiết bị',
                'keywords_i18n' => [
                    'vi' => 'cộng đồng kỹ sư cơ khí, CAD CAM Việt Nam, thiết kế cơ khí, forum kỹ thuật, marketplace thiết bị',
                    'en' => 'mechanical engineering community, CAD CAM Vietnam, mechanical design, technical forum, equipment marketplace'
                ],
                'focus_keyword' => 'cộng đồng kỹ sư cơ khí việt nam',
                'focus_keyword_i18n' => [
                    'vi' => 'cộng đồng kỹ sư cơ khí việt nam',
                    'en' => 'vietnam mechanical engineering community'
                ],
                'og_title' => 'MechaMap - Cộng đồng Kỹ sư Cơ khí Việt Nam',
                'og_title_i18n' => [
                    'vi' => 'MechaMap - Cộng đồng Kỹ sư Cơ khí Việt Nam',
                    'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
                ],
                'og_description' => 'Kết nối với hàng nghìn kỹ sư cơ khí Việt Nam. Học hỏi, chia sẻ và phát triển sự nghiệp trong lĩnh vực kỹ thuật cơ khí.',
                'og_description_i18n' => [
                    'vi' => 'Kết nối với hàng nghìn kỹ sư cơ khí Việt Nam. Học hỏi, chia sẻ và phát triển sự nghiệp trong lĩnh vực kỹ thuật cơ khí.',
                    'en' => 'Connect with thousands of Vietnamese mechanical engineers. Learn, share and develop your career in mechanical engineering.'
                ],
                'og_image' => '/images/seo/mechamap-home-og.jpg',
                'og_type' => 'website',
                'twitter_title' => 'MechaMap - Vietnam Mechanical Engineering Community',
                'twitter_title_i18n' => [
                    'vi' => 'MechaMap - Cộng đồng Kỹ sư Cơ khí Việt Nam',
                    'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
                ],
                'twitter_description' => 'Join Vietnam\'s largest mechanical engineering community for CAD/CAM discussions and professional networking.',
                'twitter_description_i18n' => [
                    'vi' => 'Tham gia cộng đồng kỹ sư cơ khí lớn nhất Việt Nam để thảo luận CAD/CAM và kết nối chuyên nghiệp.',
                    'en' => 'Join Vietnam\'s largest mechanical engineering community for CAD/CAM discussions and professional networking.'
                ],
                'twitter_image' => '/images/seo/mechamap-home-twitter.jpg',
                'twitter_card_type' => 'summary_large_image',
                'canonical_url' => '/',
                'breadcrumb_title' => 'Trang chủ',
                'breadcrumb_title_i18n' => [
                    'vi' => 'Trang chủ',
                    'en' => 'Home'
                ],
                'meta_author' => 'MechaMap Team',
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => 'MechaMap',
                    'description' => 'Cộng đồng kỹ sư cơ khí Việt Nam',
                    'url' => 'https://mechamap.test',
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => 'https://mechamap.test/search?q={search_term_string}',
                        'query-input' => 'required name=search_term_string'
                    ]
                ],
                'article_type' => 'page',
                'priority' => 10,
                'sitemap_include' => true,
                'sitemap_priority' => 1.0,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 2. Trang giới thiệu
            [
                'route_name' => 'about.index',
                'url_pattern' => '/about',
                'title' => 'Giới thiệu MechaMap - Nền tảng Kỹ sư Cơ khí Việt Nam',
                'title_i18n' => [
                    'vi' => 'Giới thiệu MechaMap - Nền tảng Kỹ sư Cơ khí Việt Nam',
                    'en' => 'About MechaMap - Vietnam Mechanical Engineering Platform'
                ],
                'description' => 'Tìm hiểu về MechaMap - nền tảng kết nối cộng đồng kỹ sư cơ khí Việt Nam. Sứ mệnh, tầm nhìn và giá trị cốt lõi của chúng tôi.',
                'description_i18n' => [
                    'vi' => 'Tìm hiểu về MechaMap - nền tảng kết nối cộng đồng kỹ sư cơ khí Việt Nam. Sứ mệnh, tầm nhìn và giá trị cốt lõi của chúng tôi.',
                    'en' => 'Learn about MechaMap - the platform connecting Vietnam\'s mechanical engineering community. Our mission, vision and core values.'
                ],
                'keywords' => 'giới thiệu mechamap, nền tảng kỹ sư cơ khí, sứ mệnh tầm nhìn, về chúng tôi',
                'keywords_i18n' => [
                    'vi' => 'giới thiệu mechamap, nền tảng kỹ sư cơ khí, sứ mệnh tầm nhìn, về chúng tôi',
                    'en' => 'about mechamap, mechanical engineering platform, mission vision, about us'
                ],
                'focus_keyword' => 'giới thiệu mechamap',
                'focus_keyword_i18n' => [
                    'vi' => 'giới thiệu mechamap',
                    'en' => 'about mechamap'
                ],
                'og_title' => 'Về MechaMap - Nền tảng Kỹ sư Cơ khí Việt Nam',
                'og_description' => 'Khám phá câu chuyện và sứ mệnh của MechaMap trong việc xây dựng cộng đồng kỹ sư cơ khí mạnh mẽ tại Việt Nam.',
                'canonical_url' => '/about',
                'breadcrumb_title' => 'Giới thiệu',
                'breadcrumb_title_i18n' => [
                    'vi' => 'Giới thiệu',
                    'en' => 'About'
                ],
                'article_type' => 'page',
                'priority' => 9,
                'sitemap_include' => true,
                'sitemap_priority' => 0.9,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 3. Diễn đàn chính
            [
                'route_name' => 'forums.index',
                'url_pattern' => '/forums',
                'title' => 'Diễn đàn Kỹ thuật Cơ khí - Thảo luận CAD/CAM | MechaMap',
                'title_i18n' => [
                    'vi' => 'Diễn đàn Kỹ thuật Cơ khí - Thảo luận CAD/CAM | MechaMap',
                    'en' => 'Mechanical Engineering Forum - CAD/CAM Discussions | MechaMap'
                ],
                'description' => 'Tham gia diễn đàn kỹ thuật cơ khí hàng đầu Việt Nam. Thảo luận về CAD/CAM, thiết kế máy móc, công nghệ chế tạo và giải pháp kỹ thuật.',
                'description_i18n' => [
                    'vi' => 'Tham gia diễn đàn kỹ thuật cơ khí hàng đầu Việt Nam. Thảo luận về CAD/CAM, thiết kế máy móc, công nghệ chế tạo và giải pháp kỹ thuật.',
                    'en' => 'Join Vietnam\'s leading mechanical engineering forum. Discuss CAD/CAM, machine design, manufacturing technology and technical solutions.'
                ],
                'keywords' => 'diễn đàn kỹ thuật cơ khí, CAD CAM forum, thiết kế máy móc, công nghệ chế tạo, thảo luận kỹ thuật',
                'focus_keyword' => 'diễn đàn kỹ thuật cơ khí',
                'focus_keyword_i18n' => [
                    'vi' => 'diễn đàn kỹ thuật cơ khí',
                    'en' => 'mechanical engineering forum'
                ],
                'og_title' => 'Diễn đàn Kỹ thuật Cơ khí - MechaMap',
                'og_description' => 'Kết nối với cộng đồng kỹ sư cơ khí, chia sẻ kiến thức và giải quyết thách thức kỹ thuật cùng nhau.',
                'canonical_url' => '/forums',
                'breadcrumb_title' => 'Diễn đàn',
                'breadcrumb_title_i18n' => [
                    'vi' => 'Diễn đàn',
                    'en' => 'Forums'
                ],
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => 'Diễn đàn Kỹ thuật Cơ khí',
                    'description' => 'Diễn đàn thảo luận kỹ thuật cơ khí',
                    'url' => 'https://mechamap.test/forums'
                ],
                'article_type' => 'forum',
                'priority' => 10,
                'sitemap_include' => true,
                'sitemap_priority' => 0.9,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 4. Marketplace
            [
                'route_name' => 'marketplace.index',
                'url_pattern' => '/marketplace',
                'title' => 'Marketplace Thiết bị Cơ khí - Mua bán Máy móc | MechaMap',
                'title_i18n' => [
                    'vi' => 'Marketplace Thiết bị Cơ khí - Mua bán Máy móc | MechaMap',
                    'en' => 'Mechanical Equipment Marketplace - Buy Sell Machinery | MechaMap'
                ],
                'description' => 'Thị trường thiết bị cơ khí trực tuyến hàng đầu Việt Nam. Mua bán máy móc, dụng cụ, phụ tùng và thiết bị công nghiệp chất lượng cao.',
                'description_i18n' => [
                    'vi' => 'Thị trường thiết bị cơ khí trực tuyến hàng đầu Việt Nam. Mua bán máy móc, dụng cụ, phụ tùng và thiết bị công nghiệp chất lượng cao.',
                    'en' => 'Vietnam\'s leading online mechanical equipment marketplace. Buy and sell high-quality machinery, tools, parts and industrial equipment.'
                ],
                'keywords' => 'marketplace thiết bị cơ khí, mua bán máy móc, thiết bị công nghiệp, phụ tùng cơ khí, dụng cụ kỹ thuật',
                'focus_keyword' => 'marketplace thiết bị cơ khí',
                'focus_keyword_i18n' => [
                    'vi' => 'marketplace thiết bị cơ khí',
                    'en' => 'mechanical equipment marketplace'
                ],
                'og_title' => 'Marketplace Thiết bị Cơ khí - MechaMap',
                'og_description' => 'Khám phá hàng nghìn sản phẩm thiết bị cơ khí chất lượng từ các nhà cung cấp uy tín trên toàn quốc.',
                'canonical_url' => '/marketplace',
                'breadcrumb_title' => 'Marketplace',
                'article_type' => 'page',
                'priority' => 10,
                'sitemap_include' => true,
                'sitemap_priority' => 0.9,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 5. Showcase
            [
                'route_name' => 'showcase.index',
                'url_pattern' => '/showcase',
                'title' => 'Showcase Dự án Cơ khí - Trưng bày Thiết kế | MechaMap',
                'title_i18n' => [
                    'vi' => 'Showcase Dự án Cơ khí - Trưng bày Thiết kế | MechaMap',
                    'en' => 'Mechanical Projects Showcase - Design Gallery | MechaMap'
                ],
                'description' => 'Khám phá những dự án kỹ thuật cơ khí xuất sắc. Trưng bày thiết kế, chia sẻ kinh nghiệm và học hỏi từ cộng đồng kỹ sư.',
                'description_i18n' => [
                    'vi' => 'Khám phá những dự án kỹ thuật cơ khí xuất sắc. Trưng bày thiết kế, chia sẻ kinh nghiệm và học hỏi từ cộng đồng kỹ sư.',
                    'en' => 'Discover outstanding mechanical engineering projects. Showcase designs, share experiences and learn from the engineering community.'
                ],
                'keywords' => 'showcase dự án cơ khí, trưng bày thiết kế, dự án kỹ thuật, portfolio kỹ sư, thiết kế sáng tạo',
                'focus_keyword' => 'showcase dự án cơ khí',
                'focus_keyword_i18n' => [
                    'vi' => 'showcase dự án cơ khí',
                    'en' => 'mechanical projects showcase'
                ],
                'og_title' => 'Showcase Dự án Cơ khí - MechaMap',
                'og_description' => 'Cảm hứng từ những dự án kỹ thuật cơ khí độc đáo và sáng tạo của cộng đồng kỹ sư Việt Nam.',
                'canonical_url' => '/showcase',
                'breadcrumb_title' => 'Showcase',
                'article_type' => 'showcase',
                'priority' => 8,
                'sitemap_include' => true,
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 6. Công cụ
            [
                'route_name' => 'tools.index',
                'url_pattern' => '/tools',
                'title' => 'Công cụ Tính toán Cơ khí - Calculator Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => 'Công cụ Tính toán Cơ khí - Calculator Kỹ thuật | MechaMap',
                    'en' => 'Mechanical Calculation Tools - Engineering Calculators | MechaMap'
                ],
                'description' => 'Bộ công cụ tính toán kỹ thuật cơ khí miễn phí. Calculator vật liệu, quy trình, tiêu chuẩn và tài liệu kỹ thuật chuyên nghiệp.',
                'description_i18n' => [
                    'vi' => 'Bộ công cụ tính toán kỹ thuật cơ khí miễn phí. Calculator vật liệu, quy trình, tiêu chuẩn và tài liệu kỹ thuật chuyên nghiệp.',
                    'en' => 'Free mechanical engineering calculation tools. Material calculators, processes, standards and professional technical documentation.'
                ],
                'keywords' => 'công cụ tính toán cơ khí, calculator kỹ thuật, tính toán vật liệu, tiêu chuẩn kỹ thuật, công cụ thiết kế',
                'focus_keyword' => 'công cụ tính toán cơ khí',
                'focus_keyword_i18n' => [
                    'vi' => 'công cụ tính toán cơ khí',
                    'en' => 'mechanical calculation tools'
                ],
                'og_title' => 'Công cụ Tính toán Cơ khí - MechaMap',
                'og_description' => 'Tăng hiệu quả công việc với bộ công cụ tính toán kỹ thuật cơ khí chuyên nghiệp và miễn phí.',
                'canonical_url' => '/tools',
                'breadcrumb_title' => 'Công cụ',
                'breadcrumb_title_i18n' => [
                    'vi' => 'Công cụ',
                    'en' => 'Tools'
                ],
                'article_type' => 'tool',
                'priority' => 8,
                'sitemap_include' => true,
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }
}
