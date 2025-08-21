<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UpdateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Cập nhật và gán Spatie roles cho tất cả users hiện tại
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu cập nhật roles cho tất cả users...');

        // Mapping role groups
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

        $users = User::all();
        $updated = 0;
        $assigned = 0;

        foreach ($users as $user) {
            try {
                // Update role_group if not set
                if (!$user->role_group && isset($roleGroups[$user->role])) {
                    $user->update([
                        'role_group' => $roleGroups[$user->role],
                        'role_updated_at' => now()
                    ]);
                    $updated++;
                }

                // Assign Spatie role if not already assigned
                if (Role::where('name', $user->role)->exists()) {
                    if (!$user->hasRole($user->role)) {
                        $user->assignRole($user->role);
                        $assigned++;
                        $this->command->info("✅ Assigned role '{$user->role}' to user: {$user->name}");
                    }
                } else {
                    $this->command->warn("⚠️ Role '{$user->role}' not found for user: {$user->name}");
                }

            } catch (\Exception $e) {
                $this->command->error("❌ Failed to update user {$user->name}: " . $e->getMessage());
            }
        }

        $this->command->info("✅ Hoàn thành cập nhật roles!");
        $this->command->info("📊 Thống kê:");
        $this->command->info("   - Tổng users: " . $users->count());
        $this->command->info("   - Updated role_group: {$updated}");
        $this->command->info("   - Assigned Spatie roles: {$assigned}");
    }
}
