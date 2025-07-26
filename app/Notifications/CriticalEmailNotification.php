<?php

namespace App\Notifications;

/**
 * Base class for critical emails that must be sent immediately
 * These emails are never queued regardless of configuration
 */
abstract class CriticalEmailNotification extends BaseEmailNotification
{
    /**
     * Critical emails are always sent immediately
     */
    public function shouldQueue(): bool
    {
        return false;
    }

    /**
     * Critical emails use high priority queue if forced to queue
     */
    public function getQueueName(): string
    {
        return 'emails-critical';
    }
}
