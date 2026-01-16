<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendeur_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_vendeur');
            $table->unsignedInteger('id_categorie');
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->foreign('id_categorie')->references('id_categorie')->on('categories_plats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendeur_categories');
    }
};
