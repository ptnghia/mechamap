<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Showcase;
use App\Models\ShowcaseCategory;
use App\Models\ShowcaseType;
use App\Models\User;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
     * Display a listing of the threads with advanced filtering.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validate advanced search parameters
        $request->validate([
            'q' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255', // Legacy parameter
            'category' => 'nullable|exists:categories,id',
            'forum' => 'nullable|exists:forums,id',
            'author' => 'nullable|string|max:100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|in:latest,oldest,most_viewed,most_commented,relevance',
            'featured' => 'nullable|boolean',
            'has_poll' => 'nullable|boolean',
        ]);

        $query = Thread::with('user', 'forum', 'category')
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings']);

        // Search query (support both 'q' and 'search' parameters)
        $searchQuery = $request->get('q') ?: $request->get('search');
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('content', 'like', "%{$searchQuery}%")
                    ->orWhereHas('user', function ($userQuery) use ($searchQuery) {
                        $userQuery->where('name', 'like', "%{$searchQuery}%")
                                  ->orWhere('username', 'like', "%{$searchQuery}%");
                    });
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Forum filter
        if ($request->has('forum') && $request->forum) {
            $query->where('forum_id', $request->forum);
        }

        // Author filter
        if ($request->has('author') && $request->author) {
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('username', 'like', "%{$request->author}%")
                          ->orWhere('name', 'like', "%{$request->author}%");
            });
        }

        // Date range filters
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Featured/Sticky filter
        if ($request->has('featured') && $request->featured) {
            $query->where('is_sticky', true);
        }

        // Poll filter
        if ($request->has('has_poll') && $request->has_poll) {
            $query->whereNotNull('poll_id');
        }

        // Quick date filters
        $quickFilter = $request->get('quick_filter');
        if ($quickFilter) {
            switch ($quickFilter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->subYear());
                    break;
            }
        }

        // Apply sorting - ALWAYS put sticky threads first (unless filtering by featured)
        $sort = $request->get('sort', 'latest');

        if (!$request->has('featured')) {
            $query->orderBy('is_sticky', 'desc');
        }

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
            case 'relevance':
                if ($searchQuery) {
                    // Simple relevance scoring based on title match
                    $query->orderByRaw("CASE WHEN title LIKE ? THEN 1 ELSE 2 END", ["%{$searchQuery}%"]);
                }
                $query->latest();
                break;
            default:
                $query->latest();
                break;
        }

        $threads = $query->paginate(15)->withQueryString();
        $categories = Category::all();
        $forums = Forum::all();

        // Prepare filter data for view
        $filters = [
            'q' => $searchQuery,
            'category' => $request->category,
            'forum' => $request->forum,
            'author' => $request->author,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'featured' => $request->featured,
            'has_poll' => $request->has_poll,
            'quick_filter' => $quickFilter,
        ];

        return view('threads.index', compact('threads', 'categories', 'forums', 'sort', 'filters'));
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
            // Showcase validation
            'create_showcase' => 'boolean',
            'showcase_type' => 'required_if:create_showcase,1|string|in:new,existing',
            'existing_showcase_id' => 'required_if:showcase_type,existing|exists:showcases,id',
            'showcase_title' => 'required_if:showcase_type,new|string|max:255',
            'showcase_description' => 'required_if:showcase_type,new|string|min:50',
            'project_type' => 'nullable|string|max:100',
            'complexity_level' => 'nullable|string|in:Beginner,Intermediate,Advanced,Expert',
            'industry_application' => 'nullable|string|max:255',
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

            // Create or attach showcase if requested
            if ($request->has('create_showcase') && $request->create_showcase) {
                if ($request->showcase_type === 'existing') {
                    // Attach existing showcase to thread
                    $existingShowcase = Showcase::findOrFail($request->existing_showcase_id);

                    // Verify ownership
                    if ($existingShowcase->user_id !== Auth::id()) {
                        throw new \Exception('Bạn không có quyền sử dụng showcase này.');
                    }

                    // Update showcase to link to this thread
                    $existingShowcase->update([
                        'showcaseable_id' => $thread->id,
                        'showcaseable_type' => Thread::class,
                    ]);
                } else {
                    // Create new showcase
                    $showcaseData = [
                        'user_id' => Auth::id(),
                        'showcaseable_id' => $thread->id,
                        'showcaseable_type' => Thread::class,
                        'title' => $request->showcase_title,
                        'slug' => Str::slug($request->showcase_title) . '-' . Str::random(5),
                        'description' => $request->showcase_description,
                        'status' => 'approved', // Auto-approve for thread-based showcases
                        'is_public' => true,
                        'allow_comments' => true,
                        'allow_downloads' => false,
                    ];

                    // Add optional fields if provided
                    if ($request->project_type) {
                        $showcaseData['project_type'] = $request->project_type;
                    }
                    if ($request->complexity_level) {
                        $showcaseData['complexity_level'] = $request->complexity_level;
                    }
                    if ($request->industry_application) {
                        $showcaseData['industry_application'] = $request->industry_application;
                    }

                    $showcase = Showcase::create($showcaseData);
                }
            }

            // Log activity
            $this->activityService->logThreadCreated(Auth::user(), $thread);

            // Send thread created notification
            \App\Services\NotificationService::sendThreadCreatedNotification($thread);

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
            'user' => function ($q) {
                $q->withCount(['threads' => function ($threadQuery) {
                    $threadQuery->where('moderation_status', 'approved');
                }]);
            },
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
        $commentsQuery = $thread->comments()->with([
            'user' => function ($q) {
                $q->withCount('comments');
            },
            'replies.user' => function ($q) {
                $q->withCount('comments');
            }
        ]);

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
        $lastComment = $thread->comments()->with([
            'user' => function ($q) {
                $q->withCount('comments');
            }
        ])->latest()->first();
        $thread->lastCommenter = $lastComment ? $lastComment->user : $thread->user;
        $thread->lastCommentAt = $lastComment ? $lastComment->created_at : null;

        // Validate showcase ownership if exists
        if ($thread->showcase && !$thread->hasValidShowcaseOwnership()) {
            // Log potential security issue
            \Log::warning('Invalid showcase ownership detected', [
                'thread_id' => $thread->id,
                'thread_user_id' => $thread->user_id,
                'showcase_id' => $thread->showcase->id,
                'showcase_user_id' => $thread->showcase->user_id
            ]);
        }

        // Get showcase categories and types for modal
        $showcaseCategories = ShowcaseCategory::active()->orderBy('name')->get();
        $showcaseTypes = ShowcaseType::active()->orderBy('name')->get();

        return view('threads.show', compact('thread', 'comments', 'isLiked', 'isSaved', 'isFollowed', 'relatedThreads', 'sort', 'showcaseCategories', 'showcaseTypes'));
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

    /**
     * Create a showcase from thread
     *
     * @param Request $request
     * @param Thread $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createShowcase(Request $request, Thread $thread)
    {
        // Authorization check
        if (Auth::user()->id !== $thread->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
            abort(403, 'Bạn không có quyền tạo showcase cho thread này.');
        }

        // Validation: Check if thread already has showcase
        if ($thread->showcase) {
            return redirect()->back()
                ->with('error', 'Thread này đã có showcase. Mỗi thread chỉ có thể có một showcase.');
        }

        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'showcase_category_id' => 'required|exists:showcase_categories,id',
            'showcase_type_id' => 'nullable|exists:showcase_types,id',
            'complexity_level' => 'nullable|string|in:Beginner,Intermediate,Advanced,Expert',
            'industry_application' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB - made optional
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:51200', // 50MB per file
            'agree_terms' => 'required|accepted'
        ], [
            'title.required' => 'Tiêu đề showcase là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả dự án là bắt buộc.',
            'description.min' => 'Mô tả phải có ít nhất 50 ký tự.',
            'showcase_category_id.required' => 'Danh mục là bắt buộc.',
            'showcase_category_id.exists' => 'Danh mục không hợp lệ.',
            'showcase_type_id.exists' => 'Loại dự án không hợp lệ.',
            'cover_image.image' => 'File phải là hình ảnh.',
            'cover_image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'cover_image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
            'attachments.max' => 'Chỉ được upload tối đa 10 file đính kèm.',
            'attachments.*.file' => 'File đính kèm không hợp lệ.',
            'attachments.*.max' => 'Mỗi file đính kèm không được vượt quá 50MB.',
            'agree_terms.required' => 'Bạn phải đồng ý với điều khoản.',
            'agree_terms.accepted' => 'Bạn phải đồng ý với điều khoản.'
        ]);

        try {
            DB::beginTransaction();

            // Handle cover image upload
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $coverImagePath = $request->file('cover_image')->store('showcases/covers', 'public');
            }

            // Create showcase
            $showcase = new \App\Models\Showcase([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'showcase_category_id' => $validated['showcase_category_id'],
                'showcase_type_id' => $validated['showcase_type_id'] ?? null,
                'complexity_level' => $validated['complexity_level'] ?? 'Intermediate',
                'industry_application' => $validated['industry_application'] ?? null,
                'cover_image' => $coverImagePath,
                'user_id' => Auth::id(),
                'status' => 'approved',
                'showcaseable_type' => Thread::class,
                'showcaseable_id' => $thread->id,
                'view_count' => 0,
                'like_count' => 0,
                'download_count' => 0,
                'share_count' => 0,
                'rating_average' => 0.0,
                'rating_count' => 0
            ]);

            $showcase->save();

            // Copy thread images to showcase media if any
            if ($thread->media && $thread->media->count() > 0) {
                foreach ($thread->media->take(5) as $media) { // Limit to 5 images
                    $showcase->media()->create([
                        'user_id' => Auth::id(),
                        'file_path' => $media->file_path,
                        'file_name' => $media->file_name,
                        'file_extension' => $media->file_extension ?? pathinfo($media->file_name, PATHINFO_EXTENSION),
                        'mime_type' => $media->mime_type ?? 'image/jpeg',
                        'file_size' => $media->file_size,
                        'file_category' => $media->file_category ?? 'image',
                        'disk' => $media->disk ?? 'public',
                        'processing_status' => 'completed',
                        'is_public' => true,
                        'is_approved' => true,
                    ]);
                }
            }

            // Handle file attachments if any
            if ($request->hasFile('attachments')) {
                $attachmentPaths = [];

                foreach ($request->file('attachments') as $attachment) {
                    // Generate unique filename
                    $fileName = time() . '_' . uniqid() . '_' . $attachment->getClientOriginalName();

                    // Store file in public/images/showcases/attachments/
                    $path = $attachment->storeAs('images/showcases/attachments', $fileName, 'public');

                    // Create media record
                    $showcase->media()->create([
                        'user_id' => Auth::id(),
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_path' => $path,
                        'file_extension' => $attachment->getClientOriginalExtension(),
                        'mime_type' => $attachment->getMimeType(),
                        'file_size' => $attachment->getSize(),
                        'file_category' => $this->getFileCategory($attachment),
                        'disk' => 'public',
                        'processing_status' => 'completed',
                        'is_public' => true,
                        'is_approved' => true,
                    ]);

                    $attachmentPaths[] = $path;
                }

                // Update showcase with file_attachments JSON
                $showcase->update([
                    'file_attachments' => $attachmentPaths
                ]);
            }

            // Log activity
            $this->activityService->logActivity(
                Auth::user(),
                'showcase_created_from_thread',
                'Đã tạo showcase từ thread: ' . $thread->title,
                $showcase
            );

            DB::commit();

            return redirect()->route('showcase.show', $showcase)
                ->with('success', 'Showcase đã được tạo thành công từ thread!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded image if exists
            if ($coverImagePath) {
                Storage::disk('public')->delete($coverImagePath);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo showcase: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create showcase from thread via AJAX
     */
    public function createShowcaseAjax(Request $request, Thread $thread)
    {
        try {
            // Authorization check
            if (Auth::user()->id !== $thread->user_id && !Auth::user()->hasRole(['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo showcase cho thread này.'
                ], 403);
            }

            // Validation: Check if thread already has showcase
            if ($thread->showcase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thread này đã có showcase. Mỗi thread chỉ có thể có một showcase.'
                ], 422);
            }

            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:50',
                'showcase_category_id' => 'required|exists:showcase_categories,id',
                'showcase_type_id' => 'nullable|exists:showcase_types,id',
                'complexity_level' => 'nullable|string|in:Beginner,Intermediate,Advanced,Expert',
                'industry_application' => 'nullable|string|max:255',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
                'attachments' => 'nullable|array|max:10',
                'attachments.*' => 'file|max:51200', // 50MB per file
                'agree_terms' => 'required|accepted'
            ], [
                'title.required' => 'Tiêu đề showcase là bắt buộc.',
                'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
                'description.required' => 'Mô tả dự án là bắt buộc.',
                'description.min' => 'Mô tả phải có ít nhất 50 ký tự.',
                'showcase_category_id.required' => 'Danh mục là bắt buộc.',
                'showcase_category_id.exists' => 'Danh mục không hợp lệ.',
                'showcase_type_id.exists' => 'Loại dự án không hợp lệ.',
                'cover_image.image' => 'File phải là hình ảnh.',
                'cover_image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
                'cover_image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
                'attachments.max' => 'Chỉ được upload tối đa 10 file đính kèm.',
                'attachments.*.file' => 'File đính kèm không hợp lệ.',
                'attachments.*.max' => 'Mỗi file đính kèm không được vượt quá 50MB.',
                'agree_terms.required' => 'Bạn phải đồng ý với điều khoản.',
                'agree_terms.accepted' => 'Bạn phải đồng ý với điều khoản.'
            ]);

            DB::beginTransaction();

            // Handle cover image upload
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $coverImagePath = $request->file('cover_image')->store('showcases/covers', 'public');
            }

            // Generate unique slug
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug is unique
            while (\App\Models\Showcase::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create showcase
            $showcase = new \App\Models\Showcase([
                'title' => $validated['title'],
                'slug' => $slug,
                'description' => $validated['description'],
                'showcase_category_id' => $validated['showcase_category_id'],
                'showcase_type_id' => $validated['showcase_type_id'] ?? null,
                'complexity_level' => $validated['complexity_level'] ?? 'Intermediate',
                'industry_application' => $validated['industry_application'] ?? null,
                'cover_image' => $coverImagePath,
                'user_id' => Auth::id(),
                'status' => 'approved',
                'showcaseable_type' => Thread::class,
                'showcaseable_id' => $thread->id,
                'view_count' => 0,
                'like_count' => 0,
                'download_count' => 0,
                'share_count' => 0,
                'rating_average' => 0.0,
                'rating_count' => 0
            ]);

            $showcase->save();

            // Copy thread images to showcase media if any
            if ($thread->media && $thread->media->count() > 0) {
                foreach ($thread->media->take(5) as $media) { // Limit to 5 images
                    $showcase->media()->create([
                        'user_id' => Auth::id(),
                        'file_path' => $media->file_path,
                        'file_name' => $media->file_name,
                        'file_extension' => $media->file_extension ?? pathinfo($media->file_name, PATHINFO_EXTENSION),
                        'mime_type' => $media->mime_type ?? 'image/jpeg',
                        'file_size' => $media->file_size,
                        'file_category' => $media->file_category ?? 'image',
                        'disk' => $media->disk ?? 'public',
                        'processing_status' => 'completed',
                        'is_public' => true,
                        'is_approved' => true,
                    ]);
                }
            }

            // Handle file attachments if any
            if ($request->hasFile('attachments')) {
                $attachmentPaths = [];

                foreach ($request->file('attachments') as $attachment) {
                    // Generate unique filename
                    $fileName = time() . '_' . uniqid() . '_' . $attachment->getClientOriginalName();

                    // Store file in public/images/showcases/attachments/
                    $path = $attachment->storeAs('images/showcases/attachments', $fileName, 'public');

                    // Create media record
                    $showcase->media()->create([
                        'user_id' => Auth::id(),
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_path' => $path,
                        'file_extension' => $attachment->getClientOriginalExtension(),
                        'mime_type' => $attachment->getMimeType(),
                        'file_size' => $attachment->getSize(),
                        'file_category' => $this->getFileCategory($attachment),
                        'disk' => 'public',
                        'processing_status' => 'completed',
                        'is_public' => true,
                        'is_approved' => true,
                    ]);

                    $attachmentPaths[] = $path;
                }

                // Update showcase with file_attachments JSON
                $showcase->update([
                    'file_attachments' => $attachmentPaths
                ]);
            }

            // Log activity
            $this->activityService->logActivity(
                Auth::user(),
                'showcase_created_from_thread',
                'Đã tạo showcase từ thread: ' . $thread->title,
                $showcase
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Showcase đã được tạo thành công từ thread!',
                'data' => [
                    'showcase_id' => $showcase->id,
                    'showcase_url' => route('showcases.show', $showcase),
                    'thread_id' => $thread->id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded image if exists
            if (isset($coverImagePath) && $coverImagePath) {
                Storage::disk('public')->delete($coverImagePath);
            }

            // Log error for monitoring
            \Log::error('Showcase creation failed', [
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo showcase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine file category based on file extension and mime type
     */
    private function getFileCategory($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        // Images
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        // Documents
        if (in_array($extension, ['pdf', 'doc', 'docx', 'txt', 'rtf'])) {
            return 'document';
        }

        // CAD Files
        if (in_array($extension, ['dwg', 'dxf', 'step', 'stp', 'stl', 'obj', 'iges', 'igs'])) {
            return 'cad';
        }

        // Spreadsheets
        if (in_array($extension, ['xls', 'xlsx', 'csv'])) {
            return 'spreadsheet';
        }

        // Presentations
        if (in_array($extension, ['ppt', 'pptx'])) {
            return 'presentation';
        }

        // Archives
        if (in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return 'archive';
        }

        return 'other';
    }
}
