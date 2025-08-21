<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Xóa index chứa cột industry_application trước
        DB::statement('ALTER TABLE showcases DROP INDEX showcases_professional_search');

        // Sử dụng raw SQL để thay đổi cột
        DB::statement('ALTER TABLE showcases MODIFY COLUMN industry_application TEXT NULL');

        // Tạo lại index với prefix length cho text column
        DB::statement('ALTER TABLE showcases ADD INDEX showcases_professional_search (industry_application(100), software_used, is_public)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa index text
        DB::statement('ALTER TABLE showcases DROP INDEX showcases_professional_search');

        // Khôi phục lại enum
        DB::statement("ALTER TABLE showcases MODIFY COLUMN industry_application ENUM('automotive', 'aerospace', 'manufacturing', 'energy', 'construction', 'marine', 'electronics', 'medical', 'general') NULL");

        // Tạo lại index enum
        DB::statement('ALTER TABLE showcases ADD INDEX showcases_professional_search (industry_application, software_used, is_public)');
    }
};
