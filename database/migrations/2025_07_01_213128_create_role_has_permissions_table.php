<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🔗 MechaMap Role-Permission Relationship
     * Bảng liên kết many-to-many giữa roles và permissions
     */
    public function up(): void
    {
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->comment('ID của role');
            $table->unsignedBigInteger('permission_id')->comment('ID của permission');

            // Metadata cho relationship
            $table->boolean('is_granted')->default(true)->comment('Cấp phép (true) hay từ chối (false)');
            $table->json('conditions')->nullable()->comment('Điều kiện áp dụng permission');
            $table->json('restrictions')->nullable()->comment('Giới hạn khi sử dụng permission');

            // Audit trail
            $table->unsignedBigInteger('granted_by')->nullable()->comment('Người cấp phép');
            $table->timestamp('granted_at')->nullable()->comment('Thời gian cấp phép');
            $table->text('grant_reason')->nullable()->comment('Lý do cấp phép');

            $table->timestamps();

            // Unique constraint
            $table->unique(['role_id', 'permission_id'], 'role_permission_unique');

            // Indexes
            $table->index('role_id');
            $table->index('permission_id');
            $table->index(['role_id', 'is_granted']);

            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('granted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
    }
};
