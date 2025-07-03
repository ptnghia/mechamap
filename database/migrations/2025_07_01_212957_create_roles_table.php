<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 👑 MechaMap Roles System
     * Tạo bảng roles để quản lý vai trò người dùng
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Tên role (vd: super_admin)');
            $table->string('display_name')->comment('Tên hiển thị (vd: Super Admin)');
            $table->text('description')->nullable()->comment('Mô tả vai trò');

            // Role grouping theo MechaMap structure
            $table->enum('role_group', [
                'system_management',
                'community_management',
                'community_members',
                'business_partners'
            ])->comment('Nhóm vai trò chính');

            // Hierarchy và permissions
            $table->integer('hierarchy_level')->default(10)->comment('Cấp độ phân quyền (1=cao nhất)');
            $table->json('default_permissions')->nullable()->comment('Permissions mặc định cho role');
            $table->json('restricted_permissions')->nullable()->comment('Permissions bị cấm');

            // UI & Display
            $table->string('color', 20)->default('primary')->comment('Màu badge hiển thị');
            $table->string('icon', 50)->default('fas fa-user')->comment('Icon hiển thị');
            $table->boolean('is_visible')->default(true)->comment('Hiển thị trong danh sách');

            // System settings
            $table->boolean('is_system')->default(false)->comment('Role hệ thống không thể xóa');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->boolean('can_be_assigned')->default(true)->comment('Có thể gán cho user');

            // Business logic
            $table->integer('max_users')->nullable()->comment('Giới hạn số user (null = không giới hạn)');
            $table->json('business_rules')->nullable()->comment('Quy tắc kinh doanh đặc biệt');

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['role_group', 'hierarchy_level']);
            $table->index(['is_active', 'is_visible']);
            $table->index('hierarchy_level');

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
