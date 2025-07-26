<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BusinessCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_vi',
        'name_en',
        'description_vi',
        'description_en',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get localized name based on current locale
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'vi' ? $this->name_vi : $this->name_en;
    }

    /**
     * Get localized description based on current locale
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'vi' ? $this->description_vi : $this->description_en;
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name_vi');
    }

    /**
     * Get categories for dropdown/selection
     */
    public static function getForSelection(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->pluck('name', 'key')
            ->toArray();
    }
}
