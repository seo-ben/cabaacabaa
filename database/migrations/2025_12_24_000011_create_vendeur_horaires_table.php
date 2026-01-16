<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendeurHorairesTable extends Migration
{
    public function up()
    {
        Schema::create('vendeur_horaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_vendeur');

            // 0 = dimanche, 1 = lundi ... 6 = samedi (aligner selon préférence)
            $table->tinyInteger('jour_semaine')->unsigned();
            $table->time('heure_ouverture')->nullable();
            $table->time('heure_fermeture')->nullable();
            $table->boolean('ferme')->default(false);
            $table->json('exceptions')->nullable(); // dates spéciales, plages, objets
            $table->integer('ordre')->default(0);
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->index(['id_vendeur','jour_semaine']);
            $table->index('ferme');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendeur_horaires');
    }
}
