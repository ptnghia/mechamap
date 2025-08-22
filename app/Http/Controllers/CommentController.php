<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\CommentLike;
use App\Models\Media;
use App\Services\UnifiedNotificationService;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * The user activity service instance.
     */
    protected UserActivityService $activityService;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserActivityService $activityService)
    {
        $this->middleware('auth');
        $this->activityService = $activityService;
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
        ]);

        // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($request, $thread) {
            $hasMedia = $request->hasFile('images');

            $comment = new Comment([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'parent_id' => $request->parent_id,
                'has_media' => $hasMedia
            ]);

            $thread->comments()->save($comment);

            // Xử lý upload hình ảnh nếu có
            if ($hasMedia) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('comment-images', 'public');
                    $comment->attachments()->create([
                        'user_id' => Auth::id(),
                        'file_path' => $path,
                        'file_name' => $image->getClientOriginalName(),
                        'file_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                    ]);
                }
            }

            // Log activity
            $this->activityService->logCommentCreated(Auth::user(), $comment);

            // Send thread replied notification
            \App\Services\NotificationService::sendThreadRepliedNotification($comment);

            // Extract and send mention notifications
            $mentions = \App\Services\NotificationService::extractMentions($comment->content);
            if (!empty($mentions)) {
                \App\Services\NotificationService::sendCommentMentionNotification($comment, $mentions);
            }

            // Tạo thông báo cho chủ thread
            if ($thread->user_id !== Auth::id()) {
                UnifiedNotificationService::send(
                    $thread->user,
                    'comment_created',
                    'Bình luận mới',
                    Auth::user()->name . ' đã bình luận trong chủ đề: ' . $thread->title,
                    [
                        'thread_id' => $thread->id,
                        'comment_id' => $comment->id,
                        'commenter_name' => Auth::user()->name,
                        'action_url' => route('threads.show', $thread->id) . '#comment-' . $comment->id,
                        'priority' => 'normal',
                        'category' => 'social'
                    ],
                    ['database']
                );
            }

            // Fire real-time event
            event(new \App\Events\CommentCreated($comment));

            // Fire thread stats update event
            $stats = [
                'comments_count' => $thread->comments()->count(),
                'participants_count' => $thread->comments()->distinct('user_id')->count('user_id') + 1, // +1 for thread author
                'last_activity' => now()->toISOString(),
            ];
            event(new \App\Events\ThreadStatsUpdated($thread, $stats));

            // Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bình luận đã được đăng thành công.',
                    'comment' => $comment->load(['user', 'attachments']),
                    'redirect' => route('threads.show', $thread) . '#comment-' . $comment->id
                ]);
            }

            return back()->with('success', 'Bình luận đã được đăng thành công.');
        });
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
        ]);

        // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($request, $comment) {
            $hasMedia = $request->hasFile('images');

            $comment->update([
                'content' => $request->content,
                'has_media' => $comment->has_media || $hasMedia
            ]);

            // Xử lý upload hình ảnh nếu có
            if ($hasMedia) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('comment-images', 'public');
                    $comment->attachments()->create([
                        'user_id' => Auth::id(),
                        'file_path' => $path,
                        'file_name' => $image->getClientOriginalName(),
                        'file_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                    ]);
                }
            }

            // Fire real-time event
            event(new \App\Events\CommentUpdated($comment));

            return back()->with('success', 'Bình luận đã được cập nhật.');
        });
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($comment) {
            $thread = $comment->thread;

            // Store data for event before deletion
            $commentId = $comment->id;
            $threadId = $comment->thread_id;
            $userId = $comment->user_id;
            $userName = $comment->user->name;

            // Xóa các file đính kèm nếu có
            if ($comment->has_media) {
                foreach ($comment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            $comment->delete();

            // Fire real-time event
            event(new \App\Events\CommentDeleted($commentId, $threadId, $userId, $userName));

            // Handle AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bình luận đã được xóa.',
                    'comment_id' => $commentId
                ]);
            }

            return back()->with('success', 'Bình luận đã được xóa.');
        });
    }

    /**
     * Toggle like for the specified comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request, Comment $comment)
    {
        $user = Auth::user();

        $like = CommentLike::where('comment_id', $comment->id)
            ->where('user_id', $user->id)
            ->first();

        $isLiked = false;
        if ($like) {
            $like->delete();
            $comment->decrement('like_count');
            $message = 'Đã bỏ thích bình luận.';
            $isLiked = false;
        } else {
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $user->id
            ]);
            $comment->increment('like_count');
            $message = 'Đã thích bình luận.';
            $isLiked = true;
        }

        // Fire real-time event
        event(new \App\Events\CommentLikeUpdated($comment, $user, $isLiked, $comment->like_count));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'like_count' => $comment->like_count,
                'is_liked' => $isLiked
            ]);
        }

        return back()->with('success', $message);
    }
}
