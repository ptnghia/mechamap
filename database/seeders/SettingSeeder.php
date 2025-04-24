<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General Settings
        $generalSettings = [
            'site_name' => 'MechaMap',
            'site_slogan' => 'Diễn đàn cộng đồng chia sẻ kiến thức',
            'site_logo' => '/images/logo.png',
            'site_favicon' => '/images/favicon.ico',
            'site_theme' => 'default',
            'site_language' => 'vi',
            'site_timezone' => 'Asia/Ho_Chi_Minh',
            'site_date_format' => 'd/m/Y',
            'site_time_format' => 'H:i',
            'site_status' => 'online',
            'site_maintenance_message' => 'Trang web đang được bảo trì. Vui lòng quay lại sau.',
            'site_domain' => 'mechamap.com',
        ];

        foreach ($generalSettings as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        // Company Settings
        $companySettings = [
            'company_name' => 'MechaMap JSC',
            'company_address' => '123 Đường ABC, Quận 1, TP. Hồ Chí Minh',
            'company_phone' => '(+84) 28 1234 5678',
            'company_email' => 'info@mechamap.com',
            'company_tax_id' => '0123456789',
            'company_registration_number' => 'DKKD-123456789',
            'company_founded_year' => '2023',
            'company_description' => 'MechaMap là công ty công nghệ chuyên phát triển các nền tảng cộng đồng và chia sẻ kiến thức.',
        ];

        foreach ($companySettings as $key => $value) {
            Setting::set($key, $value, 'company');
        }

        // Contact Settings
        $contactSettings = [
            'contact_email' => 'contact@mechamap.com',
            'contact_phone' => '(+84) 28 1234 5678',
            'contact_address' => '123 Đường ABC, Quận 1, TP. Hồ Chí Minh',
            'contact_working_hours' => 'Thứ 2 - Thứ 6: 8:00 - 17:30',
            'contact_map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4241674197956!2d106.69916857486087!3d10.777938089387898!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4670702e31%3A0xa5777fb3a5bb9972!2sBitexco%20Financial%20Tower!5e0!3m2!1sen!2s!4v1682152762005!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'contact_latitude' => '10.777939',
            'contact_longitude' => '106.701357',
        ];

        foreach ($contactSettings as $key => $value) {
            Setting::set($key, $value, 'contact');
        }

        // Social Media Settings
        $socialSettings = [
            'social_facebook' => 'https://facebook.com/mechamap',
            'social_twitter' => 'https://twitter.com/mechamap',
            'social_instagram' => 'https://instagram.com/mechamap',
            'social_linkedin' => 'https://linkedin.com/company/mechamap',
            'social_youtube' => 'https://youtube.com/c/mechamap',
            'social_tiktok' => 'https://tiktok.com/@mechamap',
            'social_pinterest' => '',
            'social_github' => 'https://github.com/mechamap',
        ];

        foreach ($socialSettings as $key => $value) {
            Setting::set($key, $value, 'social');
        }

        // API Settings
        $apiSettings = [
            'api_google_client_id' => '123456789012-abcdefghijklmnopqrstuvwxyz123456.apps.googleusercontent.com',
            'api_google_client_secret' => 'GOCSPX-abcdefghijklmnopqrstuvwxyz',
            'api_facebook_app_id' => '123456789012345',
            'api_facebook_app_secret' => 'abcdefghijklmnopqrstuvwxyz123456',
            'api_recaptcha_site_key' => '6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'api_recaptcha_secret_key' => '6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        ];

        foreach ($apiSettings as $key => $value) {
            Setting::set($key, $value, 'api');
        }

        // Copyright Settings
        $copyrightSettings = [
            'copyright_text' => '© 2023 MechaMap. Tất cả các quyền được bảo lưu.',
            'copyright_owner' => 'MechaMap JSC',
            'copyright_year' => '2023',
        ];

        foreach ($copyrightSettings as $key => $value) {
            Setting::set($key, $value, 'copyright');
        }

        // Forum Settings
        $forumSettings = [
            'forum_threads_per_page' => '20',
            'forum_posts_per_page' => '15',
            'forum_allow_guest_view' => '1',
            'forum_require_email_verification' => '1',
            'forum_allow_file_uploads' => '1',
            'forum_max_file_size' => '5', // MB
            'forum_allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip',
            'forum_max_files_per_post' => '5',
            'forum_enable_reactions' => '1',
            'forum_enable_signatures' => '1',
            'forum_signature_max_length' => '500',
            'forum_enable_polls' => '1',
            'forum_poll_max_options' => '10',
            'forum_poll_max_votes' => '1',
        ];

        foreach ($forumSettings as $key => $value) {
            Setting::set($key, $value, 'forum');
        }

        // User Settings
        $userSettings = [
            'user_allow_registration' => '1',
            'user_require_email_verification' => '1',
            'user_allow_social_login' => '1',
            'user_default_role' => 'member',
            'user_avatar_max_size' => '2', // MB
            'user_avatar_allowed_types' => 'jpg,jpeg,png,gif',
            'user_min_password_length' => '8',
            'user_require_strong_password' => '1',
            'user_allow_profile_customization' => '1',
            'user_allow_username_change' => '0',
            'user_username_min_length' => '3',
            'user_username_max_length' => '20',
        ];

        foreach ($userSettings as $key => $value) {
            Setting::set($key, $value, 'user');
        }

        // Email Settings
        $emailSettings = [
            'email_from_address' => 'noreply@mechamap.com',
            'email_from_name' => 'MechaMap',
            'email_reply_to' => 'support@mechamap.com',
            'email_welcome_subject' => 'Chào mừng bạn đến với MechaMap',
            'email_verification_subject' => 'Xác nhận địa chỉ email của bạn',
            'email_password_reset_subject' => 'Đặt lại mật khẩu của bạn',
            'email_notification_subject' => 'Thông báo mới từ MechaMap',
        ];

        foreach ($emailSettings as $key => $value) {
            Setting::set($key, $value, 'email');
        }
    }
}
