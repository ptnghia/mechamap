<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationDeliverySchedulerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBatchNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    private User $user;
    private string $notificationType;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $notificationType)
    {
        $this->user = $user;
        $this->notificationType = $notificationType;
        
        // Set queue based on priority
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Processing batch notification job", [
                'user_id' => $this->user->id,
                'notification_type' => $this->notificationType,
                'job_id' => $this->job->getJobId(),
            ]);

            $success = NotificationDeliverySchedulerService::processBatchNotifications(
                $this->user, 
                $this->notificationType
            );

            if (!$success) {
                throw new \Exception("Failed to process batch notifications");
            }

            Log::info("Batch notification job completed successfully", [
                'user_id' => $this->user->id,
                'notification_type' => $this->notificationType,
            ]);

        } catch (\Exception $e) {
            Log::error("Batch notification job failed", [
                'user_id' => $this->user->id,
                'notification_type' => $this->notificationType,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Batch notification job failed permanently", [
            'user_id' => $this->user->id,
            'notification_type' => $this->notificationType,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Could implement fallback logic here
        // e.g., send individual notifications instead of batch
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'batch-notification',
            'user:' . $this->user->id,
            'type:' . $this->notificationType,
        ];
    }
}
