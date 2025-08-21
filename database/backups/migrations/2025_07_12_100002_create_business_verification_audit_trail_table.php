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
        Schema::create('business_verification_audit_trail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('business_verification_applications')->onDelete('cascade');
            $table->enum('action_type', [
                'application_submitted',
                'application_updated',
                'document_uploaded',
                'document_verified',
                'document_rejected',
                'application_reviewed',
                'application_approved',
                'application_rejected',
                'additional_info_requested',
                'status_changed',
                'role_upgraded',
                'reviewer_assigned',
                'priority_changed',
                'deadline_set',
                'payment_processed',
                'communication_sent',
                'internal_note_added',
                'system_action'
            ]);

            // Action Details
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->text('action_description');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();

            // Related Records
            $table->foreignId('document_id')->nullable()->constrained('business_verification_documents')->onDelete('set null');
            $table->string('related_model_type')->nullable(); // Polymorphic relation
            $table->unsignedBigInteger('related_model_id')->nullable();

            // Additional Context
            $table->json('metadata')->nullable(); // Flexible data storage
            $table->json('changes')->nullable(); // Before/after values
            $table->text('reason')->nullable(); // Reason for action
            $table->text('notes')->nullable(); // Additional notes

            // Request Information
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_id')->nullable(); // For request tracing

            // System Information
            $table->string('application_version')->nullable();
            $table->string('environment')->nullable(); // production, staging, etc.
            $table->boolean('is_automated')->default(false); // System vs manual action
            $table->string('automation_source')->nullable(); // Cron, queue, etc.

            // Compliance & Legal
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('requires_retention')->default(true);
            $table->date('retention_until')->nullable();
            $table->json('compliance_tags')->nullable();

            // Performance Tracking
            $table->integer('processing_time_ms')->nullable(); // Action duration
            $table->string('batch_id')->nullable(); // For bulk operations
            $table->integer('sequence_number')->nullable(); // Order in batch

            $table->timestamp('created_at')->useCurrent();

            // Indexes for performance and querying (shortened names)
            $table->index(['application_id', 'created_at'], 'bvat_app_created_idx');
            $table->index(['action_type', 'created_at'], 'bvat_action_created_idx');
            $table->index(['performed_by', 'created_at'], 'bvat_user_created_idx');
            $table->index(['document_id', 'action_type'], 'bvat_doc_action_idx');
            $table->index(['old_status', 'new_status'], 'bvat_status_change_idx');
            $table->index(['is_automated', 'action_type'], 'bvat_auto_action_idx');
            $table->index(['batch_id', 'sequence_number'], 'bvat_batch_seq_idx');
            $table->index('request_id', 'bvat_request_idx');
            $table->index('session_id', 'bvat_session_idx');
            $table->index('retention_until', 'bvat_retention_idx');

            // Composite indexes for common queries
            $table->index(['application_id', 'action_type', 'created_at'], 'bvat_app_action_created_idx');
            $table->index(['performed_by', 'action_type', 'created_at'], 'bvat_user_action_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_verification_audit_trail');
    }
};
