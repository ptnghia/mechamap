<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * 🚨 Payment Dispute Model
 * 
 * Quản lý disputes, chargebacks và customer complaints
 * Tích hợp với payment gateways và resolution workflow
 */
class PaymentDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'dispute_reference',
        'centralized_payment_id',
        'order_id',
        'customer_id',
        'customer_email',
        'dispute_type',
        'status',
        'priority',
        'disputed_amount',
        'refund_amount',
        'currency',
        'gateway_dispute_id',
        'gateway_reason_code',
        'gateway_response',
        'customer_reason',
        'customer_description',
        'customer_evidence',
        'merchant_response',
        'merchant_evidence',
        'merchant_response_deadline',
        'assigned_to',
        'admin_notes',
        'internal_notes',
        'resolution_summary',
        'resolution_type',
        'dispute_date',
        'gateway_deadline',
        'resolved_at',
        'closed_at',
        'metadata',
    ];

    protected $casts = [
        'disputed_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'customer_evidence' => 'array',
        'merchant_evidence' => 'array',
        'internal_notes' => 'array',
        'metadata' => 'array',
        'dispute_date' => 'datetime',
        'merchant_response_deadline' => 'datetime',
        'gateway_deadline' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dispute) {
            if (empty($dispute->uuid)) {
                $dispute->uuid = Str::uuid();
            }
            if (empty($dispute->dispute_reference)) {
                $dispute->dispute_reference = self::generateDisputeReference();
            }
            if (empty($dispute->dispute_date)) {
                $dispute->dispute_date = now();
            }
        });
    }

    /**
     * Generate unique dispute reference
     */
    public static function generateDisputeReference(): string
    {
        do {
            $reference = 'DISP-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (self::where('dispute_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Relationships
     */
    public function centralizedPayment(): BelongsTo
    {
        return $this->belongsTo(CentralizedPayment::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class, 'dispute_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInvestigating($query)
    {
        return $query->where('status', 'investigating');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeNearDeadline($query)
    {
        return $query->where('gateway_deadline', '<=', now()->addDays(2))
                    ->whereNotIn('status', ['resolved', 'lost', 'withdrawn', 'expired']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Accessors & Mutators
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'investigating' => 'info',
            'evidence_required' => 'primary',
            'escalated' => 'danger',
            'resolved' => 'success',
            'lost' => 'danger',
            'withdrawn' => 'secondary',
            'expired' => 'dark',
            default => 'secondary'
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'low' => 'secondary',
            'medium' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }

    public function getDisputeTypeDisplayAttribute(): string
    {
        return match($this->dispute_type) {
            'chargeback' => 'Chargeback',
            'payment_not_received' => 'Payment Not Received',
            'unauthorized' => 'Unauthorized Transaction',
            'duplicate' => 'Duplicate Charge',
            'product_not_received' => 'Product Not Received',
            'product_defective' => 'Product Defective',
            'service_issue' => 'Service Issue',
            'billing_error' => 'Billing Error',
            'other' => 'Other',
            default => ucfirst($this->dispute_type)
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->gateway_deadline && 
               $this->gateway_deadline->isPast() && 
               !in_array($this->status, ['resolved', 'lost', 'withdrawn', 'expired']);
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->gateway_deadline) {
            return null;
        }

        return now()->diffInDays($this->gateway_deadline, false);
    }

    /**
     * Business Logic Methods
     */
    public function canBeAssigned(): bool
    {
        return in_array($this->status, ['pending', 'investigating', 'evidence_required']);
    }

    public function canBeResolved(): bool
    {
        return in_array($this->status, ['investigating', 'evidence_required', 'escalated']);
    }

    public function canAddEvidence(): bool
    {
        return in_array($this->status, ['pending', 'investigating', 'evidence_required']);
    }

    public function markAsInvestigating(User $admin): bool
    {
        if (!$this->canBeAssigned()) {
            return false;
        }

        $this->update([
            'status' => 'investigating',
            'assigned_to' => $admin->id,
        ]);

        $this->addInternalNote("Dispute assigned to {$admin->name} for investigation", $admin);
        return true;
    }

    public function markAsResolved(string $resolutionType, string $summary, User $admin): bool
    {
        if (!$this->canBeResolved()) {
            return false;
        }

        $this->update([
            'status' => 'resolved',
            'resolution_type' => $resolutionType,
            'resolution_summary' => $summary,
            'resolved_at' => now(),
            'closed_at' => now(),
        ]);

        $this->addInternalNote("Dispute resolved: {$summary}", $admin);
        return true;
    }

    public function addInternalNote(string $note, User $admin): void
    {
        $notes = $this->internal_notes ?? [];
        $notes[] = [
            'note' => $note,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'timestamp' => now()->toISOString(),
        ];

        $this->update(['internal_notes' => $notes]);
    }

    /**
     * Validation Rules
     */
    public static function validationRules(): array
    {
        return [
            'centralized_payment_id' => 'required|exists:centralized_payments,id',
            'order_id' => 'required|exists:marketplace_orders,id',
            'customer_id' => 'required|exists:users,id',
            'customer_email' => 'required|email',
            'dispute_type' => 'required|in:chargeback,payment_not_received,unauthorized,duplicate,product_not_received,product_defective,service_issue,billing_error,other',
            'disputed_amount' => 'required|numeric|min:0',
            'customer_reason' => 'required|string|max:1000',
            'priority' => 'in:low,medium,high,urgent',
        ];
    }

    /**
     * Get dispute statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'pending' => self::where('status', 'pending')->count(),
            'investigating' => self::where('status', 'investigating')->count(),
            'resolved' => self::where('status', 'resolved')->count(),
            'lost' => self::where('status', 'lost')->count(),
            'urgent' => self::where('priority', 'urgent')->count(),
            'overdue' => self::whereNotNull('gateway_deadline')
                            ->where('gateway_deadline', '<', now())
                            ->whereNotIn('status', ['resolved', 'lost', 'withdrawn', 'expired'])
                            ->count(),
        ];
    }

    /**
     * Get dispute types for dropdown
     */
    public static function getDisputeTypes(): array
    {
        return [
            'chargeback' => 'Chargeback',
            'payment_not_received' => 'Payment Not Received',
            'unauthorized' => 'Unauthorized Transaction',
            'duplicate' => 'Duplicate Charge',
            'product_not_received' => 'Product Not Received',
            'product_defective' => 'Product Defective',
            'service_issue' => 'Service Issue',
            'billing_error' => 'Billing Error',
            'other' => 'Other',
        ];
    }

    /**
     * Get resolution types for dropdown
     */
    public static function getResolutionTypes(): array
    {
        return [
            'full_refund' => 'Full Refund',
            'partial_refund' => 'Partial Refund',
            'no_refund' => 'No Refund',
            'replacement' => 'Replacement',
            'store_credit' => 'Store Credit',
            'other' => 'Other',
        ];
    }
}
