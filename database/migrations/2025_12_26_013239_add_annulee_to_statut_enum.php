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
        DB::statement("ALTER TABLE commandes MODIFY COLUMN statut ENUM('en_attente', 'confirmee', 'en_preparation', 'pret', 'termine', 'annule', 'annulee', 'prete', 'recuperee', 'livree', 'annulee_client', 'annulee_vendeur', 'litige') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No change needed or revert to previous enum state if critical
    }
};
