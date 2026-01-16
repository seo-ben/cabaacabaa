<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvisEvaluationsTable extends Migration
{
    public function up()
    {
        Schema::create('avis_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id_avis');
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_vendeur');
            $table->unsignedBigInteger('id_commande')->nullable();
            $table->tinyInteger('note');
            $table->text('commentaire')->nullable();
            $table->tinyInteger('note_qualite')->nullable();
            $table->tinyInteger('note_rapidite')->nullable();
            $table->tinyInteger('note_rapport_qualite_prix')->nullable();
            $table->enum('statut_avis', ['visible','masque','en_attente_moderation','signale'])->default('visible');
            $table->timestamp('date_publication')->useCurrent();
            $table->timestamp('date_modification')->nullable();
            $table->text('reponse_vendeur')->nullable();
            $table->timestamp('date_reponse')->nullable();

            $table->foreign('id_client')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->foreign('id_commande')->references('id_commande')->on('commandes')->onDelete('set null');

            $table->unique(['id_client','id_vendeur']);
            $table->index('id_vendeur');
            $table->index('note');
            $table->index('statut_avis');
        });
    }

    public function down()
    {
        Schema::dropIfExists('avis_evaluations');
    }
}
