<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Documentation Management System
     */
    public function up(): void
    {
        // Documentation Categories Table
        Schema::create('documentation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Font Awesome icon
            $table->string('color_code', 7)->default('#007bff'); // Hex color
            $table->foreignId('parent_id')->nullable()->constrained('documentation_categories')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false); // Public access
            $table->json('allowed_roles')->nullable(); // JSON array of allowed roles
            $table->integer('document_count')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['parent_id', 'sort_order']);
            $table->index(['is_active', 'is_public']);
            $table->index('slug');
        });

        // Documentation Table
        Schema::create('documentations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->foreignId('category_id')->constrained('documentation_categories');
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            
            // Status and visibility
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false);
            $table->json('allowed_roles')->nullable(); // JSON array of allowed roles
            
            // Content metadata
            $table->enum('content_type', ['guide', 'api', 'tutorial', 'reference', 'faq'])->default('guide');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->integer('estimated_read_time')->nullable(); // in minutes
            
            // SEO and metadata
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('featured_image')->nullable();
            
            // Statistics
            $table->integer('view_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('download_count')->default(0);
            
            // Ordering and organization
            $table->integer('sort_order')->default(0);
            $table->json('tags')->nullable(); // JSON array of tags
            $table->json('related_docs')->nullable(); // JSON array of related document IDs
            
            // File attachments
            $table->json('attachments')->nullable(); // JSON array of file paths
            $table->json('downloadable_files')->nullable(); // JSON array of downloadable files
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['category_id', 'status', 'sort_order']);
            $table->index(['is_public', 'status', 'published_at']);
            $table->index(['content_type', 'difficulty_level']);
            $table->index(['is_featured', 'published_at']);
            $table->index('slug');
            $table->fulltext(['title', 'content', 'excerpt']);
        });

        // Documentation Versions Table (for version control)
        Schema::create('documentation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documentation_id')->constrained('documentations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Who made the change
            $table->string('version_number'); // e.g., "1.0", "1.1", "2.0"
            $table->longText('content'); // Content snapshot
            $table->text('change_summary')->nullable(); // Summary of changes
            $table->json('metadata')->nullable(); // Additional metadata
            $table->boolean('is_major_version')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['documentation_id', 'created_at']);
            $table->index('version_number');
        });

        // Documentation Views Table (for analytics)
        Schema::create('documentation_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documentation_id')->constrained('documentations')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->integer('time_spent')->nullable(); // seconds
            $table->decimal('scroll_percentage', 5, 2)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['documentation_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('ip_address');
        });

        // Documentation Ratings Table
        Schema::create('documentation_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documentation_id')->constrained('documentations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->boolean('is_helpful')->nullable(); // Was this helpful?
            $table->timestamps();

            // Unique constraint
            $table->unique(['documentation_id', 'user_id']);
            
            // Indexes
            $table->index(['documentation_id', 'rating']);
            $table->index('user_id');
        });

        // Documentation Comments Table
        Schema::create('documentation_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documentation_id')->constrained('documentations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('documentation_comments')->onDelete('cascade');
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_staff_response')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['documentation_id', 'status', 'created_at']);
            $table->index(['parent_id', 'created_at']);
            $table->index('user_id');
        });

        // Documentation Downloads Table
        Schema::create('documentation_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documentation_id')->constrained('documentations')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['documentation_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_downloads');
        Schema::dropIfExists('documentation_comments');
        Schema::dropIfExists('documentation_ratings');
        Schema::dropIfExists('documentation_views');
        Schema::dropIfExists('documentation_versions');
        Schema::dropIfExists('documentations');
        Schema::dropIfExists('documentation_categories');
    }
};
