<?php

namespace App\Console\Commands;

use App\Services\NotificationEngagementService;
use Illuminate\Console\Command;

class CleanupEngagementData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'engagement:cleanup 
                            {--days=30 : Number of days to keep engagement data}
                            {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old notification engagement data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info('🧹 Notification Engagement Data Cleanup');
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will be actually deleted');
            $this->newLine();
        }

        $this->info("Cleaning up engagement data older than {$days} days...");

        try {
            if (!$dryRun) {
                $cleanedUp = NotificationEngagementService::cleanupOldEngagementData();
                $this->info("✅ Successfully cleaned up {$cleanedUp} days of old engagement data");
            } else {
                $this->info("Would clean up engagement data older than {$days} days");
            }

            // Show current engagement statistics
            $this->showEngagementStatistics();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Show current engagement statistics
     */
    private function showEngagementStatistics(): void
    {
        $this->newLine();
        $this->info('📊 Current Engagement Statistics:');

        try {
            // Get today's engagement events
            $todayEvents = NotificationEngagementService::getEngagementEvents(now());
            $this->line("Today's events: " . count($todayEvents));

            // Get yesterday's engagement events
            $yesterdayEvents = NotificationEngagementService::getEngagementEvents(now()->subDay());
            $this->line("Yesterday's events: " . count($yesterdayEvents));

            // Get top performing notification types
            $topPerforming = NotificationEngagementService::getTopPerformingNotificationTypes(5);
            
            if (!empty($topPerforming)) {
                $this->newLine();
                $this->info('🏆 Top Performing Notification Types:');
                
                $headers = ['Type', 'Sent', 'Views', 'Clicks', 'Actions', 'Engagement Rate'];
                $rows = [];
                
                foreach ($topPerforming as $type) {
                    $rows[] = [
                        $type['type'],
                        number_format($type['total_sent']),
                        number_format($type['total_views']),
                        number_format($type['total_clicks']),
                        number_format($type['total_actions']),
                        $type['engagement_rate'] . '%',
                    ];
                }
                
                $this->table($headers, $rows);
            }

            // Get engagement summary for last 7 days
            $summary = NotificationEngagementService::getEngagementSummary(
                now()->subDays(7),
                now()
            );

            $this->newLine();
            $this->info('📈 Last 7 Days Summary:');
            $this->line("Total events: " . number_format($summary['total_events']));
            $this->line("Views: " . number_format($summary['events_by_type']['view']));
            $this->line("Clicks: " . number_format($summary['events_by_type']['click']));
            $this->line("Dismisses: " . number_format($summary['events_by_type']['dismiss']));
            $this->line("Actions: " . number_format($summary['events_by_type']['action']));

        } catch (\Exception $e) {
            $this->warn('Could not retrieve engagement statistics: ' . $e->getMessage());
        }
    }
}
