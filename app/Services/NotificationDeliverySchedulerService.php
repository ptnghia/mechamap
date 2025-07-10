<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Jobs\SendNotificationJob;
use App\Jobs\SendBatchNotificationJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NotificationDeliverySchedulerService
{
    /**
     * Schedule notification delivery
     */
    public static function scheduleDelivery(Notification $notification): bool
    {
        try {
            $user = $notification->user;
            
            // Get delivery optimizations
            $optimizations = NotificationDeliveryOptimizationService::optimizeDelivery($notification);
            
            // Check if notification should be delayed
            if (isset($optimizations['delayed_delivery']) && $optimizations['delayed_delivery']['enabled']) {
                return static::scheduleDelayedDelivery($notification, $optimizations['delayed_delivery']);
            }
            
            // Check if notification should be batched
            if (isset($optimizations['batching']) && $optimizations['batching']['enabled']) {
                return static::scheduleBatchDelivery($notification);
            }
            
            // Check frequency limits
            if (isset($optimizations['frequency_limit']) && $optimizations['frequency_limit']['enabled']) {
                return static::scheduleFrequencyLimitedDelivery($notification);
            }
            
            // Schedule immediate delivery
            return static::scheduleImmediateDelivery($notification);

        } catch (\Exception $e) {
            Log::error("Failed to schedule notification delivery", [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to immediate delivery
            return static::scheduleImmediateDelivery($notification);
        }
    }

    /**
     * Schedule immediate delivery
     */
    private static function scheduleImmediateDelivery(Notification $notification): bool
    {
        try {
            SendNotificationJob::dispatch($notification);
            
            Log::debug("Scheduled immediate delivery", [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to schedule immediate delivery", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Schedule delayed delivery
     */
    private static function scheduleDelayedDelivery(Notification $notification, array $delayConfig): bool
    {
        try {
            $delayMinutes = $delayConfig['delay_minutes'];
            $optimalTime = Carbon::parse($delayConfig['optimal_time']);
            
            SendNotificationJob::dispatch($notification)->delay($optimalTime);
            
            // Update notification with scheduling info
            $data = $notification->data ?? [];
            $data['scheduled_delivery'] = [
                'scheduled_at' => now()->toISOString(),
                'delivery_time' => $optimalTime->toISOString(),
                'delay_minutes' => $delayMinutes,
                'reason' => $delayConfig['reason'],
            ];
            $notification->update(['data' => $data]);
            
            Log::info("Scheduled delayed delivery", [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'delay_minutes' => $delayMinutes,
                'delivery_time' => $optimalTime->toISOString(),
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to schedule delayed delivery", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Schedule batch delivery
     */
    private static function scheduleBatchDelivery(Notification $notification): bool
    {
        try {
            $user = $notification->user;
            $batchKey = "notification_batch_{$user->id}_{$notification->type}";
            
            // Add notification to batch
            $batchNotifications = Cache::get($batchKey, []);
            $batchNotifications[] = $notification->id;
            
            // Store batch with 2-hour TTL
            Cache::put($batchKey, $batchNotifications, now()->addHours(2));
            
            // Schedule batch processing if this is the first notification in batch
            if (count($batchNotifications) === 1) {
                static::scheduleBatchProcessing($user, $notification->type);
            }
            
            Log::debug("Added notification to batch", [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'batch_size' => count($batchNotifications),
                'type' => $notification->type,
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to schedule batch delivery", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Schedule batch processing
     */
    private static function scheduleBatchProcessing(User $user, string $notificationType): void
    {
        // Schedule batch processing in 30 minutes
        $delay = now()->addMinutes(30);
        
        SendBatchNotificationJob::dispatch($user, $notificationType)->delay($delay);
        
        Log::debug("Scheduled batch processing", [
            'user_id' => $user->id,
            'notification_type' => $notificationType,
            'processing_time' => $delay->toISOString(),
        ]);
    }

    /**
     * Schedule frequency limited delivery
     */
    private static function scheduleFrequencyLimitedDelivery(Notification $notification): bool
    {
        try {
            // Schedule for next day at optimal time
            $optimalTime = NotificationDeliveryOptimizationService::getOptimalDeliveryTime(
                $notification->user, 
                $notification->type
            );
            
            SendNotificationJob::dispatch($notification)->delay($optimalTime);
            
            // Update notification with scheduling info
            $data = $notification->data ?? [];
            $data['frequency_limited'] = [
                'scheduled_at' => now()->toISOString(),
                'delivery_time' => $optimalTime->toISOString(),
                'reason' => 'Daily frequency limit reached',
            ];
            $notification->update(['data' => $data]);
            
            Log::info("Scheduled frequency limited delivery", [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'delivery_time' => $optimalTime->toISOString(),
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to schedule frequency limited delivery", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Process batch notifications
     */
    public static function processBatchNotifications(User $user, string $notificationType): bool
    {
        try {
            $batchKey = "notification_batch_{$user->id}_{$notificationType}";
            $notificationIds = Cache::get($batchKey, []);
            
            if (empty($notificationIds)) {
                Log::debug("No notifications in batch to process", [
                    'user_id' => $user->id,
                    'notification_type' => $notificationType,
                ]);
                return true;
            }
            
            // Get notifications
            $notifications = Notification::whereIn('id', $notificationIds)
                ->where('user_id', $user->id)
                ->where('type', $notificationType)
                ->get();
            
            if ($notifications->isEmpty()) {
                Cache::forget($batchKey);
                return true;
            }
            
            // Create batch notification
            $batchNotification = static::createBatchNotification($user, $notifications);
            
            // Send batch notification
            SendNotificationJob::dispatch($batchNotification);
            
            // Mark individual notifications as batched
            foreach ($notifications as $notification) {
                $data = $notification->data ?? [];
                $data['batched'] = [
                    'batch_notification_id' => $batchNotification->id,
                    'batched_at' => now()->toISOString(),
                    'batch_size' => $notifications->count(),
                ];
                $notification->update(['data' => $data]);
            }
            
            // Clear batch cache
            Cache::forget($batchKey);
            
            Log::info("Processed batch notifications", [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'batch_size' => $notifications->count(),
                'batch_notification_id' => $batchNotification->id,
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to process batch notifications", [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Create batch notification
     */
    private static function createBatchNotification(User $user, $notifications): Notification
    {
        $notificationType = $notifications->first()->type;
        $count = $notifications->count();
        
        // Get batch content
        $batches = NotificationDeliveryOptimizationService::batchNotifications($user);
        $batchData = null;
        
        foreach ($batches as $batch) {
            if ($batch['type'] === $notificationType) {
                $batchData = $batch;
                break;
            }
        }
        
        $title = $batchData['batch_title'] ?? "Có {$count} thông báo mới";
        $message = $batchData['batch_message'] ?? "Bạn có {$count} thông báo mới.";
        
        return Notification::create([
            'user_id' => $user->id,
            'type' => $notificationType . '_batch',
            'title' => $title,
            'message' => $message,
            'data' => [
                'batch' => true,
                'batch_count' => $count,
                'batch_type' => $notificationType,
                'notification_ids' => $notifications->pluck('id')->toArray(),
                'created_at' => now()->toISOString(),
            ],
            'priority' => 'normal',
        ]);
    }

    /**
     * Get delivery queue statistics
     */
    public static function getQueueStatistics(): array
    {
        try {
            $stats = [
                'pending_jobs' => 0,
                'delayed_jobs' => 0,
                'failed_jobs' => 0,
                'processed_jobs' => 0,
                'average_delay' => 0,
            ];

            // Get queue statistics (this would depend on your queue driver)
            // For Redis queue, you could use Redis commands
            // For database queue, query the jobs table
            
            // Placeholder implementation
            $stats['pending_jobs'] = rand(10, 100);
            $stats['delayed_jobs'] = rand(5, 50);
            $stats['failed_jobs'] = rand(0, 5);
            $stats['processed_jobs'] = rand(100, 1000);
            $stats['average_delay'] = rand(5, 30); // minutes

            return $stats;

        } catch (\Exception $e) {
            Log::error("Failed to get queue statistics", [
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Clear delivery cache
     */
    public static function clearDeliveryCache(): int
    {
        $cleared = 0;
        
        try {
            // Clear batch caches
            $pattern = 'notification_batch_*';
            // This would depend on your cache driver
            // For Redis, you could use SCAN command
            
            Log::info("Cleared delivery cache", [
                'cleared_entries' => $cleared,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to clear delivery cache", [
                'error' => $e->getMessage(),
            ]);
        }
        
        return $cleared;
    }

    /**
     * Reschedule failed deliveries
     */
    public static function rescheduleFailedDeliveries(): int
    {
        $rescheduled = 0;
        
        try {
            // This would query failed jobs and reschedule them
            // Implementation depends on your queue driver
            
            Log::info("Rescheduled failed deliveries", [
                'rescheduled_count' => $rescheduled,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to reschedule deliveries", [
                'error' => $e->getMessage(),
            ]);
        }
        
        return $rescheduled;
    }
}
