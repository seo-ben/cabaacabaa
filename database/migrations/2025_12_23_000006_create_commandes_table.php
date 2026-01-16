<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->bigIncrements('id_commande');
            $table->string('numero_commande', 20)->unique();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_vendeur');
            $table->enum('statut', [
                'en_attente','confirmee','en_preparation','prete','recuperee','livree','annulee_client','annulee_vendeur','litige'
            ])->default('en_attente');
            $table->enum('type_recuperation', ['emporter','sur_place','livraison'])->default('emporter');
            $table->enum('mode_paiement_prevu', ['espece','qr_code','mobile_money','carte'])->default('espece');
            $table->boolean('paiement_effectue')->default(false);
            $table->decimal('montant_plats', 10, 2);
            $table->decimal('frais_service', 10, 2)->default(0.00);
            $table->decimal('montant_total', 10, 2);
            $table->timestamp('date_commande')->useCurrent();
            $table->timestamp('heure_recuperation_souhaitee')->nullable();
            $table->timestamp('heure_preparation_debut')->nullable();
            $table->timestamp('heure_prete')->nullable();
            $table->timestamp('heure_recuperation_effective')->nullable();
            $table->text('instructions_speciales')->nullable();
            $table->timestamp('date_annulation')->nullable();
            $table->text('raison_annulation')->nullable();

            $table->foreign('id_client')->references('id_user')->on('users')->onDelete('restrict');
            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('restrict');

            $table->index('id_client');
            $table->index('id_vendeur');
            $table->index('statut');
            $table->index('date_commande');
            $table->index('numero_commande');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commandes');
    }
}
