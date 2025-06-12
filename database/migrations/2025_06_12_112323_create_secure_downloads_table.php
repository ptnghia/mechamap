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
        Schema::create('secure_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('product_purchases')->onDelete('cascade');
            $table->foreignId('protected_file_id')->constrained('protected_files')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Download Security
            $table->string('download_token', 128)->unique();
            $table->string('download_url', 500); // Temporary signed URL
            $table->timestamp('expires_at'); // URL expiration (usually 24 hours)

            // Tracking
            $table->timestamp('downloaded_at')->nullable();
            $table->string('download_ip', 45)->nullable(); // Support IPv6
            $table->text('user_agent')->nullable();
            $table->bigInteger('download_size')->nullable();
            $table->integer('download_duration_seconds')->nullable();

            // Security
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_verified')->default(false); // File integrity verified
            $table->string('failure_reason')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['download_token', 'expires_at']);
            $table->index(['user_id', 'downloaded_at']);
            $table->index(['purchase_id', 'protected_file_id']);
            $table->index(['expires_at', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secure_downloads');
    }
};
