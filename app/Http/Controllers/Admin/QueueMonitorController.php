<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class QueueMonitorController extends Controller
{
    /**
     * Display queue monitoring dashboard
     */
    public function index()
    {
        $stats = $this->getQueueStats();
        $recentJobs = $this->getRecentJobs();
        $failedJobs = $this->getFailedJobs();
        $queueSizes = $this->getQueueSizes();
        
        return view('admin.queue.monitor', compact('stats', 'recentJobs', 'failedJobs', 'queueSizes'));
    }

    /**
     * Get queue statistics
     */
    private function getQueueStats()
    {
        $cacheKey = 'queue_stats_' . now()->format('Y-m-d-H-i');
        
        return Cache::remember($cacheKey, 60, function () {
            $totalJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            $processingJobs = DB::table('jobs')->where('reserved_at', '!=', null)->count();
            
            // Email specific stats
            $emailJobs = DB::table('jobs')
                ->where('payload', 'like', '%SendWelcomeEmail%')
                ->orWhere('payload', 'like', '%CustomVerifyEmail%')
                ->orWhere('payload', 'like', '%CustomResetPassword%')
                ->count();
                
            $emailFailedJobs = DB::table('failed_jobs')
                ->where('payload', 'like', '%SendWelcomeEmail%')
                ->orWhere('payload', 'like', '%CustomVerifyEmail%')
                ->orWhere('payload', 'like', '%CustomResetPassword%')
                ->count();

            // Performance metrics
            $avgProcessingTime = $this->getAverageProcessingTime();
            $successRate = $totalJobs > 0 ? (($totalJobs - $failedJobs) / $totalJobs) * 100 : 100;

            return [
                'total_jobs' => $totalJobs,
                'failed_jobs' => $failedJobs,
                'processing_jobs' => $processingJobs,
                'pending_jobs' => $totalJobs - $processingJobs,
                'email_jobs' => $emailJobs,
                'email_failed_jobs' => $emailFailedJobs,
                'avg_processing_time' => $avgProcessingTime,
                'success_rate' => round($successRate, 2),
                'last_updated' => now()->format('Y-m-d H:i:s'),
            ];
        });
    }

    /**
     * Get recent jobs
     */
    private function getRecentJobs($limit = 20)
    {
        return DB::table('jobs')
            ->select('id', 'queue', 'payload', 'attempts', 'created_at', 'reserved_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                $job->job_class = $payload['displayName'] ?? 'Unknown';
                $job->status = $job->reserved_at ? 'Processing' : 'Pending';
                return $job;
            });
    }

    /**
     * Get failed jobs
     */
    private function getFailedJobs($limit = 20)
    {
        return DB::table('failed_jobs')
            ->select('id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at')
            ->orderBy('failed_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                $job->job_class = $payload['displayName'] ?? 'Unknown';
                $job->error_message = $this->extractErrorMessage($job->exception);
                return $job;
            });
    }

    /**
     * Get queue sizes by queue name
     */
    private function getQueueSizes()
    {
        return DB::table('jobs')
            ->select('queue', DB::raw('count(*) as count'))
            ->groupBy('queue')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get average processing time
     */
    private function getAverageProcessingTime()
    {
        // This would require custom logging to track processing times
        // For now, return a placeholder
        return '2.5s';
    }

    /**
     * Extract error message from exception
     */
    private function extractErrorMessage($exception)
    {
        $lines = explode("\n", $exception);
        return $lines[0] ?? 'Unknown error';
    }

    /**
     * Retry failed job
     */
    public function retryJob(Request $request)
    {
        $jobId = $request->input('job_id');
        
        try {
            \Artisan::call('queue:retry', ['id' => $jobId]);
            return response()->json(['success' => true, 'message' => 'Job đã được thêm vào queue để thử lại']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Clear failed jobs
     */
    public function clearFailedJobs()
    {
        try {
            \Artisan::call('queue:flush');
            return response()->json(['success' => true, 'message' => 'Đã xóa tất cả failed jobs']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Get queue stats API for real-time updates
     */
    public function apiStats()
    {
        return response()->json($this->getQueueStats());
    }
}
