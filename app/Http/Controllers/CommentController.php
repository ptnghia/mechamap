<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\CommentLike;
use App\Models\Media;
use App\Services\UnifiedNotificationService;
use App\Services\UserActivityService;
use App\Services\UnifiedUploadService;
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
     * The unified upload service instance.
     */
    protected UnifiedUploadService $uploadService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserActivityService $activityService, UnifiedUploadService $uploadService)
    {
        $this->middleware('auth');
        $this->activityService = $activityService;
        $this->uploadService = $uploadService;
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
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
            'uploaded_images' => 'nullable|array|max:10',
            'uploaded_images.*' => 'nullable|string|url',
            'edit_mode' => 'nullable|boolean',
            'comment_id' => 'nullable|exists:comments,id',
        ]);

        // Check if this is edit mode
        if ($request->edit_mode && $request->comment_id) {
            return $this->updateComment($request, $request->comment_id);
        }

        // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($request, $thread) {
            $hasMedia = $request->hasFile('images') || $request->has('uploaded_images');

            $comment = new Comment([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'parent_id' => $request->parent_id,
                'has_media' => $hasMedia
            ]);

            $thread->comments()->save($comment);

            // Xử lý upload hình ảnh nếu có
            if ($request->hasFile('images')) {
                $uploadedFiles = $this->uploadService->uploadMultipleFiles(
                    $request->file('images'),
                    Auth::user(),
                    'comment_images',
                    [
                        'mediable_type' => Comment::class,
                        'mediable_id' => $comment->id,
                        'is_public' => true,
                        'is_approved' => true,
                    ]
                );

                // Log successful uploads
                if (!empty($uploadedFiles)) {
                    \Log::info('Comment images uploaded successfully', [
                        'comment_id' => $comment->id,
                        'files_count' => count($uploadedFiles),
                        'user_id' => Auth::id()
                    ]);
                }
            }

            // Xử lý uploaded_images (pre-uploaded URLs)
            if ($request->has('uploaded_images') && is_array($request->uploaded_images)) {
                foreach ($request->uploaded_images as $imageUrl) {
                    if (!empty($imageUrl)) {
                        $this->uploadService->createMediaFromUrl(
                            $imageUrl,
                            Auth::user(),
                            $comment,
                            'comments'
                        );
                    }
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
                // Load comment with full user info and attachments
                $comment->load(['user', 'attachments']);

                // Add additional user info using unified avatar mechanism
                $comment->user->comments_count = $comment->user->comments()->count();
                $comment->user->avatar_url = $comment->user->getAvatarUrl();
                $comment->user->created_at_formatted = $comment->user->created_at->format('M Y');
                $comment->user->username = $comment->user->username ?? $comment->user->id;

                // Add likes count
                $comment->likes_count = $comment->likes()->count();

                // Format attachments for frontend
                if ($comment->attachments) {
                    $comment->attachments = $comment->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'name' => $attachment->name,
                            'url' => $attachment->getUrl(),
                            'mime_type' => $attachment->mime_type,
                        ];
                    });
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Bình luận đã được đăng thành công.',
                    'comment' => $comment,
                    'comment_count' => $thread->comments()->count(),
                    'redirect' => route('threads.show', $thread) . '#comment-' . $comment->id
                ]);
            }

            return back()
                ->with('success', 'Phản hồi đã được đăng thành công!')
                ->with('scroll_to_comment', $comment->id);
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
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
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
                $uploadedFiles = $this->uploadService->uploadMultipleFiles(
                    $request->file('images'),
                    Auth::user(),
                    'comment_images',
                    [
                        'mediable_type' => Comment::class,
                        'mediable_id' => $comment->id,
                        'is_public' => true,
                        'is_approved' => true,
                    ]
                );

                // Log successful uploads
                if (!empty($uploadedFiles)) {
                    \Log::info('Comment images updated successfully', [
                        'comment_id' => $comment->id,
                        'files_count' => count($uploadedFiles),
                        'user_id' => Auth::id()
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
                    $this->uploadService->deleteFile($attachment);
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

    /**
     * Delete a specific image from a comment
     *
     * @param  \App\Models\Comment  $comment
     * @param  int  $mediaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage(Comment $comment, $mediaId)
    {
        // Check if user can edit this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa hình ảnh này.'
            ], 403);
        }

        // Find media that belongs to this comment
        $media = $comment->attachments()->where('id', $mediaId)->first();

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hình ảnh này.'
            ], 404);
        }

        try {
            // Delete physical file if path exists
            if ($media->file_path && Storage::exists($media->file_path)) {
                Storage::delete($media->file_path);
            }

            // Delete database record
            $media->delete();

            // Update has_media flag if no more images
            $hasMedia = $comment->attachments()->where('file_category', 'image')->exists();
            $comment->update(['has_media' => $hasMedia]);

            \Log::info('Comment image deleted successfully', [
                'comment_id' => $comment->id,
                'media_id' => $mediaId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa hình ảnh thành công.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting comment image', [
                'comment_id' => $comment->id,
                'media_id' => $mediaId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa hình ảnh.'
            ], 500);
        }
    }

    /**
     * Update comment via store method (for edit mode)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $commentId
     * @return \Illuminate\Http\Response
     */
    private function updateComment(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Check if user can edit this comment
        if ($comment->user_id !== Auth::id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa bình luận này.'
                ], 403);
            }
            return back()->with('error', 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        return DB::transaction(function () use ($request, $comment) {
            // Update comment content
            $comment->update([
                'content' => $request->content,
                'updated_at' => now()
            ]);

            // Handle deleted images
            if ($request->filled('deleted_images')) {
                $deletedImageIds = explode(',', $request->deleted_images);
                $deletedImageIds = array_filter($deletedImageIds); // Remove empty values

                if (!empty($deletedImageIds)) {
                    // Delete media records and files
                    $mediaToDelete = $comment->attachments()->whereIn('id', $deletedImageIds)->get();

                    foreach ($mediaToDelete as $media) {
                        // Delete physical file
                        if (Storage::exists($media->path)) {
                            Storage::delete($media->path);
                        }

                        // Delete database record
                        $media->delete();
                    }

                    \Log::info('Comment images deleted', [
                        'comment_id' => $comment->id,
                        'deleted_count' => count($mediaToDelete),
                        'user_id' => Auth::id()
                    ]);
                }
            }

            // Handle new image uploads if any
            if ($request->hasFile('images')) {
                $uploadedFiles = $this->uploadService->uploadMultipleFiles(
                    $request->file('images'),
                    Auth::user(),
                    'comment_images',
                    [
                        'mediable_type' => Comment::class,
                        'mediable_id' => $comment->id,
                        'is_public' => true,
                        'is_approved' => true,
                    ]
                );

                // Log successful uploads
                if (!empty($uploadedFiles)) {
                    \Log::info('Comment images updated successfully', [
                        'comment_id' => $comment->id,
                        'files_count' => count($uploadedFiles),
                        'user_id' => Auth::id()
                    ]);
                }
            }

            // Update has_media flag based on remaining media
            $hasMedia = $comment->attachments()->where('type', 'image')->exists();
            $comment->update(['has_media' => $hasMedia]);

            // Fire real-time event for comment update
            event(new \App\Events\CommentUpdated($comment));

            // Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bình luận đã được cập nhật thành công.',
                    'comment' => $comment->load(['user', 'media']),
                    'redirect' => route('threads.show', $comment->thread) . '#comment-' . $comment->id
                ]);
            }

            return back()
                ->with('success', 'Bình luận đã được cập nhật thành công!')
                ->with('scroll_to_comment', $comment->id);
        });
    }

    /**
     * Get images for a comment (for edit mode)
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImages(Comment $comment)
    {
        // Check if user can edit this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $images = $comment->media()->where('type', 'image')->get()->map(function ($media) {
            return [
                'id' => $media->id,
                'filename' => $media->filename,
                'url' => Storage::url($media->path),
                'size' => $media->size ?? 0
            ];
        });

        return response()->json([
            'success' => true,
            'images' => $images
        ]);
    }
}
