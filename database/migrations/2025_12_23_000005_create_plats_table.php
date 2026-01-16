<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatsTable extends Migration
{
    public function up()
    {
        Schema::create('plats', function (Blueprint $table) {
            $table->bigIncrements('id_plat');
            $table->unsignedBigInteger('id_vendeur');
            $table->unsignedInteger('id_categorie')->nullable();
            $table->string('nom_plat', 150);
            $table->text('description')->nullable();
            $table->decimal('prix', 10, 2);
            $table->string('devise', 3)->default('XOF');
            $table->boolean('disponible')->default(true);
            $table->boolean('stock_limite')->default(false);
            $table->integer('quantite_disponible')->nullable();
            $table->integer('temps_preparation_min')->default(15);
            $table->string('image_principale', 255)->nullable();
            $table->json('images_supplementaires')->nullable();
            $table->integer('nombre_commandes')->default(0);
            $table->integer('nombre_vues')->default(0);
            $table->boolean('en_promotion')->default(false);
            $table->decimal('prix_promotion', 10, 2)->nullable();
            $table->timestamp('date_debut_promotion')->nullable();
            $table->timestamp('date_fin_promotion')->nullable();
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_modification')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->foreign('id_categorie')->references('id_categorie')->on('categories_plats')->onDelete('set null');

            $table->index('id_vendeur');
            $table->index('id_categorie');
            $table->index('disponible');
            $table->index('nombre_commandes');
            $table->index('en_promotion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plats');
    }
}
