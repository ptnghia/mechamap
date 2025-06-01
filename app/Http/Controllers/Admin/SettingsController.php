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

    /**
     * Hiển thị trang cấu hình diễn đàn
     */
    public function forum(): View
    {
        // Lấy các cài đặt diễn đàn
        $settings = Setting::getGroup('forum');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Diễn đàn', 'url' => route('admin.settings.forum')]
        ];

        return view('admin.settings.forum', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình diễn đàn
     */
    public function updateForum(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'forum_threads_per_page' => ['required', 'integer', 'min:5', 'max:100'],
            'forum_posts_per_page' => ['required', 'integer', 'min:5', 'max:100'],
            'forum_hot_threshold' => ['nullable', 'integer', 'min:1'],
            'forum_allow_guest_view' => ['boolean'],
            'forum_require_email_verification' => ['boolean'],
            'forum_enable_polls' => ['boolean'],
            'forum_max_poll_options' => ['nullable', 'integer', 'min:2', 'max:20'],
            'forum_max_attachments' => ['nullable', 'integer', 'min:0', 'max:10'],
            'forum_allowed_file_types' => ['nullable', 'string'],
            'forum_max_file_size' => ['nullable', 'integer', 'min:1', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('forum_threads_per_page', $request->forum_threads_per_page, 'forum');
        Setting::set('forum_posts_per_page', $request->forum_posts_per_page, 'forum');
        Setting::set('forum_hot_threshold', $request->forum_hot_threshold, 'forum');
        Setting::set('forum_allow_guest_view', $request->has('forum_allow_guest_view') ? '1' : '0', 'forum');
        Setting::set('forum_require_email_verification', $request->has('forum_require_email_verification') ? '1' : '0', 'forum');
        Setting::set('forum_enable_polls', $request->has('forum_enable_polls') ? '1' : '0', 'forum');
        Setting::set('forum_max_poll_options', $request->forum_max_poll_options, 'forum');
        Setting::set('forum_max_attachments', $request->forum_max_attachments, 'forum');
        Setting::set('forum_allowed_file_types', $request->forum_allowed_file_types, 'forum');
        Setting::set('forum_max_file_size', $request->forum_max_file_size, 'forum');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Cấu hình diễn đàn đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình người dùng
     */
    public function user(): View
    {
        // Lấy các cài đặt người dùng
        $settings = Setting::getGroup('user');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Người dùng', 'url' => route('admin.settings.user')]
        ];

        return view('admin.settings.user', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình người dùng
     */
    public function updateUser(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'user_allow_registration' => ['boolean'],
            'user_require_email_verification' => ['boolean'],
            'user_allow_social_login' => ['boolean'],
            'user_default_role' => ['required', 'string', 'in:member,guest'],
            'user_min_password_length' => ['required', 'integer', 'min:6', 'max:30'],
            'user_min_username_length' => ['required', 'integer', 'min:3', 'max:20'],
            'user_avatar_max_size' => ['required', 'integer', 'min:100', 'max:5120'],
            'user_signature_max_length' => ['required', 'integer', 'min:0', 'max:1000'],
            'user_about_max_length' => ['required', 'integer', 'min:0', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('user_allow_registration', $request->has('user_allow_registration') ? '1' : '0', 'user');
        Setting::set('user_require_email_verification', $request->has('user_require_email_verification') ? '1' : '0', 'user');
        Setting::set('user_allow_social_login', $request->has('user_allow_social_login') ? '1' : '0', 'user');
        Setting::set('user_default_role', $request->user_default_role, 'user');
        Setting::set('user_min_password_length', $request->user_min_password_length, 'user');
        Setting::set('user_min_username_length', $request->user_min_username_length, 'user');
        Setting::set('user_avatar_max_size', $request->user_avatar_max_size, 'user');
        Setting::set('user_signature_max_length', $request->user_signature_max_length, 'user');
        Setting::set('user_about_max_length', $request->user_about_max_length, 'user');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Cấu hình người dùng đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình email
     */
    public function email(): View
    {
        // Lấy các cài đặt email
        $settings = Setting::getGroup('email');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Email', 'url' => route('admin.settings.email')]
        ];

        return view('admin.settings.email', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình email
     */
    public function updateEmail(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'email_from_address' => ['required', 'email', 'max:255'],
            'email_from_name' => ['required', 'string', 'max:255'],
            'email_reply_to' => ['nullable', 'email', 'max:255'],
            'email_smtp_host' => ['nullable', 'string', 'max:255'],
            'email_smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'email_smtp_username' => ['nullable', 'string', 'max:255'],
            'email_smtp_password' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('email_from_address', $request->email_from_address, 'email');
        Setting::set('email_from_name', $request->email_from_name, 'email');
        Setting::set('email_reply_to', $request->email_reply_to, 'email');
        Setting::set('email_smtp_host', $request->email_smtp_host, 'email');
        Setting::set('email_smtp_port', $request->email_smtp_port, 'email');
        Setting::set('email_smtp_username', $request->email_smtp_username, 'email');
        Setting::set('email_smtp_password', $request->email_smtp_password, 'email');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Cấu hình email đã được cập nhật thành công.');
    }

    /**
     * Kiểm tra kết nối email SMTP
     */
    public function testEmailConnection(Request $request)
    {
        try {
            // Validate dữ liệu cơ bản
            $validator = Validator::make($request->all(), [
                'smtp_host' => ['required', 'string'],
                'smtp_port' => ['required', 'integer'],
                'smtp_username' => ['required', 'string'],
                'smtp_password' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng điền đầy đủ thông tin SMTP.'
                ]);
            }

            // Cấu hình tạm thời để test
            $config = [
                'transport' => 'smtp',
                'host' => $request->smtp_host,
                'port' => (int) $request->smtp_port,
                'encryption' => $request->smtp_port == 465 ? 'ssl' : 'tls',
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'timeout' => 10,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ];

            // Tạo transport để test connection
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $config['host'],
                $config['port'],
                $config['encryption'] === 'ssl'
            );

            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);

            // Test connection
            $transport->start();
            $transport->stop();

            return response()->json([
                'success' => true,
                'message' => 'Kết nối SMTP thành công! Cấu hình email đang hoạt động bình thường.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối đến máy chủ SMTP: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hiển thị trang cấu hình bảo mật
     */
    public function security(): View
    {
        // Lấy các cài đặt bảo mật
        $settings = Setting::getGroup('security');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Bảo mật', 'url' => route('admin.settings.security')]
        ];

        return view('admin.settings.security', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình bảo mật
     */
    public function updateSecurity(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'two_factor_auth_enabled' => ['boolean'],
            'session_timeout' => ['required', 'integer', 'min:1', 'max:1440'], // 1 phút đến 24 giờ
            'max_login_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'login_lockout_duration' => ['required', 'integer', 'min:1', 'max:1440'], // 1 phút đến 24 giờ
            'password_min_length' => ['required', 'integer', 'min:6', 'max:50'],
            'password_require_uppercase' => ['boolean'],
            'password_require_lowercase' => ['boolean'],
            'password_require_numbers' => ['boolean'],
            'password_require_symbols' => ['boolean'],
            'password_expiry_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'admin_ip_whitelist' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('security_two_factor_auth_enabled', $request->boolean('two_factor_auth_enabled'), 'security');
        Setting::set('security_session_timeout', $request->session_timeout, 'security');
        Setting::set('security_max_login_attempts', $request->max_login_attempts, 'security');
        Setting::set('security_login_lockout_duration', $request->login_lockout_duration, 'security');
        Setting::set('security_password_min_length', $request->password_min_length, 'security');
        Setting::set('security_password_require_uppercase', $request->boolean('password_require_uppercase'), 'security');
        Setting::set('security_password_require_lowercase', $request->boolean('password_require_lowercase'), 'security');
        Setting::set('security_password_require_numbers', $request->boolean('password_require_numbers'), 'security');
        Setting::set('security_password_require_symbols', $request->boolean('password_require_symbols'), 'security');
        Setting::set('security_password_expiry_days', $request->password_expiry_days, 'security');
        Setting::set('security_admin_ip_whitelist', $request->admin_ip_whitelist, 'security');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Cấu hình bảo mật đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình wiki
     */
    public function wiki(): View
    {
        // Lấy các cài đặt wiki
        $settings = Setting::getGroup('wiki');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cài đặt', 'url' => route('admin.settings.general')],
            ['title' => 'Wiki', 'url' => route('admin.settings.wiki')]
        ];

        return view('admin.settings.wiki', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình wiki
     */
    public function updateWiki(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'wiki_enabled' => ['boolean'],
            'wiki_public_read' => ['boolean'],
            'wiki_public_edit' => ['boolean'],
            'wiki_require_approval' => ['boolean'],
            'wiki_versioning_enabled' => ['boolean'],
            'wiki_max_revisions' => ['required', 'integer', 'min:1', 'max:100'],
            'wiki_allow_file_uploads' => ['boolean'],
            'wiki_max_file_size' => ['required', 'integer', 'min:1', 'max:102400'], // Tối đa 100MB
            'wiki_allowed_file_types' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        Setting::set('wiki_enabled', $request->boolean('wiki_enabled'), 'wiki');
        Setting::set('wiki_public_read', $request->boolean('wiki_public_read'), 'wiki');
        Setting::set('wiki_public_edit', $request->boolean('wiki_public_edit'), 'wiki');
        Setting::set('wiki_require_approval', $request->boolean('wiki_require_approval'), 'wiki');
        Setting::set('wiki_versioning_enabled', $request->boolean('wiki_versioning_enabled'), 'wiki');
        Setting::set('wiki_max_revisions', $request->wiki_max_revisions, 'wiki');
        Setting::set('wiki_allow_file_uploads', $request->boolean('wiki_allow_file_uploads'), 'wiki');
        Setting::set('wiki_max_file_size', $request->wiki_max_file_size, 'wiki');
        Setting::set('wiki_allowed_file_types', $request->wiki_allowed_file_types, 'wiki');

        // Xóa cache
        Setting::clearCache();

        return back()->with('success', 'Cấu hình wiki đã được cập nhật thành công.');
    }
}
