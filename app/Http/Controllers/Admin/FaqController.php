<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Hiển thị danh sách câu hỏi thường gặp
     */
    public function index(Request $request): View
    {
        // Lấy các tham số lọc
        $category_id = $request->input('category_id');
        $search = $request->input('search');
        $status = $request->input('status');
        
        // Khởi tạo query
        $query = Faq::with('category');
        
        // Áp dụng các bộ lọc
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }
        
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        
        // Sắp xếp và phân trang
        $faqs = $query->orderBy('category_id')
            ->orderBy('order')
            ->paginate(20);
        
        // Lấy danh sách danh mục cho bộ lọc
        $categories = FaqCategory::all();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý FAQ', 'url' => route('admin.faqs.index')]
        ];
        
        return view('admin.faqs.index', compact('faqs', 'categories', 'breadcrumbs', 'category_id', 'search', 'status'));
    }
    
    /**
     * Hiển thị form tạo câu hỏi mới
     */
    public function create(): View
    {
        $categories = FaqCategory::all();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý FAQ', 'url' => route('admin.faqs.index')],
            ['title' => 'Tạo câu hỏi mới', 'url' => route('admin.faqs.create')]
        ];
        
        return view('admin.faqs.create', compact('categories', 'breadcrumbs'));
    }
    
    /**
     * Lưu câu hỏi mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category_id' => 'required|exists:faq_categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->category_id = $request->category_id;
        $faq->order = $request->order ?? 0;
        $faq->is_active = $request->has('is_active');
        $faq->save();
        
        return redirect()->route('admin.faqs.index')
            ->with('success', 'Câu hỏi đã được tạo thành công.');
    }
    
    /**
     * Hiển thị form chỉnh sửa câu hỏi
     */
    public function edit(Faq $faq): View
    {
        $categories = FaqCategory::all();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý FAQ', 'url' => route('admin.faqs.index')],
            ['title' => 'Chỉnh sửa câu hỏi', 'url' => route('admin.faqs.edit', $faq)]
        ];
        
        return view('admin.faqs.edit', compact('faq', 'categories', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật câu hỏi
     */
    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category_id' => 'required|exists:faq_categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->category_id = $request->category_id;
        $faq->order = $request->order ?? 0;
        $faq->is_active = $request->has('is_active');
        $faq->save();
        
        return redirect()->route('admin.faqs.index')
            ->with('success', 'Câu hỏi đã được cập nhật thành công.');
    }
    
    /**
     * Xóa câu hỏi
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        
        return redirect()->route('admin.faqs.index')
            ->with('success', 'Câu hỏi đã được xóa thành công.');
    }
    
    /**
     * Thay đổi trạng thái câu hỏi
     */
    public function toggleStatus(Faq $faq)
    {
        $faq->is_active = !$faq->is_active;
        $faq->save();
        
        $status = $faq->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.faqs.index')
            ->with('success', "Câu hỏi đã được {$status} thành công.");
    }
}
