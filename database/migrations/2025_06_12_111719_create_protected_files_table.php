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
        Schema::create('protected_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('technical_products')->onDelete('cascade');

            // File Information
            $table->string('original_filename');
            $table->string('encrypted_filename'); // Hashed filename on disk
            $table->string('file_path', 500); // Encrypted storage path
            $table->bigInteger('file_size'); // File size in bytes
            $table->string('mime_type', 100);
            $table->string('file_hash', 128); // SHA-256 for integrity

            // File Categories
            $table->enum('file_type', ['cad_file', 'documentation', 'calculation', 'tutorial', 'sample'])
                  ->default('cad_file');
            $table->string('software_required', 100)->nullable(); // "SolidWorks 2020+", "AutoCAD 2019+"
            $table->text('description')->nullable();

            // Security
            $table->string('encryption_key', 128); // Unique per file
            $table->string('encryption_method', 50)->default('AES-256-CBC');
            $table->enum('access_level', ['preview', 'sample', 'full_access'])->default('full_access');

            // Access Control
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'file_type']);
            $table->index('encrypted_filename');
            $table->index(['access_level', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protected_files');
    }
};
