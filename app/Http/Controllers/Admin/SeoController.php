<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SeoController extends Controller
{
    /**
     * Hiển thị trang cấu hình SEO chung
     */
    public function index(): View
    {
        // Lấy các cài đặt SEO chung
        $settings = SeoSetting::getGroup('general');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')]
        ];

        return view('admin.seo.index', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình SEO chung
     */
    public function updateGeneral(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'site_title' => ['required', 'string', 'max:255'],
            'site_description' => ['required', 'string', 'max:500'],
            'site_keywords' => ['nullable', 'string', 'max:500'],
            'allow_indexing' => ['boolean'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'google_search_console_id' => ['nullable', 'string', 'max:100'],
            'facebook_app_id' => ['nullable', 'string', 'max:50'],
            'twitter_username' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        SeoSetting::setValue('site_title', $request->site_title, 'general');
        SeoSetting::setValue('site_description', $request->site_description, 'general');
        SeoSetting::setValue('site_keywords', $request->site_keywords, 'general');
        SeoSetting::setValue('allow_indexing', $request->has('allow_indexing') ? '1' : '0', 'general');
        SeoSetting::setValue('google_analytics_id', $request->google_analytics_id, 'general');
        SeoSetting::setValue('google_search_console_id', $request->google_search_console_id, 'general');
        SeoSetting::setValue('facebook_app_id', $request->facebook_app_id, 'general');
        SeoSetting::setValue('twitter_username', $request->twitter_username, 'general');

        return back()->with('success', 'Cấu hình SEO chung đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang quản lý robots.txt
     */
    public function robots(): View
    {
        // Đọc nội dung file robots.txt
        $robotsContent = '';
        $robotsPath = public_path('robots.txt');

        if (File::exists($robotsPath)) {
            $robotsContent = File::get($robotsPath);
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Robots.txt', 'url' => route('admin.seo.robots')]
        ];

        return view('admin.seo.robots', compact('robotsContent', 'breadcrumbs'));
    }

    /**
     * Cập nhật file robots.txt
     */
    public function updateRobots(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'robots_content' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu nội dung vào file robots.txt
        File::put(public_path('robots.txt'), $request->robots_content);

        return back()->with('success', 'File robots.txt đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang quản lý sitemap
     */
    public function sitemap(): View
    {
        // Kiểm tra các file sitemap
        $sitemapFiles = collect(File::glob(public_path('*.xml')))
            ->filter(function ($file) {
                return str_contains(basename($file), 'sitemap');
            })
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'size' => File::size($file),
                    'modified' => File::lastModified($file),
                    'url' => url(basename($file)),
                ];
            });

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Sitemap', 'url' => route('admin.seo.sitemap')]
        ];

        return view('admin.seo.sitemap', compact('sitemapFiles', 'breadcrumbs'));
    }

    /**
     * Tạo sitemap mới
     */
    public function generateSitemap()
    {
        // Tạo sitemap cho trang chủ
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Thêm trang chủ
        $sitemap .= '<url>' . PHP_EOL;
        $sitemap .= '  <loc>' . url('/') . '</loc>' . PHP_EOL;
        $sitemap .= '  <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
        $sitemap .= '  <changefreq>daily</changefreq>' . PHP_EOL;
        $sitemap .= '  <priority>1.0</priority>' . PHP_EOL;
        $sitemap .= '</url>' . PHP_EOL;

        // Thêm các trang khác
        $pages = [
            '/login' => 0.8,
            '/register' => 0.8,
            '/new' => 0.9,
            '/whats-new' => 0.9,
            '/public-showcase' => 0.9,
        ];

        foreach ($pages as $page => $priority) {
            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '  <loc>' . url($page) . '</loc>' . PHP_EOL;
            $sitemap .= '  <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '  <changefreq>weekly</changefreq>' . PHP_EOL;
            $sitemap .= '  <priority>' . $priority . '</priority>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        // Thêm các forum
        $forums = \App\Models\Forum::all();
        foreach ($forums as $forum) {
            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '  <loc>' . route('forums.show', $forum) . '</loc>' . PHP_EOL;
            $sitemap .= '  <lastmod>' . $forum->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '  <changefreq>daily</changefreq>' . PHP_EOL;
            $sitemap .= '  <priority>0.8</priority>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        // Thêm các thread
        $threads = \App\Models\Thread::with('forum')->latest()->take(1000)->get();
        foreach ($threads as $thread) {
            if (!$thread->forum) continue;

            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '  <loc>' . route('forums.show', $thread->forum) . '#thread-' . $thread->id . '</loc>' . PHP_EOL;
            $sitemap .= '  <lastmod>' . $thread->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '  <changefreq>weekly</changefreq>' . PHP_EOL;
            $sitemap .= '  <priority>0.7</priority>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        // Thêm các profile người dùng
        $users = \App\Models\User::latest()->take(500)->get();
        foreach ($users as $user) {
            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '  <loc>' . route('profile.show', $user->username) . '</loc>' . PHP_EOL;
            $sitemap .= '  <lastmod>' . $user->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '  <changefreq>weekly</changefreq>' . PHP_EOL;
            $sitemap .= '  <priority>0.6</priority>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        $sitemap .= '</urlset>';

        // Lưu sitemap
        File::put(public_path('sitemap.xml'), $sitemap);

        return back()->with('success', 'Sitemap đã được tạo thành công.');
    }

    /**
     * Xóa sitemap
     */
    public function deleteSitemap(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'filename' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $filename = $request->filename;
        $filePath = public_path($filename);

        // Kiểm tra xem file có tồn tại không và có phải là sitemap không
        if (File::exists($filePath) && str_contains($filename, 'sitemap')) {
            File::delete($filePath);
            return back()->with('success', 'File ' . $filename . ' đã được xóa thành công.');
        }

        return back()->with('error', 'Không thể xóa file ' . $filename . '.');
    }

    /**
     * Hiển thị trang cấu hình Social Media
     */
    public function social(): View
    {
        // Lấy các cài đặt social media
        $settings = SeoSetting::getGroup('social');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Social Media', 'url' => route('admin.seo.social')]
        ];

        return view('admin.seo.social', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình Social Media
     */
    public function updateSocial(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'twitter_card' => ['nullable', 'string', 'in:summary,summary_large_image,app,player'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        SeoSetting::setValue('og_title', $request->og_title, 'social');
        SeoSetting::setValue('og_description', $request->og_description, 'social');
        SeoSetting::setValue('twitter_card', $request->twitter_card, 'social');
        SeoSetting::setValue('twitter_title', $request->twitter_title, 'social');
        SeoSetting::setValue('twitter_description', $request->twitter_description, 'social');

        // Xử lý upload og_image
        if ($request->hasFile('og_image')) {
            $ogImagePath = $request->file('og_image')->store('seo', 'public');
            SeoSetting::setValue('og_image', '/storage/' . $ogImagePath, 'social');
        }

        // Xử lý upload twitter_image
        if ($request->hasFile('twitter_image')) {
            $twitterImagePath = $request->file('twitter_image')->store('seo', 'public');
            SeoSetting::setValue('twitter_image', '/storage/' . $twitterImagePath, 'social');
        }

        return back()->with('success', 'Cấu hình Social Media đã được cập nhật thành công.');
    }

    /**
     * Hiển thị trang cấu hình nâng cao
     */
    public function advanced(): View
    {
        // Lấy các cài đặt nâng cao
        $settings = SeoSetting::getGroup('advanced');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Cấu hình nâng cao', 'url' => route('admin.seo.advanced')]
        ];

        return view('admin.seo.advanced', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình nâng cao
     */
    public function updateAdvanced(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'header_scripts' => ['nullable', 'string'],
            'footer_scripts' => ['nullable', 'string'],
            'custom_css' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'string', 'url'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lưu các cài đặt
        SeoSetting::setValue('header_scripts', $request->header_scripts, 'advanced');
        SeoSetting::setValue('footer_scripts', $request->footer_scripts, 'advanced');
        SeoSetting::setValue('custom_css', $request->custom_css, 'advanced');
        SeoSetting::setValue('canonical_url', $request->canonical_url, 'advanced');

        return back()->with('success', 'Cấu hình nâng cao đã được cập nhật thành công.');
    }
}
