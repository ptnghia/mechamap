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
        Schema::create('showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('showcaseable'); // For polymorphic relations
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            // Ensure a user can only showcase an item once
            $table->unique(['user_id', 'showcaseable_id', 'showcaseable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcases');
    }
};
