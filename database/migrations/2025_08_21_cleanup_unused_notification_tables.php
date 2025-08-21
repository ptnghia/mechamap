<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Clean up unused notification tables to avoid confusion
     */
    public function up(): void
    {
        echo "🧹 Cleaning up unused notification tables...\n";

        // List of unused tables to drop
        $unusedTables = [
            'notification_ab_test_events',
            'notification_ab_test_participants', 
            'notification_ab_tests',
            'notification_templates',
            'notification_preferences'
        ];

        foreach ($unusedTables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                echo "📋 Dropping {$table} (had {$count} records)...\n";
                Schema::dropIfExists($table);
                echo "✅ {$table} dropped successfully\n";
            } else {
                echo "⚠️ {$table} does not exist, skipping\n";
            }
        }

        echo "🎉 Notification tables cleanup completed!\n";
        echo "\n📊 Remaining notification tables:\n";
        echo "- custom_notifications (main table)\n";
        echo "- notification_logs (for debugging)\n";
        echo "- notifications (Laravel standard, minimal usage)\n";
    }

    /**
     * Reverse the migrations.
     * Recreate the dropped tables if needed
     */
    public function down(): void
    {
        echo "🔄 Recreating notification tables...\n";

        // Recreate notification_preferences table
        if (!Schema::hasTable('notification_preferences')) {
            Schema::create('notification_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->json('preferences');
                $table->json('channels');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique('user_id');
                $table->index('is_active');
            });
            echo "✅ notification_preferences table recreated\n";
        }

        // Recreate notification_templates table
        if (!Schema::hasTable('notification_templates')) {
            Schema::create('notification_templates', function (Blueprint $table) {
                $table->id();
                $table->string('type')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->json('channels');
                $table->json('email_template')->nullable();
                $table->json('database_template')->nullable();
                $table->json('broadcast_template')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('type');
                $table->index('is_active');
            });
            echo "✅ notification_templates table recreated\n";
        }

        // Recreate A/B testing tables
        if (!Schema::hasTable('notification_ab_tests')) {
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
                $table->decimal('confidence_level', 3, 2)->default(0.95);
                $table->decimal('statistical_power', 3, 2)->default(0.80);
                $table->json('results')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamp('concluded_at')->nullable();
                $table->string('conclusion_reason')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index(['notification_type', 'status']);
                $table->index(['status', 'start_date', 'end_date']);
            });
            echo "✅ notification_ab_tests table recreated\n";
        }

        if (!Schema::hasTable('notification_ab_test_participants')) {
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
            echo "✅ notification_ab_test_participants table recreated\n";
        }

        if (!Schema::hasTable('notification_ab_test_events')) {
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
                $table->index(['participant_id', 'event_type']);
                $table->index(['event_type', 'occurred_at']);
                $table->index('occurred_at');
            });
            echo "✅ notification_ab_test_events table recreated\n";
        }

        echo "🎉 Notification tables recreation completed!\n";
    }
};
