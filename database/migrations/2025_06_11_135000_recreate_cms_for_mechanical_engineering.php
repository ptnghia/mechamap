<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Enhanced CMS for Mechanical Engineering Forum
     */
    public function up(): void
    {
        // Drop and recreate existing incomplete tables
        Schema::dropIfExists('content_revisions');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('content_templates');
        Schema::dropIfExists('content_categories');

        // Drop existing simple cms table
        Schema::dropIfExists('cms');

        // Content Templates Table - For reusable technical content blocks
        Schema::create('content_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('template_content');
            $table->enum('template_type', [
                'calculation_guide',
                'cad_tutorial',
                'fea_procedure',
                'manufacturing_process',
                'safety_protocol',
                'design_standard',
                'material_spec',
                'troubleshooting'
            ]);
            $table->json('template_variables')->nullable(); // For dynamic content
            $table->json('required_skills')->nullable(); // Prerequisites
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->string('industry_sector')->nullable(); // automotive, aerospace, manufacturing
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['template_type', 'is_active'], 'tmpl_type_active_idx');
            $table->index(['difficulty_level', 'industry_sector'], 'tmpl_diff_sector_idx');
            $table->index(['is_featured', 'created_at'], 'tmpl_featured_date_idx');
            $table->fulltext(['name', 'description', 'template_content']);
        });

        // Content Blocks Table - Reusable content components
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->enum('block_type', [
                'engineering_formula',
                'material_properties',
                'standard_table',
                'calculation_example',
                'cad_snippet',
                'code_block',
                'diagram_embed',
                'reference_link'
            ]);
            $table->json('metadata')->nullable(); // Technical specifications
            $table->string('content_format', 50)->default('html'); // html, markdown, latex
            $table->json('tags')->nullable(); // Technical tags
            $table->enum('engineering_domain', [
                'mechanical_design',
                'manufacturing',
                'materials',
                'thermodynamics',
                'fluid_mechanics',
                'controls',
                'fea_analysis',
                'cad_cam'
            ])->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->integer('reference_count')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['block_type', 'is_public', 'is_approved'], 'blk_type_public_approved_idx');
            $table->index(['engineering_domain', 'created_at'], 'blk_domain_date_idx');
            $table->fulltext(['title', 'content']);
        });

        // Knowledge Base Articles Table
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->enum('article_type', [
                'tutorial',
                'best_practice',
                'case_study',
                'troubleshooting',
                'standard_procedure',
                'design_guide',
                'calculation_method',
                'software_guide'
            ]);
            $table->enum('engineering_field', [
                'mechanical_design',
                'manufacturing_engineering',
                'materials_engineering',
                'automotive_engineering',
                'aerospace_engineering',
                'industrial_engineering',
                'quality_engineering',
                'maintenance_engineering'
            ])->nullable();
            $table->json('prerequisites')->nullable(); // Required knowledge/skills
            $table->json('learning_outcomes')->nullable(); // What readers will learn
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->integer('estimated_read_time')->nullable(); // Minutes
            $table->string('featured_image')->nullable();
            $table->json('technical_specs')->nullable(); // Standards, tolerances, etc.
            $table->json('software_requirements')->nullable(); // CAD software, versions
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_pe_license')->default(false); // Professional Engineer verification
            $table->timestamps();

            // Performance indexes
            $table->index(['article_type', 'status', 'published_at'], 'art_type_status_pub_idx');
            $table->index(['engineering_field', 'difficulty_level'], 'art_field_diff_idx');
            $table->index(['is_featured', 'rating_average'], 'art_featured_rating_idx');
            $table->index(['author_id', 'created_at'], 'art_author_date_idx');
            $table->fulltext(['title', 'excerpt', 'content']);
        });

        // Content Revisions Table - Version control for technical content
        Schema::create('content_revisions', function (Blueprint $table) {
            $table->id();
            $table->morphs('revisionable'); // polymorphic relation
            $table->integer('revision_number');
            $table->longText('content_snapshot');
            $table->json('metadata_snapshot')->nullable();
            $table->string('change_summary')->nullable();
            $table->enum('change_type', [
                'technical_correction',
                'content_update',
                'formatting_fix',
                'standard_update',
                'formula_revision',
                'procedure_update'
            ]);
            $table->foreignId('created_by')->constrained('users');
            $table->string('editor_notes')->nullable();
            $table->boolean('is_major_revision')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['revisionable_type', 'revisionable_id', 'revision_number'], 'rev_morphs_rev_idx');
            $table->index(['created_by', 'created_at'], 'rev_creator_date_idx');
        });

        // Content Categories Table - Hierarchical categorization
        Schema::create('content_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon_url')->nullable();
            $table->string('color_code', 7)->nullable(); // Hex color
            $table->foreignId('parent_id')->nullable()->constrained('content_categories');
            $table->integer('sort_order')->default(0);
            $table->enum('category_type', [
                'engineering_discipline',
                'content_type',
                'skill_level',
                'industry_sector',
                'software_category'
            ]);
            $table->json('metadata')->nullable(); // Category-specific settings
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_menu')->default(true);
            $table->integer('content_count')->default(0); // Cached count
            $table->timestamps();

            // Indexes
            $table->index(['parent_id', 'sort_order'], 'cat_parent_sort_idx');
            $table->index(['category_type', 'is_active'], 'cat_type_active_idx');
            $table->index(['show_in_menu', 'sort_order'], 'cat_menu_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_revisions');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('content_templates');
        Schema::dropIfExists('content_categories');

        // Recreate simple cms table
        Schema::create('cms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
