<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * Hiển thị danh sách diễn đàn
     */
    public function index(): View
    {
        $forums = Forum::withCount('threads')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();
        
        // Tổ chức diễn đàn thành cấu trúc cây
        $rootForums = $forums->whereNull('parent_id');
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý diễn đàn', 'url' => route('admin.forums.index')]
        ];
        
        return view('admin.forums.index', compact('forums', 'rootForums', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form tạo diễn đàn mới
     */
    public function create(): View
    {
        $forums = Forum::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý diễn đàn', 'url' => route('admin.forums.index')],
            ['title' => 'Tạo diễn đàn mới', 'url' => route('admin.forums.create')]
        ];
        
        return view('admin.forums.create', compact('forums', 'categories', 'breadcrumbs'));
    }
    
    /**
     * Lưu diễn đàn mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:forums,id',
            'order' => 'nullable|integer|min:0',
            'is_private' => 'boolean',
        ]);
        
        $forum = new Forum();
        $forum->name = $request->name;
        $forum->slug = Str::slug($request->name);
        $forum->description = $request->description;
        $forum->parent_id = $request->parent_id;
        $forum->order = $request->order ?? 0;
        $forum->is_private = $request->has('is_private');
        $forum->save();
        
        return redirect()->route('admin.forums.index')
            ->with('success', 'Diễn đàn đã được tạo thành công.');
    }
    
    /**
     * Hiển thị chi tiết diễn đàn
     */
    public function show(Forum $forum): View
    {
        $forum->load('parent', 'subForums');
        $threads = $forum->threads()->with(['user', 'category'])->latest()->paginate(10);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý diễn đàn', 'url' => route('admin.forums.index')],
            ['title' => $forum->name, 'url' => route('admin.forums.show', $forum)]
        ];
        
        return view('admin.forums.show', compact('forum', 'threads', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form chỉnh sửa diễn đàn
     */
    public function edit(Forum $forum): View
    {
        $forums = Forum::where('id', '!=', $forum->id)
            ->orderBy('name')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý diễn đàn', 'url' => route('admin.forums.index')],
            ['title' => 'Chỉnh sửa diễn đàn', 'url' => route('admin.forums.edit', $forum)]
        ];
        
        return view('admin.forums.edit', compact('forum', 'forums', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật diễn đàn
     */
    public function update(Request $request, Forum $forum)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:forums,id',
            'order' => 'nullable|integer|min:0',
            'is_private' => 'boolean',
        ]);
        
        // Kiểm tra xem parent_id có phải là con của forum hiện tại không
        if ($request->parent_id) {
            $parent = Forum::find($request->parent_id);
            $currentId = $forum->id;
            
            while ($parent) {
                if ($parent->id == $currentId) {
                    return back()->withErrors(['parent_id' => 'Không thể chọn diễn đàn con làm diễn đàn cha.'])->withInput();
                }
                $parent = $parent->parent;
            }
        }
        
        $forum->name = $request->name;
        // Chỉ cập nhật slug nếu tên thay đổi
        if ($forum->name != $request->name) {
            $forum->slug = Str::slug($request->name);
        }
        $forum->description = $request->description;
        $forum->parent_id = $request->parent_id;
        $forum->order = $request->order ?? 0;
        $forum->is_private = $request->has('is_private');
        $forum->save();
        
        return redirect()->route('admin.forums.index')
            ->with('success', 'Diễn đàn đã được cập nhật thành công.');
    }
    
    /**
     * Xóa diễn đàn
     */
    public function destroy(Forum $forum)
    {
        // Kiểm tra xem có diễn đàn con không
        if ($forum->subForums()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa diễn đàn có diễn đàn con.']);
        }
        
        // Kiểm tra xem có bài đăng không
        if ($forum->threads()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa diễn đàn có bài đăng.']);
        }
        
        $forum->delete();
        
        return redirect()->route('admin.forums.index')
            ->with('success', 'Diễn đàn đã được xóa thành công.');
    }
    
    /**
     * Sắp xếp lại thứ tự diễn đàn
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'forums' => 'required|array',
            'forums.*.id' => 'required|exists:forums,id',
            'forums.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->forums as $forumData) {
            $forum = Forum::find($forumData['id']);
            $forum->order = $forumData['order'];
            $forum->save();
        }
        
        return response()->json(['success' => true]);
    }
}
