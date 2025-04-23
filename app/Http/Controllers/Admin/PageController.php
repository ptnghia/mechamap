<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index(Request $request): View
    {
        // Lấy các tham số lọc
        $status = $request->input('status');
        $category_id = $request->input('category_id');
        $search = $request->input('search');
        
        // Khởi tạo query
        $query = Page::with(['user', 'category']);
        
        // Áp dụng các bộ lọc
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Sắp xếp và phân trang
        $pages = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Lấy danh sách danh mục cho bộ lọc
        $categories = PageCategory::all();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')]
        ];
        
        return view('admin.pages.index', compact('pages', 'categories', 'breadcrumbs', 'status', 'category_id', 'search'));
    }
    
    /**
     * Hiển thị form tạo bài viết mới
     */
    public function create(): View
    {
        $categories = PageCategory::all();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')],
            ['title' => 'Tạo bài viết mới', 'url' => route('admin.pages.create')]
        ];
        
        return view('admin.pages.create', compact('categories', 'breadcrumbs'));
    }
    
    /**
     * Lưu bài viết mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:page_categories,id',
            'status' => 'required|in:draft,published',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $page = new Page();
        $page->title = $request->title;
        $page->slug = Str::slug($request->title);
        $page->content = $request->content;
        $page->excerpt = $request->excerpt;
        $page->category_id = $request->category_id;
        $page->user_id = Auth::id();
        $page->status = $request->status;
        $page->order = $request->order ?? 0;
        $page->is_featured = $request->has('is_featured');
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->save();
        
        // Xử lý featured image nếu có
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pages', $fileName, 'public');
            
            $media = new Media();
            $media->user_id = Auth::id();
            $media->file_name = $fileName;
            $media->file_path = $filePath;
            $media->file_type = $file->getMimeType();
            $media->file_size = $file->getSize();
            $media->title = $page->title;
            
            $page->attachments()->save($media);
        }
        
        return redirect()->route('admin.pages.index')
            ->with('success', 'Bài viết đã được tạo thành công.');
    }
    
    /**
     * Hiển thị chi tiết bài viết
     */
    public function show(Page $page): View
    {
        $page->load(['user', 'category', 'attachments']);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')],
            ['title' => $page->title, 'url' => route('admin.pages.show', $page)]
        ];
        
        return view('admin.pages.show', compact('page', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form chỉnh sửa bài viết
     */
    public function edit(Page $page): View
    {
        $categories = PageCategory::all();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài viết', 'url' => route('admin.pages.index')],
            ['title' => 'Chỉnh sửa bài viết', 'url' => route('admin.pages.edit', $page)]
        ];
        
        return view('admin.pages.edit', compact('page', 'categories', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:page_categories,id',
            'status' => 'required|in:draft,published',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $page->title = $request->title;
        // Chỉ cập nhật slug nếu tiêu đề thay đổi
        if ($page->title != $request->title) {
            $page->slug = Str::slug($request->title);
        }
        $page->content = $request->content;
        $page->excerpt = $request->excerpt;
        $page->category_id = $request->category_id;
        $page->status = $request->status;
        $page->order = $request->order ?? 0;
        $page->is_featured = $request->has('is_featured');
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->save();
        
        // Xử lý featured image nếu có
        if ($request->hasFile('featured_image')) {
            // Xóa featured image cũ nếu có
            $oldMedia = $page->attachments()->first();
            if ($oldMedia) {
                Storage::disk('public')->delete($oldMedia->file_path);
                $oldMedia->delete();
            }
            
            $file = $request->file('featured_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pages', $fileName, 'public');
            
            $media = new Media();
            $media->user_id = Auth::id();
            $media->file_name = $fileName;
            $media->file_path = $filePath;
            $media->file_type = $file->getMimeType();
            $media->file_size = $file->getSize();
            $media->title = $page->title;
            
            $page->attachments()->save($media);
        }
        
        return redirect()->route('admin.pages.index')
            ->with('success', 'Bài viết đã được cập nhật thành công.');
    }
    
    /**
     * Xóa bài viết
     */
    public function destroy(Page $page)
    {
        // Xóa các file media liên quan
        foreach ($page->attachments as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }
        
        // Xóa bài viết
        $page->delete();
        
        return redirect()->route('admin.pages.index')
            ->with('success', 'Bài viết đã được xóa thành công.');
    }
}
