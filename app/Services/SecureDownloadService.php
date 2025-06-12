<?php

namespace App\Services;

use App\Models\ProductPurchase;
use App\Models\ProtectedFile;
use App\Models\SecureDownload;
use App\Models\DownloadToken;
use App\Models\User;
use Illuminate\Http\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SecureDownloadService
{
    protected FileEncryptionService $encryptionService;
    protected ?AntiPiracyService $antiPiracyService = null;
    protected ?LargeFileOptimizationService $fileOptimizationService = null;

    public function __construct(
        FileEncryptionService $encryptionService,
        ?AntiPiracyService $antiPiracyService = null,
        ?LargeFileOptimizationService $fileOptimizationService = null
    ) {
        $this->encryptionService = $encryptionService;
        $this->antiPiracyService = $antiPiracyService ?? new AntiPiracyService();
        $this->fileOptimizationService = $fileOptimizationService ?? new LargeFileOptimizationService();
    }

    /**
     * Generate a secure download token for a purchased file
     */
    public function generateDownloadToken(ProductPurchase $purchase, ProtectedFile $file, User $user): DownloadToken
    {
        // Verify purchase is valid and user has access
        $this->verifyPurchaseAccess($purchase, $file, $user);

        // Check existing valid tokens to prevent multiple tokens
        $existingToken = DownloadToken::where('user_id', $user->id)
            ->where('product_purchase_id', $purchase->id)
            ->where('protected_file_id', $file->id)
            ->valid()
            ->first();

        if ($existingToken) {
            Log::info('Returning existing valid download token', [
                'token_id' => $existingToken->id,
                'user_id' => $user->id,
                'purchase_id' => $purchase->id
            ]);
            return $existingToken;
        }

        // Generate new secure token
        $tokenString = $this->generateSecureTokenString();

        // Determine expiration based on license type
        $expirationHours = $this->getTokenExpirationHours($purchase->license_type);

        // Create download token record
        $downloadToken = DownloadToken::create([
            'token' => $tokenString,
            'user_id' => $user->id,
            'product_purchase_id' => $purchase->id,
            'protected_file_id' => $file->id,
            'expires_at' => Carbon::now()->addHours($expirationHours),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_used' => false,
            'download_attempts' => 0
        ]);

        Log::info('Download token generated', [
            'token_id' => $downloadToken->id,
            'user_id' => $user->id,
            'purchase_id' => $purchase->id,
            'file_id' => $file->id,
            'expires_at' => $downloadToken->expires_at,
            'license_type' => $purchase->license_type
        ]);

        return $downloadToken;
    }

    /**
     * Process a secure download using token
     */
    public function processSecureDownload(string $token): StreamedResponse
    {
        $startTime = microtime(true);

        // Find valid download record
        $download = SecureDownload::where('download_token', $token)
            ->where('expires_at', '>', now())
            ->where('is_completed', false)
            ->firstOrFail();

        // Verify user authentication
        if (auth()->id() !== $download->user_id) {
            Log::warning('Unauthorized download attempt', [
                'token' => $token,
                'expected_user' => $download->user_id,
                'actual_user' => auth()->id(),
                'ip' => request()->ip()
            ]);
            abort(403, 'Unauthorized download attempt');
        }

        // Get protected file
        $protectedFile = $download->protectedFile;

        // Mark download as started
        $download->markAsStarted(request()->ip(), request()->userAgent());

        try {
            // Decrypt file content
            $fileContent = $this->encryptionService->decryptFile($protectedFile, $token);

            $fileSize = strlen($fileContent);
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            // Update download tracking
            $download->markAsCompleted($fileSize, (int)$duration);

            // Increment download counters
            $download->purchase->incrementDownloads(request()->ip());
            $protectedFile->incrementDownloads();

            Log::info('File downloaded successfully', [
                'download_id' => $download->id,
                'file_size' => $fileSize,
                'duration' => $duration,
                'user_id' => $download->user_id
            ]);

            // Stream file with proper headers
            return response()->streamDownload(
                function() use ($fileContent) {
                    echo $fileContent;
                },
                $protectedFile->original_filename,
                [
                    'Content-Type' => $protectedFile->mime_type,
                    'Content-Length' => $fileSize,
                    'Content-Disposition' => 'attachment; filename="' . $protectedFile->original_filename . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'DENY'
                ]
            );

        } catch (\Exception $e) {
            $download->markAsFailed($e->getMessage());

            Log::error('Download failed', [
                'download_id' => $download->id,
                'error' => $e->getMessage(),
                'user_id' => $download->user_id
            ]);

            abort(500, 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Get download links for all files in a purchase
     */
    public function getDownloadLinksForPurchase(ProductPurchase $purchase): array
    {
        if (!$purchase->isValid()) {
            throw new \Exception('Purchase is not valid or has expired');
        }

        if (!$purchase->hasDownloadsRemaining()) {
            throw new \Exception('Download limit exceeded');
        }

        $downloadLinks = [];

        foreach ($purchase->product->activeFiles as $file) {
            if ($file->access_level === 'full_access') {
                $downloadLinks[] = [
                    'file_id' => $file->id,
                    'filename' => $file->original_filename,
                    'file_type' => $file->file_type,
                    'file_size' => $file->formatted_size,
                    'download_url' => $this->generateDownloadLink($purchase, $file),
                    'expires_in_hours' => 24
                ];
            }
        }

        return $downloadLinks;
    }

    /**
     * Clean up expired download tokens
     */
    public function cleanupExpiredDownloads(): int
    {
        $expiredCount = SecureDownload::where('expires_at', '<', now())
            ->where('is_completed', false)
            ->delete();

        Log::info('Cleaned up expired downloads', ['count' => $expiredCount]);

        return $expiredCount;
    }

    /**
     * Get download statistics for a user
     */
    public function getUserDownloadStats(User $user): array
    {
        $totalDownloads = SecureDownload::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        $totalSize = SecureDownload::where('user_id', $user->id)
            ->where('is_completed', true)
            ->sum('download_size');

        $recentDownloads = SecureDownload::where('user_id', $user->id)
            ->where('downloaded_at', '>', now()->subDays(30))
            ->where('is_completed', true)
            ->count();

        return [
            'total_downloads' => $totalDownloads,
            'total_size_bytes' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize ?? 0),
            'recent_downloads_30_days' => $recentDownloads,
        ];
    }

    /**
     * Verify purchase access to a protected file
     */
    private function verifyPurchaseAccess(ProductPurchase $purchase, ProtectedFile $file): void
    {
        if (!$purchase->isValid()) {
            throw new \Exception('Purchase is not valid or has expired');
        }

        if ($purchase->product_id !== $file->product_id) {
            throw new \Exception('File does not belong to purchased product');
        }

        if (!$purchase->hasDownloadsRemaining()) {
            throw new \Exception('Download limit exceeded for this purchase');
        }

        if ($file->access_level !== 'full_access' && $file->access_level !== 'sample') {
            throw new \Exception('File is not available for download');
        }
    }

    /**
     * Generate secure download token
     */
    private function generateSecureToken(ProductPurchase $purchase, ProtectedFile $file): string
    {
        return hash('sha256', implode('|', [
            $purchase->id,
            $file->id,
            $purchase->buyer_id,
            time(),
            Str::random(32),
            config('app.key')
        ]));
    }

    /**
     * Validate download token and return file info
     */
    public function validateDownloadToken(string $tokenString): array
    {
        $token = DownloadToken::where('token', $tokenString)->first();

        if (!$token) {
            return [
                'valid' => false,
                'reason' => 'Invalid download token'
            ];
        }

        if (!$token->isValid()) {
            return [
                'valid' => false,
                'reason' => $token->isExpired() ? 'Download token has expired' : 'Download token already used'
            ];
        }

        return [
            'valid' => true,
            'token' => $token,
            'file' => $token->protectedFile,
            'purchase' => $token->productPurchase
        ];
    }

    /**
     * Check download limits for a purchase
     */
    public function checkDownloadLimits(ProductPurchase $purchase, ProtectedFile $file): array
    {
        $maxDownloads = $this->getMaxDownloadAttempts($purchase->license_type);

        $downloadsUsed = DownloadToken::where('product_purchase_id', $purchase->id)
            ->where('protected_file_id', $file->id)
            ->where('is_used', true)
            ->count();

        if ($downloadsUsed >= $maxDownloads) {
            return [
                'allowed' => false,
                'reason' => 'Download limit exceeded for this license type',
                'downloads_used' => $downloadsUsed,
                'downloads_limit' => $maxDownloads
            ];
        }

        return [
            'allowed' => true,
            'downloads_used' => $downloadsUsed,
            'downloads_limit' => $maxDownloads
        ];
    }

    /**
     * Track download attempt
     */
    public function trackDownload(ProductPurchase $purchase, ProtectedFile $file, string $ipAddress): void
    {
        // Find the most recent token for this download
        $token = DownloadToken::where('product_purchase_id', $purchase->id)
            ->where('protected_file_id', $file->id)
            ->valid()
            ->latest()
            ->first();

        if ($token) {
            $token->markAsUsed();
        }

        Log::info('Download tracked', [
            'purchase_id' => $purchase->id,
            'file_id' => $file->id,
            'ip_address' => $ipAddress,
            'license_type' => $purchase->license_type
        ]);
    }

    /**
     * Get user's download history
     */
    public function getUserDownloadHistory(int $userId, array $options = []): array
    {
        $limit = $options['limit'] ?? 20;
        $offset = $options['offset'] ?? 0;

        $query = DownloadToken::where('user_id', $userId)
            ->with(['protectedFile', 'productPurchase.technicalProduct'])
            ->orderBy('created_at', 'desc');

        $total = $query->count();
        $downloads = $query->skip($offset)->take($limit)->get();

        return [
            'downloads' => $downloads->map(function ($token) {
                return [
                    'id' => $token->id,
                    'file_name' => $token->protectedFile->original_filename,
                    'product_title' => $token->productPurchase->technicalProduct->title,
                    'license_type' => $token->productPurchase->license_type,
                    'downloaded_at' => $token->used_at,
                    'is_used' => $token->is_used,
                    'expires_at' => $token->expires_at,
                    'file_size' => $token->protectedFile->file_size
                ];
            }),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Get download analytics for a purchase
     */
    public function getPurchaseAnalytics(int $purchaseId): array
    {
        $tokens = DownloadToken::where('product_purchase_id', $purchaseId)
            ->with('protectedFile')
            ->get();

        $totalDownloads = $tokens->where('is_used', true)->count();
        $totalFiles = $tokens->groupBy('protected_file_id')->count();
        $totalSize = $tokens->where('is_used', true)
            ->sum(fn($token) => $token->protectedFile->file_size);

        return [
            'total_downloads' => $totalDownloads,
            'total_files' => $totalFiles,
            'total_size_bytes' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'files' => $tokens->groupBy('protected_file_id')->map(function ($fileTokens) {
                $file = $fileTokens->first()->protectedFile;
                $downloads = $fileTokens->where('is_used', true)->count();

                return [
                    'file_id' => $file->id,
                    'file_name' => $file->original_filename,
                    'downloads_count' => $downloads,
                    'last_downloaded' => $fileTokens->where('is_used', true)
                        ->max('used_at')
                ];
            })->values()
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check for suspicious download activity
     */
    public function detectSuspiciousActivity(User $user): array
    {
        $warnings = [];

        // Check for excessive downloads in short time
        $recentDownloads = SecureDownload::where('user_id', $user->id)
            ->where('downloaded_at', '>', now()->subHours(1))
            ->count();

        if ($recentDownloads > 10) {
            $warnings[] = 'Excessive downloads in the last hour: ' . $recentDownloads;
        }

        // Check for downloads from multiple IPs
        $uniqueIps = SecureDownload::where('user_id', $user->id)
            ->where('downloaded_at', '>', now()->subDays(1))
            ->distinct('download_ip')
            ->count();

        if ($uniqueIps > 5) {
            $warnings[] = 'Downloads from multiple IP addresses: ' . $uniqueIps;
        }

        // Check for failed downloads
        $failedDownloads = SecureDownload::where('user_id', $user->id)
            ->where('created_at', '>', now()->subDays(1))
            ->whereNotNull('failure_reason')
            ->count();

        if ($failedDownloads > 5) {
            $warnings[] = 'Multiple failed download attempts: ' . $failedDownloads;
        }

        return $warnings;
    }

    /**
     * Generate secure token string
     */
    private function generateSecureTokenString(): string
    {
        return hash('sha256', Str::random(64) . time() . request()->ip());
    }

    /**
     * Get token expiration hours based on license type
     */
    private function getTokenExpirationHours(string $licenseType): int
    {
        return match($licenseType) {
            'standard' => 1,    // 1 hour for standard license
            'extended' => 24,   // 24 hours for extended license
            'commercial' => 168, // 7 days for commercial license
            default => 1
        };
    }

    /**
     * Get maximum download attempts based on license type
     */
    public function getMaxDownloadAttempts(string $licenseType): int
    {
        return match($licenseType) {
            'standard' => 3,     // 3 downloads for standard
            'extended' => 10,    // 10 downloads for extended
            'commercial' => 50,  // 50 downloads for commercial
            default => 1
        };
    }

    /**
     * Verify download access and update tracking
     */
    public function verifyAndTrackDownload(DownloadToken $token): bool
    {
        // Check if token is valid
        if (!$token->isValid()) {
            Log::warning('Invalid download token used', [
                'token_id' => $token->id,
                'is_used' => $token->is_used,
                'expires_at' => $token->expires_at,
                'current_time' => Carbon::now()
            ]);
            return false;
        }

        // Check download limits
        $maxAttempts = $this->getMaxDownloadAttempts($token->productPurchase->license_type);

        if ($token->download_attempts >= $maxAttempts) {
            Log::warning('Download limit exceeded', [
                'token_id' => $token->id,
                'attempts' => $token->download_attempts,
                'max_attempts' => $maxAttempts,
                'license_type' => $token->productPurchase->license_type
            ]);
            return false;
        }

        // Increment download attempts
        $token->incrementAttempts();

        Log::info('Download access verified', [
            'token_id' => $token->id,
            'user_id' => $token->user_id,
            'purchase_id' => $token->product_purchase_id,
            'attempts' => $token->download_attempts,
            'max_attempts' => $maxAttempts
        ]);

        return true;
    }

    /**
     * Stream file download with security checks and optimizations
     */
    public function streamFileDownload(DownloadToken $token): StreamedResponse
    {
        $startTime = microtime(true);
        $file = $token->protectedFile;
        $user = $token->user;

        // Anti-piracy checks
        if ($this->antiPiracyService) {
            // Check if user is blocked
            if ($this->antiPiracyService->isUserBlocked($user)) {
                Log::warning('Download blocked - user is flagged for suspicious activity', [
                    'user_id' => $user->id,
                    'token_id' => $token->id
                ]);
                abort(403, 'Download temporarily blocked due to suspicious activity');
            }

            // Analyze download pattern
            $analysis = $this->antiPiracyService->analyzeDownloadPattern(
                $user,
                request()->ip(),
                request()->userAgent()
            );

            if ($analysis['action_required']) {
                $this->antiPiracyService->blockSuspiciousDownload($user, $analysis);

                Log::warning('Suspicious download pattern detected', [
                    'user_id' => $user->id,
                    'risk_score' => $analysis['risk_score'],
                    'reasons' => $analysis['reasons']
                ]);

                abort(429, 'Download rate limit exceeded');
            }
        }

        $filePath = storage_path('app/protected/' . $file->file_path);

        // Verify file exists
        if (!file_exists($filePath)) {
            Log::error('Protected file not found', [
                'file_path' => $filePath,
                'file_id' => $file->id
            ]);
            abort(404, 'File not found');
        }

        Log::info('Starting optimized file download', [
            'token_id' => $token->id,
            'file_id' => $file->id,
            'filename' => $file->original_filename,
            'file_size' => $file->file_size,
            'user_id' => $user->id,
            'ip_address' => request()->ip()
        ]);

        // Use optimization service for large files
        if ($this->fileOptimizationService && $file->file_size > 50 * 1024 * 1024) {
            $response = $this->fileOptimizationService->streamLargeFile($file);
        } else {
            // Standard streaming for smaller files
            $headers = $this->fileOptimizationService
                ? $this->fileOptimizationService->getOptimizedHeaders($file)
                : $this->getStandardHeaders($file);

            $response = response()->stream(function () use ($filePath) {
                $stream = fopen($filePath, 'rb');

                if ($stream === false) {
                    Log::error('Failed to open file stream', ['file_path' => $filePath]);
                    return;
                }

                while (!feof($stream)) {
                    echo fread($stream, 8192); // Read in 8KB chunks
                    flush();
                }

                fclose($stream);
            }, 200, $headers);
        }

        // Monitor performance
        if ($this->fileOptimizationService) {
            $performance = $this->fileOptimizationService->monitorDownloadPerformance($file, $startTime);
        }

        // Mark token as used
        $token->markAsUsed();

        return $response;
    }

    /**
     * Get standard headers for file download
     */
    private function getStandardHeaders(ProtectedFile $file): array
    {
        return [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $file->original_filename . '"',
            'Content-Length' => $file->file_size,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY'
        ];
    }
}
