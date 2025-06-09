<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\ThreadBookmark;
use App\Models\ThreadFollow;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function testThreadActions()
    {
        return view('test-thread-actions');
    }

    public function testBookmark(Request $request, $threadId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $thread = Thread::find($threadId);
        if (!$thread) {
            return response()->json(['error' => 'Thread not found'], 404);
        }

        $user = Auth::user();

        // Check if already bookmarked
        $bookmark = ThreadBookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->first();

        if ($bookmark) {
            // Remove bookmark
            $bookmark->delete();
            $thread->refresh();

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Bookmark đã được xóa',
                'bookmarked' => false,
                'bookmark_count' => $thread->bookmark_count
            ]);
        } else {
            // Add bookmark
            ThreadBookmark::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'folder' => 'default',
                'notes' => 'Test bookmark from web'
            ]);

            $thread->refresh();

            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Thread đã được bookmark',
                'bookmarked' => true,
                'bookmark_count' => $thread->bookmark_count
            ]);
        }
    }

    public function testFollow(Request $request, $threadId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $thread = Thread::find($threadId);
        if (!$thread) {
            return response()->json(['error' => 'Thread not found'], 404);
        }

        $user = Auth::user();

        // Check if already followed
        $follow = ThreadFollow::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->first();

        if ($follow) {
            // Remove follow
            $follow->delete();
            $thread->refresh();

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Đã bỏ theo dõi thread',
                'followed' => false,
                'follow_count' => $thread->follow_count
            ]);
        } else {
            // Add follow
            ThreadFollow::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id
            ]);

            $thread->refresh();

            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Đã theo dõi thread',
                'followed' => true,
                'follow_count' => $thread->follow_count
            ]);
        }
    }
}
