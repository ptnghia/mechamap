<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Forum;
use App\Models\ThreadLike;
use App\Models\ThreadSave;
use App\Models\ThreadFollow;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ThreadController extends Controller
{
    /**
     * Get a list of threads
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Thread::query();

            // Filter by forum
            if ($request->has('forum_id')) {
                $query->where('forum_id', $request->forum_id);
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                // Only show approved threads by default
                $query->where('status', 'approved');
            }

            // Filter by sticky
            if ($request->has('is_sticky')) {
                $query->where('is_sticky', $request->boolean('is_sticky'));
            }

            // Filter by locked
            if ($request->has('is_locked')) {
                $query->where('is_locked', $request->boolean('is_locked'));
            }

            // Filter by featured
            if ($request->has('is_featured')) {
                $query->where('is_featured', $request->boolean('is_featured'));
            }

            // Search by title or content
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Special case for "activity" sort
            if ($sortBy === 'activity') {
                $query->orderByRaw('COALESCE(last_comment_at, created_at) ' . $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Include sticky threads at the top if sorting by activity or created_at
            if (in_array($sortBy, ['activity', 'created_at']) && $sortOrder === 'desc') {
                $query->orderBy('is_sticky', 'desc');
            }

            // Paginate
            $perPage = $request->input('per_page', 15);
            $threads = $query->with(['user', 'forum'])->paginate($perPage);

            // Include additional information
            $threads->getCollection()->transform(function ($thread) {
                // Add user avatar URL
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
                'message' => 'Lấy danh sách chủ đề thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a thread by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Load relationships
            $thread->load(['user', 'forum', 'category']);

            // Add user avatar URL
            if ($thread->user) {
                $thread->user->avatar_url = $thread->user->getAvatarUrl();
            }

            // Add like/save/follow status for authenticated user
            if (Auth::check()) {
                $thread->is_liked = $thread->isLikedBy(Auth::user());
                $thread->is_saved = $thread->isSavedBy(Auth::user());
                $thread->is_followed = $thread->isFollowedBy(Auth::user());
            }

            // Increment view count
            $thread->incrementViewCount();

            return response()->json([
                'success' => true,
                'data' => $thread,
                'message' => 'Lấy thông tin chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new thread
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'forum_id' => 'required|exists:forums,id',
                'category_id' => 'nullable|exists:categories,id',
                'location' => 'nullable|string|max:255',
                'usage' => 'nullable|string|max:255',
                'floors' => 'nullable|integer|min:1',
            ]);

            // Check if forum exists
            $forum = Forum::findOrFail($request->forum_id);

            // Create slug from title
            $slug = Str::slug($request->title);

            // Check if slug already exists
            $count = Thread::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            // Create thread
            $thread = Thread::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'user_id' => Auth::id(),
                'forum_id' => $request->forum_id,
                'category_id' => $request->category_id,
                'location' => $request->location,
                'usage' => $request->usage,
                'floors' => $request->floors,
                'status' => 'approved', // Auto-approve for now
            ]);

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'thread_create',
                'subject_id' => $thread->id,
                'subject_type' => Thread::class,
            ]);

            // Load relationships
            $thread->load(['user', 'forum', 'category']);

            // Add user avatar URL
            if ($thread->user) {
                $thread->user->avatar_url = $thread->user->getAvatarUrl();
            }

            return response()->json([
                'success' => true,
                'data' => $thread,
                'message' => 'Tạo chủ đề mới thành công.'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo chủ đề mới.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a thread
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if user is authorized to update this thread
            if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật chủ đề này.'
                ], 403);
            }

            // Validate request
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'forum_id' => 'sometimes|required|exists:forums,id',
                'category_id' => 'nullable|exists:categories,id',
                'location' => 'nullable|string|max:255',
                'usage' => 'nullable|string|max:255',
                'floors' => 'nullable|integer|min:1',
            ]);

            // Update thread
            $thread->fill($request->only([
                'title',
                'content',
                'forum_id',
                'category_id',
                'location',
                'usage',
                'floors',
            ]));

            // Update slug if title changed
            if ($request->has('title') && $thread->isDirty('title')) {
                $slug = Str::slug($request->title);

                // Check if slug already exists
                $count = Thread::where('slug', $slug)->where('id', '!=', $thread->id)->count();
                if ($count > 0) {
                    $slug = $slug . '-' . ($count + 1);
                }

                $thread->slug = $slug;
            }

            $thread->save();

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'thread_update',
                'subject_id' => $thread->id,
                'subject_type' => Thread::class,
            ]);

            // Load relationships
            $thread->load(['user', 'forum', 'category']);

            // Add user avatar URL
            if ($thread->user) {
                $thread->user->avatar_url = $thread->user->getAvatarUrl();
            }

            return response()->json([
                'success' => true,
                'data' => $thread,
                'message' => 'Cập nhật chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if user is authorized to delete this thread
            if (Auth::id() !== $thread->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa chủ đề này.'
                ], 403);
            }

            // Delete thread
            $thread->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for a thread
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments(Request $request, $slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            $query = $thread->comments();

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->input('per_page', 15);
            $comments = $query->with(['user', 'thread'])->paginate($perPage);

            // Include additional information
            $comments->getCollection()->transform(function ($comment) {
                // Add user avatar URL
                if ($comment->user) {
                    $comment->user->avatar_url = $comment->user->getAvatarUrl();
                }

                // Add like status for authenticated user
                if (Auth::check()) {
                    $comment->is_liked = $comment->isLikedBy(Auth::user());
                }

                // Get reply count
                $comment->replies_count = $comment->replies()->count();

                return $comment;
            });

            return response()->json([
                'success' => true,
                'data' => $comments,
                'message' => 'Lấy danh sách bình luận của chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bình luận của chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Like a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if already liked
            if ($thread->isLikedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã thích chủ đề này rồi.'
                ], 400);
            }

            // Create like
            ThreadLike::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
            ]);

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'thread_like',
                'subject_id' => $thread->id,
                'subject_type' => Thread::class,
            ]);

            // Update likes count
            $thread->likes_count = $thread->likes()->count();
            $thread->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'likes_count' => $thread->likes_count,
                ],
                'message' => 'Thích chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thích chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update all threads to approved status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveAllThreads()
    {
        try {
            // Cập nhật tất cả các bài viết có trạng thái khác "approved"
            $count = Thread::where('status', '!=', 'approved')->update(['status' => 'approved']);

            // Cập nhật tất cả các bài viết có trạng thái NULL
            $nullCount = Thread::whereNull('status')->update(['status' => 'approved']);

            $totalCount = $count + $nullCount;

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái của ' . $totalCount . ' bài viết thành "approved".',
                'data' => [
                    'updated_count' => $totalCount,
                    'non_approved_count' => $count,
                    'null_status_count' => $nullCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái bài viết.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlike a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if not liked
            if (!$thread->isLikedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa thích chủ đề này.'
                ], 400);
            }

            // Delete like
            ThreadLike::where('thread_id', $thread->id)
                ->where('user_id', Auth::id())
                ->delete();

            // Update likes count
            $thread->likes_count = $thread->likes()->count();
            $thread->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'likes_count' => $thread->likes_count,
                ],
                'message' => 'Bỏ thích chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi bỏ thích chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function save($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if already saved
            if ($thread->isSavedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã lưu chủ đề này rồi.'
                ], 400);
            }

            // Create save
            ThreadSave::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
            ]);

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'thread_save',
                'subject_id' => $thread->id,
                'subject_type' => Thread::class,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lưu chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lưu chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unsave a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsave($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if not saved
            if (!$thread->isSavedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa lưu chủ đề này.'
                ], 400);
            }

            // Delete save
            ThreadSave::where('thread_id', $thread->id)
                ->where('user_id', Auth::id())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bỏ lưu chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi bỏ lưu chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Follow a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if already followed
            if ($thread->isFollowedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã theo dõi chủ đề này rồi.'
                ], 400);
            }

            // Create follow
            ThreadFollow::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
            ]);

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'thread_follow',
                'subject_id' => $thread->id,
                'subject_type' => Thread::class,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Theo dõi chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi theo dõi chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unfollow a thread
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow($slug)
    {
        try {
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Check if not followed
            if (!$thread->isFollowedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa theo dõi chủ đề này.'
                ], 400);
            }

            // Delete follow
            ThreadFollow::where('thread_id', $thread->id)
                ->where('user_id', Auth::id())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hủy theo dõi chủ đề thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chủ đề.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi hủy theo dõi chủ đề.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get saved threads for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSaved(Request $request)
    {
        try {
            $query = Auth::user()->savedThreads();

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Special case for "activity" sort
            if ($sortBy === 'activity') {
                $query->orderByRaw('COALESCE(threads.last_comment_at, threads.created_at) ' . $sortOrder);
            } else {
                $query->orderBy('threads.' . $sortBy, $sortOrder);
            }

            // Paginate
            $perPage = $request->input('per_page', 15);
            $threads = $query->with(['user', 'forum'])->paginate($perPage);

            // Include additional information
            $threads->getCollection()->transform(function ($thread) {
                // Add user avatar URL
                if ($thread->user) {
                    $thread->user->avatar_url = $thread->user->getAvatarUrl();
                }

                // Add like/save/follow status
                $thread->is_liked = $thread->isLikedBy(Auth::user());
                $thread->is_saved = true; // Already saved
                $thread->is_followed = $thread->isFollowedBy(Auth::user());

                return $thread;
            });

            return response()->json([
                'success' => true,
                'data' => $threads,
                'message' => 'Lấy danh sách chủ đề đã lưu thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách chủ đề đã lưu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get followed threads for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFollowed(Request $request)
    {
        try {
            $query = Auth::user()->followedThreads();

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Special case for "activity" sort
            if ($sortBy === 'activity') {
                $query->orderByRaw('COALESCE(threads.last_comment_at, threads.created_at) ' . $sortOrder);
            } else {
                $query->orderBy('threads.' . $sortBy, $sortOrder);
            }

            // Paginate
            $perPage = $request->input('per_page', 15);
            $threads = $query->with(['user', 'forum'])->paginate($perPage);

            // Include additional information
            $threads->getCollection()->transform(function ($thread) {
                // Add user avatar URL
                if ($thread->user) {
                    $thread->user->avatar_url = $thread->user->getAvatarUrl();
                }

                // Add like/save/follow status
                $thread->is_liked = $thread->isLikedBy(Auth::user());
                $thread->is_saved = $thread->isSavedBy(Auth::user());
                $thread->is_followed = true; // Already followed

                return $thread;
            });

            return response()->json([
                'success' => true,
                'data' => $threads,
                'message' => 'Lấy danh sách chủ đề đang theo dõi thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách chủ đề đang theo dõi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
