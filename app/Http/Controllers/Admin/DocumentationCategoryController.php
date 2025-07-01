<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationCategoryController extends Controller
{
    /**
     * Display a listing of documentation categories
     */
    public function index(Request $request)
    {
        $query = DocumentationCategory::with(['parent', 'children'])
                                    ->withCount('documentations')
                                    ->orderBy('sort_order');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by parent
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $categories = $query->paginate(20);
        $parentCategories = DocumentationCategory::whereNull('parent_id')
                                                ->orderBy('name')
                                                ->get();

        return view('admin.documentation.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = DocumentationCategory::whereNull('parent_id')
                                                ->active()
                                                ->orderBy('name')
                                                ->get();
        $userRoles = ['admin', 'moderator', 'senior_member', 'member', 'supplier', 'manufacturer', 'brand'];

        return view('admin.documentation.categories.create', compact('parentCategories', 'userRoles'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:documentation_categories,slug',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'parent_id' => 'nullable|exists:documentation_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'allowed_roles' => 'nullable|array',
        ]);

        // Handle slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default color if not provided
        if (empty($validated['color_code'])) {
            $validated['color_code'] = '#007bff';
        }

        DocumentationCategory::create($validated);

        return redirect()
            ->route('admin.documentation.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công!');
    }

    /**
     * Display the specified category
     */
    public function show(DocumentationCategory $category)
    {
        $category->load(['parent', 'children.documentations', 'documentations.author']);
        
        $statistics = $category->getStatistics();

        return view('admin.documentation.categories.show', compact('category', 'statistics'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(DocumentationCategory $category)
    {
        $parentCategories = DocumentationCategory::whereNull('parent_id')
                                                ->where('id', '!=', $category->id)
                                                ->active()
                                                ->orderBy('name')
                                                ->get();
        $userRoles = ['admin', 'moderator', 'senior_member', 'member', 'supplier', 'manufacturer', 'brand'];

        return view('admin.documentation.categories.edit', compact('category', 'parentCategories', 'userRoles'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, DocumentationCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:documentation_categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'parent_id' => 'nullable|exists:documentation_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'allowed_roles' => 'nullable|array',
        ]);

        // Handle slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Prevent setting parent to self or descendant
        if ($validated['parent_id'] === $category->id) {
            return redirect()->back()
                ->withErrors(['parent_id' => 'Danh mục không thể là cha của chính nó.']);
        }

        $descendants = collect($category->getDescendants())->pluck('id')->toArray();
        if (in_array($validated['parent_id'], $descendants)) {
            return redirect()->back()
                ->withErrors(['parent_id' => 'Danh mục không thể là cha của danh mục con của nó.']);
        }

        $category->update($validated);

        return redirect()
            ->route('admin.documentation.categories.show', $category)
            ->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(DocumentationCategory $category)
    {
        // Check if category has documentations
        if ($category->documentations()->count() > 0) {
            return redirect()->back()
                ->withErrors(['delete' => 'Không thể xóa danh mục có chứa tài liệu. Vui lòng di chuyển hoặc xóa tài liệu trước.']);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->back()
                ->withErrors(['delete' => 'Không thể xóa danh mục có chứa danh mục con. Vui lòng di chuyển hoặc xóa danh mục con trước.']);
        }

        $category->delete();

        return redirect()
            ->route('admin.documentation.categories.index')
            ->with('success', 'Danh mục đã được xóa thành công!');
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:documentation_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            DocumentationCategory::where('id', $categoryData['id'])
                                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Thứ tự đã được cập nhật.']);
    }

    /**
     * Get category tree for API
     */
    public function getTree()
    {
        $tree = DocumentationCategory::getTree();
        return response()->json($tree);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,make_public,make_private',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:documentation_categories,id',
        ]);

        $categories = DocumentationCategory::whereIn('id', $request->selected_ids);

        switch ($request->action) {
            case 'delete':
                // Check if any category has documentations or children
                $hasContent = DocumentationCategory::whereIn('id', $request->selected_ids)
                    ->where(function ($q) {
                        $q->has('documentations')
                          ->orHas('children');
                    })->exists();

                if ($hasContent) {
                    return redirect()->back()
                        ->withErrors(['bulk' => 'Không thể xóa danh mục có chứa tài liệu hoặc danh mục con.']);
                }

                $categories->delete();
                $message = 'Đã xóa các danh mục được chọn.';
                break;

            case 'activate':
                $categories->update(['is_active' => true]);
                $message = 'Đã kích hoạt các danh mục được chọn.';
                break;

            case 'deactivate':
                $categories->update(['is_active' => false]);
                $message = 'Đã vô hiệu hóa các danh mục được chọn.';
                break;

            case 'make_public':
                $categories->update(['is_public' => true]);
                $message = 'Đã công khai các danh mục được chọn.';
                break;

            case 'make_private':
                $categories->update(['is_public' => false]);
                $message = 'Đã chuyển thành riêng tư các danh mục được chọn.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
