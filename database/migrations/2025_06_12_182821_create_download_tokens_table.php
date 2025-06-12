<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('download_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('protected_file_id')->constrained()->onDelete('cascade');
            $table->timestamp('expires_at');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->integer('download_attempts')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['token', 'expires_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['product_purchase_id', 'is_used']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('download_tokens');
    }
};