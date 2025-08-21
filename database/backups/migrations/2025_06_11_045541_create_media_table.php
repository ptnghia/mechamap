<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Media table with mechanical engineering optimizations
     *
     * Merged from:
     * - create_media_table.php (base structure)
     * - enhance_media_for_mechanical_engineering.php (technical features)
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            // === BASE FIELDS ===
            $table->id();

            // User and content association
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship for flexible attachment
            $table->morphs('mediable'); // threads, comments, showcases, users

            // === CORE FILE INFORMATION ===
            $table->string('file_name'); // Tên file gốc
            $table->string('file_path'); // Đường dẫn storage
            $table->string('disk')->default('public'); // Storage disk
            $table->string('mime_type'); // MIME type
            $table->unsignedBigInteger('file_size'); // Kích thước byte
            $table->string('file_extension', 10); // Extension (.jpg, .pdf)

            // === MECHANICAL ENGINEERING OPTIMIZATIONS ===

            // CAD File Support
            $table->enum('file_category', [
                'cad_drawing',     // DWG, DXF files
                'cad_model',       // STEP, IGES, STL files
                'technical_doc',   // PDF specifications
                'image',          // JPG, PNG images
                'simulation',     // ANSYS, ABAQUS files
                'other'
            ])->default('other');

            // Technical Metadata for CAD files
            $table->json('cad_metadata')->nullable(); // CAD-specific info
            $table->string('cad_software')->nullable(); // AutoCAD, SolidWorks, etc.
            $table->string('cad_version')->nullable(); // Software version
            $table->decimal('drawing_scale', 8, 4)->nullable(); // 1:1, 1:10, etc.
            $table->string('units')->nullable(); // mm, inch, meter
            $table->json('dimensions')->nullable(); // {"width":100,"height":50,"depth":25}

            // Technical Document Properties
            $table->string('standard_compliance')->nullable(); // ASME, ISO, DIN
            $table->string('revision_number')->nullable(); // Drawing revision
            $table->date('drawing_date')->nullable(); // Creation date
            $table->string('material_specification')->nullable(); // Steel, Aluminum
            $table->text('technical_notes')->nullable(); // Technical comments

            // File Processing Status
            $table->enum('processing_status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ])->default('pending');

            $table->json('conversion_formats')->nullable(); // Available conversions
            $table->boolean('is_public')->default(false); // Public access
            $table->boolean('is_approved')->default(false); // Admin approved

            // Quality and Verification
            $table->boolean('virus_scanned')->default(false);
            $table->timestamp('scanned_at')->nullable();
            $table->boolean('contains_sensitive_data')->default(false);
            $table->integer('download_count')->default(0);

            // Image/Preview specific
            $table->string('thumbnail_path')->nullable(); // Preview image
            $table->integer('width')->nullable(); // Image width
            $table->integer('height')->nullable(); // Image height
            $table->json('exif_data')->nullable(); // Image EXIF data

            $table->timestamps();

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Base indexes
            $table->index(['user_id', 'created_at']);
            // Note: morphs() already creates index for mediable_type, mediable_id
            $table->index(['file_category', 'is_public']);
            $table->index(['mime_type']);

            // Mechanical engineering specific indexes
            $table->index(['file_category', 'cad_software'], 'media_cad_category');
            $table->index(['standard_compliance'], 'media_standards');
            $table->index(['processing_status', 'created_at'], 'media_processing');
            $table->index(['is_approved', 'is_public'], 'media_access');
            $table->index(['file_extension', 'file_category'], 'media_type_classification');
            $table->index(['download_count'], 'media_popularity');

            // Performance indexes
            $table->index(['user_id', 'file_category', 'created_at'], 'media_user_category');
            $table->index(['mediable_type', 'file_category'], 'media_attachment_type');
            $table->index(['cad_software', 'cad_version'], 'media_cad_compatibility');
            $table->index(['virus_scanned', 'is_approved'], 'media_security_status');

            // Search optimization
            $table->fullText(['file_name', 'technical_notes'], 'media_content_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
}
