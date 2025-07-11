<?php

namespace App\Services;

use App\Models\User;
use App\Models\BusinessVerificationDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Document Security Service
 * 
 * Handles secure file storage, encryption, access control,
 * and security validation for business verification documents
 */
class DocumentSecurityService
{
    // Allowed file types with their MIME types
    const ALLOWED_TYPES = [
        'pdf' => ['application/pdf'],
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'document' => [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
    ];

    // Maximum file sizes (in bytes)
    const MAX_FILE_SIZES = [
        'pdf' => 10 * 1024 * 1024,      // 10MB
        'image' => 5 * 1024 * 1024,     // 5MB
        'document' => 10 * 1024 * 1024, // 10MB
    ];

    // Dangerous file extensions to block
    const BLOCKED_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
        'php', 'asp', 'aspx', 'jsp', 'py', 'rb', 'pl', 'sh'
    ];

    /**
     * Store file securely with encryption and validation
     */
    public function storeSecurely(UploadedFile $file, string $directory): string
    {
        // Validate file security
        $this->validateFileType($file);
        $this->scanForMalware($file->getPathname());

        // Generate secure filename
        $secureFilename = $this->generateSecureFilename($file);
        
        // Create full path
        $fullPath = $directory . '/' . $secureFilename;

        // Store file
        $storedPath = Storage::putFileAs(
            $directory,
            $file,
            $secureFilename,
            'private' // Use private disk for security
        );

        if (!$storedPath) {
            throw new \Exception('Failed to store file securely');
        }

        Log::info('File stored securely', [
            'original_name' => $file->getClientOriginalName(),
            'secure_path' => $storedPath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return $storedPath;
    }

    /**
     * Encrypt file content
     */
    public function encryptFile(string $filePath): string
    {
        if (!Storage::exists($filePath)) {
            throw new \Exception('File not found for encryption');
        }

        $content = Storage::get($filePath);
        $encryptedContent = Crypt::encrypt($content);
        
        $encryptedPath = $filePath . '.encrypted';
        Storage::put($encryptedPath, $encryptedContent);

        // Delete original file
        Storage::delete($filePath);

        Log::info('File encrypted successfully', [
            'original_path' => $filePath,
            'encrypted_path' => $encryptedPath,
        ]);

        return $encryptedPath;
    }

    /**
     * Decrypt file content
     */
    public function decryptFile(string $encryptedPath): string
    {
        if (!Storage::exists($encryptedPath)) {
            throw new \Exception('Encrypted file not found');
        }

        $encryptedContent = Storage::get($encryptedPath);
        $decryptedContent = Crypt::decrypt($encryptedContent);

        $tempPath = 'temp/' . Str::random(40);
        Storage::put($tempPath, $decryptedContent);

        return $tempPath;
    }

    /**
     * Generate secure filename
     */
    public function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(16);
        
        return "{$timestamp}_{$randomString}.{$extension}";
    }

    /**
     * Validate file type and security
     */
    public function validateFileType(UploadedFile $file): bool
    {
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, self::BLOCKED_EXTENSIONS)) {
            throw new \Exception("File extension '{$extension}' is not allowed for security reasons");
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        $allowedMimes = array_merge(...array_values(self::ALLOWED_TYPES));
        
        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception("File type '{$mimeType}' is not allowed");
        }

        // Check file size
        $fileSize = $file->getSize();
        $maxSize = $this->getMaxFileSize($mimeType);
        
        if ($fileSize > $maxSize) {
            $maxSizeMB = round($maxSize / 1024 / 1024, 2);
            throw new \Exception("File size exceeds maximum allowed size of {$maxSizeMB}MB");
        }

        // Validate file header (magic bytes)
        if (!$this->validateFileHeader($file)) {
            throw new \Exception('File header validation failed - file may be corrupted or malicious');
        }

        return true;
    }

    /**
     * Scan file for malware (basic implementation)
     */
    public function scanForMalware(string $filePath): bool
    {
        // Basic malware detection - check for suspicious patterns
        $suspiciousPatterns = [
            '<?php',
            '<script',
            'eval(',
            'exec(',
            'system(',
            'shell_exec(',
            'passthru(',
        ];

        $content = file_get_contents($filePath);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                throw new \Exception('File contains suspicious content and may be malicious');
            }
        }

        // Additional checks for executable files
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        if (str_contains($mimeType, 'executable')) {
            throw new \Exception('Executable files are not allowed');
        }

        return true;
    }

    /**
     * Generate access token for secure document access
     */
    public function generateAccessToken(BusinessVerificationDocument $document, User $user): string
    {
        $payload = [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'expires_at' => now()->addHours(2)->timestamp,
            'permissions' => ['view', 'download'],
        ];

        return base64_encode(Crypt::encrypt(json_encode($payload)));
    }

    /**
     * Validate access token
     */
    public function validateAccess(string $token, User $user): bool
    {
        try {
            $payload = json_decode(Crypt::decrypt(base64_decode($token)), true);
            
            // Check expiration
            if ($payload['expires_at'] < now()->timestamp) {
                return false;
            }

            // Check user
            if ($payload['user_id'] !== $user->id) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::warning('Invalid access token', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Log document access
     */
    public function logAccess(BusinessVerificationDocument $document, User $user): void
    {
        $document->logAccess($user);

        Log::info('Document accessed', [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'application_id' => $document->application_id,
            'access_count' => $document->access_count + 1,
        ]);
    }

    /**
     * Create thumbnail for images
     */
    public function createThumbnail(string $filePath): ?string
    {
        try {
            if (!Storage::exists($filePath)) {
                return null;
            }

            $fullPath = Storage::path($filePath);
            $pathInfo = pathinfo($filePath);
            $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];

            // Create thumbnail using Intervention Image
            $image = Image::make($fullPath);
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save thumbnail
            $thumbnailFullPath = Storage::path($thumbnailPath);
            $image->save($thumbnailFullPath, 80); // 80% quality

            Log::info('Thumbnail created', [
                'original_path' => $filePath,
                'thumbnail_path' => $thumbnailPath,
            ]);

            return $thumbnailPath;

        } catch (\Exception $e) {
            Log::error('Failed to create thumbnail', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete file securely
     */
    public function deleteSecurely(string $filePath): bool
    {
        try {
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                
                Log::info('File deleted securely', [
                    'file_path' => $filePath,
                ]);
                
                return true;
            }
            
            return false;

        } catch (\Exception $e) {
            Log::error('Failed to delete file securely', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Helper methods
     */
    protected function getMaxFileSize(string $mimeType): int
    {
        foreach (self::ALLOWED_TYPES as $type => $mimes) {
            if (in_array($mimeType, $mimes)) {
                return self::MAX_FILE_SIZES[$type];
            }
        }

        return self::MAX_FILE_SIZES['document']; // Default
    }

    protected function validateFileHeader(UploadedFile $file): bool
    {
        $filePath = $file->getPathname();
        $handle = fopen($filePath, 'rb');
        
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 16);
        fclose($handle);

        $mimeType = $file->getMimeType();

        // Validate common file headers
        $validHeaders = [
            'application/pdf' => ['%PDF'],
            'image/jpeg' => ["\xFF\xD8\xFF"],
            'image/png' => ["\x89PNG\r\n\x1A\n"],
            'image/gif' => ['GIF87a', 'GIF89a'],
            'application/msword' => ["\xD0\xCF\x11\xE0"],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['PK'],
        ];

        if (!isset($validHeaders[$mimeType])) {
            return true; // Skip validation for unknown types
        }

        foreach ($validHeaders[$mimeType] as $validHeader) {
            if (str_starts_with($header, $validHeader)) {
                return true;
            }
        }

        return false;
    }
}
