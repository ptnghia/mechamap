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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('name');
            $table->string('code')->unique(); // Material code/designation
            $table->text('description')->nullable();
            $table->string('category'); // Metal, Polymer, Ceramic, Composite
            $table->string('subcategory')->nullable(); // Steel, Aluminum, Titanium, etc.

            // Material Classification
            $table->string('material_type'); // Structural, Tool, Special
            $table->string('grade')->nullable(); // AISI 1045, 6061-T6, etc.
            $table->json('alternative_designations')->nullable(); // Different naming standards

            // Physical Properties
            $table->decimal('density', 8, 4)->nullable(); // kg/m³
            $table->decimal('melting_point', 8, 2)->nullable(); // °C
            $table->decimal('thermal_conductivity', 8, 4)->nullable(); // W/m·K
            $table->decimal('thermal_expansion', 12, 8)->nullable(); // /°C
            $table->decimal('specific_heat', 8, 4)->nullable(); // J/kg·K
            $table->decimal('electrical_resistivity', 12, 8)->nullable(); // Ω·m

            // Mechanical Properties
            $table->decimal('youngs_modulus', 12, 2)->nullable(); // GPa
            $table->decimal('shear_modulus', 12, 2)->nullable(); // GPa
            $table->decimal('bulk_modulus', 12, 2)->nullable(); // GPa
            $table->decimal('poissons_ratio', 6, 4)->nullable();
            $table->decimal('yield_strength', 12, 2)->nullable(); // MPa
            $table->decimal('tensile_strength', 12, 2)->nullable(); // MPa
            $table->decimal('compressive_strength', 12, 2)->nullable(); // MPa
            $table->decimal('fatigue_strength', 12, 2)->nullable(); // MPa
            $table->decimal('hardness_hb', 8, 2)->nullable(); // Brinell hardness
            $table->decimal('hardness_hrc', 6, 2)->nullable(); // Rockwell C hardness
            $table->decimal('impact_energy', 8, 2)->nullable(); // Joules
            $table->decimal('elongation', 6, 2)->nullable(); // %

            // Chemical Composition
            $table->json('chemical_composition')->nullable(); // Element percentages
            $table->json('impurities')->nullable(); // Allowable impurities

            // Manufacturing Properties
            $table->json('machinability')->nullable(); // Cutting speeds, feeds
            $table->json('weldability')->nullable(); // Welding characteristics
            $table->json('formability')->nullable(); // Forming characteristics
            $table->json('heat_treatment')->nullable(); // Heat treatment options

            // Standards & Specifications
            $table->json('standards')->nullable(); // ASTM, ISO, JIS, DIN
            $table->json('specifications')->nullable(); // Detailed specs
            $table->json('certifications')->nullable(); // Required certifications

            // Applications
            $table->json('typical_applications')->nullable();
            $table->json('industries')->nullable(); // Automotive, Aerospace, etc.
            $table->json('manufacturing_processes')->nullable(); // Suitable processes

            // Availability & Cost
            $table->json('suppliers')->nullable(); // Available suppliers
            $table->json('forms_available')->nullable(); // Sheet, Rod, Tube, etc.
            $table->decimal('cost_per_kg', 10, 4)->nullable(); // Approximate cost
            $table->string('availability')->nullable(); // Common, Special Order, etc.

            // Environmental & Safety
            $table->json('environmental_impact')->nullable();
            $table->json('safety_considerations')->nullable();
            $table->json('recycling_info')->nullable();
            $table->boolean('hazardous')->default(false);

            // Documentation
            $table->string('datasheet_path')->nullable();
            $table->json('reference_documents')->nullable();
            $table->json('test_reports')->nullable();

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
            $table->index(['material_type', 'grade']);
            $table->index(['status', 'is_active']);
            $table->index(['is_featured', 'category']);
            $table->index(['name', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
