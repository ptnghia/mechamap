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
        Schema::create('business_verification_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('application_type', ['manufacturer', 'supplier', 'brand', 'verified_partner']);
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'requires_additional_info'
            ])->default('pending');

            // Business Information
            $table->string('business_name');
            $table->string('business_type', 100);
            $table->string('tax_id', 50);
            $table->string('registration_number', 100)->nullable();
            $table->text('business_address');
            $table->string('business_phone', 20)->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_website')->nullable();

            // Additional Business Details
            $table->text('business_description')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->integer('employee_count')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->json('business_categories')->nullable(); // Array of business categories
            $table->json('service_areas')->nullable(); // Geographic service areas

            // Verification Timeline
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Admin Actions
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');

            // Decision Details
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('additional_info_requested')->nullable();

            // Verification Metadata
            $table->integer('verification_score')->default(0);
            $table->enum('priority_level', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->integer('estimated_review_time')->default(72); // hours
            $table->boolean('is_expedited')->default(false);
            $table->decimal('application_fee', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'waived', 'refunded'])->default('pending');

            // Communication
            $table->json('communication_preferences')->nullable();
            $table->string('preferred_language', 5)->default('vi');
            $table->boolean('sms_notifications_enabled')->default(false);
            $table->boolean('email_notifications_enabled')->default(true);

            // Internal Notes
            $table->text('internal_notes')->nullable();
            $table->json('reviewer_checklist')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->integer('revision_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['application_type', 'status']);
            $table->index(['submitted_at', 'status']);
            $table->index(['priority_level', 'status']);
            $table->index(['reviewed_by', 'status']);
            $table->index('verification_score');
            $table->index('deadline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_verification_applications');
    }
};
