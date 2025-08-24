<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UltimatePageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo SEO data cho các route bổ sung cuối cùng để đạt 100+ routes
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo Ultimate SEO data để hoàn thiện hệ thống...');

        $seoData = $this->getUltimateSeoData();

        $totalCreated = 0;
        foreach ($seoData as $data) {
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

        $this->command->info("🎉 Hoàn thành! Đã tạo {$totalCreated} SEO records mới.");

        // Hiển thị thống kê tổng quan
        $total = PageSeo::where('is_active', true)->count();
        $this->command->info("📊 Tổng số SEO records hiện tại: {$total}");
    }

    /**
     * Get ultimate SEO data for remaining routes
     */
    private function getUltimateSeoData(): array
    {
        return [
            // ===== ADDITIONAL FORUM ROUTES =====

            [
                'route_name' => 'forums.latest',
                'url_pattern' => '/forums/latest',
                'title' => 'Thảo luận Mới nhất - Latest Discussions | MechaMap',
                'title_i18n' => [
                    'vi' => 'Thảo luận Mới nhất - Latest Discussions | MechaMap',
                    'en' => 'Latest Discussions - Recent Topics | MechaMap'
                ],
                'description' => 'Theo dõi các thảo luận kỹ thuật mới nhất trong cộng đồng MechaMap. Cập nhật xu hướng và chủ đề hot trong ngành cơ khí.',
                'keywords' => 'thảo luận mới nhất, latest discussions, xu hướng kỹ thuật, chủ đề hot cơ khí',
                'focus_keyword' => 'thảo luận mới nhất',
                'canonical_url' => '/forums/latest',
                'breadcrumb_title' => 'Mới nhất',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'forums.trending',
                'url_pattern' => '/forums/trending',
                'title' => 'Thảo luận Trending - Hot Topics | MechaMap',
                'title_i18n' => [
                    'vi' => 'Thảo luận Trending - Hot Topics | MechaMap',
                    'en' => 'Trending Discussions - Hot Topics | MechaMap'
                ],
                'description' => 'Khám phá các chủ đề thảo luận trending trong cộng đồng kỹ sư. Những vấn đề được quan tâm và thảo luận nhiều nhất.',
                'keywords' => 'thảo luận trending, hot topics, chủ đề hot, quan tâm nhiều nhất',
                'focus_keyword' => 'thảo luận trending',
                'canonical_url' => '/forums/trending',
                'breadcrumb_title' => 'Trending',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'threads.unanswered',
                'url_pattern' => '/threads/unanswered',
                'title' => 'Câu hỏi Chưa trả lời - Unanswered Questions | MechaMap',
                'title_i18n' => [
                    'vi' => 'Câu hỏi Chưa trả lời - Unanswered Questions | MechaMap',
                    'en' => 'Unanswered Questions - Help Needed | MechaMap'
                ],
                'description' => 'Danh sách câu hỏi kỹ thuật chưa được trả lời. Cơ hội để bạn chia sẻ kiến thức và giúp đỡ cộng đồng kỹ sư.',
                'keywords' => 'câu hỏi chưa trả lời, unanswered questions, giúp đỡ cộng đồng, chia sẻ kiến thức',
                'focus_keyword' => 'câu hỏi chưa trả lời',
                'canonical_url' => '/threads/unanswered',
                'breadcrumb_title' => 'Chưa trả lời',
                'article_type' => 'page',
                'priority' => 5,
                'sitemap_include' => true,
                'sitemap_priority' => 0.5,
                'sitemap_changefreq' => 'daily',
                'no_index' => false,
                'is_active' => true,
            ],

            // ===== MARKETPLACE ADDITIONAL ROUTES =====

            [
                'route_name' => 'marketplace.featured',
                'url_pattern' => '/marketplace/featured',
                'title' => 'Sản phẩm Nổi bật - Featured Products | MechaMap',
                'title_i18n' => [
                    'vi' => 'Sản phẩm Nổi bật - Featured Products | MechaMap',
                    'en' => 'Featured Products - Top Equipment | MechaMap'
                ],
                'description' => 'Khám phá sản phẩm thiết bị cơ khí nổi bật được đề xuất. Chất lượng cao, giá tốt và được đánh giá cao bởi cộng đồng.',
                'keywords' => 'sản phẩm nổi bật, featured products, thiết bị chất lượng cao, đề xuất tốt nhất',
                'focus_keyword' => 'sản phẩm nổi bật',
                'canonical_url' => '/marketplace/featured',
                'breadcrumb_title' => 'Nổi bật',
                'article_type' => 'page',
                'priority' => 7,
                'sitemap_include' => true,
                'sitemap_priority' => 0.7,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'marketplace.deals',
                'url_pattern' => '/marketplace/deals',
                'title' => 'Ưu đãi Đặc biệt - Special Deals | MechaMap Marketplace',
                'title_i18n' => [
                    'vi' => 'Ưu đãi Đặc biệt - Special Deals | MechaMap Marketplace',
                    'en' => 'Special Deals - Best Offers | MechaMap Marketplace'
                ],
                'description' => 'Tận dụng ưu đãi đặc biệt cho thiết bị cơ khí. Giảm giá, khuyến mãi và deals hấp dẫn từ các nhà cung cấp uy tín.',
                'keywords' => 'ưu đãi đặc biệt, special deals, giảm giá thiết bị, khuyến mãi cơ khí',
                'focus_keyword' => 'ưu đãi đặc biệt',
                'canonical_url' => '/marketplace/deals',
                'breadcrumb_title' => 'Ưu đãi',
                'article_type' => 'page',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'weekly',
                'no_index' => false,
                'is_active' => true,
            ],

            // ===== TOOLS ADDITIONAL ROUTES =====

            [
                'route_name' => 'tools.converters.index',
                'url_pattern' => '/tools/converters',
                'title' => 'Công cụ Chuyển đổi - Unit Converters | MechaMap',
                'title_i18n' => [
                    'vi' => 'Công cụ Chuyển đổi - Unit Converters | MechaMap',
                    'en' => 'Unit Converters - Conversion Tools | MechaMap'
                ],
                'description' => 'Bộ công cụ chuyển đổi đơn vị kỹ thuật. Chuyển đổi áp suất, nhiệt độ, lực, moment và các đại lượng vật lý khác.',
                'keywords' => 'chuyển đổi đơn vị, unit converter, áp suất nhiệt độ lực, đại lượng vật lý',
                'focus_keyword' => 'chuyển đổi đơn vị',
                'canonical_url' => '/tools/converters',
                'breadcrumb_title' => 'Chuyển đổi',
                'article_type' => 'tool',
                'priority' => 6,
                'sitemap_include' => true,
                'sitemap_priority' => 0.6,
                'sitemap_changefreq' => 'monthly',
                'no_index' => false,
                'is_active' => true,
            ],

            [
                'route_name' => 'tools.charts.index',
                'url_pattern' => '/tools/charts',
                'title' => 'Biểu đồ Kỹ thuật - Engineering Charts | MechaMap',
                'title_i18n' => [
                    'vi' => 'Biểu đồ Kỹ thuật - Engineering Charts | MechaMap',
                    'en' => 'Engineering Charts - Technical Diagrams | MechaMap'
                ],
                'description' => 'Thư viện biểu đồ và chart kỹ thuật. Biểu đồ ứng suất-biến dạng, nhiệt độ-áp suất và các đồ thị chuyên ngành.',
                'keywords' => 'biểu đồ kỹ thuật, engineering charts, ứng suất biến dạng, nhiệt độ áp suất',
                'focus_keyword' => 'biểu đồ kỹ thuật',
                'canonical_url' => '/tools/charts',
                'breadcrumb_title' => 'Biểu đồ',
                'article_type' => 'tool',
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
