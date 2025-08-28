<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidDigitalFileType implements ValidationRule
{
    /**
     * Allowed file types for digital products
     */
    private const ALLOWED_TYPES = [
        // CAD Files
        'dwg' => ['application/acad', 'application/x-acad', 'application/autocad_dwg', 'image/x-dwg'],
        'dxf' => ['application/dxf', 'image/vnd.dxf'],
        'step' => ['application/step', 'application/stp'],
        'stp' => ['application/step', 'application/stp'],
        'iges' => ['application/iges', 'model/iges'],
        'igs' => ['application/iges', 'model/iges'],
        'stl' => ['application/sla', 'application/vnd.ms-pki.stl', 'application/x-navistyle'],
        
        // 3D Model Files
        '3dm' => ['application/x-3dm'],
        'skp' => ['application/vnd.sketchup.skp'],
        'f3d' => ['application/octet-stream'],
        'ipt' => ['application/octet-stream'],
        'iam' => ['application/octet-stream'],
        'prt' => ['application/octet-stream'],
        'asm' => ['application/octet-stream'],
        'sldprt' => ['application/octet-stream'],
        'sldasm' => ['application/octet-stream'],
        
        // Document Files
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        
        // Archive Files
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/x-rar-compressed', 'application/vnd.rar'],
        '7z' => ['application/x-7z-compressed'],
    ];

    /**
     * Maximum file size in bytes (50MB)
     */
    private const MAX_FILE_SIZE = 52428800;

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('File không hợp lệ.');
            return;
        }

        // Check if file was uploaded successfully
        if (!$value->isValid()) {
            $fail('File upload thất bại.');
            return;
        }

        // Get file extension
        $extension = strtolower($value->getClientOriginalExtension());
        
        // Check if extension is allowed
        if (!array_key_exists($extension, self::ALLOWED_TYPES)) {
            $allowedExtensions = implode(', ', array_keys(self::ALLOWED_TYPES));
            $fail("Định dạng file không được hỗ trợ. Các định dạng được phép: {$allowedExtensions}");
            return;
        }

        // Check MIME type
        $mimeType = $value->getMimeType();
        $allowedMimeTypes = self::ALLOWED_TYPES[$extension];
        
        // Some files might have generic MIME types, so we're more lenient
        if (!in_array($mimeType, $allowedMimeTypes) && 
            !in_array($mimeType, ['application/octet-stream', 'text/plain'])) {
            $fail("MIME type của file không hợp lệ cho định dạng {$extension}.");
            return;
        }

        // Check file size
        if ($value->getSize() > self::MAX_FILE_SIZE) {
            $maxSizeMB = self::MAX_FILE_SIZE / (1024 * 1024);
            $fail("File quá lớn. Kích thước tối đa cho phép là {$maxSizeMB}MB.");
            return;
        }

        // Check for potentially dangerous files
        $this->checkForMaliciousContent($value, $fail);
    }

    /**
     * Check for potentially malicious content
     */
    private function checkForMaliciousContent(UploadedFile $file, Closure $fail): void
    {
        $filename = $file->getClientOriginalName();
        
        // Check for suspicious file names
        $suspiciousPatterns = [
            '/\.php$/i',
            '/\.exe$/i',
            '/\.bat$/i',
            '/\.cmd$/i',
            '/\.scr$/i',
            '/\.vbs$/i',
            '/\.js$/i',
            '/\.jar$/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                $fail('Tên file chứa định dạng không được phép.');
                return;
            }
        }

        // Check for double extensions
        if (substr_count($filename, '.') > 1) {
            $parts = explode('.', $filename);
            if (count($parts) > 2) {
                // Allow common patterns like .tar.gz, .step.zip, etc.
                $allowedDoubleExtensions = ['tar.gz', 'tar.bz2', 'step.zip', 'dwg.zip'];
                $lastTwoParts = strtolower(end($parts) . '.' . prev($parts));
                
                if (!in_array($lastTwoParts, $allowedDoubleExtensions)) {
                    $fail('Tên file không được chứa nhiều phần mở rộng.');
                    return;
                }
            }
        }

        // Check file content for CAD files (basic check)
        if (in_array(strtolower($file->getClientOriginalExtension()), ['dwg', 'dxf'])) {
            $this->validateCADFile($file, $fail);
        }
    }

    /**
     * Basic validation for CAD files
     */
    private function validateCADFile(UploadedFile $file, Closure $fail): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        try {
            $handle = fopen($file->getPathname(), 'rb');
            if (!$handle) {
                return; // Skip validation if can't read file
            }

            $header = fread($handle, 100);
            fclose($handle);

            if ($extension === 'dwg') {
                // DWG files should start with specific bytes
                if (!str_starts_with($header, 'AC')) {
                    $fail('File DWG không hợp lệ.');
                    return;
                }
            } elseif ($extension === 'dxf') {
                // DXF files should contain specific text patterns
                if (!str_contains($header, 'SECTION') && !str_contains($header, 'HEADER')) {
                    $fail('File DXF không hợp lệ.');
                    return;
                }
            }
        } catch (\Exception $e) {
            // If we can't validate, just log and continue
            \Log::warning('Could not validate CAD file: ' . $e->getMessage());
        }
    }

    /**
     * Get allowed file extensions
     */
    public static function getAllowedExtensions(): array
    {
        return array_keys(self::ALLOWED_TYPES);
    }

    /**
     * Get allowed MIME types for an extension
     */
    public static function getAllowedMimeTypes(string $extension): array
    {
        return self::ALLOWED_TYPES[strtolower($extension)] ?? [];
    }

    /**
     * Check if an extension is allowed
     */
    public static function isExtensionAllowed(string $extension): bool
    {
        return array_key_exists(strtolower($extension), self::ALLOWED_TYPES);
    }

    /**
     * Get maximum file size in bytes
     */
    public static function getMaxFileSize(): int
    {
        return self::MAX_FILE_SIZE;
    }

    /**
     * Get maximum file size in MB
     */
    public static function getMaxFileSizeMB(): float
    {
        return self::MAX_FILE_SIZE / (1024 * 1024);
    }
}
