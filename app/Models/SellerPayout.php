<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SellerPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'payout_id',
        'seller_id',
        'total_amount',
        'currency',
        'earnings_count',
        'period_start',
        'period_end',
        'payout_method',
        'payout_details',
        'status',
        'failure_reason',
        'transaction_reference',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payout_details' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Boot method để tự động tạo payout_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_id)) {
                $payout->payout_id = static::generatePayoutId();
            }
        });
    }

    /**
     * Quan hệ với User (seller)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Quan hệ với SellerEarnings
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(SellerEarning::class, 'payout_id');
    }

    /**
     * Tạo payout ID duy nhất
     */
    public static function generatePayoutId(): string
    {
        do {
            $payoutId = 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('payout_id', $payoutId)->exists());

        return $payoutId;
    }

    /**
     * Scope: Payouts đang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Payouts đã hoàn thành
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Payouts của seller cụ thể
     */
    public function scopeBySeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Tạo payout từ available earnings
     */
    public static function createFromAvailableEarnings(int $sellerId, array $payoutDetails): self
    {
        $availableEarnings = SellerEarning::where('seller_id', $sellerId)
                                         ->available()
                                         ->get();

        if ($availableEarnings->isEmpty()) {
            throw new \Exception('Không có earnings nào để tạo payout');
        }

        $totalAmount = $availableEarnings->sum('net_amount');
        $earningsCount = $availableEarnings->count();

        $periodStart = $availableEarnings->min('created_at')->format('Y-m-d');
        $periodEnd = $availableEarnings->max('created_at')->format('Y-m-d');

        $payout = static::create([
            'seller_id' => $sellerId,
            'total_amount' => $totalAmount,
            'currency' => 'VND',
            'earnings_count' => $earningsCount,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'payout_method' => $payoutDetails['method'],
            'payout_details' => [
                'bank_name' => $payoutDetails['bank_name'] ?? null,
                'account_number' => $payoutDetails['account_number'] ?? null,
                'account_holder' => $payoutDetails['account_holder'] ?? null,
                'encrypted' => true, // Dữ liệu đã được mã hóa
            ],
            'status' => 'pending',
        ]);

        // Cập nhật earnings với payout_id
        $availableEarnings->each(function ($earning) use ($payout) {
            $earning->markAsPaid($payout->id);
        });

        return $payout;
    }

    /**
     * Đánh dấu payout đang processing
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
        ]);
    }

    /**
     * Đánh dấu payout đã hoàn thành
     */
    public function markAsCompleted(string $transactionReference = null): void
    {
        $this->update([
            'status' => 'completed',
            'transaction_reference' => $transactionReference,
            'completed_at' => now(),
        ]);
    }

    /**
     * Đánh dấu payout thất bại
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);

        // Cập nhật lại earnings về available
        $this->earnings()->update([
            'payout_status' => 'available',
            'payout_id' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Hủy payout
     */
    public function cancel(string $reason = null): void
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            throw new \Exception('Không thể hủy payout ở trạng thái ' . $this->status);
        }

        $this->update([
            'status' => 'cancelled',
            'failure_reason' => $reason ?? 'Cancelled by admin',
        ]);

        // Cập nhật lại earnings về available
        $this->earnings()->update([
            'payout_status' => 'available',
            'payout_id' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Format amount cho display
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy thông tin payout method display name
     */
    public function getPayoutMethodName(): string
    {
        return match($this->payout_method) {
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'stripe_transfer' => 'Stripe Transfer',
            'paypal' => 'PayPal',
            default => 'Unknown',
        };
    }

    /**
     * Lấy thông tin bank account (masked)
     */
    public function getMaskedBankAccount(): ?string
    {
        $details = $this->payout_details;
        if (!isset($details['account_number'])) {
            return null;
        }

        $accountNumber = $details['account_number'];
        $masked = '****' . substr($accountNumber, -4);
        $bankName = $details['bank_name'] ?? '';

        return $bankName . ' - ' . $masked;
    }

    /**
     * Lấy payout summary
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'payout_id' => $this->payout_id,
            'amount' => $this->getFormattedAmount(),
            'method' => $this->getPayoutMethodName(),
            'account' => $this->getMaskedBankAccount(),
            'status' => $this->status,
            'earnings_count' => $this->earnings_count,
            'period' => $this->period_start . ' đến ' . $this->period_end,
            'processed_at' => $this->processed_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'failure_reason' => $this->failure_reason,
        ];
    }

    /**
     * Kiểm tra payout có thể cancel không
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Tính toán payout statistics cho seller
     */
    public static function getSellerPayoutStats(int $sellerId): array
    {
        $payouts = static::where('seller_id', $sellerId)->get();

        $totalAmount = $payouts->sum('total_amount');
        $completedAmount = $payouts->where('status', 'completed')->sum('total_amount');
        $pendingAmount = $payouts->whereIn('status', ['pending', 'processing'])->sum('total_amount');

        return [
            'total_payouts' => $payouts->count(),
            'completed_payouts' => $payouts->where('status', 'completed')->count(),
            'pending_payouts' => $payouts->whereIn('status', ['pending', 'processing'])->count(),
            'total_amount' => (float) $totalAmount,
            'completed_amount' => (float) $completedAmount,
            'pending_amount' => (float) $pendingAmount,
            'formatted' => [
                'total_amount' => number_format($totalAmount, 0, ',', '.') . ' ₫',
                'completed_amount' => number_format($completedAmount, 0, ',', '.') . ' ₫',
                'pending_amount' => number_format($pendingAmount, 0, ',', '.') . ' ₫',
            ]
        ];
    }
}
