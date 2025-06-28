<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mechamap:assign-roles {--force : Force reassign all roles}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Assign Spatie roles to all MechaMap users based on their role column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Bắt đầu gán roles cho tất cả users...');

        $force = $this->option('force');
        $users = User::all();
        $assigned = 0;
        $skipped = 0;
        $errors = 0;

        // Role group mapping
        $roleGroups = [
            'super_admin' => 'system_management',
            'system_admin' => 'system_management',
            'content_admin' => 'system_management',
            'content_moderator' => 'community_management',
            'marketplace_moderator' => 'community_management',
            'community_moderator' => 'community_management',
            'senior_member' => 'community_members',
            'member' => 'community_members',
            'guest' => 'community_members',
            'student' => 'community_members',
            'manufacturer' => 'business_partners',
            'supplier' => 'business_partners',
            'brand' => 'business_partners',
            'verified_partner' => 'business_partners',
        ];

        $this->withProgressBar($users, function ($user) use (&$assigned, &$skipped, &$errors, $force, $roleGroups) {
            try {
                // Update role_group if needed
                if (!$user->role_group && isset($roleGroups[$user->role])) {
                    $user->update([
                        'role_group' => $roleGroups[$user->role],
                        'role_updated_at' => now()
                    ]);
                }

                // Check if role exists
                if (!Role::where('name', $user->role)->exists()) {
                    $this->newLine();
                    $this->warn("⚠️ Role '{$user->role}' không tồn tại cho user: {$user->name}");
                    $errors++;
                    return;
                }

                // Assign role if not already assigned or force
                if ($force || !$user->hasRole($user->role)) {
                    // Remove all existing roles if force
                    if ($force) {
                        $user->syncRoles([]);
                    }
                    
                    $user->assignRole($user->role);
                    $user->cachePermissions(); // Cache permissions
                    $assigned++;
                } else {
                    $skipped++;
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Lỗi khi gán role cho {$user->name}: " . $e->getMessage());
                $errors++;
            }
        });

        $this->newLine(2);
        $this->info('✅ Hoàn thành gán roles!');
        $this->table(['Thống kê', 'Số lượng'], [
            ['Tổng users', $users->count()],
            ['Đã gán roles', $assigned],
            ['Bỏ qua (đã có role)', $skipped],
            ['Lỗi', $errors],
        ]);

        // Verify results
        $usersWithRoles = User::whereHas('roles')->count();
        $this->info("📊 Users có Spatie roles: {$usersWithRoles}/{$users->count()}");

        return Command::SUCCESS;
    }
}
