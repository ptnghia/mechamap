<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Region;
use App\Models\Forum;
use App\Models\User;

/**
 * Country Model
 *
 * Quản lý thông tin quốc gia cho hệ thống forum đa quốc gia
 * Hỗ trợ cấu hình mechanical engineering theo từng quốc gia
 */
class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_local',
        'code',
        'code_alpha3',
        'phone_code',
        'currency_code',
        'currency_symbol',
        'continent',
        'timezone',
        'timezones',
        'language_code',
        'languages',
        'measurement_system',
        'standard_organizations',
        'common_cad_software',
        'flag_emoji',
        'flag_icon',
        'sort_order',
        'is_active',
        'allow_user_registration',
        'mechanical_specialties',
        'industrial_sectors',
    ];

    protected $casts = [
        'timezones' => 'array',
        'languages' => 'array',
        'standard_organizations' => 'array',
        'common_cad_software' => 'array',
        'mechanical_specialties' => 'array',
        'industrial_sectors' => 'array',
        'is_active' => 'boolean',
        'allow_user_registration' => 'boolean',
    ];

    // ===================================================================
    // RELATIONSHIPS
    // ===================================================================

    /**
     * Các khu vực thuộc quốc gia này
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Các khu vực đang hoạt động
     */
    public function activeRegions(): HasMany
    {
        return $this->regions()->where('is_active', true);
    }

    /**
     * Các forum trong quốc gia này
     */
    public function forums(): HasMany
    {
        return $this->hasManyThrough(Forum::class, Region::class);
    }

    /**
     * Users thuộc quốc gia này
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Active users trong quốc gia
     */
    public function activeUsers(): HasMany
    {
        return $this->users()->where('is_active', true);
    }

    // ===================================================================
    // SCOPES
    // ===================================================================

    /**
     * Scope cho quốc gia đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope cho quốc gia cho phép đăng ký
     */
    public function scopeAllowRegistration($query)
    {
        return $query->where('allow_user_registration', true);
    }

    /**
     * Scope theo châu lục
     */
    public function scopeByContinent($query, $continent)
    {
        return $query->where('continent', $continent);
    }

    /**
     * Scope theo hệ đo lường
     */
    public function scopeByMeasurementSystem($query, $system)
    {
        return $query->where('measurement_system', $system);
    }

    // ===================================================================
    // MECHANICAL ENGINEERING METHODS
    // ===================================================================

    /**
     * Lấy danh sách phần mềm CAD phổ biến
     */
    public function getPopularCadSoftware(): array
    {
        return $this->common_cad_software ?? [
            'AutoCAD', 'SolidWorks', 'Fusion 360', 'Inventor'
        ];
    }

    /**
     * Lấy các tổ chức tiêu chuẩn
     */
    public function getStandardOrganizations(): array
    {
        return $this->standard_organizations ?? [];
    }

    /**
     * Lấy chuyên ngành cơ khí phổ biến
     */
    public function getMechanicalSpecialties(): array
    {
        return $this->mechanical_specialties ?? [
            'Design Engineering',
            'Manufacturing',
            'Automation',
            'Materials Engineering'
        ];
    }

    /**
     * Kiểm tra có hỗ trợ một phần mềm CAD không
     */
    public function supportsCadSoftware(string $software): bool
    {
        $supported = $this->getPopularCadSoftware();
        return in_array($software, $supported, true);
    }

    /**
     * Lấy multiple timezones cho quốc gia
     */
    public function getTimezones(): array
    {
        if ($this->timezones) {
            return $this->timezones;
        }

        return $this->timezone ? [$this->timezone] : [];
    }

    /**
     * Lấy languages hỗ trợ
     */
    public function getSupportedLanguages(): array
    {
        if ($this->languages) {
            return $this->languages;
        }

        return [$this->language_code];
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Format tên quốc gia với flag
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if ($this->flag_emoji) {
            $name = $this->flag_emoji . ' ' . $name;
        }
        return $name;
    }

    /**
     * Lấy URL flag icon
     */
    public function getFlagUrlAttribute(): ?string
    {
        if ($this->flag_icon) {
            // Loại bỏ slash đầu để tránh double slash
            $cleanPath = ltrim($this->flag_icon, '/');
            return asset('storage/' . $cleanPath);
        }
        return null;
    }

    /**
     * Kiểm tra có phải quốc gia metric không
     */
    public function isMetricSystem(): bool
    {
        return $this->measurement_system === 'metric';
    }

    /**
     * Kiểm tra có phải quốc gia imperial không
     */
    public function isImperialSystem(): bool
    {
        return $this->measurement_system === 'imperial';
    }

    /**
     * Lấy đơn vị đo lường chính
     */
    public function getPrimaryUnits(): array
    {
        return match($this->measurement_system) {
            'metric' => ['length' => 'mm', 'weight' => 'kg', 'temperature' => '°C'],
            'imperial' => ['length' => 'inch', 'weight' => 'lb', 'temperature' => '°F'],
            'mixed' => ['length' => 'mm/inch', 'weight' => 'kg/lb', 'temperature' => '°C/°F'],
            default => ['length' => 'mm', 'weight' => 'kg', 'temperature' => '°C']
        };
    }

    // ===================================================================
    // STATISTICAL METHODS
    // ===================================================================

    /**
     * Đếm số forum trong quốc gia
     */
    public function getForumCountAttribute(): int
    {
        return $this->forums()->count();
    }

    /**
     * Đếm số user active
     */
    public function getActiveUserCountAttribute(): int
    {
        return $this->activeUsers()->count();
    }

    /**
     * Lấy khu vực phổ biến nhất
     */
    public function getMostPopularRegion(): ?Region
    {
        return $this->regions()
            ->withCount('users')
            ->orderByDesc('users_count')
            ->first();
    }

    // ===================================================================
    // QUERY METHODS
    // ===================================================================

    /**
     * Lấy danh sách quốc gia cho dropdown
     */
    public static function getForDropdown(): array
    {
        return static::active()
            ->allowRegistration()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Lấy quốc gia theo code
     */
    public static function findByCode(string $code): ?static
    {
        return static::where('code', strtoupper($code))
            ->orWhere('code_alpha3', strtoupper($code))
            ->first();
    }

    /**
     * Lấy quốc gia với statistics
     */
    public static function withStatistics()
    {
        return static::withCount([
            'regions',
            'users',
            'activeUsers'
        ]);
    }
}
