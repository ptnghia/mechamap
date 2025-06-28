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
        Schema::create('technical_drawings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('title');
            $table->string('drawing_number')->unique();
            $table->text('description')->nullable();
            $table->string('revision', 10)->default('A');

            // Creator & Ownership
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('marketplace_sellers')->onDelete('set null');

            // File Information
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // PDF, DWG, DXF
            $table->bigInteger('file_size'); // in bytes
            $table->string('mime_type');

            // Technical Specifications
            $table->string('drawing_type')->nullable(); // Assembly, Detail, Schematic
            $table->string('scale')->nullable(); // 1:1, 1:2, 1:10, etc.
            $table->string('units')->default('mm'); // mm, inch, m
            $table->json('dimensions')->nullable(); // length, width, height
            $table->decimal('sheet_size', 8, 2)->nullable(); // A4, A3, A2, A1, A0

            // Project & Category
            $table->string('project_name')->nullable();
            $table->string('part_number')->nullable();
            $table->string('material_specification')->nullable();
            $table->json('tolerances')->nullable(); // dimensional tolerances
            $table->json('surface_finish')->nullable(); // surface finish requirements

            // Standards & Compliance
            $table->json('drawing_standards')->nullable(); // ISO, ASME, DIN
            $table->json('material_standards')->nullable(); // ASTM, JIS, etc.
            $table->json('manufacturing_notes')->nullable();

            // Version Control
            $table->integer('version_number')->default(1);
            $table->foreignId('parent_drawing_id')->nullable()->constrained('technical_drawings')->onDelete('set null');
            $table->text('revision_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            // Access Control
            $table->enum('visibility', ['public', 'private', 'company_only'])->default('private');
            $table->enum('license_type', ['free', 'commercial', 'educational'])->default('free');
            $table->decimal('price', 10, 2)->nullable();

            // Metadata
            $table->json('tags')->nullable();
            $table->json('keywords')->nullable();
            $table->string('industry_category')->nullable();
            $table->string('application_area')->nullable(); // automotive, aerospace, etc.

            // Analytics
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);

            // Status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index(['created_by', 'status']);
            $table->index(['company_id', 'visibility']);
            $table->index(['drawing_number', 'revision']);
            $table->index(['project_name', 'part_number']);
            $table->index(['industry_category', 'application_area']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_drawings');
    }
};
