<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

/**
 * MediaService - Service xử lý upload và quản lý media files
 *
 * Cải tiến với:
 * - Tổ chức file theo user ID
 * - Categorization theo loại media
 * - Support CAD files cho mechanical engineering
 * - Automatic thumbnail generation
 * - File validation và security
 */
class MediaService
{
    /**
     * Upload file với tổ chức thư mục theo user
     */
    public function uploadFile(
        UploadedFile $file,
        User $user,
        string $category = 'general',
        ?string $description = null,
        array $metadata = []
    ): Media {
        // Validate file
        $this->validateFile($file, $category);

        // Tạo tên file unique
        $fileName = $this->generateFileName($file, $user);

        // Tạo đường dẫn theo user và category
        $relativePath = $this->generateUserPath($user->id, $category, $fileName);

        // Upload file
        $fullPath = $file->storeAs('uploads', $relativePath, 'public');

        // Tạo record trong database
        $media = $this->createMediaRecord($file, $user, $fullPath, $category, $description, $metadata);

        // Tạo thumbnail nếu là image
        if ($this->isImage($file)) {
            $this->generateThumbnail($media, $file);
        }

        // Xử lý CAD metadata nếu là file CAD
        if ($this->isCadFile($file)) {
            $this->processCadMetadata($media, $file);
        }

        return $media;
    }

    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles(
        array $files,
        User $user,
        string $category = 'general',
        ?string $description = null
    ): array {
        $uploadedMedia = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                try {
                    $uploadedMedia[] = $this->uploadFile($file, $user, $category, $description);
                } catch (Exception $e) {
                    // Log error but continue with other files
                    logger()->error('Failed to upload file', [
                        'file' => $file->getClientOriginalName(),
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $uploadedMedia;
    }

    /**
     * Tạo đường dẫn file theo user và category
     */
    private function generateUserPath(int $userId, string $category, string $fileName): string
    {
        // Tạo cấu trúc: users/{user_id}/{category}/{year}/{month}/{filename}
        $year = date('Y');
        $month = date('m');

        return "users/{$userId}/{$category}/{$year}/{$month}/{$fileName}";
    }

    /**
     * Tạo tên file unique và safe
     */
    private function generateFileName(UploadedFile $file, User $user): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        // Sanitize filename
        $safeName = Str::slug($originalName);
        $timestamp = time();
        $userPrefix = substr(md5($user->id), 0, 6);

        return "{$userPrefix}_{$timestamp}_{$safeName}.{$extension}";
    }

    /**
     * Validate file upload
     */
    private function validateFile(UploadedFile $file, string $category): void
    {
        $allowedTypes = $this->getAllowedTypes($category);
        $maxSize = $this->getMaxFileSize($category);

        // Check file type
        if (!in_array($file->getClientOriginalExtension(), $allowedTypes)) {
            throw new Exception("File type không được hỗ trợ cho category {$category}");
        }

        // Check file size
        if ($file->getSize() > $maxSize) {
            throw new Exception("File vượt quá kích thước cho phép " . ($maxSize / 1024 / 1024) . "MB");
        }

        // Basic security check
        if ($this->isSuspiciousFile($file)) {
            throw new Exception("File không an toàn");
        }
    }

    /**
     * Get allowed file types by category
     */
    private function getAllowedTypes(string $category): array
    {
        $typeMap = [
            'avatar' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'cad_drawing' => ['dwg', 'dxf', 'pdf'],
            'cad_model' => ['step', 'stp', 'iges', 'igs', 'stl', 'obj', 'ply'],
            'technical_doc' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            'simulation' => ['cas', 'cdb', 'inp', 'odb', 'sim'],
            'thread_attachment' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'dwg', 'step'],
            'showcase' => ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'pdf'],
            'general' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt']
        ];

        return $typeMap[$category] ?? $typeMap['general'];
    }

    /**
     * Get max file size by category (in bytes)
     */
    private function getMaxFileSize(string $category): int
    {
        $sizeMap = [
            'avatar' => 2 * 1024 * 1024,      // 2MB
            'image' => 5 * 1024 * 1024,       // 5MB
            'cad_drawing' => 50 * 1024 * 1024,  // 50MB
            'cad_model' => 100 * 1024 * 1024,   // 100MB
            'technical_doc' => 20 * 1024 * 1024, // 20MB
            'simulation' => 200 * 1024 * 1024,   // 200MB
            'thread_attachment' => 10 * 1024 * 1024, // 10MB
            'showcase' => 50 * 1024 * 1024,    // 50MB
            'general' => 10 * 1024 * 1024      // 10MB
        ];

        return $sizeMap[$category] ?? $sizeMap['general'];
    }

    /**
     * Create media record in database
     */
    private function createMediaRecord(
        UploadedFile $file,
        User $user,
        string $filePath,
        string $category,
        ?string $description,
        array $metadata
    ): Media {
        return Media::create([
            'user_id' => $user->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_category' => $this->mapCategoryToFileCategory($category),
            'disk' => 'public',
            'processing_status' => 'completed',
            'is_public' => false, // Default private
            'is_approved' => false, // Require approval
            'virus_scanned' => false,
            'width' => $metadata['width'] ?? null,
            'height' => $metadata['height'] ?? null,
            'technical_notes' => $description,
        ]);
    }

    /**
     * Map upload category to file_category enum
     */
    private function mapCategoryToFileCategory(string $category): string
    {
        $categoryMap = [
            'avatar' => 'image',
            'image' => 'image',
            'cad_drawing' => 'cad_drawing',
            'cad_model' => 'cad_model',
            'technical_doc' => 'technical_doc',
            'simulation' => 'simulation',
            'thread_attachment' => 'other',
            'showcase' => 'image',
            'general' => 'other'
        ];

        return $categoryMap[$category] ?? 'other';
    }

    /**
     * Check if file is image
     */
    private function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    /**
     * Check if file is CAD file
     */
    private function isCadFile(UploadedFile $file): bool
    {
        $cadExtensions = ['dwg', 'dxf', 'step', 'stp', 'iges', 'igs', 'stl'];
        return in_array(strtolower($file->getClientOriginalExtension()), $cadExtensions);
    }

    /**
     * Generate thumbnail for images
     */
    private function generateThumbnail(Media $media, UploadedFile $file): void
    {
        try {
            // TODO: Implement thumbnail generation using intervention/image
            // $thumbnail = Image::make($file)->resize(300, 300, function ($constraint) {
            //     $constraint->aspectRatio();
            //     $constraint->upsize();
            // });

            // $thumbnailPath = str_replace('.', '_thumb.', $media->file_path);
            // $thumbnail->save(storage_path("app/public/{$thumbnailPath}"));

            // $media->update(['thumbnail_path' => $thumbnailPath]);

            logger()->info('Thumbnail generation placeholder', ['media_id' => $media->id]);
        } catch (Exception $e) {
            logger()->error('Failed to generate thumbnail', [
                'media_id' => $media->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process CAD file metadata
     */
    private function processCadMetadata(Media $media, UploadedFile $file): void
    {
        try {
            // TODO: Implement CAD metadata extraction
            // This would require specialized libraries for each CAD format

            $metadata = [
                'detected_software' => $this->detectCadSoftware($file),
                'file_version' => null,
                'contains_3d' => $this->contains3dGeometry($file),
                'estimated_complexity' => 'medium'
            ];

            $media->update([
                'cad_metadata' => $metadata,
                'cad_software' => $metadata['detected_software'],
                'processing_status' => 'completed'
            ]);

        } catch (Exception $e) {
            logger()->error('Failed to process CAD metadata', [
                'media_id' => $media->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Detect CAD software from file
     */
    private function detectCadSoftware(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $softwareMap = [
            'dwg' => 'AutoCAD',
            'dxf' => 'AutoCAD',
            'step' => 'SolidWorks/CATIA',
            'stp' => 'SolidWorks/CATIA',
            'iges' => 'CATIA/NX',
            'igs' => 'CATIA/NX',
            'stl' => '3D Printing/CAD'
        ];

        return $softwareMap[$extension] ?? 'Unknown';
    }

    /**
     * Check if CAD file contains 3D geometry
     */
    private function contains3dGeometry(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $threeDFormats = ['step', 'stp', 'iges', 'igs', 'stl'];

        return in_array($extension, $threeDFormats);
    }

    /**
     * Basic security check for uploaded files
     */
    private function isSuspiciousFile(UploadedFile $file): bool
    {
        $suspiciousExtensions = ['php', 'exe', 'bat', 'cmd', 'scr', 'jar'];
        $extension = strtolower($file->getClientOriginalExtension());

        return in_array($extension, $suspiciousExtensions);
    }

    /**
     * Delete media file and record
     */
    public function deleteMedia(Media $media): bool
    {
        try {
            // Delete physical file
            if (Storage::disk($media->disk)->exists($media->file_path)) {
                Storage::disk($media->disk)->delete($media->file_path);
            }

            // Delete thumbnail if exists
            if ($media->thumbnail_path && Storage::disk($media->disk)->exists($media->thumbnail_path)) {
                Storage::disk($media->disk)->delete($media->thumbnail_path);
            }

            // Delete database record
            $media->delete();

            return true;
        } catch (Exception $e) {
            logger()->error('Failed to delete media', [
                'media_id' => $media->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user's media organized by category
     */
    public function getUserMediaByCategory(User $user, string $category = null): array
    {
        $query = $user->media();

        if ($category) {
            $fileCategory = $this->mapCategoryToFileCategory($category);
            $query->where('file_category', $fileCategory);
        }

        return $query->orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy('file_category')
                    ->toArray();
    }

    /**
     * Get media storage statistics for user
     */
    public function getUserStorageStats(User $user): array
    {
        $media = $user->media;

        return [
            'total_files' => $media->count(),
            'total_size' => $media->sum('file_size'),
            'size_by_category' => $media->groupBy('file_category')
                                       ->map(fn($items) => $items->sum('file_size')),
            'files_by_category' => $media->groupBy('file_category')
                                        ->map(fn($items) => $items->count()),
            'recent_uploads' => $media->where('created_at', '>=', now()->subDays(7))->count()
        ];
    }
}
