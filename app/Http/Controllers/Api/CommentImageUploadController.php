<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UnifiedUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            // Log incoming request for debugging
            Log::info('Comment image upload request received', [
                'user_id' => Auth::id(),
                'files' => $request->hasFile('images') ? count($request->file('images')) : 'no files',
                'context' => $request->input('context'),
                'all_data' => $request->all()
            ]);

            // Collect uploaded files in a unified way (supports images[], images[0], FormData append patterns)
            $rawFiles = [];

            // 1. Standard multiple input name="images[]" (Laravel exposes as 'images')
            if ($request->hasFile('images')) {
                $candidate = $request->file('images');
                if (is_array($candidate)) {
                    $rawFiles = array_merge($rawFiles, $candidate);
                } elseif ($candidate) {
                    $rawFiles[] = $candidate; // single file edge case
                }
            }

            // 2. Indexed keys images.0, images.1 (some JS libs append this way)
            if (empty($rawFiles)) { // only scan if not already populated
                foreach ($request->allFiles() as $key => $file) {
                    // allFiles flattens nested arrays already; we still check key names for safety
                    if ($file && (Str::startsWith($key, 'images') || preg_match('/^images(\.|\[)/', $key))) {
                        if (is_array($file)) {
                            $rawFiles = array_merge($rawFiles, $file);
                        } else {
                            $rawFiles[] = $file;
                        }
                    }
                }
            }

            // Build a synthetic structure for validator so we can always use images.*
            $prepared = $request->all();
            if (!empty($rawFiles)) {
                $prepared['images'] = $rawFiles; // override / inject
            }

            $rules = [
                'context' => 'nullable|string|in:comment,reply,showcase-rating,inline-reply',
                'comment_id' => 'nullable|integer|exists:comments,id',
                'images' => 'required|array|min:1|max:5',
                'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp|max:5120'
            ];

            if (empty($rawFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No images found in request',
                    'debug_info' => [
                        'all_files_keys' => array_keys($request->allFiles()),
                        'all_input_keys' => array_keys($request->all()),
                        'php_files_superglobal' => array_keys($_FILES ?? []),
                    ]
                ], 422);
            }

            $validator = Validator::make($prepared, $rules);

            if ($validator->fails()) {
                Log::error('Comment image upload validation failed', [
                    'user_id' => Auth::id(),
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $context = $request->input('context', 'comment');
            $commentId = $request->input('comment_id');

            // Use the unified collected files
            $files = $rawFiles;

            if (empty($files)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid files found'
                ], 400);
            }

            // Upload files
            $uploadedFiles = $this->uploadService->uploadMultipleFiles(
                $files,
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
                    'url' => $media->url, // Sử dụng getUrlAttribute() từ Media model
                    'filename' => $media->file_name,
                    'size' => $media->file_size,
                    'mime_type' => $media->mime_type,
                    'thumbnail_url' => $media->url // Sử dụng cùng URL cho thumbnail
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
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
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
