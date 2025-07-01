<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentationVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'user_id',
        'version_number',
        'content',
        'change_summary',
        'metadata',
        'is_major_version',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_major_version' => 'boolean',
    ];

    /**
     * Get the documentation
     */
    public function documentation(): BelongsTo
    {
        return $this->belongsTo(Documentation::class);
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// DocumentationView Model
class DocumentationView extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'time_spent',
        'scroll_percentage',
    ];

    protected $casts = [
        'time_spent' => 'integer',
        'scroll_percentage' => 'decimal:2',
    ];

    /**
     * Get the documentation
     */
    public function documentation(): BelongsTo
    {
        return $this->belongsTo(Documentation::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
