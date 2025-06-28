<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * B2B Quote Model - Phase 3
 */
class B2BQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'product_id',
        'title',
        'description',
        'quantity',
        'specifications',
        'delivery_requirements',
        'budget_range',
        'deadline',
        'status',
        'priority',
        'quoted_amount',
        'final_amount',
        'seller_notes',
        'quoted_at',
        'responded_at',
    ];

    protected $casts = [
        'specifications' => 'array',
        'delivery_requirements' => 'array',
        'quoted_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'deadline' => 'date',
        'quoted_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
