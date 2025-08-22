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
        // Drop alerts table after successful migration to custom_notifications
        if (Schema::hasTable('alerts')) {
            Schema::dropIfExists('alerts');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate alerts table structure for rollback
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('info'); // info, success, warning, error, system_update, etc.
            $table->timestamp('read_at')->nullable();

            // Polymorphic relationship to any model
            $table->string('alertable_type')->nullable();
            $table->unsignedBigInteger('alertable_id')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['alertable_type', 'alertable_id']);
            $table->index(['type', 'created_at']);
        });
    }
};
