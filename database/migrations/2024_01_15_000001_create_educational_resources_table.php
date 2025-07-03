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
        Schema::create('educational_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('category'); // textbook, video, research_paper, etc.
            $table->string('type'); // pdf, video, audio, etc.
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            
            // File information
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('file_type')->nullable(); // MIME type
            $table->string('thumbnail_path')->nullable();
            $table->integer('duration_minutes')->nullable(); // for videos/audio
            
            // Academic information
            $table->string('language', 10)->default('en');
            $table->json('tags')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('university_id')->nullable()->constrained()->onDelete('set null');
            $table->string('course_code')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('semester')->nullable();
            
            // Learning information
            $table->json('prerequisites')->nullable(); // required knowledge/courses
            $table->json('learning_objectives')->nullable();
            $table->json('metadata')->nullable(); // additional structured data
            
            // Status and metrics
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('download_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['category', 'is_published']);
            $table->index(['difficulty_level', 'is_published']);
            $table->index(['user_id', 'is_published']);
            $table->index(['university_id', 'course_code']);
            $table->index(['is_featured', 'is_published']);
            $table->index('created_at');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_resources');
    }
};
