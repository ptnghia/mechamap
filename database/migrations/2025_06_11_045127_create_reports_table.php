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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reportable_type'); // Polymorphic type
            $table->unsignedBigInteger('reportable_id'); // Polymorphic ID
            $table->string('reason');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index(['reportable_type', 'reportable_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
