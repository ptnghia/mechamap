<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix marketplace_shopping_carts.uuid column (if table exists)
        if (Schema::hasTable('marketplace_shopping_carts') && Schema::hasColumn('marketplace_shopping_carts', 'uuid')) {
            // Check current column type
            $columnType = DB::select("SHOW COLUMNS FROM marketplace_shopping_carts WHERE Field = 'uuid'")[0]->Type ?? '';

            if (strtolower($columnType) === 'uuid') {
                // First, generate unique UUIDs for all records
                $records = DB::table('marketplace_shopping_carts')->get();
                foreach ($records as $record) {
                    DB::table('marketplace_shopping_carts')
                        ->where('id', $record->id)
                        ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
                }

                // Drop and recreate column
                Schema::table('marketplace_shopping_carts', function (Blueprint $table) {
                    $table->dropColumn('uuid');
                });

                Schema::table('marketplace_shopping_carts', function (Blueprint $table) {
                    $table->char('uuid', 36)->unique()->after('id');
                });

                // Restore UUIDs
                foreach ($records as $record) {
                    $newUuid = (string) \Illuminate\Support\Str::uuid();
                    DB::table('marketplace_shopping_carts')
                        ->where('id', $record->id)
                        ->update(['uuid' => $newUuid]);
                }

                echo "✅ Fixed marketplace_shopping_carts.uuid column\n";
            } else {
                echo "ℹ️ marketplace_shopping_carts.uuid already using CHAR(36)\n";
            }
        }

        // Fix notifications.id column (Primary Key) (if table exists)
        if (Schema::hasTable('notifications')) {
            $columnType = DB::select("SHOW COLUMNS FROM notifications WHERE Field = 'id'")[0]->Type ?? '';

            if (strtolower($columnType) === 'uuid') {
                DB::statement('ALTER TABLE notifications MODIFY COLUMN id CHAR(36) NOT NULL');
                echo "✅ Fixed notifications.id column\n";
            } else {
                echo "ℹ️ notifications.id already using CHAR(36)\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert marketplace_shopping_carts.uuid back to UUID type
        Schema::table('marketplace_shopping_carts', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('marketplace_shopping_carts', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
        });

        // Revert notifications.id back to UUID type
        DB::statement('ALTER TABLE notifications MODIFY COLUMN id UUID NOT NULL');
    }
};
