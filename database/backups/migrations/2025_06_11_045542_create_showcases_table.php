<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Showcases table with mechanical engineering project features
     *
     * Merged from:
     * - create_showcases_table.php (basic structure)
     * - enhance_showcases_for_mechanical_engineering.php (engineering project showcase)
     */
    public function up(): void
    {
        Schema::create('showcases', function (Blueprint $table) {
            // === BASE SHOWCASE FIELDS ===
            $table->id();

            // User and content association
            $table->foreignId('user_id')->constrained()->onDelete('cascade')
                ->comment('User tạo showcase project');

            // Polymorphic relationship for flexible showcase subjects
            $table->morphs('showcaseable'); // threads, comments, external_projects

            // === CORE PROJECT INFORMATION ===

            $table->string('title')
                ->comment('Tiêu đề dự án kỹ thuật');

            $table->string('slug')->unique()
                ->comment('URL-friendly identifier cho project');

            $table->text('description')->nullable()
                ->comment('Mô tả chi tiết dự án, phương pháp, kết quả');

            // === MECHANICAL ENGINEERING SPECIFIC FIELDS ===

            // Project classification and technology
            $table->enum('project_type', ['design', 'analysis', 'manufacturing', 'prototype', 'assembly', 'testing', 'research', 'optimization', 'simulation'])->nullable()
                ->comment('Loại dự án kỹ thuật');

            $table->string('software_used')->nullable()
                ->comment('Phần mềm sử dụng: SolidWorks, AutoCAD, ANSYS, CATIA, Fusion360');

            $table->string('materials')->nullable()
                ->comment('Vật liệu sử dụng: Steel, Aluminum, Composite, Plastic, etc.');

            $table->string('manufacturing_process')->nullable()
                ->comment('Quy trình sản xuất: CNC, 3D Printing, Casting, Welding, Machining');

            $table->json('technical_specs')->nullable()
                ->comment('Thông số kỹ thuật: {"dimensions":"100x50x20mm","tolerance":"±0.01","weight":"2.5kg"}');

            // === PROJECT CLASSIFICATION ===

            $table->enum('category', ['design', 'analysis', 'manufacturing', 'prototype', 'assembly', 'testing', 'research', 'innovation', 'optimization', 'education'])->default('design')
                ->comment('Danh mục dự án');

            $table->enum('complexity_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate')
                ->comment('Mức độ phức tạp kỹ thuật');

            $table->enum('industry_application', ['automotive', 'aerospace', 'manufacturing', 'energy', 'construction', 'marine', 'electronics', 'medical', 'general'])->nullable()
                ->comment('Ứng dụng ngành công nghiệp');

            // === EDUCATIONAL AND PROFESSIONAL VALUE ===

            $table->boolean('has_tutorial')->default(false)
                ->comment('Project có kèm hướng dẫn step-by-step không');

            $table->boolean('has_calculations')->default(false)
                ->comment('Project có kèm tính toán kỹ thuật không');

            $table->boolean('has_cad_files')->default(false)
                ->comment('Project có file CAD đính kèm không');

            $table->json('learning_objectives')->nullable()
                ->comment('Mục tiêu học tập: ["FEA analysis","Design optimization","Manufacturing process"]');

            // === MEDIA AND FILE MANAGEMENT ===

            $table->string('cover_image')->nullable()
                ->comment('Ảnh đại diện chính của project');

            $table->json('image_gallery')->nullable()
                ->comment('Gallery ảnh process và kết quả');

            $table->json('file_attachments')->nullable()
                ->comment('Files đính kèm: CAD, drawings, calculations, reports');

            // === PROJECT STATUS AND VISIBILITY ===

            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'featured', 'archived'])->default('draft')
                ->comment('Trạng thái review và publication');

            $table->boolean('is_public')->default(true)
                ->comment('Project có public access không');

            $table->boolean('allow_downloads')->default(false)
                ->comment('Cho phép download files không');

            $table->boolean('allow_comments')->default(true)
                ->comment('Cho phép comment và discussion không');

            // === ENGAGEMENT AND ANALYTICS ===

            $table->unsignedInteger('view_count')->default(0)
                ->comment('Số lượt xem project');

            $table->unsignedInteger('like_count')->default(0)
                ->comment('Số lượt like từ community');

            $table->unsignedInteger('download_count')->default(0)
                ->comment('Số lượt download files');

            $table->unsignedInteger('share_count')->default(0)
                ->comment('Số lượt chia sẻ project');

            // Professional rating system
            $table->decimal('rating_average', 3, 2)->default(0.00)
                ->comment('Đánh giá trung bình (0.00 - 5.00)');

            $table->unsignedInteger('rating_count')->default(0)
                ->comment('Số lượng đánh giá');

            $table->decimal('technical_quality_score', 3, 2)->default(0.00)
                ->comment('Điểm chất lượng kỹ thuật do expert đánh giá');

            // === ORGANIZATION AND DISPLAY ===

            $table->integer('display_order')->default(0)
                ->comment('Thứ tự hiển thị trong category');

            $table->timestamp('featured_at')->nullable()
                ->comment('Thời gian được featured');

            $table->timestamp('approved_at')->nullable()
                ->comment('Thời gian được approve');

            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')
                ->comment('Moderator đã approve project');

            $table->timestamps();

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Core project management
            $table->index(['status', 'created_at'], 'showcases_status_timeline');
            $table->index(['user_id', 'status'], 'showcases_user_projects');
            $table->index(['category', 'complexity_level'], 'showcases_classification');

            // Public visibility and featuring
            $table->index(['is_public', 'status', 'featured_at'], 'showcases_public_featured');
            $table->index(['status', 'featured_at', 'rating_average'], 'showcases_featured_quality');

            // Technical search and filtering
            $table->index(['project_type', 'software_used'], 'showcases_technical_tools');
            $table->index(['category', 'industry_application'], 'showcases_industry_category');
            $table->index(['complexity_level', 'has_tutorial'], 'showcases_learning_level');

            // Quality and engagement metrics
            $table->index(['rating_average', 'rating_count'], 'showcases_quality_ranking');
            $table->index(['view_count', 'like_count'], 'showcases_popularity');
            $table->index(['technical_quality_score', 'approved_at'], 'showcases_expert_approved');

            // Content features
            $table->index(['has_cad_files', 'allow_downloads'], 'showcases_downloadable_content');
            $table->index(['has_tutorial', 'has_calculations'], 'showcases_educational_content');

            // Advanced search combinations
            $table->index(['category', 'project_type', 'complexity_level', 'status'], 'showcases_advanced_filter');
            $table->index(['industry_application', 'software_used', 'is_public'], 'showcases_professional_search');

            // Full-text search for project content
            $table->fullText(['title', 'description'], 'showcases_content_search');

            // === UNIQUE CONSTRAINTS ===

            // Prevent duplicate showcases per user per object
            $table->unique(['user_id', 'showcaseable_id', 'showcaseable_type'], 'showcases_unique_user_object');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcases');
    }
};
