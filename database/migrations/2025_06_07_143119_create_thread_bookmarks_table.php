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
        Schema::create('thread_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('folder')->nullable()->comment('Bookmark folder/category');
            $table->text('notes')->nullable()->comment('Personal notes for bookmark');
            $table->timestamps();

            // Unique constraint để prevent duplicate bookmarks
            $table->unique(['thread_id', 'user_id']);

            // Indexes cho performance
            $table->index(['thread_id']);
            $table->index(['user_id']);
            $table->index(['folder']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thread_bookmarks');
    }
};
