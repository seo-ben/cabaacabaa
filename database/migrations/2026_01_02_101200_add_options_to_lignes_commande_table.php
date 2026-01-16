<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('lignes_commande', 'options')) {
            Schema::table('lignes_commande', function (Blueprint $table) {
                $table->json('options')->nullable()->after('prix_unitaire');
            });
        }
    }

    public function down()
    {
        Schema::table('lignes_commande', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
};
