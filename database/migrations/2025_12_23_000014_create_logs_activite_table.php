<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsActiviteTable extends Migration
{
    public function up()
    {
        Schema::create('logs_activite', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->unsignedBigInteger('id_utilisateur')->nullable();
            $table->string('type_action', 50);
            $table->string('table_cible', 50)->nullable();
            $table->unsignedBigInteger('id_enregistrement')->nullable();
            $table->json('details_action')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('date_action')->useCurrent();

            $table->index('id_utilisateur');
            $table->index('date_action');
            $table->index('type_action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs_activite');
    }
}
