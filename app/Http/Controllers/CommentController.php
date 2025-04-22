<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = new Comment([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id
        ]);

        $thread->comments()->save($comment);

        // Update participant count
        $participantCount = $thread->comments()->select('user_id')->distinct()->count();
        $thread->update(['participant_count' => $participantCount]);

        return back()->with('success', 'Bình luận đã được đăng thành công.');
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
            'content' => 'required|string'
        ]);

        $comment->update([
            'content' => $request->content
        ]);

        return back()->with('success', 'Bình luận đã được cập nhật.');
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

        $thread = $comment->thread;
        $comment->delete();

        // Update participant count
        $participantCount = $thread->comments()->select('user_id')->distinct()->count();
        $thread->update(['participant_count' => $participantCount]);

        return back()->with('success', 'Bình luận đã được xóa.');
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
