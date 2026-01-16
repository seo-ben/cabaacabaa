<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesPlatsTable extends Migration
{
    public function up()
    {
        Schema::create('categories_plats', function (Blueprint $table) {
            $table->increments('id_categorie');
            $table->string('nom_categorie', 50);
            $table->string('description', 255)->nullable();
            $table->string('icone', 100)->nullable();
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('actif')->default(true);
            $table->index('ordre_affichage');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories_plats');
    }
}
