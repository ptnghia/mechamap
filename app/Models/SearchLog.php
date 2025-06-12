<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $query
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property int $results_count
 * @property int $response_time_ms
 * @property array<array-key, mixed>|null $filters
 * @property string|null $content_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereQuery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereResponseTimeMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereResultsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog withResults()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchLog withoutResults()
 * @mixin \Eloquent
 */
class SearchLog extends Model
{
    use HasFactory;

    public $timestamps = false; // We only use created_at

    protected $fillable = [
        'query',
        'user_id',
        'ip_address',
        'user_agent',
        'results_count',
        'response_time_ms',
        'filters',
        'content_type',
        'created_at'
    ];

    protected $casts = [
        'filters' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user who performed the search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for recent searches
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for successful searches (with results)
     */
    public function scopeWithResults($query)
    {
        return $query->where('results_count', '>', 0);
    }

    /**
     * Scope for failed searches (no results)
     */
    public function scopeWithoutResults($query)
    {
        return $query->where('results_count', 0);
    }
}
