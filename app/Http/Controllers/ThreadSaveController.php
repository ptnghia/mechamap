<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadSave;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadSaveController extends Controller
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
     * Display a listing of the saved threads.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $savedThreads = ThreadSave::where('user_id', $user->id)
            ->with('thread.user')
            ->latest()
            ->paginate(10);

        return view('threads.saved', compact('savedThreads'));
    }

    /**
     * Toggle save for the specified thread.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function toggle(Request $request, Thread $thread)
    {
        $user = Auth::user();

        $save = ThreadSave::where('thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->first();

        if ($save) {
            $save->delete();
            $message = 'Đã bỏ lưu bài viết.';
            $isSaved = false;
        } else {
            ThreadSave::create([
                'thread_id' => $thread->id,
                'user_id' => $user->id
            ]);

            // Log activity
            $this->activityService->logThreadSaved($user, $thread);

            $message = 'Đã lưu bài viết.';
            $isSaved = true;
        }

        if ($request->ajax()) {
            // Refresh thread to get updated saves count
            $thread->refresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_saved' => $isSaved,
                'saves_count' => $thread->saves_count
            ]);
        }

        return back()->with('success', $message);
    }
}
