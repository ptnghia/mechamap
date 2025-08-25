<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class FileUploadSecurityService
{
    /**
     * Maximum file uploads per user per hour
     */
    private const MAX_UPLOADS_PER_HOUR = 20;

    /**
     * Maximum total file size per user per hour (in bytes)
     */
    private const MAX_TOTAL_SIZE_PER_HOUR = 500 * 1024 * 1024; // 500MB

    /**
     * Quarantine directory for suspicious files
     */
    private const QUARANTINE_DIR = 'quarantine';

    /**
     * Validate file upload security
     */
    public function validateUpload(UploadedFile $file, User $user): array
    {
        $result = [
            'allowed' => true,
            'reason' => null,
            'quarantine' => false,
            'scan_results' => []
        ];

        try {
            // 1. Check rate limits
            if (!$this->checkRateLimits($user, $file)) {
                $result['allowed'] = false;
                $result['reason'] = 'Rate limit exceeded';
                return $result;
            }

            // 2. Perform comprehensive security scan
            $scanResults = $this->performSecurityScan($file, $user);
            $result['scan_results'] = $scanResults;

            // 3. Determine if file should be quarantined
            if ($scanResults['threat_level'] >= 7) {
                $result['quarantine'] = true;
                $this->quarantineFile($file, $user, $scanResults);
            }

            // 4. Block high-risk files
            if ($scanResults['threat_level'] >= 9) {
                $result['allowed'] = false;
                $result['reason'] = 'High security risk detected';
            }

            // 5. Update user upload statistics
            $this->updateUploadStats($user, $file);

            // 6. Log upload attempt
            $this->logUploadAttempt($file, $user, $result);

        } catch (\Exception $e) {
            Log::error('File upload security validation failed', [
                'filename' => $file->getClientOriginalName(),
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            $result['allowed'] = false;
            $result['reason'] = 'Security validation failed';
        }

        return $result;
    }

    /**
     * Check rate limits for file uploads
     */
    private function checkRateLimits(User $user, UploadedFile $file): bool
    {
        $userId = $user->id;
        $hour = now()->format('Y-m-d-H');

        // Check upload count limit
        $countKey = "upload_count:{$userId}:{$hour}";
        $currentCount = Cache::get($countKey, 0);

        if ($currentCount >= self::MAX_UPLOADS_PER_HOUR) {
            Log::warning('Upload count limit exceeded', [
                'user_id' => $userId,
                'current_count' => $currentCount,
                'limit' => self::MAX_UPLOADS_PER_HOUR,
            ]);
            return false;
        }

        // Check total size limit
        $sizeKey = "upload_size:{$userId}:{$hour}";
        $currentSize = Cache::get($sizeKey, 0);
        $newSize = $currentSize + $file->getSize();

        if ($newSize > self::MAX_TOTAL_SIZE_PER_HOUR) {
            Log::warning('Upload size limit exceeded', [
                'user_id' => $userId,
                'current_size' => $currentSize,
                'file_size' => $file->getSize(),
                'new_total' => $newSize,
                'limit' => self::MAX_TOTAL_SIZE_PER_HOUR,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Perform comprehensive security scan
     */
    private function performSecurityScan(UploadedFile $file, User $user): array
    {
        $results = [
            'threat_level' => 0,
            'threats' => [],
            'file_hash' => hash_file('sha256', $file->getPathname()),
            'scan_timestamp' => now()->toISOString(),
        ];

        // 1. Check file against known malware hashes
        $results = $this->checkMalwareHashes($file, $results);

        // 2. Analyze file structure
        $results = $this->analyzeFileStructure($file, $results);

        // 3. Check for suspicious metadata
        $results = $this->checkSuspiciousMetadata($file, $results);

        // 4. Analyze upload patterns
        $results = $this->analyzeUploadPatterns($user, $file, $results);

        // 5. Check file reputation
        $results = $this->checkFileReputation($file, $results);

        return $results;
    }

    /**
     * Check file against known malware hashes
     */
    private function checkMalwareHashes(UploadedFile $file, array $results): array
    {
        $fileHash = hash_file('sha256', $file->getPathname());
        
        // Check against cached malware hashes
        $malwareHashes = Cache::get('malware_hashes', []);
        
        if (in_array($fileHash, $malwareHashes)) {
            $results['threat_level'] = 10;
            $results['threats'][] = 'Known malware hash detected';
            
            Log::critical('Known malware hash detected', [
                'filename' => $file->getClientOriginalName(),
                'hash' => $fileHash,
                'user_id' => auth()->id(),
            ]);
        }

        return $results;
    }

    /**
     * Analyze file structure for anomalies
     */
    private function analyzeFileStructure(UploadedFile $file, array $results): array
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Check for MIME type spoofing
        $expectedMimes = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'zip' => ['application/zip', 'application/x-zip-compressed'],
        ];

        if (isset($expectedMimes[$extension])) {
            if (!in_array($mimeType, $expectedMimes[$extension])) {
                $results['threat_level'] += 3;
                $results['threats'][] = 'MIME type spoofing detected';
            }
        }

        // Check file size anomalies
        $fileSize = $file->getSize();
        if ($fileSize === 0) {
            $results['threat_level'] += 2;
            $results['threats'][] = 'Zero-byte file detected';
        } elseif ($fileSize > 100 * 1024 * 1024) { // 100MB
            $results['threat_level'] += 1;
            $results['threats'][] = 'Unusually large file size';
        }

        return $results;
    }

    /**
     * Check for suspicious metadata
     */
    private function checkSuspiciousMetadata(UploadedFile $file, array $results): array
    {
        $filename = $file->getClientOriginalName();
        
        // Check for suspicious filename patterns
        $suspiciousPatterns = [
            '/\.(exe|bat|cmd|scr|pif|com)$/i',
            '/\.(php|asp|jsp|js)$/i',
            '/\..*\./i', // Double extensions
            '/[<>:"|?*]/',
            '/\.\./i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                $results['threat_level'] += 2;
                $results['threats'][] = 'Suspicious filename pattern';
                break;
            }
        }

        // Check filename length
        if (strlen($filename) > 255) {
            $results['threat_level'] += 1;
            $results['threats'][] = 'Filename too long';
        }

        return $results;
    }

    /**
     * Analyze upload patterns for suspicious behavior
     */
    private function analyzeUploadPatterns(User $user, UploadedFile $file, array $results): array
    {
        $userId = $user->id;
        $hour = now()->format('Y-m-d-H');
        
        // Check rapid uploads
        $recentUploads = Cache::get("recent_uploads:{$userId}", []);
        $recentCount = count(array_filter($recentUploads, function($timestamp) {
            return $timestamp > now()->subMinutes(5)->timestamp;
        }));

        if ($recentCount > 5) {
            $results['threat_level'] += 2;
            $results['threats'][] = 'Rapid upload pattern detected';
        }

        // Check for identical file uploads
        $fileHash = hash_file('md5', $file->getPathname());
        $userHashes = Cache::get("user_hashes:{$userId}", []);
        
        if (in_array($fileHash, $userHashes)) {
            $results['threat_level'] += 1;
            $results['threats'][] = 'Duplicate file upload';
        }

        return $results;
    }

    /**
     * Check file reputation
     */
    private function checkFileReputation(UploadedFile $file, array $results): array
    {
        $fileHash = hash_file('sha256', $file->getPathname());
        
        // Check against reputation database (simplified)
        $reputationKey = "file_reputation:{$fileHash}";
        $reputation = Cache::get($reputationKey);
        
        if ($reputation === 'malicious') {
            $results['threat_level'] += 5;
            $results['threats'][] = 'File has malicious reputation';
        } elseif ($reputation === 'suspicious') {
            $results['threat_level'] += 2;
            $results['threats'][] = 'File has suspicious reputation';
        }

        return $results;
    }

    /**
     * Quarantine suspicious file
     */
    private function quarantineFile(UploadedFile $file, User $user, array $scanResults): void
    {
        $quarantinePath = self::QUARANTINE_DIR . '/' . now()->format('Y/m/d');
        $filename = $scanResults['file_hash'] . '_' . $file->getClientOriginalName();
        
        // Store file in quarantine
        Storage::putFileAs($quarantinePath, $file, $filename);
        
        // Store metadata
        $metadata = [
            'original_filename' => $file->getClientOriginalName(),
            'user_id' => $user->id,
            'upload_timestamp' => now()->toISOString(),
            'scan_results' => $scanResults,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
        
        Storage::put($quarantinePath . '/' . $filename . '.meta', json_encode($metadata));
        
        Log::warning('File quarantined', [
            'filename' => $file->getClientOriginalName(),
            'user_id' => $user->id,
            'threat_level' => $scanResults['threat_level'],
            'threats' => $scanResults['threats'],
        ]);
    }

    /**
     * Update user upload statistics
     */
    private function updateUploadStats(User $user, UploadedFile $file): void
    {
        $userId = $user->id;
        $hour = now()->format('Y-m-d-H');

        // Update upload count
        $countKey = "upload_count:{$userId}:{$hour}";
        Cache::increment($countKey, 1);
        Cache::expire($countKey, 3600); // 1 hour

        // Update total size
        $sizeKey = "upload_size:{$userId}:{$hour}";
        Cache::increment($sizeKey, $file->getSize());
        Cache::expire($sizeKey, 3600);

        // Track recent uploads
        $recentKey = "recent_uploads:{$userId}";
        $recentUploads = Cache::get($recentKey, []);
        $recentUploads[] = now()->timestamp;
        
        // Keep only last 10 uploads
        $recentUploads = array_slice($recentUploads, -10);
        Cache::put($recentKey, $recentUploads, 3600);

        // Track file hashes
        $hashKey = "user_hashes:{$userId}";
        $userHashes = Cache::get($hashKey, []);
        $userHashes[] = hash_file('md5', $file->getPathname());
        
        // Keep only last 20 hashes
        $userHashes = array_slice(array_unique($userHashes), -20);
        Cache::put($hashKey, $userHashes, 86400); // 24 hours
    }

    /**
     * Log upload attempt
     */
    private function logUploadAttempt(UploadedFile $file, User $user, array $result): void
    {
        Log::info('File upload attempt', [
            'filename' => $file->getClientOriginalName(),
            'user_id' => $user->id,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'allowed' => $result['allowed'],
            'quarantined' => $result['quarantine'],
            'threat_level' => $result['scan_results']['threat_level'] ?? 0,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get upload statistics for user
     */
    public function getUploadStats(User $user): array
    {
        $userId = $user->id;
        $hour = now()->format('Y-m-d-H');

        return [
            'uploads_this_hour' => Cache::get("upload_count:{$userId}:{$hour}", 0),
            'size_this_hour' => Cache::get("upload_size:{$userId}:{$hour}", 0),
            'max_uploads_per_hour' => self::MAX_UPLOADS_PER_HOUR,
            'max_size_per_hour' => self::MAX_TOTAL_SIZE_PER_HOUR,
        ];
    }
}
