<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiseEnAvantTable extends Migration
{
    public function up()
    {
        Schema::create('mise_en_avant', function (Blueprint $table) {
            $table->bigIncrements('id_mise_en_avant');
            $table->unsignedBigInteger('id_vendeur');
            $table->enum('type_promotion', ['nouveau_vendeur','performance','sponsorise','promotion_speciale']);
            $table->integer('priorite')->default(1);
            $table->timestamp('date_debut')->useCurrent();
            $table->timestamp('date_fin')->nullable();
            $table->unsignedInteger('id_zone')->nullable();
            $table->json('zones_ciblees')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamp('date_creation')->useCurrent();
            $table->string('description', 255)->nullable();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->foreign('id_zone')->references('id_zone')->on('zones_geographiques')->onDelete('set null');

            $table->index('id_vendeur');
            $table->index(['date_debut','date_fin']);
            $table->index('actif');
            $table->index('priorite');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mise_en_avant');
    }
}
