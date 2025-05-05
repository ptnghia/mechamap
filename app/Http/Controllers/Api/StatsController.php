<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatsController extends Controller
{
    /**
     * Get forum statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForumStats()
    {
        try {
            $threadCount = Thread::count();
            $userCount = User::count();
            $commentCount = Comment::count();
            $establishedYear = config('app.established_year', 2023);

            return response()->json([
                'success' => true,
                'data' => [
                    'threadCount' => $threadCount,
                    'userCount' => $userCount,
                    'commentCount' => $commentCount,
                    'establishedYear' => $establishedYear,
                ],
                'message' => 'Lấy thống kê diễn đàn thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thống kê diễn đàn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular forums
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopularForums(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 5);

            $forums = Forum::withCount('threads')
                ->orderBy('threads_count', 'desc')
                ->take($perPage)
                ->get();

            // Format response for pagination compatibility
            $response = [
                'forums' => [
                    'data' => $forums,
                    'meta' => [
                        'current_page' => 1,
                        'from' => 1,
                        'last_page' => 1,
                        'path' => request()->url(),
                        'per_page' => $perPage,
                        'to' => count($forums),
                        'total' => count($forums),
                    ],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Lấy danh sách diễn đàn phổ biến thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách diễn đàn phổ biến.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveUsers(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 5);

            // Get users with thread and comment counts
            $users = User::select('users.*')
                ->withCount('threads')
                ->withCount('comments')
                ->orderByRaw('threads_count + comments_count DESC')
                ->take($perPage)
                ->get();

            // Add avatar URL and contribution count
            $users->transform(function ($user) {
                $user->avatar_url = $user->getAvatarUrl();
                $user->contribution_count = $user->threads_count + $user->comments_count;
                return $user;
            });

            // Format response for pagination compatibility
            $response = [
                'users' => [
                    'data' => $users,
                    'meta' => [
                        'current_page' => 1,
                        'from' => 1,
                        'last_page' => 1,
                        'path' => request()->url(),
                        'per_page' => $perPage,
                        'to' => count($users),
                        'total' => count($users),
                    ],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Lấy danh sách người dùng tích cực thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách người dùng tích cực.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured threads
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturedThreads(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 4);

            $threads = Thread::with(['user', 'forum', 'category'])
                ->where(function ($query) {
                    $query->where('is_featured', true)
                        ->orWhere('is_sticky', true)
                        ->orWhere('view_count', '>', 100);
                })
                ->orderBy('created_at', 'desc')
                ->take($perPage)
                ->get();

            // Add user avatar URL and extract first image from content
            $threads->transform(function ($thread) {
                if ($thread->user) {
                    $thread->user->avatar_url = $thread->user->getAvatarUrl();
                }

                // Extract first image from content if available
                if (!$thread->thumbnail_url) {
                    preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $thread->content, $image);
                    $thread->thumbnail_url = $image['src'] ?? null;
                }

                // Create excerpt from content
                $thread->excerpt = Str::limit(strip_tags($thread->content), 150);

                return $thread;
            });

            // Format response for pagination compatibility
            $response = [
                'threads' => [
                    'data' => $threads,
                    'meta' => [
                        'current_page' => 1,
                        'from' => 1,
                        'last_page' => 1,
                        'path' => request()->url(),
                        'per_page' => $perPage,
                        'to' => count($threads),
                        'total' => count($threads),
                    ],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Lấy danh sách bài viết nổi bật thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết nổi bật.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
