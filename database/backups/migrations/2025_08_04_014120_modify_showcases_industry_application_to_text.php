<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thay đổi cột industry_application từ enum thành text để hỗ trợ nhiều giá trị
     */
    public function up(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            // Xóa index có chứa cột industry_application trước
            $table->dropIndex('showcases_industry_category');
        });

        Schema::table('showcases', function (Blueprint $table) {
            // Thay đổi cột industry_application từ enum thành text
            $table->text('industry_application')->nullable()->change();
        });

        Schema::table('showcases', function (Blueprint $table) {
            // Tạo lại index với độ dài giới hạn cho text column
            $table->index(['category', 'industry_application(100)'], 'showcases_industry_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            // Xóa index text trước
            $table->dropIndex('showcases_industry_category');
        });

        Schema::table('showcases', function (Blueprint $table) {
            // Khôi phục lại enum (chỉ nên dùng nếu dữ liệu tương thích)
            $table->enum('industry_application', ['automotive', 'aerospace', 'manufacturing', 'energy', 'construction', 'marine', 'electronics', 'medical', 'general'])->nullable()->change();
        });

        Schema::table('showcases', function (Blueprint $table) {
            // Tạo lại index enum
            $table->index(['category', 'industry_application'], 'showcases_industry_category');
        });
    }
};
