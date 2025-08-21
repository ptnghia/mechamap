<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Social Interactions table with mechanical forum enhancements
     *
     * Merged from:
     * - create_social_interactions_table.php (basic structure)
     * - enhance_social_interactions_for_mechanical_forum.php (professional interactions)
     */
    public function up(): void
    {
        Schema::create('social_interactions', function (Blueprint $table) {
            // === BASE INTERACTION FIELDS ===
            $table->id();

            // Core interaction definition
            $table->string('interaction_type')
                ->comment('Loại tương tác: like, share, follow, bookmark, rate, endorse, mention');

            // Polymorphic relationship for flexible interactions
            $table->morphs('interactable'); // threads, comments, showcases, users

            // User relationships
            $table->unsignedBigInteger('user_id')
                ->comment('Người thực hiện tương tác');

            $table->unsignedBigInteger('target_user_id')->nullable()
                ->comment('Người nhận tương tác (cho follow, mention, endorse)');

            // === MECHANICAL ENGINEERING FORUM ENHANCEMENTS ===

            // Professional interaction metadata
            $table->json('metadata')->nullable()
                ->comment('Metadata bổ sung: {"rating": 4.5, "platform": "linkedin", "expertise_area": "FEA"}');

            $table->enum('context', ['thread', 'comment', 'showcase', 'user', 'general'])->default('general')
                ->comment('Ngữ cảnh tương tác: trong thread, comment, showcase, profile user');

            // Technical rating system for professional validation
            $table->decimal('rating_value', 3, 2)->nullable()
                ->comment('Giá trị đánh giá kỹ thuật (1.00-5.00 cho technical accuracy)');

            $table->text('interaction_note')->nullable()
                ->comment('Ghi chú cho tương tác phức tạp (lý do endorse, feedback chi tiết)');

            // Professional endorsement tracking
            $table->enum('endorsement_type', ['technical_skill', 'problem_solving', 'innovation', 'mentoring', 'leadership'])->nullable()
                ->comment('Loại endorsement chuyên môn cho mechanical engineers');

            $table->json('expertise_areas')->nullable()
                ->comment('Lĩnh vực chuyên môn được endorse: ["CAD", "FEA", "Manufacturing", "Materials"]');

            // === STATUS AND TRACKING ===

            $table->enum('status', ['active', 'hidden', 'deleted'])->default('active')
                ->comment('Trạng thái tương tác');

            $table->timestamp('interaction_date')->useCurrent()
                ->comment('Thời gian thực hiện tương tác');

            // Technical metadata for audit and analytics
            $table->ipAddress('ip_address')->nullable()
                ->comment('IP address cho audit trail');

            $table->string('user_agent', 500)->nullable()
                ->comment('User agent cho analytics');

            $table->string('referrer_url', 500)->nullable()
                ->comment('URL nguồn của tương tác');

            $table->timestamps();

            // === FOREIGN KEY CONSTRAINTS ===

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Core interaction tracking
            $table->index(['interaction_type', 'context', 'interaction_date'], 'social_interactions_type_context_date');
            $table->index(['user_id', 'interaction_type', 'created_at'], 'social_interactions_user_activity');
            $table->index(['interactable_type', 'interactable_id', 'interaction_type'], 'social_interactions_morph_type');
            $table->index(['target_user_id', 'interaction_type'], 'social_interactions_target_tracking');

            // Professional endorsement tracking
            $table->index(['endorsement_type', 'rating_value'], 'social_interactions_professional_rating');
            $table->index(['target_user_id', 'endorsement_type', 'interaction_date'], 'social_interactions_endorsement_history');

            // Status and moderation
            $table->index(['status', 'interaction_date'], 'social_interactions_status_timeline');
            $table->index(['interaction_type', 'status', 'created_at'], 'social_interactions_active_timeline');

            // Analytics and reporting
            $table->index(['context', 'interaction_date', 'rating_value'], 'social_interactions_analytics');
            $table->index(['user_id', 'target_user_id', 'interaction_type'], 'social_interactions_relationship');

            // === UNIQUE CONSTRAINTS ===

            // Prevent duplicate interactions per user per object
            $table->unique(['user_id', 'interactable_type', 'interactable_id', 'interaction_type'], 'social_interactions_unique_interaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_interactions');
    }
};
