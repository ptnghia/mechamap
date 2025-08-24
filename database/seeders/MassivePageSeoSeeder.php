<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MassivePageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data hàng loạt cho tất cả các route còn lại
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Massive SEO data cho tất cả routes còn lại...');

        $seoDataBatches = [
            $this->getForumRoutes(),
            $this->getMarketplaceRoutes(),
            $this->getShowcaseRoutes(),
            $this->getToolRoutes(),
            $this->getUserRoutes(),
            $this->getContentRoutes(),
            $this->getSearchRoutes(),
            $this->getDashboardRoutes(),
            $this->getMiscRoutes()
        ];

        $totalCreated = 0;
        foreach ($seoDataBatches as $batch) {
            foreach ($batch as $data) {
                // Kiểm tra xem đã tồn tại chưa
                $existing = PageSeo::where('route_name', $data['route_name'])->first();
                if (!$existing) {
                    PageSeo::create($data);
                    $this->command->info("✅ Tạo SEO cho: {$data['route_name']}");
                    $totalCreated++;
                } else {
                    $this->command->info("⚠️  Đã tồn tại: {$data['route_name']}");
                }
            }
        }

        $this->command->info("🎉 Hoàn thành! Đã tạo {$totalCreated} SEO records mới.");
    }

    /**
     * Get SEO data for Forum routes
     */
    private function getForumRoutes(): array
    {
        return [
            // Categories routes
            [
                'route_name' => 'categories.show',
                'url_pattern' => '/categories/*',
                'title' => '{category_name} - Danh mục Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => '{category_name} - Danh mục Kỹ thuật | MechaMap',
                    'en' => '{category_name} - Technical Category | MechaMap'
                ],
                'description' => 'Khám phá danh mục {category_name} với các chủ đề thảo luận chuyên sâu về kỹ thuật cơ khí và công nghệ.',
                'description_i18n' => [
                    'vi' => 'Khám phá danh mục {category_name} với các chủ đề thảo luận chuyên sâu về kỹ thuật cơ khí và công nghệ.',
                    'en' => 'Explore {category_name} category with in-depth discussions about mechanical engineering and technology.'
                ],
                'keywords' => 'danh mục {category_name}, thảo luận kỹ thuật, cộng đồng kỹ sư, chuyên môn',
                'focus_keyword' => 'danh mục {category_name}',
                'canonical_url' => '/categories/{category_slug}',
                'breadcrumb_title' => '{category_name}',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // Tags routes
            [
                'route_name' => 'tags.index',
                'url_pattern' => '/tags',
                'title' => 'Tags Kỹ thuật - Từ khóa Chuyên ngành | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tags Kỹ thuật - Từ khóa Chuyên ngành | MechaMap',
                    'en' => 'Technical Tags - Specialty Keywords | MechaMap'
                ],
                'description' => 'Duyệt tất cả tags và từ khóa kỹ thuật. Tìm nội dung theo chuyên ngành CAD, CAM, FEA, thiết kế máy móc và công nghệ chế tạo.',
                'description_i18n' => [
                    'vi' => 'Duyệt tất cả tags và từ khóa kỹ thuật. Tìm nội dung theo chuyên ngành CAD, CAM, FEA, thiết kế máy móc và công nghệ chế tạo.',
                    'en' => 'Browse all technical tags and keywords. Find content by specialty: CAD, CAM, FEA, machine design and manufacturing technology.'
                ],
                'keywords' => 'tags kỹ thuật, từ khóa chuyên ngành, CAD CAM FEA, thiết kế máy móc',
                'focus_keyword' => 'tags kỹ thuật',
                'canonical_url' => '/tags',
                'breadcrumb_title' => 'Tags',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'tags.show',
                'url_pattern' => '/tags/*',
                'title' => 'Tag: {tag_name} - Nội dung Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tag: {tag_name} - Nội dung Kỹ thuật | MechaMap',
                    'en' => 'Tag: {tag_name} - Technical Content | MechaMap'
                ],
                'description' => 'Tất cả nội dung được gắn tag {tag_name}. Thảo luận, hướng dẫn và kinh nghiệm từ cộng đồng kỹ sư cơ khí.',
                'description_i18n' => [
                    'vi' => 'Tất cả nội dung được gắn tag {tag_name}. Thảo luận, hướng dẫn và kinh nghiệm từ cộng đồng kỹ sư cơ khí.',
                    'en' => 'All content tagged with {tag_name}. Discussions, tutorials and experiences from mechanical engineering community.'
                ],
                'keywords' => 'tag {tag_name}, nội dung kỹ thuật, thảo luận chuyên môn, kinh nghiệm kỹ sư',
                'focus_keyword' => 'tag {tag_name}',
                'canonical_url' => '/tags/{tag_slug}',
                'breadcrumb_title' => '{tag_name}',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get SEO data for Marketplace routes
     */
    private function getMarketplaceRoutes(): array
    {
        return [
            [
                'route_name' => 'marketplace.categories.index',
                'url_pattern' => '/marketplace/categories',
                'title' => 'Danh mục Sản phẩm - Marketplace Cơ khí | MechaMap',
                'title_i18n' => [
                    'vi' => 'Danh mục Sản phẩm - Marketplace Cơ khí | MechaMap',
                    'en' => 'Product Categories - Mechanical Marketplace | MechaMap'
                ],
                'description' => 'Duyệt danh mục sản phẩm thiết bị cơ khí. Máy móc, dụng cụ, phụ tùng và thiết bị công nghiệp được phân loại chi tiết.',
                'keywords' => 'danh mục sản phẩm cơ khí, máy móc công nghiệp, thiết bị kỹ thuật, phụ tùng',
                'focus_keyword' => 'danh mục sản phẩm cơ khí',
                'canonical_url' => '/marketplace/categories',
                'breadcrumb_title' => 'Danh mục',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'marketplace.categories.show',
                'url_pattern' => '/marketplace/categories/*',
                'title' => '{category_name} - Sản phẩm Cơ khí | MechaMap Marketplace',
                'title_i18n' => [
                    'vi' => '{category_name} - Sản phẩm Cơ khí | MechaMap Marketplace',
                    'en' => '{category_name} - Mechanical Products | MechaMap Marketplace'
                ],
                'description' => 'Khám phá sản phẩm {category_name} chất lượng cao. So sánh giá, đánh giá và mua từ các nhà cung cấp uy tín.',
                'keywords' => 'sản phẩm {category_name}, mua bán thiết bị, nhà cung cấp uy tín, giá tốt',
                'focus_keyword' => 'sản phẩm {category_name}',
                'canonical_url' => '/marketplace/categories/{category_slug}',
                'breadcrumb_title' => '{category_name}',
                'article_type' => 'page',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'marketplace.sellers.index',
                'url_pattern' => '/marketplace/sellers',
                'title' => 'Nhà cung cấp Thiết bị Cơ khí - Marketplace | MechaMap',
                'title_i18n' => [
                    'vi' => 'Nhà cung cấp Thiết bị Cơ khí - Marketplace | MechaMap',
                    'en' => 'Mechanical Equipment Suppliers - Marketplace | MechaMap'
                ],
                'description' => 'Danh sách nhà cung cấp thiết bị cơ khí uy tín. Kết nối trực tiếp với các công ty và đại lý chuyên nghiệp.',
                'keywords' => 'nhà cung cấp thiết bị cơ khí, supplier uy tín, đại lý máy móc, kết nối doanh nghiệp',
                'focus_keyword' => 'nhà cung cấp thiết bị cơ khí',
                'canonical_url' => '/marketplace/sellers',
                'breadcrumb_title' => 'Nhà cung cấp',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'marketplace.sellers.show',
                'url_pattern' => '/marketplace/sellers/*',
                'title' => '{seller_name} - Nhà cung cấp Cơ khí | MechaMap',
                'title_i18n' => [
                    'vi' => '{seller_name} - Nhà cung cấp Cơ khí | MechaMap',
                    'en' => '{seller_name} - Mechanical Supplier | MechaMap'
                ],
                'description' => 'Thông tin chi tiết về {seller_name} - nhà cung cấp thiết bị cơ khí uy tín. Sản phẩm, dịch vụ và đánh giá từ khách hàng.',
                'keywords' => '{seller_name}, nhà cung cấp uy tín, thiết bị cơ khí chất lượng, dịch vụ chuyên nghiệp',
                'focus_keyword' => '{seller_name} nhà cung cấp',
                'canonical_url' => '/marketplace/sellers/{seller_slug}',
                'breadcrumb_title' => '{seller_name}',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get SEO data for Showcase routes
     */
    private function getShowcaseRoutes(): array
    {
        return [
            [
                'route_name' => 'showcase.categories.index',
                'url_pattern' => '/showcase/categories',
                'title' => 'Danh mục Dự án - Showcase Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => 'Danh mục Dự án - Showcase Kỹ thuật | MechaMap',
                    'en' => 'Project Categories - Engineering Showcase | MechaMap'
                ],
                'description' => 'Khám phá các danh mục dự án kỹ thuật cơ khí. Từ thiết kế CAD đến sản xuất, automation và đổi mới công nghệ.',
                'keywords' => 'danh mục dự án kỹ thuật, showcase cơ khí, thiết kế CAD, automation, đổi mới',
                'focus_keyword' => 'danh mục dự án kỹ thuật',
                'canonical_url' => '/showcase/categories',
                'breadcrumb_title' => 'Danh mục',
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

    /**
     * Get SEO data for Tool routes
     */
    private function getToolRoutes(): array
    {
        return [
            [
                'route_name' => 'tools.calculators.index',
                'url_pattern' => '/tools/calculators',
                'title' => 'Calculator Kỹ thuật - Công cụ Tính toán | MechaMap',
                'title_i18n' => [
                    'vi' => 'Calculator Kỹ thuật - Công cụ Tính toán | MechaMap',
                    'en' => 'Engineering Calculators - Calculation Tools | MechaMap'
                ],
                'description' => 'Bộ calculator kỹ thuật cơ khí miễn phí. Tính toán vật liệu, ứng suất, moment, tốc độ và các thông số kỹ thuật.',
                'keywords' => 'calculator kỹ thuật, tính toán cơ khí, ứng suất vật liệu, moment xoắn, công cụ miễn phí',
                'focus_keyword' => 'calculator kỹ thuật cơ khí',
                'canonical_url' => '/tools/calculators',
                'breadcrumb_title' => 'Calculator',
                'article_type' => 'tool',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'tools.materials.index',
                'url_pattern' => '/tools/materials',
                'title' => 'Cơ sở dữ liệu Vật liệu - Materials Database | MechaMap',
                'title_i18n' => [
                    'vi' => 'Cơ sở dữ liệu Vật liệu - Materials Database | MechaMap',
                    'en' => 'Materials Database - Engineering Materials | MechaMap'
                ],
                'description' => 'Cơ sở dữ liệu vật liệu kỹ thuật toàn diện. Thông số cơ lý, nhiệt độ, ứng dụng và tính chất của kim loại, polymer, composite.',
                'keywords' => 'cơ sở dữ liệu vật liệu, materials database, thông số cơ lý, kim loại polymer composite',
                'focus_keyword' => 'cơ sở dữ liệu vật liệu',
                'canonical_url' => '/tools/materials',
                'breadcrumb_title' => 'Vật liệu',
                'article_type' => 'tool',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'tools.standards.index',
                'url_pattern' => '/tools/standards',
                'title' => 'Tiêu chuẩn Kỹ thuật - Engineering Standards | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tiêu chuẩn Kỹ thuật - Engineering Standards | MechaMap',
                    'en' => 'Engineering Standards - Technical Standards | MechaMap'
                ],
                'description' => 'Thư viện tiêu chuẩn kỹ thuật quốc tế. ISO, ASME, DIN, JIS và các tiêu chuẩn Việt Nam cho thiết kế và sản xuất.',
                'keywords' => 'tiêu chuẩn kỹ thuật, ISO ASME DIN JIS, tiêu chuẩn việt nam, thiết kế sản xuất',
                'focus_keyword' => 'tiêu chuẩn kỹ thuật',
                'canonical_url' => '/tools/standards',
                'breadcrumb_title' => 'Tiêu chuẩn',
                'article_type' => 'tool',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get SEO data for User routes
     */
    private function getUserRoutes(): array
    {
        return [
            [
                'route_name' => 'users.leaderboard',
                'url_pattern' => '/users/leaderboard',
                'title' => 'Bảng xếp hạng Kỹ sư - Leaderboard | MechaMap',
                'title_i18n' => [
                    'vi' => 'Bảng xếp hạng Kỹ sư - Leaderboard | MechaMap',
                    'en' => 'Engineer Leaderboard - Top Contributors | MechaMap'
                ],
                'description' => 'Bảng xếp hạng kỹ sư tích cực nhất trong cộng đồng MechaMap. Theo dõi đóng góp, điểm số và thành tích của các thành viên.',
                'keywords' => 'bảng xếp hạng kỹ sư, leaderboard, thành viên tích cực, đóng góp cộng đồng',
                'focus_keyword' => 'bảng xếp hạng kỹ sư',
                'canonical_url' => '/users/leaderboard',
                'breadcrumb_title' => 'Xếp hạng',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get SEO data for Content routes
     */
    private function getContentRoutes(): array
    {
        return [
            [
                'route_name' => 'documentation.index',
                'url_pattern' => '/documentation',
                'title' => 'Tài liệu Kỹ thuật - Documentation | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tài liệu Kỹ thuật - Documentation | MechaMap',
                    'en' => 'Technical Documentation - Engineering Docs | MechaMap'
                ],
                'description' => 'Thư viện tài liệu kỹ thuật toàn diện. Hướng dẫn sử dụng, manual, datasheet và tài liệu tham khảo cho kỹ sư cơ khí.',
                'keywords' => 'tài liệu kỹ thuật, documentation, manual hướng dẫn, datasheet, tài liệu tham khảo',
                'focus_keyword' => 'tài liệu kỹ thuật',
                'canonical_url' => '/documentation',
                'breadcrumb_title' => 'Tài liệu',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get SEO data for Search routes
     */
    private function getSearchRoutes(): array
    {
        return [
            [
                'route_name' => 'search.advanced',
                'url_pattern' => '/search/advanced',
                'title' => 'Tìm kiếm Nâng cao - Advanced Search | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tìm kiếm Nâng cao - Advanced Search | MechaMap',
                    'en' => 'Advanced Search - Detailed Search | MechaMap'
                ],
                'description' => 'Tìm kiếm nâng cao với bộ lọc chi tiết. Tìm chính xác thông tin kỹ thuật, sản phẩm và thảo luận theo tiêu chí cụ thể.',
                'keywords' => 'tìm kiếm nâng cao, advanced search, bộ lọc chi tiết, tìm kiếm chính xác',
                'focus_keyword' => 'tìm kiếm nâng cao',
                'canonical_url' => '/search/advanced',
                'breadcrumb_title' => 'Tìm kiếm nâng cao',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.4,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get SEO data for Dashboard routes (public accessible)
     */
    private function getDashboardRoutes(): array
    {
        return [
            // Only include dashboard routes that are publicly accessible or SEO relevant
        ];
    }

    /**
     * Get SEO data for Miscellaneous routes
     */
    private function getMiscRoutes(): array
    {
        return [
            [
                'route_name' => 'contact.index',
                'url_pattern' => '/contact',
                'title' => 'Liên hệ - Contact MechaMap',
                'title_i18n' => [
                    'vi' => 'Liên hệ - Contact MechaMap',
                    'en' => 'Contact Us - MechaMap Support'
                ],
                'description' => 'Liên hệ với đội ngũ MechaMap. Hỗ trợ kỹ thuật, hợp tác kinh doanh và phản hồi từ cộng đồng kỹ sư cơ khí.',
                'keywords' => 'liên hệ mechamap, contact support, hỗ trợ kỹ thuật, hợp tác kinh doanh',
                'focus_keyword' => 'liên hệ mechamap',
                'canonical_url' => '/contact',
                'breadcrumb_title' => 'Liên hệ',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'yearly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'faq.index',
                'url_pattern' => '/faq',
                'title' => 'Câu hỏi thường gặp - FAQ | MechaMap',
                'title_i18n' => [
                    'vi' => 'Câu hỏi thường gặp - FAQ | MechaMap',
                    'en' => 'Frequently Asked Questions - FAQ | MechaMap'
                ],
                'description' => 'Câu hỏi thường gặp về MechaMap. Hướng dẫn sử dụng, chính sách và giải đáp thắc mắc cho cộng đồng kỹ sư.',
                'keywords' => 'FAQ mechamap, câu hỏi thường gặp, hướng dẫn sử dụng, giải đáp thắc mắc',
                'focus_keyword' => 'FAQ mechamap',
                'canonical_url' => '/faq',
                'breadcrumb_title' => 'FAQ',
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
