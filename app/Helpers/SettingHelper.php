<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('settings_group')) {
    /**
     * Get all settings by group
     *
     * @param string $group
     * @return array
     */
    function settings_group(string $group)
    {
        return Setting::getGroup($group);
    }
}

if (!function_exists('get_logo_url')) {
    /**
     * Get the logo URL
     *
     * @param string $default
     * @return string
     */
    function get_logo_url(string $default = '/images/logo.png')
    {
        return setting('site_logo', $default);
    }
}

if (!function_exists('get_favicon_url')) {
    /**
     * Get the favicon URL
     *
     * @param string $default
     * @return string
     */
    function get_favicon_url(string $default = '/images/favicon.ico')
    {
        return setting('site_favicon', $default);
    }
}

if (!function_exists('get_banner_url')) {
    /**
     * Get the header banner URL
     *
     * @param string $default
     * @return string
     */
    function get_banner_url(string $default = '/images/banner.webp')
    {
        return setting('site_banner', $default);
    }
}

if (!function_exists('get_site_name')) {
    /**
     * Get the site name
     *
     * @param string $default
     * @return string
     */
    function get_site_name(string $default = null)
    {
        return setting('site_name', $default ?: config('app.name'));
    }
}

if (!function_exists('get_company_info')) {
    /**
     * Get company information
     *
     * @return array
     */
    function get_company_info()
    {
        return [
            'name' => setting('company_name', config('app.name')),
            'address' => setting('company_address', ''),
            'phone' => setting('company_phone', ''),
            'email' => setting('company_email', ''),
            'tax_id' => setting('company_tax_id', ''),
            'registration_number' => setting('company_registration_number', ''),
            'founded_year' => setting('company_founded_year', ''),
            'description' => setting('company_description', ''),
        ];
    }
}

if (!function_exists('get_contact_info')) {
    /**
     * Get contact information
     *
     * @return array
     */
    function get_contact_info()
    {
        return [
            'email' => setting('contact_email', ''),
            'phone' => setting('contact_phone', ''),
            'address' => setting('contact_address', ''),
            'working_hours' => setting('contact_working_hours', ''),
            'map_embed' => setting('contact_map_embed', ''),
            'latitude' => setting('contact_latitude', ''),
            'longitude' => setting('contact_longitude', ''),
        ];
    }
}

if (!function_exists('get_social_links')) {
    /**
     * Get social media links
     *
     * @return array
     */
    function get_social_links()
    {
        return [
            'facebook' => setting('social_facebook', ''),
            'twitter' => setting('social_twitter', ''),
            'instagram' => setting('social_instagram', ''),
            'linkedin' => setting('social_linkedin', ''),
            'youtube' => setting('social_youtube', ''),
            'tiktok' => setting('social_tiktok', ''),
            'pinterest' => setting('social_pinterest', ''),
            'github' => setting('social_github', ''),
        ];
    }
}

if (!function_exists('get_api_keys')) {
    /**
     * Get API keys
     *
     * @return array
     */
    function get_api_keys()
    {
        return [
            'google_client_id' => setting('api_google_client_id', ''),
            'google_client_secret' => setting('api_google_client_secret', ''),
            'facebook_app_id' => setting('api_facebook_app_id', ''),
            'facebook_app_secret' => setting('api_facebook_app_secret', ''),
            'recaptcha_site_key' => setting('api_recaptcha_site_key', ''),
            'recaptcha_secret_key' => setting('api_recaptcha_secret_key', ''),
        ];
    }
}

if (!function_exists('get_copyright_info')) {
    /**
     * Get copyright information
     *
     * @return array
     */
    function get_copyright_info()
    {
        return [
            'text' => setting('copyright_text', '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.'),
            'owner' => setting('copyright_owner', config('app.name')),
            'year' => setting('copyright_year', date('Y')),
            'domain' => setting('site_domain', request()->getHost()),
        ];
    }
}

if (!function_exists('placeholder_image')) {
    /**
     * Generate local placeholder image URL
     *
     * @param int $width
     * @param int $height
     * @param string $text
     * @param string $bgColor
     * @param string $textColor
     * @return string
     */
    function placeholder_image(int $width = 300, int $height = null, string $text = '', string $bgColor = 'cccccc', string $textColor = '666666')
    {
        $height = $height ?: $width; // Square by default
        $text = $text ?: "{$width}x{$height}";

        // Sử dụng service local hoặc alternative
        $alternatives = [
            "https://picsum.photos/{$width}/{$height}", // Lorem Picsum
            "https://source.unsplash.com/{$width}x{$height}/?abstract", // Unsplash
            "https://dummyimage.com/{$width}x{$height}/{$bgColor}/{$textColor}&text=" . urlencode($text), // DummyImage
        ];

        // Nếu có local placeholder generator, ưu tiên local
        $publicPath = function_exists('public_path') ? public_path('images/placeholders') : __DIR__ . '/../../public/images/placeholders';
        $localFile = $publicPath . "/{$width}x{$height}.png";

        if (file_exists($localFile)) {
            if (function_exists('asset')) {
                return asset("images/placeholders/{$width}x{$height}.png");
            } else {
                // Fallback cho standalone script
                return "https://mechamap.test/images/placeholders/{$width}x{$height}.png";
            }
        }

        // Fallback về alternatives
        return $alternatives[0];
    }
}

if (!function_exists('avatar_placeholder')) {
    /**
     * Generate avatar placeholder
     *
     * @param string $name
     * @param int $size
     * @return string
     */
    function avatar_placeholder(string $name = '')
    {
        if (empty($name)) {
            $name = 'User';
        }

        // Lấy chữ cái đầu (tối đa 2 ký tự)
        $initials = strtoupper(substr($name, 0, 1));
        if (strpos($name, ' ') !== false) {
            $nameParts = explode(' ', $name);
            $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
        }

        // Sử dụng avatar generator mới (kích thước cố định 150px)
        return route('avatar.generate', ['initial' => $initials]);
    }
}
