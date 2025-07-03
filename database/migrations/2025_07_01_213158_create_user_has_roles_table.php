<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 👤 MechaMap User-Role Relationship
     * Bảng liên kết many-to-many giữa users và roles
     */
    public function up(): void
    {
        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID của user');
            $table->unsignedBigInteger('role_id')->comment('ID của role');

            // Assignment metadata
            $table->boolean('is_primary')->default(false)->comment('Role chính của user');
            $table->timestamp('assigned_at')->nullable()->comment('Thời gian gán role');
            $table->timestamp('expires_at')->nullable()->comment('Thời gian hết hạn (null = vĩnh viễn)');

            // Assignment tracking
            $table->unsignedBigInteger('assigned_by')->nullable()->comment('Người gán role');
            $table->text('assignment_reason')->nullable()->comment('Lý do gán role');
            $table->json('assignment_conditions')->nullable()->comment('Điều kiện đặc biệt');

            // Status
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->timestamp('deactivated_at')->nullable()->comment('Thời gian vô hiệu hóa');
            $table->unsignedBigInteger('deactivated_by')->nullable()->comment('Người vô hiệu hóa');

            $table->timestamps();

            // Unique constraint
            $table->unique(['user_id', 'role_id'], 'user_role_unique');

            // Indexes
            $table->index('user_id');
            $table->index('role_id');
            $table->index(['user_id', 'is_primary']);
            $table->index(['is_active', 'expires_at']);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deactivated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_roles');
    }
};
