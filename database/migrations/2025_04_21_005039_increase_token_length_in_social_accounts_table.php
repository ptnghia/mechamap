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
        Schema::table('social_accounts', function (Blueprint $table) {
            // Thay đổi kiểu dữ liệu của cột provider_token và provider_refresh_token thành text
            $table->text('provider_token')->nullable()->change();
            $table->text('provider_refresh_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            // Khôi phục kiểu dữ liệu của cột provider_token và provider_refresh_token thành string
            $table->string('provider_token')->nullable()->change();
            $table->string('provider_refresh_token')->nullable()->change();
        });
    }
};
