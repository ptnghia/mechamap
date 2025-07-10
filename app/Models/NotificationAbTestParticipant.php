<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationAbTestParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'ab_test_id',
        'user_id',
        'variant',
        'assigned_at',
        'first_notification_sent_at',
        'last_notification_sent_at',
        'total_notifications_sent',
        'total_notifications_opened',
        'total_notifications_clicked',
        'total_notifications_dismissed',
        'total_actions_taken',
        'engagement_score',
        'conversion_achieved',
        'conversion_value',
        'opted_out_at',
        'metadata',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'first_notification_sent_at' => 'datetime',
        'last_notification_sent_at' => 'datetime',
        'opted_out_at' => 'datetime',
        'conversion_achieved' => 'boolean',
        'conversion_value' => 'decimal:2',
        'engagement_score' => 'float',
        'metadata' => 'array',
    ];

    /**
     * Get the A/B test
     */
    public function abTest(): BelongsTo
    {
        return $this->belongsTo(NotificationAbTest::class, 'ab_test_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get participant events
     */
    public function events(): HasMany
    {
        return $this->hasMany(NotificationAbTestEvent::class, 'participant_id');
    }

    /**
     * Record notification sent
     */
    public function recordNotificationSent(): void
    {
        $this->increment('total_notifications_sent');
        
        if (!$this->first_notification_sent_at) {
            $this->update(['first_notification_sent_at' => now()]);
        }
        
        $this->update(['last_notification_sent_at' => now()]);
    }

    /**
     * Record notification opened
     */
    public function recordNotificationOpened(): void
    {
        $this->increment('total_notifications_opened');
        $this->updateEngagementScore();
    }

    /**
     * Record notification clicked
     */
    public function recordNotificationClicked(): void
    {
        $this->increment('total_notifications_clicked');
        $this->updateEngagementScore();
    }

    /**
     * Record notification dismissed
     */
    public function recordNotificationDismissed(): void
    {
        $this->increment('total_notifications_dismissed');
        $this->updateEngagementScore();
    }

    /**
     * Record action taken
     */
    public function recordActionTaken(string $action, array $context = []): void
    {
        $this->increment('total_actions_taken');
        $this->updateEngagementScore();

        // Record the specific event
        $this->events()->create([
            'event_type' => 'action',
            'event_data' => [
                'action' => $action,
                'context' => $context,
            ],
            'occurred_at' => now(),
        ]);
    }

    /**
     * Record conversion
     */
    public function recordConversion(float $value = 0): void
    {
        $this->update([
            'conversion_achieved' => true,
            'conversion_value' => $this->conversion_value + $value,
        ]);

        $this->events()->create([
            'event_type' => 'conversion',
            'event_data' => [
                'value' => $value,
                'total_value' => $this->conversion_value,
            ],
            'occurred_at' => now(),
        ]);

        $this->updateEngagementScore();
    }

    /**
     * Opt out of test
     */
    public function optOut(string $reason = null): void
    {
        $this->update([
            'opted_out_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], [
                'opt_out_reason' => $reason,
            ]),
        ]);

        $this->events()->create([
            'event_type' => 'opt_out',
            'event_data' => [
                'reason' => $reason,
            ],
            'occurred_at' => now(),
        ]);
    }

    /**
     * Update engagement score
     */
    private function updateEngagementScore(): void
    {
        $score = 0;
        
        // Base score from opens
        if ($this->total_notifications_sent > 0) {
            $openRate = $this->total_notifications_opened / $this->total_notifications_sent;
            $score += $openRate * 30; // 30 points max for open rate
        }

        // Score from clicks
        if ($this->total_notifications_opened > 0) {
            $clickRate = $this->total_notifications_clicked / $this->total_notifications_opened;
            $score += $clickRate * 40; // 40 points max for click rate
        }

        // Score from actions
        $score += min($this->total_actions_taken * 5, 20); // 5 points per action, max 20

        // Bonus for conversion
        if ($this->conversion_achieved) {
            $score += 10;
        }

        // Penalty for dismissals
        if ($this->total_notifications_sent > 0) {
            $dismissRate = $this->total_notifications_dismissed / $this->total_notifications_sent;
            $score -= $dismissRate * 20; // Penalty for dismissals
        }

        // Ensure score is between 0 and 100
        $score = max(0, min(100, $score));

        $this->update(['engagement_score' => $score]);
    }

    /**
     * Get open rate
     */
    public function getOpenRate(): float
    {
        if ($this->total_notifications_sent === 0) {
            return 0;
        }

        return round(($this->total_notifications_opened / $this->total_notifications_sent) * 100, 2);
    }

    /**
     * Get click rate
     */
    public function getClickRate(): float
    {
        if ($this->total_notifications_opened === 0) {
            return 0;
        }

        return round(($this->total_notifications_clicked / $this->total_notifications_opened) * 100, 2);
    }

    /**
     * Get dismiss rate
     */
    public function getDismissRate(): float
    {
        if ($this->total_notifications_sent === 0) {
            return 0;
        }

        return round(($this->total_notifications_dismissed / $this->total_notifications_sent) * 100, 2);
    }

    /**
     * Get conversion rate
     */
    public function getConversionRate(): float
    {
        if ($this->total_notifications_sent === 0) {
            return 0;
        }

        return $this->conversion_achieved ? 
            round((1 / $this->total_notifications_sent) * 100, 2) : 0;
    }

    /**
     * Check if participant is active
     */
    public function isActive(): bool
    {
        return $this->opted_out_at === null && $this->abTest->isActive();
    }

    /**
     * Get participant summary
     */
    public function getSummary(): array
    {
        return [
            'variant' => $this->variant,
            'assigned_at' => $this->assigned_at,
            'notifications_sent' => $this->total_notifications_sent,
            'open_rate' => $this->getOpenRate(),
            'click_rate' => $this->getClickRate(),
            'dismiss_rate' => $this->getDismissRate(),
            'conversion_rate' => $this->getConversionRate(),
            'engagement_score' => $this->engagement_score,
            'conversion_value' => $this->conversion_value,
            'is_active' => $this->isActive(),
            'opted_out' => $this->opted_out_at !== null,
        ];
    }
}
