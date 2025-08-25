<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileContentValidation implements ValidationRule
{
    /**
     * Forbidden content patterns
     */
    private const FORBIDDEN_PATTERNS = [
        // SQL injection attempts
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bUPDATE\b.*\bSET\b)/i',
        
        // XSS attempts
        '/<script[^>]*>.*?<\/script>/is',
        '/<iframe[^>]*>.*?<\/iframe>/is',
        '/javascript:/i',
        '/vbscript:/i',
        '/onload\s*=/i',
        '/onerror\s*=/i',
        '/onclick\s*=/i',
        
        // Command injection
        '/;\s*(cat|ls|pwd|whoami|id|uname)/i',
        '/\|\s*(cat|ls|pwd|whoami|id|uname)/i',
        '/&&\s*(cat|ls|pwd|whoami|id|uname)/i',
        '/`[^`]*`/i',
        '/\$\([^)]*\)/i',
        
        // Path traversal
        '/\.\.\//',
        '/\.\.\\\\/',
        '/%2e%2e%2f/i',
        '/%2e%2e%5c/i',
        
        // PHP code injection
        '/<\?php/i',
        '/<\?=/i',
        '/<%/i',
        '/%>/i',
        
        // Server-side includes
        '/<!--#exec/i',
        '/<!--#include/i',
        
        // LDAP injection
        '/\(\|/i',
        '/\(&/i',
        '/\(!/i',
    ];

    /**
     * Suspicious metadata patterns
     */
    private const SUSPICIOUS_METADATA = [
        'Author' => ['/script/i', '/eval/i', '/exec/i'],
        'Title' => ['/\<script/i', '/javascript:/i'],
        'Subject' => ['/\<iframe/i', '/vbscript:/i'],
        'Creator' => ['/malware/i', '/virus/i', '/trojan/i'],
        'Producer' => ['/hack/i', '/exploit/i'],
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
            // 1. Validate file content
            if (!$this->validateFileContent($value)) {
                $fail('File contains forbidden content patterns.');
                return;
            }

            // 2. Validate metadata
            if (!$this->validateMetadata($value)) {
                $fail('File metadata contains suspicious content.');
                return;
            }

            // 3. Validate embedded objects
            if (!$this->validateEmbeddedObjects($value)) {
                $fail('File contains suspicious embedded objects.');
                return;
            }

            // 4. Check for steganography
            if (!$this->checkSteganography($value)) {
                $fail('File may contain hidden data.');
                return;
            }

            Log::info('File content validation passed', [
                'filename' => $value->getClientOriginalName(),
                'mime_type' => $value->getMimeType(),
                'user_id' => auth()->id(),
            ]);

        } catch (\Exception $e) {
            Log::error('File content validation failed', [
                'filename' => $value->getClientOriginalName(),
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            
            $fail('File content validation failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate file content for forbidden patterns
     */
    private function validateFileContent(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname());
        
        foreach (self::FORBIDDEN_PATTERNS as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::warning('Forbidden content pattern detected', [
                    'filename' => $file->getClientOriginalName(),
                    'pattern' => $pattern,
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        // Check for excessive null bytes (potential buffer overflow)
        $nullCount = substr_count($content, "\x00");
        if ($nullCount > 100) {
            Log::warning('Excessive null bytes detected', [
                'filename' => $file->getClientOriginalName(),
                'null_count' => $nullCount,
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        // Check for binary content in text files
        if ($this->isTextFile($file) && !mb_check_encoding($content, 'UTF-8')) {
            $binaryRatio = $this->calculateBinaryRatio($content);
            if ($binaryRatio > 0.3) { // More than 30% binary content
                Log::warning('Binary content in text file detected', [
                    'filename' => $file->getClientOriginalName(),
                    'binary_ratio' => $binaryRatio,
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate file metadata
     */
    private function validateMetadata(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        
        // Check PDF metadata
        if ($mimeType === 'application/pdf') {
            return $this->validatePdfMetadata($file);
        }
        
        // Check image metadata
        if (str_starts_with($mimeType, 'image/')) {
            return $this->validateImageMetadata($file);
        }
        
        // Check Office document metadata
        if (str_contains($mimeType, 'officedocument') || str_contains($mimeType, 'msword')) {
            return $this->validateOfficeMetadata($file);
        }

        return true;
    }

    /**
     * Validate PDF metadata
     */
    private function validatePdfMetadata(UploadedFile $file): bool
    {
        try {
            $content = file_get_contents($file->getPathname());
            
            // Extract metadata from PDF
            if (preg_match_all('/\/(\w+)\s*\(([^)]*)\)/', $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $key = $match[1];
                    $value = $match[2];
                    
                    if (isset(self::SUSPICIOUS_METADATA[$key])) {
                        foreach (self::SUSPICIOUS_METADATA[$key] as $pattern) {
                            if (preg_match($pattern, $value)) {
                                Log::warning('Suspicious PDF metadata detected', [
                                    'filename' => $file->getClientOriginalName(),
                                    'metadata_key' => $key,
                                    'metadata_value' => substr($value, 0, 100),
                                    'user_id' => auth()->id(),
                                ]);
                                return false;
                            }
                        }
                    }
                }
            }
            
            // Check for JavaScript in PDF
            if (stripos($content, '/JavaScript') !== false || stripos($content, '/JS') !== false) {
                Log::warning('JavaScript detected in PDF', [
                    'filename' => $file->getClientOriginalName(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('PDF metadata validation failed', [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Validate image metadata (EXIF)
     */
    private function validateImageMetadata(UploadedFile $file): bool
    {
        try {
            $exifData = @exif_read_data($file->getPathname());
            
            if ($exifData) {
                foreach ($exifData as $key => $value) {
                    if (is_string($value)) {
                        foreach (self::FORBIDDEN_PATTERNS as $pattern) {
                            if (preg_match($pattern, $value)) {
                                Log::warning('Suspicious EXIF data detected', [
                                    'filename' => $file->getClientOriginalName(),
                                    'exif_key' => $key,
                                    'user_id' => auth()->id(),
                                ]);
                                return false;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // EXIF reading failed, but this is not necessarily suspicious
            Log::info('EXIF reading failed', [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
        }

        return true;
    }

    /**
     * Validate Office document metadata
     */
    private function validateOfficeMetadata(UploadedFile $file): bool
    {
        // For Office documents, we'll do basic content scanning
        // Full metadata extraction would require specialized libraries
        
        $content = file_get_contents($file->getPathname());
        
        // Check for macros
        if (stripos($content, 'vba') !== false || 
            stripos($content, 'macro') !== false ||
            stripos($content, 'autoopen') !== false ||
            stripos($content, 'autoexec') !== false) {
            
            Log::warning('Potential macro detected in Office document', [
                'filename' => $file->getClientOriginalName(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Validate embedded objects
     */
    private function validateEmbeddedObjects(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname());
        
        // Check for embedded executables
        $executableSignatures = [
            "\x4D\x5A",         // PE executable
            "\x7F\x45\x4C\x46", // ELF executable
            "\xFE\xED\xFA\xCE", // Mach-O executable
        ];

        foreach ($executableSignatures as $signature) {
            if (strpos($content, $signature) !== false) {
                Log::warning('Embedded executable detected', [
                    'filename' => $file->getClientOriginalName(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        // Check for embedded archives
        $archiveSignatures = [
            "\x50\x4B\x03\x04", // ZIP
            "\x52\x61\x72\x21", // RAR
        ];

        $archiveCount = 0;
        foreach ($archiveSignatures as $signature) {
            $archiveCount += substr_count($content, $signature);
        }

        // Multiple embedded archives might be suspicious
        if ($archiveCount > 3) {
            Log::warning('Multiple embedded archives detected', [
                'filename' => $file->getClientOriginalName(),
                'archive_count' => $archiveCount,
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Check for potential steganography
     */
    private function checkSteganography(UploadedFile $file): bool
    {
        if (!str_starts_with($file->getMimeType(), 'image/')) {
            return true; // Only check images for steganography
        }

        $content = file_get_contents($file->getPathname());
        
        // Check for unusual file size compared to image dimensions
        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo) {
            $expectedSize = $imageInfo[0] * $imageInfo[1] * 3; // Rough estimate
            $actualSize = strlen($content);
            
            // If file is significantly larger than expected, might contain hidden data
            if ($actualSize > $expectedSize * 2) {
                Log::info('Potentially large image file detected', [
                    'filename' => $file->getClientOriginalName(),
                    'expected_size' => $expectedSize,
                    'actual_size' => $actualSize,
                    'ratio' => $actualSize / $expectedSize,
                    'user_id' => auth()->id(),
                ]);
                
                // Don't fail validation, just log for monitoring
                // Steganography detection is complex and might have false positives
            }
        }

        return true;
    }

    /**
     * Check if file is a text file
     */
    private function isTextFile(UploadedFile $file): bool
    {
        $textMimeTypes = [
            'text/plain',
            'text/html',
            'text/css',
            'text/javascript',
            'application/json',
            'application/xml',
            'text/xml',
        ];

        return in_array($file->getMimeType(), $textMimeTypes);
    }

    /**
     * Calculate binary content ratio
     */
    private function calculateBinaryRatio(string $content): float
    {
        $binaryChars = 0;
        $totalChars = strlen($content);
        
        for ($i = 0; $i < $totalChars; $i++) {
            $char = ord($content[$i]);
            // Count non-printable characters (except common whitespace)
            if ($char < 32 && !in_array($char, [9, 10, 13])) {
                $binaryChars++;
            }
        }

        return $totalChars > 0 ? $binaryChars / $totalChars : 0;
    }
}
