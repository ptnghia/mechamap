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

        // Create Knowledge Videos if not exists
        if (!Schema::hasTable('knowledge_videos')) {
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
        }

        // Create Knowledge Documents if not exists
        if (!Schema::hasTable('knowledge_documents')) {
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_documents');
        Schema::dropIfExists('knowledge_videos');
    }
};
