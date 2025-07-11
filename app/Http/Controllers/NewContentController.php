<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Comment;
use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewContentController extends Controller
{
    /**
     * Display the newest content.
     */
    public function index(): View
    {
        // Get the newest threads
        $threads = Thread::with(['user', 'forum'])
            ->whereHas('forum') // Ensure forum exists
            ->latest()
            ->take(10)
            ->get();

        // Get the newest comments
        $comments = Comment::with(['user', 'thread.forum'])
            ->whereHas('thread.forum') // Ensure thread and forum exist
            ->latest()
            ->take(10)
            ->get();

        return view('new-content.index', compact('threads', 'comments'));
    }

    // REMOVED: whatsNew method - no longer used after route cleanup
}
