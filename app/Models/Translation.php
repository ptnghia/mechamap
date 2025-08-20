<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'content',
        'locale',
        'group_name',
        'namespace',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when translation is updated
        static::saved(function ($translation) {
            $translation->clearCache();
        });

        static::deleted(function ($translation) {
            $translation->clearCache();
        });
    }

    /**
     * Creator relationship
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater relationship
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * History relationship
     */
    public function history(): HasMany
    {
        return $this->hasMany(TranslationHistory::class);
    }

    /**
     * Scope: Active translations only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By locale
     */
    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope: By group
     */
    public function scopeForGroup($query, string $group)
    {
        return $query->where('group_name', $group);
    }

    /**
     * Get translation by key and locale
     */
    public static function getTranslation(string $key, string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        $cacheKey = "translation.{$locale}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $locale) {
            $translation = static::active()
                ->forLocale($locale)
                ->where('key', $key)
                ->first();

            return $translation?->content;
        });
    }

    /**
     * Get all translations for a group and locale
     */
    public static function getGroupTranslations(string $group, string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        $cacheKey = "translations.{$locale}.{$group}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $locale) {
            $translations = static::active()
                ->forLocale($locale)
                ->forGroup($group)
                ->pluck('content', 'key')
                ->toArray();

            return static::buildNestedArray($translations);
        });
    }

    /**
     * Build nested array from dot notation keys
     */
    private static function buildNestedArray(array $translations): array
    {
        $result = [];

        foreach ($translations as $key => $value) {
            $keys = explode('.', $key);
            $current = &$result;

            foreach ($keys as $k) {
                if (!isset($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }

            $current = $value;
        }

        return $result;
    }

    /**
     * Set translation value
     */
    public static function setTranslation(string $key, string $content, string $locale = null, array $options = []): self
    {
        $locale = $locale ?: app()->getLocale();

        // Parse group from key
        $keyParts = explode('.', $key);
        $group = $keyParts[0] ?? 'general';

        $translation = static::updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            array_merge([
                'content' => $content,
                'group_name' => $group,
                'is_active' => true,
                'updated_by' => auth()->id(),
            ], $options)
        );

        // Record history if content changed
        if ($translation->wasChanged('content')) {
            $translation->recordHistory($translation->getOriginal('content'), $content);
        }

        return $translation;
    }

    /**
     * Record translation history
     */
    public function recordHistory(?string $oldContent, string $newContent, string $reason = null): void
    {
        $this->history()->create([
            'old_content' => $oldContent,
            'new_content' => $newContent,
            'changed_by' => auth()->id(),
            'change_reason' => $reason,
        ]);
    }

    /**
     * Clear translation cache
     */
    public function clearCache(): void
    {
        $patterns = [
            "translation.{$this->locale}.{$this->key}",
            "translations.{$this->locale}.{$this->group_name}",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        // Clear all translation cache patterns for this locale
        $this->clearCacheByPattern("translation.{$this->locale}.*");
        $this->clearCacheByPattern("translations.{$this->locale}.*");
    }

    /**
     * Clear cache by pattern (fallback for non-Redis cache)
     */
    private function clearCacheByPattern(string $pattern): void
    {
        try {
            // Try Redis-specific pattern clearing if available
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (\Exception $e) {
            // Fallback: just clear specific known keys
            // This is less efficient but works with any cache driver
        }
    }

    /**
     * Import translations from array
     */
    public static function importTranslations(array $translations, string $locale, string $group = null): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($translations as $key => $content) {
            try {
                if ($group) {
                    $key = "{$group}.{$key}";
                }

                static::setTranslation($key, $content, $locale);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Key '{$key}': " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Export translations to array
     */
    public static function exportTranslations(string $locale, string $group = null): array
    {
        $query = static::active()->forLocale($locale);

        if ($group) {
            $query->forGroup($group);
        }

        return $query->pluck('content', 'key')->toArray();
    }

    /**
     * Get missing translations
     */
    public static function getMissingTranslations(string $fromLocale, string $toLocale): array
    {
        $fromKeys = static::forLocale($fromLocale)->pluck('key')->toArray();
        $toKeys = static::forLocale($toLocale)->pluck('key')->toArray();

        return array_diff($fromKeys, $toKeys);
    }

    /**
     * Sync translations from file system
     */
    public static function syncFromFiles(): array
    {
        $synced = 0;
        $errors = [];

        $langPath = resource_path('lang');
        $locales = ['vi', 'en']; // Add more locales as needed

        foreach ($locales as $locale) {
            $localePath = "{$langPath}/{$locale}";

            if (!is_dir($localePath)) {
                continue;
            }

            $files = glob("{$localePath}/*.php");

            foreach ($files as $file) {
                try {
                    $group = basename($file, '.php');
                    $translations = include $file;

                    if (is_array($translations)) {
                        $flatTranslations = static::flattenArray($translations, $group);
                        $result = static::importTranslations($flatTranslations, $locale);
                        $synced += $result['imported'];
                        $errors = array_merge($errors, $result['errors']);
                    }
                } catch (\Exception $e) {
                    $errors[] = "File '{$file}': " . $e->getMessage();
                }
            }
        }

        return [
            'synced' => $synced,
            'errors' => $errors,
        ];
    }

    /**
     * Flatten nested array with dot notation
     */
    private static function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, static::flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
