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
        Schema::create('user_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('visitable_id');
            $table->string('visitable_type'); // threads, profiles
            $table->timestamp('last_visit_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Đảm bảo mỗi người dùng chỉ có một bản ghi cho mỗi item
            $table->unique(['user_id', 'visitable_id', 'visitable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_visits');
    }
};
