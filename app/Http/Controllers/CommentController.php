<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\CommentLike;
use App\Models\Media;
use App\Services\AlertService;
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
     * The alert service instance.
     */
    protected AlertService $alertService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserActivityService $activityService, AlertService $alertService)
    {
        $this->middleware('auth');
        $this->activityService = $activityService;
        $this->alertService = $alertService;
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
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
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

            // Update participant count
            $participantCount = $thread->comments()->select('user_id')->distinct()->count();
            $thread->update(['participant_count' => $participantCount]);

            // Log activity
            $this->activityService->logCommentCreated(Auth::user(), $comment);

            // Tạo thông báo cho người theo dõi thread và chủ thread
            $this->alertService->createCommentAlert(Auth::user(), $thread, $comment);

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
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
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

            // Xóa các file đính kèm nếu có
            if ($comment->has_media) {
                foreach ($comment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            $comment->delete();

            // Update participant count
            $participantCount = $thread->comments()->select('user_id')->distinct()->count();
            $thread->update(['participant_count' => $participantCount]);

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

        if ($like) {
            $like->delete();
            $comment->decrement('like_count');
            $message = 'Đã bỏ thích bình luận.';
        } else {
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $user->id
            ]);
            $comment->increment('like_count');
            $message = 'Đã thích bình luận.';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'like_count' => $comment->like_count
            ]);
        }

        return back()->with('success', $message);
    }
}
