<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_vendeur');

            $table->string('type_section', 50); // texte, image, galerie, liste, info
            $table->string('titre', 150)->nullable();
            $table->text('contenu')->nullable(); // JSON ou HTML selon usage
            $table->integer('position')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->index(['id_vendeur','position']);
            $table->index('type_section');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
