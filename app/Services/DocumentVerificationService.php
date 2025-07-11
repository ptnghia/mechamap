<?php

namespace App\Services;

use App\Models\BusinessVerificationDocument;
use App\Models\BusinessVerificationApplication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Document Verification Service - Manual Review Approach
 * 
 * Handles document verification through manual admin review
 * instead of automated OCR/AI verification
 */
class DocumentVerificationService
{
    // Supported document types
    const DOCUMENT_TYPES = [
        'business_license' => 'Giấy phép kinh doanh',
        'tax_certificate' => 'Giấy chứng nhận thuế',
        'company_registration' => 'Giấy đăng ký doanh nghiệp',
        'identity_card' => 'CMND/CCCD',
        'passport' => 'Hộ chiếu',
        'bank_statement' => 'Sao kê ngân hàng',
        'address_proof' => 'Giấy tờ chứng minh địa chỉ',
        'other' => 'Tài liệu khác',
    ];

    // Document validation statuses
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REQUIRES_RESUBMISSION = 'requires_resubmission';

    /**
     * Upload and process document for manual review
     */
    public function uploadDocument(
        BusinessVerificationApplication $application,
        UploadedFile $file,
        string $documentType,
        array $metadata = []
    ): BusinessVerificationDocument {
        // Validate file
        $this->validateDocumentFile($file);
        
        // Generate secure filename
        $filename = $this->generateSecureFilename($file, $application->user_id, $documentType);
        
        // Store file securely
        $path = $this->storeDocumentSecurely($file, $filename);
        
        // Create document record
        $document = BusinessVerificationDocument::create([
            'application_id' => $application->id,
            'document_type' => $documentType,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $filename,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'verification_status' => self::STATUS_PENDING,
            'metadata' => array_merge($metadata, [
                'uploaded_at' => now()->toISOString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        // Log document upload
        Log::info('Document uploaded for verification', [
            'application_id' => $application->id,
            'document_id' => $document->id,
            'document_type' => $documentType,
            'user_id' => $application->user_id,
            'filename' => $filename,
        ]);

        // Update application status if needed
        $this->updateApplicationStatusAfterUpload($application);

        return $document;
    }

    /**
     * Admin manual verification of document
     */
    public function verifyDocument(
        BusinessVerificationDocument $document,
        User $admin,
        string $status,
        string $notes = null,
        array $verificationData = []
    ): bool {
        $oldStatus = $document->verification_status;
        
        // Update document verification
        $document->update([
            'verification_status' => $status,
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'verification_notes' => $notes,
            'verification_data' => array_merge($verificationData, [
                'verified_by_name' => $admin->name,
                'verified_by_email' => $admin->email,
                'verification_timestamp' => now()->toISOString(),
                'previous_status' => $oldStatus,
            ]),
        ]);

        // Log verification action
        Log::info('Document verification completed', [
            'document_id' => $document->id,
            'application_id' => $document->application_id,
            'admin_id' => $admin->id,
            'status' => $status,
            'previous_status' => $oldStatus,
            'notes' => $notes,
        ]);

        // Create audit trail
        $this->createVerificationAuditTrail($document, $admin, $status, $notes);

        // Check if all documents are verified
        $this->checkApplicationCompleteness($document->application);

        return true;
    }

    /**
     * Bulk verify multiple documents
     */
    public function bulkVerifyDocuments(
        array $documentIds,
        User $admin,
        string $status,
        string $notes = null
    ): array {
        $results = [];
        
        foreach ($documentIds as $documentId) {
            try {
                $document = BusinessVerificationDocument::findOrFail($documentId);
                $this->verifyDocument($document, $admin, $status, $notes);
                $results[$documentId] = ['success' => true];
            } catch (\Exception $e) {
                $results[$documentId] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
                Log::error('Bulk document verification failed', [
                    'document_id' => $documentId,
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Get document verification checklist for admin
     */
    public function getVerificationChecklist(string $documentType): array
    {
        $checklists = [
            'business_license' => [
                'Tên doanh nghiệp khớp với thông tin đăng ký',
                'Số giấy phép kinh doanh hợp lệ',
                'Ngày cấp và ngày hết hạn rõ ràng',
                'Địa chỉ kinh doanh khớp với thông tin',
                'Ngành nghề kinh doanh phù hợp',
                'Chữ ký và con dấu cơ quan cấp',
                'Chất lượng ảnh/scan rõ nét',
            ],
            'tax_certificate' => [
                'Mã số thuế khớp với thông tin đăng ký',
                'Tên doanh nghiệp chính xác',
                'Địa chỉ thuế khớp với giấy phép',
                'Trạng thái hoạt động của mã số thuế',
                'Ngày cấp hợp lệ',
                'Chữ ký và con dấu cơ quan thuế',
            ],
            'identity_card' => [
                'Họ tên khớp với thông tin đăng ký',
                'Số CMND/CCCD hợp lệ',
                'Ảnh rõ nét, không bị che khuất',
                'Ngày sinh và nơi sinh rõ ràng',
                'Ngày cấp và nơi cấp hợp lệ',
                'Không có dấu hiệu giả mạo',
            ],
            'default' => [
                'Tài liệu rõ nét, đầy đủ thông tin',
                'Không có dấu hiệu chỉnh sửa',
                'Thông tin khớp với hồ sơ đăng ký',
                'Tài liệu còn hiệu lực',
                'Chữ ký và con dấu (nếu có) hợp lệ',
            ],
        ];

        return $checklists[$documentType] ?? $checklists['default'];
    }

    /**
     * Generate document verification report
     */
    public function generateVerificationReport(BusinessVerificationApplication $application): array
    {
        $documents = $application->documents;
        
        $report = [
            'application_id' => $application->id,
            'user_info' => [
                'name' => $application->user->name,
                'email' => $application->user->email,
                'business_name' => $application->business_name,
            ],
            'total_documents' => $documents->count(),
            'verified_documents' => $documents->where('verification_status', self::STATUS_APPROVED)->count(),
            'rejected_documents' => $documents->where('verification_status', self::STATUS_REJECTED)->count(),
            'pending_documents' => $documents->where('verification_status', self::STATUS_PENDING)->count(),
            'completion_percentage' => $this->calculateCompletionPercentage($application),
            'documents' => [],
            'verification_summary' => [],
        ];

        foreach ($documents as $document) {
            $report['documents'][] = [
                'id' => $document->id,
                'type' => $document->document_type,
                'type_display' => self::DOCUMENT_TYPES[$document->document_type] ?? $document->document_type,
                'status' => $document->verification_status,
                'verified_by' => $document->verifiedBy?->name,
                'verified_at' => $document->verified_at?->format('d/m/Y H:i'),
                'notes' => $document->verification_notes,
                'file_size' => $this->formatFileSize($document->file_size),
            ];
        }

        // Generate verification summary
        $report['verification_summary'] = $this->generateVerificationSummary($application);

        return $report;
    }

    /**
     * Validate uploaded document file
     */
    private function validateDocumentFile(UploadedFile $file): void
    {
        // Check file size (max 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \InvalidArgumentException('File size exceeds 10MB limit');
        }

        // Check file type
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid file type. Allowed: JPG, PNG, GIF, PDF, DOC, DOCX');
        }

        // Basic security check
        $this->performSecurityScan($file);
    }

    /**
     * Generate secure filename
     */
    private function generateSecureFilename(UploadedFile $file, int $userId, string $documentType): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        
        return "doc_{$userId}_{$documentType}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Store document securely
     */
    private function storeDocumentSecurely(UploadedFile $file, string $filename): string
    {
        $directory = 'business-verification/documents/' . date('Y/m');
        
        // Store with private visibility
        $path = $file->storeAs($directory, $filename, 'private');
        
        return $path;
    }

    /**
     * Perform basic security scan on uploaded file
     */
    private function performSecurityScan(UploadedFile $file): void
    {
        // Check for malicious patterns in filename
        $filename = $file->getClientOriginalName();
        $maliciousPatterns = [
            '/\.php$/i', '/\.exe$/i', '/\.bat$/i', '/\.cmd$/i',
            '/\.scr$/i', '/\.vbs$/i', '/\.js$/i', '/\.jar$/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                throw new \InvalidArgumentException('Potentially malicious file detected');
            }
        }

        // Basic content scan for PDF files
        if ($file->getMimeType() === 'application/pdf') {
            $content = file_get_contents($file->getPathname());
            if (strpos($content, '/JavaScript') !== false || strpos($content, '/JS') !== false) {
                Log::warning('PDF with JavaScript detected', [
                    'filename' => $filename,
                    'ip' => request()->ip(),
                ]);
            }
        }
    }

    /**
     * Update application status after document upload
     */
    private function updateApplicationStatusAfterUpload(BusinessVerificationApplication $application): void
    {
        $requiredDocuments = $this->getRequiredDocuments($application->application_type);
        $uploadedTypes = $application->documents->pluck('document_type')->toArray();
        
        $hasAllRequired = empty(array_diff($requiredDocuments, $uploadedTypes));
        
        if ($hasAllRequired && $application->status === 'pending') {
            $application->update(['status' => 'under_review']);
        }
    }

    /**
     * Get required documents for application type
     */
    private function getRequiredDocuments(string $applicationType): array
    {
        $requirements = [
            'manufacturer' => ['business_license', 'tax_certificate', 'identity_card'],
            'supplier' => ['business_license', 'tax_certificate', 'identity_card'],
            'brand' => ['business_license', 'tax_certificate', 'identity_card'],
            'verified_partner' => ['business_license', 'tax_certificate', 'identity_card', 'bank_statement'],
        ];

        return $requirements[$applicationType] ?? ['business_license', 'identity_card'];
    }

    /**
     * Create verification audit trail
     */
    private function createVerificationAuditTrail(
        BusinessVerificationDocument $document,
        User $admin,
        string $status,
        string $notes = null
    ): void {
        app(VerificationAuditService::class)->logDocumentVerification(
            $document->application,
            $admin,
            'document_verification',
            [
                'document_id' => $document->id,
                'document_type' => $document->document_type,
                'verification_status' => $status,
                'notes' => $notes,
            ]
        );
    }

    /**
     * Check if application is complete after document verification
     */
    private function checkApplicationCompleteness(BusinessVerificationApplication $application): void
    {
        $documents = $application->documents;
        $requiredDocuments = $this->getRequiredDocuments($application->application_type);
        
        $verifiedCount = $documents->where('verification_status', self::STATUS_APPROVED)->count();
        $rejectedCount = $documents->where('verification_status', self::STATUS_REJECTED)->count();
        
        if ($verifiedCount >= count($requiredDocuments)) {
            // All required documents verified - ready for final approval
            $application->update([
                'status' => 'documents_verified',
                'verification_score' => 100,
            ]);
        } elseif ($rejectedCount > 0) {
            // Some documents rejected - needs resubmission
            $application->update([
                'status' => 'requires_additional_info',
                'verification_score' => ($verifiedCount / count($requiredDocuments)) * 100,
            ]);
        }
    }

    /**
     * Calculate completion percentage
     */
    private function calculateCompletionPercentage(BusinessVerificationApplication $application): float
    {
        $requiredDocuments = $this->getRequiredDocuments($application->application_type);
        $verifiedDocuments = $application->documents->where('verification_status', self::STATUS_APPROVED)->count();
        
        return count($requiredDocuments) > 0 ? ($verifiedDocuments / count($requiredDocuments)) * 100 : 0;
    }

    /**
     * Generate verification summary
     */
    private function generateVerificationSummary(BusinessVerificationApplication $application): array
    {
        $documents = $application->documents;
        
        return [
            'overall_status' => $this->determineOverallStatus($application),
            'completion_percentage' => $this->calculateCompletionPercentage($application),
            'next_steps' => $this->getNextSteps($application),
            'issues_found' => $this->getIssuesFound($application),
            'recommendations' => $this->getRecommendations($application),
        ];
    }

    /**
     * Determine overall verification status
     */
    private function determineOverallStatus(BusinessVerificationApplication $application): string
    {
        $documents = $application->documents;
        $requiredCount = count($this->getRequiredDocuments($application->application_type));
        $verifiedCount = $documents->where('verification_status', self::STATUS_APPROVED)->count();
        $rejectedCount = $documents->where('verification_status', self::STATUS_REJECTED)->count();
        
        if ($verifiedCount >= $requiredCount) {
            return 'ready_for_approval';
        } elseif ($rejectedCount > 0) {
            return 'requires_resubmission';
        } else {
            return 'in_progress';
        }
    }

    /**
     * Get next steps for application
     */
    private function getNextSteps(BusinessVerificationApplication $application): array
    {
        $status = $this->determineOverallStatus($application);
        
        $nextSteps = [
            'ready_for_approval' => ['Admin final review and approval'],
            'requires_resubmission' => ['User needs to resubmit rejected documents'],
            'in_progress' => ['Continue document verification process'],
        ];

        return $nextSteps[$status] ?? ['Contact support for assistance'];
    }

    /**
     * Get issues found during verification
     */
    private function getIssuesFound(BusinessVerificationApplication $application): array
    {
        return $application->documents
            ->where('verification_status', self::STATUS_REJECTED)
            ->pluck('verification_notes')
            ->filter()
            ->toArray();
    }

    /**
     * Get recommendations for improvement
     */
    private function getRecommendations(BusinessVerificationApplication $application): array
    {
        $recommendations = [];
        $documents = $application->documents;
        
        if ($documents->where('verification_status', self::STATUS_REJECTED)->count() > 0) {
            $recommendations[] = 'Ensure all documents are clear and legible';
            $recommendations[] = 'Verify all information matches registration details';
        }
        
        if ($documents->count() < count($this->getRequiredDocuments($application->application_type))) {
            $recommendations[] = 'Upload all required documents';
        }
        
        return $recommendations;
    }

    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
