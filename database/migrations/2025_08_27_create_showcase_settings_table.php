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
        Schema::create('showcase_settings', function (Blueprint $table) {
            $table->id();
            
            // Setting identification
            $table->string('key')->unique()
                ->comment('Unique key for the setting (e.g., project_types, software_options)');
            
            $table->string('name')
                ->comment('Human readable name for admin interface');
            
            $table->text('description')->nullable()
                ->comment('Description of what this setting controls');
            
            // Setting data
            $table->json('options')
                ->comment('JSON array of available options with translations');
            
            $table->json('default_value')->nullable()
                ->comment('Default selected value(s)');
            
            // Setting configuration
            $table->enum('input_type', ['select', 'multiselect', 'checkbox', 'radio', 'tags'])
                ->default('select')
                ->comment('Type of input control for admin interface');
            
            $table->boolean('is_multiple')->default(false)
                ->comment('Whether multiple values can be selected');
            
            $table->boolean('is_required')->default(false)
                ->comment('Whether this setting is required when creating showcase');
            
            $table->boolean('is_searchable')->default(true)
                ->comment('Whether this setting can be used in search filters');
            
            $table->boolean('is_active')->default(true)
                ->comment('Whether this setting is currently active');
            
            // Display configuration
            $table->integer('sort_order')->default(0)
                ->comment('Order for displaying in forms and filters');
            
            $table->string('group')->nullable()
                ->comment('Group name for organizing settings (e.g., technical, classification)');
            
            $table->string('icon')->nullable()
                ->comment('Icon class for UI display');
            
            // Metadata
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'sort_order'], 'idx_settings_active_order');
            $table->index(['group', 'sort_order'], 'idx_settings_group_order');
            $table->index(['is_searchable', 'is_active'], 'idx_settings_searchable');
            $table->index('key', 'idx_settings_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_settings');
    }
};
