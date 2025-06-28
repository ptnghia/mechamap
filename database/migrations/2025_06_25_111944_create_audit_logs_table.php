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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // User information
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->default('user'); // admin, user

            // Action details
            $table->string('action'); // create, update, delete, view, login, etc.
            $table->string('resource'); // product, user, order, etc.
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->json('details')->nullable(); // Additional action details

            // Risk assessment
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');

            // Request information
            $table->string('ip_address', 45)->nullable(); // IPv6 support
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->string('session_id')->nullable();

            // Additional metadata
            $table->json('metadata')->nullable(); // Extra context data
            $table->timestamp('created_at');

            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['resource', 'resource_id']);
            $table->index(['risk_level', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
