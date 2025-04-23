<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadFollow;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadFollowController extends Controller
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
        $this->activityService = $activityService;
    }
    
    /**
     * Toggle follow for the specified thread.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function toggle(Request $request, Thread $thread)
    {
        $user = Auth::user();

        $follow = ThreadFollow::where('thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();
            $message = 'Đã bỏ theo dõi chủ đề.';
        } else {
            ThreadFollow::create([
                'thread_id' => $thread->id,
                'user_id' => $user->id
            ]);

            // Log activity
            $this->activityService->logThreadFollowed($user, $thread);

            $message = 'Đã theo dõi chủ đề.';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'follow_count' => $thread->follows()->count()
            ]);
        }

        return back()->with('success', $message);
    }
}
