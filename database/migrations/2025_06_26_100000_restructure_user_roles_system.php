<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 🎯 MechaMap User Management Restructure - Phase 1
     * Cập nhật hệ thống roles từ 8 roles cũ sang 12 roles mới theo 4 nhóm
     */
    public function up(): void
    {
        // Tạo bảng mapping roles cũ sang roles mới
        $roleMapping = [
            // Roles cũ => Roles mới
            'admin' => 'super_admin',
            'moderator' => 'content_moderator',
            'senior' => 'senior_member',
            'member' => 'member',
            'guest' => 'guest',
            'supplier' => 'supplier',
            'manufacturer' => 'manufacturer',
            'brand' => 'brand',
        ];

        // Cập nhật role column trong users table để hỗ trợ roles mới
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'member'");

        // Cập nhật existing users với roles mới
        foreach ($roleMapping as $oldRole => $newRole) {
            DB::table('users')
                ->where('role', $oldRole)
                ->update(['role' => $newRole]);
        }

        // Thêm index cho role column để tối ưu performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_index');
        });

        // Thêm các columns mới cho business roles nếu chưa có
        if (!Schema::hasColumn('users', 'role_group')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role_group', [
                    'system_management',
                    'community_management', 
                    'community_members',
                    'business_partners'
                ])->nullable()->after('role')
                ->comment('Nhóm phân quyền chính');

                $table->json('role_permissions')->nullable()->after('role_group')
                    ->comment('Cache permissions cho role');

                $table->timestamp('role_updated_at')->nullable()->after('role_permissions')
                    ->comment('Thời gian cập nhật role gần nhất');
            });
        }

        // Cập nhật role_group cho existing users
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

        foreach ($roleGroups as $role => $group) {
            DB::table('users')
                ->where('role', $role)
                ->update([
                    'role_group' => $group,
                    'role_updated_at' => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục roles cũ
        $reverseMapping = [
            'super_admin' => 'admin',
            'system_admin' => 'admin',
            'content_admin' => 'admin',
            'content_moderator' => 'moderator',
            'marketplace_moderator' => 'moderator',
            'community_moderator' => 'moderator',
            'senior_member' => 'senior',
            'member' => 'member',
            'guest' => 'guest',
            'student' => 'member',
            'manufacturer' => 'manufacturer',
            'supplier' => 'supplier',
            'brand' => 'brand',
            'verified_partner' => 'supplier',
        ];

        foreach ($reverseMapping as $newRole => $oldRole) {
            DB::table('users')
                ->where('role', $newRole)
                ->update(['role' => $oldRole]);
        }

        // Xóa các columns mới
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropColumn([
                'role_group',
                'role_permissions',
                'role_updated_at'
            ]);
        });

        // Khôi phục ENUM cũ
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','moderator','senior','member','guest','supplier','manufacturer','brand') DEFAULT 'member'");
    }
};
