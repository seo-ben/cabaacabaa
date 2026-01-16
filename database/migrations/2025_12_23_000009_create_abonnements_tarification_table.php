<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonnementsTarificationTable extends Migration
{
    public function up()
    {
        Schema::create('abonnements_tarification', function (Blueprint $table) {
            $table->bigIncrements('id_abonnement');
            $table->unsignedBigInteger('id_vendeur');
            $table->enum('type_tarification', ['pourcentage_commande','forfait_journalier','forfait_mensuel','gratuit']);
            $table->decimal('valeur_tarif', 10, 2)->nullable();
            $table->decimal('pourcentage', 5, 2)->nullable();
            $table->timestamp('date_debut');
            $table->timestamp('date_fin')->nullable();
            $table->integer('duree_jours')->nullable();
            $table->enum('statut', ['actif','expire','suspendu','annule'])->default('actif');
            $table->boolean('periode_essai')->default(false);
            $table->decimal('montant_a_payer', 10, 2)->default(0.00);
            $table->decimal('montant_paye', 10, 2)->default(0.00);
            $table->timestamp('date_dernier_paiement')->nullable();
            $table->timestamp('date_creation')->useCurrent();
            $table->text('notes')->nullable();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');

            $table->index('id_vendeur');
            $table->index('statut');
            $table->index(['date_debut','date_fin']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('abonnements_tarification');
    }
}
