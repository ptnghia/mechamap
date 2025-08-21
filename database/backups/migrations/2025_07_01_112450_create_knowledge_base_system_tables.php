<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Knowledge Categories (Hierarchical)
        Schema::create('knowledge_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#007bff');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('knowledge_categories')->onDelete('cascade');
            $table->index(['is_active', 'sort_order']);
            $table->index('parent_id');
        });

        // 2. Knowledge Articles (Technical Content)
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('author_id');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->json('tags')->nullable();
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('views_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('knowledge_categories')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index('is_featured');
            $table->fullText(['title', 'content', 'excerpt']);
        });

        // 3. Knowledge Videos (Tutorial Content)
        Schema::create('knowledge_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->string('video_type')->default('youtube'); // youtube, vimeo, local
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('author_id');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('knowledge_categories')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index('is_featured');
            $table->fullText(['title', 'description']);
        });

        // 4. Knowledge Documents (File Resources)
        Schema::create('knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type'); // pdf, doc, xls, etc.
            $table->bigInteger('file_size'); // in bytes
            $table->string('original_filename');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('author_id');
            $table->json('tags')->nullable();
            $table->integer('download_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('knowledge_categories')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index('file_type');
            $table->index('is_featured');
            $table->fullText(['title', 'description']);
        });

        // 5. Knowledge Tags (Tagging System)
        Schema::create('knowledge_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#6c757d');
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index('usage_count');
        });

        // 6. Knowledge Bookmarks (User Bookmarks)
        Schema::create('knowledge_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('bookmarkable_type'); // App\Models\KnowledgeArticle, etc.
            $table->unsignedBigInteger('bookmarkable_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id']);
            $table->index(['bookmarkable_type', 'bookmarkable_id']);
        });

        // 7. Knowledge Ratings (User Ratings)
        Schema::create('knowledge_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ratable_type'); // App\Models\KnowledgeArticle, etc.
            $table->unsignedBigInteger('ratable_id');
            $table->tinyInteger('rating'); // 1-5 stars
            $table->text('review')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'ratable_type', 'ratable_id']);
            $table->index(['ratable_type', 'ratable_id']);
            $table->index('rating');
        });

        // 8. Knowledge Comments (Comment System)
        Schema::create('knowledge_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('commentable_type'); // App\Models\KnowledgeArticle, etc.
            $table->unsignedBigInteger('commentable_id');
            $table->unsignedBigInteger('parent_id')->nullable(); // for replies
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('knowledge_comments')->onDelete('cascade');
            $table->index(['commentable_type', 'commentable_id']);
            $table->index(['is_approved', 'created_at']);
        });

        // 9. Knowledge Views (Analytics Tracking)
        Schema::create('knowledge_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('viewable_type'); // App\Models\KnowledgeArticle, etc.
            $table->unsignedBigInteger('viewable_id');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['viewable_type', 'viewable_id']);
            $table->index('viewed_at');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_views');
        Schema::dropIfExists('knowledge_comments');
        Schema::dropIfExists('knowledge_ratings');
        Schema::dropIfExists('knowledge_bookmarks');
        Schema::dropIfExists('knowledge_tags');
        Schema::dropIfExists('knowledge_documents');
        Schema::dropIfExists('knowledge_videos');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('knowledge_categories');
    }
};
