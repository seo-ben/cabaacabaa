<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendeurContactsTable extends Migration
{
    public function up()
    {
        Schema::create('vendeur_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_vendeur');

            $table->string('adresse_ligne_1')->nullable();
            $table->string('adresse_ligne_2')->nullable();
            $table->string('quartier')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal', 20)->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('telephone_principal', 30)->nullable();
            $table->string('telephone_secondaire', 30)->nullable();
            $table->string('whatsapp', 40)->nullable();
            $table->string('email_contact')->nullable();
            $table->string('lien_google_maps')->nullable();

            $table->integer('rayon_service_metre')->nullable();

            $table->boolean('est_principal')->default(true);
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->index(['latitude','longitude']);
            $table->index('ville');
            $table->index('quartier');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendeur_contacts');
    }
}
