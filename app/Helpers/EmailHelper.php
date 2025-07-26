<?php

namespace App\Helpers;

class EmailHelper
{
    /**
     * Determine if email should be queued based on environment and email type
     */
    public static function shouldQueue(string $emailType = 'default'): bool
    {
        // Critical emails that should never be queued
        $criticalEmails = [
            'email_verification',
            'password_reset',
            'security_alert',
        ];

        // Don't queue critical emails
        if (in_array($emailType, $criticalEmails)) {
            return false;
        }

        // Don't queue in development
        if (app()->environment('local', 'development')) {
            return false;
        }

        // Only queue if Redis is available
        if (config('queue.default') === 'redis') {
            try {
                // Test Redis connection
                \Illuminate\Support\Facades\Redis::ping();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        // Don't queue for database queue (too slow)
        return false;
    }

    /**
     * Get appropriate queue name for email type
     */
    public static function getQueueName(string $emailType = 'default'): string
    {
        $queueMap = [
            'welcome' => 'emails-low',
            'newsletter' => 'emails-low',
            'notification' => 'emails-medium',
            'default' => 'emails',
        ];

        return $queueMap[$emailType] ?? $queueMap['default'];
    }
}
