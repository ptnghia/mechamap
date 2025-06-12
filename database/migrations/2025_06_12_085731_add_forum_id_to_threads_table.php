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
            // Add forum_id column after user_id (nullable first)
            $table->foreignId('forum_id')->nullable()->after('user_id')
                ->comment('Forum mà thread thuộc về')
                ->constrained()->onDelete('cascade');

            // Add index for performance
            $table->index(['forum_id', 'created_at'], 'threads_forum_timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['forum_id']);
            $table->dropIndex('threads_forum_timeline');
            $table->dropColumn('forum_id');
        });
    }
};
