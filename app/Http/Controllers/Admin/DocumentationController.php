<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\DocumentationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    /**
     * Display a listing of documentations
     */
    public function index(Request $request)
    {
        $query = Documentation::with(['category', 'author'])
                              ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by content type
        if ($request->filled('content_type')) {
            $query->where('content_type', $request->content_type);
        }

        // Filter by difficulty
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        $documentations = $query->paginate(20);
        $categories = DocumentationCategory::active()->orderBy('name')->get();

        return view('admin.documentation.index', compact('documentations', 'categories'));
    }

    /**
     * Show the form for creating a new documentation
     */
    public function create()
    {
        $categories = DocumentationCategory::active()->orderBy('name')->get();
        $contentTypes = ['guide', 'api', 'tutorial', 'reference', 'faq'];
        $difficultyLevels = ['beginner', 'intermediate', 'advanced', 'expert'];
        $statuses = ['draft', 'review', 'published', 'archived'];
        $userRoles = ['admin', 'moderator', 'senior_member', 'member', 'supplier', 'manufacturer', 'brand'];

        return view('admin.documentation.create', compact(
            'categories', 'contentTypes', 'difficultyLevels', 'statuses', 'userRoles'
        ));
    }

    /**
     * Store a newly created documentation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:documentations,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:documentation_categories,id',
            'status' => 'required|in:draft,review,published,archived',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'allowed_roles' => 'nullable|array',
            'content_type' => 'required|in:guide,api,tutorial,reference,faq',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'related_docs' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'featured_image' => 'nullable|image|max:2048',
            'attachments.*' => 'nullable|file|max:10240',
            'downloadable_files.*' => 'nullable|file|max:51200',
        ]);

        // Handle slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle tags
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('documentation/images', 'public');
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('documentation/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        // Handle downloadable files
        if ($request->hasFile('downloadable_files')) {
            $downloadableFiles = [];
            foreach ($request->file('downloadable_files') as $file) {
                $path = $file->store('documentation/downloads', 'public');
                $downloadableFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $validated['downloadable_files'] = $downloadableFiles;
        }

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $documentation = Documentation::create($validated);

        // Create initial version
        $documentation->createVersion(Auth::user(), 'Tạo tài liệu mới');

        return redirect()
            ->route('admin.documentation.show', $documentation)
            ->with('success', 'Tài liệu đã được tạo thành công!');
    }

    /**
     * Display the specified documentation
     */
    public function show(Documentation $documentation)
    {
        $documentation->load(['category', 'author', 'reviewer', 'versions.user']);
        
        $statistics = [
            'views_today' => $documentation->views()->whereDate('created_at', today())->count(),
            'views_this_week' => $documentation->views()->where('created_at', '>=', now()->subWeek())->count(),
            'views_this_month' => $documentation->views()->where('created_at', '>=', now()->subMonth())->count(),
            'average_time_spent' => $documentation->views()->avg('time_spent'),
            'average_scroll' => $documentation->views()->avg('scroll_percentage'),
            'downloads_count' => $documentation->downloads()->count(),
            'comments_count' => $documentation->comments()->count(),
        ];

        return view('admin.documentation.show', compact('documentation', 'statistics'));
    }

    /**
     * Show the form for editing the specified documentation
     */
    public function edit(Documentation $documentation)
    {
        $categories = DocumentationCategory::active()->orderBy('name')->get();
        $contentTypes = ['guide', 'api', 'tutorial', 'reference', 'faq'];
        $difficultyLevels = ['beginner', 'intermediate', 'advanced', 'expert'];
        $statuses = ['draft', 'review', 'published', 'archived'];
        $userRoles = ['admin', 'moderator', 'senior_member', 'member', 'supplier', 'manufacturer', 'brand'];
        
        // Get available documents for related docs (excluding current)
        $availableDocs = Documentation::where('id', '!=', $documentation->id)
                                    ->where('status', 'published')
                                    ->orderBy('title')
                                    ->get(['id', 'title']);

        return view('admin.documentation.edit', compact(
            'documentation', 'categories', 'contentTypes', 'difficultyLevels', 
            'statuses', 'userRoles', 'availableDocs'
        ));
    }

    /**
     * Update the specified documentation
     */
    public function update(Request $request, Documentation $documentation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:documentations,slug,' . $documentation->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:documentation_categories,id',
            'status' => 'required|in:draft,review,published,archived',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'allowed_roles' => 'nullable|array',
            'content_type' => 'required|in:guide,api,tutorial,reference,faq',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'related_docs' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'featured_image' => 'nullable|image|max:2048',
            'attachments.*' => 'nullable|file|max:10240',
            'downloadable_files.*' => 'nullable|file|max:51200',
            'change_summary' => 'nullable|string|max:500',
        ]);

        // Store original content for version comparison
        $originalContent = $documentation->content;

        // Handle slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle tags
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($documentation->featured_image) {
                Storage::disk('public')->delete($documentation->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('documentation/images', 'public');
        }

        // Handle file uploads (similar to store method)
        // ... (file handling code similar to store method)

        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $documentation->status !== 'published') {
            $validated['published_at'] = now();
        }

        // Set reviewed_at if status changed to published and there's a reviewer
        if ($validated['status'] === 'published' && Auth::user()->hasRole(['admin', 'moderator'])) {
            $validated['reviewer_id'] = Auth::id();
            $validated['reviewed_at'] = now();
        }

        $documentation->update($validated);

        // Create new version if content changed
        if ($originalContent !== $documentation->content) {
            $changeSummary = $request->change_summary ?? 'Cập nhật nội dung';
            $documentation->createVersion(Auth::user(), $changeSummary);
        }

        return redirect()
            ->route('admin.documentation.show', $documentation)
            ->with('success', 'Tài liệu đã được cập nhật thành công!');
    }

    /**
     * Remove the specified documentation
     */
    public function destroy(Documentation $documentation)
    {
        // Delete associated files
        if ($documentation->featured_image) {
            Storage::disk('public')->delete($documentation->featured_image);
        }

        if ($documentation->attachments) {
            foreach ($documentation->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        if ($documentation->downloadable_files) {
            foreach ($documentation->downloadable_files as $file) {
                Storage::disk('public')->delete($file['path']);
            }
        }

        $documentation->delete();

        return redirect()
            ->route('admin.documentation.index')
            ->with('success', 'Tài liệu đã được xóa thành công!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,feature,unfeature',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:documentations,id',
        ]);

        $documentations = Documentation::whereIn('id', $request->selected_ids);

        switch ($request->action) {
            case 'delete':
                $documentations->delete();
                $message = 'Đã xóa các tài liệu được chọn.';
                break;
            case 'publish':
                $documentations->update(['status' => 'published', 'published_at' => now()]);
                $message = 'Đã xuất bản các tài liệu được chọn.';
                break;
            case 'unpublish':
                $documentations->update(['status' => 'draft']);
                $message = 'Đã hủy xuất bản các tài liệu được chọn.';
                break;
            case 'feature':
                $documentations->update(['is_featured' => true]);
                $message = 'Đã đánh dấu nổi bật các tài liệu được chọn.';
                break;
            case 'unfeature':
                $documentations->update(['is_featured' => false]);
                $message = 'Đã bỏ đánh dấu nổi bật các tài liệu được chọn.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
