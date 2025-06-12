<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class MediaController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Hiển thị danh sách media
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Media::with(['user'])
                          ->orderBy('created_at', 'desc');

            // Filter by category
            if ($request->has('category') && $request->category !== 'all') {
                $query->where('file_category', $request->category);
            }

            // Filter by user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Search by filename
            if ($request->has('search')) {
                $query->where('file_name', 'like', '%' . $request->search . '%');
            }

            // Filter by approval status
            if ($request->has('status')) {
                if ($request->status === 'approved') {
                    $query->where('is_approved', true);
                } elseif ($request->status === 'pending') {
                    $query->where('is_approved', false);
                }
            }

            $media = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $media,
                'categories' => $this->getMediaCategories(),
                'message' => 'Danh sách media files'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload media files
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'required|file|max:102400', // 100MB max
            'category' => 'required|string|in:avatar,image,cad_drawing,cad_model,technical_doc,simulation,thread_attachment,showcase,general',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean'
        ]);

        try {
            $user = Auth::user();
            $uploadedMedia = [];

            foreach ($request->file('files') as $file) {
                $media = $this->mediaService->uploadFile(
                    $file,
                    $user,
                    $request->category,
                    $request->description
                );

                // Update public status if provided
                if ($request->has('is_public')) {
                    $media->update(['is_public' => $request->boolean('is_public')]);
                }

                $uploadedMedia[] = $media->load('user');
            }

            return response()->json([
                'success' => true,
                'data' => $uploadedMedia,
                'message' => 'Upload thành công ' . count($uploadedMedia) . ' files'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get media statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_files' => Media::count(),
                'total_size' => Media::sum('file_size'),
                'files_by_category' => Media::selectRaw('file_category, COUNT(*) as count, SUM(file_size) as total_size')
                                          ->groupBy('file_category')
                                          ->get(),
                'files_by_user' => Media::selectRaw('user_id, COUNT(*) as count')
                                       ->with('user:id,name')
                                       ->groupBy('user_id')
                                       ->orderBy('count', 'desc')
                                       ->limit(10)
                                       ->get(),
                'pending_approval' => Media::where('is_approved', false)->count(),
                'recent_uploads' => Media::where('created_at', '>=', now()->subDays(7))->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Media statistics'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve media file
     */
    public function approve(Media $media): JsonResponse
    {
        try {
            $media->update([
                'is_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $media->load('user'),
                'message' => 'Media đã được phê duyệt'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete media file
     */
    public function destroy(Media $media): JsonResponse
    {
        try {
            // Check permission
            if (!Auth::user()->hasRole(['admin', 'moderator']) && Auth::id() !== $media->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xóa file này'
                ], 403);
            }

            $deleted = $this->mediaService->deleteMedia($media);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa media thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa file'
                ], 500);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's media organized by category
     */
    public function userMedia(Request $request): JsonResponse
    {
        try {
            $user = $request->user() ?? Auth::user();
            $category = $request->get('category');

            $mediaByCategory = $this->mediaService->getUserMediaByCategory($user, $category);
            $storageStats = $this->mediaService->getUserStorageStats($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'media_by_category' => $mediaByCategory,
                    'storage_stats' => $storageStats
                ],
                'message' => 'User media data'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download media file
     */
    public function download(Media $media): mixed
    {
        try {
            // Check if file exists
            if (!Storage::disk($media->disk)->exists($media->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại'
                ], 404);
            }

            // Check permission for private files
            if (!$media->is_public && !$this->canAccessPrivateFile($media)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền truy cập file này'
                ], 403);
            }

            // Increment download count
            $media->increment('download_count');

            // Return file download
            return Storage::disk($media->disk)->download(
                $media->file_path,
                $media->file_name
            );

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if current user can access private file
     */
    private function canAccessPrivateFile(Media $media): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Owner can access
        if ($user->id === $media->user_id) {
            return true;
        }

        // Admin/moderator can access
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        // TODO: Add more complex permission logic based on forum membership, etc.

        return false;
    }

    /**
     * Get available media categories
     */
    private function getMediaCategories(): array
    {
        return [
            'avatar' => 'Avatar người dùng',
            'image' => 'Hình ảnh chung',
            'cad_drawing' => 'Bản vẽ CAD',
            'cad_model' => 'Model 3D CAD',
            'technical_doc' => 'Tài liệu kỹ thuật',
            'simulation' => 'File mô phỏng',
            'thread_attachment' => 'File đính kèm thread',
            'showcase' => 'Showcase projects',
            'other' => 'Khác'
        ];
    }
}
