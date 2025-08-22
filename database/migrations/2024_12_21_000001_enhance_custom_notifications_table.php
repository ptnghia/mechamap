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
        Schema::table('custom_notifications', function (Blueprint $table) {
            // Add new fields for unified notification system
            $table->enum('category', ['system', 'forum', 'marketplace', 'social', 'security'])
                  ->default('system')
                  ->after('type');
            
            $table->json('metadata')->nullable()->after('data');
            
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                  ->default('normal')
                  ->change();
            
            $table->tinyInteger('urgency_level')->default(1)->after('priority');
            
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'archived'])
                  ->default('pending')
                  ->after('urgency_level');
            
            // Polymorphic relationship fields (from alerts table)
            $table->string('notifiable_type')->nullable()->after('read_at');
            $table->bigInteger('notifiable_id')->nullable()->after('notifiable_type');
            
            // Action and interaction fields
            $table->string('action_url', 500)->nullable()->after('notifiable_id');
            $table->string('action_text', 100)->nullable()->after('action_url');
            $table->boolean('requires_action')->default(false)->after('action_text');
            
            // Delivery and channels
            $table->json('delivery_channels')->nullable()->after('requires_action');
            $table->json('sent_via')->nullable()->after('delivery_channels');
            
            // Scheduling and batching
            $table->timestamp('scheduled_at')->nullable()->after('sent_via');
            $table->timestamp('expires_at')->nullable()->after('scheduled_at');
            $table->string('batch_id', 100)->nullable()->after('expires_at');
            
            // Analytics and tracking
            $table->integer('view_count')->default(0)->after('batch_id');
            $table->integer('click_count')->default(0)->after('view_count');
            $table->json('interaction_data')->nullable()->after('click_count');
            
            // Add indexes for performance
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['user_id', 'is_read', 'created_at'], 'idx_user_read');
            $table->index(['type', 'category'], 'idx_type_category');
            $table->index(['priority', 'created_at'], 'idx_priority_created');
            $table->index(['notifiable_type', 'notifiable_id'], 'idx_notifiable');
            $table->index('batch_id', 'idx_batch');
            $table->index('scheduled_at', 'idx_scheduled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_notifications', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_user_status');
            $table->dropIndex('idx_user_read');
            $table->dropIndex('idx_type_category');
            $table->dropIndex('idx_priority_created');
            $table->dropIndex('idx_notifiable');
            $table->dropIndex('idx_batch');
            $table->dropIndex('idx_scheduled');
            
            // Drop columns
            $table->dropColumn([
                'category',
                'metadata',
                'urgency_level',
                'status',
                'notifiable_type',
                'notifiable_id',
                'action_url',
                'action_text',
                'requires_action',
                'delivery_channels',
                'sent_via',
                'scheduled_at',
                'expires_at',
                'batch_id',
                'view_count',
                'click_count',
                'interaction_data'
            ]);
            
            // Revert priority enum
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal')->change();
        });
    }
};
