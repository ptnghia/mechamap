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
        Schema::create('cad_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_number')->unique();
            $table->string('version', 10)->default('1.0');

            // Creator & Ownership
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('marketplace_sellers')->onDelete('set null');

            // File Information
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('file_extension'); // dwg, step, iges, stl, etc.
            $table->bigInteger('file_size'); // in bytes
            $table->string('mime_type');
            $table->string('checksum')->nullable(); // for file integrity

            // CAD Software Information
            $table->string('cad_software')->nullable(); // SolidWorks, AutoCAD, Fusion360
            $table->string('software_version')->nullable();
            $table->json('compatible_software')->nullable(); // list of compatible software

            // Technical Specifications
            $table->string('model_type'); // part, assembly, drawing, simulation
            $table->string('geometry_type')->nullable(); // solid, surface, wireframe
            $table->json('units')->nullable(); // mm, inch, m
            $table->json('bounding_box')->nullable(); // min/max coordinates
            $table->decimal('volume', 15, 6)->nullable(); // in cubic units
            $table->decimal('surface_area', 15, 6)->nullable(); // in square units
            $table->decimal('mass', 15, 6)->nullable(); // calculated mass

            // Material & Manufacturing
            $table->string('material_type')->nullable();
            $table->json('material_properties')->nullable(); // density, strength, etc.
            $table->json('manufacturing_methods')->nullable(); // CNC, 3D printing, casting
            $table->json('manufacturing_constraints')->nullable();

            // Design Information
            $table->string('design_intent')->nullable();
            $table->json('features')->nullable(); // holes, fillets, chamfers, etc.
            $table->json('parameters')->nullable(); // parametric dimensions
            $table->json('configurations')->nullable(); // different configurations

            // Related Files
            $table->foreignId('technical_drawing_id')->nullable()->constrained('technical_drawings')->onDelete('set null');
            $table->json('related_files')->nullable(); // IDs of related CAD files
            $table->string('thumbnail_path')->nullable(); // preview image

            // Standards & Compliance
            $table->json('design_standards')->nullable(); // ISO, ASME, DIN
            $table->json('tolerance_standards')->nullable();
            $table->json('quality_requirements')->nullable();

            // Version Control
            $table->integer('version_number')->default(1);
            $table->foreignId('parent_file_id')->nullable()->constrained('cad_files')->onDelete('set null');
            $table->text('version_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            // Access Control & Licensing
            $table->enum('visibility', ['public', 'private', 'company_only'])->default('private');
            $table->enum('license_type', ['free', 'commercial', 'educational', 'open_source'])->default('free');
            $table->decimal('price', 10, 2)->nullable();
            $table->json('usage_rights')->nullable(); // modify, redistribute, commercial use

            // Metadata
            $table->json('tags')->nullable();
            $table->json('keywords')->nullable();
            $table->string('industry_category')->nullable();
            $table->string('application_area')->nullable();
            $table->string('complexity_level')->nullable(); // beginner, intermediate, advanced

            // Analytics
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);

            // Processing Status
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('processing_log')->nullable(); // conversion logs, errors
            $table->timestamp('processed_at')->nullable();

            // Status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('virus_scanned')->default(false);
            $table->timestamp('virus_scan_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['created_by', 'status']);
            $table->index(['company_id', 'visibility']);
            $table->index(['file_number', 'version']);
            $table->index(['cad_software', 'model_type']);
            $table->index(['industry_category', 'application_area']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['processing_status', 'virus_scanned']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_a_d_files');
    }
};
