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
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('location');
            $table->dropColumn('usage');
            $table->dropColumn('floors');
            $table->dropColumn('participant_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('usage')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('participant_count')->default(0);
        });
    }
};
