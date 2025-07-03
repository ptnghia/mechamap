<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🔐 MechaMap Permissions System
     * Tạo bảng permissions để quản lý quyền hạn chi tiết
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Tên permission (vd: users.create)');
            $table->string('display_name')->comment('Tên hiển thị (vd: Tạo người dùng)');
            $table->text('description')->nullable()->comment('Mô tả chi tiết permission');

            // Phân nhóm permissions
            $table->string('category')->comment('Nhóm permission (vd: user_management)');
            $table->string('module')->comment('Module chính (vd: users, forums, marketplace)');
            $table->string('action')->comment('Hành động (vd: create, read, update, delete)');

            // Metadata
            $table->json('metadata')->nullable()->comment('Thông tin bổ sung (conditions, restrictions)');
            $table->boolean('is_system')->default(false)->comment('Permission hệ thống không thể xóa');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');

            // Hierarchy và dependencies
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Permission cha (hierarchy)');
            $table->json('dependencies')->nullable()->comment('Permissions phụ thuộc');

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['category', 'module']);
            $table->index(['is_active', 'is_system']);
            $table->index('parent_id');

            // Foreign keys
            $table->foreign('parent_id')->references('id')->on('permissions')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
