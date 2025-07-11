<?php

namespace App\Services;

use App\Models\User;
use App\Models\BusinessVerificationApplication;
use App\Events\SecurityIncidentDetected;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

/**
 * Security Monitoring Service
 * 
 * Real-time security monitoring for business verification system
 * Detects suspicious activities and triggers alerts
 */
class SecurityMonitoringService
{
    // Threat levels
    const THREAT_LOW = 'low';
    const THREAT_MEDIUM = 'medium';
    const THREAT_HIGH = 'high';
    const THREAT_CRITICAL = 'critical';

    // Incident types
    const INCIDENT_FAILED_LOGIN = 'failed_login';
    const INCIDENT_SUSPICIOUS_UPLOAD = 'suspicious_upload';
    const INCIDENT_UNUSUAL_ACCESS = 'unusual_access';
    const INCIDENT_RAPID_ACTIONS = 'rapid_actions';
    const INCIDENT_IP_CHANGE = 'ip_change';
    const INCIDENT_MALICIOUS_FILE = 'malicious_file';
    const INCIDENT_DATA_BREACH_ATTEMPT = 'data_breach_attempt';
    const INCIDENT_PRIVILEGE_ESCALATION = 'privilege_escalation';

    // Monitoring thresholds
    const MAX_FAILED_LOGINS = 5;
    const MAX_ACTIONS_PER_MINUTE = 30;
    const MAX_FILE_UPLOADS_PER_HOUR = 20;
    const SUSPICIOUS_FILE_EXTENSIONS = ['exe', 'bat', 'cmd', 'scr', 'vbs', 'js', 'jar', 'php'];

    /**
     * Monitor user login attempt
     */
    public function monitorLoginAttempt(string $email, bool $successful, string $ipAddress = null): void
    {
        $ipAddress = $ipAddress ?? Request::ip();
        $cacheKey = "failed_logins:{$email}:{$ipAddress}";

        if (!$successful) {
            $failedAttempts = Cache::get($cacheKey, 0) + 1;
            Cache::put($cacheKey, $failedAttempts, now()->addHours(1));

            if ($failedAttempts >= self::MAX_FAILED_LOGINS) {
                $this->triggerSecurityIncident(self::INCIDENT_FAILED_LOGIN, [
                    'email' => $email,
                    'ip_address' => $ipAddress,
                    'failed_attempts' => $failedAttempts,
                    'threat_level' => self::THREAT_HIGH,
                ]);
            }
        } else {
            // Clear failed attempts on successful login
            Cache::forget($cacheKey);
        }

        // Log all login attempts
        Log::channel('security')->info('Login attempt monitored', [
            'email' => $email,
            'successful' => $successful,
            'ip_address' => $ipAddress,
            'user_agent' => Request::userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Monitor file upload for suspicious content
     */
    public function monitorFileUpload(
        User $user,
        string $filename,
        string $mimeType,
        int $fileSize,
        BusinessVerificationApplication $application = null
    ): array {
        $threats = [];
        $threatLevel = self::THREAT_LOW;

        // Check file extension
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($extension, self::SUSPICIOUS_FILE_EXTENSIONS)) {
            $threats[] = 'Suspicious file extension detected';
            $threatLevel = self::THREAT_HIGH;
        }

        // Check file size (unusually large files)
        if ($fileSize > 50 * 1024 * 1024) { // 50MB
            $threats[] = 'Unusually large file size';
            $threatLevel = max($threatLevel, self::THREAT_MEDIUM);
        }

        // Check upload frequency
        $uploadKey = "file_uploads:{$user->id}:" . now()->format('Y-m-d-H');
        $uploadsThisHour = Cache::get($uploadKey, 0) + 1;
        Cache::put($uploadKey, $uploadsThisHour, now()->addHour());

        if ($uploadsThisHour > self::MAX_FILE_UPLOADS_PER_HOUR) {
            $threats[] = 'Excessive file uploads detected';
            $threatLevel = max($threatLevel, self::THREAT_HIGH);
        }

        // Check for suspicious filename patterns
        if (preg_match('/[<>:"|?*]/', $filename) || strpos($filename, '..') !== false) {
            $threats[] = 'Suspicious filename pattern';
            $threatLevel = max($threatLevel, self::THREAT_MEDIUM);
        }

        // Trigger incident if threats detected
        if (!empty($threats)) {
            $this->triggerSecurityIncident(self::INCIDENT_SUSPICIOUS_UPLOAD, [
                'user_id' => $user->id,
                'filename' => $filename,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'threats' => $threats,
                'threat_level' => $threatLevel,
                'application_id' => $application?->id,
            ]);
        }

        return [
            'safe' => empty($threats),
            'threats' => $threats,
            'threat_level' => $threatLevel,
            'recommendations' => $this->getUploadRecommendations($threats),
        ];
    }

    /**
     * Monitor user activity patterns
     */
    public function monitorUserActivity(User $user, string $action, array $context = []): void
    {
        $activityKey = "user_activity:{$user->id}:" . now()->format('Y-m-d-H-i');
        $actionsThisMinute = Cache::get($activityKey, 0) + 1;
        Cache::put($activityKey, $actionsThisMinute, now()->addMinutes(2));

        // Check for rapid actions
        if ($actionsThisMinute > self::MAX_ACTIONS_PER_MINUTE) {
            $this->triggerSecurityIncident(self::INCIDENT_RAPID_ACTIONS, [
                'user_id' => $user->id,
                'actions_per_minute' => $actionsThisMinute,
                'current_action' => $action,
                'threat_level' => self::THREAT_HIGH,
                'context' => $context,
            ]);
        }

        // Check for unusual access patterns
        $this->checkUnusualAccessPatterns($user, $action, $context);

        // Log activity
        Log::channel('security')->debug('User activity monitored', [
            'user_id' => $user->id,
            'action' => $action,
            'actions_this_minute' => $actionsThisMinute,
            'ip_address' => Request::ip(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Monitor IP address changes
     */
    public function monitorIPChange(User $user, string $newIpAddress): void
    {
        $lastIpKey = "last_ip:{$user->id}";
        $lastIpAddress = Cache::get($lastIpKey);

        if ($lastIpAddress && $lastIpAddress !== $newIpAddress) {
            // Check if IP change is suspicious (different country/region)
            $suspiciousChange = $this->isIPChangeSuspicious($lastIpAddress, $newIpAddress);

            if ($suspiciousChange) {
                $this->triggerSecurityIncident(self::INCIDENT_IP_CHANGE, [
                    'user_id' => $user->id,
                    'previous_ip' => $lastIpAddress,
                    'new_ip' => $newIpAddress,
                    'threat_level' => self::THREAT_MEDIUM,
                    'geolocation_change' => true,
                ]);
            }
        }

        // Update last known IP
        Cache::put($lastIpKey, $newIpAddress, now()->addDays(7));
    }

    /**
     * Scan for malicious file content
     */
    public function scanFileContent(string $filePath, string $filename): array
    {
        $threats = [];
        $threatLevel = self::THREAT_LOW;

        try {
            // Read file content for scanning
            $content = file_get_contents($filePath);
            
            // Check for malicious patterns
            $maliciousPatterns = [
                '/eval\s*\(/i' => 'PHP eval() function detected',
                '/exec\s*\(/i' => 'Command execution function detected',
                '/system\s*\(/i' => 'System command function detected',
                '/<script[^>]*>/i' => 'JavaScript code detected',
                '/javascript:/i' => 'JavaScript protocol detected',
                '/vbscript:/i' => 'VBScript protocol detected',
            ];

            foreach ($maliciousPatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    $threats[] = $description;
                    $threatLevel = self::THREAT_HIGH;
                }
            }

            // Check file size vs content ratio (potential binary in text file)
            if (strlen($content) > 1024) {
                $printableChars = preg_match_all('/[[:print:]]/', $content);
                $printableRatio = $printableChars / strlen($content);
                
                if ($printableRatio < 0.7) {
                    $threats[] = 'Potential binary content in text file';
                    $threatLevel = max($threatLevel, self::THREAT_MEDIUM);
                }
            }

        } catch (\Exception $e) {
            Log::error('File content scan failed', [
                'filename' => $filename,
                'error' => $e->getMessage(),
            ]);
            $threats[] = 'File content scan failed';
        }

        if (!empty($threats)) {
            $this->triggerSecurityIncident(self::INCIDENT_MALICIOUS_FILE, [
                'filename' => $filename,
                'file_path' => $filePath,
                'threats' => $threats,
                'threat_level' => $threatLevel,
            ]);
        }

        return [
            'safe' => empty($threats),
            'threats' => $threats,
            'threat_level' => $threatLevel,
        ];
    }

    /**
     * Generate security report
     */
    public function generateSecurityReport(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(7);
        $dateTo = $filters['date_to'] ?? now();

        // This would query actual security incidents from database
        $incidents = $this->getSecurityIncidents($dateFrom, $dateTo);

        return [
            'report_period' => [
                'from' => $dateFrom->toDateString(),
                'to' => $dateTo->toDateString(),
            ],
            'total_incidents' => count($incidents),
            'incidents_by_type' => $this->groupIncidentsByType($incidents),
            'incidents_by_threat_level' => $this->groupIncidentsByThreatLevel($incidents),
            'top_threats' => $this->getTopThreats($incidents),
            'security_score' => $this->calculateSecurityScore($incidents),
            'recommendations' => $this->getSecurityRecommendations($incidents),
            'recent_incidents' => array_slice($incidents, 0, 10),
        ];
    }

    /**
     * Trigger security incident
     */
    private function triggerSecurityIncident(string $incidentType, array $details): void
    {
        $incident = [
            'type' => $incidentType,
            'details' => $details,
            'timestamp' => now()->toISOString(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'threat_level' => $details['threat_level'] ?? self::THREAT_LOW,
        ];

        // Log incident
        Log::channel('security')->warning('Security incident detected', $incident);

        // Trigger event for real-time notifications
        event(new SecurityIncidentDetected($incident));

        // Store in audit trail if verification service is available
        if (app()->bound(VerificationAuditService::class)) {
            app(VerificationAuditService::class)->logSecurityIncident(
                $incidentType,
                $details,
                auth()->user()
            );
        }
    }

    /**
     * Check unusual access patterns
     */
    private function checkUnusualAccessPatterns(User $user, string $action, array $context): void
    {
        $currentHour = now()->hour;
        
        // Check for unusual time access
        if ($currentHour < 6 || $currentHour > 22) {
            $this->triggerSecurityIncident(self::INCIDENT_UNUSUAL_ACCESS, [
                'user_id' => $user->id,
                'action' => $action,
                'access_time' => now()->toISOString(),
                'hour' => $currentHour,
                'threat_level' => self::THREAT_LOW,
                'reason' => 'Access outside normal business hours',
            ]);
        }

        // Check for weekend access to sensitive functions
        if (now()->isWeekend() && in_array($action, ['approve_application', 'reject_application'])) {
            $this->triggerSecurityIncident(self::INCIDENT_UNUSUAL_ACCESS, [
                'user_id' => $user->id,
                'action' => $action,
                'access_time' => now()->toISOString(),
                'threat_level' => self::THREAT_MEDIUM,
                'reason' => 'Sensitive action performed during weekend',
            ]);
        }
    }

    /**
     * Check if IP change is suspicious
     */
    private function isIPChangeSuspicious(string $oldIp, string $newIp): bool
    {
        // Simple check - in production, this would use geolocation services
        $oldParts = explode('.', $oldIp);
        $newParts = explode('.', $newIp);
        
        // Consider suspicious if first two octets are different (different network)
        return ($oldParts[0] !== $newParts[0] || $oldParts[1] !== $newParts[1]);
    }

    /**
     * Get upload recommendations based on threats
     */
    private function getUploadRecommendations(array $threats): array
    {
        $recommendations = [];
        
        foreach ($threats as $threat) {
            if (strpos($threat, 'extension') !== false) {
                $recommendations[] = 'Use standard document formats (PDF, DOC, JPG, PNG)';
            }
            if (strpos($threat, 'size') !== false) {
                $recommendations[] = 'Compress large files before uploading';
            }
            if (strpos($threat, 'filename') !== false) {
                $recommendations[] = 'Use simple filenames without special characters';
            }
            if (strpos($threat, 'uploads') !== false) {
                $recommendations[] = 'Limit file uploads to necessary documents only';
            }
        }
        
        return array_unique($recommendations);
    }

    /**
     * Get security incidents (mock implementation)
     */
    private function getSecurityIncidents(Carbon $dateFrom, Carbon $dateTo): array
    {
        // In production, this would query the database
        return [];
    }

    /**
     * Group incidents by type
     */
    private function groupIncidentsByType(array $incidents): array
    {
        $grouped = [];
        foreach ($incidents as $incident) {
            $type = $incident['type'] ?? 'unknown';
            $grouped[$type] = ($grouped[$type] ?? 0) + 1;
        }
        return $grouped;
    }

    /**
     * Group incidents by threat level
     */
    private function groupIncidentsByThreatLevel(array $incidents): array
    {
        $grouped = [];
        foreach ($incidents as $incident) {
            $level = $incident['threat_level'] ?? self::THREAT_LOW;
            $grouped[$level] = ($grouped[$level] ?? 0) + 1;
        }
        return $grouped;
    }

    /**
     * Get top threats
     */
    private function getTopThreats(array $incidents): array
    {
        $threats = $this->groupIncidentsByType($incidents);
        arsort($threats);
        return array_slice($threats, 0, 5, true);
    }

    /**
     * Calculate security score
     */
    private function calculateSecurityScore(array $incidents): float
    {
        $totalIncidents = count($incidents);
        $criticalIncidents = count(array_filter($incidents, fn($i) => ($i['threat_level'] ?? '') === self::THREAT_CRITICAL));
        $highIncidents = count(array_filter($incidents, fn($i) => ($i['threat_level'] ?? '') === self::THREAT_HIGH));
        
        if ($totalIncidents === 0) {
            return 100.0;
        }
        
        $score = 100 - (($criticalIncidents * 20) + ($highIncidents * 10) + ($totalIncidents * 2));
        return max(0, min(100, $score));
    }

    /**
     * Get security recommendations
     */
    private function getSecurityRecommendations(array $incidents): array
    {
        $recommendations = [];
        
        if (count($incidents) > 10) {
            $recommendations[] = 'Consider implementing additional security measures';
        }
        
        $criticalCount = count(array_filter($incidents, fn($i) => ($i['threat_level'] ?? '') === self::THREAT_CRITICAL));
        if ($criticalCount > 0) {
            $recommendations[] = 'Immediate review of critical security incidents required';
        }
        
        return $recommendations;
    }
}
