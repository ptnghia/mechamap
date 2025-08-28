<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UnifiedUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommentImageUploadController extends Controller
{
    protected $uploadService;

    public function __construct(UnifiedUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Upload images for comment system
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'images' => 'required|array|max:5',
                'images.*' => 'required|file|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
                'context' => 'nullable|string|in:comment,reply',
                'comment_id' => 'nullable|integer|exists:comments,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $context = $request->input('context', 'comment');
            $commentId = $request->input('comment_id');
            
            // Upload files
            $uploadedFiles = $this->uploadService->uploadMultipleFiles(
                $request->file('images'),
                $user,
                'comments', // Category for comment images
                [
                    'mediable_type' => $commentId ? 'App\\Models\\Comment' : null,
                    'mediable_id' => $commentId,
                    'is_public' => true,
                    'is_approved' => true,
                ]
            );

            if (empty($uploadedFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files were uploaded successfully'
                ], 400);
            }

            // Format response data
            $responseData = [];
            foreach ($uploadedFiles as $media) {
                $responseData[] = [
                    'id' => $media->id,
                    'url' => asset($media->file_path),
                    'filename' => $media->file_name,
                    'size' => $media->file_size,
                    'mime_type' => $media->mime_type,
                    'thumbnail_url' => $this->generateThumbnailUrl($media->file_path)
                ];
            }

            Log::info('Comment images uploaded successfully', [
                'user_id' => $user->id,
                'context' => $context,
                'comment_id' => $commentId,
                'files_count' => count($uploadedFiles)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            Log::error('Comment image upload failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete uploaded image
     *
     * @param Request $request
     * @param int $mediaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $mediaId)
    {
        try {
            $user = Auth::user();
            
            // Find media record
            $media = \App\Models\Media::where('id', $mediaId)
                ->where('user_id', $user->id)
                ->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or unauthorized'
                ], 404);
            }

            // Delete physical file
            $filePath = public_path($media->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete database record
            $media->delete();

            Log::info('Comment image deleted successfully', [
                'user_id' => $user->id,
                'media_id' => $mediaId,
                'file_path' => $media->file_path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Comment image deletion failed', [
                'user_id' => Auth::id(),
                'media_id' => $mediaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate thumbnail URL for image
     *
     * @param string $filePath
     * @return string
     */
    private function generateThumbnailUrl($filePath)
    {
        // For now, return the original image URL
        // In the future, you could implement thumbnail generation
        return asset($filePath);
    }

    /**
     * Get upload progress (for future implementation)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function progress(Request $request)
    {
        // This could be implemented with Redis or session storage
        // to track upload progress for large files
        return response()->json([
            'success' => true,
            'progress' => 100
        ]);
    }
}
