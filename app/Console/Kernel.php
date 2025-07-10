<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate sitemap weekly
        $schedule->command('sitemap:generate')->weekly();

        // Clean up old database records
        $schedule->command('queue:prune-batches --hours=48')->daily();
        $schedule->command('queue:prune-failed --hours=720')->weekly();

        // Database maintenance
        $schedule->command('db:check')->weekly()->emailOutputTo(env('ADMIN_EMAIL'));

        // Cache maintenance
        $schedule->command('cache:prune-stale-tags')->hourly();

        // Backup database (if backup package is installed)
        // $schedule->command('backup:clean')->daily()->at('01:00');
        // $schedule->command('backup:run')->daily()->at('02:00');

        // WebSocket connection monitoring
        $schedule->command('websocket:monitor --health')->everyMinute();
        $schedule->command('websocket:monitor --cleanup')->everyFiveMinutes();
        $schedule->command('websocket:monitor --stats')->hourly();

        // Notification engagement cleanup
        $schedule->command('engagement:cleanup')->daily()->at('02:00');

        // Redis Cluster monitoring and maintenance
        $schedule->command('redis-cluster:monitor --health')->everyFiveMinutes();
        $schedule->command('redis-cluster:monitor --cleanup')->daily()->at('03:00');
        $schedule->command('redis-cluster:monitor --warmup')->daily()->at('04:00');

        // A/B Testing management
        $schedule->command('ab-test:manage --check')->hourly();

        // Notification delivery optimization
        $schedule->command('delivery:manage --optimize')->daily()->at('01:00');
        $schedule->command('delivery:manage --clear-cache')->daily()->at('05:00');
        $schedule->command('delivery:manage --reschedule')->everyThirtyMinutes();

        // Weekly digest notifications
        $schedule->command('digest:weekly')->weekly()->mondays()->at('09:00');

        // Typing indicators cleanup
        $schedule->command('typing:cleanup')->everyMinute();
        $schedule->command('typing:cleanup --auto-stop')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
