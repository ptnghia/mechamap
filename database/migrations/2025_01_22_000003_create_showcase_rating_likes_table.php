<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tạo bảng showcase_rating_likes để hỗ trợ:
     * - Like/unlike đánh giá
     * - Tracking user likes
     * - Prevent duplicate likes
     */
    public function up(): void
    {
        Schema::create('showcase_rating_likes', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('rating_id')
                ->constrained('showcase_ratings')
                ->onDelete('cascade')
                ->comment('ID của đánh giá được like');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID của user thực hiện like');
            
            $table->timestamps();
            
            // Unique constraint để prevent duplicate likes
            $table->unique(['rating_id', 'user_id'], 'unique_rating_like');
            
            // Indexes
            $table->index('user_id', 'idx_rating_likes_user');
            $table->index(['rating_id', 'created_at'], 'idx_rating_likes_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_rating_likes');
    }
};
