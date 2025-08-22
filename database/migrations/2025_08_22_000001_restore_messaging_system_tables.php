<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restore messaging system tables from backup
     */
    public function up(): void
    {
        echo "🔄 Restoring messaging system tables...\n";

        // Conversations table - private message conversations
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable(); // Optional conversation title
                $table->timestamps();
            });
            echo "✅ Created conversations table\n";
        } else {
            echo "⚠️ conversations table already exists, skipping\n";
        }

        // Conversation participants - who's in each conversation
        if (!Schema::hasTable('conversation_participants')) {
            Schema::create('conversation_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('last_read_at')->nullable(); // Last time user read messages
                $table->timestamps();

                $table->unique(['conversation_id', 'user_id']);
                $table->index(['user_id', 'last_read_at']);
            });
            echo "✅ Created conversation_participants table\n";
        } else {
            echo "⚠️ conversation_participants table already exists, skipping\n";
        }

        // Messages table - actual conversation messages
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('content');
                $table->timestamps();

                $table->index(['conversation_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
            });
            echo "✅ Created messages table\n";
        } else {
            echo "⚠️ messages table already exists, skipping\n";
        }

        echo "🎉 Messaging system tables restoration completed!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Dropping messaging system tables...\n";
        
        Schema::dropIfExists('messages');
        echo "✅ Dropped messages table\n";
        
        Schema::dropIfExists('conversation_participants');
        echo "✅ Dropped conversation_participants table\n";
        
        Schema::dropIfExists('conversations');
        echo "✅ Dropped conversations table\n";
        
        echo "🎉 Messaging system tables removal completed!\n";
    }
};
