<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Business Verification Audit Trail Model
 *
 * Tracks all activities and changes in the business verification system
 * for compliance and security monitoring
 */
class BusinessVerificationAuditTrail extends Model
{
    use HasFactory;

    protected $table = 'business_verification_audit_trail';

    protected $fillable = [
        'application_id',
        'action_type',
        'performed_by',
        'action_description',
        'old_status',
        'new_status',
        'document_id',
        'related_model_type',
        'related_model_id',
        'metadata',
        'changes',
        'reason',
        'notes',
        'ip_address',
        'user_agent',
        'session_id',
        'request_id',
        'application_version',
        'environment',
        'is_automated',
        'automation_source',
        'is_sensitive',
        'requires_retention',
        'retention_until',
        'compliance_tags',
        'processing_time_ms',
        'batch_id',
        'sequence_number',
    ];

    protected $casts = [
        'metadata' => 'array',
        'changes' => 'array',
        'compliance_tags' => 'array',
        'is_automated' => 'boolean',
        'is_sensitive' => 'boolean',
        'requires_retention' => 'boolean',
        'retention_until' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Get the application that this audit trail belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(BusinessVerificationApplication::class, 'application_id');
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by risk level
     */
    public function scopeByRiskLevel($query, string $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('created_at', [$dateFrom, $dateTo]);
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        $actionNames = [
            'application_created' => 'Tạo đơn xác thực',
            'application_submitted' => 'Nộp đơn xác thực',
            'application_reviewed' => 'Xem xét đơn',
            'application_approved' => 'Phê duyệt đơn',
            'application_rejected' => 'Từ chối đơn',
            'document_uploaded' => 'Tải lên tài liệu',
            'document_verified' => 'Xác minh tài liệu',
            'document_rejected' => 'Từ chối tài liệu',
            'additional_info_requested' => 'Yêu cầu bổ sung thông tin',
            'reviewer_assigned' => 'Phân công người xem xét',
            'status_changed' => 'Thay đổi trạng thái',
            'notes_added' => 'Thêm ghi chú',
            'security_incident' => 'Sự cố bảo mật',
            'data_access' => 'Truy cập dữ liệu',
            'data_export' => 'Xuất dữ liệu',
            'bulk_action' => 'Thao tác hàng loạt',
        ];

        return $actionNames[$this->action] ?? $this->action;
    }

    /**
     * Get risk level badge class
     */
    public function getRiskLevelBadgeAttribute(): string
    {
        $badges = [
            'low' => 'bg-success',
            'medium' => 'bg-warning',
            'high' => 'bg-danger',
            'critical' => 'bg-dark',
        ];

        return $badges[$this->risk_level] ?? 'bg-secondary';
    }

    /**
     * Get formatted risk level
     */
    public function getFormattedRiskLevelAttribute(): string
    {
        $levels = [
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            'critical' => 'Nghiêm trọng',
        ];

        return $levels[$this->risk_level] ?? $this->risk_level;
    }
}
