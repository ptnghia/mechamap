<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\DocumentationCategory;
use App\Models\DocumentationDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    /**
     * Display documentation portal homepage
     */
    public function index(Request $request)
    {
        // Get featured documentations
        $featuredDocs = Documentation::published()
                                   ->accessibleBy(Auth::user())
                                   ->featured()
                                   ->with(['category', 'author'])
                                   ->orderBy('published_at', 'desc')
                                   ->limit(6)
                                   ->get();

        // Get recent documentations
        $recentDocs = Documentation::published()
                                 ->accessibleBy(Auth::user())
                                 ->with(['category', 'author'])
                                 ->orderBy('published_at', 'desc')
                                 ->limit(8)
                                 ->get();

        // Get popular documentations
        $popularDocs = Documentation::published()
                                  ->accessibleBy(Auth::user())
                                  ->with(['category', 'author'])
                                  ->orderBy('view_count', 'desc')
                                  ->limit(6)
                                  ->get();

        // Get categories with document counts
        $categories = DocumentationCategory::accessibleBy(Auth::user())
                                         ->active()
                                         ->root()
                                         ->withCount(['publishedDocumentations' => function ($query) {
                                             $query->accessibleBy(Auth::user());
                                         }])
                                         ->orderBy('sort_order')
                                         ->get();

        // Get statistics
        $stats = [
            'total_docs' => Documentation::published()->accessibleBy(Auth::user())->count(),
            'total_categories' => $categories->count(),
            'total_views' => Documentation::published()->accessibleBy(Auth::user())->sum('view_count'),
            'total_downloads' => DocumentationDownload::count(),
        ];

        return view('docs.index', compact(
            'featuredDocs', 'recentDocs', 'popularDocs', 'categories', 'stats'
        ));
    }

    /**
     * Display documentation by category
     */
    public function category(DocumentationCategory $category, Request $request)
    {
        // Check access permission
        if (!$category->canAccess(Auth::user())) {
            abort(403, 'Bạn không có quyền truy cập danh mục này.');
        }

        $query = $category->publishedDocumentations()
                         ->accessibleBy(Auth::user())
                         ->with(['author']);

        // Search within category
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by content type
        if ($request->filled('type')) {
            $query->where('content_type', $request->type);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('title');
                break;
            default:
                $query->orderBy('published_at', 'desc');
        }

        $documentations = $query->paginate(12);

        // Get subcategories
        $subcategories = $category->children()
                                ->accessibleBy(Auth::user())
                                ->active()
                                ->withCount(['publishedDocumentations' => function ($query) {
                                    $query->accessibleBy(Auth::user());
                                }])
                                ->orderBy('sort_order')
                                ->get();

        return view('docs.category', compact('category', 'documentations', 'subcategories'));
    }

    /**
     * Display specific documentation
     */
    public function show(Documentation $documentation, Request $request)
    {
        // Check access permission
        if (!$documentation->canAccess(Auth::user())) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        // Record view
        $documentation->recordView(Auth::user(), [
            'time_spent' => $request->get('time_spent'),
            'scroll_percentage' => $request->get('scroll_percentage'),
        ]);

        // Load relationships
        $documentation->load(['category', 'author', 'reviewer']);

        // Get related documents
        $relatedDocs = $documentation->getRelatedDocuments();
        if ($relatedDocs->isEmpty()) {
            // Get documents from same category
            $relatedDocs = Documentation::published()
                                      ->accessibleBy(Auth::user())
                                      ->where('category_id', $documentation->category_id)
                                      ->where('id', '!=', $documentation->id)
                                      ->orderBy('view_count', 'desc')
                                      ->limit(4)
                                      ->get();
        }

        // Get user's rating if authenticated
        $userRating = null;
        if (Auth::check()) {
            $userRating = $documentation->ratings()
                                       ->where('user_id', Auth::id())
                                       ->first();
        }

        // Get comments
        $comments = $documentation->comments()
                                ->where('status', 'approved')
                                ->with(['user', 'replies.user'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('docs.show', compact(
            'documentation', 'relatedDocs', 'userRating', 'comments'
        ));
    }

    /**
     * Search documentations
     */
    public function search(Request $request)
    {
        $query = Documentation::published()
                             ->accessibleBy(Auth::user())
                             ->with(['category', 'author']);

        // Search query
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search);
            });
        }

        // Filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('content_type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Sort
        $sortBy = $request->get('sort', 'relevance');
        switch ($sortBy) {
            case 'latest':
                $query->orderBy('published_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc');
                break;
            default:
                // Relevance sorting (basic implementation)
                if ($request->filled('q')) {
                    $query->orderByRaw("CASE 
                        WHEN title LIKE ? THEN 1 
                        WHEN excerpt LIKE ? THEN 2 
                        ELSE 3 END", [
                        "%{$request->q}%", 
                        "%{$request->q}%"
                    ]);
                }
        }

        $documentations = $query->paginate(12);

        // Get filter options
        $categories = DocumentationCategory::accessibleBy(Auth::user())
                                         ->active()
                                         ->orderBy('name')
                                         ->get();

        return view('docs.search', compact('documentations', 'categories'));
    }

    /**
     * Download file
     */
    public function download(Documentation $documentation, string $file, Request $request)
    {
        // Check access permission
        if (!$documentation->canAccess(Auth::user())) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        // Find file in downloadable_files
        $downloadableFile = collect($documentation->downloadable_files ?? [])
                          ->firstWhere('name', $file);

        if (!$downloadableFile) {
            abort(404, 'File không tồn tại.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($downloadableFile['path'])) {
            abort(404, 'File không tồn tại trên server.');
        }

        // Record download
        DocumentationDownload::create([
            'documentation_id' => $documentation->id,
            'user_id' => Auth::id(),
            'file_name' => $downloadableFile['name'],
            'file_path' => $downloadableFile['path'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Increment download count
        $documentation->increment('download_count');

        // Return file download
        return Storage::disk('public')->download(
            $downloadableFile['path'],
            $downloadableFile['name']
        );
    }

    /**
     * Rate documentation
     */
    public function rate(Documentation $documentation, Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check access permission
        if (!$documentation->canAccess(Auth::user())) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        $documentation->addRating(
            Auth::user(),
            $request->rating,
            $request->comment
        );

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đã được ghi nhận.',
            'rating_average' => $documentation->fresh()->rating_average,
            'rating_count' => $documentation->fresh()->rating_count,
        ]);
    }

    /**
     * Add comment
     */
    public function comment(Documentation $documentation, Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:documentation_comments,id',
        ]);

        // Check access permission
        if (!$documentation->canAccess(Auth::user())) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        $comment = $documentation->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'status' => 'approved', // Auto-approve for now
            'is_staff_response' => Auth::user()->hasRole(['admin', 'moderator']),
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Bình luận đã được thêm.',
            'comment' => $comment,
        ]);
    }
}
