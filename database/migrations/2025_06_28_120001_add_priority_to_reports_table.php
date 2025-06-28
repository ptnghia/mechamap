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
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
            $table->index(['priority', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['priority', 'status']);
            $table->dropColumn('priority');
        });
    }
};
