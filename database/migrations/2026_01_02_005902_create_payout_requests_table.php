<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id('id_payout');
            $table->unsignedBigInteger('id_vendeur');
            $table->decimal('montant', 10, 2);
            $table->string('methode_paiement'); // ex: CinetPay, FedaPay, Bank, Flooz, T-Money
            $table->string('informations_paiement'); // ex: numéro de téléphone ou RIB
            $table->enum('statut', ['en_attente', 'approuve', 'rejete', 'complete'])->default('en_attente');
            $table->text('notes_admin')->nullable();
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};
