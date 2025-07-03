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
        Schema::create('showcase_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showcase_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Rating categories (1-5 stars each)
            $table->tinyInteger('technical_quality')->unsigned()->comment('Chất lượng kỹ thuật (1-5)');
            $table->tinyInteger('innovation')->unsigned()->comment('Tính sáng tạo (1-5)');
            $table->tinyInteger('usefulness')->unsigned()->comment('Tính hữu ích (1-5)');
            $table->tinyInteger('documentation')->unsigned()->comment('Chất lượng tài liệu (1-5)');

            // Overall rating (calculated average)
            $table->decimal('overall_rating', 3, 2)->comment('Đánh giá tổng thể (1.00-5.00)');

            // Optional review text
            $table->text('review')->nullable()->comment('Nhận xét chi tiết');

            $table->timestamps();

            // Constraints
            $table->unique(['showcase_id', 'user_id'], 'unique_user_showcase_rating');

            // Indexes
            $table->index(['showcase_id', 'overall_rating']);
            $table->index(['user_id', 'created_at']);
            $table->index(['overall_rating', 'created_at']);

            // Note: Check constraints will be handled in model validation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_ratings');
    }
};
