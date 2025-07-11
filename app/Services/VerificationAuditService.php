<?php

namespace App\Services;

use App\Models\BusinessVerificationApplication;
use App\Models\BusinessVerificationAuditTrail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

/**
 * Verification Audit Service - Enhanced Security Tracking
 * 
 * Comprehensive audit trail system for business verification activities
 * Tracks all admin actions, user activities, and system events
 */
class VerificationAuditService
{
    // Audit action types
    const ACTION_APPLICATION_CREATED = 'application_created';
    const ACTION_APPLICATION_SUBMITTED = 'application_submitted';
    const ACTION_APPLICATION_REVIEWED = 'application_reviewed';
    const ACTION_APPLICATION_APPROVED = 'application_approved';
    const ACTION_APPLICATION_REJECTED = 'application_rejected';
    const ACTION_DOCUMENT_UPLOADED = 'document_uploaded';
    const ACTION_DOCUMENT_VERIFIED = 'document_verified';
    const ACTION_DOCUMENT_REJECTED = 'document_rejected';
    const ACTION_ADDITIONAL_INFO_REQUESTED = 'additional_info_requested';
    const ACTION_REVIEWER_ASSIGNED = 'reviewer_assigned';
    const ACTION_STATUS_CHANGED = 'status_changed';
    const ACTION_NOTES_ADDED = 'notes_added';
    const ACTION_SECURITY_INCIDENT = 'security_incident';
    const ACTION_DATA_ACCESS = 'data_access';
    const ACTION_DATA_EXPORT = 'data_export';
    const ACTION_BULK_ACTION = 'bulk_action';

    // Risk levels
    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';
    const RISK_CRITICAL = 'critical';

    /**
     * Log business verification activity with enhanced security tracking
     */
    public function logActivity(
        BusinessVerificationApplication $application,
        User $actor,
        string $action,
        array $details = [],
        string $ipAddress = null
    ): BusinessVerificationAuditTrail {
        $ipAddress = $ipAddress ?? Request::ip();
        
        // Enhance details with security context
        $enhancedDetails = array_merge($details, [
            'user_agent' => Request::userAgent(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
            'actor_role' => $actor->role,
            'application_status_before' => $application->status,
        ]);

        // Determine risk level
        $riskLevel = $this->determineRiskLevel($action, $details);

        // Create audit record
        $auditRecord = BusinessVerificationAuditTrail::create([
            'application_id' => $application->id,
            'user_id' => $actor->id,
            'action' => $action,
            'details' => $enhancedDetails,
            'ip_address' => $ipAddress,
            'user_agent' => Request::userAgent(),
            'risk_level' => $riskLevel,
            'metadata' => [
                'application_type' => $application->application_type,
                'business_name' => $application->business_name,
                'actor_name' => $actor->name,
                'actor_email' => $actor->email,
            ],
        ]);

        // Log to system log for critical actions
        if (in_array($riskLevel, [self::RISK_HIGH, self::RISK_CRITICAL])) {
            Log::warning('High-risk verification activity', [
                'audit_id' => $auditRecord->id,
                'action' => $action,
                'actor_id' => $actor->id,
                'application_id' => $application->id,
                'risk_level' => $riskLevel,
                'ip_address' => $ipAddress,
            ]);
        }

        // Check for suspicious patterns
        $this->checkSuspiciousActivity($actor, $action, $ipAddress);

        return $auditRecord;
    }

    /**
     * Log document verification activity
     */
    public function logDocumentVerification(
        BusinessVerificationApplication $application,
        User $admin,
        string $action,
        array $documentDetails = []
    ): BusinessVerificationAuditTrail {
        return $this->logActivity($application, $admin, $action, [
            'document_verification' => true,
            'document_details' => $documentDetails,
            'verification_timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log bulk operations
     */
    public function logBulkOperation(
        array $applicationIds,
        User $admin,
        string $operation,
        array $operationDetails = []
    ): array {
        $auditRecords = [];
        
        foreach ($applicationIds as $applicationId) {
            try {
                $application = BusinessVerificationApplication::findOrFail($applicationId);
                $auditRecords[] = $this->logActivity($application, $admin, self::ACTION_BULK_ACTION, [
                    'bulk_operation' => true,
                    'operation_type' => $operation,
                    'total_applications' => count($applicationIds),
                    'operation_details' => $operationDetails,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to log bulk operation for application', [
                    'application_id' => $applicationId,
                    'admin_id' => $admin->id,
                    'operation' => $operation,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $auditRecords;
    }

    /**
     * Log security incident
     */
    public function logSecurityIncident(
        string $incidentType,
        array $incidentDetails,
        User $user = null,
        BusinessVerificationApplication $application = null
    ): BusinessVerificationAuditTrail {
        $details = array_merge($incidentDetails, [
            'incident_type' => $incidentType,
            'detected_at' => now()->toISOString(),
            'severity' => $this->determineIncidentSeverity($incidentType, $incidentDetails),
        ]);

        if ($application) {
            return $this->logActivity($application, $user ?? auth()->user(), self::ACTION_SECURITY_INCIDENT, $details);
        }

        // Create standalone security audit record
        return BusinessVerificationAuditTrail::create([
            'application_id' => null,
            'user_id' => $user?->id,
            'action' => self::ACTION_SECURITY_INCIDENT,
            'details' => $details,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'risk_level' => self::RISK_CRITICAL,
            'metadata' => [
                'incident_type' => $incidentType,
                'security_event' => true,
            ],
        ]);
    }

    /**
     * Get audit trail for application
     */
    public function getApplicationAuditTrail(
        BusinessVerificationApplication $application,
        array $filters = []
    ): \Illuminate\Database\Eloquent\Collection {
        $query = BusinessVerificationAuditTrail::where('application_id', $application->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['risk_level'])) {
            $query->where('risk_level', $filters['risk_level']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to']));
        }

        return $query->get();
    }

    /**
     * Get security incidents
     */
    public function getSecurityIncidents(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = BusinessVerificationAuditTrail::where('action', self::ACTION_SECURITY_INCIDENT)
            ->with('user', 'application')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['severity'])) {
            $query->whereJsonContains('details->severity', $filters['severity']);
        }

        if (!empty($filters['incident_type'])) {
            $query->whereJsonContains('details->incident_type', $filters['incident_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        return $query->get();
    }

    /**
     * Generate audit report
     */
    public function generateAuditReport(array $filters = []): array
    {
        $query = BusinessVerificationAuditTrail::query();

        // Apply filters
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to']));
        }

        $auditRecords = $query->with('user', 'application')->get();

        return [
            'total_activities' => $auditRecords->count(),
            'activities_by_action' => $auditRecords->groupBy('action')->map->count(),
            'activities_by_risk_level' => $auditRecords->groupBy('risk_level')->map->count(),
            'activities_by_user' => $auditRecords->groupBy('user.name')->map->count(),
            'security_incidents' => $auditRecords->where('action', self::ACTION_SECURITY_INCIDENT)->count(),
            'high_risk_activities' => $auditRecords->whereIn('risk_level', [self::RISK_HIGH, self::RISK_CRITICAL])->count(),
            'recent_activities' => $auditRecords->take(10)->toArray(),
            'compliance_metrics' => $this->calculateComplianceMetrics($auditRecords),
        ];
    }

    /**
     * Check for suspicious activity patterns
     */
    private function checkSuspiciousActivity(User $actor, string $action, string $ipAddress): void
    {
        // Check for rapid successive actions
        $recentActions = BusinessVerificationAuditTrail::where('user_id', $actor->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentActions > 20) {
            $this->logSecurityIncident('rapid_actions', [
                'user_id' => $actor->id,
                'action_count' => $recentActions,
                'time_window' => '5 minutes',
                'current_action' => $action,
            ], $actor);
        }

        // Check for IP address changes
        $lastIpAddress = BusinessVerificationAuditTrail::where('user_id', $actor->id)
            ->where('created_at', '>=', now()->subHours(1))
            ->orderBy('created_at', 'desc')
            ->value('ip_address');

        if ($lastIpAddress && $lastIpAddress !== $ipAddress) {
            $this->logSecurityIncident('ip_address_change', [
                'user_id' => $actor->id,
                'previous_ip' => $lastIpAddress,
                'current_ip' => $ipAddress,
                'action' => $action,
            ], $actor);
        }

        // Check for unusual time patterns
        $currentHour = now()->hour;
        if ($currentHour < 6 || $currentHour > 22) {
            $this->logSecurityIncident('unusual_time_access', [
                'user_id' => $actor->id,
                'access_time' => now()->toISOString(),
                'hour' => $currentHour,
                'action' => $action,
            ], $actor);
        }
    }

    /**
     * Determine risk level for action
     */
    private function determineRiskLevel(string $action, array $details): string
    {
        $highRiskActions = [
            self::ACTION_APPLICATION_APPROVED,
            self::ACTION_APPLICATION_REJECTED,
            self::ACTION_DATA_EXPORT,
            self::ACTION_BULK_ACTION,
        ];

        $criticalRiskActions = [
            self::ACTION_SECURITY_INCIDENT,
        ];

        if (in_array($action, $criticalRiskActions)) {
            return self::RISK_CRITICAL;
        }

        if (in_array($action, $highRiskActions)) {
            return self::RISK_HIGH;
        }

        // Check for bulk operations
        if (!empty($details['bulk_operation'])) {
            return self::RISK_HIGH;
        }

        // Check for sensitive data access
        if (!empty($details['sensitive_data_access'])) {
            return self::RISK_MEDIUM;
        }

        return self::RISK_LOW;
    }

    /**
     * Determine incident severity
     */
    private function determineIncidentSeverity(string $incidentType, array $details): string
    {
        $criticalIncidents = ['data_breach', 'unauthorized_access', 'system_compromise'];
        $highIncidents = ['rapid_actions', 'ip_address_change', 'failed_authentication'];
        $mediumIncidents = ['unusual_time_access', 'suspicious_pattern'];

        if (in_array($incidentType, $criticalIncidents)) {
            return 'critical';
        }

        if (in_array($incidentType, $highIncidents)) {
            return 'high';
        }

        if (in_array($incidentType, $mediumIncidents)) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Calculate compliance metrics
     */
    private function calculateComplianceMetrics(\Illuminate\Database\Eloquent\Collection $auditRecords): array
    {
        $totalApplications = $auditRecords->where('action', self::ACTION_APPLICATION_CREATED)->count();
        $approvedApplications = $auditRecords->where('action', self::ACTION_APPLICATION_APPROVED)->count();
        $rejectedApplications = $auditRecords->where('action', self::ACTION_APPLICATION_REJECTED)->count();

        return [
            'total_applications' => $totalApplications,
            'approval_rate' => $totalApplications > 0 ? ($approvedApplications / $totalApplications) * 100 : 0,
            'rejection_rate' => $totalApplications > 0 ? ($rejectedApplications / $totalApplications) * 100 : 0,
            'average_processing_time' => $this->calculateAverageProcessingTime($auditRecords),
            'compliance_score' => $this->calculateComplianceScore($auditRecords),
        ];
    }

    /**
     * Calculate average processing time
     */
    private function calculateAverageProcessingTime(\Illuminate\Database\Eloquent\Collection $auditRecords): float
    {
        $processingTimes = [];
        
        $applications = $auditRecords->groupBy('application_id');
        
        foreach ($applications as $applicationId => $records) {
            $created = $records->where('action', self::ACTION_APPLICATION_CREATED)->first();
            $completed = $records->whereIn('action', [self::ACTION_APPLICATION_APPROVED, self::ACTION_APPLICATION_REJECTED])->first();
            
            if ($created && $completed) {
                $processingTimes[] = $completed->created_at->diffInHours($created->created_at);
            }
        }

        return count($processingTimes) > 0 ? array_sum($processingTimes) / count($processingTimes) : 0;
    }

    /**
     * Calculate compliance score
     */
    private function calculateComplianceScore(\Illuminate\Database\Eloquent\Collection $auditRecords): float
    {
        $totalActions = $auditRecords->count();
        $highRiskActions = $auditRecords->where('risk_level', self::RISK_HIGH)->count();
        $criticalRiskActions = $auditRecords->where('risk_level', self::RISK_CRITICAL)->count();
        $securityIncidents = $auditRecords->where('action', self::ACTION_SECURITY_INCIDENT)->count();

        if ($totalActions === 0) {
            return 100;
        }

        $riskScore = (($highRiskActions * 2) + ($criticalRiskActions * 5) + ($securityIncidents * 10)) / $totalActions;
        $complianceScore = max(0, 100 - ($riskScore * 10));

        return round($complianceScore, 2);
    }
}
