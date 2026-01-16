<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsAndPlatTagTable extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id_tag');
            $table->string('nom', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        Schema::create('plat_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_plat');
            $table->unsignedBigInteger('id_tag');
            $table->timestamps();

            $table->foreign('id_plat')->references('id_plat')->on('plats')->onDelete('cascade');
            $table->foreign('id_tag')->references('id_tag')->on('tags')->onDelete('cascade');
            $table->unique(['id_plat','id_tag']);
            $table->index('id_tag');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plat_tag');
        Schema::dropIfExists('tags');
    }
}
