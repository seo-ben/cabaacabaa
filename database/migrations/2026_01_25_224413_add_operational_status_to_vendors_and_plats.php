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
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->boolean('is_busy')->default(false)->after('actif'); // Point 3
        });

        Schema::table('plats', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('disponible'); // Point 1 (Explicit toggle)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });

        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn('is_busy');
        });
    }
};
