<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Showcase;
use App\Models\MarketplaceProduct;
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
            $latestThreads = Thread::with(['user', 'category', 'forum', 'media'])
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
                ->orderBy('is_sticky', 'desc')
                ->latest()
                ->take(10)
                ->get();

            // Get featured threads (sticky or with most views)
            $featuredThreads = Thread::with(['user', 'media'])
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
                ->orderBy('is_sticky', 'desc')
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

            // Get featured showcases using optimized query
            $featuredShowcases = $this->getFeaturedShowcasesForHome();

            // Get latest marketplace products for slide swiper
            $latestProducts = MarketplaceProduct::with(['seller.user', 'category'])
                ->where('status', 'approved')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return view('home', compact(
                'latestThreads',
                'featuredThreads',
                'topForums',
                'categories',
                'topContributors',
                'featuredShowcases',
                'latestProducts'
            ));
        } catch (\Exception $e) {
            // Nếu có lỗi, return view đơn giản với dữ liệu trống
            \Log::error('HomeController error: ' . $e->getMessage());
            return view('home', [
                'latestThreads' => collect(),
                'featuredThreads' => collect(),
                'topForums' => collect(),
                'categories' => collect(),
                'topContributors' => collect(),
                'featuredShowcases' => collect(),
                'latestProducts' => collect()
            ]);
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
        try {
            $page = $request->input('page', 1);
            $perPage = 10;
            $skipAmount = ($page - 1) * $perPage + 10;

            // Lấy đầy đủ dữ liệu bao gồm category, forum, và featured_image
            $threads = \DB::table('threads')
                ->join('users', 'threads.user_id', '=', 'users.id')
                ->leftJoin('forums', 'threads.forum_id', '=', 'forums.id')
                ->leftJoin('categories', 'forums.category_id', '=', 'categories.id')
                ->select(
                    'threads.id',
                    'threads.title',
                    'threads.content',
                    'threads.created_at',
                    'threads.view_count',
                    'threads.featured_image',
                    'threads.forum_id',
                    'users.name as user_name',
                    'users.avatar as user_avatar',
                    'forums.name as forum_name',
                    'forums.id as forum_id_actual',
                    'categories.name as category_name',
                    'categories.id as category_id'
                )
                ->where('threads.status', 'published')
                ->whereNull('threads.deleted_at')
                ->orderBy('threads.created_at', 'desc')
                ->skip($skipAmount)
                ->take($perPage + 1)
                ->get();

            $hasMore = $threads->count() > $perPage;
            if ($hasMore) {
                $threads = $threads->take($perPage);
            }

            // Format dữ liệu đồng bộ hoàn toàn với cấu trúc partial blade
            $formattedThreads = $threads->map(function ($thread) {
                // Xử lý featured_image - tìm ảnh từ content nếu không có featured_image
                $actualImage = null;
                if ($thread->featured_image) {
                    $actualImage = $thread->featured_image;
                } else {
                    // Tìm ảnh đầu tiên trong content
                    preg_match('/<img[^>]+src="([^"]+)"/', $thread->content, $matches);
                    if (!empty($matches[1])) {
                        $actualImage = $matches[1];
                    }
                }

                return [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'content' => \Str::limit(strip_tags($thread->content), 220), // Đồng bộ với partial blade
                    'created_at' => $thread->created_at,
                    'view_count' => $thread->view_count ?? 0,
                    'comments_count' => 0, // Tạm thời set 0, có thể cập nhật sau
                    'status' => null, // Không hiển thị status trong load more
                    'is_sticky' => false, // Tạm thời set false
                    'is_locked' => false, // Tạm thời set false
                    'slug' => null, // Sử dụng ID thay vì slug
                    'user' => [
                        'name' => $thread->user_name,
                        'profile_photo_url' => $thread->user_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($thread->user_name) . '&color=7F9CF5&background=EBF4FF'
                    ],
                    'forum' => [
                        'id' => $thread->forum_id_actual,
                        'name' => $thread->forum_name
                    ],
                    'category' => $thread->category_name ? [
                        'id' => $thread->category_id,
                        'name' => $thread->category_name
                    ] : null,
                    'featured_image' => $actualImage,
                    'actual_image' => $actualImage, // Đồng bộ với JavaScript
                    // Action states cho authenticated users
                    'is_bookmarked' => false,
                    'is_followed' => false
                ];
            });

            return response()->json([
                'threads' => $formattedThreads,
                'has_more' => $hasMore,
                'current_page' => $page,
                'total_loaded' => $skipAmount + $formattedThreads->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('HomeController getMoreThreads error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Có lỗi xảy ra khi tải thêm threads',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured showcases for home page with smart ranking
     *
     * @param string $sortBy 'most_viewed' hoặc 'newest'
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFeaturedShowcasesForHome($sortBy = 'most_viewed')
    {
        $query = Showcase::with(['user', 'media', 'ratings'])
            ->where('is_public', 1) // Chỉ lấy showcases công khai
            ->whereIn('status', ['published', 'featured', 'approved']) // Mở rộng status
            ->where(function ($query) {
                // Ưu tiên showcases có hình ảnh
                $query->whereNotNull('cover_image')
                    ->orWhereHas('media', function ($mediaQuery) {
                        $mediaQuery->where('mime_type', 'like', 'image/%');
                    });
            })
            ->orderByRaw("CASE WHEN status = 'featured' THEN 1 ELSE 2 END"); // Featured trước

        // Áp dụng sắp xếp theo yêu cầu
        if ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc') // Mới nhất trước
                  ->orderBy('view_count', 'desc') // Thứ tự phụ: lượt xem
                  ->orderBy('rating_average', 'desc'); // Cuối cùng: rating
        } else { // most_viewed (default)
            $query->orderBy('view_count', 'desc') // Lượt xem cao nhất trước
                  ->orderBy('rating_average', 'desc') // Thứ tự phụ: rating cao
                  ->orderBy('created_at', 'desc'); // Cuối cùng: mới nhất
        }

        return $query->take(12)->get(); // Lấy đúng 12 showcases
    }
}
