<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix existing invalid dates that prevent ALTER TABLE
        DB::statement("UPDATE commandes SET date_annulation = NULL WHERE CAST(date_annulation AS CHAR) = '0000-00-00 00:00:00'");
        DB::statement("UPDATE commandes SET heure_recuperation_souhaitee = NULL WHERE CAST(heure_recuperation_souhaitee AS CHAR) = '0000-00-00 00:00:00'");
        DB::statement("UPDATE commandes SET heure_preparation_debut = NULL WHERE CAST(heure_preparation_debut AS CHAR) = '0000-00-00 00:00:00'");
        DB::statement("UPDATE commandes SET heure_prete = NULL WHERE CAST(heure_prete AS CHAR) = '0000-00-00 00:00:00'");
        DB::statement("UPDATE commandes SET heure_recuperation_effective = NULL WHERE CAST(heure_recuperation_effective AS CHAR) = '0000-00-00 00:00:00'");

        // Using raw SQL because changing ENUM values via Blueprint is sometimes problematic
        // Standardizing statuses used in both Vendor and Client controllers
        DB::statement("ALTER TABLE commandes MODIFY COLUMN statut ENUM('en_attente', 'confirmee', 'en_preparation', 'pret', 'termine', 'annule', 'annulee', 'prete', 'recuperee', 'livree', 'annulee_client', 'annulee_vendeur', 'litige') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE commandes MODIFY COLUMN statut ENUM('en_attente','confirmee','en_preparation','prete','recuperee','livree','annulee_client','annulee_vendeur','litige') DEFAULT 'en_attente'");
    }
};
