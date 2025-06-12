<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'gateway_payment_method_id',
        'name',
        'last_four',
        'brand',
        'exp_month',
        'exp_year',
        'bank_name',
        'is_default',
        'is_verified',
        'is_active',
        'metadata',
        'verified_at',
        'last_used_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Payment methods đang active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Payment methods đã verify
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Default payment method
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Đặt làm payment method mặc định
     */
    public function makeDefault(): void
    {
        // Bỏ default của tất cả payment methods khác của user
        static::where('user_id', $this->user_id)
              ->where('id', '!=', $this->id)
              ->update(['is_default' => false]);

        // Đặt payment method này làm default
        $this->update(['is_default' => true]);
    }

    /**
     * Đánh dấu đã verify
     */
    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Cập nhật lần sử dụng cuối
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Kiểm tra payment method có hết hạn không
     */
    public function isExpired(): bool
    {
        if (!$this->exp_month || !$this->exp_year) {
            return false;
        }

        $expiryDate = now()->setYear($this->exp_year)->setMonth($this->exp_month)->endOfMonth();
        return now() > $expiryDate;
    }

    /**
     * Lấy display name cho payment method
     */
    public function getDisplayName(): string
    {
        switch ($this->type) {
            case 'stripe_card':
                return ($this->brand ?? 'Card') . ' **** ' . $this->last_four;
            case 'stripe_bank':
                return ($this->bank_name ?? 'Bank') . ' **** ' . $this->last_four;
            case 'vnpay':
                return 'VNPay - ' . $this->name;
            case 'bank_account':
                return ($this->bank_name ?? 'Bank') . ' - ' . $this->name;
            default:
                return $this->name;
        }
    }

    /**
     * Lấy icon cho payment method type
     */
    public function getIcon(): string
    {
        return match($this->type) {
            'stripe_card' => match(strtolower($this->brand ?? '')) {
                'visa' => 'fab fa-cc-visa',
                'mastercard' => 'fab fa-cc-mastercard',
                'amex' => 'fab fa-cc-amex',
                default => 'fas fa-credit-card',
            },
            'stripe_bank' => 'fas fa-university',
            'vnpay' => 'fas fa-mobile-alt',
            'bank_account' => 'fas fa-university',
            default => 'fas fa-payment',
        };
    }

    /**
     * Lấy status text
     */
    public function getStatusText(): string
    {
        if (!$this->is_active) {
            return 'Không hoạt động';
        }

        if ($this->isExpired()) {
            return 'Đã hết hạn';
        }

        if (!$this->is_verified) {
            return 'Chờ xác minh';
        }

        return 'Hoạt động';
    }

    /**
     * Lấy status color
     */
    public function getStatusColor(): string
    {
        if (!$this->is_active) {
            return 'red';
        }

        if ($this->isExpired()) {
            return 'orange';
        }

        if (!$this->is_verified) {
            return 'yellow';
        }

        return 'green';
    }

    /**
     * Vô hiệu hóa payment method
     */
    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'is_default' => false,
        ]);
    }

    /**
     * Lấy payment method summary
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->getDisplayName(),
            'icon' => $this->getIcon(),
            'is_default' => $this->is_default,
            'is_verified' => $this->is_verified,
            'is_expired' => $this->isExpired(),
            'status_text' => $this->getStatusText(),
            'status_color' => $this->getStatusColor(),
            'last_used_at' => $this->last_used_at?->format('Y-m-d H:i:s'),
            'verified_at' => $this->verified_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Thêm payment method mới cho user
     */
    public static function addForUser(int $userId, array $data): self
    {
        $isFirstMethod = !static::where('user_id', $userId)->exists();

        $paymentMethod = static::create([
            'user_id' => $userId,
            'type' => $data['type'],
            'gateway_payment_method_id' => $data['gateway_payment_method_id'] ?? null,
            'name' => $data['name'],
            'last_four' => $data['last_four'] ?? null,
            'brand' => $data['brand'] ?? null,
            'exp_month' => $data['exp_month'] ?? null,
            'exp_year' => $data['exp_year'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'is_default' => $isFirstMethod, // Payment method đầu tiên sẽ là default
            'is_verified' => $data['is_verified'] ?? false,
            'is_active' => true,
            'metadata' => $data['metadata'] ?? [],
        ]);

        return $paymentMethod;
    }
}
