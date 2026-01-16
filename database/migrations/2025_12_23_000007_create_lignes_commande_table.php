<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLignesCommandeTable extends Migration
{
    public function up()
    {
        Schema::create('lignes_commande', function (Blueprint $table) {
            $table->bigIncrements('id_ligne');
            $table->unsignedBigInteger('id_commande');
            $table->unsignedBigInteger('id_plat');
            $table->string('nom_plat_snapshot', 150);
            $table->integer('quantite')->default(1);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('sous_total', 10, 2);
            $table->text('notes')->nullable();

            $table->foreign('id_commande')->references('id_commande')->on('commandes')->onDelete('cascade');
            $table->foreign('id_plat')->references('id_plat')->on('plats')->onDelete('restrict');

            $table->index('id_commande');
            $table->index('id_plat');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lignes_commande');
    }
}
