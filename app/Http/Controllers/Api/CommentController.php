<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\CommentLike;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Create a new comment for a thread
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $slug)
    {
        try {
            // Validate request
            $request->validate([
                'content' => 'required|string',
            ]);

            // Find thread
            $thread = Thread::where('slug', $slug)->firstOrFail();

            // Create comment
            $comment = Comment::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            // Update thread's last_comment_at
            $thread->last_comment_at = now();
            $thread->comments_count = $thread->comments()->count();
            $thread->save();

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'comment_create',
                'subject_id' => $comment->id,
                'subject_type' => Comment::class,
            ]);

            // Load relationships
            $comment->load(['user']);

            // Add user avatar URL
            if ($comment->user) {
                $comment->user->avatar_url = $comment->user->getAvatarUrl();
            }

            // Fire real-time event
            event(new \App\Events\CommentCreated($comment));

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Tạo bình luận mới thành công.'
            ], 201);
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
                'message' => 'Đã xảy ra lỗi khi tạo bình luận mới.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a comment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if user is authorized to update this comment
            if (Auth::id() !== $comment->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật bình luận này.'
                ], 403);
            }

            // Validate request
            $request->validate([
                'content' => 'required|string',
            ]);

            // Update comment
            $comment->content = $request->content;
            $comment->save();

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'comment_update',
                'subject_id' => $comment->id,
                'subject_type' => Comment::class,
            ]);

            // Load relationships
            $comment->load(['user']);

            // Add user avatar URL
            if ($comment->user) {
                $comment->user->avatar_url = $comment->user->getAvatarUrl();
            }

            // Fire real-time event
            event(new \App\Events\CommentUpdated($comment));

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Cập nhật bình luận thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận.'
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
                'message' => 'Đã xảy ra lỗi khi cập nhật bình luận.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if user is authorized to delete this comment
            if (Auth::id() !== $comment->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bình luận này.'
                ], 403);
            }

            // Get thread to update counts
            $thread = $comment->thread;

            // Store data for event before deletion
            $commentId = $comment->id;
            $threadId = $comment->thread_id;
            $userId = $comment->user_id;
            $userName = $comment->user->name;

            // Delete comment
            $comment->delete();

            // Update thread's comments_count
            if ($thread) {
                $thread->comments_count = $thread->comments()->count();
                $thread->save();
            }

            // Fire real-time event
            event(new \App\Events\CommentDeleted($commentId, $threadId, $userId, $userName));

            return response()->json([
                'success' => true,
                'message' => 'Xóa bình luận thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa bình luận.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Like a comment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if already liked
            if ($comment->isLikedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã thích bình luận này rồi.'
                ], 400);
            }

            // Create like
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => Auth::id(),
            ]);

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'comment_like',
                'subject_id' => $comment->id,
                'subject_type' => Comment::class,
            ]);

            // Update likes count
            $comment->likes_count = $comment->likes()->count();
            $comment->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'likes_count' => $comment->likes_count,
                ],
                'message' => 'Thích bình luận thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thích bình luận.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlike a comment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if not liked
            if (!$comment->isLikedBy(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa thích bình luận này.'
                ], 400);
            }

            // Delete like
            CommentLike::where('comment_id', $comment->id)
                ->where('user_id', Auth::id())
                ->delete();

            // Update likes count
            $comment->likes_count = $comment->likes()->count();
            $comment->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'likes_count' => $comment->likes_count,
                ],
                'message' => 'Bỏ thích bình luận thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi bỏ thích bình luận.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get replies for a comment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReplies(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            $query = $comment->replies();

            // Sort by
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $perPage = $request->input('per_page', 15);
            $replies = $query->with(['user'])->paginate($perPage);

            // Include additional information
            $replies->getCollection()->transform(function ($reply) {
                // Add user avatar URL
                if ($reply->user) {
                    $reply->user->avatar_url = $reply->user->getAvatarUrl();
                }

                // Add like status for authenticated user
                if (Auth::check()) {
                    $reply->is_liked = $reply->isLikedBy(Auth::user());
                }

                return $reply;
            });

            return response()->json([
                'success' => true,
                'data' => $replies,
                'message' => 'Lấy danh sách trả lời của bình luận thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách trả lời của bình luận.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a reply for a comment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeReply(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'content' => 'required|string',
            ]);

            // Find parent comment
            $parentComment = Comment::findOrFail($id);

            // Create reply
            $reply = Comment::create([
                'thread_id' => $parentComment->thread_id,
                'user_id' => Auth::id(),
                'parent_id' => $parentComment->id,
                'content' => $request->content,
            ]);

            // Update thread's last_comment_at
            $thread = $parentComment->thread;
            if ($thread) {
                $thread->last_comment_at = now();
                $thread->comments_count = $thread->comments()->count();
                $thread->save();
            }

            // Create user activity
            UserActivity::create([
                'user_id' => Auth::id(),
                'type' => 'comment_reply',
                'subject_id' => $reply->id,
                'subject_type' => Comment::class,
            ]);

            // Load relationships
            $reply->load(['user']);

            // Add user avatar URL
            if ($reply->user) {
                $reply->user->avatar_url = $reply->user->getAvatarUrl();
            }

            return response()->json([
                'success' => true,
                'data' => $reply,
                'message' => 'Tạo trả lời cho bình luận thành công.'
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bình luận.'
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
                'message' => 'Đã xảy ra lỗi khi tạo trả lời cho bình luận.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent comments
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecent(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);

            // Get recent comments with their threads, ordered by creation date
            $comments = Comment::with(['user', 'thread.category', 'thread.forum', 'thread.user'])
                ->whereHas('thread', function ($query) {
                    $query->where('is_locked', false);
                })
                ->whereNull('parent_id') // Only get top-level comments, not replies
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Add user avatar URL and format data
            $comments->getCollection()->transform(function ($comment) {
                // Add user avatar URL
                if ($comment->user) {
                    $comment->user->avatar_url = $comment->user->getAvatarUrl();
                }

                // Add like status for authenticated user
                if (Auth::check()) {
                    $comment->is_liked = $comment->isLikedBy(Auth::user());
                }

                return $comment;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'comments' => $comments
                ],
                'message' => 'Lấy danh sách bình luận mới nhất thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bình luận mới nhất.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
