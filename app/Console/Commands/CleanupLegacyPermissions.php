<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CleanupLegacyPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:cleanup-legacy
                            {--dry-run : Run without making changes}
                            {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup legacy permissions data after migration to multiple roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting cleanup of legacy permissions data...');

        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('This will remove legacy permissions data. Continue?')) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->cleanupCachedPermissions($dryRun);

        $this->info('✅ Legacy permissions cleanup completed!');
        return 0;
    }

    /**
     * Cleanup cached permissions for users with multiple roles
     */
    private function cleanupCachedPermissions(bool $dryRun = false): void
    {
        $this->info('📊 Analyzing users with both roles and cached permissions...');

        // Find users with both multiple roles and cached permissions
        $usersToCleanup = User::whereNotNull('role_permissions')
            ->whereHas('roles')
            ->get();

        $this->info("Found {$usersToCleanup->count()} users with cached permissions and roles");

        if ($usersToCleanup->count() === 0) {
            $this->info('No users found with legacy cached permissions to cleanup.');
            return;
        }

        $progressBar = $this->output->createProgressBar($usersToCleanup->count());
        $progressBar->start();

        foreach ($usersToCleanup as $user) {
            if ($dryRun) {
                $this->line("Would refresh permissions for user {$user->id} ({$user->email})");
            } else {
                try {
                    // Refresh permissions from roles
                    $user->refreshPermissions();
                    $this->line("✅ Refreshed permissions for user {$user->id} ({$user->email})");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to refresh permissions for user {$user->id}: " . $e->getMessage());
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }
}
