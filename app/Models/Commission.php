<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Commission Model - Phase 3
 */
class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'order_id',
        'transaction_type',
        'gross_amount',
        'commission_rate',
        'commission_amount',
        'seller_earnings',
        'currency',
        'status',
        'period',
        'metadata',
        'calculated_at',
        'paid_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'seller_earnings' => 'decimal:2',
        'metadata' => 'array',
        'calculated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
