<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonesGeographiquesTable extends Migration
{
    public function up()
    {
        Schema::create('zones_geographiques', function (Blueprint $table) {
            $table->increments('id_zone');
            $table->string('nom_zone', 100);
            $table->string('ville', 100);
            $table->string('quartier', 100)->nullable();
            $table->decimal('latitude_centre', 10, 8);
            $table->decimal('longitude_centre', 11, 8);
            $table->decimal('rayon_km', 5, 2)->default(2.0);
            $table->integer('population_estimee')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamp('date_creation')->useCurrent();

            $table->index('ville');
            $table->index(['latitude_centre', 'longitude_centre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('zones_geographiques');
    }
}
