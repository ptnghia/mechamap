<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ShowcaseSetting extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'options',
        'default_value',
        'input_type',
        'is_multiple',
        'is_required',
        'is_searchable',
        'is_active',
        'sort_order',
        'group',
        'icon',
    ];

    protected $casts = [
        'options' => 'array',
        'default_value' => 'array',
        'is_multiple' => 'boolean',
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are modified
        static::saved(function () {
            Cache::forget('showcase_settings');
            Cache::forget('showcase_search_filters');
        });

        static::deleted(function () {
            Cache::forget('showcase_settings');
            Cache::forget('showcase_search_filters');
        });
    }

    /**
     * Scope to get only active settings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only searchable settings.
     */
    public function scopeSearchable(Builder $query): Builder
    {
        return $query->where('is_searchable', true)->where('is_active', true);
    }

    /**
     * Scope to get settings by group.
     */
    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get all active settings cached.
     */
    public static function getAllCached(): array
    {
        return Cache::remember('showcase_settings', 3600, function () {
            return static::active()->ordered()->get()->keyBy('key')->toArray();
        });
    }

    /**
     * Get searchable settings for filters.
     */
    public static function getSearchFilters(): array
    {
        return Cache::remember('showcase_search_filters', 3600, function () {
            return static::searchable()->ordered()->get()->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'name' => $setting->name,
                    'options' => $setting->getTranslatedOptions(),
                    'input_type' => $setting->input_type,
                    'is_multiple' => $setting->is_multiple,
                    'group' => $setting->group,
                    'icon' => $setting->icon,
                ];
            })->toArray();
        });
    }

    /**
     * Get options with current locale translations.
     */
    public function getTranslatedOptions(): array
    {
        $locale = app()->getLocale();
        $options = $this->options ?? [];
        
        return collect($options)->map(function ($option) use ($locale) {
            if (is_array($option) && isset($option['translations'])) {
                return [
                    'value' => $option['value'],
                    'label' => $option['translations'][$locale] ?? $option['translations']['en'] ?? $option['value'],
                    'description' => $option['description'] ?? null,
                    'icon' => $option['icon'] ?? null,
                ];
            }
            
            // Fallback for simple string options
            return [
                'value' => $option,
                'label' => __("showcase.{$this->key}_{$option}", [], $locale) ?: $option,
            ];
        })->toArray();
    }

    /**
     * Get setting by key with caching.
     */
    public static function getByKey(string $key): ?self
    {
        $settings = static::getAllCached();
        
        if (isset($settings[$key])) {
            $setting = new static();
            $setting->fill($settings[$key]);
            $setting->exists = true;
            return $setting;
        }
        
        return null;
    }

    /**
     * Get options for a specific setting key.
     */
    public static function getOptionsForKey(string $key): array
    {
        $setting = static::getByKey($key);
        return $setting ? $setting->getTranslatedOptions() : [];
    }

    /**
     * Validate value against setting options.
     */
    public function validateValue($value): bool
    {
        if (empty($value) && !$this->is_required) {
            return true;
        }

        $validValues = collect($this->options)->pluck('value')->toArray();
        
        if ($this->is_multiple) {
            $values = is_array($value) ? $value : [$value];
            return collect($values)->every(fn($v) => in_array($v, $validValues));
        }
        
        return in_array($value, $validValues);
    }

    /**
     * Get default value for this setting.
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Check if setting allows multiple values.
     */
    public function allowsMultiple(): bool
    {
        return $this->is_multiple;
    }

    /**
     * Get setting groups.
     */
    public static function getGroups(): array
    {
        return static::active()
            ->whereNotNull('group')
            ->distinct('group')
            ->pluck('group')
            ->sort()
            ->values()
            ->toArray();
    }
}
