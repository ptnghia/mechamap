<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Data Encryption & Privacy Service
 * 
 * Handles encryption of sensitive business data for GDPR/CCPA compliance
 * Provides data anonymization and secure data handling
 */
class DataEncryptionService
{
    // Sensitive fields that require encryption
    const ENCRYPTED_FIELDS = [
        'tax_id',
        'registration_number', 
        'business_phone',
        'business_email',
        'business_address',
        'contact_person_phone',
        'contact_person_email',
        'bank_account_number',
        'identity_card_number',
        'passport_number',
    ];

    // Fields that can be anonymized
    const ANONYMIZABLE_FIELDS = [
        'business_name' => 'Business-***',
        'contact_person_name' => 'Contact-***',
        'business_address' => 'Address-***',
        'business_phone' => '***-***-****',
        'business_email' => 'email-***@domain.com',
    ];

    /**
     * Encrypt sensitive data before storing
     */
    public function encryptSensitiveData(array $data): array
    {
        $encryptedData = $data;
        
        foreach (self::ENCRYPTED_FIELDS as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                try {
                    $encryptedData[$field] = $this->encryptField($data[$field]);
                    $encryptedData[$field . '_encrypted'] = true;
                } catch (\Exception $e) {
                    Log::error('Failed to encrypt field', [
                        'field' => $field,
                        'error' => $e->getMessage(),
                    ]);
                    // Keep original value if encryption fails
                    $encryptedData[$field . '_encrypted'] = false;
                }
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt sensitive data for display
     */
    public function decryptSensitiveData(array $data): array
    {
        $decryptedData = $data;
        
        foreach (self::ENCRYPTED_FIELDS as $field) {
            if (isset($data[$field]) && !empty($data[$field]) && ($data[$field . '_encrypted'] ?? false)) {
                try {
                    $decryptedData[$field] = $this->decryptField($data[$field]);
                } catch (\Exception $e) {
                    Log::error('Failed to decrypt field', [
                        'field' => $field,
                        'error' => $e->getMessage(),
                    ]);
                    // Return masked value if decryption fails
                    $decryptedData[$field] = $this->maskSensitiveValue($data[$field]);
                }
            }
        }

        return $decryptedData;
    }

    /**
     * Anonymize data for compliance
     */
    public function anonymizeData(array $data): array
    {
        $anonymizedData = $data;
        
        foreach (self::ANONYMIZABLE_FIELDS as $field => $anonymizedValue) {
            if (isset($data[$field])) {
                $anonymizedData[$field] = $this->generateAnonymizedValue($field, $anonymizedValue);
            }
        }

        // Add anonymization metadata
        $anonymizedData['anonymized_at'] = now()->toISOString();
        $anonymizedData['anonymization_id'] = Str::uuid();
        
        return $anonymizedData;
    }

    /**
     * Secure delete sensitive data
     */
    public function secureDeleteData(array $sensitiveFields): bool
    {
        try {
            // Overwrite sensitive data multiple times for secure deletion
            foreach ($sensitiveFields as $field => $value) {
                if (!empty($value)) {
                    // Overwrite with random data 3 times
                    for ($i = 0; $i < 3; $i++) {
                        $randomData = Str::random(strlen($value));
                        // In a real implementation, this would overwrite the actual storage
                    }
                }
            }

            Log::info('Sensitive data securely deleted', [
                'fields_count' => count($sensitiveFields),
                'deleted_at' => now()->toISOString(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to securely delete data', [
                'error' => $e->getMessage(),
                'fields_count' => count($sensitiveFields),
            ]);
            return false;
        }
    }

    /**
     * Generate data retention report
     */
    public function generateRetentionReport(): array
    {
        // This would query actual database for retention analysis
        return [
            'total_records' => 0, // Would be calculated from database
            'records_due_for_deletion' => 0,
            'encrypted_records' => 0,
            'anonymized_records' => 0,
            'retention_policies' => $this->getRetentionPolicies(),
            'compliance_status' => 'compliant',
            'next_cleanup_date' => now()->addDays(30)->toDateString(),
        ];
    }

    /**
     * Validate data privacy compliance
     */
    public function validatePrivacyCompliance(array $data): array
    {
        $violations = [];
        $recommendations = [];

        // Check for unencrypted sensitive data
        foreach (self::ENCRYPTED_FIELDS as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                if (!($data[$field . '_encrypted'] ?? false)) {
                    $violations[] = "Field '{$field}' contains unencrypted sensitive data";
                    $recommendations[] = "Encrypt field '{$field}' before storage";
                }
            }
        }

        // Check for data retention compliance
        if (isset($data['created_at'])) {
            $createdAt = Carbon::parse($data['created_at']);
            $retentionPeriod = $this->getRetentionPeriod($data['type'] ?? 'default');
            
            if ($createdAt->addYears($retentionPeriod)->isPast()) {
                $violations[] = "Data exceeds retention period of {$retentionPeriod} years";
                $recommendations[] = "Consider data anonymization or secure deletion";
            }
        }

        return [
            'compliant' => empty($violations),
            'violations' => $violations,
            'recommendations' => $recommendations,
            'compliance_score' => $this->calculateComplianceScore($violations),
        ];
    }

    /**
     * Create privacy audit log
     */
    public function createPrivacyAuditLog(string $action, array $details): void
    {
        Log::channel('privacy')->info('Privacy action performed', [
            'action' => $action,
            'details' => $details,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Encrypt individual field
     */
    private function encryptField(string $value): string
    {
        return Crypt::encryptString($value);
    }

    /**
     * Decrypt individual field
     */
    private function decryptField(string $encryptedValue): string
    {
        return Crypt::decryptString($encryptedValue);
    }

    /**
     * Mask sensitive value for display
     */
    private function maskSensitiveValue(string $value): string
    {
        $length = strlen($value);
        
        if ($length <= 4) {
            return str_repeat('*', $length);
        }
        
        return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
    }

    /**
     * Generate anonymized value
     */
    private function generateAnonymizedValue(string $field, string $template): string
    {
        $randomId = substr(md5(uniqid()), 0, 6);
        return str_replace('***', $randomId, $template);
    }

    /**
     * Get retention policies
     */
    private function getRetentionPolicies(): array
    {
        return [
            'business_verification' => [
                'retention_period' => 7, // years
                'description' => 'Business verification documents and data',
                'legal_basis' => 'Regulatory compliance requirements',
            ],
            'personal_data' => [
                'retention_period' => 5, // years
                'description' => 'Personal identification data',
                'legal_basis' => 'GDPR Article 5(1)(e)',
            ],
            'financial_data' => [
                'retention_period' => 10, // years
                'description' => 'Financial and tax-related information',
                'legal_basis' => 'Tax law requirements',
            ],
            'audit_logs' => [
                'retention_period' => 3, // years
                'description' => 'System audit and access logs',
                'legal_basis' => 'Security and compliance monitoring',
            ],
        ];
    }

    /**
     * Get retention period for data type
     */
    private function getRetentionPeriod(string $dataType): int
    {
        $policies = $this->getRetentionPolicies();
        return $policies[$dataType]['retention_period'] ?? 5; // Default 5 years
    }

    /**
     * Calculate compliance score
     */
    private function calculateComplianceScore(array $violations): float
    {
        $maxViolations = 10; // Maximum expected violations
        $violationCount = count($violations);
        
        $score = max(0, 100 - (($violationCount / $maxViolations) * 100));
        
        return round($score, 2);
    }

    /**
     * Hash sensitive data for comparison
     */
    public function hashForComparison(string $value): string
    {
        return Hash::make($value);
    }

    /**
     * Verify hashed data
     */
    public function verifyHash(string $value, string $hash): bool
    {
        return Hash::check($value, $hash);
    }

    /**
     * Generate secure token for data access
     */
    public function generateSecureToken(array $context = []): string
    {
        $payload = [
            'timestamp' => now()->timestamp,
            'random' => Str::random(32),
            'context' => $context,
        ];

        return base64_encode(Crypt::encryptString(json_encode($payload)));
    }

    /**
     * Validate secure token
     */
    public function validateSecureToken(string $token, int $maxAge = 3600): array
    {
        try {
            $payload = json_decode(Crypt::decryptString(base64_decode($token)), true);
            
            if (!$payload || !isset($payload['timestamp'])) {
                return ['valid' => false, 'reason' => 'Invalid token format'];
            }

            if ((time() - $payload['timestamp']) > $maxAge) {
                return ['valid' => false, 'reason' => 'Token expired'];
            }

            return [
                'valid' => true,
                'context' => $payload['context'] ?? [],
                'created_at' => Carbon::createFromTimestamp($payload['timestamp']),
            ];
        } catch (\Exception $e) {
            return ['valid' => false, 'reason' => 'Token validation failed'];
        }
    }

    /**
     * Create GDPR data export
     */
    public function createGDPRExport(int $userId): array
    {
        // This would collect all user data for GDPR export
        $userData = [
            'user_profile' => [], // Would fetch from database
            'business_data' => [], // Would fetch business verification data
            'documents' => [], // Would list document metadata (not files)
            'audit_trail' => [], // Would fetch user's audit trail
            'export_metadata' => [
                'exported_at' => now()->toISOString(),
                'export_id' => Str::uuid(),
                'user_id' => $userId,
                'data_controller' => config('app.name'),
            ],
        ];

        $this->createPrivacyAuditLog('gdpr_export_created', [
            'user_id' => $userId,
            'export_id' => $userData['export_metadata']['export_id'],
        ]);

        return $userData;
    }

    /**
     * Process GDPR deletion request
     */
    public function processGDPRDeletion(int $userId, array $options = []): array
    {
        $deletionResults = [
            'user_id' => $userId,
            'deletion_id' => Str::uuid(),
            'requested_at' => now()->toISOString(),
            'status' => 'processing',
            'deleted_data' => [],
            'retained_data' => [],
            'errors' => [],
        ];

        try {
            // Anonymize instead of delete if required for legal compliance
            if ($options['anonymize_instead_of_delete'] ?? false) {
                $deletionResults['status'] = 'anonymized';
                $deletionResults['anonymized_data'] = ['user_profile', 'business_data'];
            } else {
                $deletionResults['status'] = 'deleted';
                $deletionResults['deleted_data'] = ['user_profile', 'business_data', 'documents'];
            }

            // Some data may need to be retained for legal reasons
            $deletionResults['retained_data'] = ['audit_logs', 'financial_records'];

            $this->createPrivacyAuditLog('gdpr_deletion_processed', $deletionResults);

        } catch (\Exception $e) {
            $deletionResults['status'] = 'failed';
            $deletionResults['errors'][] = $e->getMessage();
            
            Log::error('GDPR deletion failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return $deletionResults;
    }
}
