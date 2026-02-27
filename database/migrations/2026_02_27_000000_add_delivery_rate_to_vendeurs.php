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
            $table->integer('delivery_rate_per_km')->nullable()->after('is_busy')->comment('Prix de livraison par KM pour ce vendeur (FCFA)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn('delivery_rate_per_km');
        });
    }
};
