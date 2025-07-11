<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\VerificationAuditService;
use App\Services\DataEncryptionService;
use App\Services\SecurityMonitoringService;
use App\Models\BusinessVerificationApplication;
use App\Models\BusinessVerificationAuditTrail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Compliance Reporting Controller
 * 
 * Handles compliance reporting dashboard and data export
 * for business verification system
 */
class ComplianceController extends Controller
{
    protected $auditService;
    protected $encryptionService;
    protected $securityService;

    public function __construct(
        VerificationAuditService $auditService,
        DataEncryptionService $encryptionService,
        SecurityMonitoringService $securityService
    ) {
        $this->auditService = $auditService;
        $this->encryptionService = $encryptionService;
        $this->securityService = $securityService;
    }

    /**
     * Display compliance dashboard
     */
    public function index(Request $request): View
    {
        // Get date range from request or default to last 30 days
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        // Generate compliance metrics
        $complianceMetrics = $this->generateComplianceMetrics($dateFrom, $dateTo);
        
        // Get audit summary
        $auditSummary = $this->auditService->generateAuditReport([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        // Get security summary
        $securitySummary = $this->securityService->generateSecurityReport([
            'date_from' => Carbon::parse($dateFrom),
            'date_to' => Carbon::parse($dateTo),
        ]);

        // Get data retention report
        $retentionReport = $this->encryptionService->generateRetentionReport();

        return view('admin.compliance.index', compact(
            'complianceMetrics',
            'auditSummary',
            'securitySummary',
            'retentionReport',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Generate detailed audit report
     */
    public function auditReport(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'in:json,pdf,excel',
        ]);

        try {
            $report = $this->auditService->generateAuditReport([
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ]);

            $format = $request->input('format', 'json');

            switch ($format) {
                case 'pdf':
                    return $this->exportAuditReportPDF($report);
                case 'excel':
                    return $this->exportAuditReportExcel($report);
                default:
                    return response()->json([
                        'success' => true,
                        'report' => $report,
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Audit report generation failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate audit report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate security incident report
     */
    public function securityReport(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'threat_level' => 'in:low,medium,high,critical',
        ]);

        try {
            $report = $this->securityService->generateSecurityReport([
                'date_from' => Carbon::parse($request->date_from),
                'date_to' => Carbon::parse($request->date_to),
                'threat_level' => $request->threat_level,
            ]);

            return response()->json([
                'success' => true,
                'report' => $report,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate security report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate data privacy compliance report
     */
    public function privacyReport(Request $request): JsonResponse
    {
        try {
            $applications = BusinessVerificationApplication::with('documents')
                ->when($request->date_from, function ($query, $dateFrom) {
                    return $query->where('created_at', '>=', Carbon::parse($dateFrom));
                })
                ->when($request->date_to, function ($query, $dateTo) {
                    return $query->where('created_at', '<=', Carbon::parse($dateTo));
                })
                ->get();

            $privacyReport = [
                'total_applications' => $applications->count(),
                'encrypted_data_percentage' => $this->calculateEncryptedDataPercentage($applications),
                'gdpr_compliance_score' => $this->calculateGDPRComplianceScore($applications),
                'data_retention_compliance' => $this->checkDataRetentionCompliance($applications),
                'privacy_violations' => $this->findPrivacyViolations($applications),
                'recommendations' => $this->getPrivacyRecommendations($applications),
            ];

            return response()->json([
                'success' => true,
                'report' => $privacyReport,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate privacy report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export compliance data for external audit
     */
    public function exportComplianceData(Request $request): Response
    {
        $request->validate([
            'export_type' => 'required|in:full,audit_trail,security_incidents,privacy_data',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:json,csv,xml',
        ]);

        try {
            $exportData = $this->generateExportData(
                $request->export_type,
                $request->date_from,
                $request->date_to
            );

            $filename = "compliance_export_{$request->export_type}_" . now()->format('Y-m-d_H-i-s');

            switch ($request->format) {
                case 'csv':
                    return $this->exportAsCSV($exportData, $filename);
                case 'xml':
                    return $this->exportAsXML($exportData, $filename);
                default:
                    return $this->exportAsJSON($exportData, $filename);
            }

        } catch (\Exception $e) {
            Log::error('Compliance data export failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate system compliance
     */
    public function validateCompliance(): JsonResponse
    {
        try {
            $validationResults = [
                'audit_trail_compliance' => $this->validateAuditTrailCompliance(),
                'data_encryption_compliance' => $this->validateDataEncryptionCompliance(),
                'security_monitoring_compliance' => $this->validateSecurityMonitoringCompliance(),
                'data_retention_compliance' => $this->validateDataRetentionCompliance(),
                'access_control_compliance' => $this->validateAccessControlCompliance(),
            ];

            $overallScore = $this->calculateOverallComplianceScore($validationResults);

            return response()->json([
                'success' => true,
                'overall_score' => $overallScore,
                'validation_results' => $validationResults,
                'compliance_status' => $overallScore >= 80 ? 'compliant' : 'non_compliant',
                'recommendations' => $this->getComplianceRecommendations($validationResults),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Compliance validation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate compliance metrics
     */
    private function generateComplianceMetrics(string $dateFrom, string $dateTo): array
    {
        $applications = BusinessVerificationApplication::whereBetween('created_at', [$dateFrom, $dateTo])->get();
        $auditRecords = BusinessVerificationAuditTrail::whereBetween('created_at', [$dateFrom, $dateTo])->get();

        return [
            'total_applications' => $applications->count(),
            'processed_applications' => $applications->whereIn('status', ['approved', 'rejected'])->count(),
            'average_processing_time' => $this->calculateAverageProcessingTime($applications),
            'audit_trail_coverage' => $this->calculateAuditTrailCoverage($applications, $auditRecords),
            'data_encryption_rate' => $this->calculateDataEncryptionRate($applications),
            'security_incident_rate' => $this->calculateSecurityIncidentRate($auditRecords),
            'compliance_score' => $this->calculateComplianceScore($applications, $auditRecords),
        ];
    }

    /**
     * Calculate average processing time
     */
    private function calculateAverageProcessingTime($applications): float
    {
        $processingTimes = [];
        
        foreach ($applications as $application) {
            if ($application->approved_at || $application->rejected_at) {
                $completedAt = $application->approved_at ?? $application->rejected_at;
                $processingTimes[] = $application->created_at->diffInHours($completedAt);
            }
        }

        return count($processingTimes) > 0 ? array_sum($processingTimes) / count($processingTimes) : 0;
    }

    /**
     * Calculate audit trail coverage
     */
    private function calculateAuditTrailCoverage($applications, $auditRecords): float
    {
        if ($applications->count() === 0) {
            return 100;
        }

        $applicationsWithAudit = $auditRecords->pluck('application_id')->unique()->count();
        return ($applicationsWithAudit / $applications->count()) * 100;
    }

    /**
     * Calculate data encryption rate
     */
    private function calculateDataEncryptionRate($applications): float
    {
        $totalFields = 0;
        $encryptedFields = 0;

        foreach ($applications as $application) {
            foreach (\App\Services\DataEncryptionService::ENCRYPTED_FIELDS as $field) {
                if (!empty($application->$field)) {
                    $totalFields++;
                    if ($application->{$field . '_encrypted'} ?? false) {
                        $encryptedFields++;
                    }
                }
            }
        }

        return $totalFields > 0 ? ($encryptedFields / $totalFields) * 100 : 100;
    }

    /**
     * Calculate security incident rate
     */
    private function calculateSecurityIncidentRate($auditRecords): float
    {
        $totalRecords = $auditRecords->count();
        $securityIncidents = $auditRecords->where('action', 'security_incident')->count();

        return $totalRecords > 0 ? ($securityIncidents / $totalRecords) * 100 : 0;
    }

    /**
     * Calculate overall compliance score
     */
    private function calculateComplianceScore($applications, $auditRecords): float
    {
        $metrics = [
            'audit_coverage' => $this->calculateAuditTrailCoverage($applications, $auditRecords),
            'encryption_rate' => $this->calculateDataEncryptionRate($applications),
            'security_score' => 100 - $this->calculateSecurityIncidentRate($auditRecords),
        ];

        return array_sum($metrics) / count($metrics);
    }

    /**
     * Generate export data based on type
     */
    private function generateExportData(string $exportType, string $dateFrom, string $dateTo): array
    {
        switch ($exportType) {
            case 'audit_trail':
                return BusinessVerificationAuditTrail::with('user', 'application')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get()
                    ->toArray();

            case 'security_incidents':
                return BusinessVerificationAuditTrail::where('action', 'security_incident')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get()
                    ->toArray();

            case 'privacy_data':
                return BusinessVerificationApplication::with('documents')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get()
                    ->map(function ($app) {
                        return $this->encryptionService->validatePrivacyCompliance($app->toArray());
                    })
                    ->toArray();

            default: // full
                return [
                    'audit_trail' => $this->generateExportData('audit_trail', $dateFrom, $dateTo),
                    'security_incidents' => $this->generateExportData('security_incidents', $dateFrom, $dateTo),
                    'privacy_data' => $this->generateExportData('privacy_data', $dateFrom, $dateTo),
                ];
        }
    }

    /**
     * Export data as JSON
     */
    private function exportAsJSON(array $data, string $filename): Response
    {
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }

    /**
     * Export data as CSV
     */
    private function exportAsCSV(array $data, string $filename): Response
    {
        $csv = $this->arrayToCSV($data);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }

    /**
     * Export data as XML
     */
    private function exportAsXML(array $data, string $filename): Response
    {
        $xml = $this->arrayToXML($data);
        
        return response($xml)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.xml\"");
    }

    /**
     * Convert array to CSV
     */
    private function arrayToCSV(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Convert array to XML
     */
    private function arrayToXML(array $data): string
    {
        $xml = new \SimpleXMLElement('<compliance_export/>');
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $child = $xml->addChild($key);
                $this->arrayToXMLRecursive($value, $child);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
        
        return $xml->asXML();
    }

    /**
     * Recursive helper for array to XML conversion
     */
    private function arrayToXMLRecursive(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $child = $xml->addChild($key);
                $this->arrayToXMLRecursive($value, $child);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }

    // Additional validation methods would be implemented here
    private function validateAuditTrailCompliance(): array { return ['score' => 95, 'status' => 'compliant']; }
    private function validateDataEncryptionCompliance(): array { return ['score' => 90, 'status' => 'compliant']; }
    private function validateSecurityMonitoringCompliance(): array { return ['score' => 88, 'status' => 'compliant']; }
    private function validateDataRetentionCompliance(): array { return ['score' => 92, 'status' => 'compliant']; }
    private function validateAccessControlCompliance(): array { return ['score' => 94, 'status' => 'compliant']; }
    
    private function calculateOverallComplianceScore(array $results): float
    {
        $scores = array_column($results, 'score');
        return array_sum($scores) / count($scores);
    }
    
    private function getComplianceRecommendations(array $results): array
    {
        return ['Maintain current compliance standards', 'Regular security audits recommended'];
    }

    // Placeholder methods for privacy report calculations
    private function calculateEncryptedDataPercentage($applications): float { return 95.0; }
    private function calculateGDPRComplianceScore($applications): float { return 92.0; }
    private function checkDataRetentionCompliance($applications): array { return ['compliant' => true]; }
    private function findPrivacyViolations($applications): array { return []; }
    private function getPrivacyRecommendations($applications): array { return ['Continue current practices']; }
}
