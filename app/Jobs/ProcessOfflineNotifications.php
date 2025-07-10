<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Notification;
use App\Services\RealTimeNotificationService;
use App\Services\OfflineNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOfflineNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public Notification $notification;
    public int $maxAttempts = 5;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Notification $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if user is now online
            if (RealTimeNotificationService::isUserOnline($this->user)) {
                $this->handleOnlineUser();
                return;
            }

            // Check if notification is still valid
            if (!$this->isNotificationValid()) {
                $this->markAsExpired();
                return;
            }

            // Increment delivery attempt
            $this->incrementDeliveryAttempt();

            // Check if max attempts reached
            if ($this->getDeliveryAttempts() >= $this->maxAttempts) {
                $this->handleMaxAttemptsReached();
                return;
            }

            // Try alternative delivery methods
            $this->tryAlternativeDelivery();

            Log::info('Offline notification processing completed', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notification->id,
                'attempt' => $this->getDeliveryAttempts(),
                'user_online' => RealTimeNotificationService::isUserOnline($this->user),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process offline notification', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle when user comes online
     */
    private function handleOnlineUser(): void
    {
        try {
            // Deliver notification immediately
            broadcast(new \App\Events\NotificationBroadcastEvent($this->user, $this->notification))->toOthers();

            // Update notification status
            $data = $this->notification->data ?? [];
            $data['delivered_at'] = now()->toISOString();
            $data['delivery_method'] = 'online_delivery';
            $data['delivered_from_offline'] = true;
            
            $this->notification->update(['data' => $data]);

            Log::info('Offline notification delivered to online user', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notification->id,
                'delivery_attempts' => $this->getDeliveryAttempts(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to deliver notification to online user', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if notification is still valid for delivery
     */
    private function isNotificationValid(): bool
    {
        // Refresh notification from database
        $this->notification->refresh();

        // Check if notification still exists and is unread
        if (!$this->notification->exists || $this->notification->is_read) {
            return false;
        }

        // Check if notification is not too old
        $maxAge = now()->subDays(7);
        if ($this->notification->created_at->lt($maxAge)) {
            return false;
        }

        // Check if user still exists and is active
        $this->user->refresh();
        if (!$this->user->exists) {
            return false;
        }

        return true;
    }

    /**
     * Mark notification as expired
     */
    private function markAsExpired(): void
    {
        $data = $this->notification->data ?? [];
        $data['expired'] = true;
        $data['expired_at'] = now()->toISOString();
        $data['expiry_reason'] = $this->notification->is_read ? 'already_read' : 'too_old';

        $this->notification->update(['data' => $data]);

        Log::info('Offline notification marked as expired', [
            'user_id' => $this->user->id,
            'notification_id' => $this->notification->id,
            'reason' => $data['expiry_reason'],
        ]);
    }

    /**
     * Increment delivery attempt counter
     */
    private function incrementDeliveryAttempt(): void
    {
        $data = $this->notification->data ?? [];
        $data['delivery_attempts'] = ($data['delivery_attempts'] ?? 0) + 1;
        $data['last_attempt_at'] = now()->toISOString();

        $this->notification->update(['data' => $data]);
    }

    /**
     * Get current delivery attempts count
     */
    private function getDeliveryAttempts(): int
    {
        return $this->notification->data['delivery_attempts'] ?? 0;
    }

    /**
     * Handle when max attempts are reached
     */
    private function handleMaxAttemptsReached(): void
    {
        $data = $this->notification->data ?? [];
        $data['max_attempts_reached'] = true;
        $data['max_attempts_reached_at'] = now()->toISOString();
        $data['final_delivery_method'] = 'email_fallback';

        // Try email as final fallback for high priority notifications
        if ($this->notification->priority === 'high') {
            $this->sendEmailFallback();
            $data['email_fallback_sent'] = true;
        }

        $this->notification->update(['data' => $data]);

        Log::warning('Offline notification max attempts reached', [
            'user_id' => $this->user->id,
            'notification_id' => $this->notification->id,
            'total_attempts' => $this->maxAttempts,
            'email_fallback' => $this->notification->priority === 'high',
        ]);
    }

    /**
     * Try alternative delivery methods
     */
    private function tryAlternativeDelivery(): void
    {
        $attempts = $this->getDeliveryAttempts();

        // Different strategies based on attempt number
        switch ($attempts) {
            case 1:
                // First attempt: Store in cache for immediate delivery when online
                $this->storeInCache();
                break;

            case 2:
                // Second attempt: Try email for high priority notifications
                if ($this->notification->priority === 'high') {
                    $this->sendEmailNotification();
                }
                break;

            case 3:
                // Third attempt: Update user's offline notification summary
                $this->updateOfflineSummary();
                break;

            case 4:
                // Fourth attempt: Final cache update
                $this->updateFinalCache();
                break;
        }
    }

    /**
     * Store notification in cache for immediate delivery
     */
    private function storeInCache(): void
    {
        OfflineNotificationService::storeForOfflineUser($this->user, $this->notification);
    }

    /**
     * Send email notification for high priority items
     */
    private function sendEmailNotification(): void
    {
        try {
            if ($this->user->email_notifications_enabled) {
                // Use existing email notification system
                \App\Services\NotificationService::sendEmailNotification($this->notification, $this->user);

                $data = $this->notification->data ?? [];
                $data['email_sent_offline'] = true;
                $data['email_sent_at'] = now()->toISOString();
                $this->notification->update(['data' => $data]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send offline email notification', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update user's offline notification summary
     */
    private function updateOfflineSummary(): void
    {
        $cacheKey = "user_offline_summary_{$this->user->id}";
        $summary = \Cache::get($cacheKey, [
            'total_notifications' => 0,
            'high_priority' => 0,
            'last_updated' => now()->toISOString(),
        ]);

        $summary['total_notifications']++;
        if ($this->notification->priority === 'high') {
            $summary['high_priority']++;
        }
        $summary['last_updated'] = now()->toISOString();

        \Cache::put($cacheKey, $summary, now()->addDays(7));
    }

    /**
     * Update final cache before giving up
     */
    private function updateFinalCache(): void
    {
        $cacheKey = "user_final_offline_notifications_{$this->user->id}";
        $notifications = \Cache::get($cacheKey, []);

        $notifications[] = [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'created_at' => $this->notification->created_at->toISOString(),
            'priority' => $this->notification->priority,
        ];

        // Keep only last 20 notifications
        if (count($notifications) > 20) {
            $notifications = array_slice($notifications, -20);
        }

        \Cache::put($cacheKey, $notifications, now()->addDays(7));
    }

    /**
     * Send email as final fallback
     */
    private function sendEmailFallback(): void
    {
        try {
            if ($this->user->email) {
                \Mail::to($this->user->email)->queue(new \App\Mail\OfflineNotificationFallback(
                    $this->user,
                    $this->notification
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email fallback', [
                'user_id' => $this->user->id,
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Offline notification job failed permanently', [
            'user_id' => $this->user->id,
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Mark notification as failed
        $data = $this->notification->data ?? [];
        $data['job_failed'] = true;
        $data['job_failed_at'] = now()->toISOString();
        $data['failure_reason'] = $exception->getMessage();

        $this->notification->update(['data' => $data]);
    }
}
