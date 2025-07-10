<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadFollow;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    /**
     * Follow a thread (AJAX)
     */
    public function follow(Request $request, Thread $thread): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để theo dõi thread này.'
                ], 401);
            }

            // Check if already following
            $existingFollow = ThreadFollow::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if ($existingFollow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã theo dõi thread này rồi.',
                    'is_following' => true,
                    'follower_count' => $thread->followers()->count()
                ]);
            }

            // Create follow relationship
            ThreadFollow::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
            ]);

            $followerCount = $thread->followers()->count();

            return response()->json([
                'success' => true,
                'message' => 'Đã theo dõi thread thành công. Bạn sẽ nhận thông báo khi có reply mới.',
                'is_following' => true,
                'follower_count' => $followerCount
            ]);

        } catch (\Exception $e) {
            Log::error('Thread follow failed', [
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi theo dõi thread.'
            ], 500);
        }
    }

    /**
     * Unfollow a thread (AJAX)
     */
    public function unfollow(Request $request, Thread $thread): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để bỏ theo dõi thread này.'
                ], 401);
            }

            $follow = ThreadFollow::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->first();

            if (!$follow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa theo dõi thread này.',
                    'is_following' => false,
                    'follower_count' => $thread->followers()->count()
                ]);
            }

            $follow->delete();
            $followerCount = $thread->followers()->count();

            return response()->json([
                'success' => true,
                'message' => 'Đã bỏ theo dõi thread thành công.',
                'is_following' => false,
                'follower_count' => $followerCount
            ]);

        } catch (\Exception $e) {
            Log::error('Thread unfollow failed', [
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi bỏ theo dõi thread.'
            ], 500);
        }
    }

    /**
     * Get follow status for a thread
     */
    public function status(Request $request, Thread $thread): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'is_following' => false,
                    'follower_count' => $thread->followers()->count()
                ]);
            }

            $isFollowing = ThreadFollow::where('user_id', $user->id)
                ->where('thread_id', $thread->id)
                ->exists();

            return response()->json([
                'success' => true,
                'is_following' => $isFollowing,
                'follower_count' => $thread->followers()->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái theo dõi.'
            ], 500);
        }
    }
}
