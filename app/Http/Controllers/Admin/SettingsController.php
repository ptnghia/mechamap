<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Hiển thị trang cấu hình chung
     */
    public function general(): View
    {
        // Lấy các cài đặt chung
        $settings = Setting::getGroup('general');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Cấu hình chung', 'url' => route('admin.settings.general')]
        ];

        return view('admin.settings.general', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình chung
     */
    public function updateGeneral(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp,avif', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:ico,png,jpg,jpeg,gif,webp,avif', 'max:1024'],
            'site_banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:5120'],
            'site_domain' => ['nullable', 'string', 'max:255'],
            'site_language' => ['nullable', 'string', 'max:10'],
            'site_timezone' => ['nullable', 'string', 'max:50'],
            'site_maintenance_mode' => ['boolean'],
            'site_maintenance_message' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('site_name', $request->site_name, 'general');
        Setting::set('site_tagline', $request->site_tagline, 'general');
        Setting::set('site_domain', $request->site_domain, 'general');
        Setting::set('site_language', $request->site_language, 'general');
        Setting::set('site_timezone', $request->site_timezone, 'general');
        Setting::set('site_maintenance_mode', $request->has('site_maintenance_mode') ? '1' : '0', 'general');
        Setting::set('site_maintenance_message', $request->site_maintenance_message, 'general');

        // Xử lý upload logo
        if ($request->hasFile('site_logo')) {
            // Xóa logo cũ nếu có
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && file_exists(public_path(ltrim($oldLogo, '/')))) {
                unlink(public_path(ltrim($oldLogo, '/')));
            }

            // Upload logo mới
            $logoFile = $request->file('site_logo');
            $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('images/settings'), $logoName);

            Setting::set('site_logo', '/images/settings/' . $logoName, 'general');
        }

        // Xử lý upload favicon
        if ($request->hasFile('site_favicon')) {
            // Xóa favicon cũ nếu có
            $oldFavicon = Setting::get('site_favicon');
            if ($oldFavicon && file_exists(public_path(ltrim($oldFavicon, '/')))) {
                unlink(public_path(ltrim($oldFavicon, '/')));
            }

            // Upload favicon mới
            $faviconFile = $request->file('site_favicon');
            $faviconName = time() . '_' . uniqid() . '.' . $faviconFile->getClientOriginalExtension();
            $faviconFile->move(public_path('images/settings'), $faviconName);

            Setting::set('site_favicon', '/images/settings/' . $faviconName, 'general');
        }

        // Xử lý upload banner
        if ($request->hasFile('site_banner')) {
            // Xóa banner cũ nếu có
            $oldBanner = Setting::get('site_banner');
            if ($oldBanner && file_exists(public_path(ltrim($oldBanner, '/')))) {
                unlink(public_path(ltrim($oldBanner, '/')));
            }

            // Upload banner mới
            $bannerFile = $request->file('site_banner');
            $bannerName = time() . '_' . uniqid() . '.' . $bannerFile->getClientOriginalExtension();
            $bannerFile->move(public_path('images/settings'), $bannerName);

            Setting::set('site_banner', '/images/settings/' . $bannerName, 'general');
        }

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Cấu hình chung đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình công ty
     */
    public function company(): View
    {
        // Lấy các cài đặt công ty
        $settings = Setting::getGroup('company');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Thông tin công ty', 'url' => route('admin.settings.company')]
        ];

        return view('admin.settings.company', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình công ty
     */
    public function updateCompany(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_tax_id' => ['nullable', 'string', 'max:50'],
            'company_registration_number' => ['nullable', 'string', 'max:50'],
            'company_founded_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'company_description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('company_name', $request->company_name, 'company');
        Setting::set('company_address', $request->company_address, 'company');
        Setting::set('company_phone', $request->company_phone, 'company');
        Setting::set('company_email', $request->company_email, 'company');
        Setting::set('company_tax_id', $request->company_tax_id, 'company');
        Setting::set('company_registration_number', $request->company_registration_number, 'company');
        Setting::set('company_founded_year', $request->company_founded_year, 'company');
        Setting::set('company_description', $request->company_description, 'company');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Thông tin công ty đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình liên hệ
     */
    public function contact(): View
    {
        // Lấy các cài đặt liên hệ
        $settings = Setting::getGroup('contact');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Thông tin liên hệ', 'url' => route('admin.settings.contact')]
        ];

        return view('admin.settings.contact', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình liên hệ
     */
    public function updateContact(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_working_hours' => ['nullable', 'string', 'max:255'],
            'contact_map_embed' => ['nullable', 'string'],
            'contact_latitude' => ['nullable', 'numeric'],
            'contact_longitude' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('contact_email', $request->contact_email, 'contact');
        Setting::set('contact_phone', $request->contact_phone, 'contact');
        Setting::set('contact_address', $request->contact_address, 'contact');
        Setting::set('contact_working_hours', $request->contact_working_hours, 'contact');
        Setting::set('contact_map_embed', $request->contact_map_embed, 'contact');
        Setting::set('contact_latitude', $request->contact_latitude, 'contact');
        Setting::set('contact_longitude', $request->contact_longitude, 'contact');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Thông tin liên hệ đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình mạng xã hội
     */
    public function social(): View
    {
        // Lấy các cài đặt mạng xã hội
        $settings = Setting::getGroup('social');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Mạng xã hội', 'url' => route('admin.settings.social')]
        ];

        return view('admin.settings.social', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình mạng xã hội
     */
    public function updateSocial(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_pinterest' => ['nullable', 'url', 'max:255'],
            'social_github' => ['nullable', 'url', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('social_facebook', $request->social_facebook, 'social');
        Setting::set('social_twitter', $request->social_twitter, 'social');
        Setting::set('social_instagram', $request->social_instagram, 'social');
        Setting::set('social_linkedin', $request->social_linkedin, 'social');
        Setting::set('social_youtube', $request->social_youtube, 'social');
        Setting::set('social_tiktok', $request->social_tiktok, 'social');
        Setting::set('social_pinterest', $request->social_pinterest, 'social');
        Setting::set('social_github', $request->social_github, 'social');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Liên kết mạng xã hội đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình API
     */
    public function api(): View
    {
        // Lấy các cài đặt API
        $settings = Setting::getGroup('api');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'API Keys', 'url' => route('admin.settings.api')]
        ];

        return view('admin.settings.api', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình API
     */
    public function updateApi(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'api_google_client_id' => ['nullable', 'string', 'max:255'],
            'api_google_client_secret' => ['nullable', 'string', 'max:255'],
            'api_facebook_app_id' => ['nullable', 'string', 'max:255'],
            'api_facebook_app_secret' => ['nullable', 'string', 'max:255'],
            'api_recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'api_recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('api_google_client_id', $request->api_google_client_id, 'api');
        Setting::set('api_google_client_secret', $request->api_google_client_secret, 'api');
        Setting::set('api_facebook_app_id', $request->api_facebook_app_id, 'api');
        Setting::set('api_facebook_app_secret', $request->api_facebook_app_secret, 'api');
        Setting::set('api_recaptcha_site_key', $request->api_recaptcha_site_key, 'api');
        Setting::set('api_recaptcha_secret_key', $request->api_recaptcha_secret_key, 'api');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'API Keys đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình bản quyền
     */
    public function copyright(): View
    {
        // Lấy các cài đặt bản quyền
        $settings = Setting::getGroup('copyright');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Bản quyền', 'url' => route('admin.settings.copyright')]
        ];

        return view('admin.settings.copyright', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình bản quyền
     */
    public function updateCopyright(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'copyright_text' => ['nullable', 'string', 'max:500'],
            'copyright_owner' => ['nullable', 'string', 'max:255'],
            'copyright_year' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('copyright_text', $request->copyright_text, 'copyright');
        Setting::set('copyright_owner', $request->copyright_owner, 'copyright');
        Setting::set('copyright_year', $request->copyright_year, 'copyright');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Thông tin bản quyền đã được cập nhật thành công.');
    }
}
