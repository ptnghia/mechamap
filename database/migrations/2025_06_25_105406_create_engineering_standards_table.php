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
        Schema::create('engineering_standards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('title');
            $table->string('standard_number')->unique(); // ISO 9001, ASME Y14.5, etc.
            $table->text('description')->nullable();
            $table->string('organization'); // ISO, ASME, ANSI, DIN, JIS, etc.
            $table->string('category'); // Quality, Design, Safety, Testing, etc.

            // Standard Details
            $table->string('version')->nullable(); // 2015, 2020, etc.
            $table->date('publication_date')->nullable();
            $table->date('revision_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->enum('status', ['current', 'superseded', 'withdrawn', 'under_review'])->default('current');

            // Scope & Application
            $table->text('scope')->nullable(); // What the standard covers
            $table->json('applicable_industries')->nullable(); // Industries that use this
            $table->json('applicable_processes')->nullable(); // Processes covered
            $table->json('applicable_materials')->nullable(); // Materials covered
            $table->json('product_types')->nullable(); // Product types covered

            // Technical Content
            $table->json('key_requirements')->nullable(); // Main requirements
            $table->json('specifications')->nullable(); // Technical specifications
            $table->json('test_methods')->nullable(); // Testing procedures
            $table->json('acceptance_criteria')->nullable(); // Pass/fail criteria
            $table->json('measurement_methods')->nullable(); // How to measure compliance

            // Compliance Information
            $table->enum('compliance_level', ['mandatory', 'recommended', 'optional'])->default('recommended');
            $table->json('regulatory_requirements')->nullable(); // Legal requirements
            $table->json('certification_bodies')->nullable(); // Who can certify
            $table->json('compliance_costs')->nullable(); // Cost estimates

            // Related Standards
            $table->json('supersedes')->nullable(); // Standards this replaces
            $table->json('superseded_by')->nullable(); // Standards that replace this
            $table->json('related_standards')->nullable(); // Related/complementary standards
            $table->json('referenced_standards')->nullable(); // Standards referenced within

            // Implementation
            $table->json('implementation_guidelines')->nullable(); // How to implement
            $table->json('common_interpretations')->nullable(); // Common understanding
            $table->json('implementation_challenges')->nullable(); // Known difficulties
            $table->json('best_practices')->nullable(); // Recommended practices

            // Documentation
            $table->string('document_path')->nullable(); // Path to standard document
            $table->json('summary_documents')->nullable(); // Summaries, guides
            $table->json('training_materials')->nullable(); // Educational content
            $table->json('case_studies')->nullable(); // Implementation examples

            // Geographic Scope
            $table->json('geographic_scope')->nullable(); // Countries/regions where applicable
            $table->json('national_adoptions')->nullable(); // National versions
            $table->json('regional_variations')->nullable(); // Regional differences

            // Industry Impact
            $table->text('business_impact')->nullable(); // Impact on business
            $table->json('affected_stakeholders')->nullable(); // Who is affected
            $table->json('implementation_timeline')->nullable(); // Typical implementation time

            // Updates & Changes
            $table->json('recent_changes')->nullable(); // Recent updates
            $table->json('planned_changes')->nullable(); // Future updates
            $table->text('change_rationale')->nullable(); // Why changes were made

            // Resources
            $table->json('training_providers')->nullable(); // Who provides training
            $table->json('consulting_services')->nullable(); // Implementation help
            $table->json('software_tools')->nullable(); // Supporting software
            $table->json('useful_links')->nullable(); // Additional resources

            // Metadata
            $table->json('tags')->nullable();
            $table->json('keywords')->nullable();
            $table->string('created_by_user')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('reference_count')->default(0); // How often referenced

            // Admin Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->enum('admin_status', ['draft', 'pending', 'approved'])->default('draft');

            $table->timestamps();

            // Indexes
            $table->index(['organization', 'category']);
            $table->index(['status', 'is_active']);
            $table->index(['standard_number', 'version']);
            $table->index(['publication_date', 'revision_date']);
            $table->index(['is_featured', 'admin_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineering_standards');
    }
};
