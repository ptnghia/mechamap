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
        // Main users table with all consolidated fields
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('role')->default('member'); // member, senior, moderator, admin
            $table->json('permissions')->nullable();
            $table->string('status')->default('Registered'); // Registered, active, banned

            // Profile fields
            $table->string('avatar')->nullable();
            $table->text('about_me')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->text('signature')->nullable();

            // Points & activity tracking
            $table->integer('points')->default(0);
            $table->integer('reaction_score')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->text('last_activity')->nullable();
            $table->tinyInteger('setup_progress')->default(0);

            // Authentication
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Ban management
            $table->timestamp('banned_at')->nullable();
            $table->string('banned_reason')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['name']);
            $table->index(['username']);
            $table->index(['status', 'role']);
            $table->index(['last_seen_at']);
        });

        // Password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // User sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
