<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update existing threads với correct follow_count và bookmark_count
     */
    public function up(): void
    {
        // Update bookmark_count cho tất cả threads
        DB::statement('
            UPDATE threads
            SET bookmark_count = (
                SELECT COUNT(*)
                FROM thread_bookmarks
                WHERE thread_bookmarks.thread_id = threads.id
            )
        ');

        // Update follow_count cho tất cả threads
        DB::statement('
            UPDATE threads
            SET follow_count = (
                SELECT COUNT(*)
                FROM thread_follows
                WHERE thread_follows.thread_id = threads.id
            )
        ');

        echo "✅ Updated thread counts from existing bookmark and follow data" . PHP_EOL;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset counts về 0
        DB::statement('UPDATE threads SET bookmark_count = 0, follow_count = 0');

        echo "⚠️ Reset all thread counts to 0" . PHP_EOL;
    }
};
