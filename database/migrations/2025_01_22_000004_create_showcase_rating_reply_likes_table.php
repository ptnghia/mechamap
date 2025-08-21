<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tạo bảng showcase_rating_reply_likes để hỗ trợ:
     * - Like/unlike replies của đánh giá
     * - Tracking user likes cho replies
     * - Prevent duplicate likes
     */
    public function up(): void
    {
        Schema::create('showcase_rating_reply_likes', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('reply_id')
                ->constrained('showcase_rating_replies')
                ->onDelete('cascade')
                ->comment('ID của reply được like');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID của user thực hiện like');
            
            $table->timestamps();
            
            // Unique constraint để prevent duplicate likes
            $table->unique(['reply_id', 'user_id'], 'unique_reply_like');
            
            // Indexes
            $table->index('user_id', 'idx_reply_likes_user');
            $table->index(['reply_id', 'created_at'], 'idx_reply_likes_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_rating_reply_likes');
    }
};
