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
