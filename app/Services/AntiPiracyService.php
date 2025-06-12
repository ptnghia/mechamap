<?php

namespace App\Services;

use App\Models\DownloadToken;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Anti-Piracy and Abuse Detection Service
 * Implements advanced security measures for download protection
 */
class AntiPiracyService
{
    /**
     * Analyze download patterns for suspicious activity
     */
    public function analyzeDownloadPattern(User $user, string $ipAddress, string $userAgent): array
    {
        $suspicious = false;
        $reasons = [];
        $riskScore = 0;

        // 1. Check for rapid consecutive downloads
        $recentDownloads = DownloadToken::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentDownloads > 10) {
            $suspicious = true;
            $reasons[] = 'Rapid consecutive downloads detected';
            $riskScore += 30;
        }

        // 2. Check for multiple IP addresses
        $uniqueIPs = DownloadToken::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->distinct('ip_address')
            ->count();

        if ($uniqueIPs > 5) {
            $suspicious = true;
            $reasons[] = 'Multiple IP addresses used';
            $riskScore += 25;
        }

        // 3. Check for suspicious user agents
        $botPatterns = [
            'curl/', 'wget/', 'python-requests/', 'http', 'bot', 'crawler',
            'spider', 'scraper', 'automation'
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                $suspicious = true;
                $reasons[] = 'Automated tool detected';
                $riskScore += 40;
                break;
            }
        }

        // 4. Check for geographic anomalies (simplified)
        $knownCountries = Cache::get("user_{$user->id}_countries", []);
        $currentCountry = $this->getCountryFromIP($ipAddress);

        if (!empty($knownCountries) && !in_array($currentCountry, $knownCountries)) {
            if (count($knownCountries) >= 3) {
                $suspicious = true;
                $reasons[] = 'Unusual geographic access pattern';
                $riskScore += 20;
            }
        }

        // Update known countries
        $knownCountries[] = $currentCountry;
        Cache::put("user_{$user->id}_countries", array_unique($knownCountries), now()->addDays(30));

        // 5. Check for download sharing patterns
        $sharedTokens = DownloadToken::where('user_id', '!=', $user->id)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($sharedTokens > 0) {
            $suspicious = true;
            $reasons[] = 'Potential token sharing detected';
            $riskScore += 35;
        }

        return [
            'suspicious' => $suspicious,
            'risk_score' => min($riskScore, 100), // Cap at 100
            'reasons' => $reasons,
            'action_required' => $riskScore >= 70,
            'recommended_action' => $this->getRecommendedAction($riskScore)
        ];
    }

    /**
     * Implement device fingerprinting for additional security
     */
    public function generateDeviceFingerprint(array $requestData): string
    {
        $fingerprint = [
            'user_agent' => $requestData['user_agent'] ?? '',
            'accept_language' => $requestData['accept_language'] ?? '',
            'screen_resolution' => $requestData['screen_resolution'] ?? '',
            'timezone' => $requestData['timezone'] ?? '',
            'platform' => $requestData['platform'] ?? '',
        ];

        return hash('sha256', serialize($fingerprint));
    }

    /**
     * Check if device fingerprint is suspicious
     */
    public function validateDeviceFingerprint(User $user, string $fingerprint): bool
    {
        $knownFingerprints = Cache::get("user_{$user->id}_fingerprints", []);

        if (empty($knownFingerprints)) {
            // First time - store fingerprint
            Cache::put("user_{$user->id}_fingerprints", [$fingerprint], now()->addDays(90));
            return true;
        }

        if (in_array($fingerprint, $knownFingerprints)) {
            return true; // Known device
        }

        // New device - check if too many devices
        if (count($knownFingerprints) >= 10) {
            Log::warning('User has too many registered devices', [
                'user_id' => $user->id,
                'device_count' => count($knownFingerprints),
                'new_fingerprint' => substr($fingerprint, 0, 16)
            ]);
            return false;
        }

        // Add new device fingerprint
        $knownFingerprints[] = $fingerprint;
        Cache::put("user_{$user->id}_fingerprints", $knownFingerprints, now()->addDays(90));

        return true;
    }

    /**
     * Block suspicious downloads
     */
    public function blockSuspiciousDownload(User $user, array $analysisResult): void
    {
        if ($analysisResult['action_required']) {
            // Temporarily block user downloads
            Cache::put("download_blocked_{$user->id}", true, now()->addHours(2));

            // Log security incident
            Log::warning('Suspicious download activity blocked', [
                'user_id' => $user->id,
                'risk_score' => $analysisResult['risk_score'],
                'reasons' => $analysisResult['reasons'],
                'action' => $analysisResult['recommended_action']
            ]);

            // Notify administrators for high-risk activities
            if ($analysisResult['risk_score'] >= 90) {
                $this->notifyAdministrators($user, $analysisResult);
            }
        }
    }

    /**
     * Check if user is currently blocked
     */
    public function isUserBlocked(User $user): bool
    {
        return Cache::has("download_blocked_{$user->id}");
    }

    /**
     * Get download statistics for abuse detection
     */
    public function getDownloadStats(User $user): array
    {
        return [
            'total_downloads' => DownloadToken::where('user_id', $user->id)->count(),
            'downloads_today' => DownloadToken::where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfDay())->count(),
            'downloads_this_week' => DownloadToken::where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfWeek())->count(),
            'unique_files' => DownloadToken::where('user_id', $user->id)
                ->distinct('protected_file_id')->count(),
            'unique_ips' => DownloadToken::where('user_id', $user->id)
                ->distinct('ip_address')->count(),
        ];
    }

    /**
     * Get country from IP address (simplified implementation)
     */
    private function getCountryFromIP(string $ipAddress): string
    {
        // In production, use a service like MaxMind GeoIP2 or ip-api.com
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return 'LOCAL';
        }

        // Simplified demo - in production use real geolocation service
        return 'UNKNOWN';
    }

    /**
     * Get recommended action based on risk score
     */
    private function getRecommendedAction(int $riskScore): string
    {
        if ($riskScore >= 90) return 'immediate_block';
        if ($riskScore >= 70) return 'temporary_block';
        if ($riskScore >= 50) return 'require_verification';
        if ($riskScore >= 30) return 'monitor_closely';
        return 'allow';
    }

    /**
     * Notify administrators of high-risk activity
     */
    private function notifyAdministrators(User $user, array $analysisResult): void
    {
        // In production, send email/Slack notification to administrators
        Log::critical('High-risk download activity detected', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'risk_score' => $analysisResult['risk_score'],
            'reasons' => implode(', ', $analysisResult['reasons']),
            'timestamp' => now()->toISOString()
        ]);
    }
}
