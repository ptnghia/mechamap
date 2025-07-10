<?php

namespace App\Console\Commands;

use App\Services\WeeklyDigestService;
use Illuminate\Console\Command;

class SendWeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'digest:weekly 
                            {--dry-run : Show what would be sent without actually sending}
                            {--user= : Send digest to specific user ID only}
                            {--force : Force send even if already sent this week}
                            {--stats : Show digest statistics}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Send weekly digest notifications to users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📧 Weekly Digest Manager');
        $this->newLine();

        if ($this->option('stats')) {
            return $this->showStatistics();
        }

        if ($this->option('dry-run')) {
            return $this->dryRun();
        }

        return $this->sendDigests();
    }

    /**
     * Show digest statistics
     */
    private function showStatistics(): int
    {
        try {
            $statistics = WeeklyDigestService::getDigestStatistics();

            if ($this->option('json')) {
                $this->line(json_encode($statistics, JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            $this->displayStatistics($statistics);
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to get statistics: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display digest statistics
     */
    private function displayStatistics(array $stats): void
    {
        $this->info('📊 Weekly Digest Statistics');
        $this->newLine();

        $this->line("👥 <fg=blue>Total Eligible Users:</> " . number_format($stats['total_users_eligible']));
        $this->line("📧 <fg=blue>Last Week Sent:</> " . number_format($stats['last_week_sent']));
        $this->line("📈 <fg=blue>Total Digests Sent:</> " . number_format($stats['total_digests_sent']));
        $this->line("📊 <fg=blue>Average Weekly Sends:</> " . number_format($stats['average_weekly_sends'], 1));
        
        $engagementColor = $stats['engagement_rate'] >= 50 ? 'green' : 
                          ($stats['engagement_rate'] >= 25 ? 'yellow' : 'red');
        $this->line("📖 <fg=blue>Engagement Rate:</> <fg={$engagementColor}>{$stats['engagement_rate']}%</>");
    }

    /**
     * Perform dry run
     */
    private function dryRun(): int
    {
        try {
            $this->info('🔍 Dry Run Mode - No digests will be sent');
            $this->newLine();

            // Get users who would receive digest
            $users = \App\Models\User::where('is_active', true)
                ->where('email_notifications_enabled', true)
                ->whereJsonContains('notification_preferences->weekly_digest', true)
                ->orWhereNull('notification_preferences')
                ->get();

            $this->line("👥 <fg=blue>Users who would receive digest:</> {$users->count()}");
            $this->newLine();

            if ($users->count() > 0) {
                $this->line("📋 <fg=blue>Sample users:</>");
                
                $sampleUsers = $users->take(10);
                foreach ($sampleUsers as $user) {
                    $this->line("  • {$user->name} ({$user->email})");
                }

                if ($users->count() > 10) {
                    $remaining = $users->count() - 10;
                    $this->line("  ... and {$remaining} more users");
                }
            }

            $this->newLine();
            $this->info('✅ Dry run completed');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Dry run failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Send weekly digests
     */
    private function sendDigests(): int
    {
        try {
            $this->info('📧 Sending weekly digests...');
            $this->newLine();

            $startTime = now();
            $results = WeeklyDigestService::sendWeeklyDigests();

            if ($this->option('json')) {
                $this->line(json_encode($results, JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            $this->displayResults($results, $startTime);

            if ($results['error_count'] > 0) {
                $this->displayErrors($results['errors']);
                return Command::FAILURE;
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to send weekly digests: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display sending results
     */
    private function displayResults(array $results, $startTime): void
    {
        $duration = now()->diffInSeconds($startTime);
        
        $this->info('📊 Weekly Digest Results');
        $this->newLine();

        $this->line("👥 <fg=blue>Total Users:</> " . number_format($results['total_users']));
        $this->line("✅ <fg=blue>Sent Successfully:</> <fg=green>" . number_format($results['sent_count']) . "</>");
        $this->line("⏭️ <fg=blue>Skipped (No Content):</> <fg=yellow>" . number_format($results['skipped_count']) . "</>");
        $this->line("❌ <fg=blue>Errors:</> <fg=red>" . number_format($results['error_count']) . "</>");
        $this->line("⏱️ <fg=blue>Duration:</> {$duration} seconds");

        if ($results['sent_count'] > 0) {
            $avgTime = round($duration / $results['total_users'], 2);
            $this->line("📈 <fg=blue>Average Time per User:</> {$avgTime} seconds");
        }

        $this->newLine();

        // Success rate
        $successRate = $results['total_users'] > 0 ? 
            round(($results['sent_count'] / $results['total_users']) * 100, 1) : 0;
        
        $successColor = $successRate >= 90 ? 'green' : 
                       ($successRate >= 70 ? 'yellow' : 'red');
        
        $this->line("🎯 <fg=blue>Success Rate:</> <fg={$successColor}>{$successRate}%</>");

        if ($results['sent_count'] > 0) {
            $this->info("✅ Weekly digest sending completed successfully!");
        } elseif ($results['skipped_count'] > 0) {
            $this->warn("⚠️ All digests were skipped (no content to send)");
        } else {
            $this->error("❌ No digests were sent");
        }
    }

    /**
     * Display errors
     */
    private function displayErrors(array $errors): void
    {
        if (empty($errors)) {
            return;
        }

        $this->newLine();
        $this->error('❌ Errors encountered:');
        $this->newLine();

        foreach ($errors as $error) {
            if (isset($error['user_id'])) {
                $this->line("  • User ID {$error['user_id']}: {$error['error']}");
            } else {
                $this->line("  • {$error['error']}");
            }
        }
    }

    /**
     * Get command help
     */
    public function getHelp(): string
    {
        return "
📧 Weekly Digest Manager

This command manages the weekly digest notification system for MechaMap users.

USAGE:
  php artisan digest:weekly [options]

OPTIONS:
  --dry-run         Show what would be sent without actually sending
  --user=ID         Send digest to specific user ID only
  --force           Force send even if already sent this week
  --stats           Show digest statistics
  --json            Output in JSON format

EXAMPLES:
  php artisan digest:weekly --stats
    Show current digest statistics

  php artisan digest:weekly --dry-run
    Preview who would receive digests without sending

  php artisan digest:weekly
    Send weekly digests to all eligible users

  php artisan digest:weekly --user=123
    Send digest to specific user only

  php artisan digest:weekly --json
    Output results in JSON format

SCHEDULING:
  Add to app/Console/Kernel.php:
  \$schedule->command('digest:weekly')->weekly()->mondays()->at('09:00');

FEATURES:
  • Personalized content based on user activity
  • Activity summaries and statistics
  • New followers and achievements
  • Trending topics and popular content
  • Community statistics
  • Smart content filtering (only send if there's content)
  • Comprehensive error handling and logging
  • Performance monitoring and statistics

The digest includes:
  - User's weekly activity summary
  - New threads from followed users
  - Popular threads in the community
  - New followers gained
  - Achievements unlocked
  - Trending topics and forums
  - Community statistics
  - Personalized content recommendations
";
    }
}
