<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General settings
        $generalSettings = [
            'site_name' => 'MechaMap',
            'site_tagline' => 'Diễn đàn cộng đồng kiến trúc và xây dựng',
            'site_description' => 'MechaMap là diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm về kiến trúc, xây dựng và nhiều lĩnh vực khác.',
            'site_keywords' => 'mechamap, diễn đàn, cộng đồng, forum, kiến trúc, xây dựng, chia sẻ, kiến thức',
            'site_logo' => '/storage/settings/logo.png',
            'site_favicon' => '/storage/settings/favicon.png',
            'site_banner' => '/storage/settings/banner.jpg',
            'site_email' => 'contact@mechamap.com',
            'site_phone' => '+84 123 456 789',
            'site_address' => 'Hà Nội, Việt Nam',
            'site_currency' => 'VND',
            'site_language' => 'vi',
            'site_timezone' => 'Asia/Ho_Chi_Minh',
            'site_date_format' => 'd/m/Y',
            'site_time_format' => 'H:i',
            'site_pagination' => '15',
            'site_maintenance_mode' => '0',
            'site_maintenance_message' => 'Trang web đang được bảo trì. Vui lòng quay lại sau.',
        ];

        foreach ($generalSettings as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        // Company settings
        $companySettings = [
            'company_name' => 'MechaMap JSC',
            'company_address' => 'Hà Nội, Việt Nam',
            'company_phone' => '+84 123 456 789',
            'company_email' => 'info@mechamap.com',
            'company_tax_code' => '0123456789',
            'company_website' => 'https://mechamap.com',
            'company_founded' => '2025',
        ];

        foreach ($companySettings as $key => $value) {
            Setting::set($key, $value, 'company');
        }

        // Contact settings
        $contactSettings = [
            'contact_email' => 'contact@mechamap.com',
            'contact_phone' => '+84 123 456 789',
            'contact_address' => 'Hà Nội, Việt Nam',
            'contact_map' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.0964841656167!2d105.84052531493254!3d21.028856985998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b72ca17a9!2zSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1620120000000!5m2!1svi!2s',
            'contact_form_recipients' => 'contact@mechamap.com,support@mechamap.com',
        ];

        foreach ($contactSettings as $key => $value) {
            Setting::set($key, $value, 'contact');
        }

        // Social settings
        $socialSettings = [
            'social_facebook' => 'https://facebook.com/mechamap',
            'social_twitter' => 'https://twitter.com/mechamap',
            'social_instagram' => 'https://instagram.com/mechamap',
            'social_youtube' => 'https://youtube.com/mechamap',
            'social_linkedin' => 'https://linkedin.com/company/mechamap',
            'social_tiktok' => 'https://tiktok.com/@mechamap',
        ];

        foreach ($socialSettings as $key => $value) {
            Setting::set($key, $value, 'social');
        }

        // API settings
        $apiSettings = [
            'google_analytics_id' => 'UA-XXXXXXXXX-X',
            'google_maps_api_key' => 'YOUR_GOOGLE_MAPS_API_KEY',
            'recaptcha_site_key' => 'YOUR_RECAPTCHA_SITE_KEY',
            'recaptcha_secret_key' => 'YOUR_RECAPTCHA_SECRET_KEY',
            'facebook_app_id' => 'YOUR_FACEBOOK_APP_ID',
            'facebook_app_secret' => 'YOUR_FACEBOOK_APP_SECRET',
            'google_client_id' => 'YOUR_GOOGLE_CLIENT_ID',
            'google_client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        ];

        foreach ($apiSettings as $key => $value) {
            Setting::set($key, $value, 'api');
        }

        // Copyright settings
        $copyrightSettings = [
            'copyright_text' => '© 2025 MechaMap. Tất cả các quyền được bảo lưu.',
            'copyright_owner' => 'MechaMap JSC',
            'copyright_year' => '2025',
            'terms_url' => '/terms',
            'privacy_url' => '/privacy',
            'cookie_url' => '/cookie-policy',
        ];

        foreach ($copyrightSettings as $key => $value) {
            Setting::set($key, $value, 'copyright');
        }

        // Tạo thư mục settings nếu chưa tồn tại
        if (!Storage::disk('public')->exists('settings')) {
            Storage::disk('public')->makeDirectory('settings');
        }

        // Copy các file mẫu vào thư mục storage/app/public/settings
        $this->copyDemoFiles();
    }

    /**
     * Copy các file mẫu vào thư mục storage
     */
    private function copyDemoFiles(): void
    {
        // Đường dẫn đến thư mục chứa các file mẫu
        $demoPath = database_path('seeders/demo-files');
        
        // Kiểm tra xem thư mục demo-files có tồn tại không
        if (!file_exists($demoPath)) {
            mkdir($demoPath, 0755, true);
        }

        // Danh sách các file cần copy
        $files = [
            'logo.png' => 'settings/logo.png',
            'favicon.png' => 'settings/favicon.png',
            'banner.jpg' => 'settings/banner.jpg',
        ];

        // Copy từng file
        foreach ($files as $source => $destination) {
            // Nếu file nguồn không tồn tại, tạo file mẫu
            if (!file_exists($demoPath . '/' . $source)) {
                // Tạo file mẫu tương ứng
                $this->createDemoFile($demoPath . '/' . $source, $source);
            }
            
            // Copy file vào storage
            if (file_exists($demoPath . '/' . $source)) {
                Storage::disk('public')->put(
                    $destination,
                    file_get_contents($demoPath . '/' . $source)
                );
            }
        }
    }

    /**
     * Tạo file mẫu
     */
    private function createDemoFile(string $path, string $filename): void
    {
        // Tạo thư mục nếu chưa tồn tại
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Tạo file mẫu tùy theo loại file
        if ($filename === 'logo.png' || $filename === 'favicon.png') {
            // Tạo hình vuông đơn giản với chữ M ở giữa
            $image = imagecreatetruecolor(200, 200);
            $background = imagecolorallocate($image, 127, 120, 87); // #7f7857
            $textColor = imagecolorallocate($image, 255, 255, 255);
            
            // Đổ màu nền
            imagefill($image, 0, 0, $background);
            
            // Thêm chữ M ở giữa
            imagestring($image, 5, 90, 90, 'M', $textColor);
            
            // Lưu file
            imagepng($image, $path);
            imagedestroy($image);
        } elseif ($filename === 'banner.jpg') {
            // Tạo banner đơn giản
            $image = imagecreatetruecolor(1200, 400);
            $background = imagecolorallocate($image, 127, 120, 87); // #7f7857
            $textColor = imagecolorallocate($image, 255, 255, 255);
            
            // Đổ màu nền
            imagefill($image, 0, 0, $background);
            
            // Thêm chữ MechaMap ở giữa
            imagestring($image, 5, 550, 190, 'MechaMap', $textColor);
            
            // Lưu file
            imagejpeg($image, $path);
            imagedestroy($image);
        }
    }
}
