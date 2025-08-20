<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_id',
        'old_content',
        'new_content',
        'changed_by',
        'change_reason',
    ];

    /**
     * Translation relationship
     */
    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class);
    }

    /**
     * User who made the change
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope: Recent changes
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: By user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('changed_by', $userId);
    }
}
