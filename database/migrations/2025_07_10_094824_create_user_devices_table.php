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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('device_fingerprint')->unique(); // Unique device identifier
            $table->string('device_name')->nullable(); // User-friendly device name
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable(); // Chrome, Firefox, Safari, etc.
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable(); // Windows, macOS, Linux, iOS, Android
            $table->string('platform_version')->nullable();
            $table->string('user_agent')->nullable(); // Full user agent string
            $table->ipAddress('ip_address')->nullable(); // IP address when first seen
            $table->string('country')->nullable(); // Country from IP
            $table->string('city')->nullable(); // City from IP
            $table->boolean('is_trusted')->default(false); // User has marked as trusted
            $table->timestamp('first_seen_at')->nullable(); // When device was first seen
            $table->timestamp('last_seen_at')->nullable(); // Last login from this device
            $table->timestamp('trusted_at')->nullable(); // When user marked as trusted
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'device_fingerprint']);
            $table->index(['user_id', 'is_trusted']);
            $table->index(['user_id', 'last_seen_at']);

            // Add foreign key constraint separately
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
