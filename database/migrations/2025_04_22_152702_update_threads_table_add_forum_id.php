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
            $table->unsignedBigInteger('forum_id')->after('user_id')->nullable();
            $table->boolean('is_featured')->after('is_locked')->default(false);

            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['forum_id']);
            $table->dropColumn('forum_id');
            $table->dropColumn('is_featured');
        });
    }
};
