<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General SEO Settings
        $generalSettings = [
            'site_title' => 'MechaMap - Diễn đàn cộng đồng',
            'site_description' => 'MechaMap là diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
            'site_keywords' => 'mechamap, diễn đàn, cộng đồng, forum, công nghệ, lập trình, thiết kế, chia sẻ, kiến thức',
            'allow_indexing' => '1',
            'google_analytics_id' => 'G-XXXXXXXXXX',
            'google_search_console_id' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'facebook_app_id' => '123456789012345',
            'twitter_username' => 'mechamap',
        ];

        foreach ($generalSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'general');
        }

        // Social SEO Settings
        $socialSettings = [
            'og_title' => 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức',
            'og_description' => 'Tham gia MechaMap để chia sẻ và học hỏi kiến thức từ cộng đồng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
            'og_image' => '/images/og-image.jpg',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức',
            'twitter_description' => 'Tham gia MechaMap để chia sẻ và học hỏi kiến thức từ cộng đồng về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.',
            'twitter_image' => '/images/twitter-image.jpg',
        ];

        foreach ($socialSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'social');
        }

        // Advanced SEO Settings
        $advancedSettings = [
            'header_scripts' => '<!-- Header Scripts -->',
            'footer_scripts' => '<!-- Footer Scripts -->',
            'custom_css' => '/* Custom CSS */',
            'canonical_url' => '',
        ];

        foreach ($advancedSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'advanced');
        }

        // Robots.txt Settings
        $robotsSettings = [
            'robots_content' => "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /profile/\nDisallow: /api/\n\nSitemap: " . url('sitemap.xml'),
        ];

        foreach ($robotsSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'robots');
        }
    }
}
