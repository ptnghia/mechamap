<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereValue($value)
 * @mixin \Eloquent
 */
class SeoSetting extends Model
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
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return SeoSetting
     */
    public static function setValue(string $key, $value, string $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

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
        return self::where('group', $group)
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }
}
