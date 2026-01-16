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
            $table->string('nom_complet_client', 200)->nullable()->after('email_client');
            $table->dropColumn(['nom_client', 'prenom_client']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('nom_client', 100)->nullable()->after('email_client');
            $table->string('prenom_client', 100)->nullable()->after('nom_client');
            $table->dropColumn('nom_complet_client');
        });
    }
};
