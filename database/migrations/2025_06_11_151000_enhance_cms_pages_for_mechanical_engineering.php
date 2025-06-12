<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Enhanced CMS Pages for Mechanical Engineering
     */
    public function up(): void
    {
        // Enhance page_categories table
        Schema::table('page_categories', function (Blueprint $table) {
            $table->string('icon_url')->nullable()->after('description');
            $table->string('color_code', 7)->nullable()->after('icon_url');
            $table->enum('category_type', [
                'engineering_guides',
                'company_info',
                'technical_standards',
                'software_tutorials',
                'career_resources',
                'industry_news'
            ])->default('engineering_guides')->after('color_code');
            $table->boolean('is_active')->default(true)->after('category_type');
            $table->boolean('show_in_menu')->default(true)->after('is_active');
            $table->integer('page_count')->default(0)->after('show_in_menu');

            // Indexes for performance
            $table->index(['is_active', 'show_in_menu', 'order']);
            $table->index(['category_type', 'is_active']);
        });

        // Enhance faq_categories table
        Schema::table('faq_categories', function (Blueprint $table) {
            $table->string('icon_url')->nullable()->after('description');
            $table->enum('engineering_domain', [
                'mechanical_design',
                'manufacturing',
                'materials',
                'cad_software',
                'analysis_simulation',
                'career_guidance',
                'general'
            ])->default('general')->after('icon_url');
            $table->integer('faq_count')->default(0)->after('engineering_domain');

            // Indexes
            $table->index(['engineering_domain', 'is_active', 'order']);
        });

        // Enhance pages table
        Schema::table('pages', function (Blueprint $table) {
            $table->enum('page_type', [
                'about_us',
                'engineering_guide',
                'software_tutorial',
                'standards_reference',
                'career_guide',
                'company_policy',
                'technical_documentation',
                'industry_insight'
            ])->default('engineering_guide')->after('excerpt');

            $table->json('technical_specs')->nullable()->after('page_type');
            $table->json('prerequisites')->nullable()->after('technical_specs');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()->after('prerequisites');
            $table->integer('estimated_read_time')->nullable()->after('difficulty_level');
            $table->string('featured_image')->nullable()->after('estimated_read_time');
            $table->json('related_software')->nullable()->after('featured_image');
            $table->json('engineering_standards')->nullable()->after('related_software');

            $table->foreignId('author_id')->nullable()->constrained('users')->after('user_id');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->after('author_id');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('published_at');

            $table->decimal('rating_average', 3, 2)->default(0.00)->after('view_count');
            $table->integer('rating_count')->default(0)->after('rating_average');
            $table->boolean('requires_login')->default(false)->after('rating_count');
            $table->boolean('is_premium')->default(false)->after('requires_login');

            // Enhanced indexes
            $table->index(['page_type', 'status', 'published_at']);
            $table->index(['category_id', 'is_featured', 'published_at']);
            $table->index(['difficulty_level', 'rating_average']);
            $table->index(['author_id', 'created_at']);
            $table->fulltext(['title', 'content', 'excerpt']);
        });

        // Enhance faqs table
        Schema::table('faqs', function (Blueprint $table) {
            $table->enum('faq_type', [
                'software_usage',
                'calculation_method',
                'design_standard',
                'material_property',
                'manufacturing_process',
                'career_advice',
                'general_engineering'
            ])->default('general_engineering')->after('answer');

            $table->json('related_topics')->nullable()->after('faq_type');
            $table->json('applicable_standards')->nullable()->after('related_topics');
            $table->text('code_example')->nullable()->after('applicable_standards');
            $table->string('difficulty_level')->default('beginner')->after('code_example');

            $table->foreignId('created_by')->constrained('users')->after('difficulty_level');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->after('created_by');
            $table->integer('helpful_votes')->default(0)->after('reviewed_by');
            $table->integer('view_count')->default(0)->after('helpful_votes');
            $table->timestamp('last_updated')->nullable()->after('view_count');

            // Indexes for FAQ performance
            $table->index(['category_id', 'faq_type', 'is_active']);
            $table->index(['difficulty_level', 'helpful_votes']);
            $table->index(['created_by', 'created_at']);
            $table->fulltext(['question', 'answer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns and indexes
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'faq_type', 'is_active']);
            $table->dropIndex(['difficulty_level', 'helpful_votes']);
            $table->dropIndex(['created_by', 'created_at']);
            $table->dropFulltext(['question', 'answer']);

            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'faq_type',
                'related_topics',
                'applicable_standards',
                'code_example',
                'difficulty_level',
                'created_by',
                'reviewed_by',
                'helpful_votes',
                'view_count',
                'last_updated'
            ]);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['page_type', 'status', 'published_at']);
            $table->dropIndex(['category_id', 'is_featured', 'published_at']);
            $table->dropIndex(['difficulty_level', 'rating_average']);
            $table->dropIndex(['author_id', 'created_at']);
            $table->dropFulltext(['title', 'content', 'excerpt']);

            $table->dropForeign(['author_id']);
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn([
                'page_type',
                'technical_specs',
                'prerequisites',
                'difficulty_level',
                'estimated_read_time',
                'featured_image',
                'related_software',
                'engineering_standards',
                'author_id',
                'reviewer_id',
                'published_at',
                'reviewed_at',
                'rating_average',
                'rating_count',
                'requires_login',
                'is_premium'
            ]);
        });

        Schema::table('faq_categories', function (Blueprint $table) {
            $table->dropIndex(['engineering_domain', 'is_active', 'order']);
            $table->dropColumn(['icon_url', 'engineering_domain', 'faq_count']);
        });

        Schema::table('page_categories', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'show_in_menu', 'order']);
            $table->dropIndex(['category_type', 'is_active']);
            $table->dropColumn([
                'icon_url',
                'color_code',
                'category_type',
                'is_active',
                'show_in_menu',
                'page_count'
            ]);
        });
    }
};
