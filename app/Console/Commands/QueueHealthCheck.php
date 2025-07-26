<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class QueueHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:health-check 
                            {--alert : Send alerts if thresholds are exceeded}
                            {--fix : Automatically fix issues where possible}';

    /**
     * The console command description.
     */
    protected $description = 'Check queue health and alert on issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running queue health check...');
        $this->line('');

        $issues = [];
        $config = config('queue-workers.health_check');

        // Check 1: Failed jobs count
        $failedJobs = DB::table('failed_jobs')->count();
        if ($failedJobs > $config['max_failed_jobs']) {
            $issues[] = [
                'type' => 'failed_jobs',
                'severity' => 'high',
                'message' => "Too many failed jobs: {$failedJobs} (max: {$config['max_failed_jobs']})",
                'count' => $failedJobs
            ];
        }

        // Check 2: Queue size
        $queueSize = DB::table('jobs')->count();
        if ($queueSize > $config['max_queue_size']) {
            $issues[] = [
                'type' => 'queue_size',
                'severity' => 'medium',
                'message' => "Queue size too large: {$queueSize} (max: {$config['max_queue_size']})",
                'count' => $queueSize
            ];
        }

        // Check 3: Stuck jobs (processing for too long)
        $stuckJobs = DB::table('jobs')
            ->where('reserved_at', '<', now()->subSeconds($config['max_processing_time']))
            ->whereNotNull('reserved_at')
            ->count();

        if ($stuckJobs > 0) {
            $issues[] = [
                'type' => 'stuck_jobs',
                'severity' => 'high',
                'message' => "Jobs stuck in processing: {$stuckJobs}",
                'count' => $stuckJobs
            ];
        }

        // Check 4: Queue workers status
        $workersRunning = $this->checkWorkersRunning();
        if (!$workersRunning) {
            $issues[] = [
                'type' => 'no_workers',
                'severity' => 'critical',
                'message' => "No queue workers are running",
                'count' => 0
            ];
        }

        // Check 5: Email-specific issues
        $emailIssues = $this->checkEmailSpecificIssues();
        $issues = array_merge($issues, $emailIssues);

        // Display results
        if (empty($issues)) {
            $this->info('✅ Queue health check passed - no issues found!');
        } else {
            $this->displayIssues($issues);
            
            if ($this->option('fix')) {
                $this->fixIssues($issues);
            }
            
            if ($this->option('alert')) {
                $this->sendAlerts($issues);
            }
        }

        // Store health check results
        Cache::put('queue_health_check_last_run', now(), now()->addHours(24));
        Cache::put('queue_health_check_issues', $issues, now()->addHours(24));

        return empty($issues) ? 0 : 1;
    }

    /**
     * Check if queue workers are running
     */
    private function checkWorkersRunning()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('tasklist /FI "IMAGENAME eq php.exe" /FO CSV');
            return strpos($output, 'queue:work') !== false;
        } else {
            $output = shell_exec('ps aux | grep "queue:work" | grep -v grep');
            return !empty(trim($output));
        }
    }

    /**
     * Check email-specific issues
     */
    private function checkEmailSpecificIssues()
    {
        $issues = [];

        // Check failed email jobs
        $failedEmailJobs = DB::table('failed_jobs')
            ->where(function($query) {
                $query->where('payload', 'like', '%Mail%')
                      ->orWhere('payload', 'like', '%Email%')
                      ->orWhere('payload', 'like', '%Notification%');
            })
            ->count();

        if ($failedEmailJobs > 5) {
            $issues[] = [
                'type' => 'failed_email_jobs',
                'severity' => 'medium',
                'message' => "Too many failed email jobs: {$failedEmailJobs}",
                'count' => $failedEmailJobs
            ];
        }

        // Check email queue backlog
        $emailQueueSize = DB::table('jobs')
            ->where(function($query) {
                $query->where('queue', 'like', '%email%')
                      ->orWhere('payload', 'like', '%Mail%');
            })
            ->count();

        if ($emailQueueSize > 50) {
            $issues[] = [
                'type' => 'email_queue_backlog',
                'severity' => 'medium',
                'message' => "Email queue backlog: {$emailQueueSize} jobs",
                'count' => $emailQueueSize
            ];
        }

        return $issues;
    }

    /**
     * Display issues in a formatted table
     */
    private function displayIssues($issues)
    {
        $this->error('❌ Queue health check found issues:');
        $this->line('');

        $tableData = [];
        foreach ($issues as $issue) {
            $severity = match($issue['severity']) {
                'critical' => '🔴 Critical',
                'high' => '🟠 High',
                'medium' => '🟡 Medium',
                'low' => '🟢 Low',
                default => $issue['severity']
            };

            $tableData[] = [
                $issue['type'],
                $severity,
                $issue['message']
            ];
        }

        $this->table(['Issue Type', 'Severity', 'Description'], $tableData);
    }

    /**
     * Automatically fix issues where possible
     */
    private function fixIssues($issues)
    {
        $this->info('Attempting to fix issues automatically...');
        $this->line('');

        foreach ($issues as $issue) {
            switch ($issue['type']) {
                case 'stuck_jobs':
                    $this->fixStuckJobs();
                    break;
                case 'failed_email_jobs':
                    $this->fixFailedEmailJobs();
                    break;
                case 'no_workers':
                    $this->fixNoWorkers();
                    break;
            }
        }
    }

    /**
     * Fix stuck jobs
     */
    private function fixStuckJobs()
    {
        $this->info('Fixing stuck jobs...');
        
        $config = config('queue-workers.health_check');
        $stuckJobs = DB::table('jobs')
            ->where('reserved_at', '<', now()->subSeconds($config['max_processing_time']))
            ->whereNotNull('reserved_at')
            ->update(['reserved_at' => null, 'reserved_by' => null]);

        $this->info("Released {$stuckJobs} stuck jobs back to queue.");
    }

    /**
     * Fix failed email jobs
     */
    private function fixFailedEmailJobs()
    {
        $this->info('Retrying failed email jobs...');
        
        \Artisan::call('email:retry-failed', ['--batch-size' => 5]);
        $this->info('Email retry process completed.');
    }

    /**
     * Fix no workers issue
     */
    private function fixNoWorkers()
    {
        $this->warn('No queue workers running. Please start workers manually:');
        $this->line('php artisan queue:manage start');
    }

    /**
     * Send alerts for critical issues
     */
    private function sendAlerts($issues)
    {
        $criticalIssues = array_filter($issues, function($issue) {
            return in_array($issue['severity'], ['critical', 'high']);
        });

        if (empty($criticalIssues)) {
            return;
        }

        $alertConfig = config('queue-workers.monitoring.alert_channels');
        
        if ($alertConfig['email']) {
            $this->sendEmailAlert($criticalIssues, $alertConfig['email']);
        }

        // Add Slack alert if configured
        if ($alertConfig['slack']) {
            $this->sendSlackAlert($criticalIssues, $alertConfig['slack']);
        }
    }

    /**
     * Send email alert
     */
    private function sendEmailAlert($issues, $email)
    {
        $subject = 'Queue Health Alert - MechaMap';
        $message = "Queue health check found " . count($issues) . " critical issues:\n\n";
        
        foreach ($issues as $issue) {
            $message .= "- {$issue['message']}\n";
        }

        try {
            Mail::raw($message, function($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });
            
            $this->info("Alert email sent to {$email}");
        } catch (\Exception $e) {
            $this->error("Failed to send alert email: " . $e->getMessage());
        }
    }

    /**
     * Send Slack alert
     */
    private function sendSlackAlert($issues, $webhookUrl)
    {
        // Implementation for Slack webhook
        $this->info('Slack alerts not implemented yet.');
    }
}
