<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompletePageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data đầy đủ cho tất cả các route còn lại
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Complete SEO data cho tất cả routes...');

        $seoData = $this->getCompleteSeoData();

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
     * Get complete SEO data for all remaining routes
     */
    private function getCompleteSeoData(): array
    {
        return [
            // ===== LEGAL & POLICY PAGES =====

            // 1. Điều khoản sử dụng
            [
                'route_name' => 'terms.index',
                'url_pattern' => '/terms',
                'title' => 'Điều khoản Sử dụng - MechaMap',
                'title_i18n' => [
                    'vi' => 'Điều khoản Sử dụng - MechaMap',
                    'en' => 'Terms of Service - MechaMap'
                ],
                'description' => 'Điều khoản và điều kiện sử dụng nền tảng MechaMap. Quyền và nghĩa vụ của thành viên cộng đồng kỹ sư cơ khí.',
                'description_i18n' => [
                    'vi' => 'Điều khoản và điều kiện sử dụng nền tảng MechaMap. Quyền và nghĩa vụ của thành viên cộng đồng kỹ sư cơ khí.',
                    'en' => 'Terms and conditions for using MechaMap platform. Rights and obligations of mechanical engineering community members.'
                ],
                'keywords' => 'điều khoản sử dụng, terms of service, quy định mechamap, chính sách sử dụng',
                'focus_keyword' => 'điều khoản sử dụng mechamap',
                'canonical_url' => '/terms',
                'breadcrumb_title' => 'Điều khoản',
                'breadcrumb_title_i18n' => [
                    'vi' => 'Điều khoản',
                    'en' => 'Terms'
                ],
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => true,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'yearly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 2. Chính sách bảo mật
            [
                'route_name' => 'privacy.index',
                'url_pattern' => '/privacy',
                'title' => 'Chính sách Bảo mật - MechaMap',
                'title_i18n' => [
                    'vi' => 'Chính sách Bảo mật - MechaMap',
                    'en' => 'Privacy Policy - MechaMap'
                ],
                'description' => 'Chính sách bảo mật thông tin cá nhân và dữ liệu người dùng trên nền tảng MechaMap. Cam kết bảo vệ quyền riêng tư.',
                'description_i18n' => [
                    'vi' => 'Chính sách bảo mật thông tin cá nhân và dữ liệu người dùng trên nền tảng MechaMap. Cam kết bảo vệ quyền riêng tư.',
                    'en' => 'Privacy policy for personal information and user data on MechaMap platform. Commitment to protecting privacy rights.'
                ],
                'keywords' => 'chính sách bảo mật, privacy policy, bảo vệ dữ liệu, quyền riêng tư',
                'focus_keyword' => 'chính sách bảo mật mechamap',
                'canonical_url' => '/privacy',
                'breadcrumb_title' => 'Bảo mật',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => true,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'yearly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 3. Quy tắc cộng đồng
            [
                'route_name' => 'rules',
                'url_pattern' => '/rules',
                'title' => 'Quy tắc Cộng đồng - MechaMap',
                'title_i18n' => [
                    'vi' => 'Quy tắc Cộng đồng - MechaMap',
                    'en' => 'Community Rules - MechaMap'
                ],
                'description' => 'Quy tắc và nguyên tắc hoạt động trong cộng đồng kỹ sư cơ khí MechaMap. Hướng dẫn tương tác tích cực và chuyên nghiệp.',
                'description_i18n' => [
                    'vi' => 'Quy tắc và nguyên tắc hoạt động trong cộng đồng kỹ sư cơ khí MechaMap. Hướng dẫn tương tác tích cực và chuyên nghiệp.',
                    'en' => 'Rules and principles for activities in MechaMap mechanical engineering community. Guidelines for positive and professional interaction.'
                ],
                'keywords' => 'quy tắc cộng đồng, community rules, nguyên tắc hoạt động, hướng dẫn tương tác',
                'focus_keyword' => 'quy tắc cộng đồng mechamap',
                'canonical_url' => '/rules',
                'breadcrumb_title' => 'Quy tắc',
                'article_type' => 'page',
                'priority' => 4,
                'sitemap_include' => true,
                'sitemap_priority' => 0.3,
                'sitemap_changefreq' => 'yearly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 4. Accessibility
            [
                'route_name' => 'accessibility',
                'url_pattern' => '/accessibility',
                'title' => 'Khả năng Tiếp cận - MechaMap',
                'title_i18n' => [
                    'vi' => 'Khả năng Tiếp cận - MechaMap',
                    'en' => 'Accessibility - MechaMap'
                ],
                'description' => 'Cam kết về khả năng tiếp cận và hỗ trợ người dùng khuyết tật trên nền tảng MechaMap. Tính năng hỗ trợ và hướng dẫn sử dụng.',
                'description_i18n' => [
                    'vi' => 'Cam kết về khả năng tiếp cận và hỗ trợ người dùng khuyết tật trên nền tảng MechaMap. Tính năng hỗ trợ và hướng dẫn sử dụng.',
                    'en' => 'Commitment to accessibility and support for users with disabilities on MechaMap platform. Support features and usage guidelines.'
                ],
                'keywords' => 'khả năng tiếp cận, accessibility, hỗ trợ khuyết tật, tính năng hỗ trợ',
                'focus_keyword' => 'accessibility mechamap',
                'canonical_url' => '/accessibility',
                'breadcrumb_title' => 'Tiếp cận',
                'article_type' => 'page',
                'priority' => 3,
                'sitemap_include' => true,
                'sitemap_priority' => 0.2,
                'sitemap_changefreq' => 'yearly',
                'no_index' => false,
                'is_active' => true,
            ],

            // ===== FORUM ROUTES =====

            // 5. Chi tiết diễn đàn
            [
                'route_name' => 'forums.show',
                'url_pattern' => '/forums/*',
                'title' => '{forum_name} - Diễn đàn Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => '{forum_name} - Diễn đàn Kỹ thuật | MechaMap',
                    'en' => '{forum_name} - Technical Forum | MechaMap'
                ],
                'description' => 'Thảo luận chuyên sâu về {forum_name} trong cộng đồng kỹ sư cơ khí. Chia sẻ kiến thức, kinh nghiệm và giải pháp kỹ thuật.',
                'description_i18n' => [
                    'vi' => 'Thảo luận chuyên sâu về {forum_name} trong cộng đồng kỹ sư cơ khí. Chia sẻ kiến thức, kinh nghiệm và giải pháp kỹ thuật.',
                    'en' => 'In-depth discussions about {forum_name} in the mechanical engineering community. Share knowledge, experience and technical solutions.'
                ],
                'keywords' => 'diễn đàn {forum_name}, thảo luận kỹ thuật, cộng đồng kỹ sư, kinh nghiệm chuyên môn',
                'focus_keyword' => 'diễn đàn {forum_name}',
                'canonical_url' => '/forums/{forum_slug}',
                'breadcrumb_title' => '{forum_name}',
                'article_type' => 'forum',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 6. Danh sách chủ đề
            [
                'route_name' => 'threads.index',
                'url_pattern' => '/threads',
                'title' => 'Tất cả Chủ đề - Thảo luận Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tất cả Chủ đề - Thảo luận Kỹ thuật | MechaMap',
                    'en' => 'All Topics - Technical Discussions | MechaMap'
                ],
                'description' => 'Khám phá tất cả chủ đề thảo luận kỹ thuật trong cộng đồng MechaMap. Từ CAD/CAM đến thiết kế máy móc và công nghệ chế tạo.',
                'description_i18n' => [
                    'vi' => 'Khám phá tất cả chủ đề thảo luận kỹ thuật trong cộng đồng MechaMap. Từ CAD/CAM đến thiết kế máy móc và công nghệ chế tạo.',
                    'en' => 'Explore all technical discussion topics in MechaMap community. From CAD/CAM to machine design and manufacturing technology.'
                ],
                'keywords' => 'chủ đề thảo luận, technical topics, CAD CAM, thiết kế máy móc, công nghệ chế tạo',
                'focus_keyword' => 'chủ đề thảo luận kỹ thuật',
                'canonical_url' => '/threads',
                'breadcrumb_title' => 'Chủ đề',
                'article_type' => 'page',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 7. Tạo chủ đề mới
            [
                'route_name' => 'threads.create',
                'url_pattern' => '/threads/create',
                'title' => 'Tạo Chủ đề Mới - Chia sẻ Kiến thức | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tạo Chủ đề Mới - Chia sẻ Kiến thức | MechaMap',
                    'en' => 'Create New Topic - Share Knowledge | MechaMap'
                ],
                'description' => 'Tạo chủ đề thảo luận mới để chia sẻ kiến thức, đặt câu hỏi kỹ thuật hoặc thảo luận về dự án cơ khí với cộng đồng.',
                'description_i18n' => [
                    'vi' => 'Tạo chủ đề thảo luận mới để chia sẻ kiến thức, đặt câu hỏi kỹ thuật hoặc thảo luận về dự án cơ khí với cộng đồng.',
                    'en' => 'Create new discussion topics to share knowledge, ask technical questions or discuss mechanical projects with the community.'
                ],
                'keywords' => 'tạo chủ đề mới, chia sẻ kiến thức, câu hỏi kỹ thuật, thảo luận dự án',
                'focus_keyword' => 'tạo chủ đề thảo luận',
                'canonical_url' => '/threads/create',
                'breadcrumb_title' => 'Tạo chủ đề',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => false,
                'sitemap_priority' => 0.4,
                'sitemap_changefreq' => 'monthly',
                'no_index' => true,
                'is_active' => true,
            ],

            // ===== MARKETPLACE ROUTES =====

            // 8. Danh sách sản phẩm marketplace
            [
                'route_name' => 'marketplace.products.index',
                'url_pattern' => '/marketplace/products',
                'title' => 'Sản phẩm Thiết bị Cơ khí - Marketplace | MechaMap',
                'title_i18n' => [
                    'vi' => 'Sản phẩm Thiết bị Cơ khí - Marketplace | MechaMap',
                    'en' => 'Mechanical Equipment Products - Marketplace | MechaMap'
                ],
                'description' => 'Duyệt hàng nghìn sản phẩm thiết bị cơ khí chất lượng cao. Máy móc, dụng cụ, phụ tùng và thiết bị công nghiệp từ các nhà cung cấp uy tín.',
                'description_i18n' => [
                    'vi' => 'Duyệt hàng nghìn sản phẩm thiết bị cơ khí chất lượng cao. Máy móc, dụng cụ, phụ tùng và thiết bị công nghiệp từ các nhà cung cấp uy tín.',
                    'en' => 'Browse thousands of high-quality mechanical equipment products. Machinery, tools, parts and industrial equipment from trusted suppliers.'
                ],
                'keywords' => 'sản phẩm thiết bị cơ khí, máy móc công nghiệp, dụng cụ kỹ thuật, phụ tùng cơ khí',
                'focus_keyword' => 'sản phẩm thiết bị cơ khí',
                'canonical_url' => '/marketplace/products',
                'breadcrumb_title' => 'Sản phẩm',
                'article_type' => 'page',
                'priority' => 8,
                'sitemap_include' => true,
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 9. Chi tiết showcase
            [
                'route_name' => 'showcase.show',
                'url_pattern' => '/showcase/*',
                'title' => '{project_title} - Dự án Kỹ thuật | MechaMap Showcase',
                'title_i18n' => [
                    'vi' => '{project_title} - Dự án Kỹ thuật | MechaMap Showcase',
                    'en' => '{project_title} - Engineering Project | MechaMap Showcase'
                ],
                'description' => 'Khám phá dự án {project_title} - một ví dụ xuất sắc về kỹ thuật cơ khí. Tìm hiểu quy trình thiết kế, công nghệ và kinh nghiệm thực tế.',
                'description_i18n' => [
                    'vi' => 'Khám phá dự án {project_title} - một ví dụ xuất sắc về kỹ thuật cơ khí. Tìm hiểu quy trình thiết kế, công nghệ và kinh nghiệm thực tế.',
                    'en' => 'Explore {project_title} project - an excellent example of mechanical engineering. Learn about design process, technology and practical experience.'
                ],
                'keywords' => 'dự án {project_title}, showcase kỹ thuật, thiết kế cơ khí, kinh nghiệm thực tế',
                'focus_keyword' => 'dự án {project_title}',
                'canonical_url' => '/showcase/{project_slug}',
                'breadcrumb_title' => '{project_title}',
                'article_type' => 'showcase',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }
}
