<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EmailRetryService
{
    /**
     * Retry failed email jobs with intelligent backoff
     */
    public function retryFailedEmails($maxRetries = 3, $batchSize = 10)
    {
        $failedJobs = $this->getRetryableFailedJobs($maxRetries, $batchSize);
        
        $results = [
            'total' => $failedJobs->count(),
            'retried' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        foreach ($failedJobs as $job) {
            try {
                if ($this->shouldRetryJob($job)) {
                    $this->retryJob($job);
                    $results['retried']++;
                    
                    Log::info("Email job retried successfully", [
                        'job_id' => $job->uuid,
                        'job_class' => $this->extractJobClass($job->payload)
                    ]);
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'job_id' => $job->uuid,
                    'error' => $e->getMessage()
                ];
                
                Log::error("Failed to retry email job", [
                    'job_id' => $job->uuid,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Get failed jobs that can be retried
     */
    private function getRetryableFailedJobs($maxRetries, $batchSize)
    {
        return DB::table('failed_jobs')
            ->where('payload', 'like', '%Mail%')
            ->orWhere('payload', 'like', '%Email%')
            ->orWhere('payload', 'like', '%Notification%')
            ->whereRaw('JSON_EXTRACT(payload, "$.data.commandName") LIKE "%Mail%" OR JSON_EXTRACT(payload, "$.displayName") LIKE "%Mail%"')
            ->orderBy('failed_at', 'asc')
            ->limit($batchSize)
            ->get();
    }

    /**
     * Check if job should be retried based on failure reason and retry count
     */
    private function shouldRetryJob($job)
    {
        $retryKey = "email_retry_count_{$job->uuid}";
        $retryCount = Cache::get($retryKey, 0);
        
        // Max 3 retries
        if ($retryCount >= 3) {
            return false;
        }

        // Check failure reason
        $exception = $job->exception;
        
        // Don't retry certain types of errors
        $nonRetryableErrors = [
            'Invalid email address',
            'User not found',
            'Email address is blacklisted',
            'Authentication failed',
            'Invalid credentials'
        ];

        foreach ($nonRetryableErrors as $error) {
            if (strpos($exception, $error) !== false) {
                return false;
            }
        }

        // Check if enough time has passed (exponential backoff)
        $lastRetryKey = "email_last_retry_{$job->uuid}";
        $lastRetry = Cache::get($lastRetryKey);
        
        if ($lastRetry) {
            $backoffMinutes = pow(2, $retryCount) * 5; // 5, 10, 20 minutes
            $nextRetryTime = Carbon::parse($lastRetry)->addMinutes($backoffMinutes);
            
            if (now()->lt($nextRetryTime)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retry a specific job
     */
    private function retryJob($job)
    {
        $retryKey = "email_retry_count_{$job->uuid}";
        $lastRetryKey = "email_last_retry_{$job->uuid}";
        
        // Increment retry count
        $retryCount = Cache::get($retryKey, 0) + 1;
        Cache::put($retryKey, $retryCount, now()->addDays(7));
        Cache::put($lastRetryKey, now(), now()->addDays(7));

        // Parse job payload
        $payload = json_decode($job->payload, true);
        
        // Recreate and dispatch the job
        $this->recreateAndDispatchJob($payload, $job);
        
        // Remove from failed jobs table
        DB::table('failed_jobs')->where('uuid', $job->uuid)->delete();
    }

    /**
     * Recreate and dispatch job from payload
     */
    private function recreateAndDispatchJob($payload, $originalJob)
    {
        $jobClass = $payload['displayName'] ?? $payload['job'] ?? null;
        
        if (!$jobClass) {
            throw new \Exception("Cannot determine job class from payload");
        }

        // Handle different types of email jobs
        if (strpos($jobClass, 'SendQueuedMailable') !== false) {
            $this->retryMailableJob($payload);
        } elseif (strpos($jobClass, 'SendQueuedNotifications') !== false) {
            $this->retryNotificationJob($payload);
        } else {
            // Generic job retry
            $this->retryGenericJob($payload);
        }
    }

    /**
     * Retry mailable job
     */
    private function retryMailableJob($payload)
    {
        $data = unserialize($payload['data']['command']);
        
        if ($data && method_exists($data, 'send')) {
            Mail::send($data);
        }
    }

    /**
     * Retry notification job
     */
    private function retryNotificationJob($payload)
    {
        $data = unserialize($payload['data']['command']);
        
        if ($data && isset($data->notification) && isset($data->notifiable)) {
            $data->notifiable->notify($data->notification);
        }
    }

    /**
     * Retry generic job
     */
    private function retryGenericJob($payload)
    {
        // Use Laravel's built-in retry mechanism
        \Artisan::call('queue:retry', ['id' => $payload['uuid'] ?? 'all']);
    }

    /**
     * Extract job class from payload
     */
    private function extractJobClass($payload)
    {
        $data = json_decode($payload, true);
        return $data['displayName'] ?? $data['job'] ?? 'Unknown';
    }

    /**
     * Clean up old retry tracking data
     */
    public function cleanupRetryData($olderThanDays = 7)
    {
        $pattern = 'email_retry_count_*';
        $keys = Cache::getRedis()->keys($pattern);
        
        $cleaned = 0;
        foreach ($keys as $key) {
            $timestamp = Cache::get(str_replace('retry_count', 'last_retry', $key));
            
            if ($timestamp && Carbon::parse($timestamp)->lt(now()->subDays($olderThanDays))) {
                Cache::forget($key);
                Cache::forget(str_replace('retry_count', 'last_retry', $key));
                $cleaned++;
            }
        }

        return $cleaned;
    }

    /**
     * Get retry statistics
     */
    public function getRetryStats()
    {
        $failedEmailJobs = DB::table('failed_jobs')
            ->where(function($query) {
                $query->where('payload', 'like', '%Mail%')
                      ->orWhere('payload', 'like', '%Email%')
                      ->orWhere('payload', 'like', '%Notification%');
            })
            ->count();

        $totalFailedJobs = DB::table('failed_jobs')->count();
        
        return [
            'total_failed_jobs' => $totalFailedJobs,
            'failed_email_jobs' => $failedEmailJobs,
            'retryable_jobs' => $this->getRetryableFailedJobs(3, 1000)->count(),
            'last_cleanup' => Cache::get('email_retry_last_cleanup', 'Never'),
        ];
    }
}
