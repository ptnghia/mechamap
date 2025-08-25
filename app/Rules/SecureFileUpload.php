<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class SecureFileUpload implements ValidationRule
{
    /**
     * Blocked file extensions for security
     */
    private const BLOCKED_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
        'php', 'php3', 'php4', 'php5', 'phtml', 'asp', 'aspx', 'jsp',
        'sh', 'bash', 'zsh', 'fish', 'ps1', 'psm1', 'psd1',
        'msi', 'deb', 'rpm', 'dmg', 'pkg', 'app',
        'htaccess', 'htpasswd', 'ini', 'conf', 'config',
        'sql', 'db', 'sqlite', 'sqlite3'
    ];

    /**
     * Suspicious filename patterns
     */
    private const SUSPICIOUS_PATTERNS = [
        '/\.\./i',           // Directory traversal
        '/[<>:"|?*]/i',      // Invalid filename characters
        '/^(con|prn|aux|nul|com[1-9]|lpt[1-9])$/i', // Windows reserved names
        '/\.(php|asp|jsp|js|vbs|bat|cmd|exe|scr|pif)\./i', // Double extensions
        '/\x00/',            // Null bytes
        '/[\x01-\x1f\x7f-\x9f]/', // Control characters
    ];

    /**
     * Maximum allowed file sizes by type (in bytes)
     */
    private const MAX_FILE_SIZES = [
        'image' => 10 * 1024 * 1024,    // 10MB for images
        'document' => 50 * 1024 * 1024, // 50MB for documents
        'cad' => 100 * 1024 * 1024,     // 100MB for CAD files
        'archive' => 50 * 1024 * 1024,  // 50MB for archives
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        try {
            // 1. Validate file extension
            $this->validateFileExtension($value, $fail);

            // 2. Validate filename security
            $this->validateFilename($value, $fail);

            // 3. Validate file size by type
            $this->validateFileSize($value, $fail);

            // 4. Validate MIME type consistency
            $this->validateMimeType($value, $fail);

            // 5. Validate file headers
            $this->validateFileHeaders($value, $fail);

            // 6. Check for embedded executables
            $this->checkEmbeddedExecutables($value, $fail);

            // Log successful validation
            Log::info('File upload security validation passed', [
                'filename' => $value->getClientOriginalName(),
                'size' => $value->getSize(),
                'mime_type' => $value->getMimeType(),
                'user_id' => auth()->id(),
            ]);

        } catch (\Exception $e) {
            Log::error('File upload security validation failed', [
                'filename' => $value->getClientOriginalName(),
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            
            $fail('File failed security validation: ' . $e->getMessage());
        }
    }

    /**
     * Validate file extension against blocked list
     */
    private function validateFileExtension(UploadedFile $file, Closure $fail): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, self::BLOCKED_EXTENSIONS)) {
            $fail("File extension '.{$extension}' is not allowed for security reasons.");
        }

        // Check for double extensions
        $filename = $file->getClientOriginalName();
        if (preg_match('/\.[^.]+\.[^.]+$/', $filename)) {
            $fail('Files with double extensions are not allowed for security reasons.');
        }
    }

    /**
     * Validate filename for suspicious patterns
     */
    private function validateFilename(UploadedFile $file, Closure $fail): void
    {
        $filename = $file->getClientOriginalName();

        foreach (self::SUSPICIOUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $filename)) {
                $fail('Filename contains suspicious patterns and is not allowed.');
            }
        }

        // Check filename length
        if (strlen($filename) > 255) {
            $fail('Filename is too long. Maximum 255 characters allowed.');
        }

        // Check for Unicode control characters
        if (preg_match('/[\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{206F}]/u', $filename)) {
            $fail('Filename contains invisible Unicode characters and is not allowed.');
        }
    }

    /**
     * Validate file size based on file type
     */
    private function validateFileSize(UploadedFile $file, Closure $fail): void
    {
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        $maxSize = match (true) {
            str_starts_with($mimeType, 'image/') => self::MAX_FILE_SIZES['image'],
            str_starts_with($mimeType, 'application/pdf') => self::MAX_FILE_SIZES['document'],
            in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']) => self::MAX_FILE_SIZES['document'],
            str_contains($mimeType, 'zip') || str_contains($mimeType, 'rar') => self::MAX_FILE_SIZES['archive'],
            default => self::MAX_FILE_SIZES['document']
        };

        if ($fileSize > $maxSize) {
            $maxSizeMB = round($maxSize / (1024 * 1024), 1);
            $fail("File size exceeds maximum allowed size of {$maxSizeMB}MB for this file type.");
        }
    }

    /**
     * Validate MIME type consistency with file extension
     */
    private function validateMimeType(UploadedFile $file, Closure $fail): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        $expectedMimes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'zip' => ['application/zip', 'application/x-zip-compressed'],
            'dwg' => ['application/acad', 'application/x-dwg', 'image/vnd.dwg'],
        ];

        if (isset($expectedMimes[$extension])) {
            if (!in_array($mimeType, $expectedMimes[$extension])) {
                $fail('File extension does not match file content. This may indicate a security threat.');
            }
        }
    }

    /**
     * Validate file headers for consistency
     */
    private function validateFileHeaders(UploadedFile $file, Closure $fail): void
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) {
            $fail('Unable to read file for security validation.');
            return;
        }

        $header = fread($handle, 16);
        fclose($handle);

        // Check for common file signatures
        $signatures = [
            'PDF' => "\x25\x50\x44\x46",
            'ZIP' => "\x50\x4B\x03\x04",
            'JPEG' => "\xFF\xD8\xFF",
            'PNG' => "\x89\x50\x4E\x47",
            'GIF87a' => "\x47\x49\x46\x38\x37\x61",
            'GIF89a' => "\x47\x49\x46\x38\x39\x61",
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        
        // Validate specific file types
        if ($extension === 'pdf' && !str_starts_with($header, $signatures['PDF'])) {
            $fail('File claims to be PDF but header signature does not match.');
        }
        
        if (in_array($extension, ['jpg', 'jpeg']) && !str_starts_with($header, $signatures['JPEG'])) {
            $fail('File claims to be JPEG but header signature does not match.');
        }
        
        if ($extension === 'png' && !str_starts_with($header, $signatures['PNG'])) {
            $fail('File claims to be PNG but header signature does not match.');
        }
    }

    /**
     * Check for embedded executables in files
     */
    private function checkEmbeddedExecutables(UploadedFile $file, Closure $fail): void
    {
        $content = file_get_contents($file->getPathname(), false, null, 0, 8192); // Read first 8KB
        
        // Check for executable signatures
        $executableSignatures = [
            "\x4D\x5A",         // PE executable (Windows)
            "\x7F\x45\x4C\x46", // ELF executable (Linux)
            "\xFE\xED\xFA\xCE", // Mach-O executable (macOS)
            "\xFE\xED\xFA\xCF", // Mach-O 64-bit executable
        ];

        foreach ($executableSignatures as $signature) {
            if (str_contains($content, $signature)) {
                $fail('File contains embedded executable code and is not allowed.');
            }
        }

        // Check for script content in non-script files
        $scriptPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
        ];

        foreach ($scriptPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $fail('File contains suspicious script content and is not allowed.');
            }
        }
    }
}
