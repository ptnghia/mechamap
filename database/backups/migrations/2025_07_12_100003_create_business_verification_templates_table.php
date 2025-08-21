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
        Schema::create('business_verification_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('template_type', [
                'application_submitted_email',
                'application_approved_email',
                'application_rejected_email',
                'additional_info_requested_email',
                'document_verified_email',
                'document_rejected_email',
                'reviewer_assigned_email',
                'deadline_reminder_email',
                'payment_reminder_email',
                'welcome_verified_partner_email',
                'sms_notification',
                'in_app_notification',
                'admin_notification_email',
                'daily_digest_email'
            ]);

            $table->enum('business_type', [
                'manufacturer',
                'supplier',
                'brand',
                'verified_partner',
                'all'
            ])->default('all');

            // Template Content
            $table->string('template_name');
            $table->string('subject');
            $table->text('content');
            $table->text('content_html')->nullable(); // HTML version
            $table->text('content_sms')->nullable(); // SMS version (short)

            // Template Variables
            $table->json('available_variables')->nullable(); // List of available placeholders
            $table->json('required_variables')->nullable(); // Required placeholders
            $table->text('variable_descriptions')->nullable(); // Help text for variables

            // Localization
            $table->string('language', 5)->default('vi');
            $table->string('country_code', 2)->nullable();
            $table->json('translations')->nullable(); // Multi-language support

            // Template Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false); // System vs custom template
            $table->integer('priority')->default(0); // Template priority

            // Delivery Settings
            $table->boolean('send_immediately')->default(true);
            $table->integer('delay_minutes')->default(0);
            $table->json('delivery_conditions')->nullable(); // Conditions for sending
            $table->boolean('requires_approval')->default(false);

            // Design & Formatting
            $table->string('email_layout')->default('default');
            $table->json('styling_options')->nullable(); // Colors, fonts, etc.
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('reply_to_email')->nullable();

            // Analytics & Tracking
            $table->boolean('track_opens')->default(true);
            $table->boolean('track_clicks')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();

            // Version Control
            $table->integer('version')->default(1);
            $table->foreignId('parent_template_id')->nullable()->constrained('business_verification_templates')->onDelete('set null');
            $table->text('version_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            // Compliance
            $table->boolean('gdpr_compliant')->default(true);
            $table->boolean('contains_sensitive_data')->default(false);
            $table->json('compliance_notes')->nullable();

            $table->timestamps();

            // Indexes (shortened names)
            $table->index(['template_type', 'business_type'], 'bvt_type_business_idx');
            $table->index(['language', 'is_active'], 'bvt_lang_active_idx');
            $table->index(['is_active', 'is_default'], 'bvt_active_default_idx');
            $table->index(['template_type', 'is_active', 'priority'], 'bvt_type_active_priority_idx');
            $table->index('parent_template_id', 'bvt_parent_idx');
            $table->index('last_used_at', 'bvt_last_used_idx');
            $table->index(['created_by', 'updated_by'], 'bvt_creators_idx');

            // Unique constraints
            $table->unique(['template_type', 'business_type', 'language', 'is_default'], 'bvt_unique_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_verification_templates');
    }
};
