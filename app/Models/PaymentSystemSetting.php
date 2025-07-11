<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ⚙️ Payment System Setting Model
 * 
 * Quản lý cấu hình hệ thống payment
 * Key-value store cho các settings linh hoạt
 */
class PaymentSystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'sort_order',
        'is_active',
        'is_system',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: User who last updated this setting
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by group
     */
    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope: System settings (cannot be deleted)
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope: User settings (can be modified)
     */
    public function scopeUserSettings($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Get typed value based on type
     */
    public function getTypedValueAttribute()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->value) ? (float) $this->value : 0,
            'integer' => is_numeric($this->value) ? (int) $this->value : 0,
            'json' => json_decode($this->value, true),
            'array' => json_decode($this->value, true) ?: [],
            default => $this->value
        };
    }

    /**
     * Set typed value based on type
     */
    public function setTypedValue($value): void
    {
        $this->value = match($this->type) {
            'boolean' => $value ? 'true' : 'false',
            'number', 'integer' => (string) $value,
            'json', 'array' => json_encode($value),
            default => (string) $value
        };
    }

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->where('is_active', true)->first();
        
        if (!$setting) {
            return $default;
        }

        return $setting->typed_value;
    }

    /**
     * Set setting value by key
     */
    public static function set(string $key, $value, array $options = []): self
    {
        $setting = static::firstOrNew(['key' => $key]);
        
        // Determine type if not specified
        if (!isset($options['type'])) {
            $options['type'] = match(true) {
                is_bool($value) => 'boolean',
                is_int($value) => 'integer',
                is_float($value) => 'number',
                is_array($value) => 'json',
                default => 'string'
            };
        }

        $setting->fill([
            'type' => $options['type'],
            'description' => $options['description'] ?? $setting->description,
            'group' => $options['group'] ?? $setting->group ?? 'general',
            'sort_order' => $options['sort_order'] ?? $setting->sort_order ?? 0,
            'is_active' => $options['is_active'] ?? true,
            'is_system' => $options['is_system'] ?? $setting->is_system ?? false,
            'updated_by' => auth()->id(),
        ]);

        $setting->setTypedValue($value);
        $setting->save();

        return $setting;
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup(string $group): array
    {
        return static::active()
                    ->inGroup($group)
                    ->orderBy('sort_order')
                    ->get()
                    ->pluck('typed_value', 'key')
                    ->toArray();
    }

    /**
     * Get commission rates
     */
    public static function getCommissionRates(): array
    {
        return static::get('default_commission_rates', [
            'manufacturer' => 5.0,
            'supplier' => 3.0,
            'brand' => 0.0,
            'verified_partner' => 2.0
        ]);
    }

    /**
     * Get admin bank accounts
     */
    public static function getAdminBankAccounts(): array
    {
        return [
            'stripe' => static::get('admin_bank_account_stripe', []),
            'sepay' => static::get('admin_bank_account_sepay', [])
        ];
    }

    /**
     * Get payout settings
     */
    public static function getPayoutSettings(): array
    {
        return [
            'minimum_amount' => static::get('minimum_payout_amount', 100000),
            'auto_enabled' => static::get('auto_payout_enabled', false),
            'processing_days' => static::get('payout_processing_days', 7),
        ];
    }

    /**
     * Get available setting groups
     */
    public static function getGroups(): array
    {
        return [
            'general' => 'General Settings',
            'payment_gateways' => 'Payment Gateways',
            'commission' => 'Commission Settings',
            'payout' => 'Payout Settings',
            'notifications' => 'Notification Settings',
            'security' => 'Security Settings',
        ];
    }

    /**
     * Get available setting types
     */
    public static function getTypes(): array
    {
        return [
            'string' => 'Text',
            'number' => 'Number',
            'integer' => 'Integer',
            'boolean' => 'Boolean',
            'json' => 'JSON Object',
            'array' => 'Array',
        ];
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'key' => 'required|string|max:255|unique:payment_system_settings,key',
            'value' => 'required',
            'type' => 'required|in:string,number,integer,boolean,json,array',
            'description' => 'nullable|string|max:1000',
            'group' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if ($model->is_system) {
                throw new \Exception('System settings cannot be deleted');
            }
        });
    }
}
