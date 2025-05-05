<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Get all tags
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get pagination parameters
            $perPage = $request->input('per_page', 50);

            // Get tags
            $tags = Tag::withCount('threads')
                ->orderBy('threads_count', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $tags,
                'message' => 'Lấy danh sách tags thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách tags.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a tag by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $tag = Tag::where('slug', $slug)
                ->withCount('threads')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $tag,
                'message' => 'Lấy thông tin tag thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin tag.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get threads by tag
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThreads(Request $request, $slug)
    {
        try {
            $tag = Tag::where('slug', $slug)->firstOrFail();

            // Get pagination parameters
            $perPage = $request->input('per_page', 15);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Get threads
            $threads = $tag->threads()
                ->with(['user', 'forum', 'category'])
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);

            // Add user avatar URL
            $threads->getCollection()->transform(function ($thread) {
                if ($thread->user) {
                    $thread->user->avatar_url = $thread->user->getAvatarUrl();
                }

                // Add like/save/follow status for authenticated user
                if (Auth::check()) {
                    $thread->is_liked = $thread->isLikedBy(Auth::user());
                    $thread->is_saved = $thread->isSavedBy(Auth::user());
                    $thread->is_followed = $thread->isFollowedBy(Auth::user());
                }

                return $thread;
            });

            return response()->json([
                'success' => true,
                'data' => $threads,
                'message' => 'Lấy danh sách chủ đề theo tag thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách chủ đề theo tag.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new tag (admin only)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo tag mới.'
                ], 403);
            }

            // Validate request
            $request->validate([
                'name' => 'required|string|max:50|unique:tags,name',
                'description' => 'nullable|string|max:255',
            ]);

            // Create tag
            $tag = Tag::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'data' => $tag,
                'message' => 'Tạo tag mới thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo tag mới.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
