<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Comments table with technical discussion enhancements
     *
     * Merged from:
     * - create_comments_table.php (comprehensive comment system)
     * - enhance_comments_for_technical_discussion.php (technical discussion features)
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            // === BASE COMMENT FIELDS ===
            $table->id();
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('set null');
            $table->text('content');

            // Media & Features
            $table->boolean('has_media')->default(false);

            // === TECHNICAL DISCUSSION ENHANCEMENTS ===

            // Technical content indicators
            $table->boolean('has_code_snippet')->default(false)
                ->comment('Comment có chứa code/formula/technical calculation không');

            $table->boolean('has_formula')->default(false)
                ->comment('Comment có chứa công thức toán học/kỹ thuật không');

            $table->text('formula_content')->nullable()
                ->comment('Nội dung công thức (LaTeX format cho MathJax rendering)');

            // === ENGAGEMENT & QUALITY METRICS ===

            // Base engagement
            $table->integer('like_count')->default(0);
            $table->integer('dislikes_count')->default(0);

            // Enhanced engagement for technical content
            $table->integer('helpful_count')->default(0)
                ->comment('Số lượt đánh giá "hữu ích" cho câu trả lời kỹ thuật');

            $table->integer('expert_endorsements')->default(0)
                ->comment('Số lượng expert ủng hộ câu trả lời này');

            // Quality scoring system
            $table->decimal('quality_score', 5, 2)->default(0.00);

            $table->decimal('technical_accuracy_score', 3, 2)->default(0.00)
                ->comment('Điểm độ chính xác kỹ thuật (0.00 - 5.00) do expert đánh giá');

            // === EXPERT VERIFICATION SYSTEM ===

            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'disputed'])->default('unverified')
                ->comment('Trạng thái xác minh: chưa xác minh, chờ xác minh, đã xác minh, có tranh cãi');

            $table->foreignId('verified_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->comment('Expert đã verify comment này');

            $table->timestamp('verified_at')->nullable()
                ->comment('Thời gian được verify');

            // === CONTENT CLASSIFICATION ===

            $table->json('technical_tags')->nullable()
                ->comment('Tags kỹ thuật: ["calculation","design","material","manufacturing"]');

            $table->enum('answer_type', ['general', 'calculation', 'reference', 'experience', 'tutorial'])->nullable()
                ->comment('Loại câu trả lời: tổng quát, tính toán, tham khảo, kinh nghiệm, hướng dẫn');

            // === MODERATION & QUALITY CONTROL ===

            $table->boolean('is_flagged')->default(false);
            $table->boolean('is_spam')->default(false);
            $table->boolean('is_solution')->default(false);
            $table->integer('reports_count')->default(0);

            // === EDIT HISTORY & TRACKING ===

            $table->timestamp('edited_at')->nullable();
            $table->integer('edit_count')->default(0);
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('edit_reason')->nullable();

            $table->timestamps();
            $table->softDeletes(); // deleted_at

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Base comment structure indexes
            $table->index(['thread_id', 'parent_id'], 'comments_thread_hierarchy');
            $table->index(['thread_id', 'created_at'], 'comments_thread_timeline');
            $table->index(['parent_id', 'created_at'], 'comments_reply_timeline');
            $table->index(['user_id', 'created_at'], 'comments_user_activity');

            // Moderation and quality indexes
            $table->index(['is_flagged'], 'comments_moderation_flagged');
            $table->index(['is_spam'], 'comments_moderation_spam');
            $table->index(['is_solution'], 'comments_solution_tracking');
            $table->index(['quality_score'], 'comments_quality_ranking');
            $table->index(['edited_at'], 'comments_edit_history');

            // Technical discussion indexes
            $table->index(['verification_status', 'technical_accuracy_score'], 'comments_expert_verification');
            $table->index(['has_code_snippet', 'has_formula'], 'comments_technical_content');
            $table->index(['helpful_count', 'expert_endorsements'], 'comments_helpfulness_ranking');
            $table->index(['answer_type', 'created_at'], 'comments_answer_classification');

            // Advanced search and filtering
            $table->index(['thread_id', 'verification_status', 'is_solution'], 'comments_verified_solutions');
            $table->index(['answer_type', 'technical_accuracy_score', 'helpful_count'], 'comments_quality_search');

            // Full-text search for technical content
            $table->fullText(['content'], 'comments_content_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
