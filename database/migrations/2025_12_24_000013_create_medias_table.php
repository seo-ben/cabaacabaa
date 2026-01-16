<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediasTable extends Migration
{
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_vendeur')->nullable();
            $table->unsignedBigInteger('id_plat')->nullable();

            $table->enum('type', ['image','video','other'])->default('image');
            $table->string('chemin', 1024); // path or url
            $table->string('titre', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->foreign('id_plat')->references('id_plat')->on('plats')->onDelete('cascade');

            $table->index(['id_vendeur','ordre']);
            $table->index(['id_plat','ordre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
