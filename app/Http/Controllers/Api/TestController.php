<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Add multiple threads to showcase for testing
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addThreadsToShowcase()
    {
        try {
            // Xóa tất cả showcase hiện tại (chỉ dùng cho test)
            Showcase::truncate();

            // Lấy 5 thread mới nhất
            $threads = Thread::orderBy('created_at', 'desc')->take(5)->get();

            // Lấy user admin đầu tiên hoặc user đầu tiên nếu không có admin
            $admin = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->first();

            $userId = $admin ? $admin->id : User::first()->id;

            $showcases = [];

            // Thêm các thread vào showcase
            foreach ($threads as $index => $thread) {
                $showcase = new Showcase();
                $showcase->showcaseable_id = $thread->id;
                $showcase->showcaseable_type = Thread::class;
                $showcase->user_id = $userId;
                $showcase->description = "Showcase test for thread: {$thread->title}";
                $showcase->order = 5 - $index; // Để thread đầu tiên có thứ tự cao nhất
                $showcase->save();

                $showcases[] = $showcase;
            }

            // Lấy 3 post mới nhất
            $posts = Post::orderBy('created_at', 'desc')->take(3)->get();

            // Thêm các post vào showcase
            foreach ($posts as $index => $post) {
                $showcase = new Showcase();
                $showcase->showcaseable_id = $post->id;
                $showcase->showcaseable_type = Post::class;
                $showcase->user_id = $userId;
                $showcase->description = "Showcase test for post in thread: {$post->thread->title}";
                $showcase->order = 2 - $index; // Để post đầu tiên có thứ tự cao nhất trong nhóm post
                $showcase->save();

                $showcases[] = $showcase;
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm ' . count($showcases) . ' mục vào showcase thành công.',
                'data' => [
                    'threads_count' => $threads->count(),
                    'posts_count' => $posts->count(),
                    'total_showcases' => count($showcases)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thêm vào showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a single thread to showcase for testing (no authentication required)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSingleThreadToShowcase()
    {
        try {
            // Lấy thread mới nhất
            $thread = Thread::orderBy('created_at', 'desc')->first();

            if (!$thread) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thread nào.'
                ], 404);
            }

            // Lấy user admin đầu tiên hoặc user đầu tiên nếu không có admin
            $admin = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->first();

            $userId = $admin ? $admin->id : User::first()->id;

            // Kiểm tra xem thread đã có trong showcase chưa
            $existingShowcase = Showcase::where('showcaseable_id', $thread->id)
                ->where('showcaseable_type', Thread::class)
                ->first();

            if ($existingShowcase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread này đã có trong showcase.'
                ], 422);
            }

            // Thêm thread vào showcase
            $showcase = new Showcase();
            $showcase->showcaseable_id = $thread->id;
            $showcase->showcaseable_type = Thread::class;
            $showcase->user_id = $userId;
            $showcase->description = "Showcase test for thread: {$thread->title}";
            $showcase->order = 1;
            $showcase->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm thread vào showcase thành công.',
                'data' => [
                    'thread_id' => $thread->id,
                    'thread_title' => $thread->title,
                    'showcase_id' => $showcase->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thêm vào showcase.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
