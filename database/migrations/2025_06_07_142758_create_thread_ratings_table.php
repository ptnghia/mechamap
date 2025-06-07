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
        Schema::create('thread_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->comment('1-5 stars rating');
            $table->text('review')->nullable()->comment('Optional review text');
            $table->timestamps();

            // Unique constraint để prevent duplicate ratings
            $table->unique(['thread_id', 'user_id']);

            // Indexes cho performance
            $table->index(['thread_id']);
            $table->index(['user_id']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thread_ratings');
    }
};
