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
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->string('registre_commerce', 100)->nullable()->after('telephone_commercial');
            $table->string('document_identite', 255)->nullable()->after('registre_commerce');
            $table->string('justificatif_domicile', 255)->nullable()->after('document_identite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn(['registre_commerce', 'document_identite', 'justificatif_domicile']);
        });
    }
};
