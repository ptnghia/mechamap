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
        Schema::table('thread_bookmarks', function (Blueprint $table) {
            $table->string('folder')->nullable()->after('thread_id')->comment('Folder name for organizing bookmarks');
            $table->text('notes')->nullable()->after('folder')->comment('User notes for the bookmark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thread_bookmarks', function (Blueprint $table) {
            $table->dropColumn(['folder', 'notes']);
        });
    }
};
