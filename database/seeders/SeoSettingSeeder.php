<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder này tạo dữ liệu cho bảng seo_settings với 4 nhóm:
     * - general: Cấu hình SEO cơ bản
     * - social: Cấu hình mạng xã hội (Open Graph, Twitter Cards)
     * - advanced: Cấu hình nâng cao (scripts, CSS)
     * - robots: Cấu hình robots.txt
     */
    public function run(): void
    {
        // ====================================================================
        // GENERAL SEO SETTINGS (8 settings)
        // ====================================================================
        $generalSettings = [
            'site_title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'site_description' => 'MechaMap là nền tảng diễn đàn cộng đồng hàng đầu dành cho các kỹ sư cơ khí, chuyên gia thiết kế và sinh viên, chia sẻ kiến thức về CAD/CAM, thiết kế máy, công nghệ chế tạo, vật liệu kỹ thuật và automation.',
            'site_keywords' => 'mechamap, kỹ thuật cơ khí, cơ khí, mechanical engineering, CAD, CAM, thiết kế máy, chế tạo máy, automation, robotics, vật liệu kỹ thuật, FEA, CFD, manufacturing, vietnam',
            'allow_indexing' => '1',
            'google_analytics_id' => 'G-MECHAMAP2024',
            'google_search_console_id' => 'mechamap_gsc_verification_2024',
            'facebook_app_id' => '234567890123456',
            'twitter_username' => 'mechamap_vn',
        ];

        foreach ($generalSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'general');
        }

        // ====================================================================
        // SOCIAL MEDIA SEO SETTINGS (7 settings)
        // ====================================================================
        $socialSettings = [
            'og_title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'og_description' => 'Tham gia cộng đồng kỹ sư cơ khí MechaMap để học hỏi, chia sẻ kinh nghiệm về CAD/CAM, thiết kế máy, công nghệ chế tạo, automation và robotics. Kết nối với hàng nghìn chuyên gia trong ngành.',
            'og_image' => '/images/seo/mechamap-og-image.jpg',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'twitter_description' => 'Cộng đồng kỹ sư cơ khí hàng đầu Việt Nam. Chia sẻ kiến thức CAD/CAM, thiết kế máy, automation, robotics và công nghệ chế tạo hiện đại.',
            'twitter_image' => '/images/seo/mechamap-twitter-card.jpg',
        ];

        foreach ($socialSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'social');
        }

        // ====================================================================
        // ADVANCED SEO SETTINGS (4 settings)
        // ====================================================================
        $advancedSettings = [
            'header_scripts' => '<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-MECHAMAP2024"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag(\'js\', new Date());
  gtag(\'config\', \'G-MECHAMAP2024\');
</script>
<!-- End Google Analytics -->

<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,\'script\',
  \'https://connect.facebook.net/en_US/fbevents.js\');
  fbq(\'init\', \'234567890123456\');
  fbq(\'track\', \'PageView\');
</script>
<!-- End Facebook Pixel Code -->',

            'footer_scripts' => '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MECHAMAP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- MechaMap Analytics -->
<script>
// Track forum engagement
window.mechaMapAnalytics = {
    trackThreadView: function(threadId, category) {
        gtag(\'event\', \'thread_view\', {
            \'thread_id\': threadId,
            \'category\': category,
            \'event_category\': \'forum_engagement\'
        });
    },
    trackDownload: function(fileType, fileName) {
        gtag(\'event\', \'file_download\', {
            \'file_type\': fileType,
            \'file_name\': fileName,
            \'event_category\': \'resource_access\'
        });
    }
};
</script>

<!-- Crisp Chat for Technical Support -->
<script type="text/javascript">
window.$crisp=[];window.CRISP_WEBSITE_ID="mechamap-support-2024";
(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
</script>',

            'custom_css' => '/* MechaMap Custom SEO & Performance CSS */

/* Improve loading performance */
.lazy-load {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.lazy-load.loaded {
    opacity: 1;
}

/* Enhanced breadcrumb for SEO */
.breadcrumb-seo {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.breadcrumb-seo a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-seo a:hover {
    text-decoration: underline;
}

/* Technical content highlighting */
.tech-keyword {
    background: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%);
    background-repeat: no-repeat;
    background-size: 100% 0.2em;
    background-position: 0 88%;
    padding: 0 2px;
}

/* Engineering units styling */
.unit {
    font-size: 0.9em;
    font-weight: 500;
    color: #495057;
}

/* CAD file type badges */
.file-type-badge {
    display: inline-block;
    padding: 2px 6px;
    font-size: 0.75rem;
    font-weight: bold;
    border-radius: 3px;
    text-transform: uppercase;
}

.file-type-badge.dwg { background: #ff6b6b; color: white; }
.file-type-badge.step { background: #4ecdc4; color: white; }
.file-type-badge.iges { background: #45b7d1; color: white; }
.file-type-badge.pdf { background: #96ceb4; color: white; }',

            'canonical_url' => 'https://mechamap.vn',
        ];

        foreach ($advancedSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'advanced');
        }

        // ====================================================================
        // ROBOTS.TXT SETTINGS (1 setting)
        // ====================================================================
        $robotsSettings = [
            'robots_content' => 'User-agent: *
Allow: /
Allow: /forums/
Allow: /threads/
Allow: /categories/
Allow: /users/
Allow: /showcase/
Allow: /resources/

# Disallow admin and private areas
Disallow: /admin/
Disallow: /api/
Disallow: /private/
Disallow: /temp/
Disallow: /storage/logs/

# Disallow search and dynamic pages with parameters
Disallow: /search?*
Disallow: /*?page=*
Disallow: /*?sort=*
Disallow: /*?filter=*

# Disallow user-specific pages
Disallow: /settings/
Disallow: /profile/edit
Disallow: /notifications/
Disallow: /messages/

# Technical files
Disallow: /*.json$
Disallow: /*.xml$
Disallow: /*.txt$
Disallow: /*.log$

# Allow important static resources
Allow: /css/
Allow: /js/
Allow: /images/
Allow: /fonts/
Allow: /favicon.ico
Allow: /robots.txt

# Crawl-delay for respectful crawling
Crawl-delay: 1

# Sitemap location
Sitemap: ' . url('sitemap.xml') . '
Sitemap: ' . url('sitemap-images.xml') . '
Sitemap: ' . url('sitemap-forums.xml') . '
Sitemap: ' . url('sitemap-users.xml'),
        ];

        foreach ($robotsSettings as $key => $value) {
            SeoSetting::setValue($key, $value, 'robots');
        }

        // ====================================================================
        // OUTPUT CONFIRMATION
        // ====================================================================
        $this->command->info('✅ SEO Settings Seeder completed successfully!');
        $this->command->info('📊 Created ' . count($generalSettings) . ' general SEO settings');
        $this->command->info('📱 Created ' . count($socialSettings) . ' social media SEO settings');
        $this->command->info('⚙️ Created ' . count($advancedSettings) . ' advanced SEO settings');
        $this->command->info('🤖 Created ' . count($robotsSettings) . ' robots.txt settings');
        $this->command->info('🎯 Total SEO settings: ' . (
            count($generalSettings) +
            count($socialSettings) +
            count($advancedSettings) +
            count($robotsSettings)
        ));
    }
}
