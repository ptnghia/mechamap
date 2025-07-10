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
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('marketplace_products')->onDelete('cascade');
            $table->decimal('old_price', 12, 2);
            $table->decimal('new_price', 12, 2);
            $table->decimal('old_sale_price', 12, 2)->nullable();
            $table->decimal('new_sale_price', 12, 2)->nullable();
            $table->decimal('price_change', 12, 2); // Positive for increase, negative for decrease
            $table->decimal('price_change_percentage', 5, 2); // Percentage change
            $table->enum('change_type', ['increase', 'decrease', 'no_change'])->default('no_change');
            $table->string('reason')->nullable(); // Manual price change reason
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('effective_date')->default(now());
            $table->timestamps();

            // Indexes for performance
            $table->index(['product_id', 'created_at']);
            $table->index(['change_type', 'created_at']);
            $table->index(['effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_history');
    }
};
