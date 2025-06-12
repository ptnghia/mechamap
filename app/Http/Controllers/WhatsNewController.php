<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\Media;
use App\Services\ShowcaseImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsNewController extends Controller
{
    /**
     * Display the "What's New" page with recent posts
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Get recent comments with their threads, ordered by creation date
        $posts = Comment::with(['user', 'thread.category', 'thread.forum', 'thread.user'])
            ->whereHas('thread', function ($query) {
                $query->where('is_locked', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Load comment counts for threads efficiently
        $threadIds = $posts->pluck('thread.id')->unique();
        $commentCounts = Comment::selectRaw('thread_id, COUNT(*) as comment_count')
            ->whereIn('thread_id', $threadIds)
            ->groupBy('thread_id')
            ->pluck('comment_count', 'thread_id');

        // Calculate total pages
        $totalPages = ceil($posts->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new', ['page' => $page + 1])
            : '#';

        // Add thread statistics
        foreach ($posts as $post) {
            // Get comment count from pre-loaded data
            $post->thread->comment_count = $commentCounts->get($post->thread->id, 0);

            // Get page count (assuming 20 comments per page)
            $post->thread->page_count = ceil($post->thread->comment_count / 20);

            // Get latest comment info
            $latestComment = Comment::where('thread_id', $post->thread->id)
                ->orderBy('created_at', 'desc')
                ->with('user')
                ->first();

            if ($latestComment) {
                $post->thread->latest_comment_at = $latestComment->created_at;
                $post->thread->latest_comment_user = $latestComment->user;
            } else {
                $post->thread->latest_comment_at = $post->thread->created_at;
                $post->thread->latest_comment_user = $post->thread->user;
            }
        }

        return view('whats-new.index', compact(
            'posts',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display popular threads
     */
    public function popular(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;
        $timeframe = $request->input('timeframe', 'week'); // day, week, month, year, all

        // Determine date range based on timeframe
        $startDate = now();
        switch ($timeframe) {
            case 'day':
                $startDate = $startDate->subDay();
                break;
            case 'week':
                $startDate = $startDate->subWeek();
                break;
            case 'month':
                $startDate = $startDate->subMonth();
                break;
            case 'year':
                $startDate = $startDate->subYear();
                break;
            case 'all':
                $startDate = null;
                break;
        }

        // Get popular threads based on view count and recent activity
        $query = Thread::with(['user', 'category', 'forum'])
            ->withCount('allComments as comment_count')
            ->where('is_locked', false);

        if ($startDate) {
            $query->where(function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)
                    ->orWhereHas('comments', function ($q2) use ($startDate) {
                        $q2->where('created_at', '>=', $startDate);
                    });
            });
        }

        $threads = $query->orderBy('view_count', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate total pages
        $totalPages = ceil($threads->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.popular', ['page' => $page - 1, 'timeframe' => $timeframe])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.popular', ['page' => $page + 1, 'timeframe' => $timeframe])
            : '#';

        // Add thread statistics
        foreach ($threads as $thread) {
            // Comment count is already loaded via withCount
            // Get page count (assuming 20 comments per page)
            $thread->page_count = ceil($thread->comment_count / 20);

            // Get latest comment info
            $latestComment = Comment::where('thread_id', $thread->id)
                ->orderBy('created_at', 'desc')
                ->with('user')
                ->first();

            if ($latestComment) {
                $thread->latest_comment_at = $latestComment->created_at;
                $thread->latest_comment_user = $latestComment->user;
            } else {
                $thread->latest_comment_at = $thread->created_at;
                $thread->latest_comment_user = $thread->user;
            }
        }

        return view('whats-new.popular', compact(
            'threads',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl',
            'timeframe'
        ));
    }

    /**
     * Display new threads
     */
    public function threads(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Get recent threads
        $threads = Thread::with(['user', 'category', 'forum'])
            ->withCount('allComments as comment_count')
            ->where('is_locked', false)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate total pages
        $totalPages = ceil($threads->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.threads', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.threads', ['page' => $page + 1])
            : '#';

        // Add thread statistics
        foreach ($threads as $thread) {
            // Comment count is already loaded via withCount
            // Get page count (assuming 20 comments per page)
            $thread->page_count = ceil($thread->comment_count / 20);

            // Get latest comment info
            $latestComment = Comment::where('thread_id', $thread->id)
                ->orderBy('created_at', 'desc')
                ->with('user')
                ->first();

            if ($latestComment) {
                $thread->latest_comment_at = $latestComment->created_at;
                $thread->latest_comment_user = $latestComment->user;
            } else {
                $thread->latest_comment_at = $thread->created_at;
                $thread->latest_comment_user = $thread->user;
            }
        }

        return view('whats-new.threads', compact(
            'threads',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display new media
     */
    public function media(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Get recent media
        $mediaItems = Media::with(['user', 'thread.category', 'thread.forum'])
            ->whereNotNull('thread_id')
            ->whereHas('thread', function ($query) {
                $query->where('is_locked', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate total pages
        $totalPages = ceil($mediaItems->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.media', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.media', ['page' => $page + 1])
            : '#';

        return view('whats-new.media', compact(
            'mediaItems',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display threads looking for replies
     */
    public function replies(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Get threads with no replies or few replies, ordered by creation date
        $threads = Thread::with(['user', 'category', 'forum'])
            ->withCount('allComments as comment_count')
            ->where('is_locked', false)
            ->whereDoesntHave('comments')
            ->orWhereHas('comments', function ($query) {
                $query->havingRaw('COUNT(*) < 5');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate total pages
        $totalPages = ceil($threads->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.replies', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.replies', ['page' => $page + 1])
            : '#';

        // Add thread statistics
        foreach ($threads as $thread) {
            // Comment count is already loaded via withCount
            // Get page count (assuming 20 comments per page)
            $thread->page_count = ceil($thread->comment_count / 20);

            // Get latest comment info
            $latestComment = Comment::where('thread_id', $thread->id)
                ->orderBy('created_at', 'desc')
                ->with('user')
                ->first();

            if ($latestComment) {
                $thread->latest_comment_at = $latestComment->created_at;
                $thread->latest_comment_user = $latestComment->user;
            } else {
                $thread->latest_comment_at = $thread->created_at;
                $thread->latest_comment_user = $thread->user;
            }
        }

        return view('whats-new.replies', compact(
            'threads',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display new showcases
     */
    public function showcases(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Get recent showcases
        $showcases = \App\Models\Showcase::with(['user', 'showcaseable', 'media'])
            ->whereHas('showcaseable') // Ensure showcaseable exists
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate total pages
        $totalPages = ceil($showcases->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.showcases', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.showcases', ['page' => $page + 1])
            : '#';

        // Process featured images using unified service
        ShowcaseImageService::processFeaturedImages($showcases->getCollection());

        // Add additional data for each showcase
        foreach ($showcases as $showcase) {
            // Get showcase type and title
            if ($showcase->showcaseable_type === 'App\Models\Thread') {
                $showcase->showcase_type = 'Thread';
                $showcase->showcase_title = $showcase->showcaseable->title ?? 'Unknown Thread';
                $showcase->showcase_url = route('threads.show', $showcase->showcaseable);
            } elseif ($showcase->showcaseable_type === 'App\Models\Post') {
                $showcase->showcase_type = 'Post';
                $showcase->showcase_title = 'Reply in: ' . ($showcase->showcaseable->thread->title ?? 'Unknown Thread');
                $showcase->showcase_url = route('threads.show', $showcase->showcaseable->thread) . '#post-' . $showcase->showcaseable->id;
            } else {
                $showcase->showcase_type = 'Content';
                $showcase->showcase_title = $showcase->title ?? 'Showcase Item';
                $showcase->showcase_url = route('showcase.show', $showcase);
            }

            // Content preview
            if ($showcase->showcaseable) {
                $content = $showcase->showcaseable->content ?? $showcase->showcaseable->description ?? '';
                $showcase->content_preview = \Illuminate\Support\Str::limit(strip_tags($content), 150);
            } else {
                $showcase->content_preview = $showcase->description ?? '';
            }
        }

        return view('whats-new.showcases', compact(
            'showcases',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }
}
