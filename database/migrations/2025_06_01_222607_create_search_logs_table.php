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
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query'); // Từ khóa tìm kiếm
            $table->unsignedBigInteger('user_id')->nullable(); // ID người dùng (nullable cho khách vãng lai)
            $table->string('ip_address')->nullable(); // Địa chỉ IP
            $table->string('user_agent')->nullable(); // User agent
            $table->integer('results_count')->default(0); // Số kết quả trả về
            $table->integer('response_time_ms')->default(0); // Thời gian phản hồi (ms)
            $table->json('filters')->nullable(); // Lưu trữ các bộ lọc áp dụng
            $table->string('content_type')->nullable(); // Loại nội dung: threads, comments, users
            $table->timestamps();

            // Chỉ mục cho phân tích
            $table->index(['query', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['created_at']);
            $table->index(['results_count']);

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
