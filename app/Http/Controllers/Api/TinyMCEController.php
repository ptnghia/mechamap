<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TinyMCEController extends Controller
{
    /**
     * Upload image for TinyMCE editor
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'file' => 'sometimes|file|mimes:pdf,doc,docx,dwg,dxf,step,stp,stl,obj,iges,igs|max:51200', // 50MB max for files
                'filetype' => 'sometimes|string|in:image,file,media'
            ]);

            $uploadedFile = null;
            $filetype = $request->input('filetype', 'image');

            // Handle image upload
            if ($request->hasFile('image')) {
                $uploadedFile = $this->processImageUpload($request->file('image'));
            }
            // Handle file upload
            elseif ($request->hasFile('file')) {
                $uploadedFile = $this->processFileUpload($request->file('file'), $filetype);
            }

            if (!$uploadedFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có file nào được tải lên.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'url' => $uploadedFile['url'],
                'filename' => $uploadedFile['filename'],
                'size' => $uploadedFile['size'],
                'message' => 'File đã được tải lên thành công.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('TinyMCE upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lên file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process image upload
     */
    private function processImageUpload($image): array
    {
        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        
        // Create directory if it doesn't exist
        $directory = 'images/tinymce';
        $fullPath = public_path($directory);
        
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Move file to public directory
        $image->move($fullPath, $filename);

        // Get file info
        $filePath = $fullPath . '/' . $filename;
        $fileSize = filesize($filePath);

        return [
            'url' => asset($directory . '/' . $filename),
            'filename' => $filename,
            'size' => $fileSize,
            'type' => 'image'
        ];
    }

    /**
     * Process file upload (documents, CAD files, etc.)
     */
    private function processFileUpload($file, $filetype): array
    {
        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Determine directory based on file type
        $directory = match($filetype) {
            'media' => 'files/tinymce/media',
            'file' => 'files/tinymce/documents',
            default => 'files/tinymce/misc'
        };

        $fullPath = public_path($directory);
        
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Move file to public directory
        $file->move($fullPath, $filename);

        // Get file info
        $filePath = $fullPath . '/' . $filename;
        $fileSize = filesize($filePath);

        return [
            'url' => asset($directory . '/' . $filename),
            'filename' => $filename,
            'size' => $fileSize,
            'type' => $filetype
        ];
    }

    /**
     * Get uploaded files for a specific context
     */
    public function getFiles(Request $request): JsonResponse
    {
        $request->validate([
            'context' => 'required|string|in:thread,comment,showcase,admin',
            'entity_id' => 'nullable|integer'
        ]);

        try {
            // This could be extended to track uploaded files per context
            // For now, return empty array as files are handled directly
            return response()->json([
                'success' => true,
                'files' => [],
                'message' => 'Files retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách file.'
            ], 500);
        }
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile(Request $request): JsonResponse
    {
        $request->validate([
            'filename' => 'required|string',
            'type' => 'required|string|in:image,file,media'
        ]);

        try {
            $filename = $request->input('filename');
            $type = $request->input('type');

            // Determine directory based on type
            $directory = match($type) {
                'image' => 'images/tinymce',
                'media' => 'files/tinymce/media',
                'file' => 'files/tinymce/documents',
                default => 'files/tinymce/misc'
            };

            $filePath = public_path($directory . '/' . $filename);

            if (file_exists($filePath)) {
                unlink($filePath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File đã được xóa thành công.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại.'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa file.'
            ], 500);
        }
    }

    /**
     * Get TinyMCE configuration for different contexts
     */
    public function getConfig(Request $request): JsonResponse
    {
        $request->validate([
            'context' => 'required|string|in:comment,admin,showcase,minimal'
        ]);

        $context = $request->input('context');
        
        $configs = [
            'comment' => [
                'height' => 200,
                'plugins' => [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'table', 'wordcount', 'emoticons', 'autosave'
                ],
                'toolbar' => [
                    'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright',
                    'bullist numlist | outdent indent | blockquote | link image | emoticons | code fullscreen'
                ]
            ],
            'admin' => [
                'height' => 400,
                'plugins' => [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
                    'autosave', 'save'
                ],
                'toolbar' => [
                    'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor',
                    'alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
                    'blockquote | link image media | table | code fullscreen | save'
                ]
            ],
            'showcase' => [
                'height' => 250,
                'plugins' => [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'table', 'wordcount', 'emoticons', 'autosave'
                ],
                'toolbar' => [
                    'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright',
                    'bullist numlist | outdent indent | blockquote | link image | table | emoticons | fullscreen'
                ]
            ],
            'minimal' => [
                'height' => 150,
                'plugins' => ['autolink', 'lists', 'link', 'emoticons'],
                'toolbar' => 'bold italic underline | bullist numlist | link emoticons'
            ]
        ];

        return response()->json([
            'success' => true,
            'config' => $configs[$context] ?? $configs['comment'],
            'upload_url' => route('api.tinymce.upload'),
            'csrf_token' => csrf_token()
        ]);
    }
}
