<?php

namespace App\Http\Controllers\Dashboard\Community;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Showcase;
use App\Models\ShowcaseCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Showcase Controller cho Dashboard Community
 *
 * Quản lý showcases của user trong dashboard
 */
class ShowcaseController extends BaseController
{
    /**
     * Hiển thị danh sách showcases của user
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $category = $request->get('category');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $query = Showcase::with(['category', 'thread'])
            ->where('user_id', $this->user->id);

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'views':
                $query->orderByDesc('view_count');
                break;
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $showcases = $query->paginate(20);

        // Get categories for filter
        $categories = ShowcaseCategory::orderBy('name')->get();

        // Get statistics
        $stats = $this->getShowcaseStats();

        return $this->dashboardResponse('dashboard.community.showcases.index', [
            'showcases' => $showcases,
            'categories' => $categories,
            'stats' => $stats,
            'currentStatus' => $status,
            'currentCategory' => $category,
            'search' => $search,
            'currentSort' => $sort
        ]);
    }

    /**
     * Hiển thị form tạo showcase mới
     */
    public function create()
    {
        $categories = ShowcaseCategory::orderBy('name')->get();

        return $this->dashboardResponse('dashboard.community.showcases.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Lưu showcase mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category_id' => 'required|exists:showcase_categories,id',
            'thread_id' => 'nullable|exists:threads,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240', // 10MB max
            'tags' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
        ]);

        $showcase = Showcase::create([
            'user_id' => $this->user->id,
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'thread_id' => $request->thread_id,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'is_featured' => $request->boolean('is_featured'),
            'status' => 'pending', // Default to pending approval
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($showcase, $request->file('images'));
        }

        // Handle attachment uploads
        if ($request->hasFile('attachments')) {
            $this->handleAttachmentUploads($showcase, $request->file('attachments'));
        }

        return redirect()->route('dashboard.community.showcases.show', $showcase)
            ->with('success', 'Showcase created successfully and is pending approval.');
    }

    /**
     * Hiển thị showcase cụ thể
     */
    public function show(Showcase $showcase)
    {
        if ($showcase->user_id !== $this->user->id) {
            abort(403, 'You can only view your own showcases.');
        }

        $showcase->load(['category', 'thread', 'images', 'attachments', 'ratings.user']);

        return $this->dashboardResponse('dashboard.community.showcases.show', [
            'showcase' => $showcase
        ]);
    }

    /**
     * Hiển thị form chỉnh sửa showcase
     */
    public function edit(Showcase $showcase)
    {
        if ($showcase->user_id !== $this->user->id) {
            abort(403, 'You can only edit your own showcases.');
        }

        $categories = ShowcaseCategory::orderBy('name')->get();

        return $this->dashboardResponse('dashboard.community.showcases.edit', [
            'showcase' => $showcase,
            'categories' => $categories
        ]);
    }

    /**
     * Cập nhật showcase
     */
    public function update(Request $request, Showcase $showcase)
    {
        if ($showcase->user_id !== $this->user->id) {
            abort(403, 'You can only edit your own showcases.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category_id' => 'required|exists:showcase_categories,id',
            'tags' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
        ]);

        $showcase->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('dashboard.community.showcases.show', $showcase)
            ->with('success', 'Showcase updated successfully.');
    }

    /**
     * Xóa showcase
     */
    public function destroy(Showcase $showcase): JsonResponse
    {
        if ($showcase->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Delete associated files
        $this->deleteShowcaseFiles($showcase);

        $showcase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Showcase deleted successfully.'
        ]);
    }

    /**
     * Lấy thống kê showcases
     */
    private function getShowcaseStats()
    {
        $total = Showcase::where('user_id', $this->user->id)->count();
        $published = Showcase::where('user_id', $this->user->id)
            ->where('status', 'approved')->count();
        $pending = Showcase::where('user_id', $this->user->id)
            ->where('status', 'pending')->count();
        $featured = Showcase::where('user_id', $this->user->id)
            ->where('status', 'featured')->count();

        $totalViews = Showcase::where('user_id', $this->user->id)->sum('view_count');
        $totalRatings = Showcase::where('user_id', $this->user->id)->sum('rating_count');
        $averageRating = Showcase::where('user_id', $this->user->id)
            ->where('rating_count', '>', 0)
            ->avg('rating_average');

        return [
            'total' => $total,
            'published' => $published,
            'pending' => $pending,
            'featured' => $featured,
            'total_views' => $totalViews,
            'total_ratings' => $totalRatings,
            'average_rating' => round($averageRating, 2),
            'this_month' => Showcase::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];
    }

    /**
     * Handle image uploads
     */
    private function handleImageUploads(Showcase $showcase, array $images)
    {
        foreach ($images as $image) {
            $path = $image->store('showcases/images', 'public');

            $showcase->images()->create([
                'file_path' => $path,
                'file_name' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
            ]);
        }
    }

    /**
     * Handle attachment uploads
     */
    private function handleAttachmentUploads(Showcase $showcase, array $attachments)
    {
        foreach ($attachments as $attachment) {
            $path = $attachment->store('showcases/attachments', 'public');

            $showcase->attachments()->create([
                'file_path' => $path,
                'file_name' => $attachment->getClientOriginalName(),
                'file_size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
            ]);
        }
    }

    /**
     * Delete showcase files
     */
    private function deleteShowcaseFiles(Showcase $showcase)
    {
        // Delete images
        foreach ($showcase->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }

        // Delete attachments
        foreach ($showcase->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }
    }
}
