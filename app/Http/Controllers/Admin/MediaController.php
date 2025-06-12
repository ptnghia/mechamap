<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Hiển thị danh sách media với phân trang và lọc
     */
    public function index(Request $request): JsonResponse
    {
        $query = Media::with(['user'])->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by file type
        if ($request->has('file_type')) {
            switch ($request->file_type) {
                case 'image':
                    $query->where('mime_type', 'like', 'image/%');
                    break;
                case 'document':
                    $query->whereIn('mime_type', [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ]);
                    break;
                case 'cad':
                    $query->whereIn('mime_type', [
                        'application/acad',
                        'application/x-autocad',
                        'application/step',
                        'application/iges'
                    ]);
                    break;
            }
        }

        // Search by filename
        if ($request->has('search')) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        $media = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $media,
            'message' => 'Danh sách media'
        ]);
    }

    /**
     * Upload multiple files
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:51200', // 50MB max per file
            'category' => 'required|string|in:avatar,thread,showcase,page,document,cad',
            'entity_id' => 'nullable|integer',
            'entity_type' => 'nullable|string',
        ]);

        try {
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                $media = $this->mediaService->uploadFile(
                    $file,
                    $request->category,
                    Auth::user(),
                    $request->entity_id,
                    $request->entity_type
                );

                $uploadedFiles[] = $media;
            }

            return response()->json([
                'success' => true,
                'data' => $uploadedFiles,
                'message' => 'Upload files thành công'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị chi tiết media
     */
    public function show(Media $media): JsonResponse
    {
        $media->load(['user']);

        return response()->json([
            'success' => true,
            'data' => $media,
            'message' => 'Chi tiết media'
        ]);
    }

    /**
     * Cập nhật thông tin media
     */
    public function update(Request $request, Media $media): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'alt_text' => 'nullable|string|max:255',
            'is_approved' => 'boolean',
        ]);

        try {
            $media->update($request->only([
                'title',
                'description',
                'alt_text',
                'is_approved'
            ]));

            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Cập nhật media thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật media: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa media
     */
    public function destroy(Media $media): JsonResponse
    {
        try {
            // Xóa file vật lý
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }

            // Xóa thumbnail nếu có
            if ($media->thumbnail_path && Storage::disk('public')->exists($media->thumbnail_path)) {
                Storage::disk('public')->delete($media->thumbnail_path);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa media thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa media: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approval
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'integer|exists:media,id',
            'approved' => 'required|boolean'
        ]);

        try {
            $count = Media::whereIn('id', $request->media_ids)
                ->update(['is_approved' => $request->approved]);

            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật {$count} media"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi bulk approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê media
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_files' => Media::count(),
            'total_size' => Media::sum('file_size'),
            'by_category' => Media::selectRaw('category, COUNT(*) as count, SUM(file_size) as size')
                ->groupBy('category')
                ->get(),
            'by_type' => Media::selectRaw('
                CASE
                    WHEN mime_type LIKE "image/%" THEN "image"
                    WHEN mime_type LIKE "video/%" THEN "video"
                    WHEN mime_type IN ("application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document") THEN "document"
                    WHEN mime_type IN ("application/acad", "application/x-autocad", "application/step", "application/iges") THEN "cad"
                    ELSE "other"
                END as type,
                COUNT(*) as count,
                SUM(file_size) as size
            ')
                ->groupBy('type')
                ->get(),
            'recent_uploads' => Media::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'pending_approval' => Media::where('is_approved', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Thống kê media'
        ]);
    }

    /**
     * Tạo thumbnail cho image
     */
    public function createThumbnail(Media $media): JsonResponse
    {
        try {
            if (!str_starts_with($media->mime_type, 'image/')) {
                throw new \Exception('Chỉ có thể tạo thumbnail cho hình ảnh');
            }

            $thumbnailPath = $this->mediaService->createThumbnail($media);

            $media->update(['thumbnail_path' => $thumbnailPath]);

            return response()->json([
                'success' => true,
                'data' => $media,
                'message' => 'Tạo thumbnail thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo thumbnail: ' . $e->getMessage()
            ], 500);
        }
    }
}
