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
use App\Services\FileAttachmentService;
use App\Services\FileUploadSecurityService;
use App\Services\SecurityMonitoringService;
use App\Services\UnifiedUploadService;
use App\Http\Requests\ShowcaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShowcaseController extends Controller
{
    /**
     * The unified upload service instance.
     */
    protected UnifiedUploadService $uploadService;

    /**
     * Create a new controller instance.
     */
    public function __construct(UnifiedUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Display the public showcase page with new 4-section layout.
     */
    public function publicShowcase(Request $request): View
    {
        // 1. FEATURED SHOWCASES SECTION (15 items)
        // Priority: is_featured = true → view_count → rating_average → rating_count
        $featuredShowcases = Showcase::where('is_public', true)
            ->whereIn('status', ['featured', 'approved'])
            ->with(['user', 'showcaseable', 'media', 'ratings'])
            ->withCount(['comments', 'likes'])
            ->orderByRaw("CASE WHEN status = 'featured' THEN 1 ELSE 2 END")
            ->orderBy('view_count', 'desc')
            ->orderBy('rating_average', 'desc')
            ->orderBy('rating_count', 'desc')
            ->take(15)
            ->get();

        // 2. CATEGORIES GRID SECTION
        $categories = $this->getShowcaseCategories();

        // 3. SEARCH FILTERS DATA
        $searchFilters = $this->getSearchFilters();

        // 4. ALL SHOWCASES LISTING (Paginated)
        $allShowcases = $this->getAllShowcasesWithFilters($request);

        // Process featured images using unified service
        ShowcaseImageService::processFeaturedImages($featuredShowcases);
        ShowcaseImageService::processFeaturedImages($allShowcases->getCollection());

        return view('showcase.public', compact(
            'featuredShowcases',
            'categories',
            'searchFilters',
            'allShowcases'
        ));
    }

    /**
     * Get showcase categories with statistics.
     */
    private function getShowcaseCategories(): array
    {
        $categories = Showcase::select('category')
            ->whereNotNull('category')
            ->where('is_public', true)
            ->whereIn('status', ['featured', 'approved'])
            ->groupBy('category')
            ->get();

        $categoryStats = [];
        foreach ($categories as $cat) {
            $showcases = Showcase::where('category', $cat->category)
                ->where('is_public', true)
                ->whereIn('status', ['featured', 'approved'])
                ->get();

            // Get representative showcase for category image
            $representativeShowcase = $showcases->sortByDesc('view_count')->first();

            $categoryStats[] = [
                'name' => $cat->category,
                'display_name' => ucfirst($cat->category),
                'showcase_count' => $showcases->count(),
                'total_ratings' => $showcases->sum('rating_count'),
                'avg_rating' => $showcases->where('rating_count', '>', 0)->avg('rating_average') ?? 0,
                'total_views' => $showcases->sum('view_count'),
                'featured_count' => $showcases->where('status', 'featured')->count(),
                'cover_image' => $representativeShowcase ? $representativeShowcase->featured_image : null,
                'url' => route('showcase.index', ['category' => $cat->category])
            ];
        }

        return $categoryStats;
    }



    /**
     * Get all showcases with applied filters and pagination.
     */
    private function getAllShowcasesWithFilters(Request $request)
    {
        $query = Showcase::with(['user', 'showcaseable', 'media', 'ratings'])
            ->withCount(['comments', 'likes'])
            ->where('is_public', true)
            ->whereIn('status', ['featured', 'approved']);

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('project_type', 'like', "%{$searchTerm}%")
                  ->orWhere('materials', 'like', "%{$searchTerm}%")
                  ->orWhere('industry_application', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('complexity')) {
            $query->where('complexity_level', $request->get('complexity'));
        }

        if ($request->filled('project_type')) {
            $query->where('project_type', $request->get('project_type'));
        }

        if ($request->filled('industry')) {
            $query->where('industry_application', 'like', '%' . $request->get('industry') . '%');
        }

        if ($request->filled('software')) {
            $software = $request->get('software');
            $query->where(function($q) use ($software) {
                $q->where('software_used', 'like', "%{$software}%")
                  ->orWhereJsonContains('software_used', $software);
            });
        }

        if ($request->filled('rating_min')) {
            $ratingMin = (float) $request->get('rating_min');
            $query->where('rating_average', '>=', $ratingMin);
        }

        if ($request->filled('has_cad_files')) {
            $query->where('has_cad_files', true);
        }

        if ($request->filled('has_tutorial')) {
            $query->where('has_tutorial', true);
        }

        if ($request->filled('allow_downloads')) {
            $query->where('allow_downloads', true);
        }



        // Apply sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'most_viewed':
                $query->orderBy('view_count', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating_average', 'desc')->orderBy('rating_count', 'desc');
                break;
            case 'most_downloads':
                $query->orderBy('download_count', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default: // newest
                $query->latest();
                break;
        }

        return $query->paginate(18)->withQueryString();
    }

    /**
     * Get search filters data for the search form
     */
    private function getSearchFilters(): array
    {
        $settingService = app(\App\Services\ShowcaseSettingService::class);
        $filters = $settingService->getSearchFilters();

        // Add categories from showcase_categories table
        $categories = DB::table('showcase_categories')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $filters['categories'] = [
            'name' => __('showcase.filter_category'),
            'options' => collect([
                ['value' => '', 'label' => __('common.all')]
            ])->concat(
                $categories->map(function ($category) {
                    return [
                        'value' => $category->slug,
                        'label' => $category->name,
                        'icon' => 'fas fa-folder'
                    ];
                })
            )->toArray(),
            'input_type' => 'select',
            'is_multiple' => false,
            'group' => 'classification',
            'icon' => 'fas fa-th-large'
        ];

        // Add types from showcase_types table
        $types = DB::table('showcase_types')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $filters['types'] = [
            'name' => __('showcase.filter_type'),
            'options' => collect([
                ['value' => '', 'label' => __('common.all')]
            ])->concat(
                $types->map(function ($type) {
                    return [
                        'value' => $type->slug,
                        'label' => $type->name,
                        'icon' => 'fas fa-tag'
                    ];
                })
            )->toArray(),
            'input_type' => 'select',
            'is_multiple' => false,
            'group' => 'classification',
            'icon' => 'fas fa-tags'
        ];

        // Add sort options (not managed by settings)
        $filters['sort_options'] = [
            ['value' => 'newest', 'label' => __('showcase.sort.newest')],
            ['value' => 'oldest', 'label' => __('showcase.sort.oldest')],
            ['value' => 'most_viewed', 'label' => __('showcase.sort.most_viewed')],
            ['value' => 'highest_rated', 'label' => __('showcase.sort.highest_rated')],
            ['value' => 'most_downloads', 'label' => __('showcase.sort_most_downloads')],
            ['value' => 'most_likes', 'label' => __('showcase.sort_most_likes')],
            ['value' => 'most_comments', 'label' => __('showcase.sort_most_comments')],
            ['value' => 'most_bookmarks', 'label' => __('showcase.sort_most_bookmarks')],
            ['value' => 'recently_updated', 'label' => __('showcase.sort_recently_updated')],
            ['value' => 'alphabetical_az', 'label' => __('showcase.sort_alphabetical_az')],
            ['value' => 'alphabetical_za', 'label' => __('showcase.sort_alphabetical_za')],
            ['value' => 'most_featured', 'label' => __('showcase.sort_most_featured')],
            ['value' => 'trending', 'label' => __('showcase.sort_trending')],
            ['value' => 'complexity_low_high', 'label' => __('showcase.sort_complexity_low_high')],
            ['value' => 'complexity_high_low', 'label' => __('showcase.sort_complexity_high_low')],
        ];

        return $filters;
    }

    /**
     * Store a newly created showcase item in storage.
     */
    public function store(ShowcaseRequest $request): RedirectResponse
    {
        // Validate based on request type
        if ($request->has('title')) {
            // New showcase creation - validation handled by ShowcaseRequest

            /** @var User $user */
            $user = Auth::user();

            // Initialize security services
            $fileSecurityService = new FileUploadSecurityService();
            $securityMonitoringService = new SecurityMonitoringService();

            // Upload cover image with enhanced security
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');

                // Perform security validation
                $securityResult = $fileSecurityService->validateUpload($coverImage, $user);

                if (!$securityResult['allowed']) {
                    return back()->withErrors([
                        'cover_image' => 'File upload blocked: ' . $securityResult['reason']
                    ])->withInput();
                }

                // Monitor file upload
                $securityMonitoringService->monitorFileUpload(
                    $user,
                    $coverImage->getClientOriginalName(),
                    $coverImage->getMimeType(),
                    $coverImage->getSize()
                );

                $coverImageName = time() . '_' . $coverImage->getClientOriginalName();
                $coverImagePath = $coverImage->storeAs('public/uploads/showcases/' . $user->id, $coverImageName);
            }

            // Upload multiple images with enhanced security
            $imageGallery = [];
            if ($request->hasFile('multiple_images')) {
                $multipleImages = $request->file('multiple_images');

                foreach ($multipleImages as $index => $image) {
                    // Perform security validation for each image
                    $securityResult = $fileSecurityService->validateUpload($image, $user);

                    if (!$securityResult['allowed']) {
                        return back()->withErrors([
                            'multiple_images' => "Image #" . ($index + 1) . " upload blocked: " . $securityResult['reason']
                        ])->withInput();
                    }

                    // Monitor file upload
                    $securityMonitoringService->monitorFileUpload(
                        $user,
                        $image->getClientOriginalName(),
                        $image->getMimeType(),
                        $image->getSize()
                    );

                    $imageName = time() . '_' . $index . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('public/uploads/showcases/' . $user->id . '/gallery', $imageName);

                    // Store image info in array
                    $imageGallery[] = [
                        'path' => $imagePath,
                        'name' => $image->getClientOriginalName(),
                        'size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                        'order' => $index + 1,
                        'security_scan' => $securityResult['scan_results'] ?? []
                    ];
                }
            }

            // Upload file attachments with enhanced security
            $fileAttachments = [];
            if ($request->hasFile('file_attachments')) {
                $fileAttachmentService = new FileAttachmentService();
                $attachmentFiles = $request->file('file_attachments');

                // Enhanced security validation for each attachment
                foreach ($attachmentFiles as $index => $file) {
                    $securityResult = $fileSecurityService->validateUpload($file, $user);

                    if (!$securityResult['allowed']) {
                        return back()->withErrors([
                            'file_attachments' => "Attachment #" . ($index + 1) . " upload blocked: " . $securityResult['reason']
                        ])->withInput();
                    }

                    // Monitor file upload
                    $securityMonitoringService->monitorFileUpload(
                        $user,
                        $file->getClientOriginalName(),
                        $file->getMimeType(),
                        $file->getSize()
                    );
                }

                // Validate files with existing service
                $validationErrors = $fileAttachmentService->validateFiles($attachmentFiles);
                if (!empty($validationErrors)) {
                    return back()->withErrors(['file_attachments' => $validationErrors])->withInput();
                }

                // Process files
                $fileAttachments = $fileAttachmentService->processFiles($attachmentFiles, $user->id);
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
                // New technical fields
                'software_used' => $request->software_used ? json_encode($request->software_used) : null,
                'materials' => $request->materials,
                'manufacturing_process' => $request->manufacturing_process,
                'complexity_level' => $request->complexity_level,
                'industry_application' => $request->industry_application,
                // Advanced technical fields
                'technical_specs' => $this->processTechnicalSpecs($request->technical_specs),
                'learning_objectives' => $this->processLearningObjectives($request->learning_objectives),
                // Boolean fields with defaults
                'has_tutorial' => $request->boolean('has_tutorial', false),
                'has_calculations' => $request->boolean('has_calculations', false),
                'has_cad_files' => $request->boolean('has_cad_files', false),
                'is_public' => $request->boolean('is_public', true),
                'allow_downloads' => $request->boolean('allow_downloads', false),
                'allow_comments' => $request->boolean('allow_comments', true),
                // Image gallery
                'image_gallery' => !empty($imageGallery) ? json_encode($imageGallery) : null,
                // File attachments
                'file_attachments' => !empty($fileAttachments) ? json_encode($fileAttachments) : null,
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
     * Show the form for editing the specified showcase.
     */
    public function edit(Showcase $showcase): View
    {
        // Check if user owns the showcase
        if ($showcase->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get categories for sidebar (same as in create method)
        $categories = [
            [
                'name' => 'design',
                'display_name' => 'Thiết kế',
                'url' => route('showcase.index', ['category' => 'design']),
                'count' => 15,
                'showcase_count' => 15,
                'avg_rating' => 4.5,
                'cover_image' => null
            ],
            [
                'name' => 'manufacturing',
                'display_name' => 'Sản xuất',
                'url' => route('showcase.index', ['category' => 'manufacturing']),
                'count' => 12,
                'showcase_count' => 12,
                'avg_rating' => 4.2,
                'cover_image' => null
            ],
            [
                'name' => 'analysis',
                'display_name' => 'Phân tích',
                'url' => route('showcase.index', ['category' => 'analysis']),
                'count' => 8,
                'showcase_count' => 8,
                'avg_rating' => 4.7,
                'cover_image' => null
            ],
            [
                'name' => 'automation',
                'display_name' => 'Tự động hóa',
                'url' => route('showcase.index', ['category' => 'automation']),
                'count' => 10,
                'showcase_count' => 10,
                'avg_rating' => 4.3,
                'cover_image' => null
            ]
        ];

        // Get search filters for sidebar
        $searchFilters = $this->getSearchFilters();

        // Get all showcases for sidebar stats
        $allShowcases = Showcase::where('status', 'approved')->paginate(1);

        return view('showcase.edit', compact('showcase', 'categories', 'searchFilters', 'allShowcases'));
    }

    /**
     * Update the specified showcase in storage.
     */
    public function update(ShowcaseRequest $request, Showcase $showcase): RedirectResponse
    {
        // Check if user owns the showcase
        if ($showcase->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Handle image removal
        if ($request->has('remove_images')) {
            $currentImages = $showcase->images ?? [];
            $removeIndices = $request->remove_images;

            foreach ($removeIndices as $index) {
                if (isset($currentImages[$index])) {
                    // Remove from storage if it's a file path
                    if (strpos($currentImages[$index], '/images/') === 0) {
                        $filePath = public_path($currentImages[$index]);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    unset($currentImages[$index]);
                }
            }
            $showcase->images = array_values($currentImages); // Re-index array
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($showcase, $request->file('images'));
        }

        // Handle new attachment uploads
        if ($request->hasFile('attachments')) {
            $this->handleAttachmentUploads($showcase, $request->file('attachments'));
        }

        // Update slug if title changed
        $slug = $showcase->slug;
        if ($request->title !== $showcase->title) {
            $slug = \Illuminate\Support\Str::slug($request->title);
            $count = Showcase::where('slug', $slug)->where('id', '!=', $showcase->id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
        }

        // Update showcase
        $showcase->update([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'location' => $request->location,
            'application_field' => $request->application_field,
            'technical_specs' => $request->technical_specs,
            'features' => $request->features,
            'benefits' => $request->benefits,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'is_featured' => $request->boolean('is_featured', false),
        ]);

        return redirect()->route('showcase.show', $showcase)->with('success', 'Showcase đã được cập nhật thành công!');
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
                $uploadedFiles = $this->uploadService->uploadMultipleFiles(
                    $request->file('images'),
                    Auth::user(),
                    'showcase_comment_images',
                    [
                        'mediable_type' => ShowcaseComment::class,
                        'mediable_id' => $comment->id,
                        'is_public' => true,
                        'is_approved' => true,
                    ]
                );

                // Log successful uploads
                if (!empty($uploadedFiles)) {
                    \Log::info('Showcase comment images uploaded successfully', [
                        'comment_id' => $comment->id,
                        'files_count' => count($uploadedFiles),
                        'user_id' => Auth::id()
                    ]);
                }
            }

            return back()->with('success', 'Bình luận đã được thêm thành công.');
        });
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
     * Download file attachment
     */
    public function downloadFile(Showcase $showcase, string $file)
    {
        // Decode file path
        $filePath = base64_decode($file);

        // Check if user has permission to download
        if (!$showcase->allow_downloads && $showcase->user_id !== Auth::id()) {
            abort(403, 'Không có quyền tải file này.');
        }

        // Get file attachments
        $fileAttachments = json_decode($showcase->file_attachments, true) ?? [];

        // Find the requested file
        $requestedFile = null;
        foreach ($fileAttachments as $attachment) {
            if ($attachment['path'] === $filePath) {
                $requestedFile = $attachment;
                break;
            }
        }

        if (!$requestedFile) {
            abort(404, 'File không tồn tại.');
        }

        // Check if file exists in storage
        if (!\Storage::exists($filePath)) {
            abort(404, 'File không tồn tại trên server.');
        }

        // Increment download count (optional)
        $this->incrementDownloadCount($showcase, $filePath);

        // Return file download
        return \Storage::download($filePath, $requestedFile['name']);
    }

    /**
     * Increment download count for file
     */
    private function incrementDownloadCount(Showcase $showcase, string $filePath): void
    {
        $fileAttachments = json_decode($showcase->file_attachments, true) ?? [];

        foreach ($fileAttachments as &$attachment) {
            if ($attachment['path'] === $filePath) {
                $attachment['download_count'] = ($attachment['download_count'] ?? 0) + 1;
                break;
            }
        }

        $showcase->update([
            'file_attachments' => json_encode($fileAttachments),
            'download_count' => $showcase->download_count + 1
        ]);
    }

    /**
     * Process technical specifications from form input
     */
    private function processTechnicalSpecs($technicalSpecs): ?string
    {
        if (!$technicalSpecs || !is_array($technicalSpecs)) {
            return null;
        }

        $processedSpecs = [];
        foreach ($technicalSpecs as $spec) {
            // Skip empty specs
            if (empty($spec['name']) || empty($spec['value'])) {
                continue;
            }

            $processedSpecs[] = [
                'name' => trim($spec['name']),
                'value' => trim($spec['value']),
                'unit' => isset($spec['unit']) ? trim($spec['unit']) : ''
            ];
        }

        return !empty($processedSpecs) ? json_encode($processedSpecs) : null;
    }

    /**
     * Process learning objectives from form input
     */
    private function processLearningObjectives($learningObjectives): ?string
    {
        if (!$learningObjectives || !is_array($learningObjectives)) {
            return null;
        }

        $processedObjectives = [];
        foreach ($learningObjectives as $objective) {
            // Skip empty objectives
            if (empty($objective) || !is_string($objective)) {
                continue;
            }

            $trimmedObjective = trim($objective);
            if (!empty($trimmedObjective)) {
                $processedObjectives[] = $trimmedObjective;
            }
        }

        return !empty($processedObjectives) ? json_encode($processedObjectives) : null;
    }

}
