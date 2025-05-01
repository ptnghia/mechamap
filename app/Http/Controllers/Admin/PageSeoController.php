<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PageSeoController extends Controller
{
    /**
     * Hiển thị danh sách cấu hình SEO cho các trang
     */
    public function index(): View
    {
        // Lấy danh sách cấu hình SEO cho các trang
        $pages = PageSeo::orderBy('route_name')->paginate(20);

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Trang', 'url' => route('admin.page-seo.index')]
        ];

        return view('admin.page-seo.index', compact('pages', 'breadcrumbs'));
    }

    /**
     * Hiển thị form tạo cấu hình SEO mới
     */
    public function create(): View
    {
        // Lấy danh sách route
        $routes = $this->getRoutesList();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Trang', 'url' => route('admin.page-seo.index')],
            ['title' => 'Thêm mới', 'url' => route('admin.page-seo.create')]
        ];

        return view('admin.page-seo.create', compact('routes', 'breadcrumbs'));
    }

    /**
     * Lưu cấu hình SEO mới
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'route_name' => ['nullable', 'string', 'max:255'],
            'url_pattern' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'extra_meta' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Kiểm tra xem route_name hoặc url_pattern có được cung cấp không
        if (empty($request->route_name) && empty($request->url_pattern)) {
            return back()->withErrors(['route_name' => 'Bạn phải cung cấp Route name hoặc URL pattern.'])->withInput();
        }

        // Tạo cấu hình SEO mới
        $pageSeo = new PageSeo();
        $pageSeo->route_name = $request->route_name;
        $pageSeo->url_pattern = $request->url_pattern;
        $pageSeo->title = $request->title;
        $pageSeo->description = $request->description;
        $pageSeo->keywords = $request->keywords;
        $pageSeo->og_title = $request->og_title;
        $pageSeo->og_description = $request->og_description;
        $pageSeo->twitter_title = $request->twitter_title;
        $pageSeo->twitter_description = $request->twitter_description;
        $pageSeo->canonical_url = $request->canonical_url;
        $pageSeo->extra_meta = $request->extra_meta;
        $pageSeo->no_index = $request->has('no_index');
        $pageSeo->is_active = $request->has('is_active');

        // Xử lý upload og_image
        if ($request->hasFile('og_image')) {
            $ogImagePath = $request->file('og_image')->store('seo/pages', 'public');
            $pageSeo->og_image = '/storage/' . $ogImagePath;
        }

        // Xử lý upload twitter_image
        if ($request->hasFile('twitter_image')) {
            $twitterImagePath = $request->file('twitter_image')->store('seo/pages', 'public');
            $pageSeo->twitter_image = '/storage/' . $twitterImagePath;
        }

        $pageSeo->save();

        return redirect()->route('admin.page-seo.index')
            ->with('success', 'Cấu hình SEO cho trang đã được tạo thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa cấu hình SEO
     */
    public function edit(PageSeo $pageSeo): View
    {
        // Lấy danh sách route
        $routes = $this->getRoutesList();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình SEO', 'url' => route('admin.seo.index')],
            ['title' => 'Trang', 'url' => route('admin.page-seo.index')],
            ['title' => 'Chỉnh sửa', 'url' => route('admin.page-seo.edit', $pageSeo)]
        ];

        return view('admin.page-seo.edit', compact('pageSeo', 'routes', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình SEO
     */
    public function update(Request $request, PageSeo $pageSeo)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'route_name' => ['nullable', 'string', 'max:255'],
            'url_pattern' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'extra_meta' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Kiểm tra xem route_name hoặc url_pattern có được cung cấp không
        if (empty($request->route_name) && empty($request->url_pattern)) {
            return back()->withErrors(['route_name' => 'Bạn phải cung cấp Route name hoặc URL pattern.'])->withInput();
        }

        // Cập nhật cấu hình SEO
        $pageSeo->route_name = $request->route_name;
        $pageSeo->url_pattern = $request->url_pattern;
        $pageSeo->title = $request->title;
        $pageSeo->description = $request->description;
        $pageSeo->keywords = $request->keywords;
        $pageSeo->og_title = $request->og_title;
        $pageSeo->og_description = $request->og_description;
        $pageSeo->twitter_title = $request->twitter_title;
        $pageSeo->twitter_description = $request->twitter_description;
        $pageSeo->canonical_url = $request->canonical_url;
        $pageSeo->extra_meta = $request->extra_meta;
        $pageSeo->no_index = $request->has('no_index');
        $pageSeo->is_active = $request->has('is_active');

        // Xử lý upload og_image
        if ($request->hasFile('og_image')) {
            // Xóa ảnh cũ nếu có
            if ($pageSeo->og_image && Storage::disk('public')->exists(str_replace('/storage/', '', $pageSeo->og_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $pageSeo->og_image));
            }

            // Upload ảnh mới
            $ogImagePath = $request->file('og_image')->store('seo/pages', 'public');
            $pageSeo->og_image = '/storage/' . $ogImagePath;
        }

        // Xử lý upload twitter_image
        if ($request->hasFile('twitter_image')) {
            // Xóa ảnh cũ nếu có
            if ($pageSeo->twitter_image && Storage::disk('public')->exists(str_replace('/storage/', '', $pageSeo->twitter_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $pageSeo->twitter_image));
            }

            // Upload ảnh mới
            $twitterImagePath = $request->file('twitter_image')->store('seo/pages', 'public');
            $pageSeo->twitter_image = '/storage/' . $twitterImagePath;
        }

        $pageSeo->save();

        return redirect()->route('admin.page-seo.index')
            ->with('success', 'Cấu hình SEO cho trang đã được cập nhật thành công.');
    }

    /**
     * Xóa cấu hình SEO
     */
    public function destroy(PageSeo $pageSeo)
    {
        // Xóa ảnh nếu có
        if ($pageSeo->og_image && Storage::disk('public')->exists(str_replace('/storage/', '', $pageSeo->og_image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $pageSeo->og_image));
        }

        if ($pageSeo->twitter_image && Storage::disk('public')->exists(str_replace('/storage/', '', $pageSeo->twitter_image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $pageSeo->twitter_image));
        }

        // Xóa cấu hình SEO
        $pageSeo->delete();

        return redirect()->route('admin.page-seo.index')
            ->with('success', 'Cấu hình SEO cho trang đã được xóa thành công.');
    }

    /**
     * Lấy danh sách route
     */
    private function getRoutesList(): array
    {
        $routes = [];
        $excludedPrefixes = ['admin', 'api', 'debugbar', 'sanctum', '_ignition'];

        foreach (Route::getRoutes() as $route) {
            // Chỉ lấy các route có tên và phương thức GET
            if ($route->getName() && in_array('GET', $route->methods())) {
                // Loại bỏ các route admin và api
                $excluded = false;
                foreach ($excludedPrefixes as $prefix) {
                    if (str_starts_with($route->getName(), $prefix . '.')) {
                        $excluded = true;
                        break;
                    }
                }

                if (!$excluded) {
                    $routes[$route->getName()] = [
                        'name' => $route->getName(),
                        'uri' => $route->uri(),
                    ];
                }
            }
        }

        // Sắp xếp theo tên route
        ksort($routes);

        return $routes;
    }
}
