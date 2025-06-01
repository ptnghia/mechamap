<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo permissions
        $permissions = [
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'ban_users',

            // Thread management
            'view_threads',
            'create_threads',
            'edit_threads',
            'delete_threads',
            'pin_threads',
            'lock_threads',

            // Comment management
            'view_comments',
            'create_comments',
            'edit_comments',
            'delete_comments',

            // Showcase management
            'view_showcases',
            'create_showcases',
            'edit_showcases',
            'delete_showcases',

            // Report management
            'view_reports',
            'handle_reports',
            'dismiss_reports',

            // Settings management
            'view_settings',
            'edit_settings_general',
            'edit_settings_forum',
            'edit_settings_user',
            'edit_settings_email',
            'edit_settings_security',
            'edit_settings_seo',
            'edit_settings_api',

            // Page management
            'view_pages',
            'create_pages',
            'edit_pages',
            'delete_pages',

            // Media management
            'view_media',
            'upload_media',
            'delete_media',

            // FAQ management
            'view_faqs',
            'create_faqs',
            'edit_faqs',
            'delete_faqs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Tạo roles
        $adminRole = Role::create(['name' => 'admin']);
        $moderatorRole = Role::create(['name' => 'moderator']);
        $seniorRole = Role::create(['name' => 'senior']);
        $memberRole = Role::create(['name' => 'member']);
        $guestRole = Role::create(['name' => 'guest']);

        // Gán permissions cho roles

        // Admin có tất cả quyền
        $adminRole->givePermissionTo(Permission::all());

        // Moderator có quyền quản lý nội dung
        $moderatorRole->givePermissionTo([
            'view_users',
            'edit_users',
            'ban_users',
            'view_threads',
            'edit_threads',
            'delete_threads',
            'pin_threads',
            'lock_threads',
            'view_comments',
            'edit_comments',
            'delete_comments',
            'view_showcases',
            'edit_showcases',
            'delete_showcases',
            'view_reports',
            'handle_reports',
            'dismiss_reports',
            'view_settings',
            'view_media',
            'upload_media',
            'view_faqs',
            'edit_faqs',
        ]);

        // Senior có quyền tạo nội dung nâng cao
        $seniorRole->givePermissionTo([
            'view_threads',
            'create_threads',
            'edit_threads', // chỉ threads của mình
            'view_comments',
            'create_comments',
            'edit_comments', // chỉ comments của mình
            'view_showcases',
            'create_showcases',
            'edit_showcases', // chỉ showcases của mình
            'view_media',
            'upload_media',
        ]);

        // Member có quyền cơ bản
        $memberRole->givePermissionTo([
            'view_threads',
            'create_threads',
            'view_comments',
            'create_comments',
            'view_showcases',
            'create_showcases',
            'view_media',
            'upload_media',
        ]);

        // Guest chỉ có quyền xem
        $guestRole->givePermissionTo([
            'view_threads',
            'view_comments',
            'view_showcases',
            'view_media',
        ]);

        // Gán role cho users hiện có (nếu có)
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $user) {
            $user->assignRole('admin');
        }

        $moderatorUsers = User::where('role', 'moderator')->get();
        foreach ($moderatorUsers as $user) {
            $user->assignRole('moderator');
        }

        $seniorUsers = User::where('role', 'senior')->get();
        foreach ($seniorUsers as $user) {
            $user->assignRole('senior');
        }

        $memberUsers = User::where('role', 'member')->get();
        foreach ($memberUsers as $user) {
            $user->assignRole('member');
        }
    }
}
