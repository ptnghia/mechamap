<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
