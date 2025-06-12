<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder này tạo dữ liệu cho bảng settings với 8 nhóm chính:
     * - general: Cấu hình tổng quan website (18 settings)
     * - company: Thông tin công ty (10 settings)
     * - contact: Thông tin liên hệ (7 settings)
     * - social: Mạng xã hội (8 settings)
     * - forum: Cấu hình diễn đàn (20 settings)
     * - user: Quản lý người dùng (15 settings)
     * - email: Cấu hình email (10 settings)
     * - api: Cấu hình API (8 settings)
     */
    public function run(): void
    {
        // ====================================================================
        // GENERAL SETTINGS - Cấu hình tổng quan (18 settings)
        // ====================================================================
        $generalSettings = [
            'site_name' => 'MechaMap',
            'site_slogan' => 'Cộng đồng Kỹ thuật Cơ khí Việt Nam',
            'site_logo' => '/images/brand/mechamap-logo.png',
            'site_favicon' => '/images/brand/mechamap-favicon.ico',
            'site_banner' => '/images/brand/mechamap-banner.jpg',
            'site_theme' => 'mechanical-blue',
            'site_language' => 'vi',
            'site_timezone' => 'Asia/Ho_Chi_Minh',
            'site_date_format' => 'd/m/Y',
            'site_time_format' => 'H:i',
            'site_status' => 'online',
            'site_maintenance_mode' => '0',
            'site_maintenance_message' => 'MechaMap đang được nâng cấp để phục vụ cộng đồng kỹ sư cơ khí tốt hơn. Vui lòng quay lại sau 30 phút.',
            'site_domain' => 'mechamap.vn',
            'site_tagline' => 'Nơi hội tụ tri thức cơ khí',
            'site_welcome_message' => 'Chào mừng bạn đến với MechaMap - Cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam. Tham gia ngay để kết nối với hàng nghìn kỹ sư và chuyên gia trong ngành!',
            'site_analytics_enabled' => '1',
            'site_cookie_consent' => '1',
        ];

        foreach ($generalSettings as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        // ====================================================================
        // COMPANY SETTINGS - Thông tin công ty (10 settings)
        // ====================================================================
        $companySettings = [
            'company_name' => 'Công ty Cổ phần Công nghệ MechaMap',
            'company_address' => 'Tầng 15, Tòa nhà Bitexco Financial Tower, 2 Hải Triều, Quận 1, TP. Hồ Chí Minh',
            'company_phone' => '(+84) 28 3911 8888',
            'company_email' => 'contact@mechamap.vn',
            'company_tax_id' => '0316789012',
            'company_registration_number' => 'ĐKKD-0316789012',
            'company_founded_year' => '2023',
            'company_description' => 'MechaMap JSC là công ty công nghệ hàng đầu chuyên phát triển nền tảng số cho ngành kỹ thuật cơ khí, kết nối cộng đồng kỹ sư và thúc đẩy đổi mới sáng tạo trong lĩnh vực manufacturing và automation.',
            'company_vision' => 'Trở thành nền tảng công nghệ hàng đầu Đông Nam Á cho cộng đồng kỹ thuật cơ khí',
            'company_mission' => 'Kết nối tri thức, thúc đẩy đổi mới và phát triển nguồn nhân lực chất lượng cao trong ngành cơ khí',
        ];

        foreach ($companySettings as $key => $value) {
            Setting::set($key, $value, 'company');
        }

        // ====================================================================
        // CONTACT SETTINGS - Thông tin liên hệ (7 settings)
        // ====================================================================
        $contactSettings = [
            'contact_email' => 'support@mechamap.vn',
            'contact_phone' => '(+84) 28 3911 8888',
            'contact_address' => 'Tầng 15, Tòa nhà Bitexco Financial Tower, 2 Hải Triều, Quận 1, TP. Hồ Chí Minh',
            'contact_working_hours' => 'Thứ 2 - Thứ 6: 8:30 - 17:30 | Thứ 7: 8:30 - 12:00',
            'contact_emergency_phone' => '(+84) 901 234 567',
            'contact_map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4241674197956!2d106.69916857486087!3d10.777938089387898!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4670702e31%3A0xa5777fb3a5bb9972!2sBitexco%20Financial%20Tower!5e0!3m2!1sen!2s!4v1682152762005!5m2!1sen!2s" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'contact_business_hours_note' => 'Hỗ trợ kỹ thuật 24/7 qua hotline và chat online',
        ];

        foreach ($contactSettings as $key => $value) {
            Setting::set($key, $value, 'contact');
        }

        // ====================================================================
        // SOCIAL MEDIA SETTINGS - Mạng xã hội (8 settings)
        // ====================================================================
        $socialSettings = [
            'social_facebook' => 'https://facebook.com/mechamap.vietnam',
            'social_twitter' => 'https://twitter.com/mechamap_vn',
            'social_instagram' => 'https://instagram.com/mechamap.vietnam',
            'social_linkedin' => 'https://linkedin.com/company/mechamap-vietnam',
            'social_youtube' => 'https://youtube.com/@MechaMapVietnam',
            'social_tiktok' => 'https://tiktok.com/@mechamap.vietnam',
            'social_github' => 'https://github.com/mechamap-platform',
            'social_telegram' => 'https://t.me/mechamap_vietnam',
        ];

        foreach ($socialSettings as $key => $value) {
            Setting::set($key, $value, 'social');
        }

        // ====================================================================
        // FORUM SETTINGS - Cấu hình diễn đàn (20 settings)
        // ====================================================================
        $forumSettings = [
            'forum_threads_per_page' => '25',
            'forum_posts_per_page' => '20',
            'forum_allow_guest_view' => '1',
            'forum_require_email_verification' => '1',
            'forum_allow_file_uploads' => '1',
            'forum_max_file_size' => '10', // MB - tăng cho file CAD
            'forum_allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar,dwg,step,iges,stp,sat,x_t,prt,asm,ipt,iam',
            'forum_max_files_per_post' => '8',
            'forum_enable_reactions' => '1',
            'forum_enable_signatures' => '1',
            'forum_signature_max_length' => '300',
            'forum_enable_polls' => '1',
            'forum_poll_max_options' => '12',
            'forum_poll_max_votes' => '1',
            'forum_enable_latex' => '1', // Hỗ trợ công thức toán học
            'forum_enable_code_highlighting' => '1', // Syntax highlighting cho code
            'forum_enable_technical_tags' => '1', // Tags chuyên ngành cơ khí
            'forum_auto_approve_posts' => '0', // Kiểm duyệt bài viết kỹ thuật
            'forum_hot_threshold' => '50', // Ngưỡng thread hot
            'forum_trending_algorithm' => 'engagement_based', // Thuật toán trending
        ];

        foreach ($forumSettings as $key => $value) {
            Setting::set($key, $value, 'forum');
        }

        // ====================================================================
        // USER SETTINGS - Quản lý người dùng (15 settings)
        // ====================================================================
        $userSettings = [
            'user_allow_registration' => '1',
            'user_require_email_verification' => '1',
            'user_allow_social_login' => '1',
            'user_default_role' => 'member',
            'user_avatar_max_size' => '3', // MB
            'user_avatar_allowed_types' => 'jpg,jpeg,png,gif,webp',
            'user_min_password_length' => '8',
            'user_require_strong_password' => '1',
            'user_allow_profile_customization' => '1',
            'user_allow_username_change' => '0',
            'user_username_min_length' => '3',
            'user_username_max_length' => '25',
            'user_enable_professional_profiles' => '1', // Hồ sơ nghề nghiệp
            'user_require_field_verification' => '1', // Xác thực chuyên ngành
            'user_enable_skill_badges' => '1', // Huy hiệu kỹ năng
        ];

        foreach ($userSettings as $key => $value) {
            Setting::set($key, $value, 'user');
        }

        // ====================================================================
        // EMAIL SETTINGS - Cấu hình email (10 settings)
        // ====================================================================
        $emailSettings = [
            'email_from_address' => 'no-reply@mechamap.vn',
            'email_from_name' => 'MechaMap Platform',
            'email_reply_to' => 'support@mechamap.vn',
            'email_smtp_host' => 'mail.mechamap.vn',
            'email_smtp_port' => '587',
            'email_smtp_encryption' => 'tls',
            'email_smtp_username' => 'no-reply@mechamap.vn',
            'email_smtp_password' => '', // Sẽ được set qua admin
            'email_signature' => 'Trân trọng,<br>Đội ngũ MechaMap<br><a href="https://mechamap.vn">mechamap.vn</a>',
            'email_notification_frequency' => 'instant', // instant, daily, weekly
        ];

        foreach ($emailSettings as $key => $value) {
            Setting::set($key, $value, 'email');
        }

        // ====================================================================
        // API SETTINGS - Cấu hình API (8 settings)
        // ====================================================================
        $apiSettings = [
            'api_rate_limit' => '1000', // requests per hour
            'api_enable_cors' => '1',
            'api_allowed_origins' => 'https://mechamap.vn,https://app.mechamap.vn',
            'api_google_client_id' => '', // Sẽ được config qua admin
            'api_google_client_secret' => '', // Sẽ được config qua admin
            'api_facebook_app_id' => '', // Sẽ được config qua admin
            'api_recaptcha_site_key' => '', // Sẽ được config qua admin
            'api_recaptcha_secret_key' => '', // Sẽ được config qua admin
        ];

        foreach ($apiSettings as $key => $value) {
            Setting::set($key, $value, 'api');
        }

        // ====================================================================
        // ADDITIONAL SETTINGS FOR MECHANICAL ENGINEERING FOCUS
        // ====================================================================

        // Security Settings
        $securitySettings = [
            'security_enable_2fa' => '1',
            'security_session_timeout' => '120', // minutes
            'security_max_login_attempts' => '5',
            'security_login_lockout_time' => '15', // minutes
            'security_password_expiry' => '90', // days
            'security_enable_ssl' => '1',
            'security_enable_csrf_protection' => '1',
            'security_ip_whitelist' => '',
            'security_banned_ips' => '',
            'security_enable_audit_log' => '1',
            'security_data_retention_days' => '365',
        ];

        foreach ($securitySettings as $key => $value) {
            Setting::set($key, $value, 'security');
        }

        // Forum Advanced Settings cho Mechanical Engineering
        $forumAdvancedSettings = [
            'forum_enable_cad_preview' => '1', // Preview file CAD
            'forum_enable_technical_calculator' => '1', // Máy tính kỹ thuật
            'forum_enable_unit_converter' => '1', // Chuyển đổi đơn vị
            'forum_enable_material_database' => '1', // Database vật liệu
            'forum_enable_standard_references' => '1', // Tham chiếu tiêu chuẩn
            'forum_enable_expert_verification' => '1', // Xác minh chuyên gia
            'forum_enable_solution_marking' => '1', // Đánh dấu giải pháp
            'forum_enable_technical_difficulty' => '1', // Mức độ khó kỹ thuật
        ];

        foreach ($forumAdvancedSettings as $key => $value) {
            Setting::set($key, $value, 'forum_advanced');
        }

        // Copyright Settings
        $copyrightSettings = [
            'copyright_text' => '© 2024 MechaMap. Bản quyền thuộc về Công ty Cổ phần Công nghệ MechaMap.',
            'copyright_owner' => 'MechaMap JSC',
            'copyright_year' => '2024',
            'copyright_notice' => 'Mọi nội dung trên website này được bảo vệ bởi luật bản quyền Việt Nam và quốc tế.',
        ];

        foreach ($copyrightSettings as $key => $value) {
            Setting::set($key, $value, 'copyright');
        }

        // ====================================================================
        // OUTPUT CONFIRMATION
        // ====================================================================
        $totalSettings = count($generalSettings) + count($companySettings) +
                        count($contactSettings) + count($socialSettings) +
                        count($forumSettings) + count($userSettings) +
                        count($emailSettings) + count($apiSettings) +
                        count($securitySettings) + count($forumAdvancedSettings) +
                        count($copyrightSettings);

        $this->command->info('✅ Settings Seeder completed successfully!');
        $this->command->info('🏢 Created ' . count($generalSettings) . ' general settings');
        $this->command->info('🏬 Created ' . count($companySettings) . ' company settings');
        $this->command->info('📞 Created ' . count($contactSettings) . ' contact settings');
        $this->command->info('📱 Created ' . count($socialSettings) . ' social media settings');
        $this->command->info('💬 Created ' . count($forumSettings) . ' forum settings');
        $this->command->info('👥 Created ' . count($userSettings) . ' user management settings');
        $this->command->info('📧 Created ' . count($emailSettings) . ' email settings');
        $this->command->info('🔌 Created ' . count($apiSettings) . ' API settings');
        $this->command->info('🔐 Created ' . count($securitySettings) . ' security settings');
        $this->command->info('⚙️ Created ' . count($forumAdvancedSettings) . ' forum advanced settings');
        $this->command->info('©️ Created ' . count($copyrightSettings) . ' copyright settings');
        $this->command->info('🎯 Total settings: ' . $totalSettings);
        $this->command->info('🔧 MechaMap platform configured for Mechanical Engineering community!');
    }
}
