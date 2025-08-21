<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Categories table with mechanical engineering enhancements
     *
     * Merged from:
     * - create_categories_table.php (base structure)
     * - enhance_categories_for_mechanical_engineering.php (mechanical features)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // === BASE CATEGORY FIELDS ===
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->integer('order')->default(0);

            // === MECHANICAL ENGINEERING ENHANCEMENTS ===

            // Visual & UI enhancements
            $table->string('icon', 500)->nullable()
                ->comment('URL hoặc class name của icon cho danh mục (material-symbols, ionicons, etc.)');

            $table->string('color_code', 7)->nullable()
                ->comment('Mã màu hex cho danh mục (#FF5722 cho Manufacturing, #2196F3 cho CAD/CAM)');

            $table->text('meta_description')->nullable()
                ->comment('Mô tả SEO cho danh mục');

            $table->text('meta_keywords')->nullable()
                ->comment('Keywords SEO cho danh mục kỹ thuật');

            // Mechanical Engineering specific fields
            $table->boolean('is_technical')->default(true)
                ->comment('Danh mục kỹ thuật yêu cầu expertise hay thảo luận chung');

            $table->enum('expertise_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()
                ->comment('Cấp độ chuyên môn được khuyến nghị cho danh mục');

            $table->boolean('requires_verification')->default(false)
                ->comment('Yêu cầu verification từ expert để post trong danh mục này');

            $table->json('allowed_file_types')->nullable()
                ->comment('Các loại file được phép upload: ["dwg","step","iges","pdf","doc","jpg"]');

            // Forum statistics and activity tracking
            $table->integer('thread_count')->default(0)
                ->comment('Số lượng thread trong danh mục (cached)');

            $table->integer('post_count')->default(0)
                ->comment('Tổng số bài post trong danh mục (cached)');

            $table->timestamp('last_activity_at')->nullable()
                ->comment('Thời gian hoạt động cuối cùng trong danh mục');

            // Content organization
            $table->boolean('is_active')->default(true)
                ->comment('Danh mục có đang hoạt động không');

            $table->integer('sort_order')->default(0)
                ->comment('Thứ tự sắp xếp danh mục (thay thế cho order)');

            $table->timestamps();

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Original base indexes
            $table->index(['parent_id', 'order'], 'categories_hierarchy_order_index');

            // Enhanced mechanical engineering indexes
            $table->index(['is_active', 'sort_order'], 'categories_active_sort_index');
            $table->index(['is_technical', 'expertise_level'], 'categories_technical_level_index');
            $table->index(['parent_id', 'is_active', 'sort_order'], 'categories_active_hierarchy_index');
            $table->index(['thread_count', 'last_activity_at'], 'categories_activity_stats_index');

            // SEO and search optimization
            $table->index(['is_active', 'name'], 'categories_search_index');
            $table->fullText(['name', 'description', 'meta_keywords'], 'categories_fulltext_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
