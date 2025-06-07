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
        Schema::create('showcase_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showcase_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('showcase_comments')->onDelete('cascade');
            $table->text('comment');
            $table->integer('like_count')->default(0);
            $table->timestamps();

            // Indexes cho performance
            $table->index(['showcase_id', 'parent_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_comments');
    }
};
