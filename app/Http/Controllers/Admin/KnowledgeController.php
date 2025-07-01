<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeVideo;
use App\Models\KnowledgeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KnowledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Stats for dashboard
        $stats = [
            'articles_count' => KnowledgeArticle::count(),
            'videos_count' => KnowledgeVideo::count(),
            'documents_count' => KnowledgeDocument::count(),
            'total_views' => KnowledgeArticle::sum('views_count') + KnowledgeVideo::sum('views_count'),
            'published_articles' => KnowledgeArticle::where('status', 'published')->count(),
            'draft_articles' => KnowledgeArticle::where('status', 'draft')->count(),
            'published_videos' => KnowledgeVideo::where('status', 'published')->count(),
            'draft_videos' => KnowledgeVideo::where('status', 'draft')->count(),
            'public_documents' => KnowledgeDocument::where('status', 'published')->count(),
            'private_documents' => KnowledgeDocument::where('status', 'draft')->count(),
        ];

        // Recent content
        $recent_articles = KnowledgeArticle::with('author')
            ->latest()
            ->limit(5)
            ->get();

        // Popular categories
        $popular_categories = KnowledgeCategory::withCount(['articles', 'videos', 'documents'])
            ->orderByDesc('articles_count')
            ->limit(4)
            ->get();

        return view('admin.knowledge.index', compact('stats', 'recent_articles', 'popular_categories'));
    }

    /**
     * Display articles management
     */
    public function articles()
    {
        $articles = KnowledgeArticle::with(['author', 'category'])
            ->latest()
            ->paginate(20);

        $categories = KnowledgeCategory::where('is_active', true)->get();

        return view('admin.knowledge.articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function createArticle()
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.articles.create', compact('categories'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:knowledge_categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = Auth::id();

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('knowledge/articles', 'public');
        }

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $article = KnowledgeArticle::create($validated);

        return redirect()->route('admin.knowledge.articles')
            ->with('success', 'Bài viết đã được tạo thành công!');
    }

    /**
     * Display videos management
     */
    public function videos()
    {
        $videos = KnowledgeVideo::with(['author', 'category'])
            ->latest()
            ->paginate(20);

        $categories = KnowledgeCategory::where('is_active', true)->get();

        return view('admin.knowledge.videos.index', compact('videos', 'categories'));
    }

    /**
     * Show the form for creating a new video.
     */
    public function createVideo()
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.videos.create', compact('categories'));
    }

    /**
     * Store a newly created video in storage.
     */
    public function storeVideo(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'video_type' => 'required|in:youtube,vimeo,local',
            'thumbnail' => 'nullable|image|max:2048',
            'duration' => 'nullable|integer|min:1',
            'category_id' => 'required|exists:knowledge_categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = Auth::id();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('knowledge/videos', 'public');
        }

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $video = KnowledgeVideo::create($validated);

        return redirect()->route('admin.knowledge.videos')
            ->with('success', 'Video đã được tạo thành công!');
    }

    /**
     * Display documents management
     */
    public function documents()
    {
        $documents = KnowledgeDocument::with(['author', 'category'])
            ->latest()
            ->paginate(20);

        $categories = KnowledgeCategory::where('is_active', true)->get();

        return view('admin.knowledge.documents.index', compact('documents', 'categories'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function createDocument()
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.documents.create', compact('categories'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function storeDocument(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'category_id' => 'required|exists:knowledge_categories,id',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = Auth::id();

        // Handle document file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $validated['file_path'] = $file->store('knowledge/documents', 'public');
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $document = KnowledgeDocument::create($validated);

        return redirect()->route('admin.knowledge.documents')
            ->with('success', 'Tài liệu đã được tạo thành công!');
    }

    /**
     * Display categories management
     */
    public function categories()
    {
        $categories = KnowledgeCategory::withCount(['articles', 'videos', 'documents'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.knowledge.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function createCategory()
    {
        $parent_categories = KnowledgeCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return view('admin.knowledge.categories.create', compact('parent_categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:knowledge_categories,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = KnowledgeCategory::create($validated);

        return redirect()->route('admin.knowledge.categories')
            ->with('success', 'Danh mục đã được tạo thành công!');
    }

    /**
     * Show the form for editing an article.
     */
    public function editArticle(KnowledgeArticle $article)
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update an article in storage.
     */
    public function updateArticle(Request $request, KnowledgeArticle $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:knowledge_categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('knowledge/articles', 'public');
        }

        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $article->status !== 'published') {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.knowledge.articles')
            ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Remove an article from storage.
     */
    public function destroyArticle(KnowledgeArticle $article)
    {
        // Delete featured image if exists
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()->route('admin.knowledge.articles')
            ->with('success', 'Bài viết đã được xóa thành công!');
    }

    /**
     * Show the form for editing a video.
     */
    public function editVideo(KnowledgeVideo $video)
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.videos.edit', compact('video', 'categories'));
    }

    /**
     * Update a video in storage.
     */
    public function updateVideo(Request $request, KnowledgeVideo $video)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'video_type' => 'required|in:youtube,vimeo,local',
            'thumbnail' => 'nullable|image|max:2048',
            'duration' => 'nullable|integer|min:1',
            'category_id' => 'required|exists:knowledge_categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('knowledge/videos', 'public');
        }

        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $video->status !== 'published') {
            $validated['published_at'] = now();
        }

        $video->update($validated);

        return redirect()->route('admin.knowledge.videos')
            ->with('success', 'Video đã được cập nhật thành công!');
    }

    /**
     * Remove a video from storage.
     */
    public function destroyVideo(KnowledgeVideo $video)
    {
        // Delete thumbnail if exists
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }

        $video->delete();

        return redirect()->route('admin.knowledge.videos')
            ->with('success', 'Video đã được xóa thành công!');
    }

    /**
     * Show the form for editing a document.
     */
    public function editDocument(KnowledgeDocument $document)
    {
        $categories = KnowledgeCategory::where('is_active', true)->get();
        return view('admin.knowledge.documents.edit', compact('document', 'categories'));
    }

    /**
     * Update a document in storage.
     */
    public function updateDocument(Request $request, KnowledgeDocument $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'category_id' => 'required|exists:knowledge_categories,id',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Handle document file upload
        if ($request->hasFile('document_file')) {
            // Delete old file if exists
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('document_file');
            $validated['file_path'] = $file->store('knowledge/documents', 'public');
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $document->status !== 'published') {
            $validated['published_at'] = now();
        }

        $document->update($validated);

        return redirect()->route('admin.knowledge.documents')
            ->with('success', 'Tài liệu đã được cập nhật thành công!');
    }

    /**
     * Remove a document from storage.
     */
    public function destroyDocument(KnowledgeDocument $document)
    {
        // Delete file if exists
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('admin.knowledge.documents')
            ->with('success', 'Tài liệu đã được xóa thành công!');
    }

    /**
     * Show the form for editing a category.
     */
    public function editCategory(KnowledgeCategory $category)
    {
        $parent_categories = KnowledgeCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.knowledge.categories.edit', compact('category', 'parent_categories'));
    }

    /**
     * Update a category in storage.
     */
    public function updateCategory(Request $request, KnowledgeCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:knowledge_categories,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.knowledge.categories')
            ->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    /**
     * Remove a category from storage.
     */
    public function destroyCategory(KnowledgeCategory $category)
    {
        // Check if category has children or content
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.knowledge.categories')
                ->with('error', 'Không thể xóa danh mục có danh mục con!');
        }

        if ($category->articles()->count() > 0 || $category->videos()->count() > 0 || $category->documents()->count() > 0) {
            return redirect()->route('admin.knowledge.categories')
                ->with('error', 'Không thể xóa danh mục có nội dung!');
        }

        $category->delete();

        return redirect()->route('admin.knowledge.categories')
            ->with('success', 'Danh mục đã được xóa thành công!');
    }

    /**
     * Bulk delete articles
     */
    public function bulkDeleteArticles(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:knowledge_articles,id'
        ]);

        $articles = KnowledgeArticle::whereIn('id', $validated['ids'])->get();

        foreach ($articles as $article) {
            // Delete featured image if exists
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $article->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ' . count($validated['ids']) . ' bài viết thành công!'
        ]);
    }

    /**
     * Bulk delete videos
     */
    public function bulkDeleteVideos(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:knowledge_videos,id'
        ]);

        $videos = KnowledgeVideo::whereIn('id', $validated['ids'])->get();

        foreach ($videos as $video) {
            // Delete thumbnail if exists
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $video->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ' . count($validated['ids']) . ' video thành công!'
        ]);
    }

    /**
     * Bulk delete documents
     */
    public function bulkDeleteDocuments(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:knowledge_documents,id'
        ]);

        $documents = KnowledgeDocument::whereIn('id', $validated['ids'])->get();

        foreach ($documents as $document) {
            // Delete file if exists
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ' . count($validated['ids']) . ' tài liệu thành công!'
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:articles,videos,documents',
            'ids' => 'required|array',
            'status' => 'required|in:draft,published,archived'
        ]);

        $model = match($validated['type']) {
            'articles' => KnowledgeArticle::class,
            'videos' => KnowledgeVideo::class,
            'documents' => KnowledgeDocument::class,
        };

        $count = $model::whereIn('id', $validated['ids'])
            ->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật trạng thái cho {$count} mục thành công!"
        ]);
    }

    /**
     * Search content
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $category = $request->get('category');
        $status = $request->get('status');

        $results = [];

        if ($type === 'all' || $type === 'articles') {
            $articlesQuery = KnowledgeArticle::with(['author', 'category'])
                ->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%");

            if ($category) {
                $articlesQuery->where('category_id', $category);
            }
            if ($status) {
                $articlesQuery->where('status', $status);
            }

            $results['articles'] = $articlesQuery->limit(10)->get();
        }

        if ($type === 'all' || $type === 'videos') {
            $videosQuery = KnowledgeVideo::with(['author', 'category'])
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");

            if ($category) {
                $videosQuery->where('category_id', $category);
            }
            if ($status) {
                $videosQuery->where('status', $status);
            }

            $results['videos'] = $videosQuery->limit(10)->get();
        }

        if ($type === 'all' || $type === 'documents') {
            $documentsQuery = KnowledgeDocument::with(['author', 'category'])
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");

            if ($category) {
                $documentsQuery->where('category_id', $category);
            }
            if ($status) {
                $documentsQuery->where('status', $status);
            }

            $results['documents'] = $documentsQuery->limit(10)->get();
        }

        return response()->json($results);
    }
}
