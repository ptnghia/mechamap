<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
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

        // Get the newest posts
        $posts = Post::with(['user', 'thread.forum'])
            ->whereHas('thread.forum') // Ensure thread and forum exist
            ->latest()
            ->take(10)
            ->get();

        return view('new-content.index', compact('threads', 'posts'));
    }

    /**
     * Display what's new page.
     */
    public function whatsNew(): View
    {
        // Get the newest threads
        $threads = Thread::with(['user', 'forum'])
            ->whereHas('forum') // Ensure forum exists
            ->latest()
            ->paginate(20);

        // Get the newest posts
        $posts = Post::with(['user', 'thread.forum'])
            ->whereHas('thread.forum') // Ensure thread and forum exist
            ->latest()
            ->paginate(20);

        return view('new-content.whats-new', compact('threads', 'posts'));
    }
}
