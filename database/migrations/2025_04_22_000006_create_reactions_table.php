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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // like, love, haha, wow, sad, angry
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reactable_id');
            $table->string('reactable_type'); // posts, threads, profile_posts
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Đảm bảo mỗi người dùng chỉ có thể tạo một phản ứng cho mỗi item
            $table->unique(['user_id', 'reactable_id', 'reactable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
