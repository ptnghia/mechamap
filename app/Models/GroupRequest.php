<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\GroupRequestStatus;

class GroupRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_type_id',
        'title',
        'description',
        'justification',
        'expected_members',
        'creator_id',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
        'rejection_reason',
        'requested_at',
    ];

    protected $casts = [
        'status' => GroupRequestStatus::class,
        'reviewed_at' => 'datetime',
        'requested_at' => 'datetime',
    ];

    /**
     * Get the conversation type for this request
     */
    public function conversationType(): BelongsTo
    {
        return $this->belongsTo(ConversationType::class);
    }

    /**
     * Get the creator of this request
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the admin who reviewed this request
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the conversation created from this request (if approved)
     */
    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'group_request_id');
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === GroupRequestStatus::PENDING;
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === GroupRequestStatus::APPROVED;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === GroupRequestStatus::REJECTED;
    }

    /**
     * Check if request needs revision
     */
    public function needsRevision(): bool
    {
        return $this->status === GroupRequestStatus::NEEDS_REVISION;
    }

    /**
     * Check if request can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            GroupRequestStatus::PENDING,
            GroupRequestStatus::NEEDS_REVISION
        ]);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', GroupRequestStatus::PENDING);
    }

    /**
     * Scope for requests under review
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', GroupRequestStatus::UNDER_REVIEW);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', GroupRequestStatus::APPROVED);
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', GroupRequestStatus::REJECTED);
    }

    /**
     * Scope for requests by creator
     */
    public function scopeByCreator($query, int $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Scope for recent requests
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('requested_at', '>=', now()->subDays($days));
    }
}
