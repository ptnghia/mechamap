<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Poll;
use App\Models\PollOption;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThreadController extends Controller
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
        $this->middleware('auth')->except(['index', 'show']);
        $this->activityService = $activityService;
    }

    /**
     * Display a listing of the threads.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Thread::with('user', 'forum', 'category')
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings']);

        // Apply filters
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('forum')) {
            $query->where('forum_id', $request->forum);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply sorting - ALWAYS put sticky threads first
        $sort = $request->get('sort', 'latest');

        // Primary sort: sticky threads first
        $query->orderBy('is_sticky', 'desc');

        // Secondary sort: based on user selection
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'most_viewed':
                $query->orderBy('view_count', 'desc');
                break;
            case 'most_commented':
                $query->orderBy('comments_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $threads = $query->paginate(15)->withQueryString();
        $categories = Category::all();
        $forums = Forum::all();

        return view('threads.index', compact('threads', 'categories', 'forums', 'sort'));
    }

    /**
     * Show the form for creating a new thread.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Kiểm tra xem có forum_id được truyền vào không
        if (!$request->has('forum_id')) {
            return redirect()->route('forums.select');
        }

        $forum = Forum::findOrFail($request->forum_id);
        $categories = Category::all();
        $forums = Forum::all();

        return view('threads.create', compact('categories', 'forums', 'forum'));
    }

    /**
     * Store a newly created thread in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'forum_id' => 'required|exists:forums,id',
            'status' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
            // Poll validation
            'has_poll' => 'boolean',
            'poll_question' => 'required_if:has_poll,1|string|max:255',
            'poll_options' => 'required_if:has_poll,1|array|min:2',
            'poll_options.*' => 'required_if:has_poll,1|string|max:255',
            'poll_max_options' => 'required_if:has_poll,1|integer|min:1',
            'poll_allow_change_vote' => 'boolean',
            'poll_show_votes_publicly' => 'boolean',
            'poll_allow_view_without_vote' => 'boolean',
            'poll_close_after_days' => 'nullable|integer|min:1',
        ]);

        // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($request) {
            $thread = Thread::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . Str::random(5),
                'content' => $request->content,
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'forum_id' => $request->forum_id,
                'status' => $request->status,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('thread-images', 'public');
                    $thread->media()->create([
                        'user_id' => Auth::id(),
                        'file_name' => $image->getClientOriginalName(),
                        'file_path' => $path,
                        'file_extension' => $image->getClientOriginalExtension(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'file_category' => 'image',
                    ]);
                }
            }

            // Create poll if requested
            if ($request->has('has_poll') && $request->has_poll) {
                $poll = Poll::create([
                    'thread_id' => $thread->id,
                    'question' => $request->poll_question,
                    'max_options' => $request->poll_max_options,
                    'allow_change_vote' => $request->has('poll_allow_change_vote'),
                    'show_votes_publicly' => $request->has('poll_show_votes_publicly'),
                    'allow_view_without_vote' => $request->has('poll_allow_view_without_vote'),
                    'close_at' => $request->poll_close_after_days ? now()->addDays($request->poll_close_after_days) : null,
                ]);

                // Create poll options
                foreach ($request->poll_options as $optionText) {
                    if (!empty($optionText)) {
                        PollOption::create([
                            'poll_id' => $poll->id,
                            'text' => $optionText,
                        ]);
                    }
                }
            }

            // Log activity
            $this->activityService->logThreadCreated(Auth::user(), $thread);

            return redirect()->route('threads.show', $thread)
                ->with('success', 'Bài viết đã được tạo thành công.');
        });
    }

    /**
     * Display the specified thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        // Load relationships and counts
        $thread->load([
            'user',
            'category',
            'forum',
            'media',
            'showcase.user', // Load showcase with its user
            'showcase.media' // Load showcase media for display
        ]);

        $thread->loadCount([
            'likes',
            'saves',
            'follows',
            'comments'
        ]);

        // Increment view count
        $thread->incrementViewCount();

        // Get comments with pagination
        $sort = request('sort', 'oldest');
        $commentsQuery = $thread->comments()->with(['user', 'replies.user']);

        switch ($sort) {
            case 'newest':
                $commentsQuery->latest();
                break;
            case 'reactions':
                $commentsQuery->orderBy('like_count', 'desc');
                break;
            default: // oldest
                $commentsQuery->oldest();
                break;
        }

        $comments = $commentsQuery->paginate(20);

        // Check if user has liked, saved or followed the thread
        $isLiked = Auth::check() ? $thread->isLikedBy(Auth::user()) : false;
        $isSaved = Auth::check() ? $thread->isSavedBy(Auth::user()) : false;
        $isFollowed = Auth::check() ? $thread->isFollowedBy(Auth::user()) : false;

        // Get related threads
        $relatedThreads = Thread::where('category_id', $thread->category_id)
            ->where('id', '!=', $thread->id)
            ->withCount('comments')
            ->take(5)
            ->get();

        // Get last commenter and last comment time
        $lastComment = $thread->comments()->with('user')->latest()->first();
        $thread->lastCommenter = $lastComment ? $lastComment->user : $thread->user;
        $thread->lastCommentAt = $lastComment ? $lastComment->created_at : null;

        return view('threads.show', compact('thread', 'comments', 'isLiked', 'isSaved', 'isFollowed', 'relatedThreads', 'sort'));
    }

    /**
     * Show the form for editing the specified thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        $this->authorize('update', $thread);

        $categories = Category::all();
        $forums = Forum::all();

        return view('threads.edit', compact('thread', 'categories', 'forums'));
    }

    /**
     * Update the specified thread in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        $this->authorize('update', $thread);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'forum_id' => 'required|exists:forums,id',
            'status' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:media,id',
        ]);

        $thread->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'forum_id' => $request->forum_id,
            'status' => $request->status,
        ]);

        // Delete images if requested
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $mediaId) {
                $media = $thread->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete($media->file_path);
                    $media->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('thread-images', 'public');
                $thread->media()->create([
                    'user_id' => Auth::id(),
                    'file_name' => $image->getClientOriginalName(),
                    'file_path' => $path,
                    'file_extension' => $image->getClientOriginalExtension(),
                    'mime_type' => $image->getMimeType(),
                    'file_size' => $image->getSize(),
                    'file_category' => 'image',
                ]);
            }
        }

        return redirect()->route('threads.show', $thread)
            ->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    /**
     * Remove the specified thread from storage.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('delete', $thread);

        // Delete associated media files
        foreach ($thread->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $thread->delete();

        return redirect()->route('threads.index')
            ->with('success', 'Bài viết đã được xóa thành công.');
    }
}
