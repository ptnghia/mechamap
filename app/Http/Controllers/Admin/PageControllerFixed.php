<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Hiển thị danh sách pages
     */
    public function index(Request $request)
    {
        $query = Page::with(['category', 'user']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $pages = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = PageCategory::all();

        return view('admin.pages.index', compact('pages', 'categories'));
    }

    /**
     * Tạo page mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:page_categories,id',
        ]);

        try {
            $page = Page::create([
                'title' => $request->title,
                'slug' => \Str::slug($request->title),
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'category_id' => $request->category_id,
                'user_id' => Auth::id(),
                'status' => $request->status ?? 'draft',
                'order' => $request->order ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
            ]);

            // Handle featured image
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pages', $fileName, 'public');

                $media = new Media();
                $media->user_id = Auth::id();
                $media->file_name = $fileName;
                $media->file_path = $filePath;
                $media->mime_type = $file->getMimeType();
                $media->file_size = $file->getSize();
                $media->save();

                $page->attachments()->save($media);
            }

            return redirect()->route('admin.pages.index')
                ->with('success', 'Tạo page thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết page
     */
    public function show(Page $page)
    {
        $page->load(['category', 'user', 'attachments']);
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Cập nhật page
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:page_categories,id',
        ]);

        try {
            $page->update([
                'title' => $request->title,
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'order' => $request->order ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
            ]);

            return redirect()->route('admin.pages.index')
                ->with('success', 'Cập nhật page thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa page
     */
    public function destroy(Page $page)
    {
        try {
            // Delete attachments
            foreach ($page->attachments as $media) {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->delete();
            }

            $page->delete();

            return redirect()->route('admin.pages.index')
                ->with('success', 'Xóa page thành công');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
