<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id_notification');
            $table->unsignedBigInteger('id_utilisateur');
            $table->enum('type_notification', ['commande','promotion','avis','system','paiement']);
            $table->string('titre', 150);
            $table->text('message');
            $table->unsignedBigInteger('id_commande')->nullable();
            $table->unsignedBigInteger('id_vendeur')->nullable();
            $table->boolean('lue')->default(false);
            $table->timestamp('date_lecture')->nullable();
            $table->timestamp('date_creation')->useCurrent();

            $table->foreign('id_utilisateur')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_commande')->references('id_commande')->on('commandes')->onDelete('set null');
            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('set null');

            $table->index('id_utilisateur');
            $table->index('lue');
            $table->index('date_creation');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
