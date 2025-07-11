<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('business_verification_applications')->onDelete('cascade');
            $table->enum('document_type', [
                'business_license',
                'tax_certificate',
                'registration_certificate',
                'identity_document',
                'bank_statement',
                'utility_bill',
                'insurance_certificate',
                'quality_certificate',
                'trade_license',
                'vat_certificate',
                'other'
            ]);

            // File Information
            $table->string('document_name');
            $table->string('original_filename');
            $table->string('file_path', 500);
            $table->bigInteger('file_size');
            $table->string('mime_type', 100);
            $table->string('file_extension', 10);

            // Document Details
            $table->text('document_description')->nullable();
            $table->date('document_date')->nullable(); // Date on the document
            $table->date('expiry_date')->nullable(); // Document expiry date
            $table->string('issuing_authority')->nullable();
            $table->string('document_number')->nullable();

            // Verification Status
            $table->enum('verification_status', [
                'pending',
                'verified',
                'rejected',
                'requires_resubmission'
            ])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();

            // Security & Access Control
            $table->string('file_hash', 64);
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_key')->nullable();
            $table->integer('access_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->json('access_log')->nullable(); // Track who accessed when

            // File Processing
            $table->boolean('has_thumbnail')->default(false);
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->json('ocr_text')->nullable(); // Extracted text from OCR
            $table->json('metadata')->nullable(); // File metadata (EXIF, etc.)

            // Quality Control
            $table->integer('quality_score')->nullable(); // 0-100
            $table->boolean('is_legible')->default(true);
            $table->boolean('is_complete')->default(true);
            $table->boolean('is_authentic')->nullable();
            $table->text('quality_notes')->nullable();

            // Compliance & Legal
            $table->boolean('contains_sensitive_data')->default(true);
            $table->date('retention_until')->nullable(); // Data retention date
            $table->boolean('gdpr_compliant')->default(true);
            $table->json('compliance_flags')->nullable();

            $table->timestamps();

            // Indexes for performance (shortened names)
            $table->index(['application_id', 'document_type'], 'bvd_app_doc_type_idx');
            $table->index(['verification_status', 'verified_at'], 'bvd_status_verified_idx');
            $table->index(['document_type', 'verification_status'], 'bvd_type_status_idx');
            $table->index('file_hash', 'bvd_file_hash_idx');
            $table->index('expiry_date', 'bvd_expiry_date_idx');
            $table->index('verified_by', 'bvd_verified_by_idx');
            $table->index('last_accessed_at', 'bvd_last_accessed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_verification_documents');
    }
};
