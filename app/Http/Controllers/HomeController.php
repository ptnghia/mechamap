<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Showcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Khôi phục logic gốc với một số điều chỉnh để tránh lỗi
        try {
            // Get latest threads - chỉ lấy thread đáp ứng điều kiện hiển thị
            $latestThreads = Thread::with(['user', 'category', 'forum'])
                ->publicVisible() // Lọc thread không bị xóa/ẩn/archived/spam
                ->whereNull('deleted_at') // Đảm bảo không hiển thị thread đã xóa mềm
                ->where(function ($query) {
                    // Kiểm tra các điều kiện trạng thái khác
                    $query->where('status', '!=', 'cancelled')
                        ->where('status', '!=', 'rejected')
                        ->where(function ($q) {
                            $q->whereNull('status')
                                ->orWhere('status', '!=', 'deleted');
                        });
                })
                ->withCount('allComments as comments_count')
                ->latest()
                ->take(10)
                ->get();

            // Get featured threads (sticky or with most views)
            $featuredThreads = Thread::with(['user'])
                ->publicVisible() // Áp dụng điều kiện publicVisible trước
                ->whereNull('deleted_at') // Đảm bảo không hiển thị thread đã xóa mềm
                ->where(function ($query) {
                    // Kiểm tra các trạng thái hủy
                    $query->where('status', '!=', 'cancelled')
                        ->where('status', '!=', 'rejected')
                        ->where(function ($q) {
                            $q->whereNull('status')
                                ->orWhere('status', '!=', 'deleted');
                        });
                })
                ->where(function ($query) {
                    $query->where('is_featured', true)
                        ->orWhere('is_sticky', true)
                        ->orWhere(function ($subquery) {
                            $subquery->where('view_count', '>', 100);
                        });
                })
                ->latest()
                ->take(4)
                ->get();

            // Get top forums with thread count
            $topForums = Forum::select('forums.*', DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.forum_id = forums.id) as threads_count'))
                ->orderBy('threads_count', 'desc')
                ->take(5)
                ->get();

            // Get categories with thread count
            $categories = Category::select('categories.*', DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.category_id = categories.id) as threads_count'))
                ->orderBy('threads_count', 'desc')
                ->take(10)
                ->get();

            // Get top contributors this month
            $topContributors = User::select(
                'users.*',
                DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id AND threads.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as threads_count'),
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id AND comments.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as comments_count'),
                DB::raw('((SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id AND threads.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) +
                             (SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id AND comments.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH))) as contribution_count')
            )
                ->orderBy('contribution_count', 'desc')
                ->take(5)
                ->get();

            // Get featured showcases
            $featuredShowcases = Showcase::with(['user'])
                ->where('status', 'featured')
                ->whereNotNull('cover_image')
                ->orderBy('view_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            return view('home', compact(
                'latestThreads',
                'featuredThreads',
                'topForums',
                'categories',
                'topContributors',
                'featuredShowcases'
            ));
        } catch (\Exception $e) {
            // Nếu có lỗi, return view đơn giản
            return view('test-home');
        }
    }

    /**
     * API endpoint to get more threads for infinite scrolling.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMoreThreads(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        // Tính toán offset: Page 1 = skip 10 (vì trang chủ đã show 10 đầu), Page 2 = skip 20, etc.
        $skipAmount = ($page - 1) * $perPage + 10;

        $threads = Thread::with(['user', 'category', 'forum', 'media'])
            ->publicVisible() // Áp dụng điều kiện publicVisible để lọc thread
            ->whereNull('deleted_at') // Đảm bảo không hiển thị thread đã xóa mềm
            ->where(function ($query) {
                // Kiểm tra các trạng thái hủy
                $query->where('status', '!=', 'cancelled')
                    ->where('status', '!=', 'rejected')
                    ->where(function ($q) {
                        $q->whereNull('status')
                            ->orWhere('status', '!=', 'deleted');
                    });
            })
            ->withCount('allComments as comments_count')
            ->latest()
            ->skip($skipAmount)
            ->take($perPage + 1) // Lấy thêm 1 để check có còn nữa không
            ->get();

        $hasMore = $threads->count() > $perPage;

        if ($hasMore) {
            $threads = $threads->take($perPage);
        }

        // Xử lý dữ liệu trước khi trả về
        $threads->transform(function ($thread) {
            // Đảm bảo user có avatar URL
            if ($thread->user) {
                // Sử dụng helper function có sẵn hoặc fallback
                $thread->user->profile_photo_url = $thread->user->profile_photo_url
                    ?? $thread->user->avatar
                    ?? 'https://ui-avatars.com/api/?name=' . urlencode($thread->user->name) . '&color=7F9CF5&background=EBF4FF';
            }

            // Đảm bảo featured_image được load đúng từ relationship
            $thread->featured_image = $thread->getFeaturedImageAttribute();

            // Làm sạch content để hiển thị preview
            if ($thread->content) {
                $thread->content = strip_tags($thread->content);
            }

            // Thêm bookmark và follow status cho authenticated users
            if (Auth::check()) {
                $userId = Auth::id();
                $thread->is_bookmarked = \App\Models\ThreadBookmark::where('user_id', $userId)
                    ->where('thread_id', $thread->id)
                    ->exists();

                $thread->is_followed = \App\Models\ThreadFollow::where('user_id', $userId)
                    ->where('thread_id', $thread->id)
                    ->exists();
            } else {
                $thread->is_bookmarked = false;
                $thread->is_followed = false;
            }

            return $thread;
        });

        return response()->json([
            'threads' => $threads,
            'has_more' => $hasMore,
            'current_page' => $page,
            'total_loaded' => $skipAmount + $threads->count()
        ]);
    }
}
