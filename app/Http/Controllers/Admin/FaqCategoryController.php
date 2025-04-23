<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FaqCategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục FAQ
     */
    public function index(): View
    {
        $categories = FaqCategory::withCount('faqs')
            ->orderBy('order')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý FAQ', 'url' => route('admin.faqs.index')],
            ['title' => 'Danh mục FAQ', 'url' => route('admin.faq-categories.index')]
        ];
        
        return view('admin.faq-categories.index', compact('categories', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form tạo danh mục mới
     */
    public function create(): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý FAQ', 'url' => route('admin.faqs.index')],
            ['title' => 'Danh mục FAQ', 'url' => route('admin.faq-categories.index')],
            ['title' => 'Tạo danh mục mới', 'url' => route('admin.faq-categories.create')]
        ];
        
        return view('admin.faq-categories.create', compact('breadcrumbs'));
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
            'is_active' => 'boolean',
        ]);
        
        $category = new FaqCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->order = $request->order ?? 0;
        $category->is_active = $request->has('is_active');
        $category->save();
        
        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'Danh mục đã được tạo thành công.');
    }
    
    /**
     * Hiển thị form chỉnh sửa danh mục
     */
    public function edit(FaqCategory $faqCategory): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý FAQ', 'url' => route('admin.faqs.index')],
            ['title' => 'Danh mục FAQ', 'url' => route('admin.faq-categories.index')],
            ['title' => 'Chỉnh sửa danh mục', 'url' => route('admin.faq-categories.edit', $faqCategory)]
        ];
        
        return view('admin.faq-categories.edit', compact('faqCategory', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, FaqCategory $faqCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $faqCategory->name = $request->name;
        // Chỉ cập nhật slug nếu tên thay đổi
        if ($faqCategory->name != $request->name) {
            $faqCategory->slug = Str::slug($request->name);
        }
        $faqCategory->description = $request->description;
        $faqCategory->order = $request->order ?? 0;
        $faqCategory->is_active = $request->has('is_active');
        $faqCategory->save();
        
        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'Danh mục đã được cập nhật thành công.');
    }
    
    /**
     * Xóa danh mục
     */
    public function destroy(FaqCategory $faqCategory)
    {
        // Kiểm tra xem có câu hỏi không
        if ($faqCategory->faqs()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa danh mục có câu hỏi.']);
        }
        
        $faqCategory->delete();
        
        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'Danh mục đã được xóa thành công.');
    }
    
    /**
     * Thay đổi trạng thái danh mục
     */
    public function toggleStatus(FaqCategory $faqCategory)
    {
        $faqCategory->is_active = !$faqCategory->is_active;
        $faqCategory->save();
        
        $status = $faqCategory->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.faq-categories.index')
            ->with('success', "Danh mục đã được {$status} thành công.");
    }
    
    /**
     * Sắp xếp lại thứ tự danh mục
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:faq_categories,id',
            'categories.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->categories as $categoryData) {
            $category = FaqCategory::find($categoryData['id']);
            $category->order = $categoryData['order'];
            $category->save();
        }
        
        return response()->json(['success' => true]);
    }
}
