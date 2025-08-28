<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductFileUploadService
{
    /**
     * Upload digital files for products
     */
    public function uploadDigitalFiles(array $files): array
    {
        $uploadedFiles = [];
        $totalSize = 0;

        foreach ($files as $file) {
            try {
                $result = $this->uploadDigitalFile($file);
                $uploadedFiles[] = $result;
                $totalSize += $result['size'];
            } catch (\Exception $e) {
                Log::error('Error uploading digital file: ' . $e->getMessage(), [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        return [
            'files' => $uploadedFiles,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / (1024 * 1024), 2)
        ];
    }

    /**
     * Upload a single digital file
     */
    private function uploadDigitalFile(UploadedFile $file): array
    {
        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(10) . '.' . $extension;
        
        // Store file in private storage
        $path = $file->storeAs('digital-products', $filename, 'private');

        // Verify file was uploaded successfully
        if (!Storage::disk('private')->exists($path)) {
            throw new \Exception('Failed to store digital file: ' . $file->getClientOriginalName());
        }

        return [
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'uploaded_at' => now()->toISOString(),
        ];
    }

    /**
     * Upload product images
     */
    public function uploadProductImages(array $images, bool $optimize = true): array
    {
        $uploadedImages = [];

        foreach ($images as $image) {
            try {
                $result = $this->uploadProductImage($image, $optimize);
                $uploadedImages[] = $result;
            } catch (\Exception $e) {
                Log::error('Error uploading product image: ' . $e->getMessage(), [
                    'image' => $image->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        return $uploadedImages;
    }

    /**
     * Upload a single product image
     */
    public function uploadProductImage(UploadedFile $image, bool $optimize = true): string
    {
        // Generate unique filename
        $extension = $image->getClientOriginalExtension();
        $filename = 'product_' . time() . '_' . Str::random(8) . '.' . $extension;

        if ($optimize && extension_loaded('gd')) {
            return $this->uploadOptimizedImage($image, $filename);
        } else {
            // Fallback to direct upload
            return $image->storeAs('marketplace/products', $filename, 'public');
        }
    }

    /**
     * Upload and optimize image
     */
    private function uploadOptimizedImage(UploadedFile $image, string $filename): string
    {
        try {
            // Create optimized versions
            $originalPath = 'marketplace/products/' . $filename;
            $thumbnailPath = 'marketplace/products/thumbnails/' . $filename;

            // Process original image (max 1200px width)
            $img = Image::make($image->getPathname());
            
            // Resize if too large
            if ($img->width() > 1200) {
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Save original
            Storage::disk('public')->put($originalPath, $img->encode($image->getClientOriginalExtension(), 85));

            // Create thumbnail (300x300)
            $thumbnail = Image::make($image->getPathname());
            $thumbnail->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });

            // Save thumbnail
            Storage::disk('public')->put($thumbnailPath, $thumbnail->encode($image->getClientOriginalExtension(), 80));

            return $originalPath;

        } catch (\Exception $e) {
            Log::warning('Image optimization failed, using direct upload: ' . $e->getMessage());
            // Fallback to direct upload
            return $image->storeAs('marketplace/products', $filename, 'public');
        }
    }

    /**
     * Delete digital files
     */
    public function deleteDigitalFiles(array $filePaths): bool
    {
        $success = true;

        foreach ($filePaths as $path) {
            try {
                if (Storage::disk('private')->exists($path)) {
                    Storage::disk('private')->delete($path);
                }
            } catch (\Exception $e) {
                Log::error('Error deleting digital file: ' . $e->getMessage(), [
                    'path' => $path
                ]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Delete product images
     */
    public function deleteProductImages(array $imagePaths): bool
    {
        $success = true;

        foreach ($imagePaths as $path) {
            try {
                // Delete original image
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }

                // Delete thumbnail if exists
                $thumbnailPath = str_replace('marketplace/products/', 'marketplace/products/thumbnails/', $path);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            } catch (\Exception $e) {
                Log::error('Error deleting product image: ' . $e->getMessage(), [
                    'path' => $path
                ]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Get file size in human readable format
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Validate file type and size
     */
    public function validateFile(UploadedFile $file, array $allowedTypes, int $maxSize): array
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File quá lớn. Kích thước tối đa: ' . self::formatFileSize($maxSize);
        }

        // Check file type
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'Định dạng file không được hỗ trợ. Các định dạng được phép: ' . implode(', ', $allowedTypes);
        }

        // Check MIME type for security
        $mimeType = $file->getMimeType();
        if (str_contains($mimeType, 'php') || str_contains($mimeType, 'executable')) {
            $errors[] = 'File không an toàn.';
        }

        return $errors;
    }

    /**
     * Get storage usage statistics
     */
    public function getStorageStats(): array
    {
        try {
            $digitalPath = storage_path('app/private/digital-products');
            $publicPath = storage_path('app/public/marketplace/products');

            $digitalSize = $this->getDirectorySize($digitalPath);
            $publicSize = $this->getDirectorySize($publicPath);

            return [
                'digital_files' => [
                    'size_bytes' => $digitalSize,
                    'size_formatted' => self::formatFileSize($digitalSize)
                ],
                'product_images' => [
                    'size_bytes' => $publicSize,
                    'size_formatted' => self::formatFileSize($publicSize)
                ],
                'total' => [
                    'size_bytes' => $digitalSize + $publicSize,
                    'size_formatted' => self::formatFileSize($digitalSize + $publicSize)
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting storage stats: ' . $e->getMessage());
            return [
                'digital_files' => ['size_bytes' => 0, 'size_formatted' => '0 B'],
                'product_images' => ['size_bytes' => 0, 'size_formatted' => '0 B'],
                'total' => ['size_bytes' => 0, 'size_formatted' => '0 B']
            ];
        }
    }

    /**
     * Get directory size recursively
     */
    private function getDirectorySize(string $path): int
    {
        if (!is_dir($path)) {
            return 0;
        }

        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }
}
