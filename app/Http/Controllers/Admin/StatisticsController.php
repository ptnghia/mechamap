<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Hiển thị trang thống kê tổng quan
     */
    public function index(): View
    {
        // Thống kê tổng quan
        $overviewStats = [
            'users' => User::count(),
            'threads' => Thread::count(),
            'posts' => Post::count(),
            'comments' => Comment::count(),
            'forums' => Forum::count(),
            'categories' => Category::count(),
        ];
        
        // Thống kê người dùng mới theo tháng (12 tháng gần nhất)
        $userStats = User::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Thống kê bài đăng mới theo tháng (12 tháng gần nhất)
        $threadStats = Thread::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Thống kê tương tác theo tháng (12 tháng gần nhất)
        $interactionStats = DB::table(DB::raw('(
                SELECT created_at FROM posts
                UNION ALL
                SELECT created_at FROM comments
            ) as interactions'))
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Thống kê', 'url' => route('admin.statistics.index')]
        ];
        
        return view('admin.statistics.index', compact('overviewStats', 'userStats', 'threadStats', 'interactionStats', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị thống kê người dùng
     */
    public function users(): View
    {
        // Thống kê người dùng theo vai trò
        $roleStats = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();
        
        // Thống kê người dùng mới theo tháng (12 tháng gần nhất)
        $timeStats = User::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Thống kê người dùng hoạt động nhất (top 10)
        $activeUsers = User::select('users.id', 'users.name', 'users.username', 
                DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id) as thread_count'),
                DB::raw('(SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id) as post_count'),
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id) as comment_count'))
            ->orderByRaw('(thread_count + post_count + comment_count) DESC')
            ->limit(10)
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Thống kê', 'url' => route('admin.statistics.index')],
            ['title' => 'Thống kê người dùng', 'url' => route('admin.statistics.users')]
        ];
        
        return view('admin.statistics.users', compact('roleStats', 'timeStats', 'activeUsers', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị thống kê nội dung
     */
    public function content(): View
    {
        // Thống kê bài đăng theo trạng thái
        $threadStatusStats = Thread::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
        // Thống kê bài đăng theo diễn đàn
        $forumStats = Thread::select('forum_id', DB::raw('count(*) as total'))
            ->groupBy('forum_id')
            ->with('forum')
            ->get();
        
        // Thống kê bài đăng theo chuyên mục
        $categoryStats = Thread::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();
        
        // Thống kê bài đăng theo thời gian (12 tháng gần nhất)
        $threadTimeStats = Thread::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Thống kê bình luận theo thời gian (12 tháng gần nhất)
        $commentTimeStats = Comment::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Thống kê', 'url' => route('admin.statistics.index')],
            ['title' => 'Thống kê nội dung', 'url' => route('admin.statistics.content')]
        ];
        
        return view('admin.statistics.content', compact('threadStatusStats', 'forumStats', 'categoryStats', 'threadTimeStats', 'commentTimeStats', 'breadcrumbs'));
    }
    
    /**
     * Hiển thị thống kê tương tác
     */
    public function interactions(): View
    {
        // Thống kê tương tác theo loại
        $typeStats = [
            'posts' => Post::count(),
            'comments' => Comment::count(),
            'likes' => DB::table('thread_likes')->count() + DB::table('comment_likes')->count(),
            'saves' => DB::table('thread_saves')->count(),
        ];
        
        // Thống kê tương tác theo thời gian (12 tháng gần nhất)
        $timeStats = DB::table(DB::raw('(
                SELECT created_at, "post" as type FROM posts
                UNION ALL
                SELECT created_at, "comment" as type FROM comments
                UNION ALL
                SELECT created_at, "like" as type FROM thread_likes
                UNION ALL
                SELECT created_at, "like" as type FROM comment_likes
                UNION ALL
                SELECT created_at, "save" as type FROM thread_saves
            ) as interactions'))
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), 'type', DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month', 'type')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Thống kê bài đăng có nhiều tương tác nhất (top 10)
        $popularThreads = Thread::select('threads.id', 'threads.title', 
                DB::raw('(SELECT COUNT(*) FROM posts WHERE posts.thread_id = threads.id) as post_count'),
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.thread_id = threads.id) as comment_count'),
                DB::raw('(SELECT COUNT(*) FROM thread_likes WHERE thread_likes.thread_id = threads.id) as like_count'),
                DB::raw('(SELECT COUNT(*) FROM thread_saves WHERE thread_saves.thread_id = threads.id) as save_count'))
            ->orderByRaw('(post_count + comment_count + like_count + save_count) DESC')
            ->limit(10)
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Thống kê', 'url' => route('admin.statistics.index')],
            ['title' => 'Thống kê tương tác', 'url' => route('admin.statistics.interactions')]
        ];
        
        return view('admin.statistics.interactions', compact('typeStats', 'timeStats', 'popularThreads', 'breadcrumbs'));
    }
    
    /**
     * Xuất báo cáo thống kê
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'overview');
        $format = $request->input('format', 'csv');
        
        // Xử lý xuất báo cáo theo loại và định dạng
        // (Phần này sẽ được triển khai sau)
        
        return redirect()->back()->with('success', 'Báo cáo đã được xuất thành công.');
    }
}
