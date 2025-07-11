<?php

namespace App\Services;

use App\Models\User;
use App\Models\BusinessVerificationApplication;
use App\Models\BusinessVerificationDocument;
use App\Models\BusinessVerificationAuditTrail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Business Verification Service
 *
 * Core service for managing business verification applications,
 * document handling, and approval workflows
 */
class BusinessVerificationService
{
    protected DocumentSecurityService $documentSecurity;
    protected VerificationNotificationService $notificationService;
    protected VerificationAuditService $auditService;

    public function __construct(
        DocumentSecurityService $documentSecurity,
        VerificationNotificationService $notificationService,
        VerificationAuditService $auditService
    ) {
        $this->documentSecurity = $documentSecurity;
        $this->notificationService = $notificationService;
        $this->auditService = $auditService;
    }

    /**
     * Submit a new business verification application
     */
    public function submitApplication(User $user, array $data): BusinessVerificationApplication
    {
        DB::beginTransaction();

        try {
            // Validate user can submit application
            if ($this->hasActiveApplication($user)) {
                throw new \Exception('User already has an active verification application');
            }

            // Create application
            $application = BusinessVerificationApplication::create([
                'user_id' => $user->id,
                'application_type' => $data['application_type'],
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'],
                'tax_id' => $data['tax_id'],
                'registration_number' => $data['registration_number'] ?? null,
                'business_address' => $data['business_address'],
                'business_phone' => $data['business_phone'] ?? null,
                'business_email' => $data['business_email'] ?? null,
                'business_website' => $data['business_website'] ?? null,
                'business_description' => $data['business_description'] ?? null,
                'years_in_business' => $data['years_in_business'] ?? null,
                'employee_count' => $data['employee_count'] ?? null,
                'annual_revenue' => $data['annual_revenue'] ?? null,
                'business_categories' => $data['business_categories'] ?? [],
                'service_areas' => $data['service_areas'] ?? [],
                'communication_preferences' => $data['communication_preferences'] ?? [],
                'preferred_language' => $data['preferred_language'] ?? 'vi',
                'sms_notifications_enabled' => $data['sms_notifications_enabled'] ?? false,
                'email_notifications_enabled' => $data['email_notifications_enabled'] ?? true,
                'status' => BusinessVerificationApplication::STATUS_PENDING,
                'submitted_at' => now(),
                'priority_level' => $this->calculatePriority($data),
                'estimated_review_time' => $this->calculateEstimatedReviewTime($data),
            ]);

            // Log submission
            $this->auditService->logAction(
                $application,
                'application_submitted',
                $user,
                ['application_data' => $data]
            );

            // Send notifications
            $this->notificationService->sendApplicationSubmitted($application);
            $this->notificationService->notifyAdminsNewApplication($application);

            DB::commit();

            Log::info('Business verification application submitted', [
                'application_id' => $application->id,
                'user_id' => $user->id,
                'application_type' => $data['application_type'],
            ]);

            return $application;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit business verification application', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing application
     */
    public function updateApplication(BusinessVerificationApplication $application, array $data): bool
    {
        if (!$application->canBeEdited()) {
            throw new \Exception('Application cannot be edited in current status');
        }

        DB::beginTransaction();

        try {
            $oldData = $application->toArray();

            $application->update($data);
            $application->increment('revision_count');

            // Log update
            $this->auditService->logAction(
                $application,
                'application_updated',
                auth()->user(),
                [
                    'old_data' => $oldData,
                    'new_data' => $data,
                    'changes' => array_diff_assoc($data, $oldData)
                ]
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update business verification application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Upload a document for verification
     */
    public function uploadDocument(
        BusinessVerificationApplication $application,
        UploadedFile $file,
        string $type,
        array $metadata = []
    ): BusinessVerificationDocument {
        // Validate file
        $this->validateDocumentFile($file);

        DB::beginTransaction();

        try {
            // Store file securely
            $filePath = $this->documentSecurity->storeSecurely(
                $file,
                "verification/{$application->id}"
            );

            // Create document record
            $document = BusinessVerificationDocument::create([
                'application_id' => $application->id,
                'document_type' => $type,
                'document_name' => $metadata['document_name'] ?? $file->getClientOriginalName(),
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_extension' => $file->getClientOriginalExtension(),
                'document_description' => $metadata['description'] ?? null,
                'document_date' => $metadata['document_date'] ?? null,
                'expiry_date' => $metadata['expiry_date'] ?? null,
                'issuing_authority' => $metadata['issuing_authority'] ?? null,
                'document_number' => $metadata['document_number'] ?? null,
                'file_hash' => hash_file('sha256', $file->getPathname()),
                'verification_status' => BusinessVerificationDocument::STATUS_PENDING,
            ]);

            // Generate thumbnail for images
            if ($document->isImage()) {
                $thumbnailPath = $this->documentSecurity->createThumbnail($filePath);
                if ($thumbnailPath) {
                    $document->update([
                        'has_thumbnail' => true,
                        'thumbnail_path' => $thumbnailPath,
                    ]);
                }
            }

            // Log upload
            $this->auditService->logDocumentAction($document, 'document_uploaded', auth()->user());

            DB::commit();

            Log::info('Document uploaded for verification', [
                'application_id' => $application->id,
                'document_id' => $document->id,
                'document_type' => $type,
                'file_size' => $file->getSize(),
            ]);

            return $document;

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded file if database operation failed
            if (isset($filePath) && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            Log::error('Failed to upload verification document', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Assign reviewer to application
     */
    public function assignReviewer(BusinessVerificationApplication $application, User $reviewer): bool
    {
        if (!$application->canBeReviewed()) {
            throw new \Exception('Application cannot be reviewed in current status');
        }

        DB::beginTransaction();

        try {
            $application->update([
                'reviewed_by' => $reviewer->id,
                'status' => BusinessVerificationApplication::STATUS_UNDER_REVIEW,
                'reviewed_at' => now(),
            ]);

            // Log assignment
            $this->auditService->logAction(
                $application,
                'reviewer_assigned',
                auth()->user(),
                ['reviewer_id' => $reviewer->id, 'reviewer_name' => $reviewer->name]
            );

            // Notify reviewer
            $this->notificationService->notifyReviewerAssigned($application, $reviewer);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign reviewer', [
                'application_id' => $application->id,
                'reviewer_id' => $reviewer->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Approve application
     */
    public function approveApplication(
        BusinessVerificationApplication $application,
        User $admin,
        ?string $notes = null
    ): bool {
        if (!$application->canBeApproved()) {
            throw new \Exception('Application cannot be approved in current status');
        }

        DB::beginTransaction();

        try {
            // Update application status
            $application->update([
                'status' => BusinessVerificationApplication::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'approval_notes' => $notes,
            ]);

            // Upgrade user role
            $this->upgradeUserRole($application);

            // Log approval
            $this->auditService->logAction(
                $application,
                'application_approved',
                $admin,
                ['approval_notes' => $notes]
            );

            // Send notifications
            $this->notificationService->sendApplicationApproved($application);

            DB::commit();

            Log::info('Business verification application approved', [
                'application_id' => $application->id,
                'user_id' => $application->user_id,
                'approved_by' => $admin->id,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reject application
     */
    public function rejectApplication(
        BusinessVerificationApplication $application,
        User $admin,
        string $reason
    ): bool {
        if (!$application->canBeRejected()) {
            throw new \Exception('Application cannot be rejected in current status');
        }

        DB::beginTransaction();

        try {
            $application->update([
                'status' => BusinessVerificationApplication::STATUS_REJECTED,
                'rejected_by' => $admin->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Log rejection
            $this->auditService->logAction(
                $application,
                'application_rejected',
                $admin,
                ['rejection_reason' => $reason]
            );

            // Send notifications
            $this->notificationService->sendApplicationRejected($application);

            DB::commit();

            Log::info('Business verification application rejected', [
                'application_id' => $application->id,
                'user_id' => $application->user_id,
                'rejected_by' => $admin->id,
                'reason' => $reason,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Request additional information
     */
    public function requestAdditionalInfo(
        BusinessVerificationApplication $application,
        User $admin,
        string $info
    ): bool {
        DB::beginTransaction();

        try {
            $application->update([
                'status' => BusinessVerificationApplication::STATUS_REQUIRES_ADDITIONAL_INFO,
                'additional_info_requested' => $info,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            // Log request
            $this->auditService->logAction(
                $application,
                'additional_info_requested',
                $admin,
                ['info_requested' => $info]
            );

            // Send notification
            $this->notificationService->sendAdditionalInfoRequested($application);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to request additional info', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get verification statistics
     */
    public function getVerificationStats(): array
    {
        return [
            'total_applications' => BusinessVerificationApplication::count(),
            'pending' => BusinessVerificationApplication::pending()->count(),
            'under_review' => BusinessVerificationApplication::underReview()->count(),
            'approved' => BusinessVerificationApplication::approved()->count(),
            'rejected' => BusinessVerificationApplication::rejected()->count(),
            'requires_info' => BusinessVerificationApplication::requiresInfo()->count(),
            'overdue' => BusinessVerificationApplication::overdue()->count(),
            'expedited' => BusinessVerificationApplication::expedited()->count(),
            'avg_review_time' => $this->getAverageReviewTime(),
            'approval_rate' => $this->getApprovalRate(),
        ];
    }

    /**
     * Get applications by status
     */
    public function getApplicationsByStatus(string $status): Collection
    {
        return BusinessVerificationApplication::where('status', $status)
            ->with(['user', 'documents', 'reviewer'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get reviewer workload
     */
    public function getReviewerWorkload(User $reviewer): array
    {
        $applications = BusinessVerificationApplication::where('reviewed_by', $reviewer->id)
            ->whereIn('status', [
                BusinessVerificationApplication::STATUS_UNDER_REVIEW,
                BusinessVerificationApplication::STATUS_REQUIRES_ADDITIONAL_INFO
            ])
            ->get();

        return [
            'total_assigned' => $applications->count(),
            'under_review' => $applications->where('status', BusinessVerificationApplication::STATUS_UNDER_REVIEW)->count(),
            'requires_info' => $applications->where('status', BusinessVerificationApplication::STATUS_REQUIRES_ADDITIONAL_INFO)->count(),
            'overdue' => $applications->filter->is_overdue->count(),
            'avg_processing_time' => $applications->avg('days_in_review'),
        ];
    }

    /**
     * Helper methods
     */
    protected function hasActiveApplication(User $user): bool
    {
        return BusinessVerificationApplication::where('user_id', $user->id)
            ->whereNotIn('status', [
                BusinessVerificationApplication::STATUS_APPROVED,
                BusinessVerificationApplication::STATUS_REJECTED
            ])
            ->exists();
    }

    protected function calculatePriority(array $data): string
    {
        $score = 0;

        // Business size indicators
        if (isset($data['annual_revenue']) && $data['annual_revenue'] > 1000000) $score += 2;
        if (isset($data['employee_count']) && $data['employee_count'] > 50) $score += 2;
        if (isset($data['years_in_business']) && $data['years_in_business'] > 10) $score += 1;

        // Application completeness
        if (!empty($data['business_website'])) $score += 1;
        if (!empty($data['business_description'])) $score += 1;

        return match (true) {
            $score >= 5 => BusinessVerificationApplication::PRIORITY_HIGH,
            $score >= 3 => BusinessVerificationApplication::PRIORITY_MEDIUM,
            default => BusinessVerificationApplication::PRIORITY_LOW,
        };
    }

    protected function calculateEstimatedReviewTime(array $data): int
    {
        $baseTime = 72; // 3 days

        // Adjust based on application type
        $adjustments = [
            'verified_partner' => 24, // +1 day
            'manufacturer' => 12,     // +0.5 day
            'supplier' => 6,          // +0.25 day
            'brand' => 0,             // No adjustment
        ];

        return $baseTime + ($adjustments[$data['application_type']] ?? 0);
    }

    protected function upgradeUserRole(BusinessVerificationApplication $application): void
    {
        $user = $application->user;
        $targetRole = $application->application_type === 'verified_partner'
            ? 'verified_partner'
            : $application->application_type;

        $user->update(['role' => $targetRole]);

        // Log role upgrade
        $this->auditService->logAction(
            $application,
            'role_upgraded',
            $user,
            [
                'old_role' => $user->getOriginal('role'),
                'new_role' => $targetRole,
            ]
        );
    }

    protected function validateDocumentFile(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), BusinessVerificationDocument::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Invalid file type. Allowed types: PDF, Images, Word documents');
        }

        if ($file->getSize() > BusinessVerificationDocument::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum allowed size of 10MB');
        }

        // Additional security checks
        if (!$this->documentSecurity->validateFileType($file)) {
            throw new \Exception('File failed security validation');
        }
    }

    protected function getAverageReviewTime(): float
    {
        return BusinessVerificationApplication::whereNotNull('approved_at')
            ->orWhereNotNull('rejected_at')
            ->get()
            ->avg('days_in_review') ?? 0;
    }

    protected function getApprovalRate(): float
    {
        $total = BusinessVerificationApplication::whereIn('status', [
            BusinessVerificationApplication::STATUS_APPROVED,
            BusinessVerificationApplication::STATUS_REJECTED
        ])->count();

        if ($total === 0) return 0;

        $approved = BusinessVerificationApplication::approved()->count();

        return round(($approved / $total) * 100, 2);
    }
}
