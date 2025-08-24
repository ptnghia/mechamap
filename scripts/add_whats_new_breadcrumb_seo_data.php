<?php

/**
 * Script to add PageSeo data for whats-new routes to fix breadcrumb navigation
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\PageSeo;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 Adding PageSeo data for whats-new routes...\n";
echo "=" . str_repeat("=", 60) . "\n";

$routes = [
    // Main whats-new route
    [
        'route_name' => 'whats-new',
        'title' => 'Có gì mới',
        'breadcrumb_title' => 'Có gì mới',
        'vi' => 'Có gì mới',
        'en' => "What's New",
        'description' => 'Khám phá những bài viết, thảo luận và nội dung mới nhất từ cộng đồng kỹ sư cơ khí MechaMap',
        'description_en' => 'Discover the latest posts, discussions and content from the MechaMap mechanical engineering community'
    ],
    
    // Popular content
    [
        'route_name' => 'whats-new.popular',
        'title' => 'Nội dung phổ biến',
        'breadcrumb_title' => 'Phổ biến',
        'vi' => 'Phổ biến',
        'en' => 'Popular',
        'description' => 'Những bài viết được quan tâm nhất dựa trên điểm trending và lượt xem cao nhất',
        'description_en' => 'Most popular posts based on trending score and highest view count'
    ],
    
    // New threads
    [
        'route_name' => 'whats-new.threads',
        'title' => 'Chủ đề mới',
        'breadcrumb_title' => 'Chủ đề mới',
        'vi' => 'Chủ đề mới',
        'en' => 'New Threads',
        'description' => 'Danh sách các chủ đề thảo luận mới nhất được tạo bởi cộng đồng',
        'description_en' => 'List of newest discussion threads created by the community'
    ],
    
    // Hot topics
    [
        'route_name' => 'whats-new.hot-topics',
        'title' => 'Chủ đề nóng',
        'breadcrumb_title' => 'Chủ đề nóng',
        'vi' => 'Chủ đề nóng',
        'en' => 'Hot Topics',
        'description' => 'Những chủ đề có mức độ tương tác cao gần đây với điểm "nóng" cao',
        'description_en' => 'Topics with high recent engagement and hot score'
    ],
    
    // New media
    [
        'route_name' => 'whats-new.media',
        'title' => 'Phương tiện mới',
        'breadcrumb_title' => 'Phương tiện mới',
        'vi' => 'Phương tiện mới',
        'en' => 'New Media',
        'description' => 'Hình ảnh, video và file đính kèm mới nhất được tải lên trong các chủ đề thảo luận',
        'description_en' => 'Latest images, videos and attachments uploaded in discussion threads'
    ],
    
    // New showcases
    [
        'route_name' => 'whats-new.showcases',
        'title' => 'Showcase mới',
        'breadcrumb_title' => 'Showcase mới',
        'vi' => 'Showcase mới',
        'en' => 'New Showcases',
        'description' => 'Những dự án kỹ thuật mới nhất được trưng bày bởi cộng đồng',
        'description_en' => 'Latest engineering projects showcased by the community'
    ],
    
    // Threads looking for replies
    [
        'route_name' => 'whats-new.replies',
        'title' => 'Tìm kiếm trả lời',
        'breadcrumb_title' => 'Tìm kiếm trả lời',
        'vi' => 'Tìm kiếm trả lời',
        'en' => 'Looking for Replies',
        'description' => 'Những chủ đề đang cần sự giúp đỡ từ cộng đồng',
        'description_en' => 'Topics that need help from the community'
    ],
];

$created = 0;
$updated = 0;
$skipped = 0;

foreach ($routes as $route) {
    echo "\n📝 Processing route: {$route['route_name']}\n";
    
    try {
        $existing = PageSeo::where('route_name', $route['route_name'])->first();
        
        if (!$existing) {
            // Create new PageSeo entry
            $seo = new PageSeo();
            $seo->route_name = $route['route_name'];
            $seo->title = $route['title'];
            $seo->description = $route['description'];
            $seo->breadcrumb_title = $route['breadcrumb_title'];
            
            // Set multilingual data
            $seo->title_i18n = json_encode([
                'vi' => $route['title'],
                'en' => $route['en']
            ]);
            
            $seo->description_i18n = json_encode([
                'vi' => $route['description'],
                'en' => $route['description_en']
            ]);
            
            $seo->breadcrumb_title_i18n = json_encode([
                'vi' => $route['vi'],
                'en' => $route['en']
            ]);
            
            // SEO settings
            $seo->keywords = 'MechaMap, cơ khí, kỹ thuật, cộng đồng, thảo luận';
            $seo->keywords_i18n = json_encode([
                'vi' => 'MechaMap, cơ khí, kỹ thuật, cộng đồng, thảo luận',
                'en' => 'MechaMap, mechanical, engineering, community, discussion'
            ]);
            
            $seo->og_title = $route['title'];
            $seo->og_description = $route['description'];
            $seo->og_title_i18n = json_encode([
                'vi' => $route['title'],
                'en' => $route['en']
            ]);
            $seo->og_description_i18n = json_encode([
                'vi' => $route['description'],
                'en' => $route['description_en']
            ]);
            
            $seo->twitter_title = $route['title'];
            $seo->twitter_description = $route['description'];
            $seo->twitter_title_i18n = json_encode([
                'vi' => $route['title'],
                'en' => $route['en']
            ]);
            $seo->twitter_description_i18n = json_encode([
                'vi' => $route['description'],
                'en' => $route['description_en']
            ]);
            
            $seo->is_active = true;
            $seo->sitemap_include = true;
            $seo->sitemap_priority = 0.8;
            $seo->sitemap_changefreq = 'daily';
            
            $seo->save();
            $created++;
            echo "   ✅ Created: {$route['route_name']}\n";
            
        } else {
            // Update existing entry if breadcrumb data is missing
            $needsUpdate = false;
            
            if (empty($existing->breadcrumb_title_i18n)) {
                $existing->breadcrumb_title_i18n = json_encode([
                    'vi' => $route['vi'],
                    'en' => $route['en']
                ]);
                $needsUpdate = true;
            }
            
            if (empty($existing->breadcrumb_title)) {
                $existing->breadcrumb_title = $route['breadcrumb_title'];
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $existing->save();
                $updated++;
                echo "   🔄 Updated: {$route['route_name']}\n";
            } else {
                $skipped++;
                echo "   ⏭️ Skipped: {$route['route_name']} (already exists)\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error processing {$route['route_name']}: {$e->getMessage()}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Created: {$created}\n";
echo "   🔄 Updated: {$updated}\n";
echo "   ⏭️ Skipped: {$skipped}\n";
echo "   📝 Total processed: " . ($created + $updated + $skipped) . "\n";
echo "\n🎉 PageSeo data for whats-new routes setup completed!\n";
echo "📋 Next steps:\n";
echo "   1. Update BreadcrumbService to handle whats-new hierarchy\n";
echo "   2. Test breadcrumb navigation on whats-new pages\n";
echo "   3. Verify multilingual breadcrumb support\n";
