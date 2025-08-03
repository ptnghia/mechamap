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
        // First, update existing records with empty slugs
        DB::statement("
            UPDATE showcases
            SET slug = CONCAT(
                LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(title, ' ', '-'), 'đ', 'd'), 'Đ', 'D'), 'ă', 'a'), 'â', 'a')),
                '-',
                id
            )
            WHERE slug IS NULL OR slug = ''
        ");

        // Then modify the column to have a default value
        Schema::table('showcases', function (Blueprint $table) {
            $table->string('slug')->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            $table->string('slug')->default(null)->change();
        });
    }
};
