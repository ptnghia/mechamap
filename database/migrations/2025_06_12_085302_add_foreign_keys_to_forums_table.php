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
        Schema::table('forums', function (Blueprint $table) {
            // Add foreign key constraints to existing columns
            $table->foreign('last_thread_id')->references('id')->on('threads')->onDelete('set null');
            $table->foreign('last_post_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            $table->dropForeign(['last_thread_id']);
            $table->dropForeign(['last_post_user_id']);
        });
    }
};
