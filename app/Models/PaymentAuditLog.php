<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 📝 Payment Audit Log Model
 * 
 * Ghi lại tất cả các thay đổi và hoạt động liên quan đến financial transactions
 * Đảm bảo transparency và traceability cho payment system
 */
class PaymentAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'entity_type',
        'entity_id',
        'user_id',
        'admin_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
        'amount_impact',
        'currency',
        'description',
        'admin_notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'amount_impact' => 'decimal:2',
    ];

    /**
     * Relationship: User who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Admin who performed the action
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope: Filter by event type
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope: Filter by entity
     */
    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by admin
     */
    public function scopeByAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Financial impact logs
     */
    public function scopeWithFinancialImpact($query)
    {
        return $query->whereNotNull('amount_impact')
                    ->where('amount_impact', '!=', 0);
    }

    /**
     * Get event type display name
     */
    public function getEventTypeDisplayAttribute(): string
    {
        return match($this->event_type) {
            'payment_created' => 'Payment Created',
            'payment_completed' => 'Payment Completed',
            'payment_failed' => 'Payment Failed',
            'payment_refunded' => 'Payment Refunded',
            'payout_request_created' => 'Payout Request Created',
            'payout_approved' => 'Payout Approved',
            'payout_rejected' => 'Payout Rejected',
            'payout_completed' => 'Payout Completed',
            'commission_updated' => 'Commission Updated',
            'settings_changed' => 'Settings Changed',
            default => ucwords(str_replace('_', ' ', $this->event_type))
        };
    }

    /**
     * Get entity type display name
     */
    public function getEntityTypeDisplayAttribute(): string
    {
        return match($this->entity_type) {
            'centralized_payment' => 'Centralized Payment',
            'payout_request' => 'Payout Request',
            'commission_setting' => 'Commission Setting',
            'marketplace_order' => 'Marketplace Order',
            'payment_transaction' => 'Payment Transaction',
            default => ucwords(str_replace('_', ' ', $this->entity_type))
        };
    }

    /**
     * Get amount impact with proper formatting
     */
    public function getFormattedAmountImpactAttribute(): string
    {
        if ($this->amount_impact === null) {
            return 'N/A';
        }

        $prefix = $this->amount_impact >= 0 ? '+' : '';
        return $prefix . number_format($this->amount_impact, 0, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Get impact color based on amount
     */
    public function getImpactColorAttribute(): string
    {
        if ($this->amount_impact === null || $this->amount_impact == 0) {
            return 'secondary';
        }

        return $this->amount_impact > 0 ? 'success' : 'danger';
    }

    /**
     * Log payment event
     */
    public static function logPaymentEvent(
        string $eventType,
        string $entityType,
        int $entityId,
        array $data = []
    ): self {
        return static::create([
            'event_type' => $eventType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'admin_id' => $data['admin_id'] ?? (auth()->guard('admin')->check() ? auth()->guard('admin')->id() : null),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'amount_impact' => $data['amount_impact'] ?? null,
            'currency' => $data['currency'] ?? 'VND',
            'description' => $data['description'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);
    }

    /**
     * Get available event types
     */
    public static function getEventTypes(): array
    {
        return [
            'payment_created' => 'Payment Created',
            'payment_completed' => 'Payment Completed',
            'payment_failed' => 'Payment Failed',
            'payment_refunded' => 'Payment Refunded',
            'payout_request_created' => 'Payout Request Created',
            'payout_approved' => 'Payout Approved',
            'payout_rejected' => 'Payout Rejected',
            'payout_completed' => 'Payout Completed',
            'commission_updated' => 'Commission Updated',
            'settings_changed' => 'Settings Changed',
        ];
    }

    /**
     * Get available entity types
     */
    public static function getEntityTypes(): array
    {
        return [
            'centralized_payment' => 'Centralized Payment',
            'payout_request' => 'Payout Request',
            'commission_setting' => 'Commission Setting',
            'marketplace_order' => 'Marketplace Order',
            'payment_transaction' => 'Payment Transaction',
        ];
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'event_type' => 'required|string|max:100',
            'entity_type' => 'required|string|max:100',
            'entity_id' => 'required|integer|min:1',
            'user_id' => 'nullable|exists:users,id',
            'admin_id' => 'nullable|exists:users,id',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string',
            'old_values' => 'nullable|array',
            'new_values' => 'nullable|array',
            'metadata' => 'nullable|array',
            'amount_impact' => 'nullable|numeric',
            'currency' => 'nullable|string|size:3',
            'description' => 'nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ip_address)) {
                $model->ip_address = request()->ip();
            }
            if (empty($model->user_agent)) {
                $model->user_agent = request()->userAgent();
            }
            if (empty($model->currency)) {
                $model->currency = 'VND';
            }
        });
    }
}
