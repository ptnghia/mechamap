<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    /**
     * Check if the subscription has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Get the plan details.
     */
    public function getPlanDetails(): array
    {
        $plans = [
            'basic' => [
                'name' => 'Basic',
                'price' => 4.99,
                'features' => [
                    'Ad-free browsing',
                    'Access to premium forums',
                    'Custom profile badge',
                ],
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => 9.99,
                'features' => [
                    'All Basic features',
                    'Unlimited private messages',
                    'Custom signature',
                    'Priority support',
                ],
            ],
            'pro' => [
                'name' => 'Professional',
                'price' => 19.99,
                'features' => [
                    'All Premium features',
                    'Business profile',
                    'Featured listings',
                    'Analytics dashboard',
                    'Dedicated account manager',
                ],
            ],
        ];

        return $plans[$this->plan_id] ?? [];
    }
}
