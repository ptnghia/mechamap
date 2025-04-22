<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard admin
     */
    public function index(): View
    {
        // Lấy thống kê cơ bản
        $stats = [
            'users' => User::count(),
            'threads' => Thread::count(),
            'posts' => Post::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_threads_today' => Thread::whereDate('created_at', today())->count(),
            'new_posts_today' => Post::whereDate('created_at', today())->count(),
        ];

        // Lấy danh sách người dùng mới nhất
        $latestUsers = User::latest()->take(5)->get();

        // Lấy danh sách bài viết mới nhất
        $latestThreads = Thread::with(['user', 'forum'])->latest()->take(5)->get();

        // Breadcrumbs
        $breadcrumbs = [];

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestThreads', 'breadcrumbs'));
    }
}
