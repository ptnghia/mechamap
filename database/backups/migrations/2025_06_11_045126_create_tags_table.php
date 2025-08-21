<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Tags table with mechanical engineering enhancements
     *
     * Merged from:
     * - create_tags_table.php (basic structure)
     * - enhance_tags_for_mechanical_engineering.php (mechanical engineering categorization)
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            // === BASE TAG FIELDS ===
            $table->id();

            // Basic tag information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // === MECHANICAL ENGINEERING ENHANCEMENTS ===

            // Visual and categorization
            $table->string('color_code', 7)->default('#6366f1')
                ->comment('Hex color code cho tag display (#FF5722 cho manufacturing)');

            $table->enum('tag_type', ['general', 'software', 'material', 'process', 'industry'])->default('general')
                ->comment('Loại tag: chung, phần mềm, vật liệu, quy trình, ngành công nghiệp');

            $table->enum('expertise_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner')
                ->comment('Cấp độ chuyên môn yêu cầu cho tag này');

            // === USAGE TRACKING & STATISTICS ===

            $table->unsignedInteger('usage_count')->default(0)
                ->comment('Số lần tag được sử dụng (cached count)');

            $table->timestamp('last_used_at')->nullable()
                ->comment('Lần cuối tag được sử dụng');

            // === CONTENT MANAGEMENT ===

            $table->boolean('is_featured')->default(false)
                ->comment('Tag được highlight trong suggestions không');

            $table->boolean('is_active')->default(true)
                ->comment('Tag có đang được sử dụng không');

            $table->integer('sort_order')->default(0)
                ->comment('Thứ tự sắp xếp khi hiển thị tag');

            $table->timestamps();

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Basic search and identification
            $table->index(['name'], 'tags_name_search');
            $table->index(['slug'], 'tags_slug_lookup');

            // Mechanical engineering classification
            $table->index(['tag_type', 'expertise_level'], 'tags_technical_classification');
            $table->index(['tag_type', 'is_active'], 'tags_type_active');

            // Usage and popularity tracking
            $table->index(['usage_count', 'last_used_at'], 'tags_popularity_ranking');
            $table->index(['usage_count'], 'tags_usage_stats');

            // Content management and display
            $table->index(['is_active', 'is_featured'], 'tags_active_featured');
            $table->index(['is_featured', 'sort_order'], 'tags_featured_ordering');
            $table->index(['sort_order', 'name'], 'tags_display_order');

            // Advanced filtering combinations
            $table->index(['tag_type', 'expertise_level', 'is_active'], 'tags_advanced_filter');
            $table->index(['expertise_level', 'usage_count', 'last_used_at'], 'tags_expert_popularity');

            // Full-text search for tag names and descriptions
            $table->fullText(['name', 'description'], 'tags_content_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
