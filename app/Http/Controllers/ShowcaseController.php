<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\ShowcaseComment;
use App\Models\ShowcaseLike;
use App\Models\ShowcaseFollow;
use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of the user's showcase items.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $showcaseItems = $user->showcaseItems()
            ->with('showcaseable')
            ->latest()
            ->paginate(20);

        return view('showcase.index', compact('showcaseItems'));
    }

    /**
     * Display the public showcase page.
     */
    public function publicShowcase(): View
    {
        // Get featured content
        $featuredThreads = Thread::where('is_featured', true)
            ->with(['user', 'forum'])
            ->whereHas('forum') // Ensure forum exists
            ->latest()
            ->take(5)
            ->get();

        // Get most popular threads
        $popularThreads = Thread::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->with(['user', 'forum'])
            ->whereHas('forum') // Ensure forum exists
            ->take(10)
            ->get();

        // Get user showcases
        $userShowcases = Showcase::with(['user', 'showcaseable'])
            ->whereHas('showcaseable') // Ensure showcaseable exists
            ->latest()
            ->paginate(20);

        return view('showcase.public', compact('featuredThreads', 'popularThreads', 'userShowcases'));
    }

    /**
     * Store a newly created showcase item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'showcaseable_id' => 'required|integer',
            'showcaseable_type' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Check if the item is already in the showcase
        $exists = $user->showcaseItems()
            ->where('showcaseable_id', $request->showcaseable_id)
            ->where('showcaseable_type', $request->showcaseable_type)
            ->exists();

        if ($exists) {
            return back()->with('info', 'This item is already in your showcase.');
        }

        // Create the showcase item
        $user->showcaseItems()->create([
            'showcaseable_id' => $request->showcaseable_id,
            'showcaseable_type' => $request->showcaseable_type,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Item added to showcase successfully.');
    }

    /**
     * Remove the specified showcase item from storage.
     */
    public function destroy(Showcase $showcase): RedirectResponse
    {
        // Check if the showcase item belongs to the authenticated user
        if ($showcase->user_id !== Auth::id()) {
            abort(403);
        }

        $showcase->delete();

        return back()->with('success', 'Item removed from showcase successfully.');
    }

    /**
     * Hiển thị chi tiết showcase với comments, likes, follows.
     */
    public function show(Showcase $showcase): View
    {
        // Load relationships cần thiết
        $showcase->load([
            'user',
            'attachments',
            'comments' => function ($query) {
                $query->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
            },
            'likes.user',
            'follows'
        ]);

        // Lấy comments cho view
        $comments = $showcase->comments()->whereNull('parent_id')->with(['user', 'replies.user'])->latest()->get();

        // Lấy các showcase khác của tác giả
        $otherShowcases = Showcase::where('user_id', $showcase->user_id)
            ->where('id', '!=', $showcase->id)
            ->latest()
            ->take(5)
            ->get();

        return view('showcase.show', compact('showcase', 'comments', 'otherShowcases'));
    }

    /**
     * Toggle like cho showcase.
     */
    public function toggleLike(Showcase $showcase)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $existingLike = $showcase->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            // Nếu đã like thì unlike
            $existingLike->delete();
            $isLiked = false;
            $message = 'Đã bỏ thích showcase này.';
        } else {
            // Nếu chưa like thì like
            $showcase->likes()->create([
                'user_id' => $user->id,
            ]);
            $isLiked = true;
            $message = 'Đã thích showcase này.';
        }

        // Trả về JSON cho AJAX hoặc redirect cho form thường
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'likes_count' => $showcase->likesCount(),
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Toggle follow cho showcase owner.
     */
    public function toggleFollow(Showcase $showcase)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Không thể follow chính mình
        if ($user->id === $showcase->user_id) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Bạn không thể theo dõi chính mình.'], 400);
            }
            return back()->with('error', 'Bạn không thể theo dõi chính mình.');
        }

        $existingFollow = ShowcaseFollow::where('follower_id', $user->id)
            ->where('following_id', $showcase->user_id)
            ->first();

        if ($existingFollow) {
            // Nếu đã follow thì unfollow
            $existingFollow->delete();
            $isFollowing = false;
            $message = 'Đã hủy theo dõi người dùng này.';
        } else {
            // Nếu chưa follow thì follow
            ShowcaseFollow::create([
                'follower_id' => $user->id,
                'following_id' => $showcase->user_id,
            ]);
            $isFollowing = true;
            $message = 'Đã theo dõi người dùng này.';
        }

        // Trả về JSON cho AJAX hoặc redirect cho form thường
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_following' => $isFollowing,
                'follows_count' => $showcase->followsCount(),
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Thêm comment vào showcase.
     */
    public function addComment(Request $request, Showcase $showcase): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:showcase_comments,id',
        ]);

        $showcase->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Bình luận đã được thêm thành công.');
    }

    /**
     * Xóa comment.
     */
    public function deleteComment(ShowcaseComment $comment): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $showcase = $comment->showcase;

        // Chỉ cho phép xóa nếu là chủ comment hoặc chủ showcase
        if ($comment->user_id !== $user->id && $showcase->user_id !== $user->id) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Bình luận đã được xóa.');
    }
}
