<?php

namespace App\Services;

use App\Models\ProtectedFile;
use App\Models\TechnicalProduct;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileEncryptionService
{
    private string $encryptionMethod = 'AES-256-CBC';

    /**
     * Encrypt and store an uploaded file
     */
    public function encryptFile(UploadedFile $file, int $productId): array
    {
        // Generate unique encryption key for this file
        $fileKey = $this->generateFileKey($productId, $file->getClientOriginalName());

        // Read file content
        $fileContent = file_get_contents($file->getPathname());

        // Generate IV for encryption
        $iv = openssl_random_pseudo_bytes(16);

        // Encrypt file content
        $encryptedContent = openssl_encrypt(
            $fileContent,
            $this->encryptionMethod,
            $fileKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encryptedContent === false) {
            throw new \Exception('Failed to encrypt file');
        }

        // Generate secure filename
        $encryptedFilename = hash('sha256', $fileKey . time()) . '.enc';

        // Prepare storage path
        $storagePath = "protected/{$productId}/{$encryptedFilename}";

        // Store encrypted file (IV + encrypted content)
        $success = Storage::disk('local')->put($storagePath, $iv . $encryptedContent);

        if (!$success) {
            throw new \Exception('Failed to store encrypted file');
        }

        return [
            'original_filename' => $file->getClientOriginalName(),
            'encrypted_filename' => $encryptedFilename,
            'file_path' => $storagePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'encryption_key' => base64_encode($fileKey),
            'encryption_method' => $this->encryptionMethod,
            'file_hash' => hash('sha256', $encryptedContent),
        ];
    }

    /**
     * Decrypt a protected file
     */
    public function decryptFile(ProtectedFile $file, string $downloadToken): string
    {
        // Verify download permission
        $this->verifyDownloadPermission($file, $downloadToken);

        // Get encrypted content from storage
        $encryptedData = Storage::disk('local')->get($file->file_path);

        if (!$encryptedData) {
            throw new \Exception('Encrypted file not found');
        }

        // Extract IV and encrypted content
        $iv = substr($encryptedData, 0, 16);
        $encryptedContent = substr($encryptedData, 16);

        // Decrypt with file-specific key
        $decryptedContent = openssl_decrypt(
            $encryptedContent,
            $file->encryption_method,
            base64_decode($file->encryption_key),
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decryptedContent === false) {
            throw new \Exception('Failed to decrypt file');
        }

        // Verify file integrity
        if (hash('sha256', $encryptedContent) !== $file->file_hash) {
            throw new \Exception('File integrity check failed');
        }

        return $decryptedContent;
    }

    /**
     * Create a protected file record for an uploaded file
     */
    public function createProtectedFile(
        UploadedFile $file,
        TechnicalProduct $product,
        string $fileType = 'cad_file',
        string $accessLevel = 'full_access',
        string $description = null
    ): ProtectedFile {

        // Encrypt the file
        $encryptionData = $this->encryptFile($file, $product->id);

        // Create protected file record
        return ProtectedFile::create([
            'product_id' => $product->id,
            'file_type' => $fileType,
            'access_level' => $accessLevel,
            'description' => $description,
            ...$encryptionData
        ]);
    }

    /**
     * Verify download permission for a file
     */
    private function verifyDownloadPermission(ProtectedFile $file, string $downloadToken): void
    {
        // For now, we'll implement basic token verification
        // In a full implementation, this would check the SecureDownload record

        if (empty($downloadToken)) {
            throw new \Exception('Download token is required');
        }

        // Additional verification logic would go here
        // - Check if token exists in secure_downloads table
        // - Check if token is not expired
        // - Check if download limit is not exceeded
    }

    /**
     * Generate encryption key for a file
     */
    private function generateFileKey(int $productId, string $filename): string
    {
        return hash('sha256', config('app.key') . $productId . $filename . time());
    }

    /**
     * Delete encrypted file from storage
     */
    public function deleteEncryptedFile(ProtectedFile $file): bool
    {
        try {
            return Storage::disk('local')->delete($file->file_path);
        } catch (\Exception $e) {
            \Log::error('Failed to delete encrypted file: ' . $e->getMessage(), [
                'file_id' => $file->id,
                'file_path' => $file->file_path
            ]);
            return false;
        }
    }

    /**
     * Get file info without decrypting
     */
    public function getFileInfo(ProtectedFile $file): array
    {
        return [
            'original_filename' => $file->original_filename,
            'file_size' => $file->file_size,
            'mime_type' => $file->mime_type,
            'file_type' => $file->file_type,
            'access_level' => $file->access_level,
            'formatted_size' => $file->formatted_size,
            'extension' => $file->extension,
            'is_cad_file' => $file->isCadFile(),
            'is_document' => $file->isDocument(),
            'is_image' => $file->isImage(),
        ];
    }

    /**
     * Validate file before encryption
     */
    public function validateFile(UploadedFile $file, array $allowedTypes = []): void
    {
        // Check file size (max 100MB)
        $maxSize = 100 * 1024 * 1024; // 100MB in bytes
        if ($file->getSize() > $maxSize) {
            throw new \Exception('File size exceeds maximum limit of 100MB');
        }

        // Check allowed file types if specified
        if (!empty($allowedTypes)) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedTypes)) {
                throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedTypes));
            }
        }

        // Check for malicious files
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $dangerousExtensions)) {
            throw new \Exception('File type not allowed for security reasons');
        }
    }

    /**
     * Get encryption statistics
     */
    public function getEncryptionStats(): array
    {
        $totalFiles = ProtectedFile::count();
        $totalSize = ProtectedFile::sum('file_size');
        $activeFiles = ProtectedFile::where('is_active', true)->count();

        return [
            'total_files' => $totalFiles,
            'active_files' => $activeFiles,
            'total_size_bytes' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'encryption_method' => $this->encryptionMethod,
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
