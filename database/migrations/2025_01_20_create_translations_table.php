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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->comment('Translation key (e.g., common.buttons.save)');
            $table->text('content')->comment('Translation content');
            $table->string('locale', 10)->comment('Language code (vi, en, etc.)');
            $table->string('group_name', 100)->comment('Group name (common, admin, etc.)');
            $table->string('namespace', 100)->nullable()->comment('Package namespace');
            $table->boolean('is_active')->default(true)->comment('Is translation active');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who last updated');
            $table->timestamps();

            // Indexes
            $table->unique(['key', 'locale'], 'unique_translation');
            $table->index(['locale', 'group_name'], 'idx_locale_group');
            $table->index(['key', 'locale'], 'idx_key_locale');
            $table->index('is_active', 'idx_active');
            $table->index('group_name', 'idx_group');
            $table->index('locale', 'idx_locale');

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('translation_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('translation_id')->comment('Translation ID');
            $table->text('old_content')->nullable()->comment('Previous content');
            $table->text('new_content')->comment('New content');
            $table->unsignedBigInteger('changed_by')->comment('User who made change');
            $table->string('change_reason')->nullable()->comment('Reason for change');
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('translation_id', 'idx_translation_id');
            $table->index('changed_by', 'idx_changed_by');
            $table->index('created_at', 'idx_created_at');

            // Foreign keys
            $table->foreign('translation_id')->references('id')->on('translations')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('translation_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->comment('Import filename');
            $table->integer('total_keys')->comment('Total keys in import');
            $table->integer('imported_keys')->comment('Successfully imported keys');
            $table->integer('failed_keys')->default(0)->comment('Failed import keys');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->unsignedBigInteger('imported_by')->comment('User who imported');
            $table->text('error_log')->nullable()->comment('Import errors');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();

            // Indexes
            $table->index('status', 'idx_status');
            $table->index('imported_by', 'idx_imported_by');
            $table->index('created_at', 'idx_created_at');

            // Foreign keys
            $table->foreign('imported_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_imports');
        Schema::dropIfExists('translation_history');
        Schema::dropIfExists('translations');
    }
};
