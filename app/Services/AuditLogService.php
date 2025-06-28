<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class AuditLogService
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_UPLOAD = 'upload';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';

    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';
    const RISK_CRITICAL = 'critical';

    /**
     * Log an audit event
     */
    public function log($action, $resource, $resourceId = null, $details = [], $riskLevel = self::RISK_LOW)
    {
        $auditData = [
            'user_id' => Auth::guard('admin')->id() ?? Auth::id(),
            'user_type' => Auth::guard('admin')->check() ? 'admin' : 'user',
            'action' => $action,
            'resource' => $resource,
            'resource_id' => $resourceId,
            'details' => json_encode($details),
            'risk_level' => $riskLevel,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'session_id' => session()->getId(),
            'created_at' => now(),
        ];

        // Store in audit_logs table
        DB::table('audit_logs')->insert($auditData);

        // Log high-risk activities immediately
        if (in_array($riskLevel, [self::RISK_HIGH, self::RISK_CRITICAL])) {
            $this->logHighRiskActivity($auditData);
        }

        // Check for suspicious patterns
        $this->checkSuspiciousActivity($auditData);
    }

    /**
     * Log user authentication events
     */
    public function logAuthentication($action, $userId, $success = true, $details = [])
    {
        $riskLevel = $success ? self::RISK_LOW : self::RISK_MEDIUM;
        
        $authDetails = array_merge($details, [
            'success' => $success,
            'timestamp' => now()->toISOString(),
            'browser' => $this->getBrowserInfo(),
            'location' => $this->getLocationInfo(),
        ]);

        $this->log($action, 'authentication', $userId, $authDetails, $riskLevel);

        // Track failed login attempts
        if (!$success) {
            $this->trackFailedLoginAttempts($userId);
        }
    }

    /**
     * Log data access events
     */
    public function logDataAccess($resource, $resourceId, $action = self::ACTION_VIEW, $sensitiveData = false)
    {
        $riskLevel = $sensitiveData ? self::RISK_MEDIUM : self::RISK_LOW;
        
        $details = [
            'sensitive_data' => $sensitiveData,
            'access_time' => now()->toISOString(),
            'resource_type' => $resource,
        ];

        $this->log($action, $resource, $resourceId, $details, $riskLevel);
    }

    /**
     * Log administrative actions
     */
    public function logAdminAction($action, $resource, $resourceId = null, $changes = [], $targetUserId = null)
    {
        $riskLevel = $this->determineAdminActionRisk($action, $resource);
        
        $details = [
            'changes' => $changes,
            'target_user_id' => $targetUserId,
            'admin_level' => Auth::guard('admin')->user()->role ?? 'unknown',
            'action_timestamp' => now()->toISOString(),
        ];

        $this->log($action, $resource, $resourceId, $details, $riskLevel);
    }

    /**
     * Log file operations
     */
    public function logFileOperation($action, $filename, $fileSize = null, $fileType = null)
    {
        $riskLevel = in_array($action, [self::ACTION_UPLOAD, self::ACTION_DOWNLOAD]) ? self::RISK_MEDIUM : self::RISK_LOW;
        
        $details = [
            'filename' => $filename,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'operation_time' => now()->toISOString(),
        ];

        $this->log($action, 'file', null, $details, $riskLevel);
    }

    /**
     * Log system events
     */
    public function logSystemEvent($event, $details = [], $riskLevel = self::RISK_LOW)
    {
        $systemDetails = array_merge($details, [
            'system_time' => now()->toISOString(),
            'server_info' => [
                'php_version' => PHP_VERSION,
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
            ],
        ]);

        $this->log($event, 'system', null, $systemDetails, $riskLevel);
    }

    /**
     * Get audit logs with filtering
     */
    public function getAuditLogs($filters = [], $limit = 100, $offset = 0)
    {
        $query = DB::table('audit_logs')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['resource'])) {
            $query->where('resource', $filters['resource']);
        }

        if (isset($filters['risk_level'])) {
            $query->where('risk_level', $filters['risk_level']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        return $query->limit($limit)->offset($offset)->get();
    }

    /**
     * Get security analytics
     */
    public function getSecurityAnalytics($period = 30)
    {
        $startDate = now()->subDays($period);

        return [
            'total_events' => $this->getTotalEvents($startDate),
            'high_risk_events' => $this->getHighRiskEvents($startDate),
            'failed_logins' => $this->getFailedLogins($startDate),
            'suspicious_activities' => $this->getSuspiciousActivities($startDate),
            'top_users_by_activity' => $this->getTopUsersByActivity($startDate),
            'activity_by_hour' => $this->getActivityByHour($startDate),
            'risk_distribution' => $this->getRiskDistribution($startDate),
            'geographic_distribution' => $this->getGeographicDistribution($startDate),
        ];
    }

    /**
     * Generate compliance report
     */
    public function generateComplianceReport($startDate, $endDate, $format = 'array')
    {
        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $this->getComplianceSummary($startDate, $endDate),
            'user_activities' => $this->getUserActivities($startDate, $endDate),
            'data_access' => $this->getDataAccessReport($startDate, $endDate),
            'admin_actions' => $this->getAdminActionsReport($startDate, $endDate),
            'security_events' => $this->getSecurityEventsReport($startDate, $endDate),
        ];

        if ($format === 'json') {
            return json_encode($report, JSON_PRETTY_PRINT);
        }

        return $report;
    }

    /**
     * Private helper methods
     */
    private function logHighRiskActivity($auditData)
    {
        // Log to separate high-risk table or external security system
        \Log::warning('High-risk activity detected', $auditData);
        
        // Could also send to external SIEM system
        // $this->sendToSIEM($auditData);
    }

    private function checkSuspiciousActivity($auditData)
    {
        $userId = $auditData['user_id'];
        $ipAddress = $auditData['ip_address'];
        
        // Check for rapid successive actions
        $recentActions = DB::table('audit_logs')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentActions > 50) { // More than 50 actions in 5 minutes
            $this->flagSuspiciousActivity($userId, 'rapid_actions', [
                'action_count' => $recentActions,
                'time_window' => '5_minutes'
            ]);
        }

        // Check for unusual IP addresses
        $this->checkUnusualIPActivity($userId, $ipAddress);
        
        // Check for privilege escalation attempts
        $this->checkPrivilegeEscalation($auditData);
    }

    private function trackFailedLoginAttempts($userId)
    {
        $failedAttempts = DB::table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', self::ACTION_LOGIN)
            ->where('details', 'like', '%"success":false%')
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($failedAttempts >= 5) {
            $this->flagSuspiciousActivity($userId, 'multiple_failed_logins', [
                'failed_attempts' => $failedAttempts,
                'time_window' => '1_hour'
            ]);
        }
    }

    private function flagSuspiciousActivity($userId, $type, $details)
    {
        $this->log('suspicious_activity', 'security', $userId, array_merge($details, [
            'activity_type' => $type,
            'flagged_at' => now()->toISOString(),
        ]), self::RISK_HIGH);
    }

    private function determineAdminActionRisk($action, $resource)
    {
        $highRiskActions = [self::ACTION_DELETE, 'ban', 'suspend', 'grant_permission'];
        $highRiskResources = ['user', 'admin', 'permission', 'role'];

        if (in_array($action, $highRiskActions) || in_array($resource, $highRiskResources)) {
            return self::RISK_HIGH;
        }

        $mediumRiskActions = [self::ACTION_UPDATE, self::ACTION_APPROVE, self::ACTION_REJECT];
        if (in_array($action, $mediumRiskActions)) {
            return self::RISK_MEDIUM;
        }

        return self::RISK_LOW;
    }

    private function getBrowserInfo()
    {
        $userAgent = Request::userAgent();
        // Parse user agent to extract browser info
        return [
            'user_agent' => $userAgent,
            'browser' => $this->parseBrowser($userAgent),
            'platform' => $this->parsePlatform($userAgent),
        ];
    }

    private function getLocationInfo()
    {
        $ip = Request::ip();
        // In production, you'd use a GeoIP service
        return [
            'ip' => $ip,
            'country' => 'Unknown',
            'city' => 'Unknown',
        ];
    }

    private function parseBrowser($userAgent)
    {
        // Simple browser detection
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    private function parsePlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';
        return 'Unknown';
    }

    private function checkUnusualIPActivity($userId, $ipAddress)
    {
        // Check if this IP is new for this user
        $previousIPs = DB::table('audit_logs')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct()
            ->pluck('ip_address');

        if (!$previousIPs->contains($ipAddress)) {
            $this->flagSuspiciousActivity($userId, 'new_ip_address', [
                'new_ip' => $ipAddress,
                'previous_ips' => $previousIPs->toArray(),
            ]);
        }
    }

    private function checkPrivilegeEscalation($auditData)
    {
        // Check for attempts to access resources above user's privilege level
        // This would be implemented based on your specific authorization logic
    }

    // Analytics helper methods
    private function getTotalEvents($startDate)
    {
        return DB::table('audit_logs')->where('created_at', '>=', $startDate)->count();
    }

    private function getHighRiskEvents($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->whereIn('risk_level', [self::RISK_HIGH, self::RISK_CRITICAL])
            ->count();
    }

    private function getFailedLogins($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->where('action', self::ACTION_LOGIN)
            ->where('details', 'like', '%"success":false%')
            ->count();
    }

    private function getSuspiciousActivities($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->where('action', 'suspicious_activity')
            ->count();
    }

    private function getTopUsersByActivity($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->select('user_id', DB::raw('COUNT(*) as activity_count'))
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getActivityByHour($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    private function getRiskDistribution($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->select('risk_level', DB::raw('COUNT(*) as count'))
            ->groupBy('risk_level')
            ->get();
    }

    private function getGeographicDistribution($startDate)
    {
        return DB::table('audit_logs')
            ->where('created_at', '>=', $startDate)
            ->select('ip_address', DB::raw('COUNT(*) as count'))
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();
    }

    // Compliance report helper methods
    private function getComplianceSummary($startDate, $endDate) { return []; }
    private function getUserActivities($startDate, $endDate) { return []; }
    private function getDataAccessReport($startDate, $endDate) { return []; }
    private function getAdminActionsReport($startDate, $endDate) { return []; }
    private function getSecurityEventsReport($startDate, $endDate) { return []; }
}
