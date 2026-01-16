<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavorisClientsTable extends Migration
{
    public function up()
    {
        Schema::create('favoris_clients', function (Blueprint $table) {
            $table->bigIncrements('id_favori');
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_vendeur');
            $table->timestamp('date_ajout')->useCurrent();

            $table->foreign('id_client')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');

            $table->unique(['id_client','id_vendeur'], 'unique_favori');
            $table->index('id_client');
        });
    }

    public function down()
    {
        Schema::dropIfExists('favoris_clients');
    }
}
