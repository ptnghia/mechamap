<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 🎯 Task 1.3: Tạo table riêng cho user verification documents
     * Hỗ trợ upload và quản lý documents cho business verification
     */
    public function up(): void
    {
        // Kiểm tra xem table đã tồn tại chưa
        if (!Schema::hasTable('user_verification_documents')) {
            Schema::create('user_verification_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade')
                    ->comment('ID của user sở hữu document');
                
                // Document information
                $table->string('document_type', 50)->comment('Loại tài liệu: business_license, tax_certificate, etc.');
                $table->string('original_name')->comment('Tên file gốc');
                $table->string('file_path')->comment('Đường dẫn file trong storage');
                $table->string('file_hash', 64)->nullable()->comment('SHA256 hash của file');
                $table->unsignedInteger('file_size')->comment('Kích thước file (bytes)');
                $table->string('mime_type', 100)->comment('MIME type của file');
                
                // Verification status
                $table->enum('verification_status', ['pending', 'approved', 'rejected', 'expired'])
                    ->default('pending')->comment('Trạng thái xác minh document');
                $table->text('verification_notes')->nullable()->comment('Ghi chú từ admin về document');
                $table->timestamp('verified_at')->nullable()->comment('Thời gian xác minh document');
                $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')
                    ->comment('Admin xác minh document');
                
                // Metadata
                $table->json('metadata')->nullable()->comment('Metadata bổ sung (OCR data, extracted info, etc.)');
                $table->timestamp('expires_at')->nullable()->comment('Thời gian hết hạn document');
                $table->boolean('is_primary')->default(false)->comment('Document chính cho loại này');
                
                $table->timestamps();
                
                // Indexes for performance
                $table->index(['user_id', 'document_type'], 'user_documents_type_index');
                $table->index(['verification_status', 'created_at'], 'documents_verification_queue_index');
                $table->index(['user_id', 'is_primary'], 'user_primary_documents_index');
                $table->index('file_hash', 'documents_hash_index');
                $table->index(['expires_at', 'verification_status'], 'documents_expiry_index');
                
                // Unique constraint: one primary document per type per user
                $table->unique(['user_id', 'document_type', 'is_primary'], 'user_document_primary_unique');
            });
        }
        
        // Tạo table để track document access/downloads
        if (!Schema::hasTable('user_document_access_logs')) {
            Schema::create('user_document_access_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained('user_verification_documents')->onDelete('cascade');
                $table->foreignId('accessed_by')->constrained('users')->onDelete('cascade')
                    ->comment('User đã truy cập document');
                $table->enum('access_type', ['view', 'download', 'verify', 'reject'])
                    ->comment('Loại truy cập');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('access_metadata')->nullable()->comment('Metadata về access');
                $table->timestamp('accessed_at')->useCurrent();
                
                $table->index(['document_id', 'accessed_at'], 'document_access_history_index');
                $table->index(['accessed_by', 'access_type'], 'user_access_type_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_document_access_logs');
        Schema::dropIfExists('user_verification_documents');
    }
};
