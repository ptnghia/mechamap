<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileAttachmentService
{
    /**
     * Allowed file types and their configurations
     */
    private const ALLOWED_TYPES = [
        'cad' => [
            'extensions' => ['dwg', 'step', 'stp', 'iges', 'igs', 'sat', 'x_t', 'prt', 'asm', 'ipt', 'iam'],
            'max_size' => 52428800, // 50MB
            'icon' => 'fas fa-cube',
            'color' => '#fd7e14'
        ],
        'document' => [
            'extensions' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
            'max_size' => 52428800, // 50MB
            'icon' => 'fas fa-file-alt',
            'color' => '#dc3545'
        ],
        'spreadsheet' => [
            'extensions' => ['xls', 'xlsx', 'csv'],
            'max_size' => 52428800, // 50MB
            'icon' => 'fas fa-file-excel',
            'color' => '#198754'
        ],
        'presentation' => [
            'extensions' => ['ppt', 'pptx'],
            'max_size' => 52428800, // 50MB
            'icon' => 'fas fa-file-powerpoint',
            'color' => '#0d6efd'
        ],
        'image' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'],
            'max_size' => 10485760, // 10MB
            'icon' => 'fas fa-file-image',
            'color' => '#6f42c1'
        ],
        'archive' => [
            'extensions' => ['zip', 'rar', '7z', 'tar', 'gz'],
            'max_size' => 52428800, // 50MB
            'icon' => 'fas fa-file-archive',
            'color' => '#6c757d'
        ],
        'calculation' => [
            'extensions' => ['m', 'mat', 'nb', 'mw', 'mcd'],
            'max_size' => 52428800, // 50MB
            'icon' => 'fas fa-calculator',
            'color' => '#20c997'
        ]
    ];

    /**
     * Maximum number of files allowed
     */
    private const MAX_FILES = 10;

    /**
     * Validate uploaded files
     */
    public function validateFiles(array $files): array
    {
        $errors = [];

        if (count($files) > self::MAX_FILES) {
            $errors[] = "Tối đa " . self::MAX_FILES . " files được phép upload.";
            return $errors;
        }

        foreach ($files as $index => $file) {
            if (!$file instanceof UploadedFile) {
                $errors[] = "File thứ " . ($index + 1) . " không hợp lệ.";
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $fileType = $this->getFileType($extension);

            if (!$fileType) {
                $errors[] = "File '{$file->getClientOriginalName()}' có định dạng không được hỗ trợ.";
                continue;
            }

            $maxSize = self::ALLOWED_TYPES[$fileType]['max_size'];
            if ($file->getSize() > $maxSize) {
                $errors[] = "File '{$file->getClientOriginalName()}' quá lớn. Tối đa " . $this->formatFileSize($maxSize) . ".";
            }
        }

        return $errors;
    }

    /**
     * Process and store uploaded files
     */
    public function processFiles(array $files, int $userId): array
    {
        $processedFiles = [];

        foreach ($files as $index => $file) {
            $fileName = $this->generateFileName($file);
            $filePath = $file->storeAs("public/uploads/showcases/{$userId}/attachments", $fileName);

            $processedFiles[] = [
                'path' => $filePath,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'uploaded_at' => now()->toISOString(),
                'order' => $index + 1,
                'category' => $this->getFileType($file->getClientOriginalExtension()),
                'download_count' => 0,
                'is_public' => true
            ];
        }

        return $processedFiles;
    }

    /**
     * Get file type based on extension
     */
    public function getFileType(string $extension): ?string
    {
        $extension = strtolower($extension);

        foreach (self::ALLOWED_TYPES as $type => $config) {
            if (in_array($extension, $config['extensions'])) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Get file type configuration
     */
    public function getFileTypeConfig(string $type): ?array
    {
        return self::ALLOWED_TYPES[$type] ?? null;
    }

    /**
     * Generate unique filename
     */
    private function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($baseName);
        
        return time() . '_' . uniqid() . '_' . $safeName . '.' . $extension;
    }

    /**
     * Format file size for display
     */
    public function formatFileSize(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Delete files from storage
     */
    public function deleteFiles(array $fileAttachments): void
    {
        foreach ($fileAttachments as $file) {
            if (isset($file['path'])) {
                Storage::delete($file['path']);
            }
        }
    }

    /**
     * Get all allowed extensions
     */
    public function getAllowedExtensions(): array
    {
        $extensions = [];
        foreach (self::ALLOWED_TYPES as $config) {
            $extensions = array_merge($extensions, $config['extensions']);
        }
        return $extensions;
    }

    /**
     * Get file icon and color for display
     */
    public function getFileDisplay(string $extension): array
    {
        $type = $this->getFileType($extension);
        if (!$type) {
            return [
                'icon' => 'fas fa-file',
                'color' => '#adb5bd',
                'type' => 'unknown'
            ];
        }

        $config = self::ALLOWED_TYPES[$type];
        return [
            'icon' => $config['icon'],
            'color' => $config['color'],
            'type' => $type
        ];
    }

    /**
     * Generate download URL for file
     */
    public function getDownloadUrl(array $fileData, int $showcaseId): string
    {
        return route('showcase.download', [
            'showcase' => $showcaseId,
            'file' => base64_encode($fileData['path'])
        ]);
    }
}
