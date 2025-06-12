<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED: Threads table with mechanical engineering optimizations
     *
     * Merged from:
     * - create_threads_table.php (comprehensive base structure)
     * - optimize_threads_for_mechanical_forum.php (mechanical engineering features)
     */
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            // === BASE THREAD FIELDS ===
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');

            // SEO & Media
            $table->string('featured_image')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('search_keywords')->nullable();
            $table->integer('read_time')->nullable();

            // Relationships
            $table->string('status')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('forum_id')->constrained()->onDelete('cascade')->comment('Forum mà thread thuộc về');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null')->comment('Category cho classification bổ sung');

            // Thread States
            $table->boolean('is_sticky')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_solved')->default(false);

            // Solution Tracking
            $table->unsignedBigInteger('solution_comment_id')->nullable();
            $table->timestamp('solved_at')->nullable();
            $table->foreignId('solved_by')->nullable()->constrained('users')->onDelete('set null');

            // Quality & Rating
            $table->integer('quality_score')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('ratings_count')->default(0);

            // Thread Type (base enum from original)
            $table->enum('thread_type', [
                'discussion',
                'question',
                'tutorial',
                'showcase',
                'news',
                'poll'
            ])->default('discussion');

            // === MECHANICAL ENGINEERING OPTIMIZATIONS ===

            // Technical complexity classification
            $table->enum('technical_difficulty', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()
                ->comment('Cấp độ kỹ thuật của chủ đề (beginner=sinh viên, expert=kỹ sư senior)');

            $table->enum('project_type', [
                'design',
                'manufacturing',
                'analysis',
                'troubleshooting',
                'maintenance',
                'research',
                'tutorial',
                'case_study'
            ])->nullable()
                ->comment('Loại dự án/vấn đề: thiết kế, sản xuất, phân tích, xử lý sự cố');

            $table->json('software_used')->nullable()
                ->comment('Phần mềm sử dụng: ["AutoCAD","SolidWorks","ANSYS","CATIA","Fusion360"]');

            $table->enum('industry_sector', [
                'automotive',
                'aerospace',
                'manufacturing',
                'energy',
                'construction',
                'marine',
                'electronics',
                'general'
            ])->nullable()
                ->comment('Lĩnh vực công nghiệp: ô tô, hàng không, sản xuất, năng lượng');

            // Technical specifications and requirements
            $table->json('technical_specs')->nullable()
                ->comment('Thông số kỹ thuật: {"material":"Steel","tolerance":"±0.01","pressure":"10MPa"}');

            $table->boolean('requires_calculations')->default(false)
                ->comment('Thread yêu cầu tính toán kỹ thuật (FEA, stress analysis, thermal)');

            $table->boolean('has_drawings')->default(false)
                ->comment('Thread có kèm bản vẽ kỹ thuật (DWG, PDF, STEP)');

            $table->enum('urgency_level', ['low', 'normal', 'high', 'critical'])->default('normal')
                ->comment('Mức độ khẩn cấp: low=học tập, critical=sự cố sản xuất');

            // Engineering Standards and Compliance
            $table->json('standards_compliance')->nullable()
                ->comment('Tiêu chuẩn áp dụng: ["ASME","ISO","ASTM","JIS","DIN"]');

            $table->boolean('requires_pe_review')->default(false)
                ->comment('Yêu cầu review từ Professional Engineer (PE license)');

            // File attachments tracking
            $table->boolean('has_cad_files')->default(false)
                ->comment('Thread có file CAD đính kèm');

            $table->integer('attachment_count')->default(0)
                ->comment('Số lượng file đính kèm');

            // Activity and engagement metrics
            $table->integer('view_count')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('bookmarks')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('replies')->default(0);            // Additional activity tracking fields from fillable
            $table->unsignedBigInteger('last_comment_by')->nullable();
            $table->integer('bump_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->integer('bookmark_count')->default(0);
            $table->integer('follow_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->integer('cached_comments_count')->default(0);
            $table->integer('cached_participants_count')->default(0);

            // Technical tracking
            $table->json('attachment_types')->nullable();
            $table->boolean('has_calculations')->default(false);
            $table->boolean('has_3d_models')->default(false);
            $table->boolean('expert_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->decimal('technical_accuracy_score', 3, 2)->default(0.00);
            $table->json('technical_keywords')->nullable();
            $table->json('related_standards')->nullable();

            // Additional timestamps
            $table->timestamp('flagged_at')->nullable();
            $table->timestamp('last_comment_at')->nullable();
            $table->timestamp('last_bump_at')->nullable();

            // Moderation System - ADDED for forum moderation
            $table->enum('moderation_status', ['pending', 'approved', 'rejected', 'flagged'])
                ->default('approved')->index();
            $table->boolean('is_spam')->default(false)->index();
            $table->timestamp('hidden_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable()->index();
            $table->unsignedBigInteger('moderated_by')->nullable();
            $table->text('moderation_notes')->nullable();
            $table->timestamp('moderated_at')->nullable();

            // Performance and optimization
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('bumped_at')->nullable();
            $table->integer('priority')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // === CONSOLIDATED PERFORMANCE INDEXES ===

            // Base forum indexes
            $table->index(['category_id', 'status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_sticky', 'is_featured', 'created_at']);
            $table->index(['is_solved', 'solved_at']);

            // Mechanical engineering specific indexes
            $table->index(['technical_difficulty', 'project_type'], 'threads_technical_classification');
            $table->index(['industry_sector', 'requires_pe_review'], 'threads_industry_professional');
            $table->index(['software_used'], 'threads_software_index');
            $table->index(['urgency_level', 'created_at'], 'threads_urgency_timeline');
            $table->index(['has_cad_files', 'requires_calculations'], 'threads_technical_features');
            $table->index(['last_activity_at', 'view_count'], 'threads_activity_popularity');

            // Performance optimization indexes
            $table->index(['category_id', 'is_sticky', 'last_activity_at'], 'threads_category_activity');
            $table->index(['project_type', 'technical_difficulty', 'created_at'], 'threads_technical_timeline');

            // Search optimization
            $table->fullText(['title', 'content'], 'threads_content_search');

            // Foreign key constraints
            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('last_comment_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });

        // NOTE: Foreign key constraint for solution_comment_id will be added
        // in a separate migration after comments table is created
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
