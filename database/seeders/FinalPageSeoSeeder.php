<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinalPageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data cho các route bổ sung cuối cùng
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Final SEO data cho các routes còn lại...');

        $seoData = $this->getFinalSeoData();

        foreach ($seoData as $data) {
            // Kiểm tra xem đã tồn tại chưa
            $existing = PageSeo::where('route_name', $data['route_name'])->first();
            if (!$existing) {
                PageSeo::create($data);
                $this->command->info("✅ Tạo SEO cho: {$data['route_name']}");
            } else {
                $this->command->info("⚠️  Đã tồn tại: {$data['route_name']}");
            }
        }

        $this->command->info("🎉 Hoàn thành! Đã xử lý " . count($seoData) . " SEO records.");
    }

    /**
     * Get final SEO data for remaining important routes
     */
    private function getFinalSeoData(): array
    {
        return [
            // ===== USER & PROFILE ROUTES =====

            // 1. Hồ sơ người dùng
            [
                'route_name' => 'profile.show',
                'url_pattern' => '/users/*',
                'title' => '{user_name} - Hồ sơ Kỹ sư | MechaMap',
                'title_i18n' => [
                    'vi' => '{user_name} - Hồ sơ Kỹ sư | MechaMap',
                    'en' => '{user_name} - Engineer Profile | MechaMap'
                ],
                'description' => 'Hồ sơ của {user_name} - Kỹ sư cơ khí tại MechaMap. Xem kinh nghiệm, dự án, đóng góp và kết nối chuyên nghiệp.',
                'description_i18n' => [
                    'vi' => 'Hồ sơ của {user_name} - Kỹ sư cơ khí tại MechaMap. Xem kinh nghiệm, dự án, đóng góp và kết nối chuyên nghiệp.',
                    'en' => 'Profile of {user_name} - Mechanical Engineer at MechaMap. View experience, projects, contributions and professional connections.'
                ],
                'keywords' => 'hồ sơ {user_name}, kỹ sư cơ khí, kinh nghiệm chuyên môn, dự án kỹ thuật',
                'focus_keyword' => 'hồ sơ kỹ sư {user_name}',
                'canonical_url' => '/users/{user_slug}',
                'breadcrumb_title' => '{user_name}',
                'article_type' => 'user',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 2. Danh sách thành viên
            [
                'route_name' => 'members.index',
                'url_pattern' => '/members',
                'title' => 'Thành viên Cộng đồng - Kỹ sư Cơ khí | MechaMap',
                'title_i18n' => [
                    'vi' => 'Thành viên Cộng đồng - Kỹ sư Cơ khí | MechaMap',
                    'en' => 'Community Members - Mechanical Engineers | MechaMap'
                ],
                'description' => 'Khám phá cộng đồng kỹ sư cơ khí MechaMap. Kết nối với các chuyên gia, học hỏi kinh nghiệm và mở rộng mạng lưới chuyên nghiệp.',
                'description_i18n' => [
                    'vi' => 'Khám phá cộng đồng kỹ sư cơ khí MechaMap. Kết nối với các chuyên gia, học hỏi kinh nghiệm và mở rộng mạng lưới chuyên nghiệp.',
                    'en' => 'Explore MechaMap mechanical engineering community. Connect with experts, learn from experience and expand professional network.'
                ],
                'keywords' => 'thành viên cộng đồng, kỹ sư cơ khí, chuyên gia kỹ thuật, mạng lưới chuyên nghiệp',
                'focus_keyword' => 'thành viên cộng đồng kỹ sư',
                'canonical_url' => '/members',
                'breadcrumb_title' => 'Thành viên',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // ===== CONTENT & NEWS ROUTES =====

            // 3. Tin tức ngành
            [
                'route_name' => 'news.industry.index',
                'url_pattern' => '/news/industry',
                'title' => 'Tin tức Ngành Cơ khí - Xu hướng Công nghệ | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tin tức Ngành Cơ khí - Xu hướng Công nghệ | MechaMap',
                    'en' => 'Mechanical Industry News - Technology Trends | MechaMap'
                ],
                'description' => 'Cập nhật tin tức mới nhất về ngành cơ khí Việt Nam và thế giới. Xu hướng công nghệ, đổi mới sáng tạo và phát triển ngành.',
                'description_i18n' => [
                    'vi' => 'Cập nhật tin tức mới nhất về ngành cơ khí Việt Nam và thế giới. Xu hướng công nghệ, đổi mới sáng tạo và phát triển ngành.',
                    'en' => 'Latest news updates about mechanical industry in Vietnam and worldwide. Technology trends, innovation and industry development.'
                ],
                'keywords' => 'tin tức ngành cơ khí, xu hướng công nghệ, đổi mới sáng tạo, phát triển ngành',
                'focus_keyword' => 'tin tức ngành cơ khí',
                'canonical_url' => '/news/industry',
                'breadcrumb_title' => 'Tin ngành',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 4. Hướng dẫn
            [
                'route_name' => 'tutorials.index',
                'url_pattern' => '/tutorials',
                'title' => 'Hướng dẫn Kỹ thuật - Tutorial CAD/CAM | MechaMap',
                'title_i18n' => [
                    'vi' => 'Hướng dẫn Kỹ thuật - Tutorial CAD/CAM | MechaMap',
                    'en' => 'Technical Tutorials - CAD/CAM Guides | MechaMap'
                ],
                'description' => 'Thư viện hướng dẫn kỹ thuật toàn diện. Tutorial CAD/CAM, thiết kế máy móc, phân tích FEA và các kỹ năng cơ khí chuyên nghiệp.',
                'description_i18n' => [
                    'vi' => 'Thư viện hướng dẫn kỹ thuật toàn diện. Tutorial CAD/CAM, thiết kế máy móc, phân tích FEA và các kỹ năng cơ khí chuyên nghiệp.',
                    'en' => 'Comprehensive technical tutorial library. CAD/CAM tutorials, machine design, FEA analysis and professional mechanical skills.'
                ],
                'keywords' => 'hướng dẫn kỹ thuật, tutorial CAD CAM, thiết kế máy móc, phân tích FEA',
                'focus_keyword' => 'hướng dẫn kỹ thuật cơ khí',
                'canonical_url' => '/tutorials',
                'breadcrumb_title' => 'Hướng dẫn',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 5. Danh mục
            [
                'route_name' => 'categories.index',
                'url_pattern' => '/categories',
                'title' => 'Danh mục Chuyên ngành - Phân loại Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => 'Danh mục Chuyên ngành - Phân loại Kỹ thuật | MechaMap',
                    'en' => 'Specialty Categories - Technical Classification | MechaMap'
                ],
                'description' => 'Khám phá các danh mục chuyên ngành kỹ thuật cơ khí. Từ thiết kế CAD đến gia công CNC, phân tích FEA và công nghệ sản xuất.',
                'description_i18n' => [
                    'vi' => 'Khám phá các danh mục chuyên ngành kỹ thuật cơ khí. Từ thiết kế CAD đến gia công CNC, phân tích FEA và công nghệ sản xuất.',
                    'en' => 'Explore mechanical engineering specialty categories. From CAD design to CNC machining, FEA analysis and manufacturing technology.'
                ],
                'keywords' => 'danh mục chuyên ngành, phân loại kỹ thuật, CAD design, CNC machining, FEA analysis',
                'focus_keyword' => 'danh mục chuyên ngành cơ khí',
                'canonical_url' => '/categories',
                'breadcrumb_title' => 'Danh mục',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 6. Trang nội dung
            [
                'route_name' => 'pages.show',
                'url_pattern' => '/pages/*',
                'title' => '{page_title} | MechaMap',
                'title_i18n' => [
                    'vi' => '{page_title} | MechaMap',
                    'en' => '{page_title} | MechaMap'
                ],
                'description' => 'Tìm hiểu về {page_title} - thông tin hữu ích cho cộng đồng kỹ sư cơ khí MechaMap.',
                'description_i18n' => [
                    'vi' => 'Tìm hiểu về {page_title} - thông tin hữu ích cho cộng đồng kỹ sư cơ khí MechaMap.',
                    'en' => 'Learn about {page_title} - useful information for MechaMap mechanical engineering community.'
                ],
                'keywords' => '{page_title}, thông tin hữu ích, cộng đồng kỹ sư, mechamap',
                'focus_keyword' => '{page_title}',
                'canonical_url' => '/pages/{page_slug}',
                'breadcrumb_title' => '{page_title}',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }
}
