<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'order_item_id',
        'technical_product_id',
        'gross_amount',
        'platform_fee',
        'payment_fee',
        'tax_amount',
        'net_amount',
        'platform_fee_rate',
        'payment_fee_rate',
        'payout_status',
        'payout_id',
        'available_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'platform_fee_rate' => 'decimal:4',
        'payment_fee_rate' => 'decimal:4',
        'metadata' => 'array',
        'available_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Quan hệ với User (seller)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Quan hệ với OrderItem
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Quan hệ với TechnicalProduct
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(TechnicalProduct::class, 'technical_product_id');
    }

    /**
     * Quan hệ với SellerPayout
     */
    public function payout(): BelongsTo
    {
        return $this->belongsTo(SellerPayout::class, 'payout_id');
    }

    /**
     * Scope: Earnings có thể rút tiền
     */
    public function scopeAvailable($query)
    {
        return $query->where('payout_status', 'available')
                     ->where('available_at', '<=', now());
    }

    /**
     * Scope: Earnings đã được thanh toán
     */
    public function scopePaid($query)
    {
        return $query->where('payout_status', 'paid')
                     ->whereNotNull('paid_at');
    }

    /**
     * Scope: Earnings của seller cụ thể
     */
    public function scopeBySeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Scope: Earnings trong khoảng thời gian
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Kiểm tra earning có thể rút tiền không
     */
    public function canBeWithdrawn(): bool
    {
        return $this->payout_status === 'available' &&
               $this->available_at <= now() &&
               $this->payout_id === null;
    }

    /**
     * Đánh dấu earning available cho payout
     */
    public function markAsAvailable(): void
    {
        if ($this->payout_status === 'pending') {
            $this->update([
                'payout_status' => 'available',
                'available_at' => now(),
            ]);
        }
    }

    /**
     * Đánh dấu earning đã được thanh toán
     */
    public function markAsPaid(int $payoutId): void
    {
        $this->update([
            'payout_status' => 'paid',
            'payout_id' => $payoutId,
            'paid_at' => now(),
        ]);
    }

    /**
     * Tính toán fee breakdown
     */
    public function calculateFees(): array
    {
        $platformFee = $this->gross_amount * $this->platform_fee_rate;
        $paymentFee = $this->gross_amount * $this->payment_fee_rate;
        $netAmount = $this->gross_amount - $platformFee - $paymentFee - $this->tax_amount;

        return [
            'gross_amount' => (float) $this->gross_amount,
            'platform_fee' => round($platformFee, 2),
            'payment_fee' => round($paymentFee, 2),
            'tax_amount' => (float) $this->tax_amount,
            'net_amount' => round($netAmount, 2),
            'platform_fee_rate' => (float) $this->platform_fee_rate * 100, // Convert to percentage
            'payment_fee_rate' => (float) $this->payment_fee_rate * 100,
        ];
    }

    /**
     * Format amount cho display
     */
    public function getFormattedNetAmount(): string
    {
        return number_format($this->net_amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy thông tin earning summary
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'product_title' => $this->product->title ?? 'Unknown Product',
            'gross_amount' => $this->getFormattedNetAmount(),
            'net_amount' => $this->getFormattedNetAmount(),
            'status' => $this->payout_status,
            'available_at' => $this->available_at?->format('Y-m-d H:i:s'),
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'can_withdraw' => $this->canBeWithdrawn(),
        ];
    }

    /**
     * Tính tổng earnings của seller trong khoảng thời gian
     */
    public static function getSellerEarningsSummary(int $sellerId, $startDate = null, $endDate = null): array
    {
        $query = static::where('seller_id', $sellerId);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalEarnings = $query->sum('net_amount');
        $availableEarnings = $query->where('payout_status', 'available')->sum('net_amount');
        $paidEarnings = $query->where('payout_status', 'paid')->sum('net_amount');
        $pendingEarnings = $query->where('payout_status', 'pending')->sum('net_amount');

        return [
            'total_earnings' => (float) $totalEarnings,
            'available_earnings' => (float) $availableEarnings,
            'paid_earnings' => (float) $paidEarnings,
            'pending_earnings' => (float) $pendingEarnings,
            'formatted' => [
                'total_earnings' => number_format($totalEarnings, 0, ',', '.') . ' ₫',
                'available_earnings' => number_format($availableEarnings, 0, ',', '.') . ' ₫',
                'paid_earnings' => number_format($paidEarnings, 0, ',', '.') . ' ₫',
                'pending_earnings' => number_format($pendingEarnings, 0, ',', '.') . ' ₫',
            ]
        ];
    }
}
