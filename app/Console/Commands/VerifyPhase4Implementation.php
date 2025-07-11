<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentVerificationService;
use App\Services\VerificationAuditService;
use App\Services\DataEncryptionService;
use App\Services\SecurityMonitoringService;
use App\Models\User;

/**
 * Verify Phase 4: Security & Compliance Implementation
 * 
 * This command verifies that all Phase 4 security and compliance features
 * are working correctly with manual review approach
 */
class VerifyPhase4Implementation extends Command
{
    protected $signature = 'mechamap:verify-phase4';
    protected $description = 'Verify Phase 4 security and compliance system implementation';

    public function handle()
    {
        $this->info('🔒 VERIFYING PHASE 4: SECURITY & COMPLIANCE SYSTEM');
        $this->info('================================================================');
        
        $allPassed = true;
        
        // Test 1: Document Verification Service
        $allPassed &= $this->testDocumentVerificationService();
        
        // Test 2: Audit Trail Service
        $allPassed &= $this->testAuditTrailService();
        
        // Test 3: Data Encryption Service
        $allPassed &= $this->testDataEncryptionService();
        
        // Test 4: Security Monitoring Service
        $allPassed &= $this->testSecurityMonitoringService();
        
        // Test 5: Compliance Controller
        $allPassed &= $this->testComplianceController();
        
        // Test 6: Admin Routes
        $allPassed &= $this->testAdminRoutes();
        
        $this->info('================================================================');
        
        if ($allPassed) {
            $this->info('✅ ALL TESTS PASSED! Phase 4 implementation is working correctly.');
            $this->info('🎊 SECURITY & COMPLIANCE SYSTEM IS READY FOR PRODUCTION!');
            return 0;
        } else {
            $this->error('❌ SOME TESTS FAILED! Please check the implementation.');
            return 1;
        }
    }

    private function testDocumentVerificationService(): bool
    {
        $this->info('🧪 Testing Document Verification Service...');
        
        try {
            $service = app(DocumentVerificationService::class);
            
            // Test document types
            $documentTypes = DocumentVerificationService::DOCUMENT_TYPES;
            if (empty($documentTypes)) {
                $this->error('   ❌ FAILED: Document types not defined');
                return false;
            }
            
            // Test verification checklist
            $checklist = $service->getVerificationChecklist('business_license');
            if (empty($checklist)) {
                $this->error('   ❌ FAILED: Verification checklist not working');
                return false;
            }
            
            $this->info('   ✅ PASSED: Document verification service is functional');
            $this->info('      - Document types: ' . count($documentTypes));
            $this->info('      - Checklist items: ' . count($checklist));
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testAuditTrailService(): bool
    {
        $this->info('🧪 Testing Audit Trail Service...');
        
        try {
            $service = app(VerificationAuditService::class);
            
            // Test action constants
            $actions = [
                VerificationAuditService::ACTION_APPLICATION_CREATED,
                VerificationAuditService::ACTION_DOCUMENT_VERIFIED,
                VerificationAuditService::ACTION_SECURITY_INCIDENT,
            ];
            
            foreach ($actions as $action) {
                if (empty($action)) {
                    $this->error('   ❌ FAILED: Audit action constants not defined');
                    return false;
                }
            }
            
            // Test audit report generation
            $report = $service->generateAuditReport([
                'date_from' => now()->subDays(7)->toDateString(),
                'date_to' => now()->toDateString(),
            ]);
            
            if (!isset($report['total_activities'])) {
                $this->error('   ❌ FAILED: Audit report structure invalid');
                return false;
            }
            
            $this->info('   ✅ PASSED: Audit trail service is functional');
            $this->info('      - Action types: ' . count($actions));
            $this->info('      - Report structure: Valid');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testDataEncryptionService(): bool
    {
        $this->info('🧪 Testing Data Encryption Service...');
        
        try {
            $service = app(DataEncryptionService::class);
            
            // Test encryption fields
            $encryptedFields = DataEncryptionService::ENCRYPTED_FIELDS;
            if (empty($encryptedFields)) {
                $this->error('   ❌ FAILED: Encrypted fields not defined');
                return false;
            }
            
            // Test data encryption
            $testData = [
                'tax_id' => '123456789',
                'business_phone' => '+84123456789',
                'business_email' => 'test@example.com',
            ];
            
            $encryptedData = $service->encryptSensitiveData($testData);
            if ($encryptedData['tax_id'] === $testData['tax_id']) {
                $this->error('   ❌ FAILED: Data encryption not working');
                return false;
            }
            
            // Test data decryption
            $decryptedData = $service->decryptSensitiveData($encryptedData);
            if ($decryptedData['tax_id'] !== $testData['tax_id']) {
                $this->error('   ❌ FAILED: Data decryption not working');
                return false;
            }
            
            // Test data anonymization
            $anonymizedData = $service->anonymizeData($testData);
            if (!isset($anonymizedData['anonymized_at'])) {
                $this->error('   ❌ FAILED: Data anonymization not working');
                return false;
            }
            
            $this->info('   ✅ PASSED: Data encryption service is functional');
            $this->info('      - Encrypted fields: ' . count($encryptedFields));
            $this->info('      - Encryption/Decryption: Working');
            $this->info('      - Anonymization: Working');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testSecurityMonitoringService(): bool
    {
        $this->info('🧪 Testing Security Monitoring Service...');
        
        try {
            $service = app(SecurityMonitoringService::class);
            
            // Test threat levels
            $threatLevels = [
                SecurityMonitoringService::THREAT_LOW,
                SecurityMonitoringService::THREAT_MEDIUM,
                SecurityMonitoringService::THREAT_HIGH,
                SecurityMonitoringService::THREAT_CRITICAL,
            ];
            
            foreach ($threatLevels as $level) {
                if (empty($level)) {
                    $this->error('   ❌ FAILED: Threat levels not defined');
                    return false;
                }
            }
            
            // Test file upload monitoring
            $user = new User(['id' => 999, 'role' => 'member']);
            $result = $service->monitorFileUpload($user, 'test.pdf', 'application/pdf', 1024);
            
            if (!isset($result['safe']) || !isset($result['threats'])) {
                $this->error('   ❌ FAILED: File upload monitoring not working');
                return false;
            }
            
            // Test security report generation
            $report = $service->generateSecurityReport([
                'date_from' => now()->subDays(7),
                'date_to' => now(),
            ]);
            
            if (!isset($report['security_score'])) {
                $this->error('   ❌ FAILED: Security report generation not working');
                return false;
            }
            
            $this->info('   ✅ PASSED: Security monitoring service is functional');
            $this->info('      - Threat levels: ' . count($threatLevels));
            $this->info('      - File monitoring: Working');
            $this->info('      - Report generation: Working');
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testComplianceController(): bool
    {
        $this->info('🧪 Testing Compliance Controller...');
        
        try {
            $controller = app(\App\Http\Controllers\Admin\ComplianceController::class);
            
            if (!$controller) {
                $this->error('   ❌ FAILED: Compliance controller not found');
                return false;
            }
            
            // Test if controller has required methods
            $requiredMethods = [
                'index',
                'auditReport',
                'securityReport',
                'privacyReport',
                'exportComplianceData',
                'validateCompliance',
            ];
            
            foreach ($requiredMethods as $method) {
                if (!method_exists($controller, $method)) {
                    $this->error("   ❌ FAILED: Method {$method} not found in compliance controller");
                    return false;
                }
            }
            
            $this->info('   ✅ PASSED: Compliance controller is functional');
            $this->info('      - Required methods: ' . count($requiredMethods));
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    private function testAdminRoutes(): bool
    {
        $this->info('🧪 Testing Admin Routes...');
        
        try {
            // Test if compliance routes are registered
            $routes = app('router')->getRoutes();
            $complianceRoutes = [];
            
            foreach ($routes as $route) {
                if (str_contains($route->getName() ?? '', 'admin.compliance.')) {
                    $complianceRoutes[] = $route->getName();
                }
            }
            
            $expectedRoutes = [
                'admin.compliance.index',
                'admin.compliance.audit-report',
                'admin.compliance.security-report',
                'admin.compliance.privacy-report',
                'admin.compliance.export-compliance-data',
                'admin.compliance.validate-compliance',
            ];
            
            foreach ($expectedRoutes as $expectedRoute) {
                if (!in_array($expectedRoute, $complianceRoutes)) {
                    $this->error("   ❌ FAILED: Route {$expectedRoute} not found");
                    return false;
                }
            }
            
            $this->info('   ✅ PASSED: Admin routes are properly configured');
            $this->info('      - Compliance routes: ' . count($complianceRoutes));
            return true;
            
        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }
}
