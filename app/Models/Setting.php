<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Try to get from cache first
        $cacheKey = 'setting_' . $key;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // If not in cache, get from database
        $setting = self::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;

        // Store in cache for future use
        Cache::put($cacheKey, $value, now()->addDay());

        return $value;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return Setting
     */
    public static function set(string $key, $value, string $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        // Update cache
        $cacheKey = 'setting_' . $key;
        Cache::put($cacheKey, $value, now()->addDay());

        return $setting;
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return array
     */
    public static function getGroup(string $group)
    {
        // Try to get from cache first
        $cacheKey = 'setting_group_' . $group;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // If not in cache, get from database
        $settings = self::where('group', $group)
            ->get()
            ->pluck('value', 'key')
            ->toArray();

        // Store in cache for future use
        Cache::put($cacheKey, $settings, now()->addDay());

        return $settings;
    }

    /**
     * Clear settings cache
     *
     * @return void
     */
    public static function clearCache()
    {
        $keys = self::all()->pluck('key')->toArray();
        $groups = self::distinct('group')->pluck('group')->toArray();

        // Clear individual settings
        foreach ($keys as $key) {
            Cache::forget('setting_' . $key);
        }

        // Clear group settings
        foreach ($groups as $group) {
            Cache::forget('setting_group_' . $group);
        }
    }
}
