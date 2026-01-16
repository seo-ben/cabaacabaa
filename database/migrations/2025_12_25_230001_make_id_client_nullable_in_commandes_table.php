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
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_client')->nullable()->change();
            $table->string('email_client', 150)->nullable()->after('id_client');
            $table->string('nom_client', 100)->nullable()->after('email_client');
            $table->string('prenom_client', 100)->nullable()->after('nom_client');
            $table->string('telephone_client', 20)->nullable()->after('prenom_client');
            $table->string('adresse_livraison', 255)->nullable()->after('type_recuperation');
            $table->decimal('latitude_livraison', 10, 8)->nullable()->after('adresse_livraison');
            $table->decimal('longitude_livraison', 11, 8)->nullable()->after('latitude_livraison');
            $table->decimal('distance_livraison', 8, 2)->nullable()->after('longitude_livraison');
            $table->decimal('frais_livraison', 10, 2)->default(0)->after('distance_livraison');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_client')->nullable(false)->change();
            $table->dropColumn(['email_client', 'nom_client', 'prenom_client', 'telephone_client', 'adresse_livraison', 'latitude_livraison', 'longitude_livraison', 'distance_livraison', 'frais_livraison']);
        });
    }
};
