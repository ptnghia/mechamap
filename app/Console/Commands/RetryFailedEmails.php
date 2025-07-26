<?php

namespace App\Console\Commands;

use App\Services\EmailRetryService;
use Illuminate\Console\Command;

class RetryFailedEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:retry-failed 
                            {--max-retries=3 : Maximum number of retries per job}
                            {--batch-size=10 : Number of jobs to process in one batch}
                            {--cleanup : Clean up old retry tracking data}
                            {--stats : Show retry statistics}';

    /**
     * The console command description.
     */
    protected $description = 'Retry failed email jobs with intelligent backoff strategy';

    /**
     * Execute the console command.
     */
    public function handle(EmailRetryService $retryService)
    {
        if ($this->option('stats')) {
            return $this->showStats($retryService);
        }

        if ($this->option('cleanup')) {
            return $this->cleanup($retryService);
        }

        $maxRetries = $this->option('max-retries');
        $batchSize = $this->option('batch-size');

        $this->info("Starting email retry process...");
        $this->info("Max retries per job: {$maxRetries}");
        $this->info("Batch size: {$batchSize}");
        $this->line('');

        $results = $retryService->retryFailedEmails($maxRetries, $batchSize);

        // Display results
        $this->info("Email retry process completed!");
        $this->line('');
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total jobs processed', $results['total']],
                ['Successfully retried', $results['retried']],
                ['Skipped', $results['skipped']],
                ['Errors', count($results['errors'])],
            ]
        );

        if (!empty($results['errors'])) {
            $this->line('');
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("Job {$error['job_id']}: {$error['error']}");
            }
        }

        return 0;
    }

    /**
     * Show retry statistics
     */
    private function showStats(EmailRetryService $retryService)
    {
        $stats = $retryService->getRetryStats();

        $this->info('Email Retry Statistics:');
        $this->line('');
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Failed Jobs', $stats['total_failed_jobs']],
                ['Failed Email Jobs', $stats['failed_email_jobs']],
                ['Retryable Jobs', $stats['retryable_jobs']],
                ['Last Cleanup', $stats['last_cleanup']],
            ]
        );

        return 0;
    }

    /**
     * Clean up old retry tracking data
     */
    private function cleanup(EmailRetryService $retryService)
    {
        $this->info('Cleaning up old retry tracking data...');
        
        $cleaned = $retryService->cleanupRetryData();
        
        $this->info("Cleaned up {$cleaned} old retry tracking entries.");
        
        return 0;
    }
}
