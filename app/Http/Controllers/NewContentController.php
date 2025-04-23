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
    public function whatsNew(Request $request): View
    {
        $type = $request->query('type', 'posts');

        // Base query for threads
        $threadsQuery = Thread::with(['user', 'forum'])
            ->whereHas('forum'); // Ensure forum exists

        // Apply filters based on type
        switch ($type) {
            case 'popular':
                $threads = $threadsQuery->orderBy('views', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;

            case 'showcase':
                $threads = $threadsQuery->whereHas('forum', function ($query) {
                    $query->where('type', 'showcase');
                })
                    ->latest()
                    ->paginate(20);
                break;

            case 'gallery':
                $threads = $threadsQuery->whereHas('forum', function ($query) {
                    $query->where('type', 'gallery');
                })
                    ->latest()
                    ->paginate(20);
                break;

            case 'articles':
                $threads = $threadsQuery->whereHas('forum', function ($query) {
                    $query->where('type', 'news');
                })
                    ->latest()
                    ->paginate(20);
                break;

            case 'replies':
                $threads = $threadsQuery->orderBy('posts_count', 'asc')
                    ->orderBy('views', 'desc')
                    ->paginate(20);
                break;

            default: // 'posts' or any other value
                $threads = $threadsQuery->latest()
                    ->paginate(20);
                break;
        }

        // Get the newest posts (for backward compatibility)
        $posts = Post::with(['user', 'thread.forum'])
            ->whereHas('thread.forum') // Ensure thread and forum exist
            ->latest()
            ->paginate(20);

        return view('new-content.whats-new', compact('threads', 'posts'));
    }
}
