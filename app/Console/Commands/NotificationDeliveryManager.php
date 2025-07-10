<?php

namespace App\Console\Commands;

use App\Services\NotificationDeliveryOptimizationService;
use App\Services\NotificationDeliverySchedulerService;
use App\Models\User;
use Illuminate\Console\Command;

class NotificationDeliveryManager extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'delivery:manage 
                            {--stats : Show delivery statistics}
                            {--optimize : Run delivery optimization}
                            {--clear-cache : Clear delivery cache}
                            {--reschedule : Reschedule failed deliveries}
                            {--user= : Target specific user ID}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Manage notification delivery optimization';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Notification Delivery Manager');
        $this->newLine();

        if ($this->option('stats')) {
            return $this->showStatistics();
        }

        if ($this->option('optimize')) {
            return $this->runOptimization();
        }

        if ($this->option('clear-cache')) {
            return $this->clearCache();
        }

        if ($this->option('reschedule')) {
            return $this->rescheduleFailedDeliveries();
        }

        // Default: show overview
        return $this->showOverview();
    }

    /**
     * Show delivery statistics
     */
    private function showStatistics(): int
    {
        try {
            $deliveryStats = NotificationDeliveryOptimizationService::getDeliveryStatistics();
            $queueStats = NotificationDeliverySchedulerService::getQueueStatistics();

            if ($this->option('json')) {
                $this->line(json_encode([
                    'delivery' => $deliveryStats,
                    'queue' => $queueStats,
                ], JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            $this->displayDeliveryStatistics($deliveryStats);
            $this->newLine();
            $this->displayQueueStatistics($queueStats);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to get statistics: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display delivery statistics
     */
    private function displayDeliveryStatistics(array $stats): void
    {
        $this->info('📊 Delivery Optimization Statistics');
        $this->newLine();

        $this->line("🎯 <fg=blue>Total Optimizations:</> " . number_format($stats['total_optimizations']));
        $this->line("⏰ <fg=blue>Delayed Deliveries:</> " . number_format($stats['delayed_deliveries']));
        $this->line("📦 <fg=blue>Batched Notifications:</> " . number_format($stats['batched_notifications']));
        $this->line("🚫 <fg=blue>Frequency Limited:</> " . number_format($stats['frequency_limited']));
        
        $improvementColor = $stats['engagement_improvement'] >= 20 ? 'green' : 
                           ($stats['engagement_improvement'] >= 10 ? 'yellow' : 'red');
        $this->line("📈 <fg=blue>Engagement Improvement:</> <fg={$improvementColor}>{$stats['engagement_improvement']}%</>");
    }

    /**
     * Display queue statistics
     */
    private function displayQueueStatistics(array $stats): void
    {
        $this->info('⚡ Queue Statistics');
        $this->newLine();

        $this->line("⏳ <fg=blue>Pending Jobs:</> " . number_format($stats['pending_jobs']));
        $this->line("⏰ <fg=blue>Delayed Jobs:</> " . number_format($stats['delayed_jobs']));
        $this->line("❌ <fg=blue>Failed Jobs:</> " . number_format($stats['failed_jobs']));
        $this->line("✅ <fg=blue>Processed Jobs:</> " . number_format($stats['processed_jobs']));
        $this->line("⏱️ <fg=blue>Average Delay:</> {$stats['average_delay']} minutes");
    }

    /**
     * Run delivery optimization
     */
    private function runOptimization(): int
    {
        try {
            $this->info('🔧 Running delivery optimization...');
            
            $userId = $this->option('user');
            
            if ($userId) {
                $user = User::findOrFail($userId);
                $this->optimizeForUser($user);
            } else {
                $this->optimizeForAllUsers();
            }

            $this->info('✅ Delivery optimization completed');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Optimization failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Optimize for specific user
     */
    private function optimizeForUser(User $user): void
    {
        $this->line("🎯 Optimizing delivery for user: {$user->name} (ID: {$user->id})");
        
        // Clear user optimization cache to force recalculation
        NotificationDeliveryOptimizationService::clearUserOptimizationCache($user);
        
        // Get batching opportunities
        $batches = NotificationDeliveryOptimizationService::batchNotifications($user);
        
        if (!empty($batches)) {
            $this->line("📦 Found {count($batches)} batching opportunities");
            
            foreach ($batches as $batch) {
                $this->line("  • {$batch['type']}: {$batch['count']} notifications");
            }
        } else {
            $this->line("ℹ️ No batching opportunities found");
        }
    }

    /**
     * Optimize for all users
     */
    private function optimizeForAllUsers(): void
    {
        $this->line("🌐 Running global delivery optimization...");
        
        // Get active users from last 7 days
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(7))
            ->limit(100) // Process in batches
            ->get();

        $this->line("👥 Processing {$activeUsers->count()} active users");
        
        $progressBar = $this->output->createProgressBar($activeUsers->count());
        $progressBar->start();

        $optimized = 0;
        foreach ($activeUsers as $user) {
            try {
                $batches = NotificationDeliveryOptimizationService::batchNotifications($user);
                if (!empty($batches)) {
                    $optimized++;
                }
            } catch (\Exception $e) {
                // Continue with next user
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->line("✨ Optimized delivery for {$optimized} users");
    }

    /**
     * Clear delivery cache
     */
    private function clearCache(): int
    {
        try {
            $this->info('🧹 Clearing delivery cache...');
            
            $cleared = NotificationDeliverySchedulerService::clearDeliveryCache();
            
            $this->info("✅ Cleared {$cleared} cache entries");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Cache clearing failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Reschedule failed deliveries
     */
    private function rescheduleFailedDeliveries(): int
    {
        try {
            $this->info('🔄 Rescheduling failed deliveries...');
            
            $rescheduled = NotificationDeliverySchedulerService::rescheduleFailedDeliveries();
            
            if ($rescheduled > 0) {
                $this->info("✅ Rescheduled {$rescheduled} failed deliveries");
            } else {
                $this->info("ℹ️ No failed deliveries to reschedule");
            }
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Rescheduling failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Show overview
     */
    private function showOverview(): int
    {
        $this->info('📊 Delivery Optimization Overview');
        $this->newLine();

        // Show quick stats
        try {
            $deliveryStats = NotificationDeliveryOptimizationService::getDeliveryStatistics();
            $queueStats = NotificationDeliverySchedulerService::getQueueStatistics();

            $this->line("🎯 <fg=blue>Optimizations Today:</> " . number_format($deliveryStats['total_optimizations']));
            $this->line("⏳ <fg=blue>Pending Deliveries:</> " . number_format($queueStats['pending_jobs']));
            $this->line("📈 <fg=blue>Engagement Improvement:</> {$deliveryStats['engagement_improvement']}%");

        } catch (\Exception $e) {
            $this->warn('Could not retrieve statistics: ' . $e->getMessage());
        }

        $this->newLine();
        $this->line("💡 <fg=blue>Available Commands:</>");
        $this->line("  • <fg=cyan>--stats</> : Show detailed statistics");
        $this->line("  • <fg=cyan>--optimize</> : Run delivery optimization");
        $this->line("  • <fg=cyan>--clear-cache</> : Clear delivery cache");
        $this->line("  • <fg=cyan>--reschedule</> : Reschedule failed deliveries");
        $this->line("  • <fg=cyan>--user=ID</> : Target specific user");
        $this->line("  • <fg=cyan>--json</> : Output in JSON format");

        $this->newLine();
        $this->line("📋 <fg=blue>Optimization Features:</>");
        $this->line("  • ⏰ Optimal timing based on user engagement");
        $this->line("  • 📦 Notification batching to reduce fatigue");
        $this->line("  • 🚫 Frequency limiting to prevent spam");
        $this->line("  • 🌙 Quiet hours respect user preferences");
        $this->line("  • 📊 Real-time engagement tracking");

        return Command::SUCCESS;
    }
}
