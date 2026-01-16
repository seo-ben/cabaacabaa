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
        Schema::table('zones_geographiques', function (Blueprint $table) {
            // Add nom if not exists
            if (!Schema::hasColumn('zones_geographiques', 'nom')) {
                $table->string('nom')->nullable()->after('id_zone');
            }

            // Add description if not exists
            if (!Schema::hasColumn('zones_geographiques', 'description')) {
                $table->text('description')->nullable()->after('nom');
            }

            // Add code_postal if not exists
            if (!Schema::hasColumn('zones_geographiques', 'code_postal')) {
                $table->string('code_postal')->nullable()->after('quartier');
            }

            // Add latitude if not exists (as alias for latitude_centre)
            if (!Schema::hasColumn('zones_geographiques', 'latitude')) {
                $table->decimal('latitude', 10, 6)->nullable()->after('code_postal');
            }

            // Add longitude if not exists (as alias for longitude_centre)
            if (!Schema::hasColumn('zones_geographiques', 'longitude')) {
                $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones_geographiques', function (Blueprint $table) {
            if (Schema::hasColumn('zones_geographiques', 'nom')) {
                $table->dropColumn('nom');
            }
            if (Schema::hasColumn('zones_geographiques', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('zones_geographiques', 'code_postal')) {
                $table->dropColumn('code_postal');
            }
            if (Schema::hasColumn('zones_geographiques', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('zones_geographiques', 'longitude')) {
                $table->dropColumn('longitude');
            }
        });
    }
};
