<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách chuyên mục
     */
    public function index(): View
    {
        $categories = Category::withCount('threads')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();
        
        // Tổ chức chuyên mục thành cấu trúc cây
        $rootCategories = $categories->whereNull('parent_id');
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý chuyên mục', 'url' => route('admin.categories.index')]
        ];
        
        return view('admin.categories.index', compact('categories', 'rootCategories', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form tạo chuyên mục mới
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý chuyên mục', 'url' => route('admin.categories.index')],
            ['title' => 'Tạo chuyên mục mới', 'url' => route('admin.categories.create')]
        ];
        
        return view('admin.categories.create', compact('categories', 'breadcrumbs'));
    }
    
    /**
     * Lưu chuyên mục mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->parent_id = $request->parent_id;
        $category->order = $request->order ?? 0;
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Chuyên mục đã được tạo thành công.');
    }
    
    /**
     * Hiển thị chi tiết chuyên mục
     */
    public function show(Category $category): View
    {
        $category->load('parent', 'children');
        $threads = $category->threads()->with('user')->latest()->paginate(10);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý chuyên mục', 'url' => route('admin.categories.index')],
            ['title' => $category->name, 'url' => route('admin.categories.show', $category)]
        ];
        
        return view('admin.categories.show', compact('category', 'threads', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form chỉnh sửa chuyên mục
     */
    public function edit(Category $category): View
    {
        $categories = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý chuyên mục', 'url' => route('admin.categories.index')],
            ['title' => 'Chỉnh sửa chuyên mục', 'url' => route('admin.categories.edit', $category)]
        ];
        
        return view('admin.categories.edit', compact('category', 'categories', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật chuyên mục
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0',
        ]);
        
        // Kiểm tra xem parent_id có phải là con của category hiện tại không
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            $currentId = $category->id;
            
            while ($parent) {
                if ($parent->id == $currentId) {
                    return back()->withErrors(['parent_id' => 'Không thể chọn chuyên mục con làm chuyên mục cha.'])->withInput();
                }
                $parent = $parent->parent;
            }
        }
        
        $category->name = $request->name;
        // Chỉ cập nhật slug nếu tên thay đổi
        if ($category->name != $request->name) {
            $category->slug = Str::slug($request->name);
        }
        $category->description = $request->description;
        $category->parent_id = $request->parent_id;
        $category->order = $request->order ?? 0;
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Chuyên mục đã được cập nhật thành công.');
    }
    
    /**
     * Xóa chuyên mục
     */
    public function destroy(Category $category)
    {
        // Kiểm tra xem có chuyên mục con không
        if ($category->children()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa chuyên mục có chuyên mục con.']);
        }
        
        // Kiểm tra xem có bài đăng không
        if ($category->threads()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa chuyên mục có bài đăng.']);
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Chuyên mục đã được xóa thành công.');
    }
    
    /**
     * Sắp xếp lại thứ tự chuyên mục
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->categories as $categoryData) {
            $category = Category::find($categoryData['id']);
            $category->order = $categoryData['order'];
            $category->save();
        }
        
        return response()->json(['success' => true]);
    }
}
