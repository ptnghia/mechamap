<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageCategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục bài viết
     */
    public function index(): View
    {
        $categories = PageCategory::withCount('pages')
            ->orderBy('order')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')],
            ['title' => 'Danh mục bài viết', 'url' => route('admin.page-categories.index')]
        ];
        
        return view('admin.page-categories.index', compact('categories', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form tạo danh mục mới
     */
    public function create(): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')],
            ['title' => 'Danh mục bài viết', 'url' => route('admin.page-categories.index')],
            ['title' => 'Tạo danh mục mới', 'url' => route('admin.page-categories.create')]
        ];
        
        return view('admin.page-categories.create', compact('breadcrumbs'));
    }
    
    /**
     * Lưu danh mục mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $category = new PageCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->order = $request->order ?? 0;
        $category->save();
        
        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Danh mục đã được tạo thành công.');
    }
    
    /**
     * Hiển thị form chỉnh sửa danh mục
     */
    public function edit(PageCategory $pageCategory): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')],
            ['title' => 'Danh mục bài viết', 'url' => route('admin.page-categories.index')],
            ['title' => 'Chỉnh sửa danh mục', 'url' => route('admin.page-categories.edit', $pageCategory)]
        ];
        
        return view('admin.page-categories.edit', compact('pageCategory', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, PageCategory $pageCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $pageCategory->name = $request->name;
        // Chỉ cập nhật slug nếu tên thay đổi
        if ($pageCategory->name != $request->name) {
            $pageCategory->slug = Str::slug($request->name);
        }
        $pageCategory->description = $request->description;
        $pageCategory->order = $request->order ?? 0;
        $pageCategory->save();
        
        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Danh mục đã được cập nhật thành công.');
    }
    
    /**
     * Xóa danh mục
     */
    public function destroy(PageCategory $pageCategory)
    {
        // Kiểm tra xem có bài viết không
        if ($pageCategory->pages()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa danh mục có bài viết.']);
        }
        
        $pageCategory->delete();
        
        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Danh mục đã được xóa thành công.');
    }
    
    /**
     * Sắp xếp lại thứ tự danh mục
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:page_categories,id',
            'categories.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->categories as $categoryData) {
            $category = PageCategory::find($categoryData['id']);
            $category->order = $categoryData['order'];
            $category->save();
        }
        
        return response()->json(['success' => true]);
    }
}
