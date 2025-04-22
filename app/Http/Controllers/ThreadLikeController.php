<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadLikeController extends Controller
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
     * Toggle like for the specified thread.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function toggle(Request $request, Thread $thread)
    {
        $user = Auth::user();

        $like = ThreadLike::where('thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
            $message = 'Đã bỏ thích bài viết.';
        } else {
            ThreadLike::create([
                'thread_id' => $thread->id,
                'user_id' => $user->id
            ]);
            $message = 'Đã thích bài viết.';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'like_count' => $thread->likes()->count()
            ]);
        }

        return back()->with('success', $message);
    }
}
