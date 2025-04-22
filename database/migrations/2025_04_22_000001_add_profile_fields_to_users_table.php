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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('Registered')->after('role');
            $table->text('about_me')->nullable()->after('avatar');
            $table->string('website')->nullable()->after('about_me');
            $table->string('location')->nullable()->after('website');
            $table->text('signature')->nullable()->after('location');
            $table->integer('points')->default(0)->after('signature');
            $table->integer('reaction_score')->default(0)->after('points');
            $table->timestamp('last_seen_at')->nullable()->after('reaction_score');
            $table->text('last_activity')->nullable()->after('last_seen_at');
            $table->tinyInteger('setup_progress')->default(0)->after('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'about_me',
                'website',
                'location',
                'signature',
                'points',
                'reaction_score',
                'last_seen_at',
                'last_activity',
                'setup_progress'
            ]);
        });
    }
};
