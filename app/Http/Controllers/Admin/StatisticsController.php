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
        $activeUsers = User::select(
            'users.id',
            'users.name',
            'users.username',
            DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id) as thread_count'),
            DB::raw('(SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id) as post_count'),
            DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id) as comment_count')
        )
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
        $popularThreads = Thread::select(
            'threads.id',
            'threads.title',
            DB::raw('(SELECT COUNT(*) FROM posts WHERE posts.thread_id = threads.id) as post_count'),
            DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.thread_id = threads.id) as comment_count'),
            DB::raw('(SELECT COUNT(*) FROM thread_likes WHERE thread_likes.thread_id = threads.id) as like_count'),
            DB::raw('(SELECT COUNT(*) FROM thread_saves WHERE thread_saves.thread_id = threads.id) as save_count')
        )
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

        // Tạo tên file
        $fileName = 'thong-ke-' . $type . '-' . date('Y-m-d') . '.' . $format;

        // Tạo header cho file CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // Tạo dữ liệu mẫu dựa trên loại báo cáo
        $data = $this->generateSampleData($type);

        // Tạo file CSV
        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Thêm BOM để hỗ trợ Unicode trong Excel
            fputs($file, "\xEF\xBB\xBF");

            // Thêm header
            fputcsv($file, array_keys($data[0]));

            // Thêm dữ liệu
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tạo dữ liệu mẫu cho báo cáo
     */
    private function generateSampleData($type)
    {
        $data = [];

        switch ($type) {
            case 'overview':
                // Dữ liệu tổng quan
                $data = [
                    ['Loại', 'Số lượng', 'Tỷ lệ', 'Ngày cập nhật'],
                    ['Người dùng', rand(100, 1000), rand(10, 100) . '%', date('Y-m-d')],
                    ['Bài đăng', rand(500, 5000), rand(10, 100) . '%', date('Y-m-d')],
                    ['Bình luận', rand(1000, 10000), rand(10, 100) . '%', date('Y-m-d')],
                    ['Diễn đàn', rand(10, 50), rand(10, 100) . '%', date('Y-m-d')],
                    ['Chuyên mục', rand(20, 100), rand(10, 100) . '%', date('Y-m-d')],
                ];
                break;

            case 'users':
                // Dữ liệu người dùng
                $roles = ['Quản trị viên', 'Điều hành viên', 'Thành viên cấp cao', 'Thành viên'];
                $statuses = ['Hoạt động', 'Không hoạt động', 'Bị khóa'];

                for ($i = 0; $i < 20; $i++) {
                    $data[] = [
                        'ID' => $i + 1,
                        'Tên người dùng' => 'User' . ($i + 1),
                        'Email' => 'user' . ($i + 1) . '@example.com',
                        'Vai trò' => $roles[array_rand($roles)],
                        'Trạng thái' => $statuses[array_rand($statuses)],
                        'Số bài đăng' => rand(0, 100),
                        'Số bình luận' => rand(0, 200),
                        'Ngày tham gia' => date('Y-m-d', strtotime('-' . rand(1, 365) . ' days')),
                    ];
                }
                break;

            case 'content':
                // Dữ liệu nội dung
                $statuses = ['Đã xuất bản', 'Chờ duyệt', 'Bị từ chối', 'Bị khóa'];
                $forums = ['Diễn đàn 1', 'Diễn đàn 2', 'Diễn đàn 3', 'Diễn đàn 4', 'Diễn đàn 5'];
                $categories = ['Chuyên mục 1', 'Chuyên mục 2', 'Chuyên mục 3', 'Chuyên mục 4'];

                for ($i = 0; $i < 20; $i++) {
                    $data[] = [
                        'ID' => $i + 1,
                        'Tiêu đề' => 'Bài đăng mẫu ' . ($i + 1),
                        'Tác giả' => 'User' . rand(1, 10),
                        'Diễn đàn' => $forums[array_rand($forums)],
                        'Chuyên mục' => $categories[array_rand($categories)],
                        'Trạng thái' => $statuses[array_rand($statuses)],
                        'Số bình luận' => rand(0, 50),
                        'Số lượt xem' => rand(10, 1000),
                        'Ngày tạo' => date('Y-m-d', strtotime('-' . rand(1, 365) . ' days')),
                    ];
                }
                break;

            case 'interactions':
                // Dữ liệu tương tác
                $types = ['Bình luận', 'Thích', 'Lưu', 'Báo cáo'];
                $threads = ['Bài đăng 1', 'Bài đăng 2', 'Bài đăng 3', 'Bài đăng 4', 'Bài đăng 5'];
                $users = ['User1', 'User2', 'User3', 'User4', 'User5', 'User6', 'User7', 'User8', 'User9', 'User10'];

                for ($i = 0; $i < 20; $i++) {
                    $data[] = [
                        'ID' => $i + 1,
                        'Loại tương tác' => $types[array_rand($types)],
                        'Bài đăng' => $threads[array_rand($threads)],
                        'Người dùng' => $users[array_rand($users)],
                        'Nội dung' => 'Nội dung tương tác mẫu ' . ($i + 1),
                        'Ngày tạo' => date('Y-m-d', strtotime('-' . rand(1, 30) . ' days')),
                    ];
                }
                break;

            default:
                // Dữ liệu mặc định
                $data = [
                    ['ID', 'Tên', 'Giá trị', 'Ngày'],
                    [1, 'Mẫu 1', rand(100, 1000), date('Y-m-d')],
                    [2, 'Mẫu 2', rand(100, 1000), date('Y-m-d')],
                    [3, 'Mẫu 3', rand(100, 1000), date('Y-m-d')],
                    [4, 'Mẫu 4', rand(100, 1000), date('Y-m-d')],
                    [5, 'Mẫu 5', rand(100, 1000), date('Y-m-d')],
                ];
                break;
        }

        return $data;
    }
}
