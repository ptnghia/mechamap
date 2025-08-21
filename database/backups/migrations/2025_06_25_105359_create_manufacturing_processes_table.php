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
        Schema::create('manufacturing_processes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('name');
            $table->string('code')->unique(); // Process code
            $table->text('description')->nullable();
            $table->string('category'); // Machining, Forming, Joining, Additive, etc.
            $table->string('subcategory')->nullable(); // CNC, Turning, Milling, etc.

            // Process Classification
            $table->string('process_type'); // Subtractive, Additive, Formative, etc.
            $table->json('alternative_names')->nullable(); // Different naming conventions

            // Technical Specifications
            $table->json('materials_compatible')->nullable(); // Compatible materials
            $table->json('material_limitations')->nullable(); // Material restrictions
            $table->json('dimensional_capabilities')->nullable(); // Size limits, tolerances
            $table->json('surface_finish_range')->nullable(); // Achievable surface finishes
            $table->json('tolerance_capabilities')->nullable(); // Achievable tolerances

            // Equipment & Tooling
            $table->json('required_equipment')->nullable(); // Machines needed
            $table->json('tooling_requirements')->nullable(); // Tools, fixtures
            $table->json('setup_requirements')->nullable(); // Setup considerations

            // Process Parameters
            $table->json('operating_parameters')->nullable(); // Speed, feed, temperature
            $table->json('parameter_ranges')->nullable(); // Min/max values
            $table->json('optimization_guidelines')->nullable(); // Best practices

            // Capabilities & Limitations
            $table->json('geometric_capabilities')->nullable(); // Shapes possible
            $table->json('geometric_limitations')->nullable(); // Design restrictions
            $table->decimal('min_feature_size', 8, 4)->nullable(); // Smallest feature
            $table->decimal('max_part_size', 12, 2)->nullable(); // Largest part
            $table->json('complexity_rating')->nullable(); // Simple, Medium, Complex

            // Quality & Standards
            $table->json('quality_standards')->nullable(); // ISO, ASME standards
            $table->json('inspection_methods')->nullable(); // Quality control
            $table->json('typical_defects')->nullable(); // Common issues
            $table->json('prevention_methods')->nullable(); // Defect prevention

            // Economic Factors
            $table->decimal('setup_cost', 10, 2)->nullable(); // Setup cost estimate
            $table->decimal('unit_cost_factor', 8, 4)->nullable(); // Cost per unit factor
            $table->integer('minimum_quantity')->nullable(); // Economic batch size
            $table->json('cost_drivers')->nullable(); // Main cost factors

            // Time & Production
            $table->decimal('setup_time_hours', 8, 2)->nullable(); // Setup time
            $table->decimal('cycle_time_factor', 8, 4)->nullable(); // Time per unit factor
            $table->json('production_rate_factors')->nullable(); // Rate considerations
            $table->integer('lead_time_days')->nullable(); // Typical lead time

            // Environmental & Safety
            $table->json('environmental_impact')->nullable(); // Environmental considerations
            $table->json('safety_requirements')->nullable(); // Safety measures
            $table->json('waste_products')->nullable(); // Waste generated
            $table->boolean('requires_special_handling')->default(false);

            // Applications
            $table->json('typical_applications')->nullable(); // Common uses
            $table->json('industries')->nullable(); // Suitable industries
            $table->json('part_types')->nullable(); // Suitable part types

            // Process Flow
            $table->json('prerequisite_processes')->nullable(); // Required before
            $table->json('subsequent_processes')->nullable(); // Possible after
            $table->json('alternative_processes')->nullable(); // Alternative methods

            // Documentation
            $table->string('process_sheet_path')->nullable(); // Process documentation
            $table->json('reference_documents')->nullable(); // Standards, guides
            $table->json('case_studies')->nullable(); // Example applications
            $table->json('video_tutorials')->nullable(); // Educational content

            // Supplier Information
            $table->json('service_providers')->nullable(); // Companies offering service
            $table->json('equipment_suppliers')->nullable(); // Equipment vendors
            $table->json('geographic_availability')->nullable(); // Where available

            // Metadata
            $table->json('tags')->nullable();
            $table->json('keywords')->nullable();
            $table->string('created_by_user')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            // Status
            $table->enum('status', ['draft', 'pending', 'approved', 'deprecated'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Analytics
            $table->integer('usage_count')->default(0);
            $table->integer('view_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['category', 'subcategory']);
            $table->index(['process_type', 'status']);
            $table->index(['is_active', 'is_featured']);
            $table->index(['name', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_processes');
    }
};
