<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "🚀 Creating Unified Notification System...\n";

        try {
            // Step 1: Rename existing custom notifications table
            if (Schema::hasTable('notifications') && !Schema::hasTable('custom_notifications')) {
                echo "📋 Renaming custom notifications table...\n";
                Schema::rename('notifications', 'custom_notifications');
                echo "✅ Custom notifications table renamed to 'custom_notifications'\n";
            }

            // Step 2: Ensure Laravel built-in notifications table exists
            if (!Schema::hasTable('notifications')) {
                echo "📋 Creating Laravel built-in notifications table...\n";
                Schema::create('notifications', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->string('type');
                    $table->morphs('notifiable');
                    $table->text('data');
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();

                    $table->index(['notifiable_type', 'notifiable_id']);
                    $table->index('read_at');
                    $table->index('created_at');
                });
                echo "✅ Laravel built-in notifications table created\n";
            } else {
                echo "✅ Laravel built-in notifications table already exists\n";

                // Add missing indexes if they don't exist
                try {
                    $indexes = collect(DB::select('SHOW INDEX FROM notifications'))->pluck('Key_name')->toArray();

                    if (!in_array('notifications_read_at_index', $indexes)) {
                        Schema::table('notifications', function (Blueprint $table) {
                            $table->index('read_at');
                        });
                        echo "✅ Added read_at index\n";
                    }

                    if (!in_array('notifications_created_at_index', $indexes)) {
                        Schema::table('notifications', function (Blueprint $table) {
                            $table->index('created_at');
                        });
                        echo "✅ Added created_at index\n";
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Could not add indexes: " . $e->getMessage() . "\n";
                }
            }

            // Step 3: Create notification preferences table
            if (!Schema::hasTable('notification_preferences')) {
                echo "📋 Creating notification preferences table...\n";
                Schema::create('notification_preferences', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->string('channel'); // email, database, broadcast, sms
                    $table->string('type'); // notification type
                    $table->boolean('enabled')->default(true);
                    $table->json('settings')->nullable(); // additional settings
                    $table->timestamps();

                    $table->unique(['user_id', 'channel', 'type']);
                    $table->index(['user_id', 'enabled']);
                });
                echo "✅ Notification preferences table created\n";
            }

            // Step 4: Create notification templates table
            if (!Schema::hasTable('notification_templates')) {
                echo "📋 Creating notification templates table...\n";
                Schema::create('notification_templates', function (Blueprint $table) {
                    $table->id();
                    $table->string('type')->unique();
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->json('channels')->default('["database"]'); // available channels
                    $table->json('email_template')->nullable(); // email template data
                    $table->json('database_template')->nullable(); // database template data
                    $table->json('broadcast_template')->nullable(); // broadcast template data
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();

                    $table->index('type');
                    $table->index('is_active');
                });
                echo "✅ Notification templates table created\n";
            }

            // Step 5: Create notification logs table for debugging
            if (!Schema::hasTable('notification_logs')) {
                echo "📋 Creating notification logs table...\n";
                Schema::create('notification_logs', function (Blueprint $table) {
                    $table->id();
                    $table->string('notification_id')->nullable(); // UUID from notifications table
                    $table->string('type');
                    $table->string('channel');
                    $table->morphs('notifiable');
                    $table->enum('status', ['pending', 'sent', 'failed', 'delivered']);
                    $table->json('data')->nullable();
                    $table->text('error_message')->nullable();
                    $table->timestamp('sent_at')->nullable();
                    $table->timestamps();

                    $table->index(['notifiable_type', 'notifiable_id']);
                    $table->index(['type', 'channel']);
                    $table->index('status');
                    $table->index('created_at');
                });
                echo "✅ Notification logs table created\n";
            } else {
                echo "✅ Notification logs table already exists\n";
            }

            echo "🎉 Unified Notification System created successfully!\n";

        } catch (\Exception $e) {
            echo "❌ Error creating unified notification system: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Rolling back Unified Notification System...\n";

        try {
            // Drop new tables
            Schema::dropIfExists('notification_logs');
            Schema::dropIfExists('notification_templates');
            Schema::dropIfExists('notification_preferences');
            Schema::dropIfExists('notifications');

            // Restore custom notifications table
            if (Schema::hasTable('custom_notifications') && !Schema::hasTable('notifications')) {
                Schema::rename('custom_notifications', 'notifications');
                echo "✅ Custom notifications table restored\n";
            }

            echo "🎉 Unified Notification System rollback completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error rolling back unified notification system: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
