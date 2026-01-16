<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsFinancieresTable extends Migration
{
    public function up()
    {
        Schema::create('transactions_financieres', function (Blueprint $table) {
            $table->bigIncrements('id_transaction');
            $table->unsignedBigInteger('id_commande')->nullable();
            $table->unsignedBigInteger('id_vendeur');
            $table->unsignedBigInteger('id_abonnement')->nullable();
            $table->enum('type_transaction', ['commission_commande','abonnement','remboursement','ajustement']);
            $table->decimal('montant', 10, 2);
            $table->string('devise', 3)->default('XOF');
            $table->enum('statut', ['en_attente','complete','echec','annule'])->default('en_attente');
            $table->timestamp('date_transaction')->useCurrent();
            $table->string('reference_paiement', 100)->nullable();
            $table->text('notes')->nullable();

            $table->foreign('id_commande')->references('id_commande')->on('commandes')->onDelete('set null');
            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('restrict');
            $table->foreign('id_abonnement')->references('id_abonnement')->on('abonnements_tarification')->onDelete('set null');

            $table->index('id_vendeur');
            $table->index('date_transaction');
            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions_financieres');
    }
}
