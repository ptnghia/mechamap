<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 🎯 Task 1.3: Thêm các fields còn thiếu cho Registration Wizard
     * Bổ sung verification fields cần thiết cho multi-step registration
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kiểm tra và thêm các fields còn thiếu cho verification system
            
            // General verification timestamp (khác với business_verified_at)
            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('business_verified_at')
                    ->comment('Thời gian xác minh tài khoản (general verification)');
            }
            
            // Verification notes từ admin
            if (!Schema::hasColumn('users', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('verified_at')
                    ->comment('Ghi chú xác minh từ admin');
            }
            
            // JSON field để lưu thông tin documents uploaded
            if (!Schema::hasColumn('users', 'verification_documents')) {
                $table->json('verification_documents')->nullable()->after('verification_notes')
                    ->comment('Thông tin tài liệu xác minh đã upload (JSON)');
            }
            
            // Thêm index cho performance
            if (!Schema::hasIndex('users', 'users_verified_at_index')) {
                $table->index('verified_at', 'users_verified_at_index');
            }
            
            if (!Schema::hasIndex('users', 'users_business_verification_index')) {
                $table->index(['is_verified_business', 'business_verified_at'], 'users_business_verification_index');
            }
        });
        
        // Cập nhật existing business users với default values nếu cần
        DB::statement("
            UPDATE users 
            SET verification_documents = JSON_ARRAY()
            WHERE role IN ('manufacturer', 'supplier', 'brand') 
            AND verification_documents IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            if (Schema::hasIndex('users', 'users_verified_at_index')) {
                $table->dropIndex('users_verified_at_index');
            }
            
            if (Schema::hasIndex('users', 'users_business_verification_index')) {
                $table->dropIndex('users_business_verification_index');
            }
            
            // Drop columns
            if (Schema::hasColumn('users', 'verification_documents')) {
                $table->dropColumn('verification_documents');
            }
            
            if (Schema::hasColumn('users', 'verification_notes')) {
                $table->dropColumn('verification_notes');
            }
            
            if (Schema::hasColumn('users', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
