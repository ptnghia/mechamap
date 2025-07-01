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
        // First create knowledge_categories table if not exists
        if (!Schema::hasTable('knowledge_categories')) {
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
        }

        // Add missing columns to knowledge_articles
        Schema::table('knowledge_articles', function (Blueprint $table) {
            if (!Schema::hasColumn('knowledge_articles', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('content');
                $table->foreign('category_id')->references('id')->on('knowledge_categories')->onDelete('set null');
            }

            if (!Schema::hasColumn('knowledge_articles', 'views_count')) {
                $table->integer('views_count')->default(0)->after('is_featured');
            }

            if (!Schema::hasColumn('knowledge_articles', 'tags')) {
                $table->json('tags')->nullable()->after('software_requirements');
            }
        });

        // Create other knowledge tables if not exist
        if (!Schema::hasTable('knowledge_videos')) {
            Schema::create('knowledge_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('video_url');
                $table->string('video_type')->default('youtube');
                $table->string('thumbnail')->nullable();
                $table->integer('duration')->nullable();
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
            });
        }

        if (!Schema::hasTable('knowledge_documents')) {
            Schema::create('knowledge_documents', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('file_path');
                $table->string('file_type');
                $table->bigInteger('file_size');
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
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_articles', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'views_count', 'tags']);
        });

        Schema::dropIfExists('knowledge_documents');
        Schema::dropIfExists('knowledge_videos');
        Schema::dropIfExists('knowledge_categories');
    }
};
