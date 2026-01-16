<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendeursTable extends Migration
{
    public function up()
    {
        Schema::create('vendeurs', function (Blueprint $table) {
            $table->bigIncrements('id_vendeur');
            $table->unsignedBigInteger('id_user')->unique();
            $table->unsignedInteger('id_zone')->nullable();
            $table->string('nom_commercial', 150);
            $table->text('description')->nullable();
            $table->enum('type_vendeur', ['restaurant','cantine','fast_food','vendeur_independant','patisserie','autre']);
            $table->text('adresse_complete');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->json('horaires_ouverture')->nullable();
            $table->string('telephone_commercial', 20)->nullable();
            $table->enum('statut_verification', ['non_verifie','en_cours','verifie','rejete'])->default('non_verifie');
            $table->timestamp('date_verification')->nullable();
            $table->decimal('note_moyenne', 3, 2)->default(0.00);
            $table->integer('nombre_avis')->default(0);
            $table->integer('nombre_commandes_total')->default(0);
            $table->integer('nombre_commandes_mois')->default(0);
            $table->string('image_principale', 255)->nullable();
            $table->json('images_galerie')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamp('date_inscription')->useCurrent();
            $table->timestamp('date_derniere_modification')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_zone')->references('id_zone')->on('zones_geographiques')->onDelete('set null');

            $table->index('id_zone');
            $table->index(['latitude','longitude']);
            $table->index('type_vendeur');
            $table->index('statut_verification');
            $table->index('note_moyenne');
            $table->index('actif');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendeurs');
    }
}
