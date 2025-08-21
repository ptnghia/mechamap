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
        // Create notification_ab_tests table
        Schema::create('notification_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('test_type', ['title', 'message', 'timing', 'priority', 'template', 'frequency']);
            $table->string('notification_type');
            $table->enum('status', ['draft', 'active', 'paused', 'concluded', 'cancelled'])->default('draft');
            $table->json('variants');
            $table->json('traffic_split');
            $table->json('target_metrics');
            $table->json('segmentation_rules')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('min_sample_size')->default(100);
            $table->decimal('confidence_level', 5, 2)->default(95.00);
            $table->decimal('statistical_significance', 5, 4)->default(0.0500);
            $table->boolean('auto_conclude')->default(true);
            $table->json('results')->nullable();
            $table->string('winner_variant')->nullable();
            $table->decimal('statistical_confidence', 5, 2)->nullable();
            $table->decimal('effect_size', 5, 4)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('concluded_at')->nullable();
            $table->string('conclusion_reason')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['notification_type', 'status']);
            $table->index(['status', 'start_date', 'end_date']);
        });

        // Create notification_ab_test_participants table
        Schema::create('notification_ab_test_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ab_test_id');
            $table->unsignedBigInteger('user_id');
            $table->string('variant');
            $table->timestamp('assigned_at');
            $table->timestamp('first_notification_sent_at')->nullable();
            $table->timestamp('last_notification_sent_at')->nullable();
            $table->integer('total_notifications_sent')->default(0);
            $table->integer('total_notifications_opened')->default(0);
            $table->integer('total_notifications_clicked')->default(0);
            $table->integer('total_notifications_dismissed')->default(0);
            $table->integer('total_actions_taken')->default(0);
            $table->decimal('engagement_score', 5, 2)->default(0);
            $table->boolean('conversion_achieved')->default(false);
            $table->decimal('conversion_value', 10, 2)->default(0);
            $table->timestamp('opted_out_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('ab_test_id')->references('id')->on('notification_ab_tests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['ab_test_id', 'user_id']);
            $table->index(['ab_test_id', 'variant']);
            $table->index(['user_id', 'assigned_at']);
        });

        // Create notification_ab_test_events table
        Schema::create('notification_ab_test_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('notification_id')->nullable();
            $table->string('event_type');
            $table->json('event_data')->nullable();
            $table->timestamp('occurred_at');
            $table->string('session_id')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('notification_ab_test_participants')->onDelete('cascade');
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('set null');
            $table->index(['participant_id', 'event_type']);
            $table->index(['event_type', 'occurred_at']);
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_ab_test_events');
        Schema::dropIfExists('notification_ab_test_participants');
        Schema::dropIfExists('notification_ab_tests');
    }
};
