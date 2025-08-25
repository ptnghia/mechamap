<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VirusScanFile implements ValidationRule
{
    /**
     * Known malware signatures (simplified for demonstration)
     */
    private const MALWARE_SIGNATURES = [
        // Common malware patterns
        'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*', // EICAR test string
        
        // Suspicious script patterns
        'eval(base64_decode(',
        'eval(gzinflate(',
        'eval(str_rot13(',
        'eval(gzuncompress(',
        'system($_GET',
        'system($_POST',
        'shell_exec($_GET',
        'shell_exec($_POST',
        'passthru($_GET',
        'passthru($_POST',
        'exec($_GET',
        'exec($_POST',
        
        // PHP backdoor patterns
        'c99shell',
        'r57shell',
        'wso shell',
        'FilesMan',
        'Uname:',
        'Safe-mode:',
        'disable_functions',
        'phpinfo()',
        
        // JavaScript malware patterns
        'document.write(unescape(',
        'eval(unescape(',
        'String.fromCharCode(',
        'iframe src=',
        'script src=',
        
        // SQL injection patterns
        'union select',
        'drop table',
        'delete from',
        'insert into',
        'update set',
        
        // Command injection patterns
        '&& cat /etc/passwd',
        '| cat /etc/passwd',
        '; cat /etc/passwd',
        '`cat /etc/passwd`',
        '$(cat /etc/passwd)',
    ];

    /**
     * Suspicious file content patterns
     */
    private const SUSPICIOUS_PATTERNS = [
        // Obfuscated code patterns
        '/[a-zA-Z0-9+\/]{100,}={0,2}/', // Base64 encoded content (long strings)
        '/\\x[0-9a-fA-F]{2}/', // Hex encoded content
        '/chr\(\d+\)/', // Character encoding
        '/\\\\[0-7]{3}/', // Octal encoding
        
        // Suspicious function calls
        '/eval\s*\(/i',
        '/exec\s*\(/i',
        '/system\s*\(/i',
        '/shell_exec\s*\(/i',
        '/passthru\s*\(/i',
        '/file_get_contents\s*\(/i',
        '/file_put_contents\s*\(/i',
        '/fopen\s*\(/i',
        '/fwrite\s*\(/i',
        '/curl_exec\s*\(/i',
        
        // Network communication patterns
        '/fsockopen\s*\(/i',
        '/socket_create\s*\(/i',
        '/stream_socket_client\s*\(/i',
        '/gzinflate\s*\(/i',
        '/base64_decode\s*\(/i',
        '/str_rot13\s*\(/i',
        '/gzuncompress\s*\(/i',
        
        // File system manipulation
        '/unlink\s*\(/i',
        '/rmdir\s*\(/i',
        '/mkdir\s*\(/i',
        '/chmod\s*\(/i',
        '/chown\s*\(/i',
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
            // Check cache for previously scanned files
            $fileHash = $this->getFileHash($value);
            $cacheKey = "virus_scan:{$fileHash}";
            
            $cachedResult = Cache::get($cacheKey);
            if ($cachedResult !== null) {
                if ($cachedResult === 'infected') {
                    $fail('File failed virus scan (cached result).');
                    return;
                }
                // If cached as clean, skip scanning
                return;
            }

            // Perform virus scan
            $scanResult = $this->performVirusScan($value);
            
            // Cache result for 24 hours
            Cache::put($cacheKey, $scanResult ? 'clean' : 'infected', now()->addHours(24));
            
            if (!$scanResult) {
                Log::warning('File failed virus scan', [
                    'filename' => $value->getClientOriginalName(),
                    'size' => $value->getSize(),
                    'mime_type' => $value->getMimeType(),
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                ]);
                
                $fail('File failed virus scan and may contain malicious content.');
            }

        } catch (\Exception $e) {
            Log::error('Virus scan failed with exception', [
                'filename' => $value->getClientOriginalName(),
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            
            $fail('Unable to complete virus scan. Please try again.');
        }
    }

    /**
     * Perform comprehensive virus scan
     */
    private function performVirusScan(UploadedFile $file): bool
    {
        // 1. Check file size (extremely large files might be suspicious)
        if ($file->getSize() > 500 * 1024 * 1024) { // 500MB
            Log::warning('Extremely large file upload detected', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        // 2. Scan for known malware signatures
        if (!$this->scanMalwareSignatures($file)) {
            return false;
        }

        // 3. Scan for suspicious patterns
        if (!$this->scanSuspiciousPatterns($file)) {
            return false;
        }

        // 4. Check file entropy (high entropy might indicate encryption/obfuscation)
        if (!$this->checkFileEntropy($file)) {
            return false;
        }

        // 5. Validate file structure
        if (!$this->validateFileStructure($file)) {
            return false;
        }

        return true;
    }

    /**
     * Scan for known malware signatures
     */
    private function scanMalwareSignatures(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname());
        
        foreach (self::MALWARE_SIGNATURES as $signature) {
            if (stripos($content, $signature) !== false) {
                Log::warning('Malware signature detected', [
                    'filename' => $file->getClientOriginalName(),
                    'signature' => substr($signature, 0, 50) . '...',
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Scan for suspicious patterns using regex
     */
    private function scanSuspiciousPatterns(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname());
        
        foreach (self::SUSPICIOUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::warning('Suspicious pattern detected', [
                    'filename' => $file->getClientOriginalName(),
                    'pattern' => $pattern,
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Check file entropy to detect obfuscated content
     */
    private function checkFileEntropy(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname(), false, null, 0, 8192); // First 8KB
        
        if (strlen($content) < 100) {
            return true; // Skip entropy check for very small files
        }

        $entropy = $this->calculateEntropy($content);
        
        // High entropy (> 7.5) might indicate encrypted/obfuscated content
        if ($entropy > 7.5) {
            Log::warning('High entropy file detected', [
                'filename' => $file->getClientOriginalName(),
                'entropy' => $entropy,
                'user_id' => auth()->id(),
            ]);
            
            // Allow high entropy for certain file types (images, archives, etc.)
            $allowedHighEntropyTypes = [
                'image/jpeg', 'image/png', 'image/gif',
                'application/zip', 'application/x-rar-compressed',
                'application/pdf'
            ];
            
            if (!in_array($file->getMimeType(), $allowedHighEntropyTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate Shannon entropy of content
     */
    private function calculateEntropy(string $content): float
    {
        $frequencies = array_count_values(str_split($content));
        $length = strlen($content);
        $entropy = 0;

        foreach ($frequencies as $frequency) {
            $probability = $frequency / $length;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }

    /**
     * Validate file structure based on file type
     */
    private function validateFileStructure(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        
        // Validate PDF structure
        if ($mimeType === 'application/pdf') {
            return $this->validatePdfStructure($file);
        }
        
        // Validate ZIP structure
        if (str_contains($mimeType, 'zip')) {
            return $this->validateZipStructure($file);
        }
        
        // Validate image structure
        if (str_starts_with($mimeType, 'image/')) {
            return $this->validateImageStructure($file);
        }

        return true;
    }

    /**
     * Validate PDF file structure
     */
    private function validatePdfStructure(UploadedFile $file): bool
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) return false;

        $header = fread($handle, 8);
        fclose($handle);

        // PDF should start with %PDF-
        if (!str_starts_with($header, '%PDF-')) {
            return false;
        }

        return true;
    }

    /**
     * Validate ZIP file structure
     */
    private function validateZipStructure(UploadedFile $file): bool
    {
        $zip = new \ZipArchive();
        $result = $zip->open($file->getPathname(), \ZipArchive::CHECKCONS);
        
        if ($result !== true) {
            return false;
        }

        // Check for suspicious files in archive
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['exe', 'bat', 'cmd', 'php', 'asp', 'jsp'])) {
                $zip->close();
                return false;
            }
        }

        $zip->close();
        return true;
    }

    /**
     * Validate image file structure
     */
    private function validateImageStructure(UploadedFile $file): bool
    {
        $imageInfo = @getimagesize($file->getPathname());
        
        if ($imageInfo === false) {
            return false;
        }

        // Check if image dimensions are reasonable
        if ($imageInfo[0] > 10000 || $imageInfo[1] > 10000) {
            return false;
        }

        return true;
    }

    /**
     * Generate file hash for caching
     */
    private function getFileHash(UploadedFile $file): string
    {
        return hash_file('sha256', $file->getPathname());
    }
}
