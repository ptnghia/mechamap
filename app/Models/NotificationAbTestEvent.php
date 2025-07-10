<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationAbTestEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'notification_id',
        'event_type',
        'event_data',
        'occurred_at',
        'session_id',
        'user_agent',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Event types
    const EVENT_NOTIFICATION_SENT = 'notification_sent';
    const EVENT_NOTIFICATION_OPENED = 'notification_opened';
    const EVENT_NOTIFICATION_CLICKED = 'notification_clicked';
    const EVENT_NOTIFICATION_DISMISSED = 'notification_dismissed';
    const EVENT_ACTION_TAKEN = 'action';
    const EVENT_CONVERSION = 'conversion';
    const EVENT_OPT_OUT = 'opt_out';

    /**
     * Get the participant
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(NotificationAbTestParticipant::class, 'participant_id');
    }

    /**
     * Get the notification
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Scope for specific event types
     */
    public function scopeOfType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for events within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('occurred_at', [$startDate, $endDate]);
    }

    /**
     * Get event summary
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'event_type' => $this->event_type,
            'occurred_at' => $this->occurred_at,
            'participant_variant' => $this->participant->variant,
            'event_data' => $this->event_data,
            'notification_id' => $this->notification_id,
        ];
    }
}
