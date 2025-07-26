<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Base class for non-critical emails that can be queued
 */
abstract class QueueableEmailNotification extends BaseEmailNotification implements ShouldQueue
{
    /**
     * Non-critical emails can be queued based on configuration
     */
    public function shouldQueue(): bool
    {
        return parent::shouldQueue();
    }

    /**
     * Set queue delay for non-critical emails
     */
    public function delay($notifiable = null)
    {
        // Add small delay to batch emails efficiently
        return now()->addSeconds(5);
    }

    /**
     * Number of times to retry if failed
     */
    public $tries = 3;

    /**
     * Timeout for queue job
     */
    public $timeout = 60;
}
