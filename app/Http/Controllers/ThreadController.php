<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the threads.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Thread::with('user', 'forum', 'category');

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

        // Apply sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'most_viewed':
                $query->orderBy('view_count', 'desc');
                break;
            case 'most_commented':
                $query->withCount('posts')->orderBy('posts_count', 'desc');
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $forums = Forum::all();

        return view('threads.create', compact('categories', 'forums'));
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
            'location' => 'nullable|string|max:255',
            'usage' => 'nullable|string|max:255',
            'floors' => 'nullable|integer|min:1',
            'status' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
        ]);

        $thread = Thread::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'content' => $request->content,
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'forum_id' => $request->forum_id,
            'location' => $request->location,
            'usage' => $request->usage,
            'floors' => $request->floors,
            'status' => $request->status,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('thread-images', 'public');
                $thread->media()->create([
                    'user_id' => Auth::id(),
                    'path' => $path,
                    'type' => 'image',
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ]);
            }
        }

        return redirect()->route('threads.show', $thread)
            ->with('success', 'Bài viết đã được tạo thành công.');
    }

    /**
     * Display the specified thread.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
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

        // Check if user has liked or saved the thread
        $isLiked = Auth::check() ? $thread->isLikedBy(Auth::user()) : false;
        $isSaved = Auth::check() ? $thread->isSavedBy(Auth::user()) : false;

        // Get related threads
        $relatedThreads = Thread::where('category_id', $thread->category_id)
            ->where('id', '!=', $thread->id)
            ->take(5)
            ->get();

        return view('threads.show', compact('thread', 'comments', 'isLiked', 'isSaved', 'relatedThreads', 'sort'));
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
            'location' => 'nullable|string|max:255',
            'usage' => 'nullable|string|max:255',
            'floors' => 'nullable|integer|min:1',
            'status' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:media,id',
        ]);

        $thread->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'forum_id' => $request->forum_id,
            'location' => $request->location,
            'usage' => $request->usage,
            'floors' => $request->floors,
            'status' => $request->status,
        ]);

        // Delete images if requested
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $mediaId) {
                $media = $thread->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete($media->path);
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
                    'path' => $path,
                    'type' => 'image',
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
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
            Storage::disk('public')->delete($media->path);
        }

        $thread->delete();

        return redirect()->route('threads.index')
            ->with('success', 'Bài viết đã được xóa thành công.');
    }
}
