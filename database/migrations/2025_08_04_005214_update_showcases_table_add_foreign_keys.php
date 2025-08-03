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
        Schema::table('showcases', function (Blueprint $table) {
            // Add foreign key columns
            $table->unsignedBigInteger('showcase_category_id')->nullable()->after('id');
            $table->unsignedBigInteger('showcase_type_id')->nullable()->after('showcase_category_id');

            // Add foreign key constraints
            $table->foreign('showcase_category_id')->references('id')->on('showcase_categories')->onDelete('set null');
            $table->foreign('showcase_type_id')->references('id')->on('showcase_types')->onDelete('set null');

            // Add indexes
            $table->index('showcase_category_id');
            $table->index('showcase_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['showcase_category_id']);
            $table->dropForeign(['showcase_type_id']);

            // Drop columns
            $table->dropColumn(['showcase_category_id', 'showcase_type_id']);
        });
    }
};
