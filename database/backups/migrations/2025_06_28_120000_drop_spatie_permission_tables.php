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
        // Drop Spatie Permission tables in correct order (foreign keys first)
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        
        // Note: Keep personal_access_tokens as it's used by Sanctum
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't recreate these tables as we're moving to Laravel native authorization
        // If needed, restore from backup or re-run the original Spatie migration
    }
};
