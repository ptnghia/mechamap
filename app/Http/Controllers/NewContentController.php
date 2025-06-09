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
                $threads = $threadsQuery->orderBy('view_count', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;

            case 'showcase':
                // Get showcase forum IDs (forums with names containing 'showcase')
                $showcaseForumIds = Forum::where('name', 'like', '%showcase%')
                    ->orWhere('description', 'like', '%showcase%')
                    ->pluck('id')
                    ->toArray();

                $threads = $threadsQuery->whereHas('forum', function ($query) use ($showcaseForumIds) {
                    $query->whereIn('id', $showcaseForumIds);
                })
                    ->latest()
                    ->paginate(20);
                break;

            case 'gallery':
                // Get gallery forum IDs (forums with names containing 'gallery')
                $galleryForumIds = Forum::where('name', 'like', '%gallery%')
                    ->orWhere('description', 'like', '%gallery%')
                    ->pluck('id')
                    ->toArray();

                $threads = $threadsQuery->whereHas('forum', function ($query) use ($galleryForumIds) {
                    $query->whereIn('id', $galleryForumIds);
                })
                    ->latest()
                    ->paginate(20);
                break;

            case 'articles':
                // Get news forum IDs (forums with names containing 'news' or 'article')
                $newsForumIds = Forum::where('name', 'like', '%news%')
                    ->orWhere('name', 'like', '%article%')
                    ->orWhere('description', 'like', '%news%')
                    ->pluck('id')
                    ->toArray();

                $threads = $threadsQuery->whereHas('forum', function ($query) use ($newsForumIds) {
                    $query->whereIn('id', $newsForumIds);
                })
                    ->latest()
                    ->paginate(20);
                break;

            case 'replies':
                // Order by number of comments
                $threads = $threadsQuery->withCount('allComments as comments_count')
                    ->orderBy('comments_count', 'desc')
                    ->orderBy('view_count', 'desc')
                    ->paginate(20);
                break;

            default: // 'posts' or any other value
                $threads = $threadsQuery->latest()
                    ->paginate(20);
                break;
        }

        // Get the newest comments (for backward compatibility)
        $comments = Comment::with(['user', 'thread.forum'])
            ->whereHas('thread.forum') // Ensure thread and forum exist
            ->latest()
            ->paginate(20);

        return view('new-content.whats-new', compact('threads', 'comments', 'type'));
    }
}
