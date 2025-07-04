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
        Schema::table('marketplace_products', function (Blueprint $table) {
            $table->json('digital_files')->nullable()->after('download_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_products', function (Blueprint $table) {
            //
        });
    }
};
