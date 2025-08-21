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
        // Add avatar support to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('icon')
                ->comment('URL của avatar/logo cho danh mục');
            $table->unsignedBigInteger('avatar_media_id')->nullable()->after('avatar_url')
                ->comment('ID của media avatar trong bảng media');
            $table->string('banner_url')->nullable()->after('avatar_media_id')
                ->comment('URL banner cho danh mục');
            $table->unsignedBigInteger('banner_media_id')->nullable()->after('banner_url')
                ->comment('ID của media banner trong bảng media');

            // Add foreign keys
            $table->foreign('avatar_media_id')->references('id')->on('media')->onDelete('set null');
            $table->foreign('banner_media_id')->references('id')->on('media')->onDelete('set null');
        });

        // Add avatar support to forums table
        Schema::table('forums', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('description')
                ->comment('URL của avatar/logo cho forum');
            $table->unsignedBigInteger('avatar_media_id')->nullable()->after('avatar_url')
                ->comment('ID của media avatar trong bảng media');
            $table->string('banner_url')->nullable()->after('avatar_media_id')
                ->comment('URL banner cho forum');
            $table->unsignedBigInteger('banner_media_id')->nullable()->after('banner_url')
                ->comment('ID của media banner trong bảng media');
            $table->json('gallery_media_ids')->nullable()->after('banner_media_id')
                ->comment('Array các ID media cho gallery của forum');

            // Add foreign keys
            $table->foreign('avatar_media_id')->references('id')->on('media')->onDelete('set null');
            $table->foreign('banner_media_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['avatar_media_id']);
            $table->dropForeign(['banner_media_id']);
            $table->dropColumn(['avatar_url', 'avatar_media_id', 'banner_url', 'banner_media_id']);
        });

        Schema::table('forums', function (Blueprint $table) {
            $table->dropForeign(['avatar_media_id']);
            $table->dropForeign(['banner_media_id']);
            $table->dropColumn([
                'avatar_url',
                'avatar_media_id',
                'banner_url',
                'banner_media_id',
                'gallery_media_ids'
            ]);
        });
    }
};
