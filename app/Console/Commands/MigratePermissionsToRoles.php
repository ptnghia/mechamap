<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class MigratePermissionsToRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:migrate-to-roles
                            {--dry-run : Run without making changes}
                            {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy permissions to multiple roles system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting migration from legacy permissions to multiple roles system...');

        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('This will migrate all legacy permissions to multiple roles. Continue?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->migrateUsersWithLegacyPermissions($dryRun);

        $this->info('✅ Migration completed successfully!');
        return 0;
    }

    /**
     * Migrate users with legacy permissions to multiple roles
     */
    private function migrateUsersWithLegacyPermissions(bool $dryRun = false): void
    {
        $this->info('📊 Analyzing users with legacy permissions...');

        // Find users with cached permissions but no roles
        $usersWithLegacyPermissions = User::whereNotNull('role_permissions')
            ->where(function($query) {
                $query->whereDoesntHave('roles')
                      ->orWhereHas('roles', function($q) {
                          $q->havingRaw('COUNT(*) = 0');
                      });
            })
            ->get();

        $this->info("Found {$usersWithLegacyPermissions->count()} users with legacy permissions");

        if ($usersWithLegacyPermissions->count() === 0) {
            $this->info('No users found with legacy permissions to migrate.');
            return;
        }

        $progressBar = $this->output->createProgressBar($usersWithLegacyPermissions->count());
        $progressBar->start();

        foreach ($usersWithLegacyPermissions as $user) {
            $this->migrateUserPermissions($user, $dryRun);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Migrate individual user permissions
     */
    private function migrateUserPermissions(User $user, bool $dryRun = false): void
    {
        if (!$user->role_permissions || !is_array($user->role_permissions)) {
            return;
        }

        $permissions = $user->role_permissions;
        $userRole = $user->role;

        // Determine appropriate role based on user's current role and permissions
        $targetRole = $this->determineTargetRole($userRole, $permissions);

        if (!$targetRole) {
            $this->warn("Could not determine target role for user {$user->id} ({$user->email})");
            return;
        }

        if ($dryRun) {
            $this->line("Would migrate user {$user->id} ({$user->email}) to role: {$targetRole->name}");
            return;
        }

        try {
            DB::beginTransaction();

            // Assign role to user
            $user->roles()->attach($targetRole->id, [
                'is_primary' => true,
                'assigned_by' => 1, // System migration
                'assigned_at' => now(),
                'assignment_reason' => 'Migrated from legacy permissions system',
                'is_active' => true,
            ]);

            // Update user's primary role info
            $user->update([
                'role_group' => $targetRole->role_group,
                'role_updated_at' => now(),
            ]);

            // Refresh permissions cache
            $user->refreshPermissions();

            DB::commit();

            $this->line("✅ Migrated user {$user->id} ({$user->email}) to role: {$targetRole->name}");

        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Failed to migrate user {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Determine target role based on user's current role and permissions
     */
    private function determineTargetRole(string $currentRole, array $permissions): ?Role
    {
        // Role mapping based on current role
        $roleMapping = [
            'super_admin' => 'super_admin',
            'system_admin' => 'system_admin',
            'admin' => 'content_admin',
            'moderator' => 'content_moderator',
        ];

        $targetRoleName = $roleMapping[$currentRole] ?? null;

        if (!$targetRoleName) {
            // Fallback: Determine by permissions
            if (in_array('manage_system', $permissions)) {
                $targetRoleName = 'system_admin';
            } elseif (in_array('manage_users', $permissions)) {
                $targetRoleName = 'content_admin';
            } elseif (in_array('moderate-content', $permissions)) {
                $targetRoleName = 'content_moderator';
            } else {
                $targetRoleName = 'content_moderator'; // Default fallback
            }
        }

        return Role::where('name', $targetRoleName)->first();
    }
}
