<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('groupe_variantes', function (Blueprint $table) {
            $table->id('id_groupe');
            $table->unsignedBigInteger('id_plat');
            $table->string('nom'); // Ex: Taille, Sauce, Suppléments
            $table->boolean('obligatoire')->default(false);
            $table->boolean('choix_multiple')->default(false);
            $table->integer('min_choix')->default(0); // 0 si pas obligatoire
            $table->integer('max_choix')->default(1); // 1 pour choix unique
            $table->timestamps();

            $table->foreign('id_plat')->references('id_plat')->on('plats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('groupe_variantes');
    }
};
