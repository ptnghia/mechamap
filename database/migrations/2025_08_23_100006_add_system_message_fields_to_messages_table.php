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
        Schema::table('messages', function (Blueprint $table) {
            // System message fields for group activities
            $table->boolean('is_system_message')->default(false);
            $table->string('system_message_type', 50)->nullable(); // 'member_joined', 'member_left', 'role_changed'
            $table->json('system_message_data')->nullable(); // Additional data for system messages
            
            // Indexes
            $table->index('is_system_message');
            $table->index(['conversation_id', 'is_system_message']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'is_system_message',
                'system_message_type',
                'system_message_data'
            ]);
        });
    }
};
