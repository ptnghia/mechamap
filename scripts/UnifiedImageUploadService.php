<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Unified Image Upload Service
 * Chuẩn hóa việc upload hình ảnh cho toàn bộ hệ thống
 */
class UnifiedImageUploadService
{
    /**
     * Cấu trúc thư mục chuẩn
     */
    private const DIRECTORY_STRUCTURE = [
        "users" => "users/avatars",
        "threads" => "threads",
        "showcases" => "showcases", 
        "categories" => "categories",
        "forums" => "forums",
        "temp" => "temp"
    ];
    
    /**
     * Upload image với cấu trúc chuẩn
     */
    public function uploadImage(
        UploadedFile $file,
        string $category,
        User $user,
        ?int $entityId = null,
        ?string $entityType = null
    ): Media {
        // Validate
        $this->validateImage($file);
        
        // Generate filename
        $filename = $this->generateFilename($file, $user);
        
        // Generate path
        $relativePath = $this->generatePath($category, $filename);
        
        // Ensure directory exists
        $fullPath = public_path("images/" . dirname($relativePath));
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        // Move file to public/images
        $destinationPath = public_path("images/" . $relativePath);
        $file->move(dirname($destinationPath), basename($destinationPath));
        
        // Create media record
        return $this->createMediaRecord(
            $file, $user, "/images/" . $relativePath, 
            $category, $entityId, $entityType
        );
    }
    
    /**
     * Validate image file
     */
    private function validateImage(UploadedFile $file): void
    {
        $allowedMimes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException("Invalid file type");
        }
        
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException("File too large");
        }
    }
    
    /**
     * Generate standardized filename
     */
    private function generateFilename(UploadedFile $file, User $user): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        $userPrefix = substr(md5($user->id), 0, 6);
        $timestamp = time();
        $safeName = Str::slug($originalName);
        
        return "{$userPrefix}_{$timestamp}_{$safeName}.{$extension}";
    }
    
    /**
     * Generate standardized path
     */
    private function generatePath(string $category, string $filename): string
    {
        $baseDir = self::DIRECTORY_STRUCTURE[$category] ?? "general";
        return "{$baseDir}/{$filename}";
    }
    
    /**
     * Create media record
     */
    private function createMediaRecord(
        UploadedFile $file, User $user, string $path,
        string $category, ?int $entityId, ?string $entityType
    ): Media {
        return Media::create([
            "user_id" => $user->id,
            "file_name" => $file->getClientOriginalName(),
            "file_path" => $path,
            "disk" => "public",
            "mime_type" => $file->getMimeType(),
            "file_size" => $file->getSize(),
            "file_extension" => $file->getClientOriginalExtension(),
            "file_category" => "image",
            "mediable_id" => $entityId,
            "mediable_type" => $entityType,
            "is_public" => true,
            "is_approved" => true
        ]);
    }
}