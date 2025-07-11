<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Business Verification Application Model
 * 
 * Manages business verification applications for role upgrades
 * from basic business roles to verified_partner status
 */
class BusinessVerificationApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'application_type',
        'status',
        'business_name',
        'business_type',
        'tax_id',
        'registration_number',
        'business_address',
        'business_phone',
        'business_email',
        'business_website',
        'business_description',
        'years_in_business',
        'employee_count',
        'annual_revenue',
        'business_categories',
        'service_areas',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'reviewed_by',
        'approved_by',
        'rejected_by',
        'approval_notes',
        'rejection_reason',
        'additional_info_requested',
        'verification_score',
        'priority_level',
        'estimated_review_time',
        'is_expedited',
        'application_fee',
        'payment_status',
        'communication_preferences',
        'preferred_language',
        'sms_notifications_enabled',
        'email_notifications_enabled',
        'internal_notes',
        'reviewer_checklist',
        'deadline_at',
        'revision_count',
    ];

    protected $casts = [
        'business_categories' => 'array',
        'service_areas' => 'array',
        'communication_preferences' => 'array',
        'reviewer_checklist' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'deadline_at' => 'datetime',
        'annual_revenue' => 'decimal:2',
        'application_fee' => 'decimal:2',
        'sms_notifications_enabled' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'is_expedited' => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REQUIRES_ADDITIONAL_INFO = 'requires_additional_info';

    // Application type constants
    const TYPE_MANUFACTURER = 'manufacturer';
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_BRAND = 'brand';
    const TYPE_VERIFIED_PARTNER = 'verified_partner';

    // Priority level constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BusinessVerificationDocument::class, 'application_id');
    }

    public function auditTrail(): HasMany
    {
        return $this->hasMany(BusinessVerificationAuditTrail::class, 'application_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', self::STATUS_UNDER_REVIEW);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeRequiresInfo($query)
    {
        return $query->where('status', self::STATUS_REQUIRES_ADDITIONAL_INFO);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('application_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority_level', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline_at', '<', now())
                    ->whereNotIn('status', [self::STATUS_APPROVED, self::STATUS_REJECTED]);
    }

    public function scopeExpedited($query)
    {
        return $query->where('is_expedited', true);
    }

    /**
     * Accessors & Mutators
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Chờ xử lý</span>',
            self::STATUS_UNDER_REVIEW => '<span class="badge bg-info">Đang xem xét</span>',
            self::STATUS_APPROVED => '<span class="badge bg-success">Đã duyệt</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">Từ chối</span>',
            self::STATUS_REQUIRES_ADDITIONAL_INFO => '<span class="badge bg-secondary">Cần bổ sung</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Không xác định</span>';
    }

    public function getPriorityBadgeAttribute(): string
    {
        $badges = [
            self::PRIORITY_LOW => '<span class="badge bg-light text-dark">Thấp</span>',
            self::PRIORITY_MEDIUM => '<span class="badge bg-primary">Trung bình</span>',
            self::PRIORITY_HIGH => '<span class="badge bg-warning">Cao</span>',
            self::PRIORITY_URGENT => '<span class="badge bg-danger">Khẩn cấp</span>',
        ];

        return $badges[$this->priority_level] ?? '<span class="badge bg-secondary">Không xác định</span>';
    }

    public function getApplicationTypeDisplayAttribute(): string
    {
        $types = [
            self::TYPE_MANUFACTURER => 'Nhà sản xuất',
            self::TYPE_SUPPLIER => 'Nhà cung cấp',
            self::TYPE_BRAND => 'Nhãn hàng',
            self::TYPE_VERIFIED_PARTNER => 'Đối tác xác thực',
        ];

        return $types[$this->application_type] ?? 'Không xác định';
    }

    public function getDaysInReviewAttribute(): int
    {
        if (!$this->submitted_at) {
            return 0;
        }

        $endDate = $this->approved_at ?? $this->rejected_at ?? now();
        return $this->submitted_at->diffInDays($endDate);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline_at && 
               $this->deadline_at->isPast() && 
               !in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED]);
    }

    public function getCompletionPercentageAttribute(): int
    {
        $totalSteps = 5; // Basic info, business info, documents, review, approval
        $completedSteps = 0;

        // Basic info (always completed if application exists)
        $completedSteps++;

        // Business info
        if ($this->business_name && $this->tax_id && $this->business_address) {
            $completedSteps++;
        }

        // Documents
        if ($this->documents()->where('verification_status', 'verified')->count() > 0) {
            $completedSteps++;
        }

        // Review
        if ($this->reviewed_at) {
            $completedSteps++;
        }

        // Approval
        if (in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED])) {
            $completedSteps++;
        }

        return round(($completedSteps / $totalSteps) * 100);
    }

    /**
     * Business Logic Methods
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_REQUIRES_ADDITIONAL_INFO
        ]);
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_REQUIRES_ADDITIONAL_INFO
        ]);
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_UNDER_REVIEW &&
               $this->documents()->where('verification_status', 'verified')->count() > 0;
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_REQUIRES_ADDITIONAL_INFO
        ]);
    }

    public function hasRequiredDocuments(): bool
    {
        $requiredTypes = ['business_license', 'tax_certificate', 'identity_document'];
        $uploadedTypes = $this->documents()->pluck('document_type')->toArray();
        
        return count(array_intersect($requiredTypes, $uploadedTypes)) === count($requiredTypes);
    }

    public function calculateVerificationScore(): int
    {
        $score = 0;

        // Business information completeness (30 points)
        if ($this->business_name) $score += 5;
        if ($this->tax_id) $score += 10;
        if ($this->business_address) $score += 5;
        if ($this->business_phone) $score += 3;
        if ($this->business_email) $score += 3;
        if ($this->business_website) $score += 2;
        if ($this->business_description) $score += 2;

        // Document verification (50 points)
        $verifiedDocs = $this->documents()->where('verification_status', 'verified')->count();
        $totalDocs = $this->documents()->count();
        if ($totalDocs > 0) {
            $score += round(($verifiedDocs / $totalDocs) * 50);
        }

        // Business maturity (20 points)
        if ($this->years_in_business) {
            $score += min($this->years_in_business * 2, 20);
        }

        return min($score, 100);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (!$application->submitted_at) {
                $application->submitted_at = now();
            }
        });

        static::updating(function ($application) {
            // Auto-calculate verification score
            $application->verification_score = $application->calculateVerificationScore();
        });
    }
}
