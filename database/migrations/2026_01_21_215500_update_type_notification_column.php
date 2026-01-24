<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change type_notification from ENUM to VARCHAR to allow dynamic notification types
        // Using raw statement to ensure compatibility if doctrine/dbal is missing
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type_notification VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily revert to ENUM because we might have data that violates the ENUM constraint.
        // We will leave it as VARCHAR or attempt to revert if data allows (optional).
        // For safety, let's strictly try to revert to the original definition, but it might fail.
        // DB::statement("ALTER TABLE notifications MODIFY COLUMN type_notification ENUM('commande','promotion','avis','system','paiement') NOT NULL");
    }
};
