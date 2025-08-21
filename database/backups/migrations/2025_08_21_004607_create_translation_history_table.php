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
        Schema::create('translation_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('translation_id');
            $table->text('old_content')->nullable();
            $table->text('new_content');
            $table->unsignedBigInteger('changed_by');
            $table->string('change_reason')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('translation_id')->references('id')->on('translations')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index('translation_id');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_history');
    }
};
