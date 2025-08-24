<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtendedPageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data cho các route bổ sung (Medium & Low Priority)
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Extended SEO data cho MechaMap...');

        $seoData = $this->getExtendedSeoData();

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
     * Get extended SEO data for additional routes
     */
    private function getExtendedSeoData(): array
    {
        return [
            // ===== MEDIUM PRIORITY ROUTES =====

            // 1. Tìm kiếm
            [
                'route_name' => 'search.index',
                'url_pattern' => '/search',
                'title' => 'Tìm kiếm - Khám phá Nội dung Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => 'Tìm kiếm - Khám phá Nội dung Kỹ thuật | MechaMap',
                    'en' => 'Search - Discover Technical Content | MechaMap'
                ],
                'description' => 'Tìm kiếm thông tin kỹ thuật, thảo luận, sản phẩm và dự án trong cộng đồng kỹ sư cơ khí MechaMap.',
                'description_i18n' => [
                    'vi' => 'Tìm kiếm thông tin kỹ thuật, thảo luận, sản phẩm và dự án trong cộng đồng kỹ sư cơ khí MechaMap.',
                    'en' => 'Search for technical information, discussions, products and projects in MechaMap mechanical engineering community.'
                ],
                'keywords' => 'tìm kiếm kỹ thuật, search mechamap, tìm thông tin cơ khí, khám phá nội dung',
                'focus_keyword' => 'tìm kiếm kỹ thuật',
                'focus_keyword_i18n' => [
                    'vi' => 'tìm kiếm kỹ thuật',
                    'en' => 'technical search'
                ],
                'canonical_url' => '/search',
                'breadcrumb_title' => 'Tìm kiếm',
                'breadcrumb_title_i18n' => [
                    'vi' => 'Tìm kiếm',
                    'en' => 'Search'
                ],
                'article_type' => 'page',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 2. Tin tức mới
            [
                'route_name' => 'whats-new',
                'url_pattern' => '/whats-new',
                'title' => 'Có gì mới - Tin tức Kỹ thuật Cơ khí | MechaMap',
                'title_i18n' => [
                    'vi' => 'Có gì mới - Tin tức Kỹ thuật Cơ khí | MechaMap',
                    'en' => 'What\'s New - Mechanical Engineering News | MechaMap'
                ],
                'description' => 'Cập nhật tin tức mới nhất về công nghệ cơ khí, xu hướng ngành, sản phẩm mới và hoạt động cộng đồng.',
                'description_i18n' => [
                    'vi' => 'Cập nhật tin tức mới nhất về công nghệ cơ khí, xu hướng ngành, sản phẩm mới và hoạt động cộng đồng.',
                    'en' => 'Latest updates on mechanical technology, industry trends, new products and community activities.'
                ],
                'keywords' => 'tin tức cơ khí, công nghệ mới, xu hướng ngành, cập nhật mechamap',
                'focus_keyword' => 'tin tức cơ khí',
                'canonical_url' => '/whats-new',
                'breadcrumb_title' => 'Có gì mới',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // 3. Thành viên
            [
                'route_name' => 'users.index',
                'url_pattern' => '/users',
                'title' => 'Cộng đồng Kỹ sư - Danh sách Thành viên | MechaMap',
                'title_i18n' => [
                    'vi' => 'Cộng đồng Kỹ sư - Danh sách Thành viên | MechaMap',
                    'en' => 'Engineering Community - Member Directory | MechaMap'
                ],
                'description' => 'Kết nối với cộng đồng kỹ sư cơ khí Việt Nam. Tìm hiểu hồ sơ, kinh nghiệm và chuyên môn của các thành viên.',
                'description_i18n' => [
                    'vi' => 'Kết nối với cộng đồng kỹ sư cơ khí Việt Nam. Tìm hiểu hồ sơ, kinh nghiệm và chuyên môn của các thành viên.',
                    'en' => 'Connect with Vietnam\'s mechanical engineering community. Explore member profiles, experience and expertise.'
                ],
                'keywords' => 'cộng đồng kỹ sư, thành viên mechamap, hồ sơ kỹ sư, kết nối chuyên gia',
                'focus_keyword' => 'cộng đồng kỹ sư',
                'canonical_url' => '/users',
                'breadcrumb_title' => 'Thành viên',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // ===== DYNAMIC ROUTES WITH PATTERNS =====

            // 4. Chi tiết chủ đề diễn đàn
            [
                'route_name' => 'threads.show',
                'url_pattern' => '/threads/*',
                'title' => '{thread_title} | Thảo luận Kỹ thuật | MechaMap',
                'title_i18n' => [
                    'vi' => '{thread_title} | Thảo luận Kỹ thuật | MechaMap',
                    'en' => '{thread_title} | Technical Discussion | MechaMap'
                ],
                'description' => 'Tham gia thảo luận về {thread_title} cùng cộng đồng kỹ sư cơ khí. Chia sẻ kinh nghiệm và học hỏi giải pháp kỹ thuật.',
                'description_i18n' => [
                    'vi' => 'Tham gia thảo luận về {thread_title} cùng cộng đồng kỹ sư cơ khí. Chia sẻ kinh nghiệm và học hỏi giải pháp kỹ thuật.',
                    'en' => 'Join the discussion about {thread_title} with the mechanical engineering community. Share experience and learn technical solutions.'
                ],
                'keywords' => 'thảo luận kỹ thuật, {thread_title}, diễn đàn cơ khí, giải pháp kỹ thuật',
                'focus_keyword' => 'thảo luận kỹ thuật',
                'canonical_url' => '/threads/{thread_slug}',
                'breadcrumb_title' => '{thread_title}',
                'article_type' => 'thread',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // 5. Chi tiết sản phẩm marketplace
            [
                'route_name' => 'marketplace.products.show',
                'url_pattern' => '/marketplace/products/*',
                'title' => '{product_name} - Thiết bị Cơ khí | MechaMap Marketplace',
                'title_i18n' => [
                    'vi' => '{product_name} - Thiết bị Cơ khí | MechaMap Marketplace',
                    'en' => '{product_name} - Mechanical Equipment | MechaMap Marketplace'
                ],
                'description' => 'Mua {product_name} chất lượng cao với giá tốt nhất. Thông tin chi tiết, đánh giá và so sánh sản phẩm từ nhà cung cấp uy tín.',
                'description_i18n' => [
                    'vi' => 'Mua {product_name} chất lượng cao với giá tốt nhất. Thông tin chi tiết, đánh giá và so sánh sản phẩm từ nhà cung cấp uy tín.',
                    'en' => 'Buy high-quality {product_name} at the best price. Detailed information, reviews and product comparison from trusted suppliers.'
                ],
                'keywords' => 'mua {product_name}, thiết bị cơ khí, marketplace, giá tốt, chất lượng',
                'focus_keyword' => 'mua {product_name}',
                'canonical_url' => '/marketplace/products/{product_slug}',
                'breadcrumb_title' => '{product_name}',
                'article_type' => 'product',
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
