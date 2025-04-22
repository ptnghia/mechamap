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
            $table->string('location')->nullable()->after('content');
            $table->string('usage')->nullable()->after('location');
            $table->integer('floors')->nullable()->after('usage');
            $table->string('status')->nullable()->after('floors');
            $table->integer('participant_count')->default(0)->after('view_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'usage',
                'floors',
                'status',
                'participant_count'
            ]);
        });
    }
};
