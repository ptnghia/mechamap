<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Hiển thị danh sách bình luận
     */
    public function index(Request $request): View
    {
        // Lấy các tham số lọc
        $thread_id = $request->input('thread_id');
        $user_id = $request->input('user_id');
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        
        // Khởi tạo query
        $query = Comment::with(['user', 'thread']);
        
        // Áp dụng các bộ lọc
        if ($thread_id) {
            $query->where('thread_id', $thread_id);
        }
        
        if ($user_id) {
            $query->where('user_id', $user_id);
        }
        
        if ($search) {
            $query->where('content', 'like', "%{$search}%");
        }
        
        if ($status == 'reported') {
            $query->whereHas('reports');
        } elseif ($status == 'flagged') {
            $query->where('is_flagged', true);
        } elseif ($status == 'hidden') {
            $query->where('is_hidden', true);
        }
        
        // Sắp xếp và phân trang
        $comments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bình luận', 'url' => route('admin.comments.index')]
        ];
        
        return view('admin.comments.index', compact('comments', 'breadcrumbs', 'thread_id', 'user_id', 'search', 'status'));
    }
    
    /**
     * Hiển thị chi tiết bình luận
     */
    public function show(Comment $comment): View
    {
        $comment->load(['user', 'thread', 'parent', 'replies.user']);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bình luận', 'url' => route('admin.comments.index')],
            ['title' => 'Chi tiết bình luận', 'url' => route('admin.comments.show', $comment)]
        ];
        
        return view('admin.comments.show', compact('comment', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị form chỉnh sửa bình luận
     */
    public function edit(Comment $comment): View
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bình luận', 'url' => route('admin.comments.index')],
            ['title' => 'Chỉnh sửa bình luận', 'url' => route('admin.comments.edit', $comment)]
        ];
        
        return view('admin.comments.edit', compact('comment', 'breadcrumbs'));
    }
    
    /**
     * Cập nhật bình luận
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string',
            'is_flagged' => 'boolean',
            'is_hidden' => 'boolean',
        ]);
        
        $comment->content = $request->content;
        $comment->is_flagged = $request->has('is_flagged');
        $comment->is_hidden = $request->has('is_hidden');
        $comment->save();
        
        return redirect()->route('admin.comments.index')
            ->with('success', 'Bình luận đã được cập nhật thành công.');
    }
    
    /**
     * Xóa bình luận
     */
    public function destroy(Comment $comment)
    {
        // Xóa các phản hồi của bình luận
        $comment->replies()->delete();
        
        // Xóa các lượt thích của bình luận
        $comment->likes()->delete();
        
        // Xóa bình luận
        $comment->delete();
        
        return redirect()->route('admin.comments.index')
            ->with('success', 'Bình luận đã được xóa thành công.');
    }
    
    /**
     * Ẩn/hiện bình luận
     */
    public function toggleVisibility(Comment $comment)
    {
        $comment->is_hidden = !$comment->is_hidden;
        $comment->save();
        
        $status = $comment->is_hidden ? 'ẩn' : 'hiện';
        
        return redirect()->route('admin.comments.index')
            ->with('success', "Bình luận đã được {$status} thành công.");
    }
    
    /**
     * Đánh dấu/bỏ đánh dấu bình luận
     */
    public function toggleFlag(Comment $comment)
    {
        $comment->is_flagged = !$comment->is_flagged;
        $comment->save();
        
        $status = $comment->is_flagged ? 'đánh dấu' : 'bỏ đánh dấu';
        
        return redirect()->route('admin.comments.index')
            ->with('success', "Bình luận đã được {$status} thành công.");
    }
    
    /**
     * Thống kê bình luận
     */
    public function statistics(): View
    {
        // Thống kê theo trạng thái
        $statusStats = [
            'total' => Comment::count(),
            'flagged' => Comment::where('is_flagged', true)->count(),
            'hidden' => Comment::where('is_hidden', true)->count(),
            'reported' => Comment::whereHas('reports')->count(),
        ];
        
        // Thống kê theo bài đăng (top 10)
        $threadStats = Comment::select('thread_id', DB::raw('count(*) as total'))
            ->groupBy('thread_id')
            ->with('thread')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Thống kê theo người dùng (top 10)
        $userStats = Comment::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Thống kê theo thời gian (theo tháng trong năm hiện tại)
        $timeStats = Comment::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Quản lý bình luận', 'url' => route('admin.comments.index')],
            ['title' => 'Thống kê bình luận', 'url' => route('admin.comments.statistics')]
        ];
        
        return view('admin.comments.statistics', compact('statusStats', 'threadStats', 'userStats', 'timeStats', 'breadcrumbs'));
    }
}
