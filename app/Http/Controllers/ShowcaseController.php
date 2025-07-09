<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Showcase;
use App\Models\ShowcaseComment;
use App\Models\ShowcaseLike;
use App\Models\ShowcaseFollow;
use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Services\ShowcaseImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Get user showcases WITH media for unified image processing
        $userShowcases = Showcase::with(['user', 'showcaseable', 'media'])
            ->whereHas('showcaseable') // Ensure showcaseable exists
            ->latest()
            ->paginate(20);

        // Process featured images using unified service
        ShowcaseImageService::processFeaturedImages($userShowcases->getCollection());

        return view('showcase.public', compact('featuredThreads', 'popularThreads', 'userShowcases'));
    }

    /**
     * Store a newly created showcase item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate based on request type
        if ($request->has('title')) {
            // New showcase creation
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'nullable|string|max:255',
                'usage' => 'nullable|string|max:255',
                'floors' => 'nullable|integer|min:1|max:5',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
                'category' => 'nullable|string',
            ]);

            /** @var User $user */
            $user = Auth::user();

            // Upload cover image
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $coverImageName = time() . '_' . $coverImage->getClientOriginalName();
                $coverImagePath = $coverImage->storeAs('public/uploads/showcases/' . $user->id, $coverImageName);
            }

            // Create slug from title
            $slug = \Illuminate\Support\Str::slug($request->title);
            $count = Showcase::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            // Create showcase
            $showcase = Showcase::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'location' => $request->location,
                'usage' => $request->usage,
                'floors' => $request->floors,
                'cover_image' => $coverImagePath,
                'status' => 'approved', // Direct approval for regular users
                'category' => $request->category,
            ]);

            return redirect()->route('showcase.show', $showcase)->with('success', 'Showcase đã được tạo thành công!');
        } else {
            // Legacy: Adding existing thread/post to showcase
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
            'media', // Sửa từ 'attachments' thành 'media'
            'comments' => function ($query) {
                $query->whereNull('parent_id')->with(['user', 'replies.user', 'attachments', 'replies.attachments'])->latest();
            },
            'likes.user',
            'follows'
        ]);

        // Lấy comments cho view
        $comments = $showcase->comments()->whereNull('parent_id')->with(['user', 'replies.user', 'attachments', 'replies.attachments'])->latest()->get();

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
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:showcase_comments,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5120', // 5MB max per image
        ]);

        // Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu
        return DB::transaction(function () use ($request, $showcase) {
            $hasMedia = $request->hasFile('images');

            // Tạo comment
            $comment = $showcase->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $request->content,
                'parent_id' => $request->parent_id,
                'has_media' => $hasMedia,
            ]);

            // Xử lý upload hình ảnh nếu có
            if ($hasMedia) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('showcase-comment-images', 'public');
                    $comment->attachments()->create([
                        'user_id' => Auth::id(),
                        'file_path' => $path,
                        'file_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'file_extension' => $image->getClientOriginalExtension(),
                        'file_category' => 'comment_image',
                        'is_public' => true,
                        'is_approved' => true,
                    ]);
                }
            }

            return back()->with('success', 'Bình luận đã được thêm thành công.');
        });
    }

    /**
     * Xử lý upload ảnh cho comment.
     */
    private function handleCommentImages($images, $comment)
    {
        foreach ($images as $image) {
            if ($image->isValid()) {
                // Tạo tên file unique
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Lưu ảnh vào thư mục public/images/comments
                $path = $image->move(public_path('images/comments'), $filename);

                // Lưu thông tin ảnh vào database (nếu có bảng comment_images)
                // Hoặc lưu vào field images của comment (JSON format)
                $existingImages = json_decode($comment->images ?? '[]', true);
                $existingImages[] = [
                    'filename' => $filename,
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'uploaded_at' => now()->toISOString()
                ];

                $comment->update(['images' => json_encode($existingImages)]);
            }
        }
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

    /**
     * Toggle bookmark cho showcase.
     */
    public function toggleBookmark(Showcase $showcase)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if already bookmarked
        $existingBookmark = $user->bookmarks()
            ->where('bookmarkable_type', Showcase::class)
            ->where('bookmarkable_id', $showcase->id)
            ->first();

        if ($existingBookmark) {
            // Remove bookmark
            $existingBookmark->delete();
            $isBookmarked = false;
            $message = 'Đã bỏ lưu showcase này.';
        } else {
            // Add bookmark - sử dụng model Bookmark đúng cách
            $user->bookmarks()->create([
                'bookmarkable_type' => Showcase::class,
                'bookmarkable_id' => $showcase->id,
                'notes' => 'Showcase: ' . ($showcase->title ?? 'Untitled')
            ]);
            $isBookmarked = true;
            $message = 'Đã lưu showcase này vào bookmark.';
        }

        // Return JSON for AJAX or redirect for regular form
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Show the form for creating a new showcase item.
     */
    public function create(): View
    {
        return view('showcase.create');
    }

    /**
     * Display featured showcases.
     */
    public function featured(): View
    {
        $showcases = Showcase::where('status', 'featured')
            ->where('is_public', true)
            ->with(['user', 'showcaseable'])
            ->latest()
            ->paginate(20);

        return view('showcase.featured', compact('showcases'));
    }

    /**
     * Display showcase categories.
     */
    public function categories(): View
    {
        // Get showcases grouped by category
        $categories = [
            'design' => 'Thiết kế',
            'manufacturing' => 'Sản xuất',
            'automation' => 'Tự động hóa',
            'research' => 'Nghiên cứu',
            'innovation' => 'Đổi mới',
        ];

        $showcasesByCategory = [];
        foreach ($categories as $key => $name) {
            $showcasesByCategory[$key] = [
                'name' => $name,
                'showcases' => Showcase::where('category', $key)
                    ->with(['user', 'showcaseable'])
                    ->latest()
                    ->take(6)
                    ->get()
            ];
        }

        return view('showcase.categories', compact('showcasesByCategory'));
    }

    /**
     * Display trending showcases.
     */
    public function trending(): View
    {
        $showcases = Showcase::withCount(['likes', 'comments'])
            ->with(['user', 'showcaseable'])
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->orderByDesc('view_count')
            ->paginate(20);

        return view('showcase.trending', compact('showcases'));
    }

    /**
     * Display showcase leaderboard.
     */
    public function leaderboard(): View
    {
        $topCreators = User::withCount(['showcaseItems as showcases_count'])
            ->withSum('showcaseItems as total_likes', 'like_count')
            ->withSum('showcaseItems as total_views', 'view_count')
            ->having('showcases_count', '>', 0)
            ->orderByDesc('total_likes')
            ->orderByDesc('showcases_count')
            ->take(50)
            ->get();

        return view('showcase.leaderboard', compact('topCreators'));
    }

    /**
     * Display showcase competitions.
     */
    public function competitions(): View
    {
        // For now, return a coming soon page
        return view('showcase.competitions');
    }

    /**
     * Display submission guidelines.
     */
    public function guidelines(): View
    {
        return view('showcase.guidelines');
    }

    /**
     * Display user's draft showcases.
     */
    public function drafts(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $drafts = $user->showcaseItems()
            ->where('status', 'draft')
            ->with('showcaseable')
            ->latest()
            ->paginate(20);

        return view('showcase.drafts', compact('drafts'));
    }
}
