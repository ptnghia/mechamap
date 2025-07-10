<?php

namespace App\Console\Commands;

use App\Services\TypingIndicatorService;
use Illuminate\Console\Command;

class CleanupTypingIndicators extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'typing:cleanup 
                            {--stats : Show typing statistics}
                            {--auto-stop : Auto-stop inactive typing indicators}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up expired typing indicators and manage typing system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('⌨️ Typing Indicators Cleanup Manager');
        $this->newLine();

        if ($this->option('stats')) {
            return $this->showStatistics();
        }

        if ($this->option('auto-stop')) {
            return $this->autoStopInactive();
        }

        // Default: cleanup expired indicators
        return $this->cleanupExpired();
    }

    /**
     * Show typing statistics
     */
    private function showStatistics(): int
    {
        try {
            $statistics = TypingIndicatorService::getTypingStatistics();

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
     * Display typing statistics
     */
    private function displayStatistics(array $stats): void
    {
        $this->info('📊 Typing Indicators Statistics');
        $this->newLine();

        $this->line("⌨️ <fg=blue>Total Active Indicators:</> " . number_format($stats['total_active']));
        
        if (!empty($stats['by_context_type'])) {
            $this->line("📋 <fg=blue>By Context Type:</>");
            foreach ($stats['by_context_type'] as $type => $count) {
                $this->line("  • {$type}: {$count}");
            }
        }

        if (!empty($stats['by_typing_type'])) {
            $this->line("⌨️ <fg=blue>By Typing Type:</>");
            foreach ($stats['by_typing_type'] as $type => $count) {
                $this->line("  • {$type}: {$count}");
            }
        }

        $avgDuration = round($stats['average_duration'], 2);
        $this->line("⏱️ <fg=blue>Average Duration:</> {$avgDuration} seconds");

        if ($stats['total_active'] === 0) {
            $this->info('✨ No active typing indicators found');
        }
    }

    /**
     * Clean up expired indicators
     */
    private function cleanupExpired(): int
    {
        try {
            $this->info('🧹 Cleaning up expired typing indicators...');
            
            $deleted = TypingIndicatorService::cleanupExpired();

            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => true,
                    'deleted_count' => $deleted,
                ], JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            if ($deleted > 0) {
                $this->info("✅ Cleaned up {$deleted} expired typing indicators");
            } else {
                $this->info("ℹ️ No expired typing indicators found");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Auto-stop inactive typing indicators
     */
    private function autoStopInactive(): int
    {
        try {
            $this->info('🔄 Auto-stopping inactive typing indicators...');
            
            $stopped = TypingIndicatorService::autoStopInactiveTyping();

            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => true,
                    'stopped_count' => $stopped,
                ], JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            if ($stopped > 0) {
                $this->info("✅ Auto-stopped {$stopped} inactive typing indicators");
            } else {
                $this->info("ℹ️ No inactive typing indicators found");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Auto-stop failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get command help
     */
    public function getHelp(): string
    {
        return "
⌨️ Typing Indicators Cleanup Manager

This command manages the typing indicators system for MechaMap.

USAGE:
  php artisan typing:cleanup [options]

OPTIONS:
  --stats           Show current typing statistics
  --auto-stop       Auto-stop inactive typing indicators
  --json            Output in JSON format

EXAMPLES:
  php artisan typing:cleanup --stats
    Show current typing indicators statistics

  php artisan typing:cleanup
    Clean up expired typing indicators

  php artisan typing:cleanup --auto-stop
    Auto-stop inactive typing indicators (inactive for 2+ minutes)

  php artisan typing:cleanup --json
    Output results in JSON format

SCHEDULING:
  Add to app/Console/Kernel.php:
  \$schedule->command('typing:cleanup')->everyMinute();
  \$schedule->command('typing:cleanup --auto-stop')->everyFiveMinutes();

FEATURES:
  • Automatic cleanup of expired typing indicators
  • Auto-stop inactive typing indicators
  • Real-time statistics and monitoring
  • Broadcasting support for real-time updates
  • Performance optimized with caching
  • Comprehensive error handling and logging

The typing indicators system provides:
  - Real-time typing status for threads, comments, messages
  - Automatic expiration and cleanup
  - Broadcasting for live updates
  - Performance monitoring and statistics
  - Bulk operations support
  - Heartbeat mechanism for keeping indicators alive
";
    }
}
