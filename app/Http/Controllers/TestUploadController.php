<?php

namespace App\Http\Controllers;

use App\Services\UnifiedUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestUploadController extends Controller
{
    private UnifiedUploadService $uploadService;

    public function __construct(UnifiedUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Show test upload form
     */
    public function index()
    {
        return view('test.upload');
    }

    /**
     * Test single file upload
     */
    public function testSingleUpload(Request $request)
    {
        // Debug: Log request data
        \Log::info('Test upload request', [
            'has_file' => $request->hasFile('file'),
            'files' => $request->allFiles(),
            'all_data' => $request->all()
        ]);

        try {
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'category' => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 400);
        }

        $user = Auth::user();
        $file = $request->file('file');
        $category = $request->input('category', 'test');

        try {
            $media = $this->uploadService->uploadFile($file, $user, $category);

            if ($media) {
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'data' => $this->uploadService->getFileInfo($media)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Upload service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test multiple files upload
     */
    public function testMultipleUpload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240',
            'category' => 'required|string'
        ]);

        $user = Auth::user();
        $files = $request->file('files');
        $category = $request->input('category', 'test');

        try {
            $mediaList = $this->uploadService->uploadMultipleFiles($files, $user, $category);

            $results = [];
            foreach ($mediaList as $media) {
                $results[] = $this->uploadService->getFileInfo($media);
            }

            return response()->json([
                'success' => true,
                'message' => count($results) . ' files uploaded successfully',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test file validation
     */
    public function testValidation(Request $request)
    {
        $user = Auth::user();
        $file = $request->file('file');
        $category = $request->input('category', 'test');

        // Test with strict validation
        $options = [
            'max_size' => 1024 * 1024, // 1MB only
            'allowed_extensions' => ['jpg', 'png'], // Only images
            'allowed_mime_types' => ['image/jpeg', 'image/png']
        ];

        try {
            $media = $this->uploadService->uploadFile($file, $user, $category, $options);

            if ($media) {
                return response()->json([
                    'success' => true,
                    'message' => 'File passed validation and uploaded',
                    'data' => $this->uploadService->getFileInfo($media)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user upload stats
     */
    public function getUserStats()
    {
        $user = Auth::user();
        $stats = $this->uploadService->getUserUploadStats($user);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Test file deletion
     */
    public function testDelete(Request $request)
    {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id'
        ]);

        $mediaId = $request->input('media_id');
        $media = \App\Models\Media::findOrFail($mediaId);

        // Check if user owns the file
        if ($media->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $success = $this->uploadService->deleteFile($media);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'File deleted successfully' : 'Delete failed'
        ]);
    }

    /**
     * List user files
     */
    public function listFiles(Request $request)
    {
        $user = Auth::user();
        $category = $request->input('category');

        $query = \App\Models\Media::where('user_id', $user->id);

        if ($category) {
            $query->where('file_category', $category);
        }

        $media = $query->latest()->paginate(20);

        $results = [];
        foreach ($media as $item) {
            $results[] = $this->uploadService->getFileInfo($item);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'pagination' => [
                'current_page' => $media->currentPage(),
                'total_pages' => $media->lastPage(),
                'total_items' => $media->total()
            ]
        ]);
    }

    /**
     * Test directory cleanup
     */
    public function testCleanup()
    {
        $user = Auth::user();
        $this->uploadService->cleanupEmptyDirectories($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Cleanup completed'
        ]);
    }
}
