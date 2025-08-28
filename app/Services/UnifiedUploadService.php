<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UnifiedUploadService
{
    /**
     * Upload file với cấu trúc thống nhất: public/uploads/{user_id}/{category}/
     */
    public function uploadFile(
        UploadedFile $file,
        User $user,
        string $category,
        array $options = []
    ): ?Media {
        try {
            // Get file info immediately before any operations
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Validate file
            $this->validateFile($file, $options);

            // Generate file info
            $fileName = $this->generateFileName($file, $options);
            $filePath = $this->generateFilePath($user->id, $category, $fileName);

            // Create directory if not exists
            $this->ensureDirectoryExists($user->id, $category);

            // Move file to destination
            $fullPath = public_path($filePath);
            if (!$file->move(dirname($fullPath), basename($fullPath))) {
                throw new \Exception('Failed to move uploaded file');
            }

            // Create media record with pre-captured file info
            return $this->createMediaRecordWithInfo($originalName, $fileSize, $mimeType, $user, $filePath, $category, $options);

        } catch (\Exception $e) {
            \Log::error('Upload failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'category' => $category,
                'file_name' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles(
        array $files,
        User $user,
        string $category,
        array $options = []
    ): array {
        $results = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $media = $this->uploadFile($file, $user, $category, $options);
                if ($media) {
                    $results[] = $media;
                }
            }
        }

        return $results;
    }

    /**
     * Generate file path: uploads/{user_id}/{category}/{filename}
     */
    private function generateFilePath(int $userId, string $category, string $fileName): string
    {
        return "uploads/{$userId}/{$category}/{$fileName}";
    }

    /**
     * Generate unique file name
     */
    private function generateFileName(UploadedFile $file, array $options = []): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Use custom name if provided
        if (!empty($options['custom_name'])) {
            return $options['custom_name'] . '.' . $extension;
        }

        // Generate unique name
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = Str::slug($baseName);
        $timestamp = time();
        $random = Str::random(6);

        return "{$safeName}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Ensure directory exists
     */
    private function ensureDirectoryExists(int $userId, string $category): void
    {
        $dirPath = public_path("uploads/{$userId}/{$category}");

        if (!File::exists($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file, array $options = []): void
    {
        // Check file size (default 10MB)
        $maxSize = $options['max_size'] ?? 10 * 1024 * 1024;
        $fileSize = $file->getSize();

        if ($fileSize === false || $fileSize === null) {
            throw new \Exception('Cannot determine file size');
        }

        if ($fileSize > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size');
        }

        // Check allowed extensions
        $allowedExtensions = $options['allowed_extensions'] ?? [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', // Images
            'pdf', 'doc', 'docx', 'txt', // Documents
            'dwg', 'dxf', 'step', 'stp', 'iges', 'igs' // CAD files
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('File type not allowed');
        }

        // Check MIME type for security
        $allowedMimeTypes = $options['allowed_mime_types'] ?? [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/octet-stream' // For CAD files
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('Invalid file MIME type');
        }
    }

    /**
     * Create media record in database with pre-captured file info
     */
    private function createMediaRecordWithInfo(
        string $originalName,
        int $fileSize,
        string $mimeType,
        User $user,
        string $filePath,
        string $category,
        array $options = []
    ): Media {
        // Map category to valid enum values
        $validCategories = [
            'cad_drawing', 'cad_model', 'technical_doc', 'image', 'simulation', 'other'
        ];

        $mappedCategory = $this->mapCategoryToEnum($category, $mimeType);

        $data = [
            'user_id' => $user->id,
            'file_name' => $originalName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'file_extension' => pathinfo($originalName, PATHINFO_EXTENSION),
            'file_category' => $mappedCategory,
            'is_public' => $options['is_public'] ?? true,
            'is_approved' => $options['is_approved'] ?? true,
            'processing_status' => 'completed',
            'virus_scanned' => 0,
            'download_count' => 0,
            'disk' => 'public',
            // Set default values for polymorphic relationship
            'mediable_type' => $options['mediable_type'] ?? 'App\\Models\\User',
            'mediable_id' => $options['mediable_id'] ?? $user->id,
        ];

        // Only add description if it's provided and the column exists
        if (isset($options['description']) && \Schema::hasColumn('media', 'description')) {
            $data['description'] = $options['description'];
        }

        return Media::create($data);
    }

    /**
     * Create media record in database (legacy method)
     */
    private function createMediaRecord(
        UploadedFile $file,
        User $user,
        string $filePath,
        string $category,
        array $options = []
    ): Media {
        return Media::create([
            'user_id' => $user->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'file_extension' => $file->getClientOriginalExtension(),
            'file_category' => $category,
            'is_public' => $options['is_public'] ?? true,
            'is_approved' => $options['is_approved'] ?? true,
            'description' => $options['description'] ?? null,
        ]);
    }

    /**
     * Delete file and media record
     */
    public function deleteFile(Media $media): bool
    {
        try {
            // Delete physical file
            $fullPath = public_path($media->file_path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            // Delete media record
            $media->delete();

            return true;
        } catch (\Exception $e) {
            \Log::error('Delete file failed: ' . $e->getMessage(), [
                'media_id' => $media->id,
                'file_path' => $media->file_path
            ]);
            return false;
        }
    }

    /**
     * Get file URL
     */
    public function getFileUrl(Media $media): string
    {
        return asset($media->file_path);
    }

    /**
     * Get file info
     */
    public function getFileInfo(Media $media): array
    {
        $fullPath = public_path($media->file_path);

        return [
            'id' => $media->id,
            'name' => $media->file_name,
            'path' => $media->file_path,
            'url' => $this->getFileUrl($media),
            'size' => $media->file_size,
            'size_human' => $this->formatFileSize($media->file_size),
            'mime_type' => $media->mime_type,
            'extension' => $media->file_extension,
            'category' => $media->file_category,
            'exists' => File::exists($fullPath),
            'created_at' => $media->created_at,
            'user' => $media->user ? $media->user->name : 'Unknown'
        ];
    }

    /**
     * Format file size to human readable
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get user upload statistics
     */
    public function getUserUploadStats(User $user): array
    {
        $media = Media::where('user_id', $user->id);

        return [
            'total_files' => $media->count(),
            'total_size' => $media->sum('file_size'),
            'total_size_human' => $this->formatFileSize($media->sum('file_size')),
            'by_category' => $media->groupBy('file_category')
                ->selectRaw('file_category, count(*) as count, sum(file_size) as size')
                ->get()
                ->keyBy('file_category')
                ->toArray(),
            'recent_uploads' => $media->latest()->limit(10)->get()
        ];
    }

    /**
     * Clean up empty directories
     */
    public function cleanupEmptyDirectories(int $userId): void
    {
        $userDir = public_path("uploads/{$userId}");

        if (File::exists($userDir)) {
            $this->removeEmptyDirectories($userDir);
        }
    }

    /**
     * Recursively remove empty directories
     */
    private function removeEmptyDirectories(string $path): void
    {
        if (!File::isDirectory($path)) {
            return;
        }

        $files = File::files($path);
        $directories = File::directories($path);

        // Clean subdirectories first
        foreach ($directories as $directory) {
            $this->removeEmptyDirectories($directory);
        }

        // Remove this directory if it's empty
        if (empty(File::files($path)) && empty(File::directories($path))) {
            File::deleteDirectory($path);
        }
    }

    /**
     * Map category to valid enum values
     */
    private function mapCategoryToEnum(string $category, string $mimeType): string
    {
        // Direct mapping for valid enum values
        $validCategories = [
            'cad_drawing', 'cad_model', 'technical_doc', 'image', 'simulation', 'other'
        ];

        if (in_array($category, $validCategories)) {
            return $category;
        }

        // Map based on MIME type
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if ($mimeType === 'application/pdf') {
            return 'technical_doc';
        }

        // Map common categories
        $categoryMappings = [
            'test' => 'other',
            'gallery' => 'image',
            'documents' => 'technical_doc',
            'comments' => 'other',
            'threads' => 'other',
            'showcases' => 'image',
            'avatars' => 'image',
        ];

        return $categoryMappings[$category] ?? 'other';
    }
}
