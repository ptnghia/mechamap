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
        // User Activities Table
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
        });

        // User Visits Table
        Schema::create('user_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('visitable_id');
            $table->string('visitable_type');
            $table->timestamp('last_visit_at');

            $table->unique(['user_id', 'visitable_id', 'visitable_type']);
        });

        // Search Logs Table
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('results_count')->default(0);
            $table->integer('response_time_ms')->default(0);
            $table->json('filters')->nullable();
            $table->string('content_type')->nullable();
            $table->timestamps();

            $table->index(['query', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['created_at']);
            $table->index(['results_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('user_visits');
        Schema::dropIfExists('user_activities');
    }
};
