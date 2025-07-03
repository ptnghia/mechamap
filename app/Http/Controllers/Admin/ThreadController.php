<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * Hiển thị danh sách bài đăng
     */
    public function index(Request $request): View
    {
        // Lấy các tham số lọc
        $status = $request->input('status');
        $forum_id = $request->input('forum_id');
        $category_id = $request->input('category_id');
        $search = $request->input('search');
        $user_id = $request->input('user_id');

        // Khởi tạo query
        $query = Thread::with(['user', 'forum', 'category']);

        // Áp dụng các bộ lọc
        if ($status) {
            $query->where('status', $status);
        }

        if ($forum_id) {
            $query->where('forum_id', $forum_id);
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

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        // Sắp xếp và phân trang
        $threads = $query->orderBy('created_at', 'desc')->paginate(20);

        // Lấy danh sách diễn đàn và chuyên mục cho bộ lọc
        $forums = Forum::all();
        $categories = Category::all();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài đăng', 'url' => route('admin.threads.index')]
        ];

        return view('admin.threads.index', compact('threads', 'forums', 'categories', 'breadcrumbs', 'status', 'forum_id', 'category_id', 'search', 'user_id'));
    }

    /**
     * Hiển thị form tạo bài đăng mới
     */
    public function create(): View
    {
        $forums = Forum::all();
        $categories = Category::all();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài đăng', 'url' => route('admin.threads.index')],
            ['title' => 'Tạo bài đăng mới', 'url' => route('admin.threads.create')]
        ];

        return view('admin.threads.create', compact('forums', 'categories', 'breadcrumbs'));
    }

    /**
     * Lưu bài đăng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'forum_id' => 'required|exists:forums,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,pending,published,rejected',
            'is_sticky' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $thread = new Thread();
        $thread->title = $request->title;
        $thread->slug = Str::slug($request->title);
        $thread->content = $request->content;
        $thread->user_id = Auth::id();
        $thread->forum_id = $request->forum_id;
        $thread->category_id = $request->category_id;
        $thread->status = $request->status;
        $thread->is_sticky = $request->has('is_sticky');
        $thread->is_featured = $request->has('is_featured');
        $thread->save();

        return redirect()->route('admin.threads.index')
            ->with('success', 'Bài đăng đã được tạo thành công.');
    }

    /**
     * Hiển thị chi tiết bài đăng
     */
    public function show(Thread $thread): View
    {
        $thread->load(['user', 'forum', 'category', 'posts', 'comments.user', 'comments.replies.user']);

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài đăng', 'url' => route('admin.threads.index')],
            ['title' => $thread->title, 'url' => route('admin.threads.show', $thread)]
        ];

        return view('admin.threads.show', compact('thread', 'breadcrumbs'));
    }

    /**
     * Hiển thị form chỉnh sửa bài đăng
     */
    public function edit(Thread $thread): View
    {
        $forums = Forum::all();
        $categories = Category::all();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài đăng', 'url' => route('admin.threads.index')],
            ['title' => 'Chỉnh sửa bài đăng', 'url' => route('admin.threads.edit', $thread)]
        ];

        return view('admin.threads.edit', compact('thread', 'forums', 'categories', 'breadcrumbs'));
    }

    /**
     * Cập nhật bài đăng
     */
    public function update(Request $request, Thread $thread)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'forum_id' => 'required|exists:forums,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,pending,published,rejected',
            'is_sticky' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $thread->title = $request->title;
        // Chỉ cập nhật slug nếu tiêu đề thay đổi
        if ($thread->title != $request->title) {
            $thread->slug = Str::slug($request->title);
        }
        $thread->content = $request->content;
        $thread->forum_id = $request->forum_id;
        $thread->category_id = $request->category_id;
        $thread->status = $request->status;
        $thread->is_sticky = $request->has('is_sticky');
        $thread->is_featured = $request->has('is_featured');
        $thread->save();

        return redirect()->route('admin.threads.index')
            ->with('success', 'Bài đăng đã được cập nhật thành công.');
    }

    /**
     * Xóa bài đăng
     */
    public function destroy(Thread $thread)
    {
        // Xóa các bài viết, bình luận và tương tác liên quan
        $thread->posts()->delete();
        $thread->comments()->delete();
        $thread->reactions()->delete();
        $thread->likes()->delete();
        $thread->saves()->delete();

        // Xóa bài đăng
        $thread->delete();

        return redirect()->route('admin.threads.index')
            ->with('success', 'Bài đăng đã được xóa thành công.');
    }

    /**
     * Duyệt bài đăng
     */
    public function approve(Thread $thread)
    {
        $thread->status = 'published';
        $thread->save();

        return redirect()->route('admin.threads.index')
            ->with('success', 'Bài đăng đã được duyệt và xuất bản thành công.');
    }

    /**
     * Từ chối bài đăng
     */
    public function reject(Request $request, Thread $thread)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $thread->status = 'rejected';
        $thread->rejection_reason = $request->reason;
        $thread->save();

        return redirect()->route('admin.threads.index')
            ->with('success', 'Bài đăng đã bị từ chối.');
    }

    /**
     * Pin/Unpin thread (Ghim bài đăng)
     */
    public function togglePin(Thread $thread)
    {
        $thread->is_sticky = !$thread->is_sticky;
        $thread->save();

        $message = $thread->is_sticky
            ? 'Bài đăng đã được ghim thành công.'
            : 'Bài đăng đã được bỏ ghim thành công.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Lock/Unlock thread (Khóa bài đăng)
     */
    public function toggleLock(Thread $thread)
    {
        $thread->is_locked = !$thread->is_locked;
        $thread->save();

        $message = $thread->is_locked
            ? 'Bài đăng đã được khóa thành công.'
            : 'Bài đăng đã được mở khóa thành công.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Feature/Unfeature thread (Nổi bật bài đăng)
     */
    public function toggleFeature(Thread $thread)
    {
        $thread->is_featured = !$thread->is_featured;
        $thread->save();

        $message = $thread->is_featured
            ? 'Bài đăng đã được đánh dấu nổi bật.'
            : 'Bài đăng đã được bỏ đánh dấu nổi bật.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Thống kê bài đăng
     */
    public function statistics(): View
    {
        // Thống kê theo trạng thái
        $statusStats = Thread::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Thống kê theo diễn đàn
        $forumStats = Thread::select('forum_id', DB::raw('count(*) as total'))
            ->groupBy('forum_id')
            ->with('forum')
            ->get();

        // Thống kê theo chuyên mục
        $categoryStats = Thread::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Thống kê theo người dùng (top 10)
        $userStats = Thread::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Thống kê theo thời gian (theo tháng trong năm hiện tại)
        $timeStats = Thread::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bài đăng', 'url' => route('admin.threads.index')],
            ['title' => 'Thống kê bài đăng', 'url' => route('admin.threads.statistics')]
        ];

        return view('admin.threads.statistics', compact('statusStats', 'forumStats', 'categoryStats', 'userStats', 'timeStats', 'breadcrumbs'));
    }
}
